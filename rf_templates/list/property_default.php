<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}
	add_action( 'abprf_property_default_template', function ( $params = [] ) {
		if ( ! is_array( $params ) ) {
			return;
		}
		$all_property = $params['all_property'] ?? [];
		if ( ! empty( $all_property ) && is_array( $all_property ) ) {
			$style  = $params['style'] ?? 'default';
			$column = absint( $params['column'] ?? 3 );
			$column = $column > 0 ? $column : 3;
			$class      = ( $style === 'grid' && $column > 1 ) ? 'abprf_grid item_' . $column : 'abprf_lists item_' . $column;
			$show_post  = absint( ( $params['show'] ?? 0 ) ?: ( $column * 3 ) );
			$post_count = 0;
			$args = [
				'total' => count( $all_property ),
				'page_item' => $show_post,
			];
			?>
            <div class="<?php echo esc_attr( $class ); ?>">
				<?php
					foreach ( $all_property as $property ) {
						if ( ! is_array( $property ) ) {
							continue;
						}
						$post_count ++;
						$property_post_id = $property['post_id'] ?? '';
						$abprf_infos = [
							'post_id' => $property_post_id,
							'property_id' => $property['id'] ?? '',
							'property_name' => $property['name'] ?? '',
							'add_class' => ( $show_post >= $post_count ) ? 'pagination_item' : 'pagination_item abp_close',
						];
						do_action( 'abprf_property_item', $abprf_infos, $property );
					}
				?>
            </div>
			<?php
			do_action( 'abprf_pagination', $args );
		} else {
			ABPRF_Layout::layout_warning_info( 'no_property_found' );
		}
	} );