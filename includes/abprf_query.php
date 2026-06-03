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
				$order_table_name = '`' . str_replace( '`', '``', $order_table_name ) . '`';
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
				$total_order         = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $order_table_name" );
				$property_table_name = $wpdb->prefix . 'abprf_property';
				$property_table_name = '`' . str_replace( '`', '``', $property_table_name ) . '`';
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
				$total_property               = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $property_table_name" );
				$cpt                          = ABPRF_Function::get_cpt();
				$abprf_info                   = array();
				$configuration                = ABPRF_Configuration;
				$post_ids                     = self::get_post_id( [ 'status' => [ 'publish', 'draft', 'private', 'trash' ] ] );
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
				$abprf_info['label']          = isset( $configuration['label'] ) && $configuration['label'] ? $configuration['label'] : __( 'RentalForge', 'abprf-rental-forge' );
				$abprf_info['category_label'] = ABPRF_Function::category_label();
				$abprf_info['brand_icon']     = isset( $configuration['brand_icon'] ) && $configuration['brand_icon'] ? $configuration['brand_icon'] : 'fas fa-hammer';

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

			public static function get_post_id( $filters = [] ): array {
				$post_type = array_key_exists( 'cpt', $filters ) && ! empty( $filters['cpt'] ) ? $filters['cpt'] : ABPRF_Function::get_cpt();
				$show      = array_key_exists( 'posts_per_page', $filters ) && ! empty( $filters['posts_per_page'] ) ? $filters['posts_per_page'] : - 1;
				$page      = array_key_exists( 'paged', $filters ) && ! empty( $filters['paged'] ) ? $filters['paged'] : 1;
				$status    = array_key_exists( 'status', $filters ) && ! empty( $filters['status'] ) ? $filters['status'] : 'publish';
				//=====================//
				$cat_id    = array_key_exists( 'cat_id', $filters ) && ! empty( $filters['cat_id'] ) ? $filters['cat_id'] : null;
				$loc_id    = array_key_exists( 'loc_id', $filters ) && ! empty( $filters['loc_id'] ) ? $filters['loc_id'] : null;
				$rent_rule = array_key_exists( 'rent_rule', $filters ) && ! empty( $filters['rent_rule'] ) ? $filters['rent_rule'] : null;
				$cat_query = ! empty( $cat_id ) ? array( 'key' => 'abprf_category', 'value' => $cat_id, 'compare' => '=' ) : '';
				$loc_query = ! empty( $loc_id ) ? array( 'key' => 'abprf_location', 'value' => $loc_id, 'compare' => 'LIKE' ) : '';
				$rent_rule = ! empty( $rent_rule ) ? array( 'key' => 'rent_rule', 'value' => $rent_rule, 'compare' => '=', 'type' => 'CHAR' ) : '';
				$all_data  = get_posts( array(
					'fields' => 'ids',
					'post_type' => $post_type,
					'posts_per_page' => $show,
					'paged' => $page,
					'post_status' => $status,
					'meta_query' => array( 'relation' => 'AND', $cat_query, $loc_query, $rent_rule )
				) );

				return array_unique( $all_data );
			}

			public static function get_property( $filters = array(), $count = false ) {
				global $wpdb;
				$table_name = $wpdb->prefix . 'abprf_property';
				$conditions = [];
				$params     = [];
				$limit      = array_key_exists( 'limit', $filters ) && ! empty( $filters['limit'] ) ? $filters['limit'] : 0;
				$offset     = array_key_exists( 'offset', $filters ) && ! empty( $filters['offset'] ) ? $filters['offset'] : 0;
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
					$params[]     = intval( $property_id );
				}
				/***************/
				$rent_continue = array_key_exists( 'rent_continue', $filters ) && ! empty( $filters['rent_continue'] ) ? $filters['rent_continue'] : null;
				if ( ! empty( $rent_continue ) ) {
					$conditions[] = "rent_continue = %s";
					$params[]     = trim( $rent_continue );
				}
				/***************/
				$rent_rule = array_key_exists( 'rent_rule', $filters ) && ! empty( $filters['rent_rule'] ) ? $filters['rent_rule'] : null;
				if ( ! empty( $rent_rule ) ) {
					$conditions[] = "rent_rule = %s";
					$params[]     = trim( $rent_rule );
				}
				/***************/
				$brand_id = array_key_exists( 'brand_id', $filters ) && ! empty( $filters['brand_id'] ) ? $filters['brand_id'] : null;
				if ( ! empty( $brand_id ) ) {
					$conditions[] = "brand = %s";
					$params[]     = trim( $brand_id );
				}
				/***************/
				$category = array_key_exists( 'category', $filters ) && ! empty( $filters['category'] ) ? $filters['category'] : null;
				$cat_id   = array_key_exists( 'cat_id', $filters ) && ! empty( $filters['cat_id'] ) ? $filters['cat_id'] : null;
				$category = empty( $cat_id ) ? $category : $cat_id;
				if ( ! empty( $category ) ) {
					$conditions[] = "category = %s";
					$params[]     = trim( $category );
				}
				/***************/
				$location = array_key_exists( 'location', $filters ) && ! empty( $filters['location'] ) ? $filters['location'] : null;
				$loc_id   = array_key_exists( 'loc_id', $filters ) && ! empty( $filters['loc_id'] ) ? $filters['loc_id'] : null;
				$location = empty( $loc_id ) ? $location : $loc_id;
				if ( ! empty( $location ) ) {
					$conditions[] = "FIND_IN_SET(%s, location)";
					$params[]     = trim( $location );
				}
				/***************/
				$status = array_key_exists( 'status', $filters ) && ! empty( $filters['status'] ) ? $filters['status'] : null;
				if ( ! empty( $status ) ) {
					$conditions[] = "status = %s";
					$params[]     = trim( $status );
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
					// phpcs:disable WordPress.DB.DirectDatabaseQuery.DirectQuery
					// phpcs:disable WordPress.DB.DirectDatabaseQuery.NoCaching
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

			public static function get_item_query( $item_id, $key = '*' ) {
				if ( ! empty( $item_id ) && $item_id > 0 ) {
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

			public static function get_booking_query( $filters = array(), $limit = 0, $offset = 0, $count = false ) {
				global $wpdb;
				$table_name        = $wpdb->prefix . 'abprf_orders';
				$conditions        = [];
				$params            = [];
				$status            = array_key_exists( 'status', $filters ) && ! empty( $filters['status'] ) ? sanitize_text_field( $filters['status'] ) : null;
				$booked_status     = $status ?: ABPRF_Function::booking_status();
				$booked_status     = $booked_status ? explode( ',', $booked_status ) : [];
				$query_status      = current( $booked_status ) == 'all' ? '' : implode( ',', array_fill( 0, count( $booked_status ), '%s' ) );
				$property_id       = array_key_exists( 'property_id', $filters ) && ! empty( $filters['property_id'] ) ? intval( $filters['property_id'] ) : null;
				$ex_id             = array_key_exists( 'ex_id', $filters ) && ! empty( $filters['ex_id'] ) ? intval( $filters['ex_id'] ) : null;
				$post_id           = array_key_exists( 'post_id', $filters ) && ! empty( $filters['post_id'] ) ? intval( $filters['post_id'] ) : null;
				$user_id           = array_key_exists( 'user_id', $filters ) && ! empty( $filters['user_id'] ) ? intval( $filters['user_id'] ) : null;
				$item_id           = array_key_exists( 'item_id', $filters ) && ! empty( $filters['item_id'] ) ? intval( $filters['item_id'] ) : null;
				$order_id          = array_key_exists( 'order_id', $filters ) && ! empty( $filters['order_id'] ) ? intval( $filters['order_id'] ) : null;
				$location          = array_key_exists( 'location', $filters ) && ! empty( $filters['location'] ) ? intval( $filters['location'] ) : null;
				$start_time        = array_key_exists( 'start_time', $filters ) && ! empty( $filters['start_time'] ) ? gmdate( 'Y-m-d H:i:s', strtotime( $filters['start_time'] ) ) : null;
				$end_time          = array_key_exists( 'end_time', $filters ) && ! empty( $filters['end_time'] ) ? gmdate( 'Y-m-d H:i:s', strtotime( $filters['end_time'] ) ) : null;
				$order_date        = array_key_exists( 'order_date', $filters ) && ! empty( $filters['order_date'] ) ? gmdate( 'Y-m-d', strtotime( $filters['order_date'] ) ) : '';
				$booking_time_from = array_key_exists( 'booking_time_from', $filters ) && ! empty( $filters['booking_time_from'] ) ? gmdate( 'Y-m-d', strtotime( $filters['booking_time_from'] ) ) : null;
				$booking_time_to   = array_key_exists( 'booking_time_to', $filters ) && ! empty( $filters['booking_time_to'] ) ? gmdate( 'Y-m-d', strtotime( $filters['booking_time_to'] ) ) : null;
				$order_time_from   = array_key_exists( 'order_date_from', $filters ) && ! empty( $filters['order_date_from'] ) ? gmdate( 'Y-m-d', strtotime( $filters['order_date_from'] ) ) : null;
				$order_time_to     = array_key_exists( 'order_date_to', $filters ) && ! empty( $filters['order_date_to'] ) ? gmdate( 'Y-m-d', strtotime( $filters['order_date_to'] ) ) : null;
				$billing_name      = array_key_exists( 'billing_name', $filters ) && ! empty( $filters['billing_name'] ) ? '%' . sanitize_text_field( $filters['billing_name'] ) . '%' : null;
				$billing_email     = array_key_exists( 'billing_email', $filters ) && ! empty( $filters['billing_email'] ) ? '%' . sanitize_text_field( $filters['billing_email'] ) . '%' : null;
				$billing_phone     = array_key_exists( 'billing_phone', $filters ) && ! empty( $filters['billing_phone'] ) ? '%' . sanitize_text_field( $filters['billing_phone'] ) . '%' : null;
				$order_by          = array_key_exists( 'order_by', $filters ) && ! empty( $filters['order_by'] ) ? sanitize_sql_orderby( $filters['order_by'] ) : 'order_id';
				$order_dir         = array_key_exists( 'order_dir', $filters ) && in_array( strtoupper( $filters['order_dir'] ), [ 'ASC', 'DESC' ] ) ? strtoupper( $filters['order_dir'] ) : 'DESC';
				if ( ! empty( $query_status ) ) {
					$conditions[] = "order_status IN ($query_status)";
					$params       = array_merge( $params, $booked_status );
				}
				if ( ! empty( $property_id ) ) {
					$conditions[] = "JSON_CONTAINS(property_id, %s)";
					$params[]     = json_encode( $property_id );
				}
				if ( ! empty( $ex_id ) ) {
					$conditions[] = "JSON_CONTAINS(ex_id, %s)";
					$params[]     = json_encode( $ex_id );
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
				if ( ! empty( $order_id ) ) {
					$conditions[] = "order_id = %d";
					$params[]     = $order_id;
				}
				if ( ! empty( $location ) ) {
					$conditions[] = "JSON_CONTAINS(location, %s)";
					$params[]     = json_encode( $location );
				}
				if ( ! empty( $start_time ) && ! empty( $end_time ) ) {
					$conditions[] = "(book_from < %s AND book_to > %s)";
					$params[]     = $end_time;
					$params[]     = $start_time;
				} else {
					if ( ! empty( $start_time ) ) {
						$conditions[] = "DATE(start_time) = %s ";
						$params[]     = $start_time;
					}
				}
				if ( ! empty( $order_date ) ) {
					$conditions[] = "DATE(created_at) = %s ";
					$params[]     = $order_date;
				}
				if ( ! empty( $booking_time_from ) && ! empty( $booking_time_to ) ) {
					$conditions[] = "DATE(start_time) BETWEEN %s AND %s";
					$params[]     = $booking_time_from;
					$params[]     = $booking_time_to;
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

			public static function get_sold_qty( $filters = [] ) {
				$sold_qty      = 0;
				$booking_lists = self::get_booking_query( $filters );
				if ( ! empty( $booking_lists ) ) {
					$id = array_key_exists( 'property_id', $filters ) ? $filters['property_id'] : '';
					foreach ( $booking_lists as $booking_list ) {
						$property_ids = array_key_exists( 'property_id', $booking_list ) ? $booking_list['property_id'] : '';
						$property_ids = ! empty( $property_ids ) ? json_decode( $property_ids, true ) : [];
						$ticket_infos = array_key_exists( 'property_info', $booking_list ) ? $booking_list['property_info'] : '';
						$ticket_infos = ! empty( $ticket_infos ) ? json_decode( $ticket_infos, true ) : [];
						if ( ! empty( $id ) ) {
							if ( in_array( $id, $property_ids ) && array_key_exists( $id, $ticket_infos ) ) {
								foreach ( $ticket_infos as $key => $ticket_info ) {
									if ( $key == $id ) {
										$qty      = array_key_exists( 'qty', $ticket_info ) ? $ticket_info['qty'] : 1;
										$sold_qty = $sold_qty + $qty;
									}
								}
							}
						} else {
							foreach ( $ticket_infos as $ticket_info ) {
								$qty      = array_key_exists( 'qty', $ticket_info ) ? $ticket_info['qty'] : 1;
								$sold_qty = $sold_qty + $qty;
							}
						}
					}
				}

				return $sold_qty;
			}

			public static function get_sold_qty_ex( $filters = [] ) {
				$sold_qty      = 0;
				$booking_lists = self::get_booking_query( $filters );
				if ( ! empty( $booking_lists ) ) {
					$id = array_key_exists( 'ex_id', $filters ) ? $filters['ex_id'] : '';
					foreach ( $booking_lists as $booking_list ) {
						$ex_ids = array_key_exists( 'ex_id', $booking_list ) ? $booking_list['ex_id'] : '';
						$ex_ids = ! empty( $ex_ids ) ? json_decode( $ex_ids, true ) : [];
						$additional_infos = array_key_exists( 'ex_info', $booking_list ) ? $booking_list['ex_info'] : '';
						$additional_infos = ! empty( $additional_infos ) ? json_decode( $additional_infos, true ) : [];
						if ( ! empty( $id ) ) {
							if ( in_array( $id, $ex_ids ) && array_key_exists( $id, $additional_infos ) ) {
								foreach ( $additional_infos as $key => $additional_info ) {
									if ( $key == $id ) {
										$qty      = array_key_exists( 'qty', $additional_infos ) ? $additional_infos['qty'] : 1;
										$sold_qty = $sold_qty + $qty;
									}
								}
							}
						} else {
							foreach ( $additional_infos as $additional_info ) {
								$qty      = array_key_exists( 'qty', $additional_info ) ? $additional_info['qty'] : 1;
								$sold_qty = $sold_qty + $qty;
							}
						}
					}
				}

				return $sold_qty;
			}
		}
		new ABPRF_Query();
	}