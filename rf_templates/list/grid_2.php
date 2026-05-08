<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	add_action( 'abprf_grid_2_template', function ( $params = [] ) {
		//echo '<pre>';print_r($params);echo '</pre>';
		$post_ids = ABPRF_Query::get_post_id( $params );
		if ( ! empty( $post_ids ) && sizeof( $post_ids ) > 0 ) {
			$show_post         = array_key_exists( 'show', $params ) && $params['show'] ? $params['show'] : 9;
			$column            = array_key_exists( 'column', $params ) ? $params['column'] : 3;
			$post_count        = 0;
			$args['total']     = sizeof( $post_ids );
			$args['page_item'] = $show_post;
			asort( $post_ids );
			$all_categories = ABPRF_Function::get_option( 'abprf_category' );
			?>
            <div class=" abprf_grid pagination_content_area">
                <div class="_f_gap_f_wrap_mar_tb">
					<?php foreach ( $post_ids as $post_id ) {
						$post_count ++;
						$display_category = ABPRF_Function::get_post_info( $post_id, 'display_category', 'on' );
						$cat_id           = ABPRF_Function::get_post_info( $post_id, 'category' );
						$category         = is_array( $all_categories ) && array_key_exists( $cat_id, $all_categories ) ? $all_categories[ $cat_id ]['name'] : '';
						$image_url        = ABPRF_Function::get_image_url( $post_id );
						$title            = get_the_title( $post_id );
						?>
                        <div class="pagination_item list_item _reflex <?php echo esc_attr( 'grid_' . $column . ' ' . ( $show_post >= $post_count ? '' : '_d_none' ) ); ?>">
                            <div data-image-href="<?php echo esc_url( $image_url ); ?>"><img class="_img_control" src="#" alt="<?php echo esc_attr( $title ); ?>"></div>
                            <div class="ribbon_full">
                                <a class="_abprf_text_center_fs_h6_w_full_color_white" href="<?php echo esc_url( get_the_permalink( $post_id ) ); ?>" target="_blank">
									<?php echo esc_html( $title . ( $category && $display_category == 'on' ? ' - ' . $category : '' ) ); ?>
                                </a>
                            </div>
                        </div>
					<?php } ?>
                </div>
				<?php do_action( 'abprf_pagination', $args ); ?>
            </div>
			<?php
		} else {
			ABPRF_Layout::layout_warning_info( 'not_found' );
		}
	}, 10, 2 );