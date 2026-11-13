<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	add_action( 'abprf_total_price_template', function ( $abprf_infos = [] ) {
		$post_id = absint( $abprf_infos['post_id'] ?? 0 );
		if ( $post_id <= 0 ) {
			return;
		}
		$wc_link = absint( ABPRF_Function::get_post_info( $post_id, 'link_wc_id', 0 ) );
		if ( $wc_link <= 0 ) {
			return;
		}
		$display_additional = $abprf_infos['display_additional_services'] ?? ABPRF_Function::get_post_info( $post_id, 'display_additional_services', 'on' );
		?>
        <div class="total_continue_area">
            <div class="total_continue item_box_1">
                <div class="_fj_between">
                    <h4 class="abp_color_theme_gap_xs">
                        <span class="fas fa-receipt"></span>
                        <span><?php esc_html_e('Booking Summary', 'abp-rentalforge'); ?></span>
                    </h4>
	                <?php if ( is_admin() && str_contains( wp_get_referer(), 'admin_order' ) ) { ?>
                        <input type="submit" class="abp_hidden" name="add-admin-order" value="<?php echo esc_attr( $wc_link ); ?>"/>
	                <?php } else { ?>
                        <input type="submit" class="abp_hidden" name="add-to-cart" value="<?php echo esc_attr( $wc_link ); ?>"/>
	                <?php } ?>
                    <button class="_btn_theme abprf_book_continue" type="button" data-alert="<?php esc_attr_e( 'No property Selected ! Please Select property', 'abp-rentalforge' ); ?>" data-msg="<?php esc_attr_e( 'Added to Cart Successfully', 'abp-rentalforge' ); ?>">
		                <?php esc_html_e( 'Continue', 'abp-rentalforge' ); ?>
                        <span class="fas fa-angle-double-right _mar_l_xs"></span>
                    </button>
                </div>
                <div class="_divider_xs"></div>
                <div class="_fd_column">
                    <h5 class="abp_fj_between">
                        <span><?php esc_html_e( 'Property Rent : ', 'abp-rentalforge' ); ?>&nbsp;</span>
                        <span class="item_total _color_theme_text_right"></span>
                    </h5>
					<?php if ( ABPRF_Function::on_off( 'additional_info' ) && $display_additional === 'on' ) { ?>
                        <h5 class="abp_fj_between">
                            <span><?php esc_html_e( 'Additional : ', 'abp-rentalforge' ); ?>&nbsp;</span>
                            <span class="additional_total _color_theme_text_right"></span>
                        </h5>
					<?php } ?>
					<?php if ( ABPRF_Function::on_off( 'deposit' ) ) { ?>
                        <h5 class="abp_fj_between">
                            <span><?php esc_html_e( 'Deposit : ', 'abp-rentalforge' ); ?>&nbsp;</span>
                            <span class="deposit_total _color_theme_text_right"></span>
                        </h5>
					<?php } ?>
                    <div class="_divider_xs"></div>
                    <h5 class="abp_fj_between">
                        <span class="_color_theme"><?php esc_html_e( 'Total : ', 'abp-rentalforge' ); ?>&nbsp;</span>
                        <span class="abprf_total _color_theme_text_right"></span>
                    </h5>
                </div>
				
            </div>
        </div>
		<?php
	}, 10, 2 );