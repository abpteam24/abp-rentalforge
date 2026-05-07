<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	if ( ! class_exists( 'ABPRF_Woocommerce' ) ) {
		class ABPRF_Woocommerce {
			public function __construct() {
				add_filter( 'woocommerce_add_cart_item_data', array( $this, 'add_cart_item_data' ), 90, 3 );
				add_action( 'woocommerce_before_calculate_totals', array( $this, 'before_calculate_totals' ), 90 );
				add_filter( 'woocommerce_cart_item_thumbnail', array( $this, 'cart_item_thumbnail' ), 90, 3 );
				add_filter( 'woocommerce_get_item_data', array( $this, 'get_item_data' ), 90, 2 );
				//=============================//
				//add_action( 'woocommerce_after_checkout_validation', array( $this, 'after_checkout_validation' ) );
				add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'checkout_create_order_line_item' ), 90, 4 );
				add_action( 'woocommerce_checkout_order_processed', array( $this, 'checkout_order_processed' ) );
				add_action( 'woocommerce_store_api_checkout_order_processed', array( $this, 'api_checkout_order_processed' ) );
				add_filter( 'woocommerce_order_status_changed', array( $this, 'order_status_changed' ), 90, 4 );
			}

			public function add_cart_item_data( $cart_item, $product_id ) {
				$linked_id = ABPRF_Function::get_post_info( $product_id, 'link_abprf_id', $product_id );
				$post_id   = is_string( get_post_status( $linked_id ) ) ? $linked_id : $product_id;
				if ( get_post_type( $post_id ) == ABPRF_Function::get_cpt() && isset( $_POST['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'abprf_registration_nonce' ) ) {
					$start_time                       = isset( $_POST['start_time'] ) ? sanitize_text_field( wp_unslash( $_POST['start_time'] ) ) : '';
					$end_time                         = isset( $_POST['end_time'] ) ? sanitize_text_field( wp_unslash( $_POST['end_time'] ) ) : '';
					$rent_rule                        = isset( $_POST['rent_rule'] ) ? sanitize_text_field( wp_unslash( $_POST['rent_rule'] ) ) : '';
					$date_length_infos                = ABPRF_Function::get_date_time_difference( $start_time, $end_time );
					$abprf_infos['post_id']           = $post_id;
					$abprf_infos['start_time']        = $start_time;
					$abprf_infos['end_time']          = $end_time;
					$abprf_infos['rent_rule']         = $rent_rule;
					$abprf_infos['date_length_infos'] = $date_length_infos;
					$ticket_info                      = self::get_ticket_info( $abprf_infos );
					$additional_infos                 = self::get_additional_info( $post_id );
					$rent_price                       = self::get_price( $ticket_info );
					$ex_price                         = self::get_additional_price( $additional_infos );
					$deposit                          = self::get_deposit_price( $ticket_info );
					$total_price                      = $rent_price + $ex_price + $deposit;
					$cart_item['post_id']             = $post_id;
					$cart_item['start_time']          = $start_time;
					$cart_item['end_time']            = $end_time;
					$cart_item['rent_rule']           = $rent_rule;
					$cart_item['duration']            = array_key_exists( 'text', $date_length_infos ) ? $date_length_infos['text'] : '';
					$cart_item['ticket_info']         = $ticket_info;
					$cart_item['additional_info']     = $additional_infos;
					$cart_item['pass_info']           = self::get_passenger_info( $post_id );
					$cart_item['rent']                = $rent_price;
					$cart_item['ex_price']            = $ex_price;
					$cart_item['deposit']             = $deposit;
					$cart_item['total_price']         = $total_price;
					$cart_item['line_total']          = $total_price;
					$cart_item['line_subtotal']       = $total_price;
					$cart_item                        = apply_filters( 'abprf_add_cart_item_data', $cart_item, $post_id );
					$_SESSION['abprf_cart_success']   = get_the_title( $post_id ) . ' ' . __( 'Add to cart successfully!', 'abprf-rental-forge' );
				}

				//echo '<pre>';print_r( $cart_item);					echo '</pre>';die();
				return $cart_item;
			}

			public function before_calculate_totals( $cart_object ): void {
				foreach ( $cart_object->cart_contents as $value ) {
					$post_id = array_key_exists( 'post_id', $value ) ? $value['post_id'] : 0;
					if ( get_post_type( $post_id ) == ABPRF_Function::get_cpt() ) {
						$total_price = $value['total_price'];
						$value['data']->set_price( $total_price );
						$value['data']->set_regular_price( $total_price );
						$value['data']->set_sale_price( $total_price );
						$value['data']->set_sold_individually( 'yes' );
						$value['data']->get_price();
					}
				}
			}

			public function cart_item_thumbnail( $thumbnail, $cart_item ) {
				$post_id = array_key_exists( 'post_id', $cart_item ) ? $cart_item['post_id'] : 0;
				if ( get_post_type( $post_id ) == ABPRF_Function::get_cpt() ) {
					$url = ABPRF_Function::get_image_url( $post_id );
					if ( $url ) {
						$thumbnail = '<div class="bg_img" data-href="' . get_the_permalink( $post_id ) . '"><div data-bg-image="' . $url . '"></div></div>';
					}
				}

				return $thumbnail;
			}

			public function get_item_data( $item_data, $cart_item ) {
				$post_id = array_key_exists( 'post_id', $cart_item ) ? $cart_item['post_id'] : 0;
				if ( get_post_type( $post_id ) == ABPRF_Function::get_cpt() ) {
					if ( is_checkout() && has_block( 'woocommerce/checkout', wc_get_page_id( 'checkout' ) ) ) {
						$item_data = $this->display_cart_item_block( $cart_item );
					} elseif ( is_cart() && has_block( 'woocommerce/cart', wc_get_page_id( 'cart' ) ) ) {
						$item_data = $this->display_cart_item_block( $cart_item );
					} else {
						ob_start();
						do_action( 'abprf_display_cart_item', $cart_item );
						$item_data[] = array( 'name' => __( 'Booking Details', 'abprf-rental-forge' ), 'value' => ob_get_clean() );
					}
				}

				return $item_data;
			}

			public static function get_ticket_info( $abprf_infos = [] ) {
				$booking_info = [];
				if ( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'abprf_registration_nonce' ) ) {
					$post_id           = array_key_exists( 'post_id', $abprf_infos ) ? $abprf_infos['post_id'] : '';
					$start_time        = array_key_exists( 'start_time', $abprf_infos ) ? $abprf_infos['start_time'] : '';
					$end_time          = array_key_exists( 'end_time', $abprf_infos ) ? $abprf_infos['end_time'] : '';
					$rent_rule         = array_key_exists( 'rent_rule', $abprf_infos ) ? $abprf_infos['rent_rule'] : '';
					$date_length_infos = array_key_exists( 'date_length_infos', $abprf_infos ) ? $abprf_infos['date_length_infos'] : [];
					$property_ids      = isset( $_POST['property_id'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['property_id'] ) ) : [];
					$property_check    = isset( $_POST['property_check'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['property_check'] ) ) : [];
					$property_qty      = isset( $_POST['property_qty'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['property_qty'] ) ) : [];
					if ( ! empty( $start_time ) && ! empty( $end_time ) && ! empty( $rent_rule ) && ! empty( $date_length_infos ) && sizeof( $date_length_infos ) > 0 && ! empty( $property_ids ) && ! empty( $property_check ) && ! empty( $property_qty ) && ! empty( $post_id ) ) {
						if ( sizeof( $property_check ) > 0 ) {
							foreach ( $property_check as $key => $check ) {
								$property_id = array_key_exists( $key, $property_ids ) ? $property_ids[ $key ] : '';
								$qty         = array_key_exists( $key, $property_qty ) ? $property_qty[ $key ] : '';
								if ( ! empty( $check ) && ! empty( $property_id ) && ! empty( $qty ) ) {
									$property = current( ABPRF_Query::get_property( [ 'property_id' => $property_id ] ) );
									if ( ! empty( $property ) ) {
										$abprf_infos['property_id']              = $property_id;
										$abprf_infos['property_info']            = $property;
										$abprf_infos['qty']                      = $qty;
										$price                                   = ABPRF_Function::get_price( $abprf_infos );
										$abprf_infos['price']                    = $price;
										$booking_info[ $property_id ]['name']    = array_key_exists( 'name', $property ) ? $property['name'] : '';
										$booking_info[ $property_id ]['price']   = $price;
										$booking_info[ $property_id ]['deposit'] = ABPRF_Function::get_deposit_price( $abprf_infos );
										$booking_info[ $property_id ]['qty']     = $qty;
									}
								}
							}
						}
					}
				}

				return apply_filters( 'abprf_cart_booking_info_filter', $booking_info );
			}

			public static function get_price( $ticket_infos ) {
				$price = 0;
				if ( is_array( $ticket_infos ) && sizeof( $ticket_infos ) > 0 ) {
					foreach ( $ticket_infos as $ticket_info ) {
						$ticket_price = array_key_exists( 'price', $ticket_info ) ? $ticket_info['price'] : 0;
						$price        = $price + $ticket_price;
					}
				}

				return $price;
			}

			public static function get_deposit_price( $ticket_infos ) {
				$price = 0;
				if ( is_array( $ticket_infos ) && sizeof( $ticket_infos ) > 0 ) {
					foreach ( $ticket_infos as $ticket_info ) {
						$ticket_price = array_key_exists( 'deposit', $ticket_info ) ? $ticket_info['deposit'] : 0;
						$price        = $price + $ticket_price;
					}
				}

				return $price;
			}

			public static function get_additional_price( $services ) {
				$price = 0;
				if ( is_array( $services ) && sizeof( $services ) > 0 ) {
					foreach ( $services as $service ) {
						$ticket_price = array_key_exists( 'price', $service ) ? $service['price'] : 0;
						$qty          = array_key_exists( 'qty', $service ) ? $service['qty'] : 0;
						$price        = $price + $ticket_price * $qty;
					}
				}

				return $price;
			}

			public static function get_additional_info( $post_id ): array {
				$infos = array();
				if ( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'abprf_registration_nonce' ) ) {
					$display                  = ABPRF_Function::get_post_info( $post_id, 'display_additional_services', 'on' );
					$active_global_additional = ABPRF_Function::get_post_info( $post_id, 'active_global_additional', 'on' );
					if ( $active_global_additional == 'on' ) {
						$services = ABPRF_Function::get_option( 'abprf_additional' );
					} else {
						$services = ABPRF_Function::get_post_info( $post_id, 'abprf_additional', [] );
					}
					if ( $display == 'on' && sizeof( $services ) > 0 ) {
						$abprf_infos['display_additional_services'] = $display;
						$abprf_infos['active_global_additional']    = $active_global_additional;
						$abprf_infos['abprf_additional']            = $services;
						foreach ( $services as $id => $service ) {
							$name     = isset( $_POST[ 'name_' . $id ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'name_' . $id ] ) ) : '';
							$quantity = isset( $_POST[ 'qty_' . $id ] ) ? sanitize_text_field( wp_unslash( $_POST[ 'qty_' . $id ] ) ) : '';
							if ( ! empty( $name ) && ! empty( $quantity ) && $quantity > 0 ) {
								$infos[ $id ]['name']       = $name;
								$infos[ $id ]['qty']        = $quantity;
								$infos[ $id ]['price']      = ABPRF_Function::get_additional_price( $post_id, $name, $abprf_infos );
								$infos[ $id ]['icon']       = array_key_exists( 'icon', $service ) ? $service['icon'] : '';
								$infos[ $id ]['returnable'] = array_key_exists( 'returnable', $service ) ? $service['returnable'] : 'no';
							}
						}
					}
				}

				return $infos;
			}

			public static function get_passenger_info( $post_id ): array {
				$pass_info = [];
				if ( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'abprf_registration_nonce' ) ) {
					$display       = ABPRF_Function::get_post_info( $post_id, 'display_client_form', 'on' );
					$active_global = ABPRF_Function::get_post_info( $post_id, 'active_global_form', 'on' );
					if ( $active_global == 'on' ) {
						$forms = ABPRF_Function::get_option( 'abprf_forms' );
					} else {
						$forms = ABPRF_Function::get_post_info( $post_id, 'abprf_forms', [] );
					}
					if ( $display == 'on' && sizeof( $forms ) > 0 ) {
						foreach ( $forms as $id => $form ) {
							$info                      = isset( $_POST[ $id ] ) ? sanitize_text_field( wp_unslash( $_POST[ $id ] ) ) : '';
							$pass_info[ $id ]['label'] = array_key_exists( 'label', $form ) ? $form['label'] : '';
							$pass_info[ $id ]['value'] = $info;
						}
					}
				}

				return $pass_info;
			}

			public function display_cart_item_block( $cart_item ): array {
				$start_time      = array_key_exists( 'start_time', $cart_item ) ? $cart_item['start_time'] : '';
				$end_time        = array_key_exists( 'end_time', $cart_item ) ? $cart_item['end_time'] : '';
				$duration        = array_key_exists( 'duration', $cart_item ) ? $cart_item['duration'] : '';
				$ticket_infos    = array_key_exists( 'ticket_info', $cart_item ) ? $cart_item['ticket_info'] : [];
				$additional_info = array_key_exists( 'additional_info', $cart_item ) ? $cart_item['additional_info'] : [];
				$attendee_infos  = array_key_exists( 'pass_info', $cart_item ) ? $cart_item['pass_info'] : [];
				$item_data[]     = array( 'name' => __( 'Booking Information', 'abprf-rental-forge' ), 'value' => '<br />' );
				$item_data[]     = array( 'name' => __( 'Rent Start Time', 'abprf-rental-forge' ), 'value' => ABPRF_Function::date_format( $start_time, 'full' ) . '<br />' );
				$item_data[]     = array( 'name' => __( 'Rent End Time', 'abprf-rental-forge' ), 'value' => ABPRF_Function::date_format( $end_time, 'full' ) . '<br />' );
				$item_data[]     = array( 'name' => __( 'Duration', 'abprf-rental-forge' ), 'value' => $duration . '<br />' );
				if ( ! empty( $ticket_infos ) && sizeof( $ticket_infos ) > 0 ) {
					$item_data[] = array( 'name' => __( 'Property Information', 'abprf-rental-forge' ), 'value' => '<br />' );
					foreach ( $ticket_infos as $key => $ticket_info ) {
						$item_data[] = array( 'name' => __( 'Name', 'abprf-rental-forge' ), 'value' => $ticket_info['name'] . '<br />' );
						$item_data[] = array( 'name' => __( 'Quantity', 'abprf-rental-forge' ), 'value' => $ticket_info['qty'] . '<br />' );
						$price       = array_key_exists( 'price', $ticket_info ) ? $ticket_info['price'] : 0;
						$price       = $price > 0 ? wc_price( $price ) : __( 'FREE', 'abprf-rental-forge' );
						$item_data[] = array( 'name' => __( 'Rent', 'abprf-rental-forge' ), 'value' => $price . '<br />' );
						$deposit     = array_key_exists( 'deposit', $ticket_info ) ? $ticket_info['deposit'] : '';
						if ( ! empty( $deposit ) ) {
							$item_data[] = array( 'name' => __( 'Deposit', 'abprf-rental-forge' ), 'value' => wc_price( $deposit ) . '<br />' );
						}
						$item_data = apply_filters( 'abprf_cart_property_info_block', $item_data, $cart_item, $key );
					}
					if ( ! empty( $additional_info ) && sizeof( $additional_info ) > 0 ) {
						$item_data[] = array( 'name' => __( 'Additional Information', 'abprf-rental-forge' ), 'value' => '<br />' );
						foreach ( $additional_info as $additional ) {
							if ( is_array( $additional ) ) {
								$qty         = array_key_exists( 'qty', $additional ) ? $additional['qty'] : 1;
								$price       = array_key_exists( 'price', $additional ) ? $additional['price'] : 0;
								$price_text  = $price > 0 ? wc_price( $price ) : __( 'FREE', 'abprf-rental-forge' );
								$ex_price    = $price > 0 ? wc_price( $price * $qty ) : __( 'FREE', 'abprf-rental-forge' );
								$item_data[] = array( 'name' => array_key_exists( 'name', $additional ) && $additional['name'] ? $additional['name'] : '', 'value' => $price_text . ' X ' . $qty . '  = ' . $ex_price . '<br />' );
							}
						}
					}
					if ( ! empty( $attendee_infos ) && sizeof( $attendee_infos ) > 0 ) {
						$item_data[] = array( 'name' => __( 'Client Information', 'abprf-rental-forge' ), 'value' => '<br />' );
						foreach ( $attendee_infos as $attendee_info ) {
							$label = array_key_exists( 'label', $attendee_info ) ? $attendee_info['label'] : '';
							$value = array_key_exists( 'value', $attendee_info ) ? $attendee_info['value'] : '';
							if ( $label && $value ) {
								$item_data[] = array( 'name' => $label, 'value' => $value . '<br />' );
							}
						}
					}
				}

				return $item_data;
			}

			//=============================//
			public function after_checkout_validation(): void {
				global $woocommerce;
				$cart_items = $woocommerce->cart->get_cart();
				foreach ( $cart_items as $cart_item ) {
					$post_id = array_key_exists( 'post_id', $cart_item ) ? $cart_item['post_id'] : 0;
					if ( get_post_type( $post_id ) == ABPRF_Function::get_cpt() ) {
						$bp           = array_key_exists( 'bp', $cart_item ) ? $cart_item['bp'] : '';
						$dp           = array_key_exists( 'dp', $cart_item ) ? $cart_item['dp'] : '';
						$origin_time  = array_key_exists( 'origin_time', $cart_item ) ? $cart_item['origin_time'] : '';
						$seat_type    = ABPRF_Function::get_post_info( $post_id, 'seat_type', 'seat_plan' );
						$ticket_infos = array_key_exists( 'ticket_info', $cart_item ) ? $cart_item['ticket_info'] : [];
						if ( sizeof( $ticket_infos ) > 0 ) {
							$sold_seats = ABPRF_Query::get_sold_ticket( $post_id, $bp, $dp, $origin_time );
							if ( $seat_type == 'seat_plan' ) {
								foreach ( $ticket_infos as $ticket_info ) {
									$seat_name = array_key_exists( 'seat', $ticket_info ) ? $ticket_info['seat'] : '';
									if ( $seat_name ) {
										if ( in_array( $seat_name, $sold_seats ) ) {
											$woocommerce->cart->empty_cart();
											wc_add_notice( __( "Oh ! We are Sorry, Your Selected Seat Already Booked by another . please Try another Seat.", 'abprf-rental-forge' ), 'error' );
										}
									}
								}
							} else {
								$total_qty = 0;
								foreach ( $ticket_infos as $ticket_info ) {
									$total_qty += array_key_exists( 'qty', $ticket_info ) ? $ticket_info['qty'] : 0;
								}
								$total_ticket   = ABPRF_Function::get_post_info( $post_id, 'total_seat', 0 );
								$sold_ticket    = sizeof( $sold_seats );
								$available_seat = max( 0, $total_ticket - $sold_ticket );
								if ( $available_seat < $total_qty ) {
									$woocommerce->cart->empty_cart();
									wc_add_notice( __( "Oh ! We are Sorry, Your Selected Ticket Already Booked by another . please Try another Ticket.", 'abprf-rental-forge' ), 'error' );
								}
							}
						}
					}
				}
			}

			public function checkout_create_order_line_item( $item, $key, $cart_item ): void {
				$post_id = array_key_exists( 'post_id', $cart_item ) ? $cart_item['post_id'] : 0;
				if ( get_post_type( $post_id ) == ABPRF_Function::get_cpt() ) {
					$start_time      = $cart_item['start_time'] ?? '';
					$end_time        = $cart_item['end_time'] ?? '';
					$duration        = $cart_item['duration'] ?? '';
					$ticket_infos    = $cart_item['ticket_info'] ?? [];
					$additional_info = $cart_item['additional_info'] ?? [];
					$attendee_infos  = $cart_item['pass_info'] ?? [];
					if ( ! empty( $ticket_infos ) && sizeof( $ticket_infos ) > 0 ) {
						$item->add_meta_data( __( 'Booking Information', 'abprf-rental-forge' ), '' );
						$item->add_meta_data( __( 'Rent Start Time', 'abprf-rental-forge' ), ABPRF_Function::date_format( $start_time, 'full' ) );
						$item->add_meta_data( __( 'Rent End Time', 'abprf-rental-forge' ), ABPRF_Function::date_format( $end_time, 'full' ) );
						$item->add_meta_data( __( 'Duration', 'abprf-rental-forge' ), $duration );
						$item->add_meta_data( __( 'Property Information', 'abprf-rental-forge' ), '' );
						foreach ( $ticket_infos as $ticket_info ) {
							$item->add_meta_data( __( 'Property Name', 'abprf-rental-forge' ), $ticket_info['name'] );
							$item->add_meta_data( __( 'Quantity', 'abprf-rental-forge' ), $ticket_info['qty'] );
							$price = array_key_exists( 'price', $ticket_info ) ? $ticket_info['price'] : 0;
							$price = $price > 0 ? wc_price( $price ) : __( 'FREE', 'abprf-rental-forge' );
							$item->add_meta_data( __( 'Rent', 'abprf-rental-forge' ), $price );
							$deposit = array_key_exists( 'deposit', $ticket_info ) ? $ticket_info['deposit'] : '';
							if ( ! empty( $deposit ) ) {
								$item->add_meta_data( __( 'Deposit', 'abprf-rental-forge' ), wc_price( $deposit ) );
							}
						}
						if ( ! empty( $additional_info ) && sizeof( $additional_info ) > 0 ) {
							$item->add_meta_data( __( 'Additional Information', 'abprf-rental-forge' ), '' );
							foreach ( $additional_info as $additional ) {
								$name       = array_key_exists( 'name', $additional ) && $additional['name'] ? $additional['name'] : '';
								$qty        = array_key_exists( 'qty', $additional ) ? $additional['qty'] : 1;
								$price      = array_key_exists( 'price', $additional ) ? $additional['price'] : 0;
								$price_text = $price > 0 ? wc_price( $price ) : __( 'FREE', 'abprf-rental-forge' );
								$ex_price   = $price > 0 ? wc_price( $price * $qty ) : __( 'FREE', 'abprf-rental-forge' );
								$item->add_meta_data( $name, '  ( ' . $price_text . ' X ' . $additional['qty'] . ') = ' . $ex_price );
							}
						}
						if ( ! empty( $attendee_infos ) && sizeof( $attendee_infos ) > 0 ) {
							$item->add_meta_data( __( 'Client Information', 'abprf-rental-forge' ), '' );
							foreach ( $attendee_infos as $attendee_info ) {
								$label = array_key_exists( 'label', $attendee_info ) ? $attendee_info['label'] : '';
								$value = array_key_exists( 'value', $attendee_info ) ? $attendee_info['value'] : '';
								if ( ! empty( $label ) && ! empty( $value ) ) {
									$item->add_meta_data( $label, $value );
								}
							}
						}
						//=============================//
						$item_info = [
							'post_id' => $post_id,
							'user_id' => get_current_user_id(),
							'start_time' => $start_time,
							'end_time' => $end_time,
							'duration' => $duration,
							'rent_rule' => $cart_item['rent_rule'] ?? '',
							'ticket_info' => $ticket_infos,
							'additional_info' => $additional_info,
							'pass_info' => $attendee_infos,
							'rent' => $cart_item['rent'] ?? '',
							'ex_price' => $cart_item['ex_price'] ?? '',
							'deposit' => $cart_item['deposit'] ?? '',
							'item_total' => $cart_item['total_price'] ?? '',
						];
						$item_info = apply_filters( 'abprf_checkout_create_order_line_item', $item_info, $cart_item );
						$item->add_meta_data( '_abprf_items', $item_info, true );
					}
				}
			}

			public static function save_custom_data( $order_id ): void {
				if ( $order_id ) {
					$order               = wc_get_order( $order_id );
					$order_status        = $order->get_status();
					$order_meta          = get_post_meta( $order_id );
					$payment_method      = $order_meta['_payment_method_title'][0] ?? '';
					$user_id             = $order_meta['_customer_user'][0] ?? '';
					$_billing_first_name = array_key_exists( '_billing_first_name', $order_meta ) ? $order_meta['_billing_first_name'][0] : '';
					$_billing_last_name  = array_key_exists( '_billing_last_name', $order_meta ) ? $order_meta['_billing_last_name'][0] : '';
					$billing_email       = array_key_exists( '_billing_email', $order_meta ) ? $order_meta['_billing_email'][0] : '';
					$billing_phone       = array_key_exists( '_billing_phone', $order_meta ) ? $order_meta['_billing_phone'][0] : '';
					$_billing_address_1  = array_key_exists( '_billing_address_1', $order_meta ) ? $order_meta['_billing_address_1'][0] : '';
					$_billing_address_2  = array_key_exists( '_billing_address_2', $order_meta ) ? $order_meta['_billing_address_2'][0] : '';
					$billing_name        = $_billing_first_name . ' ' . $_billing_last_name;
					$billing_address     = $_billing_address_1 . ' ' . $_billing_address_2;
					if ( $order_status != 'failed' ) {
						$total_order = ABPRF_Query::get_booking_query( [ 'order_id' => $order_id ], 0, 0, true );
						if ( $total_order == 0 ) {
							foreach ( $order->get_items() as $item_id => $item ) {
								$item_info = wc_get_order_item_meta( $item_id, '_abprf_items' );
								if ( ! empty( $item_info ) && is_array( $item_info ) && sizeof( $item_info ) > 0 ) {
									$post_id = array_key_exists( 'post_id', $item_info ) ? $item_info['post_id'] : 0;
									if ( get_post_type( $post_id ) == ABPRF_Function::get_cpt() ) {
										$ticket_infos    = array_key_exists( 'ticket_info', $item_info ) ? $item_info['ticket_info'] : [];
										$start_time      = array_key_exists( 'start_time', $item_info ) ? $item_info['start_time'] : '';
										$end_time        = array_key_exists( 'end_time', $item_info ) ? $item_info['end_time'] : '';
										$book_from       = array_key_exists( 'book_from', $item_info ) ? $item_info['book_from'] : '';
										$book_to         = array_key_exists( 'book_to', $item_info ) ? $item_info['book_to'] : '';
										$additional_info = array_key_exists( 'additional_info', $item_info ) ? $item_info['additional_info'] : '';
										global $wpdb;
										$table_name = $wpdb->prefix . 'abprf_orders';
										if ( ! empty( $ticket_infos ) && sizeof( $ticket_infos ) > 0 ) {
											$property_id = $ex_id = [];
											foreach ( $ticket_infos as $key => $ticket_info ) {
												$property_id[] = $key;
											}
											if ( ! empty( $additional_info ) && sizeof( $additional_info ) > 0 ) {
												foreach ( $additional_info as $key => $additional ) {
													$ex_id[] = $key;
												}
											}
											$price_info['rent']       = array_key_exists( 'rent', $item_info ) ? $item_info['rent'] : '';
											$price_info['ex_price']   = array_key_exists( 'ex_price', $item_info ) ? $item_info['ex_price'] : '';
											$price_info['deposit']    = array_key_exists( 'deposit', $item_info ) ? $item_info['deposit'] : '';
											$price_info['item_total'] = array_key_exists( 'item_total', $item_info ) ? $item_info['item_total'] : '';
											$others['rent_rule']      = array_key_exists( 'rent_rule', $item_info ) ? $item_info['rent_rule'] : '';
											$others['duration']       = array_key_exists( 'duration', $item_info ) ? $item_info['duration'] : '';
											$data                     = [
												'order_id' => intval( $order_id ),
												'item_id' => intval( $item_id ),
												'post_id' => intval( $post_id ),
												'user_id' => intval( $user_id ),
												'property_id' => json_encode( $property_id ),
												'ex_id' => json_encode( $ex_id ),
												'pick_up' => sanitize_text_field( array_key_exists( 'pick_up', $item_info ) ? $item_info['pick_up'] : '' ),
												'start_time' => sanitize_text_field( $start_time ),
												'drop_off' => sanitize_text_field( array_key_exists( 'drop_off', $item_info ) ? $item_info['drop_off'] : '' ),
												'end_time' => sanitize_text_field( $end_time ),
												'book_from' => sanitize_text_field( $book_from ),
												'book_to' => sanitize_text_field( $book_to ),
												'category' => sanitize_text_field( get_post_meta( $post_id, 'category', true ) ),
												'price_info' => json_encode( $price_info ),
												'property_info' => json_encode( $ticket_infos ),
												'ex_info' => json_encode( $additional_info ),
												'pass_info' => json_encode( array_key_exists( 'pass_info', $item_info ) ? $item_info['pass_info'] : [] ),
												'delivery_option' => sanitize_text_field( 'self' ),
												'book_status' => sanitize_text_field( 'placed' ),
												'order_status' => sanitize_text_field( 'wc-' . $order_status ),
												'payment_method' => sanitize_text_field( $payment_method ),
												'billing_name' => sanitize_text_field( $billing_name ),
												'billing_email' => sanitize_text_field( $billing_email ),
												'billing_phone' => sanitize_text_field( $billing_phone ),
												'billing_address' => sanitize_text_field( $billing_address ),
												'others' => json_encode( $others ),
												'created_at' => current_time( 'Y-m-d H:i' ),
												'updated_at' => current_time( 'Y-m-d H:i' )
											];
											// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
											$wpdb->insert( $table_name, $data );
										}
									}
								}
							}
						}
					}
				}
			}

			public function checkout_order_processed( $order_id ): void {
				self::save_custom_data( $order_id );
			}

			public function api_checkout_order_processed( $order ): void {
				$this->checkout_order_processed( $order->get_id() );
			}

			public function order_status_changed( $order_id ): void {
				if ( $order_id && $order_id > 0 ) {
					global $wpdb;
					$table_name   = $wpdb->prefix . 'abprf_orders';
					$order        = wc_get_order( $order_id );
					$order_status = $order->get_status();
					foreach ( $order->get_items() as $item_id => $item_values ) {
						if ( $item_id ) {
							$order_infos = ABPRF_Query::get_booking_query( [ 'item_id' => $item_id ] );
							if ( ! empty( $order_infos ) && sizeof( $order_infos ) > 0 ) {
								$order_info = current( $order_infos );
								$others     = array_key_exists( 'others', $order_info ) ? $order_info['others'] : '';
								if ( ! empty( $others ) ) {
									$others               = json_decode( $others, true );
									$user_id              = get_current_user_id();
									$others['updated_by'] = $user_id;
									$data                 = [
										'others' => json_encode( $others ),
										'order_status' => 'wc-' . $order_status,
										'updated_at' => current_time( 'Y-m-d H:i' )
									];
									$where                = [ 'item_id' => $item_id ];
									// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
									$wpdb->update( $table_name, $data, $where, [ '%s', '%s', '%s' ], [ '%d' ] );
									do_action( 'abprf_send_mail', $item_id );
								}
							}
						}
					}
				}
			}
		}
		new ABPRF_Woocommerce();
	}
