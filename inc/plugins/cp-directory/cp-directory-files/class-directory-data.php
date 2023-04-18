<?php
class CPDirectoryData {
	private static $id = 0;

	var $atts;
	var $post_type_object;

	var $taxonomy_filters = null;
	var $fields           = null;


	function __construct( $atts ) {
		// Bumps the id on every init so each directory has unique ID.
		self::$id++;

		// Sets block attribute for easy access inside class.
		$this->atts = $atts;

		// Sets post type data
		$this->post_type_object = get_post_type_object( $this->atts['source'] );

		if ( $this->post_type_object === null ) {
			return false;
		}
	}

	function get_directory_id() {
		return 'cp-dir-' . self::$id;
	}

	function get_taxonomy_filters() {
		if ( $this->taxonomy_filters !== null ) {
			return $this->taxonomy_filters;
		}

		$this->taxonomy_filters = apply_filters( 'cp_dir_pre_taxonomies_filters_data', false, $this->atts );

		if ( false !== $this->taxonomy_filters ) {
			return $this->taxonomy_filters;
		}

		$this->taxonomy_filters = array();

		if ( !isset( $this->atts['filters'][ $this->atts['source'] ] ) ) {
			return $this->taxonomy_filters;
		}

		$enabled_filters = $this->atts['filters'][ $this->atts['source'] ];

		//Checks if any "tax_" filters exist.
		$filters            = $this->get_filters();
		$filters_tax_exists = false;
		foreach ( $filters as $filter ) {
			if ( substr( $filter, 0, 4 ) === 'tax_' ) {
				$filters_tax_exists = true;
				break;
			}
		}

		if ( !$filters_tax_exists ) {
			return $this->taxonomy_filters;
		}
		
		$taxonomies = get_object_taxonomies( $this->atts['source'], 'object' );
		foreach ( $taxonomies as $taxonomy ) {


			if ( in_array( 'tax_' . $taxonomy->name, $enabled_filters ) ) {
				$parent_id = 0;
				if ( isset( $this->atts['categories'][ $taxonomy->name ] ) && $this->atts['categories'][ $taxonomy->name ] ) {
					$parent_id = $this->atts['categories'][ $taxonomy->name ];
				}

				if ( in_array( 'tax_childs_' . $taxonomy->name, $enabled_filters ) ) {
					$terms = get_terms(
						$taxonomy->name,
						array(
							'hide_empty' => true,
							'parent'     => $parent_id,
						)
					);
					foreach ( $terms as $term ) {
						$field_key = 'tax_' . $taxonomy->name . '-' . $term->term_id;

						if ( get_term_children( $term->term_id, $taxonomy->name ) ) {
							$this->taxonomy_filters[$field_key] = array(
								'label'      => $term->name,
								'taxonomy'   => $taxonomy->name,
								'parent_id'  => $term->term_id,
								'select_id'  => $this->get_directory_id() . '-tax-' . $taxonomy->name . '-' . $term->term_id,
								'field_name' => 'cp-dir-field-' . $taxonomy->name . '-' . $term->term_id,
							);
						}
					}
				}
				else {
					// Use parent name if we are filtering.
					if ( $parent_id ) {
						$term = get_term( $parent_id, $taxonomy->name );

						$label      = $term->name;
						$field_key = 'tax_' . $taxonomy->name . '-' . $term->term_id;
						$field_name = $taxonomy->name . '-' . $term->term_id;
					} else {
						$label      = $taxonomy->label;
						$field_key = 'tax_' . $taxonomy->name;
						$field_name = $taxonomy->name;
					}
					if ( ! $parent_id || get_term_children( $parent_id, $taxonomy->name ) ) {
						$this->taxonomy_filters[$field_key] = array(
							'label'      => $label,
							'taxonomy'   => $taxonomy->name,
							'parent_id'  => $parent_id,
							'select_id'  => $this->get_directory_id() . '-tax-' . $taxonomy->name,
							'field_name' => 'cp-dir-field-' . $field_name,
						);
					}
				}
			}
		}

		$this->taxonomy_filters = apply_filters( 'cp_dir_taxonomies_filters_data', $this->taxonomy_filters, $this->atts );

		return $this->taxonomy_filters;
	}

	function get_fields() {
		if ( $this->fields !== null ) {
			return $this->fields;
		}

		$pre = apply_filters( 'cp_dir_pre_get_fields', false, $this->atts );

		if ( false !== $pre ) {
			return $pre;
		}

		$taxonomy_filters = $this->get_taxonomy_filters();
		
		if( isset( $this->atts['fields'][ $this->atts['source'] ] ) ) {
			$enabled_fields = $this->atts['fields'][ $this->atts['source'] ];
		} else {
			$enabled_fields = array();
		}

		$this->fields = array();

		$available_fields = cp_dir_get_available_fields( $this->atts['source'] );
		foreach ( $available_fields as $field_key => $field_details ) {
			$field_enabled = in_array( $field_key, $enabled_fields ) || $field_details['default'];

			if( $field_enabled ) {
				if (  $field_details['type'] != 'taxonomy' ) {
					$field_name = 'cp-dir-field-' . $field_details['name'];

					$this->fields[$field_key] = array_merge(
						$field_details,
						array(
							'field_name' => $field_name,
							'hidden'     => false,
						)
					);
				}
			}
		}

		// Lets process taxonomies seperately because we will want them anyway to apply the filters
		foreach ( $taxonomy_filters as $filter_key => $filter ) {
			$field_enabled = in_array( 'tax_' . $filter['taxonomy'], $enabled_fields );

			$this->fields[$filter_key] = array_merge(
				$available_fields['tax_' . $filter['taxonomy']],
				array(
					'label'      => $filter['label'],
					'field_name' => $filter['field_name'],
					'args'       => array( 'parent_id' => $filter['parent_id'] ),
					'hidden'     => $field_enabled ? false : true,
				)
			);
		}

		$this->fields = apply_filters( 'cp_dir_get_fields', $this->fields, $this->atts );

		return $this->fields;
	}

