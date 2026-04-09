<?php
	if (!defined('ABSPATH')) {
		die;
	} // Cannot access pages directly
	$form_data = $form_data ?? [];
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$bp = array_key_exists('bp', $form_data) ? $form_data['bp'] : '';
	$dp = array_key_exists('dp', $form_data) ? $form_data['dp'] : '';
	$bp_date = array_key_exists('j_date', $form_data) ? $form_data['j_date'] : '';
	$transport_item = $transport_item ?? [];
	$equipment_id = is_array($transport_item) && array_key_exists('id', $transport_item) ? $transport_item['id'] : 0;
	$bp_time = is_array($transport_item) && array_key_exists('time', $transport_item) ? $transport_item['time'] : '';
	$dp_time = is_array($transport_item) && array_key_exists('dp_time', $transport_item) ? $transport_item['dp_time'] : '';
	$rent_continue = ABPRF_Function::get_post_info($equipment_id, 'rent_continue', 'on');
	if ($equipment_id && $equipment_id > 0 && $rent_continue == 'on') {
		$full_infos = ABPRF_Function::get_route_full_info($equipment_id, $bp, $bp_date);
		$origin_time = sizeof($full_infos) > 0 ? current($full_infos)['time'] : '';
		$collapse_id = '#' . uniqid();
		$abprf_infos = ABPRF_Function::get_all_meta($equipment_id);
		$abprf_infos['post_id'] = $equipment_id;
		$seat_type = array_key_exists('seat_type', $abprf_infos) ? $abprf_infos['seat_type'] : 'seat_plan';
		$seat_ticket_key = $seat_type == 'seat_plan' ? 'seat' : 'ticket';
		$abprf_infos['booking_info'] = ABPRF_Query::get_sold_info($equipment_id, $bp, $dp, $origin_time, $seat_type);
		$sold_seats = sizeof(array_key_exists('seat', $abprf_infos['booking_info']) ? $abprf_infos['booking_info']['seat'] : []);
		$total_seat = array_key_exists('total_seat', $abprf_infos) ? $abprf_infos['total_seat'] : '';
		$available_seat = $total_seat - $sold_seats;
		$display_slider = array_key_exists('display_slider', $abprf_infos) ? $abprf_infos['display_slider'] : 'on';
		?>
        <div class="_section_xs_pad_zero transportation_item">
            <form class="_fd_column">
                <input type="hidden" name="post_id" value="<?php echo esc_attr($equipment_id); ?>"/>
                <input type="hidden" name="bp" value="<?php echo esc_attr($bp); ?>"/>
                <input type="hidden" name="dp" value="<?php echo esc_attr($dp); ?>"/>
                <input type="hidden" name="j_date" value="<?php echo esc_attr($bp_date); ?>"/>
				<?php ABPRF_Layout::hidden_search_form($form_data); ?>
                <div class="transportation_item_content">
					<?php do_action('abprf_category', $abprf_infos, true);
						do_action('abprf_slider_only', $abprf_infos, 'transport_slider');
					?>
                    <div class="transport_details">
                        <div class="details_left">
                            <a class="_abprf" href="<?php echo esc_url(get_the_permalink($equipment_id) . '?_bp= ' . $bp . '&_dp=' . $dp . '&_j_date=' . $bp_date); ?>" target="_blank"><?php do_action('abprf_title', $abprf_infos); ?></a>
                            <div class="details_item">
                                <div class="item_left">
                                    <p class="_abprf"><span class="fas fa-map-marker-alt _mar_r_xs"></span><?php echo esc_html(__('From : ', 'abprf-rental-forge') . $bp); ?></p>
                                    <p class="_abprf"><span class="fas fa-map-marker-alt _mar_r_xs"></span><?php echo esc_html(__('To : ', 'abprf-rental-forge') . $dp); ?></p>
                                    <p class="_abprf"><span class="fas fa-calendar-day _mar_r_xs"></span><?php echo esc_html( __('Date : ', 'abprf-rental-forge') . ABPRF_Function::date_format($bp_time)); ?></p>
                                    <p class="_abprf"><span class="fas fa-business-time _mar_r_xs"></span><?php echo esc_html( __('Approximate Time : ', 'abprf-rental-forge') . ABPRF_Function::get_date_time_difference($bp_time, $dp_time)); ?></p>
                                </div>
                                <div class="details_item_info">
                                    <div class="item_departure">
                                        <h6 class="_abprf_color_theme"><?php echo esc_html(ABPRF_Function::date_format($bp_time, 'time')); ?></h6>
                                        <span><?php esc_html_e('Departure Time', 'abprf-rental-forge'); ?></span>
                                    </div>
                                    <div class="item_arrival">
                                        <h6 class="_abprf_color_theme"><?php echo esc_html(ABPRF_Function::date_format($dp_time, 'time')); ?></h6>
                                        <span><?php esc_html_e('Arrival Time', 'abprf-rental-forge'); ?></span>
                                    </div>
                                    <div class="item_available">
                                        <h6 class="_abprf_color_theme"><?php echo esc_html($available_seat); ?></h6>
                                        <span><?php echo esc_html(ABPRF_Function::get_available_text($seat_type, $available_seat)); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="details_right">
							<?php if ($seat_type == 'seat_plan') {
								$item_price = ABPRF_Function::get_price($equipment_id, $bp, $dp);
								echo '<h3 class="_abprf_color_theme_text_center">' . wp_kses_post(wc_price($item_price)) . '</h3>';
							}
								if ($available_seat > 0) { ?>
                                    <button type="button" class="_btn_light_theme_xs_mar_t_xs abprf_get_rental_details"
                                            data-collapse-target="<?php echo esc_attr($collapse_id); ?>"
                                            data-open-icon="fas fa-eye" data-close-icon="fas fa-times"
                                            data-add-class="_btn_warning"
                                            data-close-text="<?php echo esc_attr(ABPRF_Function::get_view_text($seat_type)); ?>" data-open-text="<?php esc_attr_e('Close', 'abprf-rental-forge'); ?>"
                                    >
                                        <span data-icon class="fas fa-eye _mar_r_xs"></span>
                                        <span data-text><?php echo esc_html(ABPRF_Function::get_view_text($seat_type)); ?></span>
                                    </button>
								<?php } ?>
                        </div>
                    </div>
                </div>
                <div data-collapse="<?php echo esc_attr($collapse_id); ?>">
                    <div class="route_details"><?php do_action('abptm_route_direction', $abprf_infos); ?></div>
                </div>
            </form>
            <div class="abprf_rental_details"></div>
        </div>
		<?php
	}
