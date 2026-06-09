<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	if ( ! class_exists( 'ABPRF_Orders' ) ) {
		class ABPRF_Orders {
			public function __construct() {
				add_action( 'abprf_load_orders', [ $this, 'load_orders' ] );
				add_action( 'wp_ajax_abprf_load_order_list', [ $this, 'load_order_list' ] );
				add_action( 'wp_ajax_abprf_item_cancel', [ $this, 'item_cancel' ] );
			}

			public function load_orders(): void {
				?>
                <div class="abprf_orders _abp_panel">
                    <div class="_panel_head_ov_auto">
                        <h4 class="_abprf"><span class="_mar_r_xxs">📋</span> <?php esc_html_e( 'Order Filter', 'abp-rentalforge' ); ?></h4>
                    </div>
                    <div class="_panel_body_ov_initial">
                        <form class="load_order_list" method="post" action="">
                            <div class="_form_inline">
								<?php
									ABPRF_Layout::filter_post_list();
									ABPRF_Layout::filter_booking_date_between();
									ABPRF_Layout::filter_booking_date();
									ABPRF_Layout::filter_order_date();
									ABPRF_Layout::filter_user_id();
									ABPRF_Layout::filter_location();
									ABPRF_Layout::filter_order_id();
									ABPRF_Layout::filter_order_date_between();
									ABPRF_Layout::filter_bill_name();
									ABPRF_Layout::filter_bill_email();
									ABPRF_Layout::filter_bill_phone();
								?>
                            </div>
                            <div class="_form_inline_mar_t_xs">
                                <div class="_input_item">
                                    <button type="submit" class="_btn_theme_xs_w_full">
                                        <span class="_mar_r_xs">🔎</span><?php esc_html_e( 'Search', 'abp-rentalforge' ); ?>
                                    </button>
                                </div>
                                <div class="_input_item">
                                    <button class="_btn_theme_xs _w_full" title="<?php esc_attr_e( 'More Options', 'abp-rentalforge' ); ?>" type="button" data-collapse-target="#view_more_filter_option"
                                            data-close-text="👁️ <?php esc_attr_e( 'More Options', 'abp-rentalforge' ); ?>" data-open-text="🙈  <?php esc_attr_e( 'Close Options', 'abp-rentalforge' ); ?>"
                                    >
                                        <span data-text>👁️ <?php esc_html_e( 'More Options', 'abp-rentalforge' ); ?></span>
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

			public function order_lists( $filter_args = [] ): void {
				$page_number           = isset( $filter_args['page_number'] ) && is_numeric( $filter_args['page_number'] ) ? (int) $filter_args['page_number'] : 1;
				$limit                 = isset( $filter_args['page_item'] ) && is_numeric( $filter_args['page_item'] ) ? (int) $filter_args['page_item'] : ABPRF_Function::get_option( 'abprf_per_page_item', 20 );
				$post_id               = isset( $filter_args['post_id'] ) && is_numeric( $filter_args['post_id'] ) ? (int) $filter_args['post_id'] : 0;
				$data_status           = ! empty( $filter_args['status'] ) ? sanitize_text_field( $filter_args['status'] ) : '';
				$si                    = ( $page_number - 1 ) * $limit + 1;
				$offset                = $si - 1;
				$booking_lists         = ABPRF_Query::get_booking_query( $filter_args, $limit, $offset );
				$filter_args['status'] = 'all';
				$total_order           = ABPRF_Query::get_booking_query( $filter_args, 0, 0, true );
				$configuration         = ABPRF_Function::get_option( 'abprf_configuration' );
				$label                 = ABPRF_Function::label();
				$brand_icon            = ABPRF_Function::icon();
				$booked_status         = isset( $configuration['booked_status'] ) && $configuration['booked_status'] ? $configuration['booked_status'] : 'wc-processing,wc-completed';
				$booked_status         = $booked_status ? explode( ',', $booked_status ) : [];
				?>
                <div class="_panel_head_xs_ov_auto">
                    <div class="_group_content order_status_menu">
                        <button class="_btn_white_xs_text_nowrap <?php echo esc_attr( $data_status === 'all' ? 'rf_active' : '' ); ?>" type="button" data-status="all" title="<?php esc_attr_e( 'All Booking', 'abp-rentalforge' ); ?>">
							<?php echo esc_html( __( 'All Booking', 'abp-rentalforge' ) . ' (' . ABPRF_Query::get_booking_query( $filter_args, 0, 0, true ) . ' )' ) ?>
                        </button>
                        <button class="_btn_white_xs_text_nowrap <?php echo esc_attr( ! $data_status ? 'rf_active' : '' ); ?>" type="button" data-status="" title="<?php esc_attr_e( 'Booking Completed', 'abp-rentalforge' ); ?>">
							<?php
								$filter_args['status'] = '';
								echo esc_html( __( 'Booking Completed', 'abp-rentalforge' ) . ' (' . ABPRF_Query::get_booking_query( $filter_args, 0, 0, true ) . ' )' );
							?>
                        </button>
						<?php
							$all_status = wc_get_order_statuses();
							if ( ! empty( $all_status ) && is_array( $all_status ) ) {
								foreach ( $all_status as $key => $status ) {
									?>
                                    <button class="_btn_white_xs_text_nowrap <?php echo esc_attr( $data_status === $key ? 'rf_active' : '' ); ?>" type="button" data-status="<?php echo esc_attr( $key ); ?>">
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
					<?php if ( ! empty( $booking_lists ) && is_array( $booking_lists ) ) { ?>
                        <table class=" _abprf">
                            <thead>
                            <tr>
                                <th><?php esc_html_e( 'Action', 'abp-rentalforge' ); ?></th>
                                <th><?php esc_html_e( 'Order ID/ Date', 'abp-rentalforge' ); ?></th>
								<?php if ( $post_id === 0 ) { ?>
                                    <th><?php ABPRF_Layout::image_icon( $brand_icon, '_mar_r_xs' ); ?><?php echo esc_html( $label ); ?></th>
								<?php } ?>
                                <th class="_min_150"><?php esc_html_e( 'Rent Time', 'abp-rentalforge' ); ?></th>
                                <th><?php esc_html_e( 'Property Information', 'abp-rentalforge' ); ?></th>
                                <th><?php esc_html_e( 'Rent + Additional + Deposit = Total ', 'abp-rentalforge' ); ?></th>
                                <th><?php esc_html_e( 'Status', 'abp-rentalforge' ); ?></th>
                                <th><?php esc_html_e( 'Payment Method', 'abp-rentalforge' ); ?></th>
                                <th><?php esc_html_e( 'Additional services', 'abp-rentalforge' ); ?></th>
                                <th><?php esc_html_e( 'Billing Information', 'abp-rentalforge' ); ?></th>
                                <th><?php esc_html_e( 'Client Information', 'abp-rentalforge' ); ?></th>
                            </tr>
                            </thead>
                            <tbody>
							<?php foreach ( $booking_lists as $booking_list ) {
								$item_id          = $booking_list['item_id'] ?? '';
								$_post_id         = $booking_list['post_id'] ?? '';
								$order_id         = $booking_list['order_id'] ?? '';
								$status           = $booking_list['order_status'] ?? '';
								$order_time       = $booking_list['created_at'] ?? '';
								$price_info       = json_decode( $booking_list['price_info'] ?? '', true ) ?: [];
								$total_price      = $price_info['item_total'] ?? 0;
								$rent             = $price_info['rent'] ?? 0;
								$ex_price         = $price_info['ex_price'] ?? 0;
								$deposit          = $price_info['deposit'] ?? 0;
								$ticket_infos     = json_decode( $booking_list['property_info'] ?? '', true ) ?: [];
								$passenger_infos  = json_decode( $booking_list['pass_info'] ?? '', true ) ?: [];
								$additional_infos = json_decode( $booking_list['ex_info'] ?? '', true ) ?: [];
								$others           = json_decode( $booking_list['others'] ?? '', true ) ?: [];
								$start_time       = $booking_list['start_time'] ?? '';
								$start_date       = ! empty( $start_time ) ? gmdate( 'Y-m-d', strtotime( $start_time ) ) : '';
								$end_time         = $booking_list['end_time'] ?? '';
								$end_date         = ! empty( $end_time ) ? gmdate( 'Y-m-d', strtotime( $end_time ) ) : '';
								$end_time_format  = strtotime( $start_date ) === strtotime( $end_date ) ? 'time' : 'full';
								?>
                                <tr>
                                    <th>
                                        <div class="_group_content">
											<?php do_action('abprf_order_action',$order_id);
                                                if ( in_array( $status, $booked_status, true ) ) { ?>
                                                <button class="_btn_light_danger_xxs abprf_item_cancel" data-item_id="<?php echo esc_attr( $item_id ); ?>" title="<?php esc_attr_e( 'Rent Cancel', 'abp-rentalforge' ); ?>" type="button"><span class="fas fa-times"></span></button>
											<?php } ?>
                                        </div>
                                    </th>
                                    <th class="_text_left">
                                        <p class="_abprf"><?php echo esc_html( $si . '. #' . $order_id ); ?></p>
                                        <p class="_abprf_fs_label_color_theme"><?php echo esc_html( ABPRF_Function::date_format( $order_time ) ); ?></p>
                                    </th>
									<?php if ( $post_id === 0 ) { ?>
                                        <th class="_text_left"><?php ABPRF_Layout::title( $_post_id ); ?></th>
									<?php } ?>
                                    <td>
										<?php echo esc_html( ABPRF_Function::date_format( $start_time, 'full' ) . '-' . ABPRF_Function::date_format( $end_time, $end_time_format ) ); ?>
                                        <p class="_abprf_fs_label_color_theme"><?php echo esc_html( $others['duration'] ?? '' ); ?></p>
                                    </td>
                                    <th>
										<?php if ( ! empty( $ticket_infos ) && is_array( $ticket_infos ) ) {
											foreach ( $ticket_infos as $ticket_info ) {
												if ( ! empty( $ticket_info ) && is_array( $ticket_info ) ) {
													$name  = $ticket_info['name'] ?? '';
													$qty   = $ticket_info['qty'] ?? 1;
													$price = $ticket_info['price'] ?? '';
													if ( ! empty( $name ) ) {
														?><p class="_abprf"><?php echo esc_html( $name . ' X ' . $qty . ' = ' ) . ' ' . ( ! empty( $price ) && $price > 0 ? wp_kses_post( wc_price( $price ) ) : esc_html__( 'FREE', 'abp-rentalforge' ) ); ?></p><?php
													}
												}
											}
										} ?>
                                    </th>
                                    <th>
                                        <span><?php echo ( ! empty( $rent ) && $rent > 0 ) ? wp_kses_post( wc_price( $rent ) ) : esc_html__( 'FREE', 'abp-rentalforge' ); ?></span>+
                                        <span><?php echo ( ! empty( $ex_price ) && $ex_price > 0 ) ? wp_kses_post( wc_price( $ex_price ) ) : esc_html__( 'FREE', 'abp-rentalforge' ); ?></span>+
                                        <span><?php echo ( ! empty( $deposit ) && $deposit > 0 ) ? wp_kses_post( wc_price( $deposit ) ) : esc_html__( 'FREE', 'abp-rentalforge' ); ?></span>=
                                        <span><?php echo ( ! empty( $total_price ) && $total_price > 0 ) ? wp_kses_post( wc_price( $total_price ) ) : esc_html__( 'FREE', 'abp-rentalforge' ); ?></span>
                                    </th>
                                    <th class="_text_capitalize">
                                        <p class="_abprf <?php echo esc_attr( ABPRF_Function::status_text( $status ) ); ?>"> <?php echo esc_html( ABPRF_Function::status_text( $status ) ); ?></p>
                                        <p class="_abprf <?php echo esc_attr( $booking_list['book_status'] ?? '' ); ?>"><?php echo esc_html( $booking_list['book_status'] ?? '' ); ?></p>
                                    </th>
                                    <th class="_text_capitalize"><?php echo esc_html( $booking_list['payment_method'] ?? '' ); ?></th>
                                    <td>
										<?php if ( ! empty( $additional_infos ) && is_array( $additional_infos ) ) {
											foreach ( $additional_infos as $ex_info ) {
												if ( ! empty( $ex_info ) && is_array( $ex_info ) ) {
													$name       = $ex_info['name'] ?? '';
													$qty        = $ex_info['qty'] ?? 1;
													$price      = $ex_info['price'] ?? '';
													$returnable = $ex_info['returnable'] ?? 'no';
													if ( ! empty( $name ) ) { ?>
                                                        <p class="_abprf">
															<?php echo esc_html( $name . ' X ' . $qty . ' = ' ) . ' ' . ( ! empty( $price ) && $price > 0 ? wp_kses_post( wc_price( $price ) ) : esc_html__( 'FREE', 'abp-rentalforge' ) );
																if ( $returnable === 'yes' ) {
																	?> <span class="trash"><?php esc_html_e( 'Returnable', 'abp-rentalforge' ); ?></span><?php
																} else {
																	?><span class="publish"><?php esc_html_e( 'Non-Returnable', 'abp-rentalforge' ); ?></span>  <?php
																} ?>
                                                        </p>
														<?php
													}
												}
											}
										} ?>
                                    </td>
                                    <td>
                                        <p class="_abprf"><?php echo esc_html( $booking_list['billing_name'] ?? '' ); ?></p>
                                        <p class="_abprf"><?php echo esc_html( $booking_list['billing_email'] ?? '' ); ?></p>
                                        <p class="_abprf" data-collapse="#bill_<?php echo esc_attr( $order_id . '_' . $item_id ); ?>"><?php echo esc_html( $booking_list['billing_phone'] ?? '' ); ?></p>
                                        <p class="_abprf" data-collapse="#bill_<?php echo esc_attr( $order_id . '_' . $item_id ); ?>"><?php echo esc_html( $booking_list['billing_address'] ?? '' ); ?></p>
                                        <p class="_abprf_fs_label_color_theme" data-collapse-target="#bill_<?php echo esc_attr( $order_id . '_' . $item_id ); ?>" data-close-text="<?php esc_attr_e( 'Less More', 'abp-rentalforge' ); ?>" data-open-text="<?php esc_attr_e( 'Load More', 'abp-rentalforge' ); ?>">
                                            <span data-text><?php esc_html_e( 'Load More', 'abp-rentalforge' ); ?></span>
                                        </p>
                                    </td>
                                    <td>
										<?php if ( ! empty( $passenger_infos ) && is_array( $passenger_infos ) ) {
											$pass_count = 0;
											foreach ( $passenger_infos as $pas_form ) {
												if ( ! empty( $pas_form ) && is_array( $pas_form ) ) {
													$p_label = $pas_form['label'] ?? '';
													$p_value = $pas_form['value'] ?? '';
													if ( ! empty( $p_label ) && ! empty( $p_value ) ) {
														$pass_count ++;
														if ( $pass_count > 2 ) {
															?><p class="_abprf" data-collapse="#pass_<?php echo esc_attr( $order_id . '_' . $item_id ); ?>"><?php echo esc_html( $p_label . ' : ' . $p_value ); ?></p><?php
														} else {
															?><p class="_abprf"><?php echo esc_html( $p_label . ' : ' . $p_value ); ?></p><?php
														}
													}
												}
											}
											if ( $pass_count > 2 ) {
												?>
                                                <p class="_abprf_fs_label_color_theme" data-collapse-target="#pass_<?php echo esc_attr( $order_id . '_' . $item_id ); ?>" data-close-text="<?php esc_attr_e( 'Less More', 'abp-rentalforge' ); ?>" data-open-text="<?php esc_attr_e( 'Load More', 'abp-rentalforge' ); ?>">
                                                    <span data-text><?php esc_html_e( 'Load More', 'abp-rentalforge' ); ?></span>
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
				<?php
			}

			public function load_order_list(): void {
				if ( ! check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce', false ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Invalid security token.', 'abp-rentalforge' ) ], 403 );
				}
				if ( ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Insufficient permissions.', 'abp-rentalforge' ) ], 403);
				}
				ob_start();
				$filter_args              = isset( $_POST ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST ) ) : [];
				$limit                    = isset( $filter_args['page_item'] ) ? (int) $filter_args['page_item'] : 20;
				$data_limit               = (int) ABPRF_Function::get_option( 'abprf_per_page_item', 20 );
				$filter_args['page_item'] = $limit > 0 ? $limit : $data_limit;
				if ( $limit > 0 && $data_limit !== $limit ) {
					update_option( 'abprf_per_page_item', $limit );
				}
				$this->order_lists( $filter_args );
				$html = ob_get_clean();
				wp_send_json_success( [ 'html' => $html, 'msg' => esc_html__( 'Order Loaded Successfully !', 'abp-rentalforge' ) ] );
			}

			public function item_cancel(): void {
				if ( ! check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce', false ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Invalid security token.', 'abp-rentalforge' ) ], 403 );
				}
				if ( ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Insufficient permissions.', 'abp-rentalforge' ) ], 403);
				}
				$item_id = isset( $_POST['item_id'] ) ? sanitize_text_field( wp_unslash( $_POST['item_id'] ) ) : '';
				if ( ! empty( $item_id ) ) {
					global $wpdb;
					$table_name = $wpdb->prefix . 'abprf_orders';
					$value      = ABPRF_Query::get_item_query( $item_id );
					if ( ! empty( $value ) && is_array( $value ) ) {
						$others = $value['others'] ?? '';
						if ( ! empty( $others ) ) {
							$others              = json_decode( $others, true ) ?: [];
							$user_id             = get_current_user_id();
							$others['cancel_by'] = $user_id;
							$data                = [
								'others' => json_encode( $others ),
								'order_status' => 'wc-cancelled',
								'updated_at' => current_time( 'Y-m-d H:i:s' )
							];
							$where               = [ 'item_id' => (int) $item_id ];
							// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
							$wpdb->update( $table_name, $data, $where, [ '%s', '%s', '%s' ], [ '%d' ] );
						}
					}
					wp_send_json_success( ['html' => '', 'msg' => esc_html__( 'Deleted Successfully !', 'abp-rentalforge' ) ] );
				}
				wp_send_json_error( ['html' => '', 'msg' => esc_html__( 'Something Error Occurred !', 'abp-rentalforge' ) ] );
			}
		}
		new ABPRF_Orders();
	}