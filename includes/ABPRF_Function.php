<?php
	if (!defined('ABSPATH')) {
		exit; // Exit if accessed directly
	}
	if (!class_exists('ABPRF_Function')) {
		class ABPRF_Function {
			public function __construct() { }
			//=========== Template Related==================//
			public static function details_template_path($post_id): string {
				$post_id = $post_id ?? get_the_id();
				$template_name = ABPRF_LIB_Function::get_post_info($post_id, 'abprf_template', 'default');
				$file_name = 'details_theme/' . $template_name . '.php';
				$dir = ABPRF_DIR . '/abprf_templates/' . $file_name;
				if (!file_exists($dir)) {
					$file_name = 'themes/default.php';
				}
				return self::template_path($file_name);
			}
			public static function template_path($file_name): string {
				$file_path = wp_normalize_path(WP_CONTENT_DIR . DIRECTORY_SEPARATOR . '/abprf_templates/' . $file_name);
				$default_dir = wp_normalize_path(ABPRF_DIR . '/abprf_templates/' . $file_name);
				return file_exists($file_path) ? $file_path : $default_dir;
			}
			//=============================//
			public static function get_cpt(): string { return 'abprf_post'; }
			//============== Equipment Function===============//
			public static function route_for_price($routing_infos, $price_infos, $ticket_types): array {
				$full_price = [];
				if (sizeof($routing_infos) > 0) {
					foreach ($routing_infos as $key => $routing_info) {
						if ($routing_info['type'] == 'bp' || $routing_info['type'] == 'both') {
							$bp = $routing_info['stop'];
							$next_infos = array_slice($routing_infos, $key + 1);
							if (sizeof($next_infos) > 0) {
								foreach ($next_infos as $next_info) {
									if ($next_info['type'] == 'dp' || $next_info['type'] == 'both') {
										$dp = $next_info['stop'];
										$path_price = [];
										$path_price['bp'] = $bp;
										$path_price['dp'] = $dp;
										if (sizeof($price_infos) > 0) {
											foreach ($price_infos as $price_info) {
												if (strtolower($price_info['bp']) == strtolower($bp) && strtolower($price_info['dp']) == strtolower($dp)) {
													if (sizeof($ticket_types) > 0) {
														foreach ($ticket_types as $key => $ticket_type) {
															$price = array_key_exists($key, $price_info) && $price_info[$key] ? (float)$price_info[$key] : '';
															if ($key == 'adult' && !$price) {
																$price = array_key_exists('price', $price_info) && $price_info['price'] ? (float)$price_info['price'] : '';
															}
															$path_price[$key] = $price;
														}
													} else {
														$path_price['price'] = array_key_exists('price', $price_info) && $price_info['price'] ? (float)$price_info['price'] : '';
													}
												}
											}
										}
										$full_price[] = $path_price;
									}
								}
							}
						}
					}
				}
				return $full_price;
			}
			public static function get_routes($post_id = 0, $bp = true, $bp_point = ''): array {
				$route_lists = [];
				if ($post_id > 0) {
					$route_lists = self::get_route($post_id, $bp, $bp_point);
				} else {
					$equipment_ids = ABPRF_Query::get_equipment_id($bp_point);
					if (sizeof($equipment_ids) > 0) {
						foreach ($equipment_ids as $equipment_id) {
							$routes = self::get_route($equipment_id, $bp, $bp_point);
							$route_lists = array_merge($route_lists, $routes);
						}
					}
				}
				return array_unique($route_lists);
			}
			public static function get_route($post_id = 0, $bp = true, $bp_point = '') {
				$route_lists = [];
				if ($post_id > 0) {
					if ($bp) {
						$route_lists = ABPRF_LIB_Function::get_post_info($post_id, 'abptm_bp', []);
					} else {
						if ($bp_point) {
							$routes = ABPRF_LIB_Function::get_post_info($post_id, 'routing_infos', []);
							if (sizeof($routes) > 0) {
								$exit_bp = 0;
								foreach ($routes as $route) {
									if ($exit_bp > 0) {
										if ($route['type'] == 'dp' || $route['type'] == 'both') {
											$route_lists[] = $route['stop'];
										}
									} else {
										if ($route['stop'] == $bp_point && ($route['type'] == 'bp' || $route['type'] == 'both')) {
											$exit_bp++;
										}
									}
								}
							}
						} else {
							$route_lists = ABPRF_LIB_Function::get_post_info($post_id, 'abptm_dp', []);
						}
					}
				}
				return $route_lists;
			}
			//=============Date Section================//
			public static function get_dates($post_id = 0, $bp = '', $dp = ''): array {
				$all_dates = [];
				if ($post_id > 0) {
					$all_dates = self::get_bus_dates($post_id, $bp);
				} else {
					if ($bp) {
						$bus_ids = ABPRF_Query::get_equipment_id($bp, $dp);
						if (sizeof($bus_ids) > 0) {
							foreach ($bus_ids as $bus_id) {
								$dates = self::get_bus_dates($bus_id, $bp);
								$all_dates = array_merge($all_dates, $dates);
							}
						}
					}
				}
				$all_dates = array_unique($all_dates);
				usort($all_dates, "ABPRF_LIB_Function::sort_date" );
				return $all_dates;
			}
			public static function get_bus_dates($post_id, $bp = ''): array {
				$all_dates = [];
				if ($post_id > 0) {
					$dates = self::get_post_dates($post_id);
					if (sizeof($dates) > 0) {
						$routes = ABPRF_LIB_Function::get_post_info($post_id, 'routing_infos', []);
						foreach ($dates as $date) {
							$route_infos = self::get_route_info($post_id, $date, $routes);
							if (sizeof($route_infos) > 0) {
								foreach ($route_infos as $route_info) {
									if (sizeof($route_info) > 0) {
										foreach ($route_info as $info) {
											if (array_key_exists('type', $info) && ($info['type'] == 'bp' || $info['type'] == 'both')) {
												if ($bp) {
													if ($bp == $info['stop']) {
														$all_dates[] = gmdate('Y-m-d', strtotime($info['time']));
													}
												} else {
													$all_dates[] = gmdate('Y-m-d', strtotime($info['time']));
												}
											}
										}
									}
								}
							}
						}
					}
				}
				return array_unique($all_dates);
			}
			public static function get_post_dates($post_id): array {
				$all_dates = [];
				if ($post_id > 0) {
					$date_type = ABPRF_LIB_Function::get_post_info($post_id, 'date_type', 'periodic_date');
					$now = current_time('Y-m-d');
					if ($date_type == 'specific_date') {
						$specific_dates = ABPRF_LIB_Function::get_post_info($post_id, 'specific_dates', array());
						if (sizeof($specific_dates)) {
							foreach ($specific_dates as $specific_date) {
								$date_item = gmdate('Y-m-d', strtotime($specific_date));
								if (strtotime($date_item) >= strtotime($now)) {
									$all_dates[] = $date_item;
								}
							}
						}
					} else {
						$start_date = ABPRF_LIB_Function::get_post_info($post_id, 'periodic_start_date') ?: ABPRF_LIB_Function::get_options('abprf_configuration', 'periodic_start_date', $now);
						$sale_end_date = ABPRF_LIB_Function::get_options('abprf_configuration', 'periodic_end_date') ?: ABPRF_LIB_Function::get_post_info($post_id, 'periodic_end_date');
						$sale_end_date = $sale_end_date ? gmdate('Y-m-d', strtotime($sale_end_date)) : '';
						$active_days = ABPRF_LIB_Function::get_post_info($post_id, 'advance_date_number') ?: ABPRF_LIB_Function::get_options('abprf_configuration', 'advance_date_number', 28);
						if (strtotime($now) >= strtotime($start_date)) {
							$start_date = $now;
						}
						$end_date = gmdate('Y-m-d', strtotime($start_date . ' +' . $active_days . ' day'));
						if ($sale_end_date && strtotime($sale_end_date) < strtotime($end_date)) {
							$end_date = $sale_end_date;
						}
						if (strtotime($start_date) < strtotime($end_date)) {
							$off_dates = [];
							$all_off_dates = ABPRF_LIB_Function::get_post_info($post_id, 'off_date_range', array());
							if (sizeof($all_off_dates) > 0) {
								foreach ($all_off_dates as $off_date) {
									if ($off_date['from'] && $off_date['to']) {
										$from_date = gmdate('Y-m-d', strtotime($off_date['from']));
										$to_date = gmdate('Y-m-d', strtotime($off_date['to']));
										$off_date_lists = ABPRF_LIB_Function::date_separate_period($from_date, $to_date);
										foreach ($off_date_lists as $off_date_list) {
											$off_dates[] = $off_date_list->format('Y-m-d');
										}
									}
								}
							}
							$particular_off_dates = ABPRF_LIB_Function::get_post_info($post_id, 'specific_off_dates', array());
							if (sizeof($particular_off_dates) > 0) {
								foreach ($particular_off_dates as $particular_off_date) {
									$particular_off_date = gmdate('Y-m-d', strtotime($particular_off_date));
									$off_dates[] = $particular_off_date;
								}
							}
							$off_dates = array_unique($off_dates);
							$off_days = ABPRF_LIB_Function::get_post_info($post_id, 'weekend');
							$off_day_array = $off_days ? explode(',', $off_days) : [];
							$repeat = ABPRF_LIB_Function::get_post_info($post_id, 'periodic_after', 1);
							$dates = ABPRF_LIB_Function::date_separate_period($start_date, $end_date, $repeat);
							foreach ($dates as $date) {
								$date = $date->format('Y-m-d');
								if (strtotime($date) >= strtotime($now)) {
									$day = strtolower(gmdate('l', strtotime($date)));
									if (!in_array($date, $off_dates) && !in_array($day, $off_day_array)) {
										$all_dates[] = $date;
									}
								}
							}
						}
					}
				}
				return array_unique($all_dates);
			}
			public static function get_route_info($post_id, $date, $route_infos = []): array {
				$all_infos = [];
				$now = current_time('Y-m-d H:i');
				$route_infos = sizeof($route_infos) > 0 ? $route_infos : ABPRF_LIB_Function::get_post_info($post_id, 'routing_infos', []);
				if ($date && sizeof($route_infos) > 0) {
					$prev_date = $date;
					$prev_full_date = $date;
					$count = 0;
					foreach ($route_infos as $info) {
						if (array_key_exists('time', $info) && $info['time']) {
							$current_date = gmdate('Y-m-d H:i', strtotime($prev_date . ' ' . $info['time']));
							if ($count > 0) {
								if (strtotime($prev_full_date) > strtotime($current_date)) {
									$current_date = gmdate('Y-m-d H:i', strtotime($current_date . ' +1 day'));
								}
							}
							if (strtotime($now) < strtotime($current_date)) {
								$info['time'] = $current_date;
								$all_infos[$date][] = $info;
								$prev_full_date = $current_date;
								$prev_date = gmdate('Y-m-d', strtotime($current_date));
								$count++;
							}
						}
					}
				} else {
					$all_infos = $route_infos;
				}
				return $all_infos;
			}
			public static function get_route_full_info($post_id, $bp, $bp_date) {
				$bp_date = strtotime($bp_date);
				if ($post_id > 0) {
					$now = current_time('Y-m-d H:i');
					$dates = self::get_post_dates($post_id);
					if (sizeof($dates) > 0) {
						$routes = ABPRF_LIB_Function::get_post_info($post_id, 'routing_infos', []);
						foreach ($dates as $date) {
							$route_infos = self::get_route_info($post_id, $date, $routes);
							if (sizeof($route_infos) > 0) {
								foreach ($route_infos as $route_info) {
									if (sizeof($route_info) > 0) {
										foreach ($route_info as $info) {
											$current_date = strtotime(gmdate('Y-m-d', strtotime($info['time'])));
											if (array_key_exists('stop', $info) && strtolower($info['stop']) == strtolower($bp) && $bp_date == $current_date) {
												$slice_time = self::slice_buffer_time($info['time']);
												if (strtotime($now) < strtotime($slice_time)) {
													return $route_info;
												}
											}
										}
									}
								}
							}
						}
					}
				}
				return [];
			}
			public static function slice_buffer_time($date) {
				$buffer_time = ABPRF_LIB_Function::get_options('abprf_configuration', 'ticket_sale_close_before', 0) * 60;
				if ($buffer_time > 0) {
					$date = gmdate('Y-m-d H:i', strtotime($date) - $buffer_time);
				}
				return $date;
			}
			//=============================//
			public static function get_ticket_type_key($abprf_infos): array {
				$post_id = array_key_exists('post_id', $abprf_infos) ? $abprf_infos['post_id'] : 0;
				$seat_type = array_key_exists('seat_type', $abprf_infos) ? $abprf_infos['seat_type'] : 'seat_plan';
				$ticket_types = [];
				if ($seat_type == 'seat_plan') {
					$ticket_type = array_key_exists('ticket_type', $abprf_infos) ? $abprf_infos['ticket_type'] : '';
					$ticket_type = $ticket_type ? explode(',', $ticket_type) : [];
					foreach ($ticket_type as $key) {
						$ticket_types[$key] = ABPRF_Function::get_ticket_type($key);
					}
				} else {
					$ticket_infos = array_key_exists('equipment_infos', $abprf_infos) ? $abprf_infos['equipment_infos'] : ABPRF_LIB_Function::get_post_info($post_id, 'equipment_infos', []);
					if (sizeof($ticket_infos) > 0) {
						foreach ($ticket_infos as $key => $ticket_info) {
							$ticket_types[$key] = is_array($ticket_info) && array_key_exists('name', $ticket_info) ? $ticket_info['name'] : '';
						}
					}
				}
				return $ticket_types;
			}
			//=============================//
			public static function already_in_cart($post_id, $bp, $dp, $bp_date, $seat_name) {
				$count = 0;
				if (is_admin() && str_contains(wp_get_referer(), 'add_order')) {
					return $count;
				}
				global $woocommerce;
				$cart_items = $woocommerce->cart->get_cart();
				if (is_array($cart_items) && sizeof($cart_items) > 0) {
					foreach ($cart_items as $cart_item) {
						$cart_post_id = array_key_exists('post_id', $cart_item) ? $cart_item['post_id'] : '';
						$cart_date = array_key_exists('bp_time', $cart_item) ? $cart_item['bp_time'] : '';
						$cart_date = $cart_date ? gmdate('Y-m-d', strtotime($cart_date)) : '';
						$bp_date = $bp_date ? gmdate('Y-m-d', strtotime($bp_date)) : '';
						if ($cart_post_id == $post_id && strtotime($cart_date) == strtotime($bp_date)) {
							$routes = ABPRF_LIB_Function::get_post_info($post_id, 'route_direction', []);
							if (sizeof($routes) > 0) {
								$cart_bp = array_key_exists('bp', $cart_item) ? $cart_item['bp'] : '';
								$cart_dp = array_key_exists('dp', $cart_item) ? $cart_item['dp'] : '';
								$sp = array_search($bp, $routes);
								$ep = array_search($dp, $routes);
								if (in_array($cart_bp, array_slice($routes, 0, $ep)) && in_array($cart_dp, array_slice($routes, $sp + 1))) {
									$seat_infos = array_key_exists('ticket_info', $cart_item) ? $cart_item['ticket_info'] : '';
									if (sizeof($seat_infos) > 0) {
										foreach ($seat_infos as $seat_info) {
											if (array_key_exists('seat', $seat_info) && strtolower($seat_info['seat']) == strtolower($seat_name)) {
												$count += array_key_exists('qty', $cart_item) ? $cart_item['qty'] : 1;
											}
										}
									}
								}
							}
						}
					}
				}
				return $count;
			}
			//==============Price Section===============//
			public static function get_price($post_id, $bp, $dp, $type = 'price', $ud = false, $date = '') {
				$price = 0;
				$price_infos = ABPRF_LIB_Function::get_post_info($post_id, 'price_infos', []);
				if (sizeof($price_infos) > 0) {
					foreach ($price_infos as $price_info) {
						if ($price_info['bp'] == $bp && $price_info['dp'] == $dp) {
							$price = $price_info[$type];
							if ($ud && $price) {
								$ud_increase = (int)ABPRF_LIB_Function::get_post_info($post_id, 'abptm_ud_price_increase', 0);
								$price = $price + ($price * $ud_increase / 100);
							}
						}
					}
				}
				return ABPRF_LIB_Function::get_wc_raw_price($post_id, $price);
			}
			public static function get_additional_price($post_id, $service_name) {
				$services = ABPRF_LIB_Function::get_post_info($post_id, 'additional_services');
				$display = ABPRF_LIB_Function::get_post_info($post_id, 'display_additional_services', 'on');
				$price = 0;
				if ($display == 'on' && sizeof($services) > 0) {
					foreach ($services as $service) {
						$ex_name = array_key_exists('name', $service) ? $service['name'] : '';
						if ($ex_name == $service_name) {
							$price = array_key_exists('price', $service) ? $service['price'] : 0;
						}
					}
				}
				return ABPRF_LIB_Function::get_wc_raw_price($post_id, $price);
			}
			//=============================//
			public static function get_transport_list_details($bp, $dp, $bp_date): array {
				$list_infos = [];
				$equipment_ids = ABPRF_Query::get_equipment_id($bp, $dp);
				if (sizeof($equipment_ids) > 0) {
					foreach ($equipment_ids as $equipment_id) {
						$full_infos = ABPRF_Function::get_route_full_info($equipment_id, $bp, $bp_date);
						if (sizeof($full_infos) > 0) {
							foreach ($full_infos as $full_info) {
								if ($full_info['stop'] == $bp) {
									$list_infos[$equipment_id]['id'] = $equipment_id;
									$list_infos[$equipment_id]['time'] = $full_info['time'];
								}
								if ($full_info['stop'] == $dp) {
									$list_infos[$equipment_id]['dp_time'] = $full_info['time'];
								}
							}
						}
					}
					usort($list_infos, "ABPRF_LIB_Function::sort_date_array" );
				}
				return $list_infos;
			}
			//=============================//
			public static function get_seat_type($type): string {
				if ($type == 'adult' || $type == 'child' || $type == 'infant') {
					$ticket_names_array = ABPRF_Function::get_ticket_type();
					return '( ' . $ticket_names_array[$type] . ' )';
				}
				return '';
			}
			//=============================//
			public static function get_form_data($abprf_infos = []): array {
				$post_id_form = $post_id_url = 0;
				$transport_bp_form = $transport_bp_url = $transport_dp_form = $transport_dp_url = $bp_date_form = $bp_date_url = $return_date_form = $return_date_url = '';
				$single_post_form = $single_post_url = false;
				if (isset($_POST['abprf_search_form_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['abprf_search_form_nonce'])), 'abprf_search_form_nonce')) {
					$post_id_form = isset($_POST['_post_id']) ? sanitize_text_field(wp_unslash($_POST['_post_id'])) : $post_id_form;
					$transport_bp_form = isset($_POST['_bp']) ? sanitize_text_field(wp_unslash($_POST['_bp'])) : '';
					$transport_dp_form = isset($_POST['_dp']) ? sanitize_text_field(wp_unslash($_POST['_dp'])) : '';
					$bp_date_form = isset($_POST['_j_date']) ? sanitize_text_field(wp_unslash($_POST['_j_date'])) : '';
					$return_date_form = isset($_POST['_r_date']) ? sanitize_text_field(wp_unslash($_POST['_r_date'])) : '';
					$single_post_form = isset($_POST['single_post']) && sanitize_text_field(wp_unslash($_POST['single_post']));
				}
				if (isset($_GET['abprf_search_form_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['abprf_search_form_nonce'])), 'abprf_search_form_nonce')) {
					$post_id_url = isset($_GET['_post_id']) ? sanitize_text_field(wp_unslash($_GET['_post_id'])) : $post_id_url;
					$transport_bp_url = isset($_GET['_bp']) ? sanitize_text_field(wp_unslash($_GET['_bp'])) : '';
					$transport_dp_url = isset($_GET['_dp']) ? sanitize_text_field(wp_unslash($_GET['_dp'])) : '';
					$bp_date_url = isset($_GET['_j_date']) ? sanitize_text_field(wp_unslash($_GET['_j_date'])) : '';
					$return_date_url = isset($_GET['_r_date']) ? sanitize_text_field(wp_unslash($_GET['_r_date'])) : '';
					$single_post_url = isset($_GET['single_post']) && sanitize_text_field(wp_unslash($_GET['single_post']));
				}
				$post_id = array_key_exists('_post_id', $abprf_infos) ? $abprf_infos['_post_id'] : 0;
				$bp_date = $bp_date_form ?: $bp_date_url;
				$bp_date = $bp_date ? gmdate('Y-m-d', strtotime($bp_date)) : '';
				$return_date = $return_date_form ?: $return_date_url;
				$return_date = $return_date ? gmdate('Y-m-d', strtotime($return_date)) : '';
				$single_post = array_key_exists('single_post', $abprf_infos) && $abprf_infos['single_post'];
				$single_post = $single_post || $single_post_form || $single_post_url;
				//============================//
				$form_data['_post_id'] = max($post_id, $post_id_form, $post_id_url);
				$form_data['_bp'] = $transport_bp_form ?: $transport_bp_url;
				$form_data['_dp'] = $transport_dp_form ?: $transport_dp_url;
				$form_data['_j_date'] = $bp_date;
				$form_data['_r_date'] = $return_date;
				$form_data['single_post'] = $single_post;
				return $form_data;
			}
			//=============================//
			public static function get_available_text($seat_type, $available_seat): string {
				if ($seat_type == 'seat_plan') {
					if ($available_seat > 1) {
						return __('Seats Available !', 'abprf-rental-forge');
					} else {
						return __('Seat Available !', 'abprf-rental-forge');
					}
				} else {
					if ($available_seat > 1) {
						return __('Tickets Available !', 'abprf-rental-forge');
					} else {
						return __('Ticket Available !', 'abprf-rental-forge');
					}
				}
			}
			public static function get_view_text($seat_type): string {
				if ($seat_type == 'seat_plan') {
					return __('View Seats', 'abprf-rental-forge');
				} else {
					return __('View Tickets', 'abprf-rental-forge');
				}
			}
			public static function status_text($status) {
				$status_array = wc_get_order_statuses();
				return array_key_exists($status, $status_array) ? $status_array[$status] : '';
			}
			public static function get_ticket_type($type = '') {
				$types = [
					'adult' => __('Adult', 'abprf-rental-forge'),
					'child' => __('Child', 'abprf-rental-forge'),
					'infant' => __('Infant', 'abprf-rental-forge'),
				];
				return $type ? (array_key_exists($type, $types) ? $types[$type] : '') : $types;
			}
			//=============================//
		}
		new ABPRF_Function();
	}