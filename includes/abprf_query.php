<?php
	if ( ! defined( 'ABSPATH' ) ) {
		die;
	} // Cannot access pages directly
	if ( ! class_exists( 'ABPRF_Query' ) ) {
		class ABPRF_Query {
			public function __construct() {
			}

			public static function get_info() {
				global $wpdb;
				$order_table_name = $wpdb->prefix . 'abprf_orders';
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
				$total_order         = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM %d", $order_table_name ) );
				$property_table_name = $wpdb->prefix . 'abprf_property';
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
				$total_property               = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM %d", $property_table_name ) );
				$cpt                          = ABPRF_Function::get_cpt();
				$abprf_info                   = array();
				$abprf_configuration          = ABPRF_Function::get_option( 'abprf_configuration' );
				$post_ids                     = self::get_all_post_id( $cpt, - 1, 1, [ 'publish', 'draft', 'private', 'trash' ] );
				$post_counts                  = wp_count_posts( $cpt );
				$total_publish                = $post_counts->publish ?? 0;
				$total_draft                  = $post_counts->draft ?? 0;
				$total_private                = $post_counts->private ?? 0;
				$total_trash                  = $post_counts->trash ?? 0;
				$abprf_info['post_ids']       = $post_ids;
				$abprf_info['total_post']     = sizeof( $post_ids );
				$abprf_info['total_publish']  = $total_publish;
				$abprf_info['total_draft']    = $total_draft;
				$abprf_info['total_private']  = $total_private;
				$abprf_info['total_trash']    = $total_trash;
				$abprf_info['total_property'] = $total_property;
				$abprf_info['total_order']    = $total_order;
				$abprf_info['new_post_url']   = admin_url( 'post-new.php?post_type=' . $cpt );
				$abprf_info['label']          = isset( $abprf_configuration['label'] ) && $abprf_configuration['label'] ? $abprf_configuration['label'] : __( 'RentalForge', 'abprf-rental-forge' );
				$abprf_info['brand_icon']     = isset( $abprf_configuration['brand_icon'] ) && $abprf_configuration['brand_icon'] ? $abprf_configuration['brand_icon'] : 'fas fa-hammer';

				return $abprf_info;
			}

			public static function query_post_type( $post_type, $show = - 1, $page = 1 ): WP_Query {
				$args = array(
					'post_type' => $post_type,
					'posts_per_page' => $show,
					'paged' => $page,
					'post_status' => 'publish'
				);

				return new WP_Query( $args );
			}

			public static function get_all_post_id( $post_type, $show = - 1, $page = 1, $status = 'publish' ): array {
				$all_data = get_posts( array(
					'fields' => 'ids',
					'post_type' => $post_type,
					'posts_per_page' => $show,
					'paged' => $page,
					'post_status' => $status
				) );

				return array_unique( $all_data );
			}

			public static function get_property( $filters = array(), $limit = 0, $offset = 0, $count = false ) {
				global $wpdb;
				$table_name = $wpdb->prefix . 'abprf_property';
				$conditions = [];
				$params     = [];
				/***************/
				$post_id = array_key_exists( 'post_id', $filters ) && ! empty( $filters['post_id'] ) ? $filters['post_id'] : null;
				if ( ! empty( $post_id ) && $post_id !== 'all' ) {
					if ( $post_id == 'on' || $post_id == 'off' ) {
						$conditions[] = "rent_continue = %s";
						$params[]     = sanitize_text_field( $post_id );
					} else {
						$conditions[] = "post_id = %d";
						$params[]     = intval( $post_id );
					}
				}
				/***************/
				$property_id = array_key_exists( 'property_id', $filters ) && ! empty( $filters['property_id'] ) ? $filters['property_id'] : null;
				if ( ! empty( $property_id ) ) {
					$conditions[] = "id = %d";
					$params[]     = intval($property_id);
				}
				/***************/
				$rent_continue = array_key_exists( 'rent_continue', $filters ) && ! empty( $filters['rent_continue'] ) ? $filters['rent_continue'] : null;
				if ( ! empty( $rent_continue ) ) {
					$conditions[] = "rent_continue = %s";
					$params[]     = trim($rent_continue);
				}
				/***************/
				$price_rules = array_key_exists( 'price_rule', $filters ) && ! empty( $filters['price_rule'] ) ? $filters['price_rule'] : null;
				if ( ! empty( $price_rules ) ) {
					$price_rules = explode( ',', $price_rules );
					if ( is_array( $price_rules ) && sizeof( $price_rules ) > 0 ) {
						$price_rule_condition =[];
						foreach ( $price_rules as $rule ) {
							$price_rule_condition[]="FIND_IN_SET(%s, price_rule) > 0";
							$params[]             = trim($rule);
						}
						$conditions[] = '(' . implode( ' OR ', $price_rule_condition ) . ')';
					}
				}
				/***************/
				$status = array_key_exists( 'status', $filters ) && ! empty( $filters['status'] ) ? $filters['status'] : null;
				if ( ! empty( $status ) ) {
					$conditions[] = "status = %s";
					$params[]     = trim($status);
				}
				/***************/
				$order_by  = array_key_exists( 'order_by', $filters ) && ! empty( $filters['order_by'] ) ? sanitize_sql_orderby( $filters['order_by'] ) : 'created_at';
				$order_dir = array_key_exists( 'order_dir', $filters ) && in_array( strtoupper( $filters['order_dir'] ), [ 'ASC', 'DESC' ] ) ? strtoupper( $filters['order_dir'] ) : 'DESC';
				if ( $count ) {
					$sql = "SELECT COUNT(*) FROM $table_name";
				} else {
					$sql = "SELECT *FROM $table_name";
				}
				if ( ! empty( $conditions ) ) {
					$sql .= " WHERE " . implode( " AND ", $conditions );
				}
				$sql .= " ORDER BY $order_by $order_dir";
				if ( $limit > 0 ) {
					$sql      .= "  LIMIT %d OFFSET %d";
					$params[] = $limit;
					$params[] = $offset;
				}
				if ( ! empty( $params ) ) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching.
					// phpcs:ignore WordPress.PreparedSQL.NotPrepared -- Prepared later using wpdb->prepare()
					$query = $wpdb->prepare( $sql, ...$params );
				} else {
					$query = $sql;
				}
				if ( $count ) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching.
					// phpcs:ignore WordPress.PreparedSQL.NotPrepared -- Prepared later using wpdb->get_var()
					$results = $wpdb->get_var( $query );
				} else {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					// phpcs:ignore WordPress.PreparedSQL.NotPrepared -- Prepared later using wpdb->get_results()
					$results = $wpdb->get_results( $query, ARRAY_A );
				}

				return $results;
			}

			/********************************************/
			public static function get_sold_ticket( $post_id, $bp, $dp, $origin_date ) {
				$ticket_list = [];
				$results     = self::get_sold_query( $post_id, $bp, $dp, $origin_date, 'ticket' );
				if ( ! empty( $results ) ) {
					foreach ( $results as $value ) {
						foreach ( $value as $seats ) {
							$seats = json_decode( $seats );
							if ( is_array( $seats ) ) {
								foreach ( $seats as $seat ) {
									$ticket_list[] = $seat;
								}
							}
						}
					}
				}

				return array_unique( $ticket_list );
			}

			public static function get_sold_query( $post_id, $bp, $dp, $origin_date, $key = '*' ) {
				$routes = ABPRF_Function::get_post_info( $post_id, 'route_direction', [] );
				if ( sizeof( $routes ) > 0 ) {
					global $wpdb;
					$table_name      = $wpdb->prefix . 'abprf_orders';
					$allowed_columns = array( 'id', 'price', 'qty', 'total', 'ticket', '*' );
					if ( ! in_array( $key, $allowed_columns, true ) ) {
						return array(); // or wp_die()
					}
					$date                = gmdate( 'Y-m-d', strtotime( $origin_date ) );
					$time                = gmdate( 'H:i', strtotime( $origin_date ) );
					$booked_status       = ABPRF_Function::get_options( 'abprf_configuration', 'booked_status', 'wc-processing,wc-completed' );
					$booked_status       = $booked_status ? explode( ',', $booked_status ) : [];
					$status_placeholders = implode( ',', array_fill( 0, count( $booked_status ), '%s' ) );
					$sp                  = array_search( $bp, $routes );
					$ep                  = array_search( $dp, $routes );
					$bp_array            = array_slice( $routes, 0, $ep );
					$dp_array            = array_slice( $routes, $sp + 1 );
					$bp_placeholders     = implode( ',', array_fill( 0, count( $bp_array ), '%s' ) );
					$dp_placeholders     = implode( ',', array_fill( 0, count( $dp_array ), '%s' ) );
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Prepared later using wpdb->get_var()
					$query = $wpdb->prepare( "SELECT {$key} FROM {$table_name}  WHERE post_id = %d AND bp IN ($bp_placeholders)  AND dp IN ($dp_placeholders) AND order_status IN ($status_placeholders) AND DATE(origin_time) = %s AND TIME(origin_time) = %s", array_merge( [ $post_id ], $bp_array, $dp_array, $booked_status, [ $date ], [ $time ] ) );
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Prepared later using wpdb->get_results()
					return $wpdb->get_results( $query, ARRAY_A );
				}

				return null;
			}

			public static function get_item_query( $item_id, $key = '*' ) {
				if ( $item_id && $item_id > 0 ) {
					global $wpdb;
					$table_name = $wpdb->prefix . 'abprf_orders';
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Prepared later using wpdb->get_var()
					$query = $wpdb->prepare( "SELECT  {$key}  FROM {$table_name}  WHERE item_id = %d ", array_merge( [ $item_id ] ) );
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Prepared later using wpdb->get_results()
					$results = $wpdb->get_results( $query, ARRAY_A );

					return ( ! empty( $results ) && sizeof( $results ) > 0 ) ? current( $results ) : [];
				}

				return null;
			}

			public static function get_sold_info( $post_id, $bp, $dp, $origin_date, $seat_type ) {
				$all_info = [];
				$results  = self::get_sold_query( $post_id, $bp, $dp, $origin_date );
				if ( ! empty( $results ) ) {
					$ticket_list     = [];
					$seat_list       = [];
					$additional_list = [];
					foreach ( $results as $value ) {
						$tickets = array_key_exists( 'ticket', $value ) ? $value['ticket'] : '';
						if ( ! empty( $tickets ) ) {
							$seats = json_decode( $tickets );
							if ( is_array( $seats ) ) {
								foreach ( $seats as $seat ) {
									$seat_list[] = $seat;
								}
							}
						}
						if ( $seat_type == 'ticket_type' ) {
							$ticket_infos = array_key_exists( 'ticket_info', $value ) ? $value['ticket_info'] : '';
							if ( ! empty( $ticket_infos ) ) {
								$ticket_infos = json_decode( $ticket_infos );
								foreach ( $ticket_infos as $ticket_info ) {
									$ticket_list[ $ticket_info->type ] = array_key_exists( $ticket_info->type, $ticket_list ) ? $ticket_list[ $ticket_info->type ] + $ticket_info->qty : $ticket_info->qty;
								}
							}
						}
						$additional_infos = array_key_exists( 'additional_info', $value ) ? $value['additional_info'] : '';
						if ( ! empty( $additional_infos ) ) {
							$additional_infos = json_decode( $additional_infos );
							foreach ( $additional_infos as $additional_info ) {
								if ( is_object( $additional_info ) ) {
									$additional_info = get_object_vars( $additional_info );
								}
								if ( is_array( $additional_info ) && sizeof( $additional_info ) > 0 ) {
									foreach ( $additional_info as $key => $additional ) {
										$additional_list[ $key ] = array_key_exists( $key, $additional_list ) ? $additional_list[ $key ] + $additional->qty : $additional->qty;
									}
								}
							}
						}
					}
					$all_info['seat']       = $seat_list;
					$all_info['ticket']     = $ticket_list;
					$all_info['additional'] = $additional_list;
				}

				return $all_info;
			}

			public static function get_booking_query( $filters = array(), $limit = 0, $offset = 0, $count = false ) {
				global $wpdb;
				$table_name       = $wpdb->prefix . 'abprf_orders';
				$conditions       = [];
				$params           = [];
				$status           = array_key_exists( 'status', $filters ) && ! empty( $filters['status'] ) ? sanitize_text_field( $filters['status'] ) : null;
				$booked_status    = $status ?: ABPRF_Function::get_options( 'abprf_configuration', 'booked_status', 'wc-processing,wc-completed' );
				$booked_status    = $booked_status ? explode( ',', $booked_status ) : [];
				$query_status     = current( $booked_status ) == 'all' ? '' : implode( ',', array_fill( 0, count( $booked_status ), '%s' ) );
				$post_id          = array_key_exists( 'post_id', $filters ) && ! empty( $filters['post_id'] ) ? intval( $filters['post_id'] ) : null;
				$user_id          = array_key_exists( 'user_id', $filters ) && ! empty( $filters['user_id'] ) ? intval( $filters['user_id'] ) : null;
				$item_id          = array_key_exists( 'item_id', $filters ) && ! empty( $filters['item_id'] ) ? intval( $filters['item_id'] ) : null;
				$origin_time      = array_key_exists( 'origin_time', $filters ) && ! empty( $filters['origin_time'] ) ? gmdate( 'Y-m-d', strtotime( $filters['origin_time'] ) ) : null;
				$origin_time_from = array_key_exists( 'origin_time_from', $filters ) && ! empty( $filters['origin_time_from'] ) ? gmdate( 'Y-m-d', strtotime( $filters['origin_time_from'] ) ) : null;
				$origin_time_to   = array_key_exists( 'origin_time_to', $filters ) && ! empty( $filters['origin_time_to'] ) ? gmdate( 'Y-m-d', strtotime( $filters['origin_time_to'] ) ) : null;
				$bp               = array_key_exists( 'bp', $filters ) && ! empty( $filters['bp'] ) ? sanitize_text_field( $filters['bp'] ) : null;
				$bp_time          = array_key_exists( 'bp_time', $filters ) && ! empty( $filters['bp_time'] ) ? gmdate( 'Y-m-d', strtotime( $filters['bp_time'] ) ) : null;
				$dp               = array_key_exists( 'dp', $filters ) && ! empty( $filters['dp'] ) ? sanitize_text_field( $filters['dp'] ) : null;
				$order_id         = array_key_exists( 'order_id', $filters ) && ! empty( $filters['order_id'] ) ? intval( $filters['order_id'] ) : null;
				$order_time       = array_key_exists( 'order_date', $filters ) && ! empty( $filters['order_date'] ) ? gmdate( 'Y-m-d', strtotime( $filters['order_date'] ) ) : '';
				$order_time_from  = array_key_exists( 'order_date_from', $filters ) && ! empty( $filters['order_date_from'] ) ? gmdate( 'Y-m-d', strtotime( $filters['order_date_from'] ) ) : null;
				$order_time_to    = array_key_exists( 'order_date_to', $filters ) && ! empty( $filters['order_date_to'] ) ? gmdate( 'Y-m-d', strtotime( $filters['order_date_to'] ) ) : null;
				$billing_name     = array_key_exists( 'billing_name', $filters ) && ! empty( $filters['billing_name'] ) ? '%' . sanitize_text_field( $filters['billing_name'] ) . '%' : null;
				$billing_email    = array_key_exists( 'billing_email', $filters ) && ! empty( $filters['billing_email'] ) ? '%' . sanitize_text_field( $filters['billing_email'] ) . '%' : null;
				$billing_phone    = array_key_exists( 'billing_phone', $filters ) && ! empty( $filters['billing_phone'] ) ? '%' . sanitize_text_field( $filters['billing_phone'] ) . '%' : null;
				$order_by         = array_key_exists( 'order_by', $filters ) && ! empty( $filters['order_by'] ) ? sanitize_sql_orderby( $filters['order_by'] ) : 'order_id';
				$order_dir        = array_key_exists( 'order_dir', $filters ) && in_array( strtoupper( $filters['order_dir'] ), [ 'ASC', 'DESC' ] ) ? strtoupper( $filters['order_dir'] ) : 'DESC';
				if ( ! empty( $query_status ) ) {
					$conditions[] = "order_status IN ($query_status)";
					$params       = array_merge( $params, $booked_status );
				}
				if ( ! empty( $post_id ) ) {
					$conditions[] = "post_id = %d";
					$params[]     = $post_id;
				}
				if ( ! empty( $user_id ) ) {
					$conditions[] = "user_id = %d";
					$params[]     = $user_id;
				}
				if ( ! empty( $item_id ) ) {
					$conditions[] = "item_id = %d";
					$params[]     = $item_id;
				}
				if ( ! empty( $origin_time ) ) {
					$conditions[] = "DATE(origin_time) = %s ";
					$params[]     = $origin_time;
				}
				if ( ! empty( $origin_time_from ) && ! empty( $origin_time_to ) ) {
					$conditions[] = "DATE(origin_time) BETWEEN %s AND %s";
					$params[]     = $origin_time_from;
					$params[]     = $origin_time_to;
				}
				if ( ! empty( $bp ) ) {
					$conditions[] = "bp = %s";
					$params[]     = $bp;
				}
				if ( ! empty( $bp_time ) ) {
					$conditions[] = "DATE(bp_time) = %s ";
					$params[]     = $bp_time;
				}
				if ( ! empty( $dp ) ) {
					$conditions[] = "dp = %s";
					$params[]     = $dp;
				}
				if ( ! empty( $order_id ) ) {
					$conditions[] = "order_id = %d";
					$params[]     = $order_id;
				}
				if ( ! empty( $order_time ) ) {
					$conditions[] = "DATE(created_at) = %s ";
					$params[]     = $order_time;
				}
				if ( ! empty( $order_time_from ) && ! empty( $order_time_to ) ) {
					$conditions[] = "DATE(created_at) BETWEEN %s AND %s";
					$params[]     = $order_time_from;
					$params[]     = $order_time_to;
				}
				if ( ! empty( $billing_name ) ) {
					$conditions[] = "billing_name LIKE %s";
					$params[]     = $billing_name;
				}
				if ( ! empty( $billing_email ) ) {
					$conditions[] = "billing_email LIKE %s";
					$params[]     = $billing_email;
				}
				if ( ! empty( $billing_phone ) ) {
					$conditions[] = "billing_phone LIKE %s";
					$params[]     = $billing_phone;
				}
				if ( $count ) {
					$sql = "SELECT COUNT(*) FROM $table_name";
				} else {
					$sql = "SELECT *FROM $table_name";
				}
				if ( ! empty( $conditions ) ) {
					$sql .= " WHERE " . implode( " AND ", $conditions );
				}
				if ( $limit > 0 ) {
					$sql      .= " ORDER BY $order_by $order_dir LIMIT %d OFFSET %d";
					$params[] = $limit;
					$params[] = $offset;
				}
				// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Prepared later using wpdb->prepare()
				$query = $wpdb->prepare( $sql, ...$params );
				if ( $count ) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Prepared later using wpdb->get_var()
					$results = $wpdb->get_var( $query );
				} else {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- Prepared later using wpdb->get_results()
					$results = $wpdb->get_results( $query );
				}

				return $results;
			}

			public static function get_equipment_id( $bp = '', $dp = '', $cat = '', $org = '' ): array {
				$bus_ids   = [];
				$bp_query  = ! empty( $bp ) ? array( 'key' => 'abptm_bp', 'value' => $bp, 'compare' => 'LIKE' ) : '';
				$dp_query  = ! empty( $dp ) ? array( 'key' => 'abptm_dp', 'value' => $dp, 'compare' => 'LIKE' ) : '';
				$cat_query = ! empty( $cat ) ? array( 'key' => 'category', 'value' => $cat, 'compare' => '=', 'type' => 'CHAR' ) : '';
				$org_query = ! empty( $org ) ? array( 'key' => 'organizer', 'value' => $org, 'compare' => '=', 'type' => 'CHAR' ) : '';
				$args      = array(
					'post_type' => array( ABPRF_Function::get_cpt() ),
					'posts_per_page' => - 1,
					'order' => 'ASC',
					'post_status' => 'publish',
					'meta_query' => array( 'relation' => 'AND', $bp_query, $dp_query, $cat_query, $org_query )
				);
				$bus_query = new WP_Query( $args );
				while ( $bus_query->have_posts() ) {
					$bus_query->the_post();
					$id = get_the_id();
					if ( $bp && $dp ) {
						$abptm_bp = ABPRF_Function::get_post_info( $id, 'abptm_bp', [] );
						$abptm_dp = ABPRF_Function::get_post_info( $id, 'abptm_dp', [] );
						if ( in_array( $bp, $abptm_bp ) && in_array( $dp, $abptm_dp ) ) {
							$bus_ids[] = $id;
						}
					} elseif ( $bp ) {
						$abptm_bp = ABPRF_Function::get_post_info( $id, 'abptm_bp', [] );
						if ( in_array( $bp, $abptm_bp ) ) {
							$bus_ids[] = $id;
						}
					} elseif ( $dp ) {
						$abptm_dp = ABPRF_Function::get_post_info( $id, 'abptm_dp', [] );
						if ( in_array( $dp, $abptm_dp ) ) {
							$bus_ids[] = $id;
						}
					} else {
						$bus_ids[] = $id;
					}
				}
				wp_reset_postdata();

				return $bus_ids;
			}
		}
		new ABPRF_Query();
	}