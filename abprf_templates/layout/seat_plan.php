<?php
	if (!defined('ABSPATH')) {
		exit; // Exit if accessed directly
	}
	$form_data = $form_data ?? [];
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$post_id = array_key_exists('post_id', $form_data) ? $form_data['post_id'] : 0;
	$bp = array_key_exists('bp', $form_data) ? $form_data['bp'] : '';
	$dp = array_key_exists('dp', $form_data) ? $form_data['dp'] : '';
	$bp_date = array_key_exists('j_date', $form_data) ? $form_data['j_date'] : '';
	$origin_time = array_key_exists('origin_time', $form_data) ? $form_data['origin_time'] : '';
	$full_infos = array_key_exists('all_info', $form_data) ? $form_data['all_info'] : [];
	$origin_place = array_key_exists('origin_place', $form_data) ? $form_data['origin_place'] : '';
	$bp_time = array_key_exists('bp_time', $form_data) ? $form_data['bp_time'] : '';
	$dp_time = array_key_exists('dp_time', $form_data) ? $form_data['dp_time'] : '';
	$abprf_infos = $abprf_infos ?? [];
	$seat_type = array_key_exists('seat_type', $abprf_infos) ? $abprf_infos['seat_type'] : 'seat_plan';
	if ($bp && $dp && $bp_date && $post_id > 0 && sizeof($full_infos) > 0 && $seat_type == 'seat_plan') {
		$rows = ABPRF_LIB_Function::get_post_info($post_id, 'ld_rows', 0);
		$columns = ABPRF_LIB_Function::get_post_info($post_id, 'ld_columns', 0);
		$sp_ld_infos = ABPRF_LIB_Function::get_post_info($post_id, 'ld_infos', []);
		$display_ud = array_key_exists('display_ud', $abprf_infos) ? $abprf_infos['display_ud'] : 'off';
		$sp_ud_infos = array_key_exists('ud_infos', $abprf_infos) ? $abprf_infos['ud_infos'] : [];
		$rows_ud = array_key_exists('ud_rows', $abprf_infos) ? $abprf_infos['ud_rows'] : 0;
		$columns_ud = array_key_exists('ud_columns', $abprf_infos) ? $abprf_infos['ud_columns'] : 0;
		if (sizeof($sp_ld_infos) > 0 && $rows > 0 && $columns > 0) {
			$sold_seats =array_key_exists('booking_info',$abprf_infos) && array_key_exists('seat',$abprf_infos['booking_info'])?$abprf_infos['booking_info']['seat']:[];
           // echo '<pre>';print_r($sold_seats);echo '</pre>';
			?>
            <div class="abptm_seat_plan_area">
                <div class="seat_plan_ld">
					<?php if ($display_ud == 'on' && sizeof($sp_ud_infos) > 0 && $rows_ud > 0 && $columns_ud > 0) { ?>
                        <h6 class="_abprf_text_center_color_1"><?php esc_html_e('Lower Deck', 'abprf-rental-forge'); ?></h6>
					<?php } ?>
                    <input type="hidden" name="selected_ld"/>
                    <input type="hidden" name="selected_ld_type"/>
					<?php ABPRF_Layout::get_seat_plan($abprf_infos, $bp, $dp, $bp_date, $sp_ld_infos, $sold_seats); ?>
                </div>
				<?php if ($display_ud == 'on' && sizeof($sp_ud_infos) > 0 && $rows_ud > 0 && $columns_ud > 0) { ?>
                    <div class="seat_plan_ud">
                        <h6 class="_abprf_text_center_color_1"><?php esc_html_e('Upper Deck', 'abprf-rental-forge'); ?></h6>
                        <input type="hidden" name="selected_ud"/>
                        <input type="hidden" name="selected_ud_type"/>
						<?php ABPRF_Layout::get_seat_plan($abprf_infos, $bp, $dp, $bp_date, $sp_ud_infos, $sold_seats, true); ?>
                    </div>
				<?php } ?>
            </div>
			<?php
		}
	}
