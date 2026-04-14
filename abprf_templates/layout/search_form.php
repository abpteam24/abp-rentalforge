<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	add_action( 'abprf_template_search_form', function ( $all_dates, $params = [] ) {
		$params_form = array_key_exists( 'form', $params ) ? $params['form'] : 'inline';
		$post_id     = array_key_exists( 'post_id', $params ) ? $params['post_id'] : '';
		$brand_icon  = ABPRF_Function::get_brand_icon();
		?>
        <div id="abprf_search_area">
            <h2 class="_abprf_mar_b_xs"><span class="_mar_r_xxs">📅</span><?php esc_html_e( 'Select Rental Period', 'abprf-rental-forge' ); ?></h2>
            <form class="<?php echo esc_attr( $params_form == 'column' ? '_form_column' : '_form_inline' ); ?>" method="post" action="">
                <input type="hidden" name="post_id" value="<?php echo esc_attr( $post_id ); ?>"/>
                <div class="abprf_start_date _input_item"><?php ABPRF_Layout::rent_start_date( $all_dates ); ?></div>
                <div class="_input_item_fj_between_fd_column">
                    <span></span>
                    <button type="button" class="_btn_theme abprf_get_rental"><?php ABPRF_Layout::image_icon( $brand_icon, '_mar_r_xs' ); ?><?php esc_html_e( 'Check Availability', 'abprf-rental-forge' ); ?></button>
                </div>
            </form>
        </div>
		<?php
	}, 10, 2 );