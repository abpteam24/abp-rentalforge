<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	if ( ! class_exists( 'ABPRF_Function' ) ) {
		class ABPRF_Function {
			public function __construct() { }

			public static function get_post_info( $post_id, $key, $default = '' ) {
				$data = get_post_meta( $post_id, $key, true ) ?: $default;

				return self::data_sanitize( $data );
			}

			public static function get_all_meta( $post_id = 0 ): array {
				$all_data = [];
				if ( $post_id > 0 ) {
					$all_data['post_title'] = get_the_title( $post_id );
					$all_data['post_id']    = $post_id;
					$metas                  = get_post_meta( $post_id );
					if ( !empty($metas) && sizeof( $metas ) > 0 ) {
						foreach ( $metas as $key => $meta ) {
							$all_data[ $key ] = self::data_sanitize( $meta[0] );
						}
					}
				}

				return $all_data;
			}

			public static function get_taxonomy( $name ): array|WP_Error|string {
				return get_terms( array( 'taxonomy' => $name, 'hide_empty' => false ) );
			}

			public static function get_all_term_data( $term_name ): array {
				$all_data   = [];
				$taxonomies = self::get_taxonomy( $term_name );
				if ( ! empty( $taxonomies ) && is_array( $taxonomies ) && sizeof( $taxonomies ) > 0 ) {
					foreach ( $taxonomies as $taxonomy ) {
						$all_data[ $taxonomy->term_id ] = $taxonomy->name;
					}
				}

				return $all_data;
			}

			public static function data_sanitize( $data ) {
				$data = maybe_unserialize( $data );
				if ( is_string( $data ) ) {
					$data = maybe_unserialize( $data );
					if ( is_array( $data ) ) {
						$data = self::data_sanitize( $data );
					} else {
						$data = sanitize_text_field( stripslashes( wp_strip_all_tags( $data ) ) );
					}
				} elseif ( is_array( $data ) ) {
					foreach ( $data as &$value ) {
						if ( is_array( $value ) ) {
							$value = self::data_sanitize( $value );
						} else {
							$value = sanitize_text_field( stripslashes( wp_strip_all_tags( $value ) ) );
						}
					}
				}

				return $data;
			}

			public static function get_option( $option, $default = [] ) {
				$option_data = get_option( sanitize_key( $option ) );

				return $option_data ?: $default;
			}

			public static function get_options( $option, $key, $default = '' ) {
				$options = get_option( sanitize_key( $option ) );
				if ( isset( $options[ $key ] ) && $options[ $key ] ) {
					$default = $options[ $key ];
				}

				return $default;
			}

			public static function array_to_string( $array ) {
				$ids = '';
				if ( sizeof( $array ) > 0 ) {
					foreach ( $array as $data ) {
						if ( $data ) {
							$ids = $ids ? $ids . ',' . $data : $data;
						}
					}
				}

				return $ids;
			}

			public static function serialize_array_convert( $form_data ): array {
				$infos = [];
				if ( sizeof( $form_data ) > 0 ) {
					foreach ( $form_data as $data ) {
						$_name = is_array( $data ) && array_key_exists( 'name', $data ) ? sanitize_text_field( $data['name'] ) : '';
						$name  = explode( '[]', $_name )[0];
						$value = is_array( $data ) && array_key_exists( 'value', $data ) ? sanitize_text_field( $data['value'] ) : '';
						if ( $name ) {
							if ( $_name !== $name ) {
								$infos[ $name ][] = $value;
							} else {
								$infos[ $name ] = $value;
							}
						}
					}
				}

				return $infos;
			}
			public static function get_cpt(): string { return 'abprf_post'; }
			public static function booking_status() {
				return is_array(ABPRF_Configuration) && array_key_exists( 'booked_status', ABPRF_Configuration ) && !empty(ABPRF_Configuration['booked_status'])?ABPRF_Configuration['booked_status']:'wc-processing,wc-completed';
			}
			public static function brand_icon() {
				return is_array(ABPRF_Configuration) && array_key_exists( 'brand_icon', ABPRF_Configuration ) && !empty(ABPRF_Configuration['brand_icon'])?ABPRF_Configuration['brand_icon']:'fas fa-hammer';
			}
			public static function category_label() {
				return is_array(ABPRF_Configuration) && array_key_exists( 'category_label', ABPRF_Configuration ) && !empty(ABPRF_Configuration['category_label'])?ABPRF_Configuration['category_label']:__( 'Category', 'abprf-rental-forge' );
			}
			public static function get_date_format() {
				return is_array(ABPRF_Configuration) && array_key_exists( 'date_format', ABPRF_Configuration ) && !empty(ABPRF_Configuration['date_format'])?ABPRF_Configuration['date_format']:'D d M , yy';
			}

			public static function get_image_url( $post_id = '', $image_id = '', $size = 'full' ): bool|string {
				$image_id = $post_id && $post_id > 0 ? get_post_thumbnail_id( $post_id ) : $image_id;

				return wp_get_attachment_image_url( $image_id, $size );
			}

			public static function get_page_by_slug( $slug ): bool|WP_Post {
				if ( $pages = get_pages() ) {
					foreach ( $pages as $page ) {
						if ( $slug === $page->post_name ) {
							return $page;
						}
					}
				}

				return false;
			}

			public static function get_id_by_slug( $page_slug ): ?int {
				$page = get_page_by_path( $page_slug );

				return $page?->ID;
			}

			public static function check_wc(): int {
				include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
				$plugin_dir = ABSPATH . 'wp-content/plugins/woocommerce';
				if ( in_array( 'woocommerce/woocommerce.php', get_option( 'active_plugins' ) ) ) {
					return 2;
				} elseif ( is_dir( $plugin_dir ) ) {
					return 1;
				} else {
					return 0;
				}
			}

			public static function already_in_cart( $post_id, $bp, $dp, $bp_date, $seat_name ) {
				$count = 0;
				if ( is_admin() && str_contains( wp_get_referer(), 'add_order' ) ) {
					return $count;
				}
				global $woocommerce;
				$cart_items = $woocommerce->cart->get_cart();
				if ( is_array( $cart_items ) && sizeof( $cart_items ) > 0 ) {
					foreach ( $cart_items as $cart_item ) {
						$cart_post_id = array_key_exists( 'post_id', $cart_item ) ? $cart_item['post_id'] : '';
						$cart_date    = array_key_exists( 'bp_time', $cart_item ) ? $cart_item['bp_time'] : '';
						$cart_date    = $cart_date ? gmdate( 'Y-m-d', strtotime( $cart_date ) ) : '';
						$bp_date      = $bp_date ? gmdate( 'Y-m-d', strtotime( $bp_date ) ) : '';
						if ( $cart_post_id == $post_id && strtotime( $cart_date ) == strtotime( $bp_date ) ) {
							$routes = self::get_post_info( $post_id, 'route_direction', [] );
							if ( sizeof( $routes ) > 0 ) {
								$cart_bp = array_key_exists( 'bp', $cart_item ) ? $cart_item['bp'] : '';
								$cart_dp = array_key_exists( 'dp', $cart_item ) ? $cart_item['dp'] : '';
								$sp      = array_search( $bp, $routes );
								$ep      = array_search( $dp, $routes );
								if ( in_array( $cart_bp, array_slice( $routes, 0, $ep ) ) && in_array( $cart_dp, array_slice( $routes, $sp + 1 ) ) ) {
									$seat_infos = array_key_exists( 'ticket_info', $cart_item ) ? $cart_item['ticket_info'] : '';
									if ( sizeof( $seat_infos ) > 0 ) {
										foreach ( $seat_infos as $seat_info ) {
											if ( array_key_exists( 'seat', $seat_info ) && strtolower( $seat_info['seat'] ) == strtolower( $seat_name ) ) {
												$count += array_key_exists( 'qty', $cart_item ) ? $cart_item['qty'] : 1;
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

			public static function get_user_role( $user_ID ): string {
				global $wp_roles;
				$user_role_list = '';
				$user_data      = get_userdata( $user_ID );
				$user_role_slug = $user_data->roles;
				if ( is_array( $user_role_slug ) && sizeof( $user_role_slug ) > 0 ) {
					$user_count = 0;
					foreach ( $user_role_slug as $user_role ) {
						$user_count ++;
						if ( $user_count > 1 ) {
							$user_role_list .= ", ";
						}
						$user_role_list .= translate_user_role( $wp_roles->roles[ $user_role ]['name'] );
					}
				}

				return $user_role_list;
			}

			//=========== Template Related==================//
			public static function details_template_path( $post_id ): string {
				$post_id       = $post_id ?? get_the_id();
				$template_name = self::get_post_info( $post_id, 'abprf_template', 'grid' );
				$file_name     = 'details_theme/' . $template_name . '.php';
				$dir           = ABPRF_DIR . '/rf_templates/' . $file_name;
				if ( ! file_exists( $dir ) ) {
					$file_name = 'details_theme/grid.php';
				}

				return self::template_path( $file_name );
			}

			public static function template_path( $file_name ): string {
				$file_path   = wp_normalize_path( WP_CONTENT_DIR . DIRECTORY_SEPARATOR . '/rf_templates/' . $file_name );
				$default_dir = wp_normalize_path( ABPRF_DIR . '/rf_templates/' . $file_name );

				return file_exists( $file_path ) ? $file_path : $default_dir;
			}

			//============= Date function================//
			public static function check_date_exit( $abprf_infos ): bool {
				$post_id         = array_key_exists( 'post_id', $abprf_infos ) ? $abprf_infos['post_id'] : 0;
				$start_date_time = array_key_exists( 'start_time', $abprf_infos ) ? $abprf_infos['start_time'] : '';
				$end_date_time   = array_key_exists( 'end_time', $abprf_infos ) ? $abprf_infos['end_time'] : '';
				$rent_rule       = array_key_exists( 'rent_rule', $abprf_infos ) ? $abprf_infos['rent_rule'] : self::get_post_info( $post_id, 'rent_rule' );
				if ( ! empty( $post_id ) && $post_id > 0 && ! empty( $start_date_time ) && ! empty( $end_date_time ) && ! empty( $rent_rule ) ) {
					$all_dates = ABPRF_Function::get_dates( $post_id );
					$start     = gmdate( 'Y-m-d', strtotime( $start_date_time ) );
					$end       = gmdate( 'Y-m-d', strtotime( $end_date_time ) );
					if ( in_array( $start, $all_dates ) && in_array( $end, $all_dates ) ) {
						if ( $rent_rule == 'hourly' || $rent_rule == 'multi_day' ) {
							$start_time = gmdate( 'H:i', strtotime( $start_date_time ) );
							$end_time   = gmdate( 'H:i', strtotime( $end_date_time ) );
							$time_list  = self::get_time( $post_id );
							if ( ! empty( $time_list ) ) {
								$day_name = strtolower( gmdate( 'l', strtotime( $start ) ) );
								if ( array_key_exists( $start, $time_list ) ) {
									$time_slots = $time_list[ $start ];
								} elseif ( array_key_exists( $day_name, $time_list ) ) {
									$time_slots = $time_list[ $day_name ];
								} else {
									$time_slots = array_key_exists( 'slot', $time_list ) ? $time_list['slot'] : '';
								}
								if ( ! empty( $time_slots ) && ABPRF_Function::check_time_slot_exit( $time_slots, $start_time . '-' . $end_time ) ) {
									return true;
								}
							}
						} else {
							return true;
						}
					}
				}

				return false;
			}

			public static function get_dates( $post_id = '', $filters = [] ): array {
				$all_dates = [];
				if ( ! empty( $post_id ) ) {
					$active_global_dates = self::get_post_info( $post_id, 'active_global_dates', 'on' );
					if ( $active_global_dates == 'on' ) {
						$post_id   = 'global';
						$all_dates = json_decode( get_transient( 'abprf_date_global' ), true );
					} else {
						$all_dates = json_decode( get_transient( 'abprf_date_' . $post_id ), true );
					}
					if ( empty( $all_dates ) ) {
						self::update_dates( $post_id );
						$all_dates = json_decode( get_transient( 'abprf_date_' . $post_id ), true );
					}
				} else {
					$post_ids = ABPRF_Query::get_post_id( $filters );
					if ( ! empty( $post_ids ) ) {
						$global = 0;
						foreach ( $post_ids as $post_id ) {
							$active_global_dates = self::get_post_info( $post_id, 'active_global_dates', 'on' );
							if ( $active_global_dates == 'on' ) {
								if ( $global == 0 ) {
									$dates = json_decode( get_transient( 'abprf_date_global' ), true );
									$global ++;
									if ( empty( $dates ) ) {
										self::update_dates( 'global' );
										$dates     = json_decode( get_transient( 'abprf_date_global' ), true );
										$all_dates = array_merge( $all_dates, $dates );
									}
								}
							} else {
								$dates = json_decode( get_transient( 'abprf_date_' . $post_id ), true );
								if ( empty( $dates ) ) {
									self::update_dates( $post_id );
									$dates = json_decode( get_transient( 'abprf_date_' . $post_id ), true );
								}
								$all_dates = array_merge( $all_dates, $dates );
							}
						}
						if ( ! empty( $all_dates ) ) {
							$all_dates = array_unique( $all_dates );
							usort( $all_dates, "ABPRF_Function::sort_date" );
						}
					}
				}

				return $all_dates;
			}

			public static function get_time( $post_id = '', $type = 'time' ): array {
				$option_name = $type == 'js' ? 'abprf_time_info_js' : 'abprf_time_info';
				$time_info   = ABPRF_Function::get_option( $option_name );
				if ( ! empty( $post_id ) ) {
					$info = array_key_exists( $post_id, $time_info ) ? $time_info[ $post_id ] : ( array_key_exists( 'global', $time_info ) ? $time_info['global'] : [] );
				} else {
					$info = [];
				}

				return $info;
			}

			public static function update_dates( $post_id = '' ): void {
				if ( ! empty( $post_id ) ) {
					if ( $post_id == 'global' ) {
						$date_infos = self::get_option( 'abprf_dates' );
					} else {
						$active_global_dates = self::get_post_info( $post_id, 'active_global_dates', 'on' );
						if ( $active_global_dates == 'on' ) {
							$date_infos = self::get_option( 'abprf_dates' );
							$post_id    = 'global';
						} else {
							$date_infos = self::get_post_info( $post_id, 'abprf_dates', [] );
						}
					}
					$all_dates = self::get_date_info( $date_infos );
					$all_dates = array_unique( $all_dates );
					usort( $all_dates, "ABPRF_Function::sort_date" );
					set_transient( 'abprf_date_' . $post_id, json_encode( $all_dates ), HOUR_IN_SECONDS );
				}
			}

			public static function update_time_slot( $post_id = '' ): void {
				$all_slots    = ABPRF_Function::get_option( 'abprf_time_info' );
				$all_js_slots = ABPRF_Function::get_option( 'abprf_time_info_js' );
				$date_infos   = [];
				$key          = 'global';
				if ( ! empty( $post_id ) ) {
					$active_global_dates = self::get_post_info( $post_id, 'active_global_dates', 'on' );
					if ( $active_global_dates !== 'on' ) {
						$date_infos = self::get_post_info( $post_id, 'abprf_dates', [] );
						$key        = $post_id;
					}
				} else {
					$date_infos = ABPRF_Dates;
				}
				if ( ! empty( $date_infos ) ) {
					$slots             = self::get_time_slot( $date_infos );
					$all_slots[ $key ] = $slots;
					$js_slots          = [];
					if ( ! empty( $slots ) ) {
						foreach ( $slots as $count => $slot ) {
							if ( ! empty( $slot ) ) {
								$slot_info  = explode( '-', $slot );
								$start_time = $slot_info[0] ?? '';
								$end_time   = $slot_info[1] ?? '';
								if ( ! empty( $start_time ) && ! empty( $end_time ) ) {
									$slot_data = self::generate_time_slot( $start_time, $end_time );
									if ( ! empty( $slot_data ) ) {
										$js_slots[ $count ] = $slot_data;
									}
								}
							}
						}
					}
					$all_js_slots[ $key ] = $js_slots;
					update_option( 'abprf_time_info', $all_slots );
					update_option( 'abprf_time_info_js', $all_js_slots );
				}
			}

			public static function get_date_info( $date_infos ): array {
				$all_dates = [];
				$date_type = array_key_exists( 'date_type', $date_infos ) ? $date_infos['date_type'] : 'periodic_date';
				$now       = current_time( 'Y-m-d' );
				if ( $date_type == 'specific_date' ) {
					$specific_dates = array_key_exists( 'specific_dates', $date_infos ) ? $date_infos['specific_dates'] : [];
					if ( is_array( $specific_dates ) && sizeof( $specific_dates ) > 0 ) {
						foreach ( $specific_dates as $specific_date ) {
							$date_item = is_array( $specific_date ) && array_key_exists( 'date', $specific_date ) ? $specific_date['date'] : '';
							if ( ! empty( $date_item ) ) {
								$date_item = gmdate( 'Y-m-d', strtotime( $date_item ) );
								if ( strtotime( $date_item ) >= strtotime( $now ) ) {
									$all_dates[] = $date_item;
								}
							}
						}
					}
				} else {
					$start_date    = array_key_exists( 'periodic_start_date', $date_infos ) ? $date_infos['periodic_start_date'] : '';
					$start_date    = $start_date ?: $now;
					$sale_end_date = array_key_exists( 'periodic_end_date', $date_infos ) ? $date_infos['periodic_end_date'] : '';
					$sale_end_date = $sale_end_date ? gmdate( 'Y-m-d', strtotime( $sale_end_date ) ) : '';
					$active_days   = array_key_exists( 'advance_date_number', $date_infos ) ? $date_infos['advance_date_number'] : 28;
					if ( strtotime( $now ) >= strtotime( $start_date ) ) {
						$start_date = $now;
					}
					$end_date = gmdate( 'Y-m-d', strtotime( $start_date . ' +' . $active_days . ' day' ) );
					if ( $sale_end_date && strtotime( $sale_end_date ) < strtotime( $end_date ) ) {
						$end_date = $sale_end_date;
					}
					if ( strtotime( $start_date ) < strtotime( $end_date ) ) {
						$off_dates       = [];
						$date_rule       = array_key_exists( 'date_rule', $date_infos ) ? $date_infos['date_rule'] : '';
						$date_rule_array = $date_rule ? explode( ',', $date_rule ) : [];
						if ( in_array( 'off_date_range', $date_rule_array ) ) {
							$off_date_range = array_key_exists( 'off_date_range', $date_infos ) ? $date_infos['off_date_range'] : [];
							if ( is_array( $off_date_range ) && sizeof( $off_date_range ) > 0 ) {
								foreach ( $off_date_range as $off_date ) {
									if ( is_array( $off_date ) && array_key_exists( 'from', $off_date ) && $off_date['from'] && array_key_exists( 'to', $off_date ) && $off_date['to'] ) {
										$from_date      = gmdate( 'Y-m-d', strtotime( $off_date['from'] ) );
										$to_date        = gmdate( 'Y-m-d', strtotime( $off_date['to'] ) );
										$off_date_lists = self::date_separate_period( $from_date, $to_date );
										foreach ( $off_date_lists as $off_date_list ) {
											$off_dates[] = $off_date_list->format( 'Y-m-d' );
										}
									}
								}
							}
						}
						if ( in_array( 'specific_of_date', $date_rule_array ) ) {
							$particular_off_dates = array_key_exists( 'specific_off_dates', $date_infos ) ? $date_infos['specific_off_dates'] : [];
							if ( is_array( $particular_off_dates ) && sizeof( $particular_off_dates ) > 0 ) {
								foreach ( $particular_off_dates as $particular_off_date ) {
									$particular_off_date = gmdate( 'Y-m-d', strtotime( $particular_off_date ) );
									$off_dates[]         = $particular_off_date;
								}
							}
						}
						$off_dates     = array_unique( $off_dates );
						$off_day_array = [];
						if ( in_array( 'weekend', $date_rule_array ) ) {
							$off_days      = array_key_exists( 'weekend', $date_infos ) ? $date_infos['weekend'] : '';
							$off_day_array = $off_days ? explode( ',', $off_days ) : [];
						}
						$repeat = array_key_exists( 'periodic_after', $date_infos ) ? $date_infos['periodic_after'] : 1;
						$dates  = self::date_separate_period( $start_date, $end_date, $repeat );
						foreach ( $dates as $date ) {
							$date = $date->format( 'Y-m-d' );
							if ( strtotime( $date ) >= strtotime( $now ) ) {
								$day = strtolower( gmdate( 'l', strtotime( $date ) ) );
								if ( ! in_array( $date, $off_dates ) && ! in_array( $day, $off_day_array ) ) {
									$all_dates[] = $date;
								}
							}
						}
						//==================//
						if ( in_array( 'special_on_dates', $date_rule_array ) ) {
							$special_on_dates = array_key_exists( 'special_on_dates', $date_infos ) ? $date_infos['special_on_dates'] : [];
							if ( is_array( $special_on_dates ) && sizeof( $special_on_dates ) > 0 ) {
								foreach ( $special_on_dates as $special_on_date ) {
									$date_item = is_array( $special_on_date ) && array_key_exists( 'date', $special_on_date ) ? $special_on_date['date'] : '';
									if ( ! empty( $date_item ) ) {
										$date_item = gmdate( 'Y-m-d', strtotime( $date_item ) );
										if ( strtotime( $date_item ) >= strtotime( $now ) ) {
											$all_dates[] = $date_item;
										}
									}
								}
							}
						}
					}
				}

				return $all_dates;
			}

			public static function get_time_slot( $date_infos ): array {
				$all_slots            = [];
				$date_type            = array_key_exists( 'date_type', $date_infos ) ? $date_infos['date_type'] : 'periodic_date';
				$operation_time_start = array_key_exists( 'operation_time_start', $date_infos ) && ! empty( $date_infos['operation_time_start'] ) ? $date_infos['operation_time_start'] : "00:00";
				$operation_time_end   = array_key_exists( 'operation_time_end', $date_infos ) && ! empty( $date_infos['operation_time_end'] ) ? $date_infos['operation_time_end'] : "23:59";
				if ( strtotime( $operation_time_start ) < strtotime( $operation_time_end ) ) {
					$all_slots['slot'] = $operation_time_start . '-' . $operation_time_end;
				}
				if ( $date_type == 'specific_date' ) {
					$specific_dates = array_key_exists( 'specific_dates', $date_infos ) ? $date_infos['specific_dates'] : [];
					if ( is_array( $specific_dates ) && sizeof( $specific_dates ) > 0 ) {
						foreach ( $specific_dates as $specific_date ) {
							$date_item = is_array( $specific_date ) && array_key_exists( 'date', $specific_date ) ? $specific_date['date'] : '';
							if ( ! empty( $date_item ) ) {
								$start_time = is_array( $specific_date ) && array_key_exists( 'start', $specific_date ) ? $specific_date['start'] : '';
								$end_time   = is_array( $specific_date ) && array_key_exists( 'end', $specific_date ) ? $specific_date['end'] : '';
								if ( ! empty( $start_time ) && ! empty( $end_time ) && strtotime( $start_time ) < strtotime( $end_time ) ) {
									$all_slots[ $date_item ] = $start_time . '-' . $end_time;
								}
							}
						}
					}
				} else {
					$date_rule       = array_key_exists( 'date_rule', $date_infos ) ? $date_infos['date_rule'] : '';
					$date_rule_array = $date_rule ? explode( ',', $date_rule ) : [];
					if ( in_array( 'special_on_dates', $date_rule_array ) ) {
						$special_on_dates = array_key_exists( 'special_on_dates', $date_infos ) ? $date_infos['special_on_dates'] : [];
						if ( is_array( $special_on_dates ) && sizeof( $special_on_dates ) > 0 ) {
							foreach ( $special_on_dates as $special_on_date ) {
								$date_item = is_array( $special_on_date ) && array_key_exists( 'date', $special_on_date ) ? $special_on_date['date'] : '';
								if ( ! empty( $date_item ) ) {
									$start_time = is_array( $special_on_date ) && array_key_exists( 'start', $special_on_date ) ? $special_on_date['start'] : '';
									$end_time   = is_array( $special_on_date ) && array_key_exists( 'end', $special_on_date ) ? $special_on_date['end'] : '';
									if ( ! empty( $start_time ) && ! empty( $end_time ) && strtotime( $start_time ) < strtotime( $end_time ) ) {
										$all_slots[ $date_item ] = $start_time . '-' . $end_time;
									}
								}
							}
						}
					}
					$operation_times = array_key_exists( 'day_wise_time', $date_infos ) ? $date_infos['day_wise_time'] : [];
					if ( in_array( 'day_wise_time', $date_rule_array ) && sizeof( $operation_times ) > 0 ) {
						$days = ABPRF_Layout::week_day();
						foreach ( $days as $key => $day ) {
							$times      = array_key_exists( $key, $operation_times ) && sizeof( $operation_times[ $key ] ) > 0 ? $operation_times[ $key ] : [];
							$start_time = array_key_exists( 'start', $times ) ? $times['start'] : '';
							$end_time   = array_key_exists( 'end', $times ) ? $times['end'] : '';
							if ( ! empty( $start_time ) && ! empty( $end_time ) && strtotime( $start_time ) < strtotime( $end_time ) ) {
								$all_slots[ $key ] = $start_time . '-' . $end_time;
							}
						}
					}
				}

				return $all_slots;
			}

			public static function generate_time_slot( $start_time, $end_time, $interval = 60 ): string {
				$slots = [];
				try {
					if ( ! empty( $start_time ) && ! empty( $end_time ) ) {
						$start  = new DateTime( $start_time );
						$end    = new DateTime( $end_time );
						$minute = (int) $start->format( 'i' );
						if ( $minute > 0 && $minute % $interval !== 0 ) {
							$diff = $interval - ( $minute % $interval );
							$start->modify( "+$diff minutes" );
						}
						$start->setTime( (int) $start->format( 'H' ), (int) $start->format( 'i' ), 0 );
						while ( $start < $end ) {
							$current_slot_start = $start->format( 'H:i' );
							$start->add( new DateInterval( "PT{$interval}M" ) );
							$slots[] = $current_slot_start . '--' . self::date_format( $current_slot_start, 'time' );
						}
					}
				} catch ( Exception $e ) {
					//error_log( $e->getMessage() );
				}

				return implode( '##', $slots );
			}

			public static function date_picker_format(): string {
				$formats = [
					'yy/mm/dd' => 'Y/m/d',
					'yy-dd-mm' => 'Y-d-m',
					'yy/dd/mm' => 'Y/d/m',
					'dd-mm-yy' => 'd-m-Y',
					'dd/mm/yy' => 'd/m/Y',
					'mm-dd-yy' => 'm-d-Y',
					'mm/dd/yy' => 'm/d/Y',
					'd M , yy' => 'j M , Y',
					'D d M , yy' => 'D j M , Y',
					'M d , yy' => 'M  j, Y',
					'D M d , yy' => 'D M  j, Y',
				];

				return $formats[ ABPRF_Date_Format ] ?? 'Y-m-d';
			}

			public static function date_format( $date, $format = 'date' ): string {
				$date_format = ABPRF_Date_Format;
				$time_format = ABPRF_Time_Format;
				$wp_settings = $date_format . '  ' . $time_format;
				//$timezone = wp_timezone_string();
				$timestamp = strtotime( $date );
				if ( $format == 'date' ) {
					$date = date_i18n( $date_format, $timestamp );
				} elseif ( $format == 'time' ) {
					$date = date_i18n( $time_format, $timestamp );
				} elseif ( $format == 'full' ) {
					$date = date_i18n( $wp_settings, $timestamp );
				} elseif ( $format == 'day' ) {
					$date = date_i18n( 'd', $timestamp );
				} elseif ( $format == 'month' ) {
					$date = date_i18n( 'M', $timestamp );
				} elseif ( $format == 'year' ) {
					$date = date_i18n( 'Y', $timestamp );
				} else {
					$date = date_i18n( $format, $timestamp );
				}

				return $date;
			}

			public static function date_separate_period( $start_date, $end_date, $repeat = 1 ): DatePeriod {
				$repeat    = max( $repeat, 1 );
				$_interval = "P" . $repeat . "D";
				$end_date  = gmdate( 'Y-m-d', strtotime( $end_date . ' +1 day' ) );

				return new DatePeriod( new DateTime( $start_date ), new DateInterval( $_interval ), new DateTime( $end_date ) );
			}

			public static function check_time_exit_date( $date ): bool {
				if ( $date ) {
					$parse_date = date_parse( $date );
					if ( ( $parse_date['hour'] && $parse_date['hour'] > 0 ) || ( $parse_date['minute'] && $parse_date['minute'] > 0 ) || ( $parse_date['second'] && $parse_date['second'] > 0 ) ) {
						return true;
					}
				}

				return false;
			}

			public static function sort_date( $a, $b ): int { return strtotime( $a ) - strtotime( $b ); }

			public static function sort_date_array( $a, $b ): int {
				$dateA = strtotime( $a['time'] );
				$dateB = strtotime( $b['time'] );
				if ( $dateA == $dateB ) {
					return 0;
				} elseif ( $dateA > $dateB ) {
					return 1;
				} else {
					return - 1;
				}
			}

			public static function get_date_time_difference( $start_time, $end_time ): array {
				$text = '';
				$info = [];
				if ( ! empty( $start_time ) && ! empty( $end_time ) && strtotime( $start_time ) < strtotime( $end_time ) ) {
					$date1   = date_create( $start_time );
					$date2   = date_create( $end_time );
					$diff    = date_diff( $date1, $date2 );
					$years   = $diff->y;
					$months  = $diff->m;
					$days    = $diff->d;
					$hours   = $diff->h;
					$minutes = $diff->i;
					$seconds = $diff->s;
					if ( $years > 0 ) {
						$text         .= sprintf(
						/* translators: %s =Years */
							_n( ' %s Year', ' %s Years', $years, 'abprf-rental-forge' ), $years );
						$info['year'] = $years;
					}
					if ( $months > 0 ) {
						$text          .= sprintf(
						/* translators: %s = Months */
							_n( ' %s Month', ' %s Months', $months, 'abprf-rental-forge' ), $months );
						$info['month'] = $months;
					}
					if ( $days > 0 ) {
						$text        .= sprintf(
						/* translators: %s = Days */
							_n( ' %s Day', ' %s Days', $days, 'abprf-rental-forge' ), $days );
						$info['day'] = $days;
					}
					if ( $hours > 0 ) {
						$text         .= sprintf(
						/* translators: %s = Hours */
							_n( ' %s Hour', ' %s Hours', $hours, 'abprf-rental-forge' ), $hours );
						$info['hour'] = $hours;
					}
					if ( $minutes > 0 ) {
						$text        .= sprintf(
						/* translators: %s = Minutes */
							_n( ' %s Minute', ' %s Minutes', $minutes, 'abprf-rental-forge' ), $minutes );
						$info['min'] = $minutes;
					}
					if ( $seconds > 0 ) {
						$text        .= sprintf(
						/* translators: %s = Seconds */
							_n( ' %s Second', ' %s Seconds', $seconds, 'abprf-rental-forge' ), $seconds );
						$info['sec'] = $seconds;
					}
					$info['text']     = $text;
					$info['duration'] = abs( strtotime( $end_time ) - strtotime( $start_time ) );
				}

				return $info;
			}

			public static function check_time_slot_exit( $main_slots, $input_slots ): bool {
				if ( ! empty( $main_slots ) && ! empty( $input_slots ) ) {
					$main_slots  = explode( '-', $main_slots );
					$input_slots = explode( '-', $input_slots );
					if ( isset( $main_slots[0] ) && isset( $main_slots[1] ) && isset( $input_slots[0] ) && isset( $input_slots[1] ) ) {
						$main_start  = strtotime( $main_slots[0] );
						$main_end    = strtotime( $main_slots[1] );
						$input_start = strtotime( $input_slots[0] );
						$input_end   = strtotime( $input_slots[1] );
						if ( $main_start <= $input_start && $main_end >= $input_end && $main_start < $input_end && $main_end > $input_start ) {
							return true;
						}
					}
				}

				return false;
			}

			public static function time_duration( $abprf_infos = [], $price_info = [] ) {
				$post_id   = array_key_exists( 'post_id', $abprf_infos ) ? $abprf_infos['post_id'] : 0;
				$rent_rule = array_key_exists( 'rent_rule', $abprf_infos ) ? $abprf_infos['rent_rule'] : '';
				$date_info = array_key_exists( 'date_info', $abprf_infos ) ? $abprf_infos['date_info'] : [];
				$duration  = array_key_exists( 'duration', $date_info ) ? $date_info['duration'] : 0;
				$min       = array_key_exists( 'min', $price_info ) ? $price_info['min'] : 1;
				$max       = array_key_exists( 'max', $price_info ) ? $price_info['max'] : '';
				$dif       = 0;
				if ( $rent_rule == 'hourly' ) {
					$dif = $duration / 3600;
					$dif = ceil( $dif );
					$dif = max( 1, $dif );
				}
				if ( ! empty( $max ) ) {
					$dif_exit = $min <= $dif && $max >= $dif ? 1 : 0;
				} else {
					$dif_exit = $min <= $dif ? 1 : 0;
				}

				return $dif_exit > 0 ? $dif : false;
			}

			public static function booking_buffer( $time, $end = '' ): string {
				$date_infos = ABPRF_Dates;
				if ( ! empty( $end ) ) {
					$buffer_time = array_key_exists( 'sale_close_after', $date_infos ) ? $date_infos['sale_close_after'] : 0;
					$buffer_time = $buffer_time * 60;
					$time        = gmdate( 'Y-m-d H:i', strtotime( $time ) + $buffer_time );
				} else {
					$buffer_time = array_key_exists( 'sale_close_before', $date_infos ) ? $date_infos['sale_close_before'] : 0;
					$buffer_time = $buffer_time * 60;
					$time        = gmdate( 'Y-m-d H:i', strtotime( $time ) - $buffer_time );
				}

				return $time;
			}

			//=============Price Function================//
			public static function tax_with_price( $post_id, $price ): string {
				$num_of_decimal = get_option( 'woocommerce_price_num_decimals', 2 );
				$_product       = self::get_post_info( $post_id, 'link_wc_id', $post_id );
				$product        = wc_get_product( $_product );
				$tax_display    = get_option( 'woocommerce_tax_display_shop' );
				if ( '' === $price ) {
					return '';
				}
				$return_price = (float) $price;
				if ( $product && $product->is_taxable() ) {
					$tax_rates = WC_Tax::get_rates( $product->get_tax_class() );
					if ( ! empty( $tax_rates ) ) {
						$taxes     = WC_Tax::calc_tax( $return_price, $tax_rates, false );
						$tax_total = 'yes' === get_option( 'woocommerce_tax_round_at_subtotal' ) ? array_sum( $taxes ) : array_sum( array_map( 'wc_round_tax_total', $taxes ) );
						if ( ! empty( WC()->customer ) && WC()->customer->get_is_vat_exempt() ) {
							$return_price = round( $return_price, $num_of_decimal );
						} else {
							$return_price = $tax_display === 'excl' ? round( $return_price, $num_of_decimal ) : round( $return_price + $tax_total, $num_of_decimal );
						}
					}
				}

				// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound
				return apply_filters( 'woocommerce_get_price_to_display', $return_price, 1, $product );
			}

			public static function get_price( $abprf_infos = [], $property = [], $time_duration = '' ) {
				$price          = 0;
				$post_id        = array_key_exists( 'post_id', $abprf_infos ) ? $abprf_infos['post_id'] : 0;
				$rent_rule      = array_key_exists( 'rent_rule', $abprf_infos ) ? $abprf_infos['rent_rule'] : '';
				$qty            = array_key_exists( 'qty', $abprf_infos ) ? $abprf_infos['qty'] : 1;
				$property_id    = array_key_exists( 'property_id', $abprf_infos ) ? $abprf_infos['property_id'] : 0;
				$property       = is_array( $property ) && sizeof( $property ) > 0 ? $property : current( ABPRF_Query::get_property( [ 'property_id' => $property_id ] ) );
				$price_qty_info = array_key_exists( 'price_qty_info', $property ) ? $property['price_qty_info'] : '';
				$price_qty_info = ! empty( $price_qty_info ) ? json_decode( $price_qty_info, true ) : [];
				$location       = array_key_exists( 'location', $abprf_infos ) ? $abprf_infos['location'] : '';
				$price_qty_info = ! empty( $price_qty_info ) && ! empty( $location ) && array_key_exists( $location, $price_qty_info ) ? $price_qty_info[ $location ] : $price_qty_info;
				$price_info     = array_key_exists( $rent_rule, $price_qty_info ) ? $price_qty_info[ $rent_rule ] : [];
				$time_duration  = ! empty( $time_duration ) ? $time_duration : self::time_duration( $abprf_infos, $price_info );
				if ( ! empty( $rent_rule ) && ! empty( $time_duration ) && ! empty( $price_info ) ) {
					$rate = is_array( $price_info ) && array_key_exists( 'price', $price_info ) ? $price_info['price'] : 0;
					$rate = apply_filters( 'abprf_filter_property_price', $rate, $abprf_infos, $property );
					if ( $rent_rule == 'hourly' || $rent_rule == 'daily' || $rent_rule == 'monthly' ) {
						$price = $rate * $time_duration * $qty;
					}
				}

				return $price > 0 ? self::tax_with_price( $post_id, $price ) : 0;
			}

			public static function get_deposit_price( $abprf_infos = [], $property = [] ) {
				$price = 0;
				if ( is_array( $abprf_infos ) && sizeof( $abprf_infos ) > 0 ) {
					$property_id    = array_key_exists( 'property_id', $abprf_infos ) ? $abprf_infos['property_id'] : 0;
					$rent_rule      = array_key_exists( 'rent_rule', $abprf_infos ) ? $abprf_infos['rent_rule'] : '';
					$qty            = array_key_exists( 'qty', $abprf_infos ) ? $abprf_infos['qty'] : 0;
					$property       = is_array( $property ) && sizeof( $property ) > 0 ? $property : current( ABPRF_Query::get_property( [ 'property_id' => $property_id ] ) );
					$price_qty_info = array_key_exists( 'price_qty_info', $property ) ? $property['price_qty_info'] : '';
					$price_qty_info = ! empty( $price_qty_info ) ? json_decode( $price_qty_info, true ) : [];
					$location       = array_key_exists( 'location', $abprf_infos ) ? $abprf_infos['location'] : '';
					$price_qty_info = ! empty( $price_qty_info ) && ! empty( $location ) && array_key_exists( $location, $price_qty_info ) ? $price_qty_info[ $location ] : $price_qty_info;
					$price_info     = ! empty( $rent_rule ) && array_key_exists( $rent_rule, $price_qty_info ) ? $price_qty_info[ $rent_rule ] : [];
					$deposit_info   = array_key_exists( 'deposit', $price_info ) ? $price_info['deposit'] : [];
					$deposit_type   = is_array( $deposit_info ) && array_key_exists( 'type', $deposit_info ) ? $deposit_info['type'] : '';
					$deposit_value  = is_array( $deposit_info ) && array_key_exists( 'value', $deposit_info ) ? $deposit_info['value'] : '';
					if ( ! empty( $deposit_type ) && ! empty( $deposit_type ) && $qty > 0 && ! empty( $property ) ) {
						if ( $deposit_type == 'fixed' ) {
							$price = $deposit_value;
						} elseif ( $deposit_type == 'percent' ) {
							$price = array_key_exists( 'price', $abprf_infos ) ? $abprf_infos['price'] : 0;
							$price = $price * $deposit_value / 100;
						} else {
							$price = $qty * $deposit_value;
						}
					}
				}

				return $price;
			}

			public static function get_additional_price( $post_id, $service_name, $abprf_infos = [] ): int|string {
				$display                  = array_key_exists( 'display_additional_services', $abprf_infos ) ? $abprf_infos['display_additional_services'] : ABPRF_Function::get_post_info( $post_id, 'display_additional_services', 'on' );
				$active_global_additional = array_key_exists( 'active_global_additional', $abprf_infos ) ? $abprf_infos['active_global_additional'] : ABPRF_Function::get_post_info( $post_id, 'active_global_additional', 'on' );
				if ( $active_global_additional == 'on' ) {
					$services = ABPRF_Function::get_option( 'abprf_additional' );
				} else {
					$services = array_key_exists( 'abprf_additional', $abprf_infos ) ? $abprf_infos['abprf_additional'] : ABPRF_Function::get_post_info( $post_id, 'abprf_additional', [] );
				}
				$price = 0;
				if ( $display == 'on' && sizeof( $services ) > 0 ) {
					foreach ( $services as $service ) {
						$ex_name = array_key_exists( 'name', $service ) ? $service['name'] : '';
						if ( $ex_name == $service_name ) {
							$price = array_key_exists( 'price', $service ) ? $service['price'] : 0;
						}
					}
				}

				return $price > 0 ? self::tax_with_price( $post_id, $price ) : 0;
			}

			//=============================//
			public static function update_global_data( $post_id = '' ): void {
				if ( ! empty( $post_id ) ) {
					$img_info             = [];
					$fec_info             = [];
					$brand_info             = '';
					$min_price             = ABPRF_Function::get_option( 'abprf_min_price' );
					$arg['rent_continue'] = 'on';
					$arg['status']        = 'publish';
					$arg['post_id']       = $post_id;
					$count                = 0;
					$properties           = ABPRF_Query::get_property( $arg );
					if ( ! empty( $properties ) && is_array( $properties ) && sizeof( $properties ) > 0 ) {
						$rent_rule  = ABPRF_Function::get_post_info( $post_id, 'rent_rule' );
						$title = get_the_title( $post_id );
						$rate=[];
						foreach ( $properties as $property ) {
							$slider    = array_key_exists( 'gallery', $property ) ? $property['gallery'] : '';
							$name      = array_key_exists( 'name', $property ) ? $property['name'] : '';
							$brand      = array_key_exists( 'brand', $property ) ? $property['brand'] : '';
							if(!empty($brand)) {
								$brand_info = ! empty( $brand_info ) ? $brand_info . ',' . $brand : $brand;
							}
							$image_ids = explode( ',', $slider );
							if ( ! empty( $image_ids ) ) {
								foreach ( $image_ids as $id ) {
									$img_info[ $count ]['id']    = $id;
									$img_info[ $count ]['post']  = $title;
									$img_info[ $count ]['label'] = $name;
									$count ++;
								}
							}
							$features = array_key_exists( 'features', $property ) ? $property['features'] : '';
							$features = ! empty( $features ) ? explode( ',', $features ) : [];
							if ( ! empty( $features ) && is_array( $features ) && sizeof( $features ) > 0 ) {
								$fec_info = array_merge( $fec_info, $features );
							}

							$price_qty_info = array_key_exists( 'price_qty_info', $property ) ? $property['price_qty_info'] : '';
							$price_qty_info = ! empty( $price_qty_info ) ? json_decode( $price_qty_info, true ) : [];
							$price_info     = ! empty( $rent_rule ) && array_key_exists( $rent_rule, $price_qty_info ) ? $price_qty_info[ $rent_rule ] : [];
							$rate[] = is_array( $price_info ) && array_key_exists( 'price', $price_info ) ? $price_info['price'] : 0;

						}
						$fec_info=array_unique( $fec_info );
						$fec_info = implode( ',', $fec_info );
						$min_price[$post_id]=min($rate);
						update_post_meta( $post_id, 'abprf_sliders', $img_info );
						update_post_meta( $post_id, 'abprf_features', $fec_info );
						update_post_meta( $post_id, 'abprf_brand', $brand_info );
						update_option(  'abprf_min_price', $min_price );
					}
				}
			}
			//=============================//
			//============== Post Function===============//
			public static function get_transport_list_details( $bp, $dp, $bp_date ): array {
				$list_infos = [];
				//$equipment_ids = ABPRF_Query::get_equipment_id( $bp, $dp );
				$equipment_ids = [];
				if ( sizeof( $equipment_ids ) > 0 ) {
					foreach ( $equipment_ids as $equipment_id ) {
						//$full_infos = self::get_route_full_info( $equipment_id, $bp, $bp_date );
						$full_infos = [];
						if ( sizeof( $full_infos ) > 0 ) {
							foreach ( $full_infos as $full_info ) {
								if ( $full_info['stop'] == $bp ) {
									$list_infos[ $equipment_id ]['id']   = $equipment_id;
									$list_infos[ $equipment_id ]['time'] = $full_info['time'];
								}
								if ( $full_info['stop'] == $dp ) {
									$list_infos[ $equipment_id ]['dp_time'] = $full_info['time'];
								}
							}
						}
					}
					usort( $list_infos, "self::sort_date_array" );
				}

				return $list_infos;
			}

			public static function get_seat_type( $type ): string {
				if ( $type == 'adult' || $type == 'child' || $type == 'infant' ) {
					$ticket_names_array = self::get_ticket_type();

					return '( ' . $ticket_names_array[ $type ] . ' )';
				}

				return '';
			}

			public static function get_form_data( $abprf_infos = [] ): array {
				$post_id_form      = $post_id_url = 0;
				$transport_bp_form = $transport_bp_url = $transport_dp_form = $transport_dp_url = $bp_date_form = $bp_date_url = $return_date_form = $return_date_url = '';
				$single_post_form  = $single_post_url = false;
				if ( isset( $_POST['abprf_search_form_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['abprf_search_form_nonce'] ) ), 'abprf_search_form_nonce' ) ) {
					$post_id_form      = isset( $_POST['_post_id'] ) ? sanitize_text_field( wp_unslash( $_POST['_post_id'] ) ) : $post_id_form;
					$transport_bp_form = isset( $_POST['_bp'] ) ? sanitize_text_field( wp_unslash( $_POST['_bp'] ) ) : '';
					$transport_dp_form = isset( $_POST['_dp'] ) ? sanitize_text_field( wp_unslash( $_POST['_dp'] ) ) : '';
					$bp_date_form      = isset( $_POST['_j_date'] ) ? sanitize_text_field( wp_unslash( $_POST['_j_date'] ) ) : '';
					$return_date_form  = isset( $_POST['_r_date'] ) ? sanitize_text_field( wp_unslash( $_POST['_r_date'] ) ) : '';
					$single_post_form  = isset( $_POST['single_post'] ) && sanitize_text_field( wp_unslash( $_POST['single_post'] ) );
				}
				if ( isset( $_GET['abprf_search_form_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['abprf_search_form_nonce'] ) ), 'abprf_search_form_nonce' ) ) {
					$post_id_url      = isset( $_GET['_post_id'] ) ? sanitize_text_field( wp_unslash( $_GET['_post_id'] ) ) : $post_id_url;
					$transport_bp_url = isset( $_GET['_bp'] ) ? sanitize_text_field( wp_unslash( $_GET['_bp'] ) ) : '';
					$transport_dp_url = isset( $_GET['_dp'] ) ? sanitize_text_field( wp_unslash( $_GET['_dp'] ) ) : '';
					$bp_date_url      = isset( $_GET['_j_date'] ) ? sanitize_text_field( wp_unslash( $_GET['_j_date'] ) ) : '';
					$return_date_url  = isset( $_GET['_r_date'] ) ? sanitize_text_field( wp_unslash( $_GET['_r_date'] ) ) : '';
					$single_post_url  = isset( $_GET['single_post'] ) && sanitize_text_field( wp_unslash( $_GET['single_post'] ) );
				}
				$post_id     = array_key_exists( '_post_id', $abprf_infos ) ? $abprf_infos['_post_id'] : 0;
				$bp_date     = $bp_date_form ?: $bp_date_url;
				$bp_date     = $bp_date ? gmdate( 'Y-m-d', strtotime( $bp_date ) ) : '';
				$return_date = $return_date_form ?: $return_date_url;
				$return_date = $return_date ? gmdate( 'Y-m-d', strtotime( $return_date ) ) : '';
				$single_post = array_key_exists( 'single_post', $abprf_infos ) && $abprf_infos['single_post'];
				$single_post = $single_post || $single_post_form || $single_post_url;
				//============================//
				$form_data['_post_id']    = max( $post_id, $post_id_form, $post_id_url );
				$form_data['_bp']         = $transport_bp_form ?: $transport_bp_url;
				$form_data['_dp']         = $transport_dp_form ?: $transport_dp_url;
				$form_data['_j_date']     = $bp_date;
				$form_data['_r_date']     = $return_date;
				$form_data['single_post'] = $single_post;

				return $form_data;
			}

			public static function status_text( $status ) {
				$status_array = wc_get_order_statuses();

				return array_key_exists( $status, $status_array ) ? $status_array[ $status ] : '';
			}

			public static function get_ticket_type( $type = '' ) {
				$types = [
					'adult' => __( 'Adult', 'abprf-rental-forge' ),
					'child' => __( 'Child', 'abprf-rental-forge' ),
					'infant' => __( 'Infant', 'abprf-rental-forge' ),
				];

				return $type ? ( array_key_exists( $type, $types ) ? $types[ $type ] : '' ) : $types;
			}
		}
		new ABPRF_Function();
	}