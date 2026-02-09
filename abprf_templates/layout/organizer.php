<?php
	if (!defined('ABSPATH')) {
		exit; // Exit if accessed directly
	}
	$abprf_infos = $abprf_infos ?? [];
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$display_organizer = array_key_exists('display_organizer', $abprf_infos) ? $abprf_infos['display_organizer'] : 'on';
	$organizer = array_key_exists('organizer', $abprf_infos) ? $abprf_infos['organizer'] : '';
	if ($organizer && $display_organizer == 'on') {
		$abprf_configuration = array_key_exists('abprf_configuration', $abprf_infos) ? $abprf_infos['abprf_configuration'] : [];
		$organizer_label = is_array($abprf_configuration) && array_key_exists('organizer_label', $abprf_configuration) && $abprf_configuration['organizer_label'] ? $abprf_configuration['organizer_label'] : __('Organizer', 'abprf-rental-forge');
		?>
        <span class="_fs_label_mar_r">
            <?php echo esc_html($organizer_label . __(' : ', 'abprf-rental-forge') . $organizer); ?>
		</span>
		<?php
	};