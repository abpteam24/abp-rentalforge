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
					if ( sizeof( $metas ) > 0 ) {
						foreach ( $metas as $key => $meta ) {
							$all_data[ $key ] = self::data_sanitize( $meta[0] );
						}
					}
					$all_data['abprf_configuration'] = self::get_option( 'abprf_configuration' );
				}

				return $all_data;
			}

			public static function get_taxonomy( $name ): array|WP_Error|string {
				return get_terms( array( 'taxonomy' => $name, 'hide_empty' => false ) );
			}

			public static function get_term_meta( $meta_id, $meta_key, $default = '' ) {
				$data = get_term_meta( $meta_id, $meta_key, true ) ?: $default;

				return self::data_sanitize( $data );
			}

			public static function get_all_term_data( $term_name, $value = 'name' ): array {
				$all_data   = [];
				$taxonomies = self::get_taxonomy( $term_name );
				if ( $taxonomies && is_array( $taxonomies ) && sizeof( $taxonomies ) > 0 ) {
					foreach ( $taxonomies as $taxonomy ) {
						$all_data[] = $taxonomy->$value;
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
				if ( is_array( $default ) ) {
					$option_data = $option_data && is_array( $option_data ) ? $option_data : $default;
				} else {
					$option_data = $option_data ?: $default;
				}

				return $option_data;
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

			//============= Date Section================//
			public static function get_post_dates( $post_id ): array {
				$all_dates = [];
				if ( $post_id > 0 ) {
					$active_global_dates = self::get_post_info( $post_id, 'active_global_dates', 'on' );
					if ( $active_global_dates == 'on' ) {
						$date_infos = self::get_option( 'abprf_dates' );
					} else {
						$date_infos = self::get_post_info( $post_id, 'abprf_dates', [] );
					}
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
				}
				$all_dates = array_unique( $all_dates );
				usort( $all_dates, "ABPRF_Function::sort_date" );

				return $all_dates;
			}

			public static function date_picker_format(): string {
				$format      = ABPRF_Date_Format;
				$date_format = 'Y-m-d';
				$date_format = $format == 'yy/mm/dd' ? 'Y/m/d' : $date_format;
				$date_format = $format == 'yy-dd-mm' ? 'Y-d-m' : $date_format;
				$date_format = $format == 'yy/dd/mm' ? 'Y/d/m' : $date_format;
				$date_format = $format == 'dd-mm-yy' ? 'd-m-Y' : $date_format;
				$date_format = $format == 'dd/mm/yy' ? 'd/m/Y' : $date_format;
				$date_format = $format == 'mm-dd-yy' ? 'm-d-Y' : $date_format;
				$date_format = $format == 'mm/dd/yy' ? 'm/d/Y' : $date_format;
				$date_format = $format == 'd M , yy' ? 'j M , Y' : $date_format;
				$date_format = $format == 'D d M , yy' ? 'D j M , Y' : $date_format;
				$date_format = $format == 'M d , yy' ? 'M  j, Y' : $date_format;

				return $format == 'D M d , yy' ? 'D M  j, Y' : $date_format;
			}

			public static function date_format( $date, $format = 'date' ): string {
				$date_format = get_option( 'date_format' );
				$time_format = get_option( 'time_format' );
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

			public static function get_date_time_difference( $date1, $date2 ): string {
				$text = '';
				if ( $date1 && $date2 ) {
					$date1   = date_create( $date1 );
					$date2   = date_create( $date2 );
					$diff    = date_diff( $date1, $date2 );
					$years   = $diff->y;
					$months  = $diff->m;
					$days    = $diff->d;
					$hours   = $diff->h;
					$minutes = $diff->i;
					$seconds = $diff->s;
					if ( $years > 0 ) {
						$text = $years > 1 ? $years . ' ' . __( 'Years', 'abprf-rental-forge' ) : $years . ' ' . __( 'Year', 'abprf-rental-forge' );
					}
					if ( $months > 0 ) {
						$month_text = $months > 1 ? $months . ' ' . __( 'Months', 'abprf-rental-forge' ) : $months . ' ' . __( 'Month', 'abprf-rental-forge' );
						$text       .= $text ? ' , ' . $month_text : $month_text;
					}
					if ( $days > 0 ) {
						$day_text = $days > 1 ? $days . ' ' . __( 'Days', 'abprf-rental-forge' ) : $days . ' ' . __( 'Day', 'abprf-rental-forge' );
						$text     .= $text ? ' , ' . $day_text : $day_text;
					}
					if ( $hours > 0 ) {
						$hour_text = $hours > 1 ? $hours . ' ' . __( 'Hours', 'abprf-rental-forge' ) : $hours . ' ' . __( 'Hour', 'abprf-rental-forge' );
						$text      .= $text ? ' , ' . $hour_text : $hour_text;
					}
					if ( $minutes > 0 ) {
						$minute_text = $minutes > 1 ? $minutes . ' ' . __( 'Minutes', 'abprf-rental-forge' ) : $minutes . ' ' . __( 'Minute', 'abprf-rental-forge' );
						$text        .= $text ? ' , ' . $minute_text : $minute_text;
					}
					if ( $seconds > 0 ) {
						$second_text = $seconds > 1 ? $seconds . ' ' . __( 'Seconds', 'abprf-rental-forge' ) : $seconds . ' ' . __( 'Second', 'abprf-rental-forge' );
						$text        .= $text ? ' , ' . $second_text : $second_text;
					}
				}

				return $text;
			}

			//=============================//
			public static function price_convert_raw( $price ) {
				$price = wp_strip_all_tags( $price );
				$price = str_replace( self::get_option( 'woocommerce_price_display_suffix', '' ), '', $price );
				$price = str_replace( get_woocommerce_currency_symbol(), '', $price );
				$price = str_replace( wc_get_price_thousand_separator(), 't_s', $price );
				$price = str_replace( wc_get_price_decimal_separator(), 'd_s', $price );
				$price = str_replace( 't_s', '', $price );
				$price = str_replace( 'd_s', '.', $price );
				$price = str_replace( '&nbsp;', '', $price );

				return max( $price, 0 );
			}

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

				return apply_filters( 'woocommerce_get_price_to_display', $return_price, 1, $product );
			}

			public static function get_wc_raw_price( $post_id, $price ) {
				$price = self::tax_with_price( $post_id, $price );

				return self::price_convert_raw( $price );
			}

			//=============================//
			public static function get_brand_icon() { return self::get_options( 'abprf_configuration', 'brand_icon' ,'fas fa-hammer'); }

			public static function get_cpt(): string { return 'abprf_post'; }

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
				$template_name = self::get_post_info( $post_id, 'abprf_template', 'default' );
				$file_name     = 'details_theme/' . $template_name . '.php';
				$dir           = ABPRF_DIR . '/abprf_templates/' . $file_name;
				if ( ! file_exists( $dir ) ) {
					$file_name = 'themes/default.php';
				}

				return self::template_path( $file_name );
			}

			public static function template_path( $file_name ): string {
				$file_path   = wp_normalize_path( WP_CONTENT_DIR . DIRECTORY_SEPARATOR . '/abprf_templates/' . $file_name );
				$default_dir = wp_normalize_path( ABPRF_DIR . '/abprf_templates/' . $file_name );

				return file_exists( $file_path ) ? $file_path : $default_dir;
			}

			//============== Category/Post Function===============//
			public static function route_for_price( $routing_infos, $price_infos, $ticket_types ): array {
				$full_price = [];
				if ( sizeof( $routing_infos ) > 0 ) {
					foreach ( $routing_infos as $key => $routing_info ) {
						if ( $routing_info['type'] == 'bp' || $routing_info['type'] == 'both' ) {
							$bp         = $routing_info['stop'];
							$next_infos = array_slice( $routing_infos, $key + 1 );
							if ( sizeof( $next_infos ) > 0 ) {
								foreach ( $next_infos as $next_info ) {
									if ( $next_info['type'] == 'dp' || $next_info['type'] == 'both' ) {
										$dp               = $next_info['stop'];
										$path_price       = [];
										$path_price['bp'] = $bp;
										$path_price['dp'] = $dp;
										if ( sizeof( $price_infos ) > 0 ) {
											foreach ( $price_infos as $price_info ) {
												if ( strtolower( $price_info['bp'] ) == strtolower( $bp ) && strtolower( $price_info['dp'] ) == strtolower( $dp ) ) {
													if ( sizeof( $ticket_types ) > 0 ) {
														foreach ( $ticket_types as $key => $ticket_type ) {
															$price = array_key_exists( $key, $price_info ) && $price_info[ $key ] ? (float) $price_info[ $key ] : '';
															if ( $key == 'adult' && ! $price ) {
																$price = array_key_exists( 'price', $price_info ) && $price_info['price'] ? (float) $price_info['price'] : '';
															}
															$path_price[ $key ] = $price;
														}
													} else {
														$path_price['price'] = array_key_exists( 'price', $price_info ) && $price_info['price'] ? (float) $price_info['price'] : '';
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

			public static function get_routes( $post_id = 0, $bp = true, $bp_point = '' ): array {
				$route_lists = [];
				if ( $post_id > 0 ) {
					$route_lists = self::get_route( $post_id, $bp, $bp_point );
				} else {
					$equipment_ids = ABPRF_Query::get_equipment_id( $bp_point );
					if ( sizeof( $equipment_ids ) > 0 ) {
						foreach ( $equipment_ids as $equipment_id ) {
							$routes      = self::get_route( $equipment_id, $bp, $bp_point );
							$route_lists = array_merge( $route_lists, $routes );
						}
					}
				}

				return array_unique( $route_lists );
			}

			public static function get_route( $post_id = 0, $bp = true, $bp_point = '' ) {
				$route_lists = [];
				if ( $post_id > 0 ) {
					if ( $bp ) {
						$route_lists = self::get_post_info( $post_id, 'abptm_bp', [] );
					} else {
						if ( $bp_point ) {
							$routes = self::get_post_info( $post_id, 'routing_infos', [] );
							if ( sizeof( $routes ) > 0 ) {
								$exit_bp = 0;
								foreach ( $routes as $route ) {
									if ( $exit_bp > 0 ) {
										if ( $route['type'] == 'dp' || $route['type'] == 'both' ) {
											$route_lists[] = $route['stop'];
										}
									} else {
										if ( $route['stop'] == $bp_point && ( $route['type'] == 'bp' || $route['type'] == 'both' ) ) {
											$exit_bp ++;
										}
									}
								}
							}
						} else {
							$route_lists = self::get_post_info( $post_id, 'abptm_dp', [] );
						}
					}
				}

				return $route_lists;
			}

			//=============Date Section================//
			public static function get_dates( $post_id = 0, $bp = '', $dp = '' ): array {
				$all_dates = [];
				if ( $post_id > 0 ) {
					$all_dates = self::get_bus_dates( $post_id, $bp );
				} else {
					if ( $bp ) {
						$bus_ids = ABPRF_Query::get_equipment_id( $bp, $dp );
						if ( sizeof( $bus_ids ) > 0 ) {
							foreach ( $bus_ids as $bus_id ) {
								$dates     = self::get_bus_dates( $bus_id, $bp );
								$all_dates = array_merge( $all_dates, $dates );
							}
						}
					}
				}
				$all_dates = array_unique( $all_dates );
				usort( $all_dates, "ABPRF_Function::sort_date" );

				return $all_dates;
			}

			public static function get_bus_dates( $post_id, $bp = '' ): array {
				$all_dates = [];
				if ( $post_id > 0 ) {
					$dates = self::get_post_dates( $post_id );
					if ( sizeof( $dates ) > 0 ) {
						$routes = self::get_post_info( $post_id, 'routing_infos', [] );
						foreach ( $dates as $date ) {
							$route_infos = self::get_route_info( $post_id, $date, $routes );
							if ( sizeof( $route_infos ) > 0 ) {
								foreach ( $route_infos as $route_info ) {
									if ( sizeof( $route_info ) > 0 ) {
										foreach ( $route_info as $info ) {
											if ( array_key_exists( 'type', $info ) && ( $info['type'] == 'bp' || $info['type'] == 'both' ) ) {
												if ( $bp ) {
													if ( $bp == $info['stop'] ) {
														$all_dates[] = gmdate( 'Y-m-d', strtotime( $info['time'] ) );
													}
												} else {
													$all_dates[] = gmdate( 'Y-m-d', strtotime( $info['time'] ) );
												}
											}
										}
									}
								}
							}
						}
					}
				}

				return array_unique( $all_dates );
			}

			public static function get_route_info( $post_id, $date, $route_infos = [] ): array {
				$all_infos   = [];
				$now         = current_time( 'Y-m-d H:i' );
				$route_infos = sizeof( $route_infos ) > 0 ? $route_infos : self::get_post_info( $post_id, 'routing_infos', [] );
				if ( $date && sizeof( $route_infos ) > 0 ) {
					$prev_date      = $date;
					$prev_full_date = $date;
					$count          = 0;
					foreach ( $route_infos as $info ) {
						if ( array_key_exists( 'time', $info ) && $info['time'] ) {
							$current_date = gmdate( 'Y-m-d H:i', strtotime( $prev_date . ' ' . $info['time'] ) );
							if ( $count > 0 ) {
								if ( strtotime( $prev_full_date ) > strtotime( $current_date ) ) {
									$current_date = gmdate( 'Y-m-d H:i', strtotime( $current_date . ' +1 day' ) );
								}
							}
							if ( strtotime( $now ) < strtotime( $current_date ) ) {
								$info['time']         = $current_date;
								$all_infos[ $date ][] = $info;
								$prev_full_date       = $current_date;
								$prev_date            = gmdate( 'Y-m-d', strtotime( $current_date ) );
								$count ++;
							}
						}
					}
				} else {
					$all_infos = $route_infos;
				}

				return $all_infos;
			}

			public static function get_route_full_info( $post_id, $bp, $bp_date ) {
				$bp_date = strtotime( $bp_date );
				if ( $post_id > 0 ) {
					$now   = current_time( 'Y-m-d H:i' );
					$dates = self::get_post_dates( $post_id );
					if ( sizeof( $dates ) > 0 ) {
						$routes = self::get_post_info( $post_id, 'routing_infos', [] );
						foreach ( $dates as $date ) {
							$route_infos = self::get_route_info( $post_id, $date, $routes );
							if ( sizeof( $route_infos ) > 0 ) {
								foreach ( $route_infos as $route_info ) {
									if ( sizeof( $route_info ) > 0 ) {
										foreach ( $route_info as $info ) {
											$current_date = strtotime( gmdate( 'Y-m-d', strtotime( $info['time'] ) ) );
											if ( array_key_exists( 'stop', $info ) && strtolower( $info['stop'] ) == strtolower( $bp ) && $bp_date == $current_date ) {
												$slice_time = self::slice_buffer_time( $info['time'] );
												if ( strtotime( $now ) < strtotime( $slice_time ) ) {
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

			public static function slice_buffer_time( $date ) {
				$buffer_time = self::get_options( 'abprf_configuration', 'ticket_sale_close_before', 0 ) * 60;
				if ( $buffer_time > 0 ) {
					$date = gmdate( 'Y-m-d H:i', strtotime( $date ) - $buffer_time );
				}

				return $date;
			}

			//=============================//
			public static function get_ticket_type_key( $abprf_infos ): array {
				$post_id      = array_key_exists( 'post_id', $abprf_infos ) ? $abprf_infos['post_id'] : 0;
				$seat_type    = array_key_exists( 'seat_type', $abprf_infos ) ? $abprf_infos['seat_type'] : 'seat_plan';
				$ticket_types = [];
				if ( $seat_type == 'seat_plan' ) {
					$ticket_type = array_key_exists( 'ticket_type', $abprf_infos ) ? $abprf_infos['ticket_type'] : '';
					$ticket_type = $ticket_type ? explode( ',', $ticket_type ) : [];
					foreach ( $ticket_type as $key ) {
						$ticket_types[ $key ] = self::get_ticket_type( $key );
					}
				} else {
					$ticket_infos = array_key_exists( 'equipment_infos', $abprf_infos ) ? $abprf_infos['equipment_infos'] : self::get_post_info( $post_id, 'equipment_infos', [] );
					if ( sizeof( $ticket_infos ) > 0 ) {
						foreach ( $ticket_infos as $key => $ticket_info ) {
							$ticket_types[ $key ] = is_array( $ticket_info ) && array_key_exists( 'name', $ticket_info ) ? $ticket_info['name'] : '';
						}
					}
				}

				return $ticket_types;
			}

			//=============================//
			//==============Price Section===============//
			public static function get_price( $post_id, $bp, $dp, $type = 'price', $ud = false, $date = '' ) {
				$price       = 0;
				$price_infos = self::get_post_info( $post_id, 'price_infos', [] );
				if ( sizeof( $price_infos ) > 0 ) {
					foreach ( $price_infos as $price_info ) {
						if ( $price_info['bp'] == $bp && $price_info['dp'] == $dp ) {
							$price = $price_info[ $type ];
							if ( $ud && $price ) {
								$ud_increase = (int) self::get_post_info( $post_id, 'abptm_ud_price_increase', 0 );
								$price       = $price + ( $price * $ud_increase / 100 );
							}
						}
					}
				}

				return self::get_wc_raw_price( $post_id, $price );
			}

			public static function get_additional_price( $post_id, $service_name ) {
				$services = self::get_post_info( $post_id, 'additional_services' );
				$display  = self::get_post_info( $post_id, 'display_additional_services', 'on' );
				$price    = 0;
				if ( $display == 'on' && sizeof( $services ) > 0 ) {
					foreach ( $services as $service ) {
						$ex_name = array_key_exists( 'name', $service ) ? $service['name'] : '';
						if ( $ex_name == $service_name ) {
							$price = array_key_exists( 'price', $service ) ? $service['price'] : 0;
						}
					}
				}

				return self::get_wc_raw_price( $post_id, $price );
			}

			//=============================//
			public static function get_transport_list_details( $bp, $dp, $bp_date ): array {
				$list_infos    = [];
				$equipment_ids = ABPRF_Query::get_equipment_id( $bp, $dp );
				if ( sizeof( $equipment_ids ) > 0 ) {
					foreach ( $equipment_ids as $equipment_id ) {
						$full_infos = self::get_route_full_info( $equipment_id, $bp, $bp_date );
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

			//=============================//
			public static function get_seat_type( $type ): string {
				if ( $type == 'adult' || $type == 'child' || $type == 'infant' ) {
					$ticket_names_array = self::get_ticket_type();

					return '( ' . $ticket_names_array[ $type ] . ' )';
				}

				return '';
			}

			//=============================//
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

			//=============================//
			public static function get_available_text( $seat_type, $available_seat ): string {
				if ( $seat_type == 'seat_plan' ) {
					if ( $available_seat > 1 ) {
						return __( 'Seats Available !', 'abprf-rental-forge' );
					} else {
						return __( 'Seat Available !', 'abprf-rental-forge' );
					}
				} else {
					if ( $available_seat > 1 ) {
						return __( 'Tickets Available !', 'abprf-rental-forge' );
					} else {
						return __( 'Ticket Available !', 'abprf-rental-forge' );
					}
				}
			}

			public static function get_view_text( $seat_type ): string {
				if ( $seat_type == 'seat_plan' ) {
					return __( 'View Seats', 'abprf-rental-forge' );
				} else {
					return __( 'View Tickets', 'abprf-rental-forge' );
				}
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
			//=============================//
		}
		new ABPRF_Function();
	}