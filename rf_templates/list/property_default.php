<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	add_action( 'abprf_property_default_template', function ( $params = [] ) {
		$all_property = array_key_exists( 'all_property', $params ) && $params['all_property'] ? $params['all_property'] : [];
		if ( ! empty( $all_property ) && sizeof( $all_property ) > 0 ) {
			$style             = array_key_exists( 'style', $params ) && $params['style'] ? $params['style'] : 'default';
			$column            = array_key_exists( 'column', $params ) ? $params['column'] : 3;
			$class             = $style == 'grid' && $column > 1 ? 'abprf_grid item_' . $column : 'abprf_lists item_' . $column;
			$show_post         = array_key_exists( 'show', $params ) && $params['show'] ? $params['show'] : $column * 3;
			$post_count        = 0;
			$args['total']     = sizeof( $all_property );
			$args['page_item'] = $show_post;
			?>
            <div class="<?php echo esc_attr( $class ); ?>">
				<?php
					foreach ( $all_property as $property ) {
						$post_count ++;
						$property_post_id             = array_key_exists( 'post_id', $property ) ? $property['post_id'] : '';
						$abprf_infos['post_id']       = $property_post_id;
						$abprf_infos['property_id']   = array_key_exists( 'id', $property ) ? $property['id'] : '';
						$abprf_infos['property_name'] = array_key_exists( 'name', $property ) ? $property['name'] : '';
						$abprf_infos['add_class']     = $show_post >= $post_count ? 'pagination_item' : 'pagination_item rf_close';
						do_action( 'abprf_property_item', $abprf_infos, $property );
					} ?>
            </div>
			<?php do_action( 'abprf_pagination', $args ); ?>
			<?php
		} else {
			ABPRF_Layout::layout_warning_info( 'no_property_found' );
		}
	}, 10, 2 );