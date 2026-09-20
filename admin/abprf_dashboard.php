<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	if ( ! class_exists( 'ABPRF_Dashboard' ) ) {
		class ABPRF_Dashboard {
			public function __construct() {
				add_action( 'abprf_load_dashboard', array( $this, 'load_dashboard' ) );
				add_action( 'wp_ajax_abprf_dashboard_booking_popup', array( $this, 'booking_popup' ) );
				//=============================//
				add_action( 'wp_ajax_abprf_dashboard_book_status', array( $this, 'update_book_status' ) );
				//=============================//
				add_action( 'wp_ajax_abprf_wc_config', array( $this, 'wc_config' ) );
				//=============================//
				add_action( 'wp_ajax_abprf_create_page', array( $this, 'create_page' ) );
				//=============================//
				add_action( 'wp_ajax_abprf_import_dummy', array( $this, 'import_dummy' ) );
			}
			public function load_dashboard( $abprf_info ): void {
				$label = ABPRF_Function::label();
				$kpi   = self::kpi_data();
				?>
                <div class="abprf_dashboard">
					<?php $this->hero( $label ); ?>
					<?php $this->kpi_cards( $abprf_info, $kpi ); ?>
                    <div class="dash_columns">
                        <div class="dash_main">
							<?php $this->today_deliveries(); ?>
							<?php $this->today_collections(); ?>
							<?php $this->orders_summary(); ?>
                        </div>
                        <aside class="dash_side">
                            <?php do_action('abprf_dashboard_sidebar'); ?>
							<?php $this->quick_actions(); ?>
							<?php $this->system_status( $abprf_info, $label ); ?>
							<?php $this->content_breakdown( $abprf_info ); ?>
                        </aside>
                    </div>
                </div>
				<?php
			}
			//=============================//
			private function hero( $label ): void {
				$user = wp_get_current_user();
				$name = $user && ! empty( $user->display_name ) ? $user->display_name : __( 'Admin', 'abp-rentalforge' );
				?>
                <div class="dash_hero">
                    <div class="dash_hero_main">
                        <div class="dash_hero_text">
                            <span class="dash_hero_date"><i class="far fa-calendar-alt"></i> <?php echo esc_html( ABPRF_Function::date_format( current_time( 'Y-m-d' ) ) ); ?></span>
                            <h2>
								<?php
									/* translators: %s: current user display name. */
									printf( esc_html__( 'Welcome back, %s!', 'abp-rentalforge' ), esc_html( $name ) );
								?>
                            </h2>
                            <p>
								<?php
									/* translators: %s: plugin label. */
									printf( esc_html__( 'Here is what is happening with your %s business today.', 'abp-rentalforge' ), esc_html( $label ) );
								?>
                            </p>
                        </div>
                    </div>
                    <div class="dash_hero_side">
                        <div class="dash_hero_actions">
                            <a class="dash_btn dash_btn_solid" href="<?php echo esc_url( admin_url( 'post-new.php?post_type=' . ABPRF_Function::get_cpt() ) ); ?>">
                                <i class="fas fa-plus"></i> <?php echo esc_html( $label ); ?>
                            </a>
                            <a class="dash_btn dash_btn_ghost" href="<?php echo esc_url( ABPRF_Function::build_url( 'orders' ) ); ?>">
                                <i class="fas fa-file-invoice"></i> <?php esc_html_e( 'Orders', 'abp-rentalforge' ); ?>
                            </a>
                        </div>
                    </div>
                </div>
				<?php
			}
			private function kpi_cards( $abprf_info, $kpi ): void {
				$currency = function_exists( 'get_woocommerce_currency_symbol' ) ? get_woocommerce_currency_symbol() : '';
				$revenue  = $currency . number_format_i18n( (float) ( $kpi['revenue'] ?? 0 ), 2 );
				$cards    = array(
					array( 'theme', 'fas fa-hammer', (string) ( $abprf_info['total_post'] ?? 0 ), __( 'Total Posts', 'abp-rentalforge' ), ABPRF_Function::build_url( 'posts' ) ),
					array( 'navy', 'fas fa-file-invoice', (string) ( $abprf_info['total_order'] ?? 0 ), __( 'Total Orders', 'abp-rentalforge' ), ABPRF_Function::build_url( 'orders' ) ),
					array( 'success', 'fas fa-cubes', (string) ( $kpi['rented'] ?? 0 ), __( 'Items Rented', 'abp-rentalforge' ), ABPRF_Function::build_url( 'orders' ) ),
					array( 'purple', 'fas fa-money-bill-wave', $revenue, __( 'Total Revenue', 'abp-rentalforge' ), ABPRF_Function::build_url( 'orders' ) ),
					array( 'warning', 'fas fa-calendar-day', (string) ( $kpi['today'] ?? 0 ), __( 'Booked Today', 'abp-rentalforge' ), ABPRF_Function::build_url( 'orders' ) ),
				);
				?>
                <div class="dash_kpi_grid">
					<?php foreach ( $cards as $card ) { ?>
                        <a class="dash_kpi" href="<?php echo esc_url( $card[4] ); ?>">
                            <span class="dash_kpi_icon <?php echo esc_attr( $card[0] ); ?>"><i class="<?php echo esc_attr( $card[1] ); ?>"></i></span>
                            <span class="dash_kpi_body">
                                <span class="dash_kpi_value"><?php echo esc_html( $card[2] ); ?></span>
                                <span class="dash_kpi_label"><?php echo esc_html( $card[3] ); ?></span>
                            </span>
                        </a>
					<?php } ?>
                </div>
				<?php
			}
			private function today_deliveries(): void {
				$today    = current_time( 'Y-m-d' );
				$bookings = ABPRF_Query::get_booking_query( array( 'booking_time_from' => $today, 'booking_time_to' => $today, 'order_dir' => 'ASC' ) );
				$by_post  = array();
				if ( is_array( $bookings ) ) {
					foreach ( $bookings as $booking ) {
						$pid = (int) ( $booking['post_id'] ?? 0 );
						if ( $pid > 0 ) {
							$by_post[ $pid ] = $pid;
						}
					}
				}
				$rows = array();
				foreach ( $by_post as $pid ) {
					$meta = self::booking_meta( $pid, $today, - 1, - 1 );
					if ( ( $meta['order_count'] ?? 0 ) > 0 ) {
						$rows[] = $meta;
					}
				}
				?>
                <div class="dash_card">
                    <div class="dash_card_head">
                        <h4 class="abp"><i class="fas fa-truck _color_theme"></i> <?php esc_html_e( "Today's Deliveries", 'abp-rentalforge' ); ?></h4>
                        <a class="_btn_light_theme_xs" href="<?php echo esc_url( ABPRF_Function::build_url( 'orders' ) ); ?>"><?php esc_html_e( 'View All', 'abp-rentalforge' ); ?> <i class="fas fa-angle-right"></i></a>
                    </div>
                    <div class="dash_card_body dash_card_body_plain">
						<?php $this->delivery_post_rows( $rows, $today ); ?>
                    </div>
                </div>
				<?php
			}
			private function delivery_post_rows( $rows, $today ): void {
				$rows = is_array( $rows ) ? array_values( $rows ) : array();
				if ( empty( $rows ) ) {
					?>
                    <div class="dash_empty">
                        <i class="fas fa-calendar-check"></i>
                        <p><?php esc_html_e( 'No deliveries scheduled for today.', 'abp-rentalforge' ); ?></p>
                    </div>
					<?php
					return;
				}
				$orders = 0;
				$items  = 0;
				foreach ( $rows as $row ) {
					$orders += (int) ( $row['order_count'] ?? 0 );
					$items  += (int) ( $row['sold'] ?? 0 );
				}
				$count_posts = count( $rows );
				/* translators: %d: number of posts. */
				$meta_posts = sprintf( _n( '%d post', '%d posts', $count_posts, 'abp-rentalforge' ), $count_posts );
				/* translators: %d: number of orders. */
				$meta_orders = sprintf( _n( '%d order', '%d orders', $orders, 'abp-rentalforge' ), $orders );
				/* translators: %d: number of items. */
				$meta_items = sprintf( _n( '%d item', '%d items', $items, 'abp-rentalforge' ), $items );
				?>
                <div class="dash_orders_meta"><?php echo esc_html( $meta_posts ); ?> &bullet; <?php echo esc_html( $meta_orders ); ?> &bullet; <?php echo esc_html( $meta_items ); ?></div>
                <div class="dash_rows">
					<?php foreach ( $rows as $row ) {
						$post_id = (int) ( $row['post_id'] ?? 0 );
						$sold    = (int) ( $row['sold'] ?? 0 );
						$avail   = (int) ( $row['available'] ?? 0 );
						$reserve = (int) ( $row['reserve'] ?? 0 );
						$rent    = (float) ( $row['day_rent'] ?? 0 );
						$deposit = (float) ( $row['day_deposit'] ?? 0 );
						$row_orders = (int) ( $row['order_count'] ?? 0 );
						/* translators: %d: number of orders. */
						$row_orders_text = sprintf( _n( '%d order', '%d orders', $row_orders, 'abp-rentalforge' ), $row_orders );
						?>
                        <div class="dash_row dash_booking_row" data-booking data-post="<?php echo esc_attr( $post_id ); ?>" data-start="<?php echo esc_attr( $today ); ?>" title="<?php esc_attr_e( 'Click to view booking details', 'abp-rentalforge' ); ?>">
                            <div class="dash_row_main">
                                <h6 class="abp_gap_xs"><?php echo esc_html( $row['title'] ?? get_the_title( $post_id ) ); ?></h6>
                                <small>
                                    <i class="far fa-calendar-alt"></i> <?php echo esc_html( ABPRF_Function::date_format( $today ) ); ?>
                                    &bullet; <?php echo esc_html( $row_orders_text ); ?>
                                </small>
                            </div>
                            <div class="dash_row_meta dash_booking_meta">
                                <span class="dash_chip dash_chip_total"><i class="fas fa-cube"></i> <?php /* translators: %d: booked count */ echo esc_html( sprintf( __( 'Booked %d', 'abp-rentalforge' ), $sold ) ); ?></span>
                                <span class="dash_chip dash_chip_avail"><i class="fas fa-check-circle"></i> <?php /* translators: %d: available count */ echo esc_html( sprintf( __( 'Avail %d', 'abp-rentalforge' ), $avail ) ); ?></span>
                                <span class="dash_chip dash_chip_reserve"><i class="fas fa-lock"></i> <?php /* translators: %d: reserved count */ echo esc_html( sprintf( __( 'Reserve %d', 'abp-rentalforge' ), $reserve ) ); ?></span>
                                <span class="dash_chip"><i class="fas fa-money-bill-wave"></i> <?php /* translators: %s: rent amount */ echo esc_html( sprintf( __( 'Rent %s', 'abp-rentalforge' ), $rent > 0 && function_exists( 'wc_price' ) ? wp_strip_all_tags( wc_price( $rent ) ) : ( $rent > 0 ? number_format_i18n( $rent, 2 ) : __( 'FREE', 'abp-rentalforge' ) ) ) ); ?></span>
                                <span class="dash_chip"><i class="fas fa-hand-holding-dollar"></i> <?php /* translators: %s: deposit amount */ echo esc_html( sprintf( __( 'Deposit %s', 'abp-rentalforge' ), $deposit > 0 && function_exists( 'wc_price' ) ? wp_strip_all_tags( wc_price( $deposit ) ) : ( $deposit > 0 ? number_format_i18n( $deposit, 2 ) : __( 'FREE', 'abp-rentalforge' ) ) ) ); ?></span>
                            </div>
                        </div>
					<?php } ?>
                </div>
                <div class="dash_booking_hint"><i class="fas fa-mouse-pointer"></i> <?php esc_html_e( 'Click a post to see who booked what.', 'abp-rentalforge' ); ?></div>
				<?php
			}
			private function today_collections(): void {
				global $wpdb;
				$today = current_time( 'Y-m-d' );
				$due   = array();
				$table = $wpdb->prefix . 'abprf_orders';
				$statuses = array_filter( array_map( 'sanitize_text_field', explode( ',', (string) ABPRF_Function::booking_status() ) ) );
				if ( ! empty( $statuses ) && current( $statuses ) !== 'all' ) {
					$placeholders = implode( ',', array_fill( 0, count( $statuses ), '%s' ) );
					$sql          = "SELECT id, item_id, order_id, post_id, start_time, end_time, order_status, book_status, billing_name, property_info, price_info FROM %i WHERE DATE(end_time) <= %s AND order_status IN ($placeholders) AND book_status NOT IN (3,5) ORDER BY end_time ASC";
					$params       = array_merge( array( $table, $today ), $statuses );
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
					$due = $wpdb->get_results( $wpdb->prepare( $sql, $params ), ARRAY_A );
					$due = is_array( $due ) ? $due : array();
				}
				?>
                <div class="dash_card">
                    <div class="dash_card_head">
                        <h4 class="abp"><i class="fas fa-box-open _color_theme"></i> <?php esc_html_e( "Today's Collections", 'abp-rentalforge' ); ?></h4>
                        <a class="_btn_light_theme_xs" href="<?php echo esc_url( ABPRF_Function::build_url( 'orders' ) ); ?>"><?php esc_html_e( 'View All', 'abp-rentalforge' ); ?> <i class="fas fa-angle-right"></i></a>
                    </div>
                    <div class="dash_card_body dash_card_body_plain">
						<?php $this->collection_order_rows( $due ); ?>
                    </div>
                </div>
				<?php
			}
			private function collection_order_rows( $due ): void {
				$bookings = is_array( $due ) ? array_values( $due ) : array();
				if ( empty( $bookings ) ) {
					?>
                    <div class="dash_empty">
                        <i class="fas fa-calendar-check"></i>
                        <p><?php esc_html_e( 'No collections pending.', 'abp-rentalforge' ); ?></p>
                    </div>
					<?php
					return;
				}
				$now           = current_time( 'Y-m-d H:i:s' );
				$items         = 0;
				$overdue_count = 0;
				foreach ( $bookings as $key => $booking ) {
					$property_infos = json_decode( $booking['property_info'] ?? '', true ) ?: array();
					if ( is_array( $property_infos ) ) {
						foreach ( $property_infos as $property_info ) {
							$items += (int) ( $property_info['qty'] ?? 0 );
						}
					}
					$is_overdue = ( $booking['end_time'] ?? '' ) !== '' && strtotime( $booking['end_time'] ) < strtotime( $now );
					$bookings[ $key ]['_is_overdue'] = $is_overdue;
					if ( $is_overdue ) {
						$overdue_count ++;
					}
				}
				$count = count( $bookings );
				/* translators: %d: number of orders. */
				$meta_orders = sprintf( _n( '%d order', '%d orders', $count, 'abp-rentalforge' ), $count );
				/* translators: %d: number of items. */
				$meta_items = sprintf( _n( '%d item', '%d items', $items, 'abp-rentalforge' ), $items );
				/* translators: %d: number of overdue orders. */
				$meta_overdue = $overdue_count > 0 ? sprintf( _n( '%d overdue', '%d overdue', $overdue_count, 'abp-rentalforge' ), $overdue_count ) : '';
				?>
                <div class="dash_orders_meta"><?php echo esc_html( $meta_orders ); ?> &bullet; <?php echo esc_html( $meta_items ); ?><?php if ( $meta_overdue !== '' ) { ?> &bullet; <?php echo esc_html( $meta_overdue ); ?><?php } ?></div>
                <div class="dash_rows">
					<?php foreach ( $bookings as $booking ) {
						$post_id        = (int) ( $booking['post_id'] ?? 0 );
						$order_id       = (int) ( $booking['order_id'] ?? 0 );
						$book_item_id   = (int) ( $booking['item_id'] ?? 0 );
						$book_status    = (int) ( $booking['book_status'] ?? 0 );
						$start_time     = $booking['start_time'] ?? '';
						$end_time       = $booking['end_time'] ?? '';
						$status         = $booking['order_status'] ?? '';
						$name           = $booking['billing_name'] ?? '';
						$is_overdue     = ! empty( $booking['_is_overdue'] );
						$property_infos = json_decode( $booking['property_info'] ?? '', true ) ?: array();
						$property_text  = array();
						$qty            = 0;
						if ( is_array( $property_infos ) ) {
							foreach ( $property_infos as $property_info ) {
								$p_qty   = (int) ( $property_info['qty'] ?? 1 );
								$p_name  = $property_info['name'] ?? '';
								$qty    += $p_qty;
								if ( $p_name !== '' ) {
									$property_text[] = $p_name . ' x ' . $p_qty;
								}
							}
						}
						$price_info = json_decode( $booking['price_info'] ?? '', true ) ?: array();
						$total      = (float) ( $price_info['item_total'] ?? 0 );
						$deposit    = (float) ( $price_info['deposit'] ?? 0 );
						?>
                        <div class="dash_row dash_collect_row">
                            <div class="dash_row_main">
                                <h6 class="abp_gap_xs"><?php echo esc_html( $name !== '' ? $name : __( 'Guest', 'abp-rentalforge' ) ); ?> <span class="dash_oid">#<?php echo esc_html( $order_id ); ?></span></h6>
                                <small>
                                    <i class="fas fa-cube"></i> <?php echo esc_html( ! empty( $property_text ) ? implode( ', ', $property_text ) : __( 'No items', 'abp-rentalforge' ) ); ?>
                                    &bullet; <?php ABPRF_Layout::title( $post_id ); ?>
                                </small>
                                <small>
                                    <i class="far fa-calendar-alt"></i>
									<?php echo esc_html( ABPRF_Function::date_format( $start_time ) ); ?>
									<?php if ( $end_time !== '' ) { ?>
                                        &mdash; <?php echo esc_html( ABPRF_Function::date_format( $end_time ) ); ?>
									<?php } ?>
                                </small>
                            </div>
                            <div class="dash_row_meta dash_booking_meta">
                                <span class="dash_pill <?php echo esc_attr( $status ); ?>"><?php echo esc_html( ABPRF_Layout::status_text( $status ) ); ?></span>
<?php if ( $is_overdue ) {
										$late = ABPRF_Function::get_date_time_difference( $end_time, $now, '' );
										$late_text = trim( $late['text'] ?? '' );
										$late_text = preg_replace( '/\s+\d+\s+Seconds?/i', '', (string) $late_text );
										if ( $late_text !== '' ) {
											/* translators: %s: overdue duration. */
											$overdue_label = sprintf( __( 'Overdue by %s', 'abp-rentalforge' ), $late_text );
										} else {
											$overdue_label = __( 'Overdue', 'abp-rentalforge' );
										}
										?>
                                        <span class="dash_chip dash_chip_overdue"><i class="fas fa-exclamation-triangle"></i> <?php echo esc_html( $overdue_label ); ?></span>
								<?php } else { ?>
                                    <span class="dash_chip dash_chip_due"><?php esc_html_e( 'Due today', 'abp-rentalforge' ); ?></span>
								<?php } ?>
                                <span class="dash_chip dash_chip_total"><?php echo esc_html( 'x' . $qty ); ?></span>
								<?php if ( $total > 0 ) { ?>
                                    <span class="dash_chip"><i class="fas fa-money-bill-wave"></i> <?php echo function_exists( 'wc_price' ) ? wp_kses_post( wc_price( $total ) ) : esc_html( number_format_i18n( $total, 2 ) ); ?></span>
								<?php } ?>
								<?php if ( $deposit > 0 ) { ?>
                                    <span class="dash_chip"><i class="fas fa-hand-holding-dollar"></i> <?php echo function_exists( 'wc_price' ) ? wp_kses_post( wc_price( $deposit ) ) : esc_html( number_format_i18n( $deposit, 2 ) ); ?></span>
								<?php } ?>
								<?php if ( $book_item_id > 0 && $book_status < 3 ) { ?>
                                    <button type="button" class="_btn_light_success_xs dash_book_status_btn" data-item="<?php echo esc_attr( $book_item_id ); ?>" data-status="3"><?php esc_html_e( 'Mark Received', 'abp-rentalforge' ); ?></button>
								<?php } else { ?>
                                    <span class="dash_book_status_text"><?php echo esc_html( ABPRF_Layout::book_status_text( $book_status ) ); ?></span>
								<?php } ?>
                            </div>
                        </div>
					<?php } ?>
                </div>
                <div class="dash_booking_hint"><i class="fas fa-undo"></i> <?php esc_html_e( 'Use Mark Received when the item is returned.', 'abp-rentalforge' ); ?></div>
				<?php
			}
			private function quick_actions(): void {
				$actions = array(
					array( 'fas fa-toggle-on', __( 'ON/OFF Configuration', 'abp-rentalforge' ), __( 'Enable & disable features', 'abp-rentalforge' ), ABPRF_Function::build_url( 'configuration', [ 'configuration' => 'on_off' ] ), 'warning' ),
					array( 'fas fa-calendar-days', __( 'Global Dates', 'abp-rentalforge' ), __( 'Manage global dates', 'abp-rentalforge' ), ABPRF_Function::build_url( 'global', [ 'global' => 'dates' ] ), 'theme' ),
				);
				if ( ABPRF_Function::on_off( 'additional_info' ) ) {
					$actions[] = array( 'fas fa-list-check', __( 'Additional Services', 'abp-rentalforge' ), __( 'Manage additional services', 'abp-rentalforge' ), ABPRF_Function::build_url( 'global', [ 'global' => 'additional' ] ), 'success' );
				}
				if ( ABPRF_Function::on_off( 'client_info' ) ) {
					$actions[] = array( 'fas fa-user', __( 'Client Form', 'abp-rentalforge' ), __( 'Manage client form fields', 'abp-rentalforge' ), ABPRF_Function::build_url( 'global', [ 'global' => 'client_form' ] ), 'purple' );
				}
				if ( ABPRF_Function::on_off( 'location' ) ) {
					$actions[] = array( 'fas fa-map-marker-alt', __( 'Locations', 'abp-rentalforge' ), __( 'Manage rental locations', 'abp-rentalforge' ), ABPRF_Function::build_url( 'global', [ 'global' => 'location' ] ), 'navy' );
				}
				if ( ABPRF_Function::on_off( 'category' ) ) {
					$actions[] = array( 'fas fa-folder', __( 'Categories', 'abp-rentalforge' ), __( 'Manage rental categories', 'abp-rentalforge' ), ABPRF_Function::build_url( 'global', [ 'global' => 'category' ] ), 'theme' );
				}
				if ( ABPRF_Function::on_off( 'brand' ) ) {
					$actions[] = array( 'fas fa-tag', __( 'Brands', 'abp-rentalforge' ), __( 'Manage rental brands', 'abp-rentalforge' ), ABPRF_Function::build_url( 'global', [ 'global' => 'brand' ] ), 'purple' );
				}
				if ( defined( 'ABPRF_DIR_PRO' ) ) {
					if ( ABPRF_Function::on_off( 'partial_payment' ) ) {
						$actions[] = array( 'fas fa-hand-holding-dollar', __( 'Deposit Settings', 'abp-rentalforge' ), __( 'Configure deposit system', 'abp-rentalforge' ), ABPRF_Function::build_url( 'global', [ 'global' => 'discount' ] ), 'success' );
					}
				}
				?>
                <div class="dash_card">
                    <div class="dash_card_head">
                        <h4><i class="fas fa-bolt"></i> <?php esc_html_e( 'Quick Actions', 'abp-rentalforge' ); ?></h4>
                    </div>
                    <div class="dash_card_body">
                        <div class="dash_action_grid">
							<?php foreach ( $actions as $action ) { ?>
                                <a class="dash_action" href="<?php echo esc_url( $action[3] ); ?>">
                                    <span class="dash_action_icon <?php echo esc_attr( $action[4] ); ?>"><i class="<?php echo esc_attr( $action[0] ); ?>"></i></span>
                                    <span class="dash_action_text">
                                        <b><?php echo esc_html( $action[1] ); ?></b>
                                        <small><?php echo esc_html( $action[2] ); ?></small>
                                    </span>
                                    <i class="fas fa-angle-right dash_action_arrow"></i>
                                </a>
							<?php } ?>
                        </div>
                    </div>
                </div>
				<?php
			}
			private function orders_summary(): void {
				$last  = ABPRF_Query::get_booking_query( array(), 10 );
				$today = ABPRF_Query::get_booking_query( array( 'order_date' => current_time( 'Y-m-d' ) ) );
				?>
                <div class="dash_card dash_orders_card">
                    <div class="dash_card_head ">
                        <h4 class="abp_gap_xs"><i class="fas fa-receipt"></i> <?php esc_html_e( 'Recent Orders', 'abp-rentalforge' ); ?></h4>
                        <div class="_group_content">
                            <button type="button" class="_btn_light_theme_xs abp_active dash_order_tab" data-dtab="recent"><i class="fas fa-list-ul"></i> <?php esc_html_e( 'Last 10 Orders', 'abp-rentalforge' ); ?></button>
                            <button type="button" class="_btn_light_theme_xs dash_order_tab" data-dtab="today"><i class="fas fa-calendar-day"></i> <?php esc_html_e( "Today's Orders", 'abp-rentalforge' ); ?></button>
                        </div>
                        <a class="dash_card_link" href="<?php echo esc_url( ABPRF_Function::build_url( 'orders' ) ); ?>"><?php esc_html_e( 'All Orders', 'abp-rentalforge' ); ?> <i class="fas fa-angle-right"></i></a>
                    </div>
                    <div class="dash_orders_body">
                        <div class="dash_orders_pane abp_active" data-dpane="recent">
							<?php $this->order_table( $last, false ); ?>
                        </div>
                        <div class="dash_orders_pane" data-dpane="today">
							<?php $this->today_orders_pane( $today ); ?>
                        </div>
                    </div>
                </div>
				<?php
			}
			private function today_orders_pane( $items ): void {
				$total = count( $items );
				$step  = 10;
				?>
                <div class="dash_orders_meta"><?php echo esc_html( $total ); ?> <?php echo esc_html( _n( 'order', 'orders', $total, 'abp-rentalforge' ) ); ?></div>
				<?php $this->order_table( $items, false, $step ); ?>
				<?php if ( $total > $step ) { ?>
                    <div class="dash_pane_more">
                        <button type="button" class="_btn_light_theme_xs dash_orders_loadmore" data-step="<?php echo esc_attr( $step ); ?>"><?php esc_html_e( 'Load More', 'abp-rentalforge' ); ?></button>
                    </div>
				<?php } ?>
				<?php
			}
			private function order_table( $items, $with_total = true, $limit = 0 ): void {
				$totals = array( 'total' => 0 );
				?>
                <table class="dash_order_table">
                    <thead>
                    <tr>
                        <th><?php esc_html_e( 'Order / Post', 'abp-rentalforge' ); ?></th>
                        <th><?php esc_html_e( 'Rental Period', 'abp-rentalforge' ); ?></th>
                        <th><?php esc_html_e( 'Status', 'abp-rentalforge' ); ?></th>
                        <th class="dash_num"><?php esc_html_e( 'Total', 'abp-rentalforge' ); ?></th>
                    </tr>
                    </thead>
                    <tbody>
					<?php if ( ! empty( $items ) ) { ?>
						<?php foreach ( $items as $index => $item ) {
							$order_id   = (int) ( $item['order_id'] ?? 0 );
							$start_time = $item['start_time'] ?? '';
							$end_time   = $item['end_time'] ?? '';
							$price_info = json_decode( $item['price_info'] ?? '', true ) ?: array();
							$total      = (float) ( $price_info['item_total'] ?? 0 );
							$totals['total'] += $total;
							$row_class  = ( $limit > 0 && $index >= $limit ) ? 'dash_row_hidden' : '';
							?>
                            <tr class="<?php echo esc_attr( $row_class ); ?>">
                                <td data-label="<?php esc_attr_e( 'Order / Post', 'abp-rentalforge' ); ?>">
                                    <b class="dash_oid">#<?php echo esc_html( $order_id ); ?></b>
									<?php ABPRF_Layout::title( (int) ( $item['post_id'] ?? 0 ) ); ?>
                                </td>
                                <td data-label="<?php esc_attr_e( 'Rental Period', 'abp-rentalforge' ); ?>"><span class="dash_route"><?php
									if ( ! empty( $start_time ) ) {
										echo esc_html( ABPRF_Function::date_format( $start_time ) );
										if ( ! empty( $end_time ) ) {
											echo ' &mdash; ' . esc_html( ABPRF_Function::date_format( $end_time ) );
										}
									} else {
										echo '&mdash;';
									}
								?></span></td>
                                <td data-label="<?php esc_attr_e( 'Status', 'abp-rentalforge' ); ?>"><span class="dash_pill <?php echo esc_attr( $item['order_status'] ?? '' ); ?>"><?php echo esc_html( ABPRF_Layout::status_text( $item['order_status'] ?? '' ) ); ?></span></td>
                                <td class="dash_num" data-label="<?php esc_attr_e( 'Total', 'abp-rentalforge' ); ?>">
									<?php
										if ( $total > 0 ) {
											echo function_exists( 'wc_price' ) ? wp_kses_post( wc_price( $total ) ) : esc_html( number_format_i18n( $total, 2 ) );
										} else {
											esc_html_e( 'FREE', 'abp-rentalforge' );
										}
									?>
                                </td>
                            </tr>
						<?php } ?>
						<?php if ( $with_total ) { ?>
                            <tr class="dash_order_total_row">
                                <td colspan="3"><?php esc_html_e( 'Total', 'abp-rentalforge' ); ?></td>
                                <td class="dash_num"><?php echo function_exists( 'wc_price' ) ? wp_kses_post( wc_price( $totals['total'] ) ) : esc_html( number_format_i18n( $totals['total'], 2 ) ); ?></td>
                            </tr>
						<?php } ?>
					<?php } else { ?>
                        <tr>
                            <td colspan="4" class="dash_order_empty"><?php esc_html_e( 'No orders found.', 'abp-rentalforge' ); ?></td>
                        </tr>
					<?php } ?>
                    </tbody>
                </table>
				<?php
			}
			//=============================//
			public function booking_popup(): void {
				if ( ! check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce', false ) || ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( [ 'msg' => __( 'Invalid security token or Insufficient permissions.', 'abp-rentalforge' ), 'type' => 'warn' ], 403 );
				}
				$post_id    = isset( $_POST['post_id'] ) ? (int) $_POST['post_id'] : 0;
				$start_time = isset( $_POST['start_time'] ) ? sanitize_text_field( wp_unslash( $_POST['start_time'] ) ) : '';
				if ( $post_id <= 0 || empty( $start_time ) ) {
					wp_send_json_error( [ 'msg' => __( 'Invalid booking.', 'abp-rentalforge' ), 'type' => 'warn' ], 400 );
				}
				$meta = self::booking_meta( $post_id, $start_time );
				ob_start();
				?>
                <div class="dash_booking_popup">
                    <div class="dash_booking_pop_head">
                        <div>
                            <h4><i class="fas fa-hammer"></i> <?php echo esc_html( $meta['title'] ?? __( 'Post', 'abp-rentalforge' ) ); ?></h4>
                            <small><i class="far fa-calendar-alt"></i> <?php echo esc_html( ABPRF_Function::date_format( $start_time ) ); ?>
								<?php if ( ! empty( $meta['end_time'] ) ) { ?>
                                    &mdash; <?php echo esc_html( ABPRF_Function::date_format( $meta['end_time'] ) ); ?>
								<?php } ?>
                            </small>
                        </div>
                    </div>
                    <div class="dash_booking_stats">
                        <span class="dash_chip dash_chip_sold"><i class="fas fa-cube"></i> <?php /* translators: %d: rented count. */ echo esc_html( sprintf( __( 'Rented %d', 'abp-rentalforge' ), $meta['sold'] ) ); ?></span>
                        <span class="dash_chip dash_chip_avail"><i class="fas fa-check-circle"></i> <?php /* translators: %d: available count. */ echo esc_html( sprintf( __( 'Available %d', 'abp-rentalforge' ), $meta['available'] ) ); ?></span>
                        <span class="dash_chip dash_chip_total"><i class="fas fa-cubes"></i> <?php /* translators: %d: total count. */ echo esc_html( sprintf( __( 'Total %d', 'abp-rentalforge' ), $meta['total'] ) ); ?></span>
						<?php if ( $meta['reserve'] > 0 ) { ?>
                            <span class="dash_chip dash_chip_reserve"><i class="fas fa-lock"></i> <?php /* translators: %d: reserved count. */ echo esc_html( sprintf( __( 'Reserve %d', 'abp-rentalforge' ), $meta['reserve'] ) ); ?></span>
						<?php } ?>
                    </div>
                    <div class="dash_booking_section">
                        <h5><i class="fas fa-file-invoice"></i> <?php esc_html_e( 'Bookings', 'abp-rentalforge' ); ?> (<?php echo esc_html( $meta['order_count'] ); ?>)</h5>
						<?php $this->today_bookings( $post_id, $start_time ); ?>
                    </div>
                </div>
				<?php
				$html = ob_get_clean();
				wp_send_json_success( [ 'html' => $html, 'msg' => esc_html__( 'Booking details loaded.', 'abp-rentalforge' ), 'type' => 'success' ] );
			}
			//=============================//
			public function update_book_status(): void {
				if ( ! check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce', false ) || ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( [ 'msg' => __( 'Invalid security token or Insufficient permissions.', 'abp-rentalforge' ), 'type' => 'warn' ], 403 );
				}
				$item_id     = isset( $_POST['item_id'] ) ? (int) $_POST['item_id'] : 0;
				$book_status = isset( $_POST['book_status'] ) ? (int) $_POST['book_status'] : 0;
				if ( $item_id <= 0 || ! in_array( $book_status, array( 2, 3 ), true ) ) {
					wp_send_json_error( [ 'msg' => __( 'Invalid request.', 'abp-rentalforge' ), 'type' => 'warn' ], 400 );
				}
				global $wpdb;
				$table   = $wpdb->prefix . 'abprf_orders';
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
				$current = $wpdb->get_var( $wpdb->prepare( "SELECT book_status FROM %i WHERE item_id = %d", $table, $item_id ) );
				if ( null === $current ) {
					wp_send_json_error( [ 'msg' => __( 'Booking not found.', 'abp-rentalforge' ), 'type' => 'warn' ], 404 );
				}
				if ( (int) $current === 5 ) {
					wp_send_json_error( [ 'msg' => __( 'This booking is canceled.', 'abp-rentalforge' ), 'type' => 'warn' ], 400 );
				}
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
				$updated = $wpdb->update( $table, array( 'book_status' => $book_status, 'updated_at' => current_time( 'Y-m-d H:i:s' ) ), array( 'item_id' => $item_id ), array( '%d', '%s' ), array( '%d' ) );
				if ( false === $updated ) {
					wp_send_json_error( [ 'msg' => __( 'Could not update status.', 'abp-rentalforge' ), 'type' => 'warn' ] );
				}
				$text = ABPRF_Layout::book_status_text( $book_status );
				/* translators: %s: new rent status label. */
				wp_send_json_success( [ 'msg' => sprintf( __( 'Status updated to %s.', 'abp-rentalforge' ), $text ), 'type' => 'success', 'book_status' => $book_status, 'book_status_text' => $text ] );
			}
			//=============================//
			private function today_bookings( $post_id, $start_time ): void {
				$filters  = array(
					'post_id'    => $post_id,
					'start_time' => $start_time,
					'cache'      => false,
				);
				$bookings = ABPRF_Query::get_booking_query( $filters );
				if ( empty( $bookings ) ) {
					echo '<div class="dash_empty"><i class="fas fa-file-invoice"></i><p>' . esc_html__( 'No bookings for this period yet.', 'abp-rentalforge' ) . '</p></div>';
					return;
				}
				?>
                <div class="dash_booking_bookings">
					<?php foreach ( $bookings as $booking ) {
						$name           = $booking['billing_name'] ?? '';
						$order_id       = (int) ( $booking['order_id'] ?? 0 );
						$book_item_id   = (int) ( $booking['item_id'] ?? 0 );
						$book_status    = (int) ( $booking['book_status'] ?? 0 );
						$property_infos = json_decode( $booking['property_info'] ?? '', true ) ?: array();
						$property_text  = array();
						$qty            = 0;
						if ( is_array( $property_infos ) ) {
							foreach ( $property_infos as $property_info ) {
								$p_qty   = (int) ( $property_info['qty'] ?? 1 );
								$p_name  = $property_info['name'] ?? '';
								$qty    += $p_qty;
								if ( ! empty( $p_name ) ) {
									$property_text[] = esc_html( $p_name ) . ' x ' . $p_qty;
								}
							}
						}
						$price_info = json_decode( $booking['price_info'] ?? '', true ) ?: array();
						$rent       = (float) ( $price_info['rent'] ?? 0 );
						$ex_price   = (float) ( $price_info['ex_price'] ?? 0 );
						$deposit    = (float) ( $price_info['deposit'] ?? 0 );
						$item_total = (float) ( $price_info['item_total'] ?? 0 );
						$ex_infos   = json_decode( $booking['ex_info'] ?? '', true ) ?: array();
						$ex_text    = array();
						if ( is_array( $ex_infos ) ) {
							foreach ( $ex_infos as $ex_info ) {
								if ( ! is_array( $ex_info ) ) {
									continue;
								}
								$ex_name = $ex_info['name'] ?? '';
								$ex_qty  = (int) ( $ex_info['qty'] ?? 1 );
								if ( $ex_name !== '' ) {
									$ex_text[] = esc_html( $ex_name ) . ' x ' . $ex_qty;
								}
							}
						}
						$status = $booking['order_status'] ?? '';
						?>
                        <div class="dash_booking_item">
                            <div class="dash_booking_item_main">
                                <strong><?php echo esc_html( $name !== '' ? $name : '#' . $order_id ); ?></strong>
								<?php if ( ! empty( $property_text ) ) { ?>
                                    <small><i class="fas fa-cube"></i> <?php echo esc_html( implode( ', ', $property_text ) ); ?></small>
								<?php } ?>
                                <small class="dash_booking_prices">
                                    <span><?php esc_html_e( 'Rent', 'abp-rentalforge' ); ?>: <?php echo $rent > 0 && function_exists( 'wc_price' ) ? wp_kses_post( wc_price( $rent ) ) : esc_html__( 'FREE', 'abp-rentalforge' ); ?></span>
                                    <span><?php esc_html_e( 'Additional', 'abp-rentalforge' ); ?>: <?php echo $ex_price > 0 && function_exists( 'wc_price' ) ? wp_kses_post( wc_price( $ex_price ) ) : esc_html__( 'FREE', 'abp-rentalforge' ); ?></span>
									<?php if ( $deposit > 0 ) { ?>
                                        <span><?php esc_html_e( 'Deposit', 'abp-rentalforge' ); ?>: <?php echo function_exists( 'wc_price' ) ? wp_kses_post( wc_price( $deposit ) ) : esc_html( number_format_i18n( $deposit, 2 ) ); ?></span>
									<?php } ?>
                                    <span><b><?php esc_html_e( 'Total', 'abp-rentalforge' ); ?>: <?php echo $item_total > 0 && function_exists( 'wc_price' ) ? wp_kses_post( wc_price( $item_total ) ) : esc_html__( 'FREE', 'abp-rentalforge' ); ?></b></span>
                                </small>
								<?php if ( ! empty( $ex_text ) ) { ?>
                                    <small><i class="fas fa-list-check"></i> <?php echo esc_html( implode( ', ', $ex_text ) ); ?></small>
								<?php } ?>
                            </div>
                            <div class="dash_booking_item_side">
                                <span class="dash_pill <?php echo esc_attr( $status ); ?>"><?php echo esc_html( ABPRF_Layout::status_text( $status ) ); ?></span>
                                <span class="dash_chip dash_chip_total"><?php echo esc_html( 'x' . $qty ); ?></span>
								<?php if ( $book_item_id > 0 && $book_status < 2 ) { ?>
                                    <button type="button" class="_btn_light_success_xs dash_book_status_btn" data-item="<?php echo esc_attr( $book_item_id ); ?>" data-status="2"><?php esc_html_e( 'Start Rent', 'abp-rentalforge' ); ?></button>
								<?php } else { ?>
                                    <span class="dash_book_status_text"><?php echo esc_html( ABPRF_Layout::book_status_text( $book_status ) ); ?></span>
								<?php } ?>
                            </div>
                        </div>
					<?php } ?>
                </div>
				<?php
			}
			private function system_status( $abprf_info, $label ): void {
				$total           = (int) ( $abprf_info['total_post'] ?? 0 );
				$total_property  = (int) ( $abprf_info['total_property'] ?? 0 );
				?>
                <div class="dash_card">
                    <div class="dash_card_head">
                        <h4 class="abp_gap_xs"><i class="fas fa-server"></i> <?php esc_html_e( 'System Status', 'abp-rentalforge' ); ?></h4>
                    </div>
                    <div class="dash_card_body dash_card_body_plain">
                        <div class="dash_kv_list">
                            <div class="dash_kv">
                                <span><?php esc_html_e( 'Plugin', 'abp-rentalforge' ); ?></span>
                                <b><?php echo esc_html( ABPRF_VERSION ); ?></b>
                            </div>
                            <div class="dash_kv">
                                <span><?php esc_html_e( 'WordPress', 'abp-rentalforge' ); ?></span>
                                <b><?php echo esc_html( get_bloginfo( 'version' ) ); ?></b>
                            </div>
                            <div class="dash_kv">
                                <span><?php esc_html_e( 'PHP', 'abp-rentalforge' ); ?></span>
                                <b><?php echo esc_html( phpversion() ); ?></b>
                            </div>
                            <div class="dash_kv">
                                <span><?php esc_html_e( 'WooCommerce', 'abp-rentalforge' ); ?></span>
								<?php if ( defined( 'ABPRF_WC' ) && ABPRF_WC >= 2 && defined( 'WC_VERSION' ) ) { ?>
                                    <b class="<?php echo esc_attr( version_compare( WC_VERSION, '8.0', '>' ) ? '' : 'dash_status_warn' ); ?>"><?php echo esc_html( WC_VERSION ); ?></b>
								<?php } else { ?>
                                    <b class="dash_status_warn"><?php esc_html_e( 'Not active', 'abp-rentalforge' ); ?></b>
								<?php } ?>
                            </div>
							<?php if ( defined( 'ABPRF_WC' ) && ABPRF_WC >= 2 ) {
								$wc_name  = get_option( 'woocommerce_email_from_name' );
								$wc_email = get_option( 'woocommerce_email_from_address' );
								if ( ! empty( $wc_name ) ) { ?>
                                    <div class="dash_kv">
                                        <span><?php esc_html_e( 'WC Name', 'abp-rentalforge' ); ?></span>
                                        <b><?php echo esc_html( $wc_name ); ?></b>
                                    </div>
								<?php }
								if ( ! empty( $wc_email ) ) { ?>
                                    <div class="dash_kv">
                                        <span><?php esc_html_e( 'WC Email', 'abp-rentalforge' ); ?></span>
                                        <b><?php echo esc_html( $wc_email ); ?></b>
                                    </div>
								<?php }
							} ?>
                            <div class="dash_status_checklist">
								<?php $this->checklist_item( __( 'WooCommerce Plugin', 'abp-rentalforge' ), defined( 'ABPRF_WC' ) && ABPRF_WC >= 2, defined( 'ABPRF_WC' ) && ABPRF_WC == 1 ? 'wc_active' : 'wc_install_active' ); ?>
								<?php $this->checklist_item( __( 'Booking Page', 'abp-rentalforge' ), (bool) ABPRF_Function::get_page_by_slug( 'rf_booking' ), 'rf_booking' ); ?>
								<?php $this->checklist_item(
								/* translators: %s: plugin label. */
									sprintf( esc_html__( '%s List Page', 'abp-rentalforge' ), esc_html( $label ) ),
									(bool) ABPRF_Function::get_page_by_slug( 'rf_post' ),
									'rf_post'
								); ?>
								<?php $this->checklist_item( __( 'Property List Page', 'abp-rentalforge' ), (bool) ABPRF_Function::get_page_by_slug( 'rf_property' ), 'rf_property' ); ?>
								<?php $this->checklist_item( __( 'Gallery Page', 'abp-rentalforge' ), (bool) ABPRF_Function::get_page_by_slug( 'rf_gallery' ), 'rf_gallery' ); ?>
								<?php $this->checklist_item( __( 'First Post Added', 'abp-rentalforge' ), $total > 0, 'add_new' ); ?>
                                <div class="dash_kv">
                                    <span class="dash_status_item is_todo">
                                        <i class="far fa-circle"></i>
                                        <?php esc_html_e( 'Number of Posts', 'abp-rentalforge' ); ?>
                                    </span>
                                    <b class="dash_status_ready"><?php echo esc_html( $total ); ?></b>
                                </div>
                                <div class="dash_kv">
                                    <span class="dash_status_item <?php echo esc_attr( $total_property > 0 ? 'is_done' : 'is_todo' ); ?>">
                                        <i class="<?php echo esc_attr( $total_property > 0 ? 'fas fa-check-circle' : 'far fa-circle' ); ?>"></i>
                                        <?php esc_html_e( 'Number of Properties', 'abp-rentalforge' ); ?>
                                    </span>
                                    <b class="<?php echo esc_attr( $total_property > 0 ? 'dash_status_ready' : 'dash_status_warn' ); ?>"><?php echo esc_html( $total_property ); ?></b>
                                </div>
                                <div class="dash_kv">
                                    <span class="dash_status_item is_todo">
                                        <i class="fas fa-magic"></i>
                                        <?php esc_html_e( 'Dummy Import', 'abp-rentalforge' ); ?>
                                    </span>
                                    <button class="dash_status_fix import_dummy" type="button"><?php esc_html_e( 'Add New Dummy Post', 'abp-rentalforge' ); ?></button>
                                </div>
                            </div>
                        </div>
                        <div class="_divider"></div>
                        <?php do_action( 'abprf_add_tools' ); ?>
                    </div>
                </div>
				<?php
			}
			private function checklist_item( $label, $done, $fix_type = '' ): void {
				?>
                <div class="dash_kv">
                    <span class="dash_status_item <?php echo esc_attr( $done ? 'is_done' : 'is_todo' ); ?>">
                        <i class="<?php echo esc_attr( $done ? 'fas fa-check-circle' : 'far fa-circle' ); ?>"></i>
                        <?php echo esc_html( $label ); ?>
                    </span>
					<?php if ( $done ) { ?>
                        <b class="dash_status_ready"><?php esc_html_e( 'Ready', 'abp-rentalforge' ); ?></b>
					<?php } elseif ( $fix_type === 'add_new' ) { ?>
                        <a class="dash_status_fix" href="<?php echo esc_url( admin_url( 'post-new.php?post_type=' . ABPRF_Function::get_cpt() ) ); ?>"><?php esc_html_e( 'Fix', 'abp-rentalforge' ); ?></a>
					<?php } elseif ( in_array( $fix_type, array( 'wc_active', 'wc_install_active' ), true ) ) { ?>
                        <button class="dash_status_fix" onclick="abprf_wc_config('<?php echo esc_attr( $fix_type ); ?>', this)" type="button">
							<?php echo esc_html( $fix_type === 'wc_active' ? __( 'Active Now', 'abp-rentalforge' ) : __( 'Install & Activate', 'abp-rentalforge' ) ); ?>
                        </button>
					<?php } elseif ( ! empty( $fix_type ) ) { ?>
                        <button class="dash_status_fix create_page" data-page_type="<?php echo esc_attr( $fix_type ); ?>" type="button"><?php esc_html_e( 'Fix', 'abp-rentalforge' ); ?></button>
					<?php } ?>
                </div>
				<?php
			}
			private function content_breakdown( $abprf_info ): void {
				$rows = array(
					array( 'publish', __( 'Published', 'abp-rentalforge' ), (int) ( $abprf_info['total_publish'] ?? 0 ) ),
					array( 'draft', __( 'Draft', 'abp-rentalforge' ), (int) ( $abprf_info['total_draft'] ?? 0 ) ),
					array( 'private', __( 'Private', 'abp-rentalforge' ), (int) ( $abprf_info['total_private'] ?? 0 ) ),
					array( 'trash', __( 'Trash', 'abp-rentalforge' ), (int) ( $abprf_info['total_trash'] ?? 0 ) ),
				);
				$sum  = 0;
				foreach ( $rows as $row ) {
					$sum += $row[2];
				}
				?>
                <div class="dash_card">
                    <div class="dash_card_head">
                        <h4><i class="fas fa-layer-group"></i> <?php esc_html_e( 'Content Breakdown', 'abp-rentalforge' ); ?></h4>
                    </div>
                    <div class="dash_card_body">
                        <div class="dash_bar">
							<?php foreach ( $rows as $row ) {
								$width = $sum > 0 ? (int) round( ( $row[2] / $sum ) * 100 ) : 0;
								if ( $row[2] > 0 && $width < 1 ) {
									$width = 1;
								}
								?>
                                <span class="<?php echo esc_attr( $row[0] ); ?>" style="width:<?php echo esc_attr( $width ); ?>%;" title="<?php echo esc_attr( $row[1] . ': ' . $row[2] ); ?>"></span>
							<?php } ?>
                        </div>
                        <ul class="dash_legend">
							<?php foreach ( $rows as $row ) { ?>
                                <li>
                                    <span class="dash_dot <?php echo esc_attr( $row[0] ); ?>"></span>
									<?php echo esc_html( $row[1] ); ?>
                                    <b><?php echo esc_html( $row[2] ); ?></b>
                                </li>
							<?php } ?>
                        </ul>
                    </div>
                </div>
				<?php
			}
			//=============================//
			public static function kpi_data(): array {
				$cache_key = 'abprf_dashboard_kpi';
				$cached    = wp_cache_get( $cache_key, 'abprf_dashboard' );
				if ( is_array( $cached ) ) {
					return $cached;
				}
				global $wpdb;
				$table_name = $wpdb->prefix . 'abprf_orders';
				$data       = array( 'revenue' => 0, 'rented' => 0, 'today' => 0 );
				$statuses   = array_filter( array_map( 'sanitize_text_field', explode( ',', (string) ABPRF_Function::booking_status() ) ) );
				if ( ! empty( $statuses ) && current( $statuses ) !== 'all' ) {
					$placeholders = implode( ',', array_fill( 0, count( $statuses ), '%s' ) );
					$sql          = "SELECT price_info, property_info FROM %i WHERE order_status IN ($placeholders)";
					$params       = array_merge( array( $table_name ), $statuses );
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared -- $placeholders is a generated %s list passed via $wpdb->prepare().
					$rows         = $wpdb->get_results( $wpdb->prepare( $sql, $params ), ARRAY_A );
					if ( ! empty( $rows ) ) {
						foreach ( $rows as $row ) {
							$price_info   = json_decode( $row['price_info'] ?? '', true ) ?: array();
							$data['revenue'] += (float) ( $price_info['item_total'] ?? 0 );
							$property_info = json_decode( $row['property_info'] ?? '', true ) ?: array();
							if ( is_array( $property_info ) ) {
								foreach ( $property_info as $p ) {
									$data['rented'] += (int) ( $p['qty'] ?? 0 );
								}
							}
						}
					}
				}
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
				$data['today'] = (int) $wpdb->get_var(
					$wpdb->prepare( 'SELECT COUNT(*) FROM %i WHERE DATE(created_at) = %s', $table_name, current_time( 'Y-m-d' ) )
				);
				wp_cache_set( $cache_key, $data, 'abprf_dashboard' );
				return $data;
			}
			//=============================//
			public static function booking_meta( $post_id, $start_time, $sold_qty = - 1, $order_count = - 1 ): array {
				$post_id = (int) $post_id;
				$total   = 0;
				$reserve = 0;
				$properties = ABPRF_Query::get_property( array( 'post_id' => $post_id, 'status' => 'publish' ) );
				if ( is_array( $properties ) ) {
					foreach ( $properties as $property ) {
						$stock    = self::property_stock( $property );
						$total   += $stock['qty'];
						$reserve += $stock['reserve'];
					}
				}
				$filters = array( 'post_id' => $post_id, 'start_time' => $start_time, 'cache' => false );
				$day_rent    = 0;
				$day_deposit = 0;
				if ( $sold_qty < 0 ) {
					$booking_lists = ABPRF_Query::get_booking_query( $filters );
					$sold_qty = 0;
					if ( ! empty( $booking_lists ) ) {
						foreach ( $booking_lists as $booking ) {
							$p_infos = json_decode( $booking['property_info'] ?? '', true ) ?: array();
							foreach ( $p_infos as $pi ) {
								$sold_qty += (int) ( $pi['qty'] ?? 0 );
							}
							$price_info   = json_decode( $booking['price_info'] ?? '', true ) ?: array();
							$day_rent    += (float) ( $price_info['rent'] ?? 0 );
							$day_deposit += (float) ( $price_info['deposit'] ?? 0 );
						}
					}
				}
				if ( $order_count < 0 ) {
					$order_count = count( ABPRF_Query::get_booking_query( $filters ) );
				}
				$available = $total - $sold_qty - $reserve;
				if ( $available < 0 ) {
					$available = 0;
				}
				return array(
					'post_id'     => $post_id,
					'start_time'  => $start_time,
					'end_time'    => $start_time,
					'title'       => get_the_title( $post_id ),
					'started'     => strtotime( $start_time ) <= current_time( 'timestamp' ),
					'sold'        => (int) $sold_qty,
					'available'   => $available,
					'total'       => $total,
					'reserve'     => $reserve,
					'day_rent'    => $day_rent,
					'day_deposit' => $day_deposit,
					'order_count' => (int) $order_count,
				);
			}
			//=============================//
			private static function property_stock( $property ): array {
				$stock = array( 'qty' => 0, 'reserve' => 0 );
				if ( ! is_array( $property ) ) {
					return $stock;
				}
				$pqi = json_decode( $property['price_qty_info'] ?? '', true );
				if ( ! is_array( $pqi ) ) {
					return $stock;
				}
				$rule = $property['rent_rule'] ?? '';
				$candidates = array();
				if ( $rule !== '' && isset( $pqi[ $rule ] ) && is_array( $pqi[ $rule ] ) ) {
					$candidates[] = $pqi[ $rule ];
				}
				foreach ( $pqi as $sub ) {
					if ( is_array( $sub ) && isset( $sub[ $rule ] ) && is_array( $sub[ $rule ] ) ) {
						$candidates[] = $sub[ $rule ];
					}
				}
				if ( empty( $candidates ) ) {
					foreach ( $pqi as $sub ) {
						if ( is_array( $sub ) && isset( $sub['qty'] ) ) {
							$candidates[] = $sub;
							break;
						}
					}
				}
				if ( ! empty( $candidates ) ) {
					$info             = $candidates[0];
					$stock['qty']     = (int) ( $info['qty'] ?? 0 );
					$stock['reserve'] = (int) ( $info['reserve'] ?? 0 );
				}
				return $stock;
			}
			//=============================//
			public function wc_config(): void {
				if (!check_ajax_referer('abprf_admin_ajax_nonce', 'nonce', false) || !current_user_can('manage_options')) {
					wp_send_json_error(['msg' => __('Invalid security token or Insufficient permissions.', 'abp-rentalforge'), 'type' => 'warn'], 403);
				}
				$post_val = fn($key, $default = '') => isset($_POST[$key]) ? sanitize_text_field(wp_unslash($_POST[$key])) : $default;
				$page_type = $post_val('type');
				if ($page_type == 'wc_install_active') {
					include_once(ABSPATH . 'wp-admin/includes/plugin-install.php');
					include_once(ABSPATH . 'wp-admin/includes/file.php');
					include_once(ABSPATH . 'wp-admin/includes/misc.php');
					include_once(ABSPATH . 'wp-admin/includes/class-wp-upgrader.php');
					$plugin = 'woocommerce';
					$api = plugins_api('plugin_information', array(
						'slug' => $plugin,
						'fields' => array(
							'short_description' => false,
							'sections' => false,
							'requires' => false,
							'rating' => false,
							'ratings' => false,
							'downloaded' => false,
							'last_updated' => false,
							'added' => false,
							'tags' => false,
							'compatibility' => false,
							'homepage' => false,
							'donate_link' => false,
						),
					));
					if (is_wp_error($api)) {
						wp_send_json_error(['html' => '', 'msg' => $api->get_error_message()]);
					}
					$title = 'title';
					$url = 'url';
					$nonce = 'nonce';
					$woocommerce_plugin = new Plugin_Upgrader(new Plugin_Installer_Skin(compact('title', 'url', 'nonce', 'plugin', 'api')));
					$installed = $woocommerce_plugin->install($api->download_link);
					if (is_wp_error($installed)) {
						wp_send_json_error(['msg' => $installed->get_error_message(), 'type' => 'warn']);
					}
					$activated = activate_plugin('woocommerce/woocommerce.php');
					if (is_wp_error($activated)) {
						wp_send_json_error(['msg' => $activated->get_error_message(), 'type' => 'warn']);
					}
					wp_send_json_success(['msg' => esc_html__('WooCommerce installed and activated successfully!', 'abp-rentalforge'), 'type' => 'success'], 200);
				}
				if ($page_type == 'wc_active') {
					if (defined('ABPRF_WC') && ABPRF_WC == 1) {
						$activated = activate_plugin('woocommerce/woocommerce.php');
						if (is_wp_error($activated)) {
							wp_send_json_error(['msg' => $activated->get_error_message(), 'type' => 'warn']);
						}
						wp_send_json_success(['msg' => esc_html__('WooCommerce activated successfully!', 'abp-rentalforge'), 'type' => 'success'], 200);
					}
				}
				wp_send_json_error(['msg' => esc_html__('WooCommerce is either not installed or already active.', 'abp-rentalforge'), 'type' => 'warn'], 403);
			}
			//=============================//
			public function create_page(): void {
				if (!check_ajax_referer('abprf_admin_ajax_nonce', 'nonce', false)) {
					wp_send_json_error(['info_type' => 'warn', 'msg' => __('Invalid security token.', 'abp-rentalforge')], 403);
				}
				if (!current_user_can('manage_options')) {
					wp_send_json_error(['info_type' => 'warn', 'msg' => __('Insufficient permissions.', 'abp-rentalforge')], 403);
				}
				$post_val = fn($key, $default = '') => isset($_POST[$key]) ? sanitize_text_field(wp_unslash($_POST[$key])) : $default;
				$page_type = $post_val('type');
				if (!empty($page_type)) {
					if (!ABPRF_Function::get_page_by_slug($page_type)) {
						$label = ABPRF_Function::label();
						$short_code = '';
						if ($page_type == 'rf_booking') {
							$label = __('Booking', 'abp-rentalforge');
							$short_code = '[abprf-booking]';
						}
						if ($page_type == 'rf_post') {
							$short_code = '[abprf-post]';
						}
						if ($page_type == 'rf_property') {
							$label = __('Property', 'abp-rentalforge');
							$short_code = '[abprf-property]';
						}
						if ($page_type == 'rf_gallery') {
							$label = __('Gallery', 'abp-rentalforge');
							$short_code = '[abprf-gallery]';
						}
						$page = array(
							'post_type' => 'page',
							'post_name' => $page_type,
							'post_title' => $label,
							'post_content' => $short_code,
							'post_status' => 'publish',
						);
						$post_id = wp_insert_post($page);
						if (is_wp_error($post_id) || 0 === $post_id) {
							wp_send_json_error(['info_type' => 'warn', 'msg' => esc_html__('Failed to create page.', 'abp-rentalforge')]);
						}
						flush_rewrite_rules();
						/* translators: %s: Custom rental item type label (e.g., Vehicle, Equipment, Property) */
						$translated_format = esc_html__('%s Page Created successfully.....', 'abp-rentalforge');
						$msg = sprintf($translated_format, $label);
						wp_send_json_success(['info_type' => 'success', 'msg' => $msg]);
					}
					wp_send_json_error(['info_type' => 'warn', 'msg' => esc_html__('Page already exists.', 'abp-rentalforge')]);
				} else {
					wp_send_json_error(['info_type' => 'warn', 'msg' => esc_html__('Something Wrong...!', 'abp-rentalforge')]);
				}
			}
			//=============================//
			public function import_dummy(): void {
				if (!check_ajax_referer('abprf_admin_ajax_nonce', 'nonce', false)) {
					wp_send_json_error(['html' => '', 'msg' => __('Invalid security token.', 'abp-rentalforge')], 403);
				}
				if (!current_user_can('manage_options')) {
					wp_send_json_error(['html' => '', 'msg' => __('Insufficient permissions.', 'abp-rentalforge')], 403);
				}
				$category = isset($_POST['category']) ? sanitize_text_field(wp_unslash($_POST['category'])) : '';
				try {
					self::add_data($category);
					flush_rewrite_rules();
					wp_send_json_success([
						'msg' => esc_html__('Dummy data imported successfully!', 'abp-rentalforge')
					]);
				} catch (Exception $e) {
					wp_send_json_error([
						'msg' => esc_html__('An error occurred during data import.', 'abp-rentalforge')
					]);
				}
			}
			//=============================//
			public static function add_data($_category): void {
				global $wpdb;
				$table_name = $wpdb->prefix . 'abprf_property';
				$dummy_infos = self::dummy_data();
				if (isset($dummy_infos['taxonomy'])) {
					foreach ($dummy_infos['taxonomy'] as $tax => $taxonomy_option) {
						if (taxonomy_exists($tax)) {
							$check_terms = get_terms(array('taxonomy' => $tax, 'hide_empty' => false));
							if (is_string($check_terms) || sizeof($check_terms) == 0) {
								foreach ($taxonomy_option as $taxonomy_data) {
									unset($term);
									$term = wp_insert_term($taxonomy_data['name'], $tax);
								}
							}
						}
					}
					do_action('abprf_category_update');
					do_action('abprf_location_update');
					do_action('abprf_brand_update');
				}
				if (isset($dummy_infos['options'])) {
					foreach ($dummy_infos['options'] as $option => $dummy_option) {
						$option_data = get_option($option);
						if (!$option_data || sizeof($option_data) == 0) {
							update_option($option, $dummy_option);
						}
					}
					ABPRF_Feature::update_feature_js();
				}
				if (isset($dummy_infos['custom_post'])) {
					$abprf_location = ABPRF_Function::get_option('abprf_location');
					$abprf_brand = ABPRF_Function::get_option('abprf_brand');
					$abprf_category = ABPRF_Function::get_option('abprf_category');
					$_category = !empty($_category) ? explode(',', $_category) : ['transport'];
					foreach ($dummy_infos['custom_post'] as $custom_post => $dummy_post) {
						foreach ($dummy_post as $cat_key => $cat_data) {
							if (in_array($cat_key, $_category)) {
								foreach ($cat_data as $dummy_data) {
									$args = array();
									if (isset($dummy_data['name'])) {
										$args['post_title'] = $dummy_data['name'];
									}
									$args['post_status'] = 'publish';
									$args['post_type'] = $custom_post;
									$post_id = wp_insert_post($args);
									$post_data = $dummy_data['post_data'] ?? [];
									if (!empty($post_data)) {
										$common_data = $post_data['common_data'] ?? [];
										foreach ($common_data as $meta_key => $data) {
											update_post_meta($post_id, $meta_key, $data);
										}
										$template = $post_data['abprf_template'] ?? 'grid';
										update_post_meta($post_id, 'abprf_template', $template);
										$rent_rule = $post_data['rent_rule'] ?? 'hourly';
										update_post_meta($post_id, 'rent_rule', $rent_rule);
										$post_cat = sizeof($abprf_category) > 0 ? array_key_first($abprf_category) : '';
										update_post_meta($post_id, 'abprf_category', $post_cat);
										$post_loc = '';
										if (sizeof($abprf_location) > 3) {
											$post_loc_key = array_rand($abprf_location, 3);
											$post_loc = implode(',', $post_loc_key);
										}
										update_post_meta($post_id, 'abprf_location', $post_loc);
										$properties = $post_data['property'] ?? [];
										if (!empty($properties)) {
											foreach ($properties as $property) {
												$post_brand = sizeof($abprf_brand) > 0 ? array_rand($abprf_brand) : '';
												$data = [
													'post_id' => intval($post_id),
													'rent_continue' => 'on',
													'name' => sanitize_text_field($property['name'] ?? ''),
													'brand' => sanitize_text_field($post_brand),
													'category' => sanitize_text_field($post_cat),
													'location' => sanitize_text_field($post_loc),
													'features' => sanitize_text_field($property['features'] ?? ''),
													'rent_rule' => sanitize_text_field($rent_rule),
													'price_qty_info' => wp_json_encode($property['price_qty_info'] ?? []),
													'gallery' => sanitize_text_field($property['gallery'] ?? ''),
													'status' => !empty($post_id) ? get_post_status($post_id) : '',
													'others' => wp_json_encode($property['others'] ?? []),
													'updated_at' => current_time('Y-m-d H:i')
												];
												// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
												$wpdb->insert($table_name, $data);
											}
										}
										ABPRF_Function::update_dates($post_id);
										ABPRF_Function::update_time_slot($post_id);
										ABPRF_Function::update_global_data($post_id);
									}
								}
							}
						}
					}
				}
			}
			//=============================//
			public static function dummy_data(): array {
				return [
					'taxonomy' => [
						'abprf_category' => ABPRF_Static::static_category(),
						'abprf_location' => ABPRF_Static::static_location(),
						'abprf_brand' => ABPRF_Static::static_brand(),
					],
					'options' => [
						'abprf_feature' => ABPRF_Static::static_feature(),
						'abprf_additional' => ABPRF_Static::static_additional(),
						'abprf_forms' => ABPRF_Static::static_form(),
						'abprf_faqs' => ABPRF_Static::static_faq(),
						'abprf_tc' => ABPRF_Static::static_tc(),
					],
					'custom_post' => [
						'abprf_post' => [
							'transport' => [
								0 => [
									'name' => 'Motor Bike',
									'post_data' => [
										'common_data' => self::post_data(),
										'abprf_template' => 'grid',
										'rent_rule' => 'hourly',
										'property' => [
											0 => [
												'name' => 'Ducati Panigale V4',
												'features' => 'fec_id_4,fec_id_7,fec_id_9,fec_id_11',
												'rent_rule' => 'hourly',
												'gallery' => '10,20,30,40,50',
												'others' => ['icon' => '🏍️'],
												'price_qty_info' => [
													'hourly' => ['qty' => 10, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 3, 'min' => 2, 'max' => 5],
													'daily' => ['qty' => 10, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 10, 'min' => 1, 'max' => 10],
													'multi_day' => ['qty' => 10, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 10, 'price_multi' => 2, 'min' => 1, 'max' => 10],
												]
											],
											1 => [
												'name' => 'Yamaha YZF-R1',
												'features' => 'fec_id_3,fec_id_7,fec_id_9,fec_id_11,fec_id_10',
												'rent_rule' => 'hourly',
												'gallery' => '10,20,30,40,50',
												'others' => ['icon' => 'fas fa-motorcycle'],
												'price_qty_info' => [
													'hourly' => ['qty' => 15, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 1, 'price' => 4, 'min' => 2, 'max' => 10],
													'daily' => ['qty' => 15, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 30, 'min' => 1, 'max' => 10],
													'multi_day' => ['qty' => 15, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 30, 'price_multi' => 2, 'min' => 1, 'max' => 10],
												]
											],
											2 => [
												'name' => 'BMW S1000RR',
												'features' => 'fec_id_3,fec_id_7,fec_id_9,fec_id_11,fec_id_10',
												'rent_rule' => 'hourly',
												'gallery' => '10,20,30,40,50',
												'others' => ['icon' => '🏍️'],
												'price_qty_info' => [
													'hourly' => ['qty' => 5, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 1, 'price' => 5, 'min' => 2, 'max' => 10],
													'daily' => ['qty' => 15, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 30, 'min' => 1, 'max' => 10],
													'multi_day' => ['qty' => 15, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 30, 'price_multi' => 2, 'min' => 1, 'max' => 10],
												]
											],
											3 => [
												'name' => 'Kawasaki Ninja H2',
												'features' => 'fec_id_4,fec_id_7,fec_id_9,fec_id_11,fec_id_10',
												'rent_rule' => 'hourly',
												'gallery' => '10,20,30,40,50',
												'others' => ['icon' => 'fas fa-motorcycle'],
												'price_qty_info' => [
													'hourly' => ['qty' => 5, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 1, 'price' => 6, 'min' => 2, 'max' => 10],
													'daily' => ['qty' => 15, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 20, 'min' => 1, 'max' => 10],
													'multi_day' => ['qty' => 15, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 30, 'price_multi' => 2, 'min' => 1, 'max' => 10],
												]
											],
											4 => [
												'name' => 'Honda CBR1000RR-R Fireblade',
												'features' => 'fec_id_3,fec_id_7,fec_id_9,fec_id_11,fec_id_10',
												'rent_rule' => 'hourly',
												'gallery' => '10,20,30,40,50',
												'others' => ['icon' => '🏍️'],
												'price_qty_info' => [
													'hourly' => ['qty' => 8, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 1, 'price' => 7, 'min' => 2, 'max' => 10],
													'daily' => ['qty' => 15, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 25, 'min' => 1, 'max' => 10],
													'multi_day' => ['qty' => 15, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 30, 'price_multi' => 3, 'min' => 1, 'max' => 10],
												]
											]
										]
									]
								],
								1 => [
									'name' => 'Bicycle',
									'post_data' => [
										'common_data' => self::post_data(),
										'abprf_template' => 'group',
										'rent_rule' => 'daily',
										'property' => [
											0 => [
												'name' => 'BMC',
												'features' => 'fec_id_4,fec_id_7,fec_id_9,fec_id_11',
												'rent_rule' => 'daily',
												'gallery' => '10,20,30,40,50',
												'others' => ['icon' => '🚴‍♂️'],
												'price_qty_info' => [
													'hourly' => ['qty' => 20, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 3, 'min' => 2, 'max' => 5],
													'daily' => ['qty' => 20, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 10, 'min' => 1, 'max' => 10],
													'multi_day' => ['qty' => 20, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 10, 'price_multi' => 2, 'min' => 1, 'max' => 10],
												]
											],
											1 => [
												'name' => 'Trek',
												'features' => 'fec_id_3,fec_id_7,fec_id_9,fec_id_11,fec_id_10',
												'rent_rule' => 'daily',
												'gallery' => '10,20,30,40,50',
												'others' => ['icon' => '🚲'],
												'price_qty_info' => [
													'hourly' => ['qty' => 25, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 1, 'price' => 4, 'min' => 2, 'max' => 10],
													'daily' => ['qty' => 25, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 30, 'min' => 1, 'max' => 10],
													'multi_day' => ['qty' => 25, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 30, 'price_multi' => 2, 'min' => 1, 'max' => 10],
												]
											],
											2 => [
												'name' => 'Giant',
												'features' => 'fec_id_3,fec_id_7,fec_id_9,fec_id_11,fec_id_10',
												'rent_rule' => 'daily',
												'gallery' => '10,20,30,40,50',
												'others' => ['icon' => '🚴‍♂️'],
												'price_qty_info' => [
													'hourly' => ['qty' => 15, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 1, 'price' => 5, 'min' => 2, 'max' => 10],
													'daily' => ['qty' => 15, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 30, 'min' => 1, 'max' => 10],
													'multi_day' => ['qty' => 15, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 30, 'price_multi' => 2, 'min' => 1, 'max' => 10],
												]
											],
											3 => [
												'name' => 'Cannondale',
												'features' => 'fec_id_4,fec_id_7,fec_id_9,fec_id_11,fec_id_10',
												'rent_rule' => 'daily',
												'gallery' => '10,20,30,40,50',
												'others' => ['icon' => '🚲'],
												'price_qty_info' => [
													'hourly' => ['qty' => 15, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 1, 'price' => 6, 'min' => 2, 'max' => 10],
													'daily' => ['qty' => 15, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 20, 'min' => 1, 'max' => 10],
													'multi_day' => ['qty' => 15, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 30, 'price_multi' => 2, 'min' => 1, 'max' => 10],
												]
											],
											4 => [
												'name' => 'Specialized Bicycle',
												'features' => 'fec_id_3,fec_id_7,fec_id_9,fec_id_11,fec_id_10',
												'rent_rule' => 'daily',
												'gallery' => '10,20,30,40,50',
												'others' => ['icon' => '🚴‍♂️'],
												'price_qty_info' => [
													'hourly' => ['qty' => 18, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 1, 'price' => 7, 'min' => 2, 'max' => 10],
													'daily' => ['qty' => 15, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 25, 'min' => 1, 'max' => 10],
													'multi_day' => ['qty' => 15, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 30, 'price_multi' => 3, 'min' => 1, 'max' => 10],
												]
											]
										]
									]
								],
								2 => [
									'name' => 'Car',
									'post_data' => [
										'common_data' => self::post_data(),
										'abprf_template' => 'grid',
										'rent_rule' => 'multi_day',
										'property' => [
											0 => [
												'name' => 'Bugatti Chiron',
												'features' => 'fec_id_4,fec_id_7,fec_id_9,fec_id_11',
												'rent_rule' => 'multi_day',
												'gallery' => '10,20,30,40,50',
												'others' => ['icon' => '🚗️'],
												'price_qty_info' => [
													'hourly' => ['qty' => 20, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 10, 'min' => 2, 'max' => 5],
													'daily' => ['qty' => 20, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 100, 'min' => 1, 'max' => 10],
													'multi_day' => ['qty' => 20, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 100, 'price_multi' => 15, 'min' => 1, 'max' => 10],
												]
											],
											1 => [
												'name' => 'Ferrari SF90 Stradale',
												'features' => 'fec_id_3,fec_id_7,fec_id_9,fec_id_11,fec_id_10',
												'rent_rule' => 'multi_day',
												'gallery' => '10,20,30,40,50',
												'others' => ['icon' => 'fas fa-car'],
												'price_qty_info' => [
													'hourly' => ['qty' => 25, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 1, 'price' => 15, 'min' => 2, 'max' => 10],
													'daily' => ['qty' => 25, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 120, 'min' => 1, 'max' => 10],
													'multi_day' => ['qty' => 25, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 130, 'price_multi' => 20, 'min' => 1, 'max' => 10],
												]
											],
											2 => [
												'name' => 'Rolls-Royce Phantom',
												'features' => 'fec_id_2,fec_id_7,fec_id_9,fec_id_11,fec_id_10',
												'rent_rule' => 'multi_day',
												'gallery' => '10,20,30,40,50',
												'others' => ['icon' => '🚗️'],
												'price_qty_info' => [
													'hourly' => ['qty' => 5, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 1, 'price' => 50, 'min' => 2, 'max' => 10],
													'daily' => ['qty' => 15, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 200, 'min' => 1, 'max' => 10],
													'multi_day' => ['qty' => 15, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 200, 'price_multi' => 25, 'min' => 1, 'max' => 10],
												]
											],
											3 => [
												'name' => 'Tesla Model S Plaid',
												'features' => 'fec_id_1,fec_id_7,fec_id_9,fec_id_11,fec_id_10',
												'rent_rule' => 'multi_day',
												'gallery' => '10,20,30,40,50',
												'others' => ['icon' => 'fas fa-car'],
												'price_qty_info' => [
													'hourly' => ['qty' => 12, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 1, 'price' => 16, 'min' => 2, 'max' => 10],
													'daily' => ['qty' => 15, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 200, 'min' => 1, 'max' => 10],
													'multi_day' => ['qty' => 12, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 200, 'price_multi' => 20, 'min' => 1, 'max' => 10],
												]
											],
											4 => [
												'name' => 'Lamborghini Revuelto',
												'features' => 'fec_id_1,fec_id_7,fec_id_9,fec_id_11,fec_id_10',
												'rent_rule' => 'multi_day',
												'gallery' => '10,20,30,40,50',
												'others' => ['icon' => '🚗️'],
												'price_qty_info' => [
													'hourly' => ['qty' => 18, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 1, 'price' => 17, 'min' => 2, 'max' => 10],
													'daily' => ['qty' => 15, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 250, 'min' => 1, 'max' => 10],
													'multi_day' => ['qty' => 16, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 150, 'price_multi' => 30, 'min' => 1, 'max' => 10],
												]
											]
										]
									]
								],
							],
						]
					]
				];
			}
			//=============================//
			public static function post_data(): array {
				return [
					'rent_continue' => 'on',
					'display_sku' => 'on',
					'post_sku' => wp_rand(100, 999),
					'display_category' => 'on',
					'display_location' => 'on',
					'day_time_start' => '11:00',
					'day_time_end' => '10:00',
					'hour_threshold' => '20',
					'cut_off_date' => '10',
					'day_threshold' => '25',
					'active_global_dates' => 'on',
					'display_additional_services' => 'on',
					'active_global_additional' => 'on',
					'display_client_form' => 'on',
					'active_global_form' => 'on',
					'display_faq' => 'on',
					'active_global_faq' => 'on',
					'display_tc' => 'on',
					'active_global_tc' => 'on',
					'dummy' => 'on',
				];
			}
		}
		new ABPRF_Dashboard();
	}