	function get_fields_js() {
		$pre = apply_filters( 'cp_dir_pre_get_fields_js', false, $this->atts );

		if ( false !== $pre ) {
			return $pre;
		}

		$fields_js = array( array( 'data' => array( 'entry-id', 'entry-parent-ids' ) ) );
		$fields    = $this->get_fields();
		foreach ( $fields as $field ) {
			if ( isset( $field['type'] ) && $field['type'] == 'taxonomy' ) {
				$fields_js[] = array(
					'name' => $field['field_name'],
					'attr' => 'data-value',
				);
			} elseif ( isset( $field['name'] ) && $field['name'] == 'post_title' ) {
				$fields_js[] = array(
					'name' => $field['field_name'],
					'attr' => 'data-value',
				);
			} elseif ( isset( $field['value_type'] ) && $field['value_type'] == 'email' ) {
				$fields_js[] = array(
					'name' => $field['field_name'],
					'attr' => 'data-value',
				);
			} else {
				if( isset( $field['hidden'] ) && $field['hidden'] ) {
					$fields_js[] = array(
						'name' => $field['field_name'],
						'attr' => 'data-value',
					);
				}
				else {
					$fields_js[] = $field['field_name'];
				}
			}
		}

		return apply_filters( 'cp_dir_get_fields_js', $fields_js, $this->atts );
	}

	function get_entries() {
		$pre = apply_filters( 'cp_dir_pre_get_entries', false, $this->atts );

		if ( false !== $pre ) {
			return $pre;
		}

		$args = array(
			'numberposts' => $this->get_entries_limit(),
			'orderby'     => 'title',
			'order'       => 'ASC',
			'post_type'   => $this->atts['source'],
			'fields'      => 'ids',
		);
		
		if ( isset( $this->atts['sort_by'] ) && $this->atts['sort_by'] ) {
			$available_order = cp_dir_get_available_order( $this->atts['source'] );
			foreach( $available_order as $order ) {
				if( $order['value'] == $this->atts['sort_by'] ) {
					if( isset( $order['orderby'] ) && $order['orderby'] ) {
						$args['orderby'] = $order['orderby'];
					}
					else {
						$args['orderby'] = $order['value'];
					}

					if( isset( $order['meta'] ) && $order['meta'] ) {
						$args['meta_key'] = $order['meta'];
					}

					break;
				}
			}
			
		}

		if ( $this->atts['categories'] ) {
			$taxonomies = get_object_taxonomies( $this->atts['source'] );
			foreach ( $this->atts['categories'] as $taxonomy_name => $taxonomy_child_id ) {
				if ( in_array( $taxonomy_name, $taxonomies ) && $taxonomy_child_id ) {
					if ( ! isset( $args['tax_query'] ) ) {
						$args['tax_query'] = array();
					}
					$args['tax_query'][] = array(
						'taxonomy'         => $taxonomy_name,
						'field'            => 'term_id',
						'terms'            => $taxonomy_child_id,
						'include_children' => true,
					);
				}
			}
		}

		if ( isset( $this->atts['post_ids'] ) && array( $this->atts['post_ids'] ) ) {
			$args['post__in'] = $this->atts['post_ids'];
		}

		/**
		 * Allows to filter the entries args
		 */
		$args = apply_filters( 'cp_dir_get_entries_args', $args, $this->atts );

		$entries = get_posts( $args );

		return apply_filters( 'cp_dir_get_entries', $entries, $args, $this->atts );
	}

	function get_filters() {
		$filters = array();
		if ( isset( $this->atts['filters'][ $this->atts['source'] ] ) ) {
			$filters = $this->atts['filters'][ $this->atts['source'] ];
		}

		return apply_filters( 'cp_dir_get_filters', $filters, $this->atts );
	}

	function get_posts_per_page( $total = false ) {
		$posts_per_page = false;
		if ( isset( $this->atts['posts_per_page'] ) && $this->atts['posts_per_page'] ) {
			if ( $total == false || $total > $this->atts['posts_per_page'] || ( defined( 'REST_REQUEST' ) && $total >= $this->atts['posts_per_page'] ) ) {
				$posts_per_page = $this->atts['posts_per_page'];
			}
		}

		return apply_filters( 'cp_dir_get_posts_per_page', $posts_per_page, $this->atts );
	}

	function get_entries_limit() {
		$entries_limit = apply_filters( 'cp_dir_get_entries_limit', 500, $this->atts );

		if( defined( 'REST_REQUEST' ) ) {
			$entries_limit = $this->get_posts_per_page();
		}

		return $entries_limit;
	}
}
