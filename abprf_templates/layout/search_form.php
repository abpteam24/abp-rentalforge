<?php
	if (!defined('ABSPATH')) {
		exit; // Exit if accessed directly
	}
	$abprf_infos = $abprf_infos ?? [];
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$params = $params ?? [];
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$form_data = $form_data ?? ABPRF_Function::get_form_data($abprf_infos);
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	//================================//
	$transport_id = array_key_exists('_post_id', $form_data) ? $form_data['_post_id'] : 0;
	$transport_bp = array_key_exists('_bp', $form_data) ? $form_data['_bp'] : '';
	$transport_dp = array_key_exists('_dp', $form_data) ? $form_data['_dp'] : '';
	$bp_date = array_key_exists('_j_date', $form_data) ? $form_data['_j_date'] : '';
	$return_date = array_key_exists('_r_date', $form_data) ? $form_data['_r_date'] : '';
	$single_post = array_key_exists('single_post', $form_data) ? $form_data['single_post'] : '';
	$abptm_route_bp = ABPRF_Function::get_routes($transport_id);
	$abptm_route_dp = ABPRF_Function::get_routes($transport_id, false);
	//================================//
	$params_form = array_key_exists('form', $params) ? $params['form'] : 'inline';
	$params_transport = array_key_exists('transport', $params) ? $params['transport'] : '';
	$params_return = array_key_exists('return', $params) ? $params['return'] : '';
	$enable_return = ABPRF_LIB_Function::get_options('abprf_layout', 'enable_return', 'on');
	//=============================//
	$transport_icon = ABPRF_LIB_Function::get_transport_icon();
	$transport_icon = $transport_icon ? $transport_icon . ' _mar_r_xs' : '';
	//=============================//
	$enable_transport_search = ABPRF_LIB_Function::get_options('abprf_layout', 'enable_transport_search', 'off');
	$redirect_search = ABPRF_LIB_Function::get_options('abprf_layout', 'redirect_search');
	$submit_url = $redirect_search && !is_admin() && $transport_id == 0 && !$single_post ? get_home_url() . '/' . get_page_uri($redirect_search) : '';
?>
    <div id="abprf_search_area">
        <form class="_section_light <?php echo esc_attr($params_form == 'column' ? '_form_column' : '_form_inline'); ?>" method="get" action="<?php echo esc_url($submit_url); ?>">
			<?php
				wp_nonce_field('abptm_search_form_nonce', 'abptm_search_form_nonce');
                if ($params_transport == 'on' || is_admin() || (($enable_transport_search == 'on' && $params_transport != 'off') && (($transport_id && $single_post) || !$single_post))) {
				ABPRF_Layout::filter_transport($transport_id);
			}
				if ($single_post) { ?>
                    <input type="hidden" name="_post_id" value="<?php echo esc_attr($transport_id); ?>"/>
				<?php } ?>
            <input type="hidden" name="single_post" value="<?php echo esc_attr($single_post); ?>"/>
            <div class="abptm_bp dropdown_area _input_item"><?php ABPRF_Layout::boarding_from($abptm_route_bp, $transport_bp); ?></div>
            <div class="abptm_dp dropdown_area _input_item"><?php ABPRF_Layout::dropping_from($abptm_route_dp, $transport_dp); ?></div>
            <div class="abptm_bp_date _input_item"><?php ABPRF_Layout::departure_date($transport_id, $transport_bp, $bp_date); ?></div>
			<?php if (($enable_return == 'on' && !$single_post && $params_return != 'off') || $params_return == 'on') { ?>
                <div class="abptm_return_date _input_item"><?php ABPRF_Layout::return_date($transport_bp, $transport_dp, $bp_date, $return_date); ?> </div>
			<?php } ?>
            <div class="_input_item_fj_between_fd_column">
                <span></span>
				<?php if ($submit_url) { ?>
                    <button type="submit" class="_btn_theme abprf_submit"><span class="<?php echo esc_attr($transport_icon); ?>"></span><?php esc_html_e('Search', 'abprf-rental-forge'); ?></button>
				<?php } else { ?>
                    <button type="button" class="_btn_theme abprf_get_rental abprf_submit"><span class="<?php echo esc_attr($transport_icon); ?>"></span><?php esc_html_e('Search', 'abprf-rental-forge'); ?></button>
				<?php } ?>
            </div>
        </form>
    </div>
<?php
