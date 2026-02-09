<?php
	if (!defined('ABSPATH')) {
		exit; // Exit if accessed directly
	}
	if (!class_exists('ABPRF_Configuration_Post')) {
		class ABPRF_Configuration_Post {
			public function __construct() {
				add_action('add_meta_boxes', [$this, 'settings_meta']);
				add_action('save_post', array($this, 'save_settings'));
			}
			//=============================//
			public function settings_meta(): void {
				$abprf_configuration = ABPRF_LIB_Function::get_option('abprf_configuration');
				$label = isset($abprf_configuration['label']) && $abprf_configuration['label'] ? $abprf_configuration['label'] : __('Transportation', 'abprf-rental-forge');
				$transport_icon = isset($abprf_configuration['transport_icon']) && $abprf_configuration['transport_icon'] ? $abprf_configuration['transport_icon'] : 'fas fa-bus';
				$label = $label . ' ' . __('Configuration', 'abprf-rental-forge') . get_the_title(get_the_id());
				add_meta_box('abprf_configuration', '<span class="' . esc_attr($transport_icon ?: '') . '"></span>' . esc_html($label), array($this, 'settings'), esc_attr(ABPRF_Function::get_cpt()), 'normal', 'high');
			}
			//=============================//
			public function settings(): void {
				$post_id = get_the_id();
				$abprf_infos = ABPRF_LIB_Function::get_all_meta($post_id);
				wp_nonce_field('abprf_post_nonce', 'abprf_post_nonce');
				?>
                <input type="hidden" name="abprf_post_id" value="<?php echo esc_attr($post_id); ?>"/>
                <div class="abprf_area">
                    <div class="_reflex_6_abprf_panel">
                        <div class="abprf_tabs tab_left">
                            <ul class="_abprf tab_lists">
                                <li data-tabs-target="#abptm_general"><span class="fas fa-rainbow"></span><?php esc_html_e('General Configuration', 'abprf-rental-forge'); ?></li>
                                <li data-tabs-target="#abptm_dates"><span class="fas fa-calendar-check"></span><?php esc_html_e('Date Configuration', 'abprf-rental-forge'); ?></li>
                                <li data-tabs-target="#abptm_seats"><span class="fas fa-chair"></span><?php esc_html_e('Ticket Configuration', 'abprf-rental-forge'); ?></li>
                                <li data-tabs-target="#abptm_routing"><span class="fas fa-route"></span><?php esc_html_e('Route Configuration', 'abprf-rental-forge'); ?></li>
                                <li data-tabs-target="#abprf_pricing"><span class="fas fa-dollar-sign"></span><?php esc_html_e('Price Configuration', 'abprf-rental-forge'); ?></li>
                                <li data-tabs-target="#abprf_additional_service"><span class="fas fa-hand-holding-usd"></span><?php esc_html_e('Additional services', 'abprf-rental-forge'); ?></li>
								<?php do_action('abprf_post_tab_menu', $abprf_infos); ?>
                                <li data-tabs-target="#abprf_slider"><span class="fas fa-photo-video"></span><?php esc_html_e('Slider Configuration', 'abprf-rental-forge'); ?></li>
                                <li data-tabs-target="#abptm_tax"><span class="fas fa-money-bill-wave"></span><?php esc_html_e('Tax Configuration', 'abprf-rental-forge'); ?></li>
                            </ul>
                            <div class="tab_content">
								<?php do_action('abprf_post_content', $abprf_infos); ?>
                            </div>
                        </div>
                    </div>
                </div>
				<?php
			}
			public function save_settings($post_id): void {
				if (!isset($_POST['abprf_post_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['abprf_post_nonce'])), 'abprf_post_nonce') && defined('DOING_AUTOSAVE') && DOING_AUTOSAVE && !current_user_can('edit_post', $post_id)) {
					return;
				}
				if (get_post_type($post_id) == ABPRF_Function::get_cpt()) {
					$meta_info = [];
					$meta_info['sale_continue'] = isset($_POST['sale_continue']) ? sanitize_text_field(wp_unslash($_POST['sale_continue'])) : 'off';
					$meta_info['display_transport_id'] = isset($_POST['display_transport_id']) ? sanitize_text_field(wp_unslash($_POST['display_transport_id'])) : 'off';
					$meta_info['transport_id'] = isset($_POST['transport_id']) ? sanitize_text_field(wp_unslash($_POST['transport_id'])) : '';
					$meta_info['display_category'] = isset($_POST['display_category']) ? sanitize_text_field(wp_unslash($_POST['display_category'])) : 'off';
					$meta_info['category'] = isset($_POST['category']) ? sanitize_text_field(wp_unslash($_POST['category'])) : '';
					$meta_info['display_organizer'] = isset($_POST['display_organizer']) ? sanitize_text_field(wp_unslash($_POST['display_organizer'])) : 'off';
					$meta_info['organizer'] = isset($_POST['organizer']) ? sanitize_text_field(wp_unslash($_POST['organizer'])) : '';
					$meta_info['abptm_template'] = isset($_POST['abptm_template']) ? sanitize_text_field(wp_unslash($_POST['abptm_template'])) : 'default';
					//=============================//
					$meta_info['date_type'] = isset($_POST['date_type']) ? sanitize_text_field(wp_unslash($_POST['date_type'])) : 'periodic_date';
					$periodic_start_date = isset($_POST['periodic_start_date']) ? sanitize_text_field(wp_unslash($_POST['periodic_start_date'])) : '';
					$periodic_end_date = isset($_POST['periodic_end_date']) ? sanitize_text_field(wp_unslash($_POST['periodic_end_date'])) : '';
					$meta_info['periodic_start_date'] = $periodic_start_date ? gmdate('Y-m-d', strtotime($periodic_start_date)) : '';
					$meta_info['periodic_end_date'] = $periodic_end_date ? gmdate('Y-m-d', strtotime($periodic_end_date)) : '';
					$meta_info['periodic_after'] = isset($_POST['periodic_after']) ? sanitize_text_field(wp_unslash($_POST['periodic_after'])) : '1';
					$meta_info['advance_date_number'] = isset($_POST['advance_date_number']) ? sanitize_text_field(wp_unslash($_POST['advance_date_number'])) : '';
					$meta_info['weekend'] = isset($_POST['weekend']) ? sanitize_text_field(wp_unslash($_POST['weekend'])) : '';
					$specific_off_dates = isset($_POST['specific_off_dates']) && is_array($_POST['specific_off_dates']) ? array_map('sanitize_text_field', wp_unslash($_POST['specific_off_dates'])) : [];
					$off_dates = array();
					if (sizeof($specific_off_dates) > 0) {
						foreach ($specific_off_dates as $off_date) {
							if ($off_date) {
								$off_dates[] = gmdate('Y-m-d', strtotime($off_date));
							}
						}
					}
					$meta_info['specific_off_dates'] = array_unique($off_dates);
					$off_schedules = [];
					$from_dates = isset($_POST['abptm_off_from']) && is_array($_POST['abptm_off_from']) ? array_map('sanitize_text_field', wp_unslash($_POST['abptm_off_from'])) : [];
					$to_dates = isset($_POST['abptm_off_to']) && is_array($_POST['abptm_off_to']) ? array_map('sanitize_text_field', wp_unslash($_POST['abptm_off_to'])) : [];
					if (sizeof($from_dates) > 0) {
						foreach ($from_dates as $key => $from_date) {
							if ($from_date && $to_dates[$key]) {
								$off_schedules[] = [
									'from' => $from_date,
									'to' => $to_dates[$key],
								];
							}
						}
					}
					$meta_info['off_date_range'] = $off_schedules;
					$specific_dates = isset($_POST['specific_dates']) ? array_map('sanitize_text_field', wp_unslash($_POST['specific_dates'])) : [];
					$specific = array();
					if (sizeof($specific_dates) > 0) {
						foreach ($specific_dates as $specific_date) {
							if ($specific_date) {
								$specific[] = gmdate('Y-m-d', strtotime($specific_date));
							}
						}
					}
					$meta_info['specific_dates'] = array_unique($specific);
					//=============================//
					$seat_type = isset($_POST['seat_type']) ? sanitize_text_field(wp_unslash($_POST['seat_type'])) : 'seat_plan';
					$total_seat = 0;
					if ($seat_type == 'seat_plan') {
						$ticket_type = isset($_POST['ticket_type']) ? sanitize_text_field(wp_unslash($_POST['ticket_type'])) : '';
						$display_ticket_type = isset($_POST['display_ticket_type']) ? sanitize_text_field(wp_unslash($_POST['display_ticket_type'])) : 'off';
						$ticket_type = $display_ticket_type == 'on' ? $ticket_type : '';
						//=============================//
						$ld_infos = [];
						$hidden_ids = isset($_POST['abptm_sp_row_id']) ? array_map('sanitize_text_field', wp_unslash($_POST['abptm_sp_row_id'])) : [];
						if (sizeof($hidden_ids) > 0) {
							foreach ($hidden_ids as $hidden_id) {
								if ($hidden_id) {
									$row_infos = isset($_POST[$hidden_id]) ? array_map('sanitize_text_field', wp_unslash($_POST[$hidden_id])) : [];
									$ld_infos[] = $row_infos;
									if (sizeof($row_infos) > 0) {
										foreach ($row_infos as $row_info) {
											$ticket_name = explode('_@@_', $row_info)[0];
											$col = explode('_&&_', $row_info)[1];
											if ($ticket_name == 'ticket' && $col > 0) {
												$total_seat++;
											}
										}
									}
								}
							}
						}
						$rows = sizeof($ld_infos);
						$columns = $rows > 0 ? sizeof(current($ld_infos)) : 0;
						$meta_info['display_ticket_type'] = $display_ticket_type;
						$meta_info['ticket_type'] = $ticket_type;
						$meta_info['ld_infos'] = $ld_infos;
						$meta_info['ld_rows'] = $rows;
						$meta_info['ld_columns'] = $columns;
						//=============================//
						$ud_infos = [];
						$hidden_ids = isset($_POST['abptm_sp_ud_row_id']) ? array_map('sanitize_text_field', wp_unslash($_POST['abptm_sp_ud_row_id'])) : [];
						if (sizeof($hidden_ids) > 0) {
							foreach ($hidden_ids as $hidden_id) {
								if ($hidden_id) {
									$row_infos = isset($_POST[$hidden_id]) ? array_map('sanitize_text_field', wp_unslash($_POST[$hidden_id])) : [];
									$ud_infos[] = $row_infos;
									if (sizeof($row_infos) > 0) {
										foreach ($row_infos as $row_info) {
											$ticket_name = explode('_@@_', $row_info)[0];
											$col = explode('_&&_', $row_info)[1];
											if ($ticket_name == 'ticket' && $col > 0) {
												$total_seat++;
											}
										}
									}
								}
							}
						}
						$rows = sizeof($ud_infos);
						$columns = $rows > 0 ? sizeof(current($ud_infos)) : 0;
						$meta_info['display_ud'] = isset($_POST['display_ud']) ? sanitize_text_field(wp_unslash($_POST['display_ud'])) : 'off';
						$meta_info['ud_infos'] = $ud_infos;
						$meta_info['ud_rows'] = $rows;
						$meta_info['ud_columns'] = $columns;
					} else {
						$ticket_infos = array();
						$hidden_ids = isset($_POST['ticket_type_hidden_id']) ? array_map('sanitize_text_field', wp_unslash($_POST['ticket_type_hidden_id'])) : [];
						if (sizeof($hidden_ids) > 0) {
							$icon = isset($_POST['abptm_ticket_icon']) ? array_map('sanitize_text_field', wp_unslash($_POST['abptm_ticket_icon'])) : [];
							$name = isset($_POST['abptm_ticket_name']) ? array_map('sanitize_text_field', wp_unslash($_POST['abptm_ticket_name'])) : [];
							$qty = isset($_POST['ticket_qty']) ? array_map('sanitize_text_field', wp_unslash($_POST['ticket_qty'])) : [];
							$max_qty = isset($_POST['abptm_ticket_max_qty']) ? array_map('sanitize_text_field', wp_unslash($_POST['abptm_ticket_max_qty'])) : [];
							$description = isset($_POST['abptm_ticket_description']) ? array_map('sanitize_textarea_field', wp_unslash($_POST['abptm_ticket_description'])) : [];
							foreach ($hidden_ids as $key => $hidden_id) {
								if ($name[$key] && $qty[$key] > 0) {
									$ticket_infos[$hidden_id]['icon'] = $icon[$key] ?? '';
									$ticket_infos[$hidden_id]['name'] = $name[$key];
									$ticket_infos[$hidden_id]['qty'] = $qty[$key];
									$ticket_infos[$hidden_id]['max_qty'] = $max_qty[$key];
									$ticket_infos[$hidden_id]['description'] = $description[$key] ?? '';
									$total_seat = $total_seat + $qty[$key];
								}
							}
						}
						$meta_info['abptm_ticket_info'] = $ticket_infos;
					}
					$meta_info['seat_type'] = $seat_type;
					$meta_info['total_seat'] = $total_seat;
					//=============================//
					$meta_info['display_pickup'] = isset($_POST['display_pickup']) ? sanitize_text_field(wp_unslash($_POST['display_pickup'])) : 'off';
					$meta_info['required_pickup'] = isset($_POST['required_pickup']) ? sanitize_text_field(wp_unslash($_POST['required_pickup'])) : 'off';
					$meta_info['display_drop'] = isset($_POST['display_drop']) ? sanitize_text_field(wp_unslash($_POST['display_drop'])) : 'off';
					$meta_info['required_drop'] = isset($_POST['required_drop']) ? sanitize_text_field(wp_unslash($_POST['required_drop'])) : 'off';
					$all_stop = [];
					$route_infos = array();
					$route_direction = [];
					$bp = [];
					$dp = [];
					$stops = isset($_POST['abptm_stop']) ? array_map('sanitize_text_field', wp_unslash($_POST['abptm_stop'])) : [];
					$types = isset($_POST['abptm_type']) ? array_map('sanitize_text_field', wp_unslash($_POST['abptm_type'])) : [];
					$times = isset($_POST['abptm_time']) ? array_map('sanitize_text_field', wp_unslash($_POST['abptm_time'])) : [];
					$route_hidden_id = isset($_POST['route_hidden_id']) ? array_map('sanitize_text_field', wp_unslash($_POST['route_hidden_id'])) : [];
					$count = count($stops);
					for ($i = 0; $i < $count; $i++) {
						$stop = $stops[$i] ?? '';
						$type = $types[$i] ?? '';
						if ($stop && $type) {
							$route_infos[$i]['stop'] = $stop;
							$route_infos[$i]['type'] = $type;
							$route_infos[$i]['time'] = $times[$i] ?? '';
							$route_direction[] = $stop;
							$all_stop[] = $stop;
							$id = $route_hidden_id[$i];
							if ($id) {
								if ($type == 'bp' || $type == 'both') {
									$pickup_info = [];
									$pickup_names = isset($_POST['pickup_name_' . $id]) ? array_map('sanitize_text_field', wp_unslash($_POST['pickup_name_' . $id])) : [];
									$pick_times = isset($_POST['pickup_time_' . $id]) ? array_map('sanitize_text_field', wp_unslash($_POST['pickup_time_' . $id])) : [];
									if (sizeof($pickup_names) > 0) {
										foreach ($pickup_names as $key => $pickup_name) {
											if ($pickup_name) {
												$pickup_info[$key]['name'] = $pickup_name;
												$pickup_info[$key]['time'] = $pick_times[$key] ?? '';
												$all_stop[] = $pickup_name;
											}
										}
									}
									$route_infos[$i]['pickup_infos'] = $pickup_info;
								}
								if ($type == 'dp' || $type == 'both') {
									$drop_info = [];
									$drop_names = isset($_POST['drop_name_' . $id]) ? array_map('sanitize_text_field', wp_unslash($_POST['drop_name_' . $id])) : [];
									$drop_times = isset($_POST['drop_time_' . $id]) ? array_map('sanitize_text_field', wp_unslash($_POST['drop_time_' . $id])) : [];
									if (sizeof($drop_names) > 0) {
										foreach ($drop_names as $key => $drop_name) {
											if ($drop_name) {
												$drop_info[$key]['name'] = $drop_name;
												$drop_info[$key]['time'] = $drop_times[$key] ?? '';
												$all_stop[] = $drop_name;
											}
										}
									}
									$route_infos[$i]['drop_infos'] = $drop_info;
								}
							}
						}
					}
					$count = sizeof($route_infos);
					if ($count > 0) {
						$route_infos[0]['type'] = 'bp';
						$route_infos[0]['drop_infos'] = [];
						$route_infos[$count - 1]['type'] = 'dp';
						$route_infos[$count - 1]['pickup_infos'] = [];
						foreach ($route_infos as $route_info) {
							if ($route_info['type'] == 'bp') {
								$bp[] = $route_info['stop'];
							} elseif ($route_info['type'] == 'dp') {
								$dp[] = $route_info['stop'];
							} else {
								$bp[] = $route_info['stop'];
								$dp[] = $route_info['stop'];
							}
						}
					}
					$meta_info['routing_infos'] = $route_infos;
					$meta_info['route_direction'] = $route_direction;
					$meta_info['abptm_bp'] = $bp;
					$meta_info['abptm_dp'] = $dp;
					$meta_info['abprf_stops'] = $all_stop;
					//echo '<pre>';print_r($meta_info);echo '</pre>';die();
					//=============================//
					$price_infos = [];
					$abptm_from = isset($_POST['abptm_from']) ? array_map('sanitize_text_field', wp_unslash($_POST['abptm_from'])) : [];
					$abptm_to = isset($_POST['abptm_to']) ? array_map('sanitize_text_field', wp_unslash($_POST['abptm_to'])) : [];
					if ($seat_type == 'seat_plan') {
						$price = isset($_POST['abptm_price']) ? array_map('sanitize_text_field', wp_unslash($_POST['abptm_price'])) : [];
						$adult_price = isset($_POST['abptm_adult_price']) ? array_map('sanitize_text_field', wp_unslash($_POST['abptm_adult_price'])) : [];
						$child_price = isset($_POST['abptm_child_price']) ? array_map('sanitize_text_field', wp_unslash($_POST['abptm_child_price'])) : [];
						$infant_price = isset($_POST['abptm_infant_price']) ? array_map('sanitize_text_field', wp_unslash($_POST['abptm_infant_price'])) : [];
						if (sizeof($abptm_from) > 0) {
							foreach ($abptm_from as $key => $from) {
								if ($from && $abptm_to[$key]) {
									$d_price = array_key_exists($key, $price) ? $price[$key] : '';
									$a_price = array_key_exists($key, $adult_price) ? $adult_price[$key] : '';
									$d_price = $d_price ?: $a_price;
									$a_price = $a_price ?: $d_price;
									$price_infos[] = [
										'bp' => $from,
										'dp' => $abptm_to[$key],
										'price' => $d_price,
										'adult' => $a_price,
										'child' => array_key_exists($key, $child_price) ? $child_price[$key] : '',
										'infant' => array_key_exists($key, $infant_price) ? $infant_price[$key] : '',
									];
								}
							}
						}
					} else {
						$hidden_ids = isset($_POST['ticket_type_hidden_id']) ? array_map('sanitize_text_field', wp_unslash($_POST['ticket_type_hidden_id'])) : [];
						$names = isset($_POST['abptm_ticket_name']) ? array_map('sanitize_text_field', wp_unslash($_POST['abptm_ticket_name'])) : [];
						$qty = isset($_POST['ticket_qty']) ? array_map('sanitize_text_field', wp_unslash($_POST['ticket_qty'])) : [];
						if (sizeof($names) > 0 && sizeof($qty) > 0 && sizeof($abptm_from) > 0 && sizeof($hidden_ids) > 0) {
							$prices = [];
							foreach ($hidden_ids as $hidden_id) {
								$prices[$hidden_id] = isset($_POST['abptm_' . $hidden_id . '_price']) ? array_map('sanitize_text_field', wp_unslash($_POST['abptm_' . $hidden_id . '_price'])) : [];
							}
							foreach ($abptm_from as $key => $from) {
								if ($from && $abptm_to[$key]) {
									$price_infos[$key] = ['bp' => $from, 'dp' => $abptm_to[$key]];
									foreach ($names as $_key => $name) {
										if ($qty[$_key] && $hidden_ids[$_key]) {
											$price_infos[$key][$hidden_ids[$_key]] = $prices[$hidden_ids[$_key]][$key];
										}
									}
								}
							}
						}
					}
					$meta_info['price_infos'] = $price_infos;
					//=============================//
					$meta_info['display_additional_services'] = isset($_POST['display_additional_services']) ? sanitize_text_field(wp_unslash($_POST['display_additional_services'])) : 'off';
					$meta_info['display_single_additional'] = isset($_POST['display_single_additional']) ? sanitize_text_field(wp_unslash($_POST['display_single_additional'])) : 'on';
					$meta_info['additional_services'] = ABPRF_Additional::service_info();
					//===========gallery==================//
					$images = isset($_POST['abprf_sliders']) ? sanitize_text_field(wp_unslash($_POST['abprf_sliders'])) : '';
					$meta_info['display_slider'] = isset($_POST['display_slider']) ? sanitize_text_field(wp_unslash($_POST['display_slider'])) : 'off';
					$meta_info['abprf_sliders'] = explode(',', $images);
					//=============================//
					if (get_option('woocommerce_calc_taxes') == 'yes') {
						$meta_info['_tax_status'] = isset($_POST['_tax_status']) ? sanitize_text_field(wp_unslash($_POST['_tax_status'])) : 'none';
						$meta_info['_tax_class'] = isset($_POST['_tax_class']) ? sanitize_text_field(wp_unslash($_POST['_tax_class'])) : '';
					}
					//=============================//
					$meta_info = apply_filters('abptm_meta_info_update', $meta_info);
					if (sizeof($meta_info) > 0) {
						foreach ($meta_info as $key => $value) {
							update_post_meta($post_id, sanitize_key($key), $value);
						}
					}
				}
			}
		}
		new ABPRF_Configuration_Post();
	}