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
				add_action( 'woocommerce_after_checkout_validation', array( $this, 'after_checkout_validation' ) );
				add_action( 'woocommerce_checkout_create_order_line_item', array( $this, 'checkout_create_order_line_item' ), 90, 4 );
				add_action( 'woocommerce_checkout_order_processed', array( $this, 'checkout_order_processed' ) );
				add_action( 'woocommerce_store_api_checkout_order_processed', array( $this, 'api_checkout_order_processed' ) );
				add_filter( 'woocommerce_order_status_changed', array( $this, 'order_status_changed' ), 90, 4 );
			}

			public function add_cart_item_data( $cart_item, $product_id ) {
				$linked_id = ABPRF_Function::get_post_info( $product_id, 'link_abprf_id', $product_id );
				$post_id   = is_string( get_post_status( $linked_id ) ) ? $linked_id : $product_id;
				if ( get_post_type( $post_id ) == ABPRF_Function::get_cpt() && isset( $_POST['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'abprf_registration_nonce' ) ) {
					$origin                       = isset( $_POST['origin_place'] ) ? sanitize_text_field( wp_unslash( $_POST['origin_place'] ) ) : '';
					$origin_time                  = isset( $_POST['origin_time'] ) ? sanitize_text_field( wp_unslash( $_POST['origin_time'] ) ) : '';
					$bp                           = isset( $_POST['transport_bp'] ) ? sanitize_text_field( wp_unslash( $_POST['transport_bp'] ) ) : '';
					$bp_time                      = isset( $_POST['bp_time'] ) ? sanitize_text_field( wp_unslash( $_POST['bp_time'] ) ) : '';
					$dp                           = isset( $_POST['transport_dp'] ) ? sanitize_text_field( wp_unslash( $_POST['transport_dp'] ) ) : '';
					$dp_time                      = isset( $_POST['dp_time'] ) ? sanitize_text_field( wp_unslash( $_POST['dp_time'] ) ) : '';
					$ticket_info                  = self::get_ticket_info( $post_id, $bp, $dp );
					$additional_infos             = self::get_additional_info( $post_id );
					$total_price                  = self::get_price( $ticket_info ) + self::get_additional_price( $additional_infos );
					$cart_item['post_id']         = $post_id;
					$cart_item['origin']          = $origin;
					$cart_item['origin_time']     = $origin_time;
					$cart_item['bp']              = $bp;
					$cart_item['bp_time']         = $bp_time;
					$cart_item['dp']              = $dp;
					$cart_item['dp_time']         = $dp_time;
					$cart_item['pickup']          = isset( $_POST['pickup_point'] ) ? sanitize_text_field( wp_unslash( $_POST['pickup_point'] ) ) : '';
					$cart_item['drop']            = isset( $_POST['drop_point'] ) ? sanitize_text_field( wp_unslash( $_POST['drop_point'] ) ) : '';
					$cart_item['seat_type']       = ABPRF_Function::get_post_info( $post_id, 'seat_type', 'seat_plan' );
					$cart_item['ticket_info']     = $ticket_info;
					$cart_item['additional_info'] = $additional_infos;
					$cart_item['total_price']     = $total_price;
					$cart_item['line_total']      = $total_price;
					$cart_item['line_subtotal']   = $total_price;
					$cart_item                    = apply_filters( 'abprf_add_cart_item_data', $cart_item, $post_id );
				}

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
						$thumbnail = '<div class="abprf_bg_img" data-href="' . get_the_permalink( $post_id ) . '"><div data-bg-image="' . $url . '"></div></div>';
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
						do_action( 'abptm_display_cart_item', $cart_item );
						$item_data[] = array( 'name' => __( 'Booking Details', 'abprf-rental-forge' ), 'value' => ob_get_clean() );
					}
				}

				return $item_data;
			}

			public static function get_ticket_info( $post_id, $bp, $dp ) {
				$ticket_info = [];
				if ( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'abprf_registration_nonce' ) ) {
					$seat_type = ABPRF_Function::get_post_info( $post_id, 'seat_type', 'seat_plan' );
					if ( $seat_type == 'seat_plan' ) {
						$seats        = isset( $_POST['selected_ld'] ) ? sanitize_text_field( wp_unslash( $_POST['selected_ld'] ) ) : '';
						$seats        = $seats ? explode( ',', $seats ) : [];
						$ticket_types = isset( $_POST['selected_ld_type'] ) ? sanitize_text_field( wp_unslash( $_POST['selected_ld_type'] ) ) : '';
						$ticket_types = $ticket_types ? explode( ',', $ticket_types ) : [ 0 ];
						if ( sizeof( $seats ) > 0 && sizeof( $ticket_types ) > 0 ) {
							foreach ( $seats as $key => $seat ) {
								$type = $ticket_types[ $key ];
								if ( $seat ) {
									$ticket_info[] = self::ticket_info( $post_id, $bp, $dp, $seat, $type );
								}
							}
						}
						$dd_seats        = isset( $_POST['selected_ud'] ) ? sanitize_text_field( wp_unslash( $_POST['selected_ud'] ) ) : '';
						$dd_seats        = $dd_seats ? explode( ',', $dd_seats ) : [];
						$dd_ticket_types = isset( $_POST['selected_ud_type'] ) ? sanitize_text_field( wp_unslash( $_POST['selected_ud_type'] ) ) : '';
						$dd_ticket_types = $dd_ticket_types ? explode( ',', $dd_ticket_types ) : [ 0 ];
						if ( sizeof( $dd_seats ) > 0 && sizeof( $dd_ticket_types ) > 0 ) {
							foreach ( $dd_seats as $key => $seat ) {
								$type = $dd_ticket_types[ $key ];
								if ( $seat ) {
									$ticket_info[] = self::ticket_info( $post_id, $bp, $dp, $seat, $type, 1, true );
								}
							}
						}
					} else {
						$id    = isset( $_POST['ticket_id'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['ticket_id'] ) ) : [];
						$qty   = isset( $_POST['equipment_qty'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['equipment_qty'] ) ) : [];
						$types = isset( $_POST['ticket_types'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['ticket_types'] ) ) : [];
						$count = count( $id );
						if ( $count > 0 ) {
							for ( $i = 0; $i < $count; $i ++ ) {
								if ( $qty[ $i ] > 0 ) {
									$ticket_info[] = self::ticket_info( $post_id, $bp, $dp, $types[ $i ], $id[ $i ], $qty[ $i ] );
								}
							}
						}
					}
				}

				return apply_filters( 'abptm_cart_ticket_info_filter', $ticket_info, $post_id );
			}

			public static function ticket_info( $post_id, $bp, $dp, $seat, $ticket_id, $qty = 1, $dd = false ): array {
				$ticket_info['type']  = $ticket_id;
				$ticket_info['seat']  = $seat;
				$ticket_info['price'] = ABPRF_Function::get_price( $post_id, $bp, $dp, $ticket_id );
				$ticket_info['qty']   = $qty;
				$ticket_info['dd']    = $dd ? 1 : '';

				return $ticket_info;
			}

			public static function get_price( $ticket_infos ) {
				$price = 0;
				if ( is_array( $ticket_infos ) && sizeof( $ticket_infos ) > 0 ) {
					foreach ( $ticket_infos as $ticket_info ) {
						$ticket_price = array_key_exists( 'price', $ticket_info ) ? $ticket_info['price'] : 0;
						$qty          = array_key_exists( 'qty', $ticket_info ) ? $ticket_info['qty'] : 0;
						$price        = $price + $ticket_price * $qty;
					}
				}

				return $price;
			}

			public static function get_additional_price( $additional_services ) {
				$price = 0;
				if ( is_array( $additional_services ) && sizeof( $additional_services ) > 0 ) {
					foreach ( $additional_services as $additional_service ) {
						if ( is_array( $additional_service ) && sizeof( $additional_service ) > 0 ) {
							foreach ( $additional_service as $additional ) {
								$ticket_price = array_key_exists( 'price', $additional ) ? $additional['price'] : 0;
								$qty          = array_key_exists( 'qty', $additional ) ? $additional['qty'] : 0;
								$price        = $price + $ticket_price * $qty;
							}
						}
					}
				}

				return $price;
			}

			public static function get_additional_info( $post_id ): array {
				$infos = array();
				if ( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'abprf_registration_nonce' ) ) {
					$additional_services = ABPRF_Function::get_post_info( $post_id, 'additional_services', [] );
					$display             = ABPRF_Function::get_post_info( $post_id, 'display_additional_services', 'on' );
					if ( $display == 'on' && sizeof( $additional_services ) > 0 ) {
						foreach ( $additional_services as $id => $additional_service ) {
							$names    = isset( $_POST[ 'name_' . $id ] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST[ 'name_' . $id ] ) ) : [];
							$quantity = isset( $_POST[ 'qty_' . $id ] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST[ 'qty_' . $id ] ) ) : [];
							if ( sizeof( $names ) > 0 ) {
								foreach ( $names as $key => $name ) {
									if ( $name && $quantity[ $key ] > 0 ) {
										$infos[ $key ][ $id ]['name']  = $name;
										$infos[ $key ][ $id ]['qty']   = $quantity[ $key ];
										$infos[ $key ][ $id ]['price'] = ABPRF_Function::get_additional_price( $post_id, $name );
										$infos[ $key ][ $id ]['icon']  = array_key_exists( 'icon', $additional_service ) ? $additional_service['icon'] : '';
									}
								}
							}
						}
					}
				}

				return $infos;
			}

			public function display_cart_item_block( $cart_item ): array {
				$post_id         = array_key_exists( 'post_id', $cart_item ) ? $cart_item['post_id'] : 0;
				$additional_info = array_key_exists( 'additional_info', $cart_item ) ? $cart_item['additional_info'] : [];
				$origin          = array_key_exists( 'origin', $cart_item ) ? $cart_item['origin'] : '';
				$origin_time     = array_key_exists( 'origin_time', $cart_item ) ? $cart_item['origin_time'] : '';
				$bp              = array_key_exists( 'bp', $cart_item ) ? $cart_item['bp'] : '';
				$bp_time         = array_key_exists( 'bp_time', $cart_item ) ? $cart_item['bp_time'] : '';
				$dp              = array_key_exists( 'dp', $cart_item ) ? $cart_item['dp'] : '';
				$dp_time         = array_key_exists( 'dp_time', $cart_item ) ? $cart_item['dp_time'] : '';
				$seat_type       = array_key_exists( 'seat_type', $cart_item ) ? $cart_item['seat_type'] : '';
				$ticket_infos    = array_key_exists( 'ticket_info', $cart_item ) ? $cart_item['ticket_info'] : [];
				$pickup          = array_key_exists( 'pickup', $cart_item ) ? $cart_item['pickup'] : '';
				$drop            = array_key_exists( 'drop', $cart_item ) ? $cart_item['drop'] : '';
				$item_data[]     = array( 'name' => __( 'Booking Details', 'abprf-rental-forge' ), 'value' => '' );
				$item_data[]     = array( 'name' => __( 'Departure', 'abprf-rental-forge' ), 'value' => esc_html( $bp ) );
				$item_data[]     = array( 'name' => __( 'Departure Time', 'abprf-rental-forge' ), 'value' => esc_html( ABPRF_Function::date_format( $bp_time, 'full' ) ) );
				$item_data[]     = array( 'name' => __( 'Arrival', 'abprf-rental-forge' ), 'value' => esc_html( $dp ) );
				$item_data[]     = array( 'name' => __( 'Arrival Time', 'abprf-rental-forge' ), 'value' => esc_html( ABPRF_Function::date_format( $dp_time, 'full' ) ) );
				if ( $bp != $origin ) {
					$item_data[] = array( 'name' => __( 'Starting point', 'abprf-rental-forge' ), 'value' => esc_html( $origin ) );
					$item_data[] = array( 'name' => __( 'Starting Time', 'abprf-rental-forge' ), 'value' => esc_html( ABPRF_Function::date_format( $origin_time, 'full' ) ) );
				}
				if ( $pickup ) {
					$item_data[] = array( 'name' => __( 'Pickup Point', 'abprf-rental-forge' ), 'value' => esc_html( $pickup ) );
				}
				if ( $drop ) {
					$item_data[] = array( 'name' => __( 'Drop-off Point', 'abprf-rental-forge' ), 'value' => esc_html( $drop ) );
				}
				$item_data[] = array( 'name' => __( 'Approximate Time', 'abprf-rental-forge' ), 'value' => esc_html( ABPRF_Function::get_date_time_difference( $bp_time, $dp_time ) ) );
				if ( sizeof( $ticket_infos ) > 0 ) {
					$item_data[] = array( 'name' => __( 'Ticket Details', 'abprf-rental-forge' ), 'value' => '' );
					foreach ( $ticket_infos as $key => $ticket_info ) {
						$seat_text   = $seat_type == 'seat_plan' ? ( __( 'Seat', 'abprf-rental-forge' ) . ' ' . ( $ticket_info['dd'] ? '( ' . __( 'Upper Deck', 'abprf-rental-forge' ) . ' )' : '' ) ) : __( 'Ticket', 'abprf-rental-forge' );
						$item_data[] = array( 'name' => esc_html( $seat_text ), 'value' => esc_html( $ticket_info['seat'] . ' ' . ABPRF_Function::get_seat_type( $ticket_info['type'] ) ) . ' (' . wp_kses_post( wc_price( $ticket_info['price'] ) ) . esc_html( ' X ' ) . esc_html( $ticket_info['qty'] ) . esc_html( ' ) = ' ) . wp_kses_post( wc_price( $ticket_info['price'] * $ticket_info['qty'] ) ) );
						$item_data   = apply_filters( 'abprf_cart_client_info', $item_data, $cart_item, $key );
					}
					if ( sizeof( $additional_info ) > 0 ) {
						$additional_infos = current( $additional_info );
						if ( sizeof( $additional_infos ) > 0 ) {
							foreach ( $additional_infos as $additional ) {
								if ( is_array( $additional ) ) {
									$item_data[] = array( 'name' => esc_html( array_key_exists( 'name', $additional ) && $additional['name'] ? $additional['name'] : '' ), 'value' => wp_kses_post( wc_price( $additional['price'] ) ) . esc_html( ' X ' ) . esc_html( $additional['qty'] ) . esc_html( '  = ' ) . wp_kses_post( wc_price( $additional['price'] * $additional['qty'] ) ) );
								}
							}
						}
					}
					$item_data = apply_filters( 'abprf_cart_client_info', $item_data, $cart_item );
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
					$item_total = 0;
					$bp         = array_key_exists( 'bp', $cart_item ) ? $cart_item['bp'] : '';
					$bp_time    = array_key_exists( 'bp_time', $cart_item ) ? $cart_item['bp_time'] : '';
					$item->add_meta_data( __( 'Departure', 'abprf-rental-forge' ), esc_html( $bp . ' ( ' . ABPRF_Function::date_format( $bp_time, 'full' ) . ' ) ' ) );
					//=============================//
					$dp      = array_key_exists( 'dp', $cart_item ) ? $cart_item['dp'] : '';
					$dp_time = array_key_exists( 'dp_time', $cart_item ) ? $cart_item['dp_time'] : '';
					$item->add_meta_data( __( 'Arrival', 'abprf-rental-forge' ), esc_html( $dp . ' ( ' . ABPRF_Function::date_format( $dp_time, 'full' ) . ' ) ' ) );
					//=============================//
					$origin      = array_key_exists( 'origin', $cart_item ) ? $cart_item['origin'] : '';
					$origin_time = array_key_exists( 'origin_time', $cart_item ) ? $cart_item['origin_time'] : '';
					if ( $bp !== $origin ) {
						$item->add_meta_data( __( 'Starting point', 'abprf-rental-forge' ), esc_html( $origin . ' ( ' . ABPRF_Function::date_format( $origin_time, 'full' ) . ' ) ' ) );
					}
					//=============================//
					$pickup = array_key_exists( 'pickup', $cart_item ) ? $cart_item['pickup'] : '';
					$drop   = array_key_exists( 'drop', $cart_item ) ? $cart_item['drop'] : '';
					if ( $pickup ) {
						$item->add_meta_data( __( 'Pickup Point', 'abprf-rental-forge' ), esc_html( $pickup ) );
					}
					if ( $drop ) {
						$item->add_meta_data( __( 'Drop-off Point', 'abprf-rental-forge' ), esc_html( $drop ) );
					}
					//=============================//
					$seat_type        = array_key_exists( 'seat_type', $cart_item ) ? $cart_item['seat_type'] : '';
					$ticket_infos     = array_key_exists( 'ticket_info', $cart_item ) ? $cart_item['ticket_info'] : [];
					$additional_infos = array_key_exists( 'additional_info', $cart_item ) ? $cart_item['additional_info'] : [];
					$qty              = 0;
					if ( sizeof( $ticket_infos ) > 0 ) {
						foreach ( $ticket_infos as $ticket_info ) {
							$qty = $qty + $ticket_info['qty'];
							if ( $seat_type == 'seat_plan' ) {
								$item->add_meta_data( __( 'Seat', 'abprf-rental-forge' ), ( $ticket_info['dd'] ? __( 'Upper Deck', 'abprf-rental-forge' ) : '' ) . ' ' . esc_html( $ticket_info['seat'] . ' ' . ABPRF_Function::get_seat_type( $ticket_info['type'] ) ) . ' ' . esc_html( ' ( ' ) . wp_kses_post( wc_price( $ticket_info['price'] ) ) . esc_html( ' X ' ) . esc_html( $ticket_info['qty'] ) . esc_html( ') = ' ) . wp_kses_post( wc_price( $ticket_info['price'] * $ticket_info['qty'] ) ) );
							} else {
								$item->add_meta_data( __( 'Ticket', 'abprf-rental-forge' ), esc_html( $ticket_info['seat'] . ' ' . ABPRF_Function::get_seat_type( $ticket_info['type'] ) ) . ' ' . esc_html( ' ( ' ) . wp_kses_post( wc_price( $ticket_info['price'] ) ) . esc_html( ' X ' ) . esc_html( $ticket_info['qty'] ) . esc_html( ') = ' ) . wp_kses_post( wc_price( $ticket_info['price'] * $ticket_info['qty'] ) ) );
							}
							$item_total = $item_total + $ticket_info['price'] * $ticket_info['qty'];
						}
						if ( sizeof( $additional_infos ) > 0 ) {
							foreach ( $additional_infos as $additional ) {
								$name = array_key_exists( 'name', $additional ) && $additional['name'] ? $additional['name'] : '';
								$item->add_meta_data( esc_html( $name ), esc_html( '  ( ' ) . wp_kses_post( wc_price( $additional['price'] ) ) . esc_html( ' X ' ) . esc_html( $additional['qty'] ) . esc_html( ') = ' ) . wp_kses_post( wc_price( $additional['price'] * $additional['qty'] ) ) );
								$item_total = $item_total + $additional['price'] * $additional['qty'];
							}
						}
					}
					//=============================//
					$item_info['post_id']         = $post_id;
					$item_info['user_id']         = get_current_user_id();
					$item_info['origin']          = $origin;
					$item_info['origin_time']     = $origin_time;
					$item_info['bp']              = $bp;
					$item_info['bp_time']         = $bp_time;
					$item_info['dp']              = $dp;
					$item_info['dp_time']         = $dp_time;
					$item_info['pick_up']         = $pickup;
					$item_info['drop_off']        = $drop;
					$item_info['ticket_info']     = $ticket_infos;
					$item_info['additional_info'] = $additional_infos;
					$item_info['qty']             = $qty;
					$item_info['item_total']      = $item_total;
					$item_info                    = apply_filters( 'abprf_checkout_create_order_line_item', $item_info, $cart_item );
					$item->add_meta_data( '_abprf_items', $item_info );
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
								if ( is_array( $item_info ) && sizeof( $item_info ) > 0 ) {
									$post_id = array_key_exists( 'post_id', $item_info ) ? $item_info['post_id'] : 0;
									if ( get_post_type( $post_id ) == ABPRF_Function::get_cpt() ) {
										$ticket_info = array_key_exists( 'ticket_info', $item_info ) ? $item_info['ticket_info'] : [];
										$ticket_type = ABPRF_Function::get_post_info( $post_id, 'seat_type', 'seat_plan' );
										$bp          = array_key_exists( 'bp', $item_info ) ? sanitize_text_field( $item_info['bp'] ) : '';
										$dp          = array_key_exists( 'dp', $item_info ) ? sanitize_text_field( $item_info['dp'] ) : '';
										$origin_date = array_key_exists( 'origin_time', $item_info ) ? sanitize_text_field( $item_info['origin_time'] ) : '';
										$ticket      = [];
										if ( sizeof( $ticket_info ) > 0 ) {
											if ( $ticket_type == 'seat_plan' ) {
												foreach ( $ticket_info as $ticket_info_value ) {
													$ticket[] = array_key_exists( 'seat', $ticket_info_value ) ? $ticket_info_value['seat'] : '';
												}
											} else {
												$seat          = 1;
												$booked_ticked = ABPRF_Query::get_sold_ticket( $post_id, $bp, $dp, $origin_date );
												foreach ( $ticket_info as $ticket_info_value ) {
													$qty = array_key_exists( 'qty', $ticket_info_value ) ? $ticket_info_value['qty'] : 1;
													for ( $i = 0; $i < $qty; $i ++ ) {
														while ( in_array( $seat, $booked_ticked ) ) {
															$seat ++;
														}
														$booked_ticked[] = $seat;
														$ticket[]        = $seat;
													}
												}
											}
											$others = [];
											$data   = [
												'order_id' => intval( $order_id ),
												'item_id' => intval( $item_id ),
												'post_id' => intval( $post_id ),
												'user_id' => intval( $user_id ),
												'origin' => array_key_exists( 'origin', $item_info ) ? sanitize_text_field( $item_info['origin'] ) : '',
												'origin_time' => sanitize_text_field( $origin_date ),
												'bp' => sanitize_text_field( $bp ),
												'bp_time' => array_key_exists( 'bp_time', $item_info ) ? sanitize_text_field( $item_info['bp_time'] ) : '',
												'dp' => sanitize_text_field( $dp ),
												'dp_time' => array_key_exists( 'dp_time', $item_info ) ? sanitize_text_field( $item_info['dp_time'] ) : '',
												'pick_up' => array_key_exists( 'pick_up', $item_info ) ? sanitize_text_field( $item_info['pick_up'] ) : '',
												'drop_off' => array_key_exists( 'drop_off', $item_info ) ? sanitize_text_field( $item_info['drop_off'] ) : '',
												'ticket' => json_encode( $ticket ),
												'ticket_info' => array_key_exists( 'ticket_info', $item_info ) ? json_encode( $item_info['ticket_info'] ) : '',
												'additional_info' => array_key_exists( 'additional_info', $item_info ) ? json_encode( $item_info['additional_info'] ) : '',
												'pass_info' => array_key_exists( 'pass_info', $item_info ) ? json_encode( $item_info['pass_info'] ) : json_encode( [] ),
												'order_status' => sanitize_text_field( 'wc-' . $order_status ),
												'payment_method' => sanitize_text_field( $payment_method ),
												'billing_name' => sanitize_text_field( $billing_name ),
												'billing_email' => sanitize_text_field( $billing_email ),
												'billing_phone' => sanitize_text_field( $billing_phone ),
												'billing_address' => sanitize_text_field( $billing_address ),
												'item_total' => array_key_exists( 'item_total', $item_info ) ? floatval( $item_info['item_total'] ) : '',
												'qty' => array_key_exists( 'qty', $item_info ) ? intval( $item_info['qty'] ) : 1,
												'others' => json_encode( $others ),
												'created_at' => current_time( 'Y-m-d H:i' ),
												'updated_at' => current_time( 'Y-m-d H:i' )
											];
											global $wpdb;
											$table_name = $wpdb->prefix . 'abprf_orders';
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
							$value = ABPRF_Query::get_item_query( $item_id );
							if ( ! empty( $value ) && sizeof( $value ) > 0 ) {
								$others = array_key_exists( 'others', $value ) ? $value['others'] : '';
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