<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	add_action( 'abprf_search_form_template', function ( $abprf_infos ) {
		$post_id            = array_key_exists( 'post_id', $abprf_infos ) ? $abprf_infos['post_id'] : '';
		$location            = array_key_exists( 'abprf_location', $abprf_infos ) ? $abprf_infos['abprf_location'] : '';

		$params_form        = array_key_exists( 'form', $abprf_infos ) ? $abprf_infos['form'] : 'inline';
		$rent_rule          = array_key_exists( 'rent_rule', $abprf_infos ) ? $abprf_infos['rent_rule'] : 'hourly';
		$brand_icon         = ABPRF_Function::brand_icon();
		//echo '<pre>';print_r( ABPRF_Function::get_time($post_id));					echo '</pre>';
		//echo '<pre>';print_r( ABPRF_Function::get_time($post_id,'js'));					echo '</pre>';
		do_action( 'abprf_generate_script_data', $post_id );
		if ( isset( $_SESSION['abprf_cart_success'] ) ) {
			?>
            <div class="abprf_add_to_cart_notice _d_none">
				<?php echo esc_html( sanitize_text_field( wp_unslash( $_SESSION['abprf_cart_success'] ) ) ); ?>
            </div>
			<?php
			unset( $_SESSION['abprf_cart_success'] );
		}
		?>
        <div id="abprf_search_area">
            <h2 class="_abprf_mar_b_xs"><span class="_mar_r_xxs">📅</span><?php esc_html_e( 'Select Rental Period', 'abprf-rental-forge' ); ?></h2>
            <form class="abprf_property_form <?php echo esc_attr( $params_form == 'column' ? '_form_column' : '_form_inline' ); ?>" method="post" action="">
                <input type="hidden" name="post_id" value="<?php echo esc_attr( $post_id ); ?>"/>
                <input type="hidden" name="rent_rule" value="<?php echo esc_attr( $rent_rule ); ?>"/>
                <?php ABPRF_Layout::location_select($post_id,$location); ?>
                <div class="start_date _input_item"><?php ABPRF_Layout::rent_start_date( $post_id ); ?></div>
				<?php if ( $rent_rule == 'hourly' ) { ?>
                    <div class="start_time _input_item">
                        <label>
                            <span><i class="fas fa-calendar-check _mar_r_xxs"></i><?php esc_html_e( 'Pickup Time', 'abprf-rental-forge' ); ?><sup class="_color_required">*</sup></span>
                            <select class="_form_control" name="start_time"></select>
                        </label>
                    </div>
				<?php } ?>
				<?php if ( $rent_rule == 'hourly' ) { ?>
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