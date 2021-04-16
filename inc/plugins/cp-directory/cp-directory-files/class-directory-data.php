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

		//Checks if any "tax_" filters exist.
		$filters            = $this->get_filters();
		$filters_tax_exists = array();
		foreach ( $filters as $filter ) {
			if ( substr( $filter, 0, 4 ) === 'tax_' ) {
				$filters_tax_exists = true;
				break;
			}
		}

		$this->taxonomy_filters = array();
		if ( $filters_tax_exists ) {
			$taxonomies = get_object_taxonomies( $this->atts['source'], 'object' );
			foreach ( $taxonomies as $taxonomy ) {
				if ( isset( $this->atts['filters'][ $this->atts['source'] ] ) && in_array( 'tax_' . $taxonomy->name, $this->atts['filters'][ $this->atts['source'] ] ) ) {
					$parent_id = 0;
					if ( isset( $this->atts['categories'][ $taxonomy->name ] ) && $this->atts['categories'][ $taxonomy->name ] ) {
						$parent_id = $this->atts['categories'][ $taxonomy->name ];
					}

					if ( in_array( 'tax_childs_' . $taxonomy->name, $this->atts['filters'][ $this->atts['source'] ] ) ) {
						$terms = get_terms(
							$taxonomy->name,
							array(
								'hide_empty' => true,
								'parent'     => $parent_id,
							)
						);
						foreach ( $terms as $term ) {
							if ( get_term_children( $term->term_id, $taxonomy->name ) ) {
								$this->taxonomy_filters[] = array(
									'label'      => $term->name,
									'taxonomy'   => $taxonomy->name,
									'parent_id'  => $term->term_id,
									'select_id'  => $this->get_directory_id() . '-tax-' . $taxonomy->name . '-' . $term->term_id,
									'field_name' => 'cp-dir-field-' . $taxonomy->name . '-' . $term->term_id,
								);
							}
						}
					}

					if ( empty( $this->taxonomy_filters ) ) {
						// Use parent name if childs are being shown.
						if ( $parent_id ) {
							$term = get_term( $parent_id, $taxonomy->name );

							$label      = $term->name;
							$field_name = $taxonomy->name . '-' . $term->term_id;
						} else {
							$label      = $taxonomy->label;
							$field_name = $taxonomy->name;
						}
						if ( ! $parent_id || get_term_children( $parent_id, $taxonomy->name ) ) {
							$this->taxonomy_filters[] = array(
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

		$this->fields = array();

		$available_fields = cp_dir_get_available_fields( $this->atts['source'] );
		foreach ( $available_fields as $field_key => $field_details ) {
			$field_enabled           = isset( $this->atts['fields'][ $this->atts['source'] ] ) && in_array( $field_key, $this->atts['fields'][ $this->atts['source'] ] );
			$taxonomy_filter_enabled = false;
			if ( $field_details['type'] == 'taxonomy' ) {
				//Checks if filter is enabled
				foreach ( $taxonomy_filters as $taxonomy_filter_details ) {
					if ( $taxonomy_filter_details['taxonomy'] == $field_details['name'] ) {
						$taxonomy_filter_enabled = true;
						break;
					}
				}
			}
			if ( $field_details['default'] || $field_enabled || $taxonomy_filter_enabled ) {
				if ( $field_details['type'] == 'taxonomy' ) {
					if ( $taxonomy_filters ) {
						foreach ( $taxonomy_filters as $filter ) {
							$this->fields[$filter['field_name']] = array_merge(
								$field_details,
								array(
									'label'      => $filter['label'],
									'field_name' => $filter['field_name'],
									'args'       => array( 'parent_id' => $filter['parent_id'] ),
									'hidden'     => ! $field_enabled && $taxonomy_filter_enabled ? true : false,
								)
							);
						}
					}
				} else {
					$field_name = 'name';
					if ( isset( $field_details['args']['name_field'] ) && $field_details['args']['name_field'] ) {
						$field_name = 'cp-dir-field-' . $field_details['name'];
					}

					$this->fields[$field_name] = array_merge(
						$field_details,
						array(
							'field_name' => $field_name,
							'hidden'     => false,
						)
					);
				}
			}
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
			if ( $field['type'] == 'taxonomy' ) {
				$fields_js[] = array(
					'name' => $field['field_name'],
					'attr' => 'data-value',
				);
			} elseif ( $field['name'] == 'post_title' ) {
				$fields_js[] = array(
					'name' => $field['field_name'],
					'attr' => 'data-value',
				);
			} elseif ( $field['value_type'] == 'email' ) {
				$fields_js[] = array(
					'name' => $field['field_name'],
					'attr' => 'data-value',
				);
			} else {
				if( !$field['hidden'] ) {
					$fields_js[] = $field['field_name'];
				}
				else {
					$fields_js[] = array(
						'name' => $field['field_name'],
						'attr' => 'data-value',
					);
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
			'category'    => 0,
			'orderby'     => 'title',
			'order'       => 'ASC',
			'post_type'   => $this->atts['source'],
			'fields'      => 'ids',
		);
		
		if ( isset( $this->atts['sort_by'] ) && $this->atts['sort_by'] ) {
			$args['orderby'] = $this->atts['sort_by'];
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
		if ( isset( $this->atts['posts_per_page'] ) ) {
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
