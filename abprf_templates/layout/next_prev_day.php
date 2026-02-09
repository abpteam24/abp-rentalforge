<?php
	if (!defined('ABSPATH')) {
		exit; // Exit if accessed directly
	}
	$form_data = $form_data ?? [];
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$bp = array_key_exists('bp', $form_data) ? $form_data['bp'] : '';
	$dp = array_key_exists('dp', $form_data) ? $form_data['dp'] : '';
	$j_date = array_key_exists('j_date', $form_data) ? $form_data['j_date'] : '';
	$all_dates = ABPRF_Function::get_dates(0, $bp, $dp);
	if (sizeof($all_dates) > 0) {
		$current_key = array_search($j_date, $all_dates);
		$prev_date = $current_key > 0 ? $all_dates[$current_key - 1] : '';
		$next_date = sizeof($all_dates) != $current_key + 1 ? $all_dates[$current_key + 1] : '';
		$prev_disabled = $prev_date ? '' : 'disabled';
		$next_disabled = $next_date ? '' : 'disabled';
		?>
        <div class="_fj_between_f_wrap_mar_tb_xs">
            <button type="button" class="_btn_default_light_xs abptm_goto_date" data-go_date="<?php echo esc_attr($prev_date); ?>" <?php echo esc_attr($prev_disabled); ?>>
                <span class="fas fa-angle-double-left _mar_r_xs"></span><?php esc_html_e('Prev Day', 'abprf-rental-forge'); ?>
            </button>
            <div class="_all_center">
                <h5 class="_abprf_text_nowrap _color_navy_blue"><?php echo esc_html($bp); ?></h5>
                <span class="fas fa-arrow-right _color_theme _mar_lr"></span>
                <h5 class="_abprf_text_nowrap _color_navy_blue"><?php echo esc_html($dp); ?></h5>
            </div>
            <button type="button" class="_btn_default_light_xs abptm_goto_date" data-go_date="<?php echo esc_attr($next_date); ?>" <?php echo esc_attr($next_disabled); ?>>
				<?php esc_html_e('Next Day', 'abprf-rental-forge'); ?><span class="fas fa-angle-double-right _mar_l_xs"></span>
            </button>
        </div>
		<?php
	}
