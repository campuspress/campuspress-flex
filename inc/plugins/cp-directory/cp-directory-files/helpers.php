<?php
/**
 * Gets available sources.
 *
 * @return array
 */
function cp_dir_get_sources() {
	$sources = apply_filters( 'cp_dir_sources', array() );

	return $sources;
}

/**
 * Builds array necessary to generate correct taxonomy based filters for taxonomy.
 *
 * @param [array] $categories selected categories for each taxonomies.
 * @param [string] $source post type used to select taxonomies.
 * @param boolean $multiple_subcats decides if subcategories should be split into multiple filters
 * @return array
 */
function cp_dir_get_taxonomies_filters_data( $categories, $source, $multiple_subcats = false ) {
	$pre = apply_filters( 'cp_dir_pre_taxonomies_filters_data', false, $categories, $source, $multiple_subcats );

	if ( false !== $pre ) {
		return $pre;
	}

	$taxonomies         = get_object_taxonomies( $source, 'object' );
	$taxonomies_filters = array();
	foreach ( $taxonomies as $taxonomy ) {
		$parent_id = 0;
		if ( isset( $categories[ $taxonomy->name ] ) && $categories[ $taxonomy->name ] ) {
			$parent_id = $categories[ $taxonomy->name ];
		}

		if ( $multiple_subcats ) {
			$terms = get_terms(
				$taxonomy->name,
				array(
					'hide_empty' => true,
					'parent'     => $parent_id,
				)
			);
			foreach ( $terms as $term ) {
				if ( get_term_children( $term->term_id, $taxonomy->name ) ) {
					$taxonomies_filters[] = array(
						'label'     => $term->name,
						'taxonomy'  => $taxonomy->name,
						'parent_id' => $term->term_id,
					);
				}
			}
		}

		if ( empty( $taxonomies_filters ) ) {
			$taxonomies_filters[] = array(
				'label'     => $taxonomy->label,
				'taxonomy'  => $taxonomy->name,
				'parent_id' => $parent_id,
			);
		}
	}

	return apply_filters( 'cp_dir_taxonomies_filters_data', $taxonomies_filters, $categories, $source, $multiple_subcats );
}

/**
 * Gets available filters.
 *
 * @param [string] $source post type used to select fields.
 * @return array
 */
function cp_dir_get_available_filters( $source ) {
	$pre = apply_filters( 'cp_dir_pre_get_available_filters', false, $source );

	if ( false !== $pre ) {
		return $pre;
	}

	$filters = array();

	$filters['search'] = array(
		'name'  => 'search',
		'label' => __( 'Search', 'cpschool' ),
		'type'  => 'search',
	);

	$taxonomies = get_object_taxonomies( $source, 'objects' );
	if ( $taxonomies ) {
		foreach ( $taxonomies as $taxonomy_name => $taxonomy_details ) {
			$filters[ 'tax_' . $taxonomy_name ]        = array(
				'name'  => $taxonomy_name,
				'label' => $taxonomy_details->label,
				'type'  => 'taxonomy',
			);
			$filters[ 'tax_childs_' . $taxonomy_name ] = array(
				'name'  => $taxonomy_name,
				'label' => $taxonomy_details->label . __( ' Childs', 'cp-dir' ),
				'type'  => 'taxonomy_child',
			);
		}
	}

	return apply_filters( 'cp_dir_get_available_filters', $filters, $source );
}

/**
 * Gets data about fields used for post type.
 *
 * @param [string] $source post type used to select fields.
 * @return array
 */
function cp_dir_get_available_fields( $source ) {
	$pre = apply_filters( 'cp_dir_pre_get_available_fields', false, $source );

	if ( false !== $pre ) {
		return $pre;
	}

	$fields           = array();
	$post_type_object = get_post_type_object( $source );

	$fields['post_title'] = array(
		'name'       => 'post_title',
		'label'      => __( 'Title', 'cpschool' ),
		'type'       => 'post',
		'value_type' => 'text',
		'default'    => true,
		'args'       => array(
			'link'       => $post_type_object->publicly_queryable,
		),
	);

	$fields['post_excerpt'] = array(
		'name' => 'post_excerpt',
		'label' => __( 'Excerpt', 'cpschool' ),
		'type' => 'post',
		'value_type' => 'text',
		'default' => false,
	);

	$taxonomies = get_object_taxonomies( $source, 'object' );
	foreach ( $taxonomies as $taxonomy ) {
		$fields[ 'tax_' . $taxonomy->name ] = array(
			'name'       => $taxonomy->name,
			'label'      => $taxonomy->label,
			'type'       => 'taxonomy',
			'value_type' => 'text',
			'default'    => false,
		);
	}

	if ( function_exists( 'acf_get_field_groups' ) && function_exists( 'acf_get_fields' ) ) :
		$groups = acf_get_field_groups( array( 'post_type' => $source ) );
		foreach ( $groups as $group ) {
			$group_fields = acf_get_fields( $group );
			foreach ( $group_fields as $group_field ) {
				$fields[ 'cf_' . $group_field['name'] ] = array(
					'name'       => $group_field['name'],
					'label'      => $group_field['label'],
					'type'       => 'custom_field',
					'value_type' => $group_field['type'],
					'default'    => false,
				);
			}
		}
	endif;

	return apply_filters( 'cp_dir_get_available_fields', $fields, $source );
}

/**
 * Gets the value of single field.
 *
 * @param [numeric] $entry_id Post ID.
 * @param [array] details of the field.
 * @return string
 */
function cp_dir_get_field_value( $entry_id, $field_details ) {
	$pre = apply_filters( 'cp_dir_pre_get_field_value', false, $entry_id, $field_details );

	if ( false !== $pre ) {
		return $pre;
	}

	$value = array();
	switch ( $field_details['type'] ) :
		case 'post':
			switch ( $field_details['name'] ) :
				case 'post_title':
					$entry_post = get_post( $entry_id );
					$value_raw_content = get_the_title( $entry_post );
					$value_raw_attr    = $value_raw_content;
					if ( isset( $field_details['args']['link'] ) && $field_details['args']['link'] ) {
						$link = get_permalink( $entry_post );
						if( apply_filters( 'cp_dir_enable_go_back', true ) ) {
							if ( get_the_ID() ) {
								$link = add_query_arg( 'cp-dir-id', get_the_ID(), $link );
							}
						}
						$value_raw_content = '<a href="' . esc_url( $link ) . '">' . $value_raw_content . '</a>';
					}
					break;
				case 'post_excerpt':
					$entry_post = get_post( $entry_id );
					$value_raw_content = wp_trim_excerpt( $entry_post->post_excerpt, $entry_post );
					break;
			endswitch;
			break;
		case 'custom_field':
			$value_raw_content = get_post_meta( $entry_id, $field_details['name'], true );
			break;
		case 'taxonomy':
			// For some reason "fields => 'id=>name'" is not working here.
			$args = array( 'hierarchical' => true );
			if ( isset( $field_details['args']['parent_id'] ) ) {
				// Includes all children and parent so it works when parent is not selected in entry.
				$term_include    = get_term_children( $field_details['args']['parent_id'], $field_details['name'] );
				//looks like this is no longer needed?
				//$term_include[]  = $field_details['args']['parent_id'];
				$args['include'] = $term_include;
			}
			$entry_taxonomies       = wp_get_post_terms( $entry_id, $field_details['name'], $args );
			$entry_taxonomies_names = $entry_taxonomies_ids = array();
			foreach ( $entry_taxonomies as $entry_taxonomy ) {
				$entry_taxonomies_names[] = $entry_taxonomy->name;
				$entry_taxonomies_ids[]   = $entry_taxonomy->term_id;
				if ( $entry_taxonomy->parent && !in_array($entry_taxonomy->parent, $entry_taxonomies_ids ) ) {
					$entry_taxonomies_ids[] = $entry_taxonomy->parent;
				}
			}

			// If entry is checking in children and finds some, lets say it is also part of parent.
			if ( isset( $field_details['args']['parent_id'] ) && $entry_taxonomies_ids ) {
				$entry_taxonomies_ids[] = $field_details['args']['parent_id'];
			}

			//looks like this is no longer needed?
			//$entry_taxonomies_ids = array_unique( $entry_taxonomies_ids );

			$value_raw_content = implode( ', ', $entry_taxonomies_names );
			$value_raw_attr    = implode( ',', $entry_taxonomies_ids );
			break;
	endswitch;

	if ( ! isset( $value_raw_attr ) ) {
		$value_raw_attr = false;
	}

	if ( ! isset( $value_raw_content ) ) {
		$value_raw_content = false;
	}

	if ( $value_raw_content ) {
		switch ( $field_details['value_type'] ) :
			case 'email':
				$email_data        = explode( '@', $value_raw_content );
				$value_raw_attr    = $email_data[0];
				$value_raw_content = '<a href="mailto:' . esc_attr( $value_raw_content ) . '">' . esc_html( $value_raw_content ) . '</a>';
				break;
		endswitch;
	}

	$value['content'] = $value_raw_content;
	$value['attr']    = $value_raw_attr;

	return apply_filters( 'cp_dir_get_field_value', $value, $value_raw_content, $value_raw_attr, $entry_id, $field_details );
}

/**
 * Gets available orders
 *
 * @param [array] $order.
 * @param [string] $source post type.
 * @return array
 */
function cp_dir_get_available_order( $source = '' ) {

	$order = array(
		array( 'value' => '', 'label' => __('Title', 'cp-dir') ),
		array( 'value' => 'date', 'label' => __('Date', 'cp-dir') ),
		array( 'value' => 'menu_order', 'label' => __('Order', 'cp-dir') ),
	);

	return apply_filters( 'cp_dir_get_available_order', $order, $source );
}