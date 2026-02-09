<?php
	if (!defined('ABSPATH')) {
		exit; // Exit if accessed directly
	}
	$abprf_infos = $abprf_infos ?? [];
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$post_id = array_key_exists('post_id', $abprf_infos) ? $abprf_infos['post_id'] : 0;
	if ($post_id > 0) {
		$display_transport_id = array_key_exists('display_transport_id', $abprf_infos) ? $abprf_infos['display_transport_id'] : ABPRF_LIB_Function::get_post_info($post_id, 'display_transport_id', 'on');
		$transport_id = array_key_exists('transport_id', $abprf_infos) ? $abprf_infos['transport_id'] : ABPRF_LIB_Function::get_post_info($post_id, 'transport_id');
		$transport_icon = ABPRF_LIB_Function::get_transport_icon();
		$transport_icon = $transport_icon ? $transport_icon . ' _mar_r_xs' : '';
		?>
        <h5 class="_abprf_color_theme">
            <span class="<?php echo esc_attr($transport_icon); ?> "></span>
			<?php echo esc_html(get_the_title($post_id)); ?>
			<?php if ($transport_id && $display_transport_id == 'on') { ?>
                <small class="_abprf_color_gray">&nbsp;(<?php echo esc_html($transport_id); ?>)</small>
			<?php } ?>
        </h5>
		<?php
	}
