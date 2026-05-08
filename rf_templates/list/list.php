<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	add_action( 'abprf_list_template', function ( $params = [] ) {
		//echo '<pre>';print_r($params);echo '</pre>';
		$post_ids = ABPRF_Query::get_post_id( $params );
		if ( ! empty( $post_ids ) && sizeof( $post_ids ) > 0 ) {
			$column            = array_key_exists( 'column', $params ) ? $params['column'] : 3;
			$show_post         = array_key_exists( 'show', $params ) && $params['show'] ? $params['post'] : $column * 3;
			$post_count        = 0;
			$args['total']     = sizeof( $post_ids );
			$args['page_item'] = $show_post;
			asort( $post_ids );
			$all_categories = ABPRF_Function::get_option( 'abprf_category' );
			?>
            <div class=" abprf_lists pagination_content_area">
                <div class="_f_gap_f_wrap_mar_tb">
					<?php foreach ( $post_ids as $post_id ) {
						$post_count ++;
						$image_url = ABPRF_Function::get_image_url( $post_id );
						$title     = get_the_title( $post_id );
						?>
                        <div class="pagination_item list_item _reflex <?php echo esc_attr( $show_post >= $post_count ? '' : '_d_none' ); ?>">
                            <div class="item_content">
                                <div data-image-href="<?php echo esc_url( $image_url ); ?>"><img class="_img_control" src="#" alt="<?php echo esc_attr( $title ); ?>"></div>
                                <div class="item_details">
                                    <a class="_abprf list_title _fs_h5" href="<?php echo esc_url( get_the_permalink( $post_id ) ); ?>" target="_blank"><?php echo esc_html( $title ); ?></a>
                                    <div class="_divider_xxs"></div>
									<?php do_action( 'abprf_category', $post_id, $all_categories ); ?>
                                </div>
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