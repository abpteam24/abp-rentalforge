<?php
	if (!defined('ABSPATH')) {
		exit; // Exit if accessed directly
	}
	$form_data = $form_data ?? [];
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$abprf_infos = $abprf_infos ?? [];
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$display_additional_services = array_key_exists('display_additional_services', $abprf_infos) ? $abprf_infos['display_additional_services'] : 'on';
	$additional_services = array_key_exists('additional_services', $abprf_infos) ? $abprf_infos['additional_services'] : [];
	$display_single_additional = array_key_exists('display_single_additional', $abprf_infos) ? $abprf_infos['display_single_additional'] : 'on';
	if (!class_exists('ABPTM_Transport_Manager_Pro')) {
		if ($display_additional_services == 'on' && $display_single_additional == 'off' && sizeof($additional_services) > 0) {
			?>
            <div class="additional_item_hidden">
				<?php do_action('abprf_additional', $abprf_infos, $form_data); ?>
            </div>
			<?php
		}
	}
