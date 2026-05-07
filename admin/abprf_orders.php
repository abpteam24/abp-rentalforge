<?php
	if ( ! defined( 'ABSPATH' ) ) {
		die;
	} // Cannot access pages directly.
	if ( ! class_exists( 'ABPRF_Orders' ) ) {
		class ABPRF_Orders {
			public function __construct() {
				add_action( 'abprf_load_orders', array( $this, 'load_orders' ) );
				add_action( 'wp_ajax_abprf_load_order_list', array( $this, 'load_order_list' ) );
				add_action( 'wp_ajax_abprf_item_cancel', [ $this, 'item_cancel' ] );
			}

			public function load_orders( $abprf_info ): void {
				?>
                <div class="abprf_orders">
                    <div class="_section_xs ">
                        <h4 class="_abprf_color_theme"><span class="_mar_r_xxs">📋</span> <?php esc_html_e( 'Order Filter', 'abprf-rental-forge' ); ?></h4>
                        <form class="load_order_list" method="post" action="">
                            <div class="_form_inline">
								<?php
									ABPRF_Layout::filter_post_list($abprf_info);
									ABPRF_Layout::filter_booking_date();
									ABPRF_Layout::filter_order_date();
									ABPRF_Layout::filter_booking_date_between();
									ABPRF_Layout::filter_user_id();
									ABPRF_Layout::filter_order_id();
									ABPRF_Layout::filter_order_date_between();
									ABPRF_Layout::filter_bill_name();
									ABPRF_Layout::filter_bill_email();
									ABPRF_Layout::filter_bill_phone();
								?>
                            </div>
                            <div class="_form_inline">
                                <div class="_input_item">
                                    <button type="submit" class="_btn_theme_xs_w_full">
                                        <span class="_mar_r_xs">🔎</span><?php esc_html_e( 'Search', 'abprf-rental-forge' ); ?>
                                    </button>
                                </div>
                                <div class="_input_item">
                                    <button class="_btn_theme_xs _w_full" title="<?php esc_attr_e( 'More Options', 'abprf-rental-forge' ); ?>" type="button" data-collapse-target="#view_more_filter_option"
                                            data-close-text="👁️ <?php esc_attr_e( 'More Options', 'abprf-rental-forge' ); ?>" data-open-text="🙈  <?php esc_attr_e( 'Close Options', 'abprf-rental-forge' ); ?>"
                                    >
                                        <span data-text>👁️ <?php esc_html_e( 'More Options', 'abprf-rental-forge' ); ?></span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="order_list">
						<?php $this->order_lists(); ?>
                    </div>
                </div>
				<?php
			}

			public function load_order_list() {
				if ( is_admin() && check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce' ) && current_user_can( 'manage_options' ) ) {
					ob_start();
					$filter_args              = isset( $_POST ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST ) ) : array();
					$limit                    = array_key_exists( 'page_item', $filter_args ) ? $filter_args['page_item'] : 20;
					$data_limit               = ABPRF_Function::get_option( 'abprf_per_page_item', 20 );
					$filter_args['page_item'] = ! empty( $limit ) ? (int) $limit : $data_limit;
					if ( $limit > 0 && $data_limit !== $limit ) {
						update_option( 'abprf_per_page_item', $limit );
					}
					//echo '<pre>';				print_r( $_POST );				echo '</pre>';
					$this->order_lists( $filter_args );
					$html = ob_get_clean();
					wp_send_json_success( [ 'html' => $html, 'msg' => esc_html__( 'Order Loaded Successfully............. ! ', 'abprf-rental-forge' ) ] );
				} else {
					wp_send_json_success( [ 'html' => esc_html__( 'Something Error Occur !', 'abprf-rental-forge' ), 'msg' => esc_html__( 'Something Error Occur !', 'abprf-rental-forge' ) ] );
				}
				wp_die();
			}

			public function item_cancel() {
				if ( is_admin() && check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce' ) && current_user_can( 'manage_options' ) ) {
					$item_id = isset( $_POST['item_id'] ) ? sanitize_text_field( wp_unslash( $_POST['item_id'] ) ) : '';
					if ( $item_id ) {
						global $wpdb;
						$table_name = $wpdb->prefix . 'abprf_orders';
						$value      = ABPRF_Query::get_item_query( $item_id );
						if ( ! empty( $value ) && sizeof( $value ) > 0 ) {
							$others = array_key_exists( 'others', $value ) ? $value['others'] : '';
							if ( ! empty( $others ) ) {
								$others              = json_decode( $others, true );
								$user_id             = get_current_user_id();
								$others['cancel_by'] = $user_id;
								$data                = [
									'others' => json_encode( $others ),
									'order_status' => 'wc-cancelled',
									'updated_at' => current_time( 'Y-m-d H:i' )
								];
								$where               = [ 'item_id' => $item_id ];
								// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
								$wpdb->update( $table_name, $data, $where, [ '%s', '%s', '%s' ], [ '%d' ] );
							}
						}
					}
					wp_send_json_success( [ 'msg' => esc_html__( 'Deleted Successfully............. ! ', 'abprf-rental-forge' ) ] );
				} else {
					wp_send_json_success( [ 'msg' => esc_html__( 'Something Error Occur !', 'abprf-rental-forge' ) ] );
				}
				wp_die();
			}

			public function order_lists( $filter_args = [] ): void {
				$page_number           = array_key_exists( 'page_number', $filter_args ) && is_numeric( $filter_args['page_number'] ) ? (int) $filter_args['page_number'] : 1;
				$limit                 = array_key_exists( 'page_item', $filter_args ) && is_numeric( $filter_args['page_item'] ) ? (int) $filter_args['page_item'] : ABPRF_Function::get_option( 'abprf_per_page_item', 20 );
				$post_id               = array_key_exists( 'post_id', $filter_args ) && ! empty( $filter_args['post_id'] ) ? $filter_args['post_id'] : 0;
				$post_id               = is_numeric( $post_id ) ? $post_id : 0;
				$data_status           = array_key_exists( 'status', $filter_args ) && ! empty( $filter_args['status'] ) ? $filter_args['status'] : '';
				$si                    = ( $page_number - 1 ) * $limit + 1;
				$offset                = $si - 1;
				$booking_lists         = ABPRF_Query::get_booking_query( $filter_args, $limit, $offset );
				$filter_args['status'] = 'all';
				$total_order           = ABPRF_Query::get_booking_query( $filter_args, 0, 0, true );
				//=============================//
				$configuration = ABPRF_Function::get_option( 'abprf_configuration' );
				$label         = isset( $configuration['label'] ) && $configuration['label'] ? $configuration['label'] : __( 'RentalForge', 'abprf-rental-forge' );
				$brand_icon    = isset( $configuration['brand_icon'] ) && $configuration['brand_icon'] ? $configuration['brand_icon'] : 'fas fa-hammer';
				$booked_status = isset( $configuration['booked_status'] ) && $configuration['booked_status'] ? $configuration['booked_status'] : 'wc-processing,wc-completed';
				$status_array  = wc_get_order_statuses();
				$booked_status = $booked_status ? explode( ',', $booked_status ) : [];
				?>
                <div class="_reflex_5 _abp_panel_xs">
                    <div class="_panel_head_bg_theme_ov_auto">
                        <div class="_group_content order_status_menu">
                            <button class="_btn_theme_xs_text_nowrap <?php echo esc_attr( $data_status == 'all' ? 'rf_active' : '' ); ?>" type="button" data-status="all" title="<?php esc_attr_e( 'All Booking', 'abprf-rental-forge' ); ?>">
								<?php echo esc_html( __( 'All Booking', 'abprf-rental-forge' ) . ' (' . ABPRF_Query::get_booking_query( $filter_args, 0, 0, true ) . ' )' ) ?>
                            </button>
                            <button class="_btn_theme_xs_text_nowrap <?php echo esc_attr( ! $data_status ? 'rf_active' : '' ); ?>" type="button" data-status="" title="<?php esc_attr_e( 'Booking Completed', 'abprf-rental-forge' ); ?>">
								<?php
									$filter_args['status'] = '';
									echo esc_html( __( 'Booking Completed', 'abprf-rental-forge' ) . ' (' . ABPRF_Query::get_booking_query( $filter_args, 0, 0, true ) . ' )' );
								?>
                            </button>
							<?php
								$all_status = wc_get_order_statuses();
								if ( sizeof( $all_status ) > 0 ) {
									foreach ( $all_status as $key => $status ) {
										?>
                                        <button class="_btn_theme_xs_text_nowrap <?php echo esc_attr( $data_status == $key ? 'rf_active' : '' ); ?>" type="button" data-status="<?php echo esc_attr( $key ); ?>">
											<?php
												$filter_args['status'] = sanitize_key( $key );
												echo esc_html( $status . ' (' . ABPRF_Query::get_booking_query( $filter_args, 0, 0, true ) . ')' );
											?>
                                        </button>
										<?php
									}
								}
							?>
                        </div>
                    </div>
                    <div class="_panel_body">
						<?php if ( sizeof( $booking_lists ) > 0 ) { ?>
                            <table class=" _abprf">
                                <thead>
                                <tr class="_bg_light_1">
                                    <th><?php esc_html_e( 'Action', 'abprf-rental-forge' ); ?></th>
                                    <th><?php esc_html_e( 'Order ID/ Date', 'abprf-rental-forge' ); ?></th>
									<?php if ( $post_id == 0 ) { ?>
                                        <th><?php ABPRF_Layout::image_icon( $brand_icon, '_mar_r_xs' ); ?><?php echo esc_html( $label ); ?></th>
									<?php } ?>
                                    <th><?php esc_html_e( 'Rent Time', 'abprf-rental-forge' ); ?></th>
                                    <th><?php esc_html_e( 'Property Information', 'abprf-rental-forge' ); ?></th>
                                    <th><?php esc_html_e( 'Rent + Additional + Deposit =Total ', 'abprf-rental-forge' ); ?></th>
                                    <th><?php esc_html_e( 'Status', 'abprf-rental-forge' ); ?></th>
                                    <th><?php esc_html_e( 'Payment Method', 'abprf-rental-forge' ); ?></th>
                                    <th><?php esc_html_e( 'Additional services', 'abprf-rental-forge' ); ?></th>
                                    <th><?php esc_html_e( 'Billing Information', 'abprf-rental-forge' ); ?></th>
                                    <th><?php esc_html_e( 'Client Information', 'abprf-rental-forge' ); ?></th>
                                </tr>
                                </thead>
                                <tbody>
								<?php foreach ( $booking_lists as $booking_list ) {
									$item_id          = array_key_exists( 'item_id', $booking_list ) ? $booking_list['item_id'] : '';
									$_post_id         = array_key_exists( 'post_id', $booking_list ) ? $booking_list['post_id'] : '';
									$order_id         = array_key_exists( 'order_id', $booking_list ) ? $booking_list['order_id'] : '';
									$status           = array_key_exists( 'order_status', $booking_list ) ? $booking_list['order_status'] : '';
									$order_time       = array_key_exists( 'created_at', $booking_list ) ? $booking_list['created_at'] : '';
									$price_info       = array_key_exists( 'price_info', $booking_list ) ? $booking_list['price_info'] : '';
									$price_info       = ! empty( $price_info ) ? json_decode( $price_info, true ) : [];
									$total_price      = array_key_exists( 'item_total', $price_info ) ? $price_info['item_total'] : 0;
									$rent             = array_key_exists( 'rent', $price_info ) ? $price_info['rent'] : 0;
									$ex_price         = array_key_exists( 'ex_price', $price_info ) ? $price_info['ex_price'] : 0;
									$deposit          = array_key_exists( 'deposit', $price_info ) ? $price_info['deposit'] : 0;
									$ticket_infos     = array_key_exists( 'property_info', $booking_list ) ? $booking_list['property_info'] : '';
									$ticket_infos     = ! empty( $ticket_infos ) ? json_decode( $ticket_infos, true ) : [];
									$passenger_infos  = array_key_exists( 'pass_info', $booking_list ) ? $booking_list['pass_info'] : '';
									$passenger_infos  = ! empty( $passenger_infos ) ? json_decode( $passenger_infos, true ) : [];
									$additional_infos = array_key_exists( 'ex_info', $booking_list ) ? $booking_list['ex_info'] : '';
									$additional_infos = ! empty( $additional_infos ) ? json_decode( $additional_infos, true ) : [];
									$others           = array_key_exists( 'others', $booking_list ) ? $booking_list['others'] : '';
									$others           = ! empty( $others ) ? json_decode( $others, true ) : [];
									$start_time       = array_key_exists( 'start_time', $booking_list ) ? $booking_list['start_time'] : '';
									$start_date       = ! empty( $start_time ) ? gmdate( 'Y-m-d', strtotime( $start_time ) ) : '';
									$end_time         = array_key_exists( 'end_time', $booking_list ) ? $booking_list['end_time'] : '';
									$end_date         = ! empty( $end_time ) ? gmdate( 'Y-m-d', strtotime( $end_time ) ) : '';
									$end_time_format  = strtotime( $start_date ) == strtotime( $end_date ) ? 'time' : 'full';
									$row_class        = $si % 2 < 1 ? '_bg_light_1' : '_bg_light_2';
									?>
                                    <tr class="<?php echo esc_attr( $row_class ); ?>">
                                        <th>
                                            <div class="_group_content">
												<?php if ( in_array( $status, $booked_status ) ) { ?>
                                                    <button class="_btn_light_danger_xxs abprf_item_cancel" data-item_id="<?php echo esc_attr( $item_id ); ?>" title="<?php esc_attr_e( 'Rent Cancel', 'abprf-rental-forge' ); ?>" type="button"><span class="fas fa-times"></span></button>
												<?php } ?>
                                            </div>
                                        </th>
                                        <th class="_text_left">
                                            <p class="_abprf"><?php echo esc_html( $si . '. #' . $order_id ); ?></p>
                                            <p class="_abprf_fs_label_color_theme"><?php echo esc_html( ABPRF_Function::date_format( $order_time, 'full' ) ); ?></p>
                                        </th>
										<?php if ( $post_id == 0 ) { ?>
                                            <th class="_text_left"><?php ABPRF_Layout::title( $_post_id ); ?></th>
										<?php } ?>
                                        <td>
											<?php echo esc_html( ABPRF_Function::date_format( $start_time, 'full' ) . '-' . ABPRF_Function::date_format( $end_time, $end_time_format ) ); ?>
                                            <p class="_abprf_fs_label_color_theme"><?php echo esc_html( array_key_exists( 'duration', $others ) ? $others['duration'] : '' ); ?></p>
                                        </td>
                                        <th>
											<?php if ( ! empty( $ticket_infos ) && sizeof( $ticket_infos ) > 0 ) {
												foreach ( $ticket_infos as $ticket_info ) {
													if ( ! empty( $ticket_info ) && sizeof( $ticket_info ) > 0 ) {
														$name  = array_key_exists( 'name', $ticket_info ) ? $ticket_info['name'] : '';
														$qty   = array_key_exists( 'qty', $ticket_info ) ? $ticket_info['qty'] : 1;
														$price = array_key_exists( 'price', $ticket_info ) ? $ticket_info['price'] : '';
														if ( ! empty( $name ) ) {
															?><p class="_abprf"><?php echo esc_html( $name . ' X ' . $qty . ' = ' ) . ' ' . ( ! empty( $price ) && $price > 0 ? wp_kses_post( wc_price( $price ) ) : esc_html__( 'FREE', 'abprf-rental-forge' ) ); ?></p><?php
														}
													}
												}
											} ?>
                                        </th>
                                        <th>
                                            <span><?php echo ( ! empty( $rent ) && $rent > 0 ) ? wp_kses_post( wc_price( $rent ) ) : esc_html__( 'FREE', 'abprf-rental-forge' ); ?></span>+
                                            <span><?php echo ( ! empty( $ex_price ) && $ex_price > 0 ) ? wp_kses_post( wc_price( $ex_price ) ) : esc_html__( 'FREE', 'abprf-rental-forge' ); ?></span>+
                                            <span><?php echo ( ! empty( $deposit ) && $deposit > 0 ) ? wp_kses_post( wc_price( $deposit ) ) : esc_html__( 'FREE', 'abprf-rental-forge' ); ?></span>=
                                            <span><?php echo ( ! empty( $total_price ) && $total_price > 0 ) ? wp_kses_post( wc_price( $total_price ) ) : esc_html__( 'FREE', 'abprf-rental-forge' ); ?></span>
                                        </th>
                                        <th class="_text_capitalize">
                                            <span class="<?php echo esc_attr( array_key_exists( $status, $status_array ) ? $status_array[ $status ] : '' ); ?>"> <?php echo esc_html( array_key_exists( $status, $status_array ) ? $status_array[ $status ] : '' ); ?></span>/
                                            <span class="<?php echo esc_attr( array_key_exists( 'book_status', $booking_list ) ? $booking_list['book_status'] : '' ); ?>"><?php echo esc_html( array_key_exists( 'book_status', $booking_list ) ? $booking_list['book_status'] : '' ); ?></span>
                                        </th>
                                        <th class="_text_capitalize"><?php echo esc_html( array_key_exists( 'payment_method', $booking_list ) ? $booking_list['payment_method'] : '' ); ?></th>
                                        <td>
											<?php if ( ! empty( $additional_infos ) && sizeof( $additional_infos ) > 0 ) {
												foreach ( $additional_infos as $ex_info ) {
													if ( ! empty( $ex_info ) && sizeof( $ex_info ) > 0 ) {
														$name       = array_key_exists( 'name', $ex_info ) ? $ex_info['name'] : '';
														$qty        = array_key_exists( 'qty', $ex_info ) ? $ex_info['qty'] : 1;
														$price      = array_key_exists( 'price', $ex_info ) ? $ex_info['price'] : '';
														$returnable = array_key_exists( 'returnable', $ex_info ) ? $ex_info['returnable'] : 'no';
														if ( ! empty( $name ) ) { ?>
                                                            <p class="_abprf">
																<?php echo esc_html( $name . ' X ' . $qty . ' = ' ) . ' ' . ( ! empty( $price ) && $price > 0 ? wp_kses_post( wc_price( $price ) ) : esc_html__( 'FREE', 'abprf-rental-forge' ) );
																	if ( $returnable == 'yes' ) {
																		?> <span class="trash"><?php esc_html_e( 'Returnable', 'abprf-rental-forge' ); ?></span><?php
																	} else {
																		?><span class="publish"><?php esc_html_e( 'Nor-Returnable', 'abprf-rental-forge' ); ?></span>  <?php
																	} ?>
                                                            </p>
															<?php
														}
													}
												}
											} ?>
                                        </td>
                                        <td>
                                            <p class="_abprf"><?php echo esc_html( array_key_exists( 'billing_name', $booking_list ) ? $booking_list['billing_name'] : '' ); ?></p>
                                            <p class="_abprf"><?php echo esc_html( array_key_exists( 'billing_email', $booking_list ) ? $booking_list['billing_email'] : '' ); ?></p>
                                            <p class="_abprf" data-collapse="#bill_<?php echo esc_attr( $order_id . '_' . $item_id ); ?>"><?php echo esc_html( array_key_exists( 'billing_phone', $booking_list ) ? $booking_list['billing_phone'] : '' ); ?></p>
                                            <p class="_abprf" data-collapse="#bill_<?php echo esc_attr( $order_id . '_' . $item_id ); ?>"><?php echo esc_html( array_key_exists( 'billing_address', $booking_list ) ? $booking_list['billing_address'] : '' ); ?></p>
                                            <p class="_abprf_fs_label_color_theme" data-collapse-target="#bill_<?php echo esc_attr( $order_id . '_' . $item_id ); ?>" data-close-text="<?php esc_attr_e( 'Less More', 'abprf-rental-forge' ); ?>" data-open-text="<?php esc_attr_e( 'Load More', 'abprf-rental-forge' ); ?>">
                                                <span data-text><?php esc_html_e( 'Load More', 'abprf-rental-forge' ); ?></span>
                                            </p>
                                        </td>
                                        <td>
											<?php if ( ! empty( $passenger_infos ) && sizeof( $passenger_infos ) > 0 ) {
												$pass_count = 0;
												foreach ( $passenger_infos as $pas_form ) {
													if ( ! empty( $pas_form ) && sizeof( $pas_form ) > 0 ) {
														$label = array_key_exists( 'label', $pas_form ) ? $pas_form['label'] : '';
														$value = array_key_exists( 'value', $pas_form ) ? $pas_form['value'] : '';
														if ( ! empty( $label ) && ! empty( $value ) ) {
															$pass_count ++;
															if ( $pass_count > 2 ) {
																?><p class="_abprf" data-collapse="#pass_<?php echo esc_attr( $order_id . '_' . $item_id ); ?>"><?php echo esc_html( $label . ' : ' . $value ); ?></p><?php
															} else {
																?><p class="_abprf"><?php echo esc_html( $label . ' : ' . $value ); ?></p><?php
															}
														}
													}
												}
												if ( $pass_count > 2 ) {
													?>
                                                    <p class="_abprf_fs_label_color_theme" data-collapse-target="#pass_<?php echo esc_attr( $order_id . '_' . $item_id ); ?>" data-close-text="<?php esc_attr_e( 'Less More', 'abprf-rental-forge' ); ?>" data-open-text="<?php esc_attr_e( 'Load More', 'abprf-rental-forge' ); ?>">
                                                        <span data-text><?php esc_html_e( 'Load More', 'abprf-rental-forge' ); ?></span>
                                                    </p>
													<?php
												}
											} ?>
                                        </td>
                                    </tr>
									<?php $si ++;
								} ?>
                                </tbody>
                            </table>
						<?php } else {
							ABPRF_Layout::layout_warning_info( 'no_order_found' );
						}
							do_action( 'abprf_pagination', [ 'page_item' => $limit, 'page_number' => $page_number, 'total' => $total_order, 'style' => 'ajax' ] ); ?>
                    </div>
                </div>
				<?php
				//echo '<pre>';				print_r( $booking_lists );				echo '</pre>';
			}
		}
		new ABPRF_Orders();
	}