<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	add_action( 'abprf_total_price_template', function ( $abprf_infos = [] ) {
		$post_id = array_key_exists( 'post_id', $abprf_infos ) ? $abprf_infos['post_id'] : '';
		$wc_link = ABPRF_Function::get_post_info( $post_id, 'link_wc_id', 0 );
		if ( $post_id > 0 && $wc_link > 0 ) {
			$display_additional = array_key_exists( 'display_additional_services', $abprf_infos ) ? $abprf_infos['display_additional_services'] : ABPRF_Function::get_post_info( $post_id, 'display_additional_services', 'on' );
			?>
            <div class="total_continue_area">
                <div class="total_continue item_box_1">
                    <div class="_fd_column_max_500">
                        <h5 class="_abprf _f_equal"><span><?php esc_html_e( 'Property Rent : ', 'abprf-rental-forge' ); ?>&nbsp;</span><span class="item_total _color_theme_text_right"></span></h5>
						<?php if ( $display_additional == 'on' ) { ?>
                            <h5 class="_abprf _f_equal"><span><?php esc_html_e( 'Additional : ', 'abprf-rental-forge' ); ?>&nbsp;</span><span class="additional_total _color_theme_text_right"></span></h5>
						<?php } ?>
                        <h5 class="_abprf _f_equal"><span><?php esc_html_e( 'Deposit : ', 'abprf-rental-forge' ); ?>&nbsp;</span><span class="deposit_total _color_theme_text_right"></span></h5>
                        <div class="_divider_xs"></div>
                        <h5 class="_abprf _f_equal"><span><?php esc_html_e( 'Total : ', 'abprf-rental-forge' ); ?>&nbsp;</span><span class="abprf_total _color_theme_text_right"></span></h5>
                    </div>
					<?php if ( is_admin() && str_contains( wp_get_referer(), 'add_order' ) ) { ?>
                        <input type="submit" class="_d_none" name="add-admin-order" value="<?php echo esc_attr( $wc_link ); ?>"/>
					<?php } else { ?>
                        <input type="submit" class="_d_none" name="add-to-cart" value="<?php echo esc_attr( $wc_link ); ?>"/>
					<?php } ?>
                    <button class="_btn_light_theme abprf_book_continue" type="button" data-alert="<?php esc_attr_e( 'No property Selected ! Please Select property', 'abprf-rental-forge' ); ?>" data-msg="<?php esc_attr_e( 'Added to Cart Successfully', 'abprf-rental-forge' ); ?>">
						<?php esc_html_e( 'Continue', 'abprf-rental-forge' ); ?>
                        <span class="fas fa-angle-double-right _mar_l_xs"></span>
                    </button>
                </div>
            </div>
			<?php
		}
	}, 10, 2 );
