<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	add_action( 'abprf_search_form_template', function ( $abprf_infos ) {
		$post_id     = array_key_exists( 'post_id', $abprf_infos ) ? $abprf_infos['post_id'] : '';
		$location    = array_key_exists( 'abprf_location', $abprf_infos ) ? $abprf_infos['abprf_location'] : '';
		$params_form = array_key_exists( 'form', $abprf_infos ) ? $abprf_infos['form'] : 'inline';
		$rent_rule   = array_key_exists( 'rent_rule', $abprf_infos ) ? $abprf_infos['rent_rule'] : 'hourly';
		$brand_icon  = ABPRF_Function::icon();
		//echo '<pre>';print_r( ABPRF_Function::get_option( 'abprf_mm_time' ));					echo '</pre>';
		do_action( 'abprf_generate_script_data', $post_id );
		if ( isset( $_SESSION['abprf_cart_success'] ) ) {
			?>
            <div class="abprf_add_to_cart_notice _d_none">
				<?php echo esc_html( sanitize_text_field( wp_unslash( $_SESSION['abprf_cart_success'] ) ) ); ?>
            </div>
			<?php
			unset( $_SESSION['abprf_cart_success'] );
		}
		$all_dates     = ABPRF_Function::get_dates( $post_id );
		$upcoming_date = current( $all_dates );
		$upcoming_date = ! empty( $upcoming_date ) ? gmdate( 'Y-m-d', strtotime( $upcoming_date ) ) : '';
		?>
        <div id="abprf_search_area">
            <h2 class="_abprf_mar_b_xs"><span class="_mar_r_xxs">📅</span><?php esc_html_e( 'Select Rental Period', 'abprf-rental-forge' ); ?></h2>
            <form class="abprf_property_form <?php echo esc_attr( $params_form == 'column' ? '_form_column' : '_form_inline' ); ?>" method="post" action="">
                <input type="hidden" name="post_id" value="<?php echo esc_attr( $post_id ); ?>"/>
                <input type="hidden" name="rent_rule" value="<?php echo esc_attr( $rent_rule ); ?>"/>
				<?php ABPRF_Layout::location_select( $post_id, $location ); ?>
				<?php if ( $rent_rule == 'monthly' ) {
					$all_dates = ABPRF_Function::get_start_month( $post_id, $all_dates );
					//echo '<pre>';print_r( $all_dates);					echo '</pre>';
					if ( !empty($all_dates) ) {
                        $first_array=current( $all_dates );
						$upcoming_date=is_array($first_array) && array_key_exists( 'value', $first_array ) ? $first_array['value'] : '';
						$upcoming_date = ! empty( $upcoming_date ) ? gmdate( 'Y-m-d', strtotime( $upcoming_date ) ) : '';
						?>
                        <div class="start_date _input_item"><?php ABPRF_Layout::rent_start_month( $all_dates ); ?></div>
                        <div class="end_date _input_item"><?php ABPRF_Layout::rent_end_month( $post_id, $upcoming_date ); ?></div>
					<?php }
				} ?>
				<?php if ( $rent_rule == 'hourly' || $rent_rule =='daily' || $rent_rule=='multi_day' || $rent_rule=='multi_month' ) { ?>
                    <div class="start_date _input_item"><?php ABPRF_Layout::rent_start_date( $all_dates, $upcoming_date ); ?></div>
				<?php } ?>
				<?php if ( $rent_rule == 'hourly' || $rent_rule == 'multi_day' ) { ?>
                    <div class="start_time _input_item">
                        <label>
                            <span><i class="fas fa-calendar-check _mar_r_xxs"></i><?php esc_html_e( 'Pickup Time', 'abprf-rental-forge' ); ?><sup class="_color_required">*</sup></span>
                            <select class="_form_control" name="start_time"></select>
                        </label>
                    </div>
				<?php } ?>
				<?php if ($rent_rule =='daily' || $rent_rule=='multi_day' || $rent_rule=='multi_month' ) {
					$all_end_dates = ABPRF_Function::get_end_dates( $post_id, $upcoming_date,$all_dates );
					?>
                    <div class="end_date _input_item"><?php ABPRF_Layout::rent_end_date( $all_end_dates ); ?></div>
				<?php } ?>
				<?php if ( $rent_rule == 'hourly' || $rent_rule == 'multi_day' ) { ?>
                    <div class="end_time _input_item">
                        <label>
                            <span><i class="fas fa-calendar-check _mar_r_xxs"></i><?php esc_html_e( 'Drop-off Time', 'abprf-rental-forge' ); ?><sup class="_color_required">*</sup></span>
                            <select class="_form_control" name="end_time"></select>
                        </label>
                    </div>
				<?php } ?>
                <div class="_input_item_fj_between_fd_column">
                    <span></span>
                    <button type="submit" class="_btn_theme"><?php ABPRF_Layout::image_icon( $brand_icon, '_mar_r_xs' ); ?><?php esc_html_e( 'Check Availability', 'abprf-rental-forge' ); ?></button>
                </div>
            </form>
            <div class="date_details"></div>
        </div>
        <div class="toast_msg_area"></div>
		<?php
	}, 10, 2 );