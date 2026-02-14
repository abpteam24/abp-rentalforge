<?php
	if (!defined('ABSPATH')) {
		exit; // Exit if accessed directly
	}
	$abprf_infos = $abprf_infos ?? [];
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$post_id = array_key_exists('post_id', $abprf_infos) ? $abprf_infos['post_id'] : 0;
	if ($post_id > 0) {
		$display_equipment_id = array_key_exists('display_equipment_id', $abprf_infos) ? $abprf_infos['display_equipment_id'] : ABPRF_LIB_Function::get_post_info($post_id, 'display_equipment_id', 'on');
		$equipment_id = array_key_exists('equipment_id', $abprf_infos) ? $abprf_infos['equipment_id'] : ABPRF_LIB_Function::get_post_info($post_id, 'equipment_id');
		$equipment_icon = ABPRF_LIB_Function::get_equipment_icon();
		$equipment_icon = $equipment_icon ? $equipment_icon . ' _mar_r_xs' : '';
		?>
        <h5 class="_abprf_color_theme">
            <span class="<?php echo esc_attr($equipment_icon); ?> "></span>
			<?php echo esc_html(get_the_title($post_id)); ?>
			<?php if ($equipment_id && $display_equipment_id == 'on') { ?>
                <small class="_abprf_color_gray">&nbsp;(<?php echo esc_html($equipment_id); ?>)</small>
			<?php } ?>
        </h5>
		<?php
	}
