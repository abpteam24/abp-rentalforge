<?php
	if (!defined('ABSPATH')) {
		exit; // Exit if accessed directly
	}
	$abprf_infos = $abprf_infos ?? [];
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$ribbon = $ribbon ?? false;
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$display_total_seat = array_key_exists('display_total_seat', $abprf_infos) ? $abprf_infos['display_total_seat'] : 'on';
	$total_seat = array_key_exists('total_seat', $abprf_infos) ? $abprf_infos['total_seat'] : '';
	if ($total_seat && $display_total_seat == 'on') {
		if ($ribbon) { ?>
            <div class="ribbon_right ">
				<?php esc_html_e('Capacity : ', 'abprf-rental-forge'); ?>&nbsp;
				<?php echo esc_html($total_seat); ?>
            </div>
		<?php } else { ?>
            <span class="_fs_label_mar_r">
                <?php esc_html_e('Capacity : ', 'abprf-rental-forge'); ?>&nbsp;
                <?php echo  esc_html($total_seat); ?>
            </span>
			<?php
		}
	}