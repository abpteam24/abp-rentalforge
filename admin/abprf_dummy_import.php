<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	if ( ! class_exists( 'ABPRF_Dummy_Import' ) ) {
		class ABPRF_Dummy_Import {
			public function __construct() {
				add_action( 'wp_ajax_abprf_import_dummy', array( $this, 'import_dummy' ) );
				//=============================//
				add_action( 'wp_ajax_abprf_remove_dummy', array( $this, 'remove_dummy' ) );
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
			public function remove_dummy(): void {
				if (!check_ajax_referer('abprf_admin_ajax_nonce', 'nonce', false)) {
					wp_send_json_error(['html' => '', 'msg' => __('Invalid security token.', 'abp-rentalforge')], 403);
				}
				if (!current_user_can('manage_options')) {
					wp_send_json_error(['html' => '', 'msg' => __('Insufficient permissions.', 'abp-rentalforge')], 403);
				}
				try {
					self::delete_dummy_data();
					flush_rewrite_rules();
					wp_send_json_success([
						'msg' => esc_html__('Dummy data removed successfully!', 'abp-rentalforge')
					]);
				} catch (Exception $e) {
					wp_send_json_error([
						'msg' => esc_html__('An error occurred while removing dummy data.', 'abp-rentalforge')
					]);
				}
			}
			//=============================//
			public static function delete_dummy_data(): void {
				global $wpdb;
				$table_name = $wpdb->prefix . 'abprf_property';
				$dummy_posts = get_posts([
					'post_type' => ABPRF_Function::get_cpt(),
					'meta_key' => 'dummy',
					'meta_value' => 'on',
					'posts_per_page' => -1,
					'post_status' => 'any',
					'fields' => 'ids',
				]);
				foreach ($dummy_posts as $post_id) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					$wpdb->delete($table_name, ['post_id' => intval($post_id)]);
					wp_delete_post($post_id, true);
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
						if (empty($option_data)) {
							update_option($option, $dummy_option);
						}
					}
					ABPRF_Feature::update_feature_js();
				}
				if (isset($dummy_infos['custom_post'])) {
					$abprf_location = ABPRF_Function::get_option('abprf_location');
					$abprf_brand = ABPRF_Function::get_option('abprf_brand');
					$abprf_category = ABPRF_Function::get_option('abprf_category');
					$_category = !empty($_category) ? explode(',', $_category) : array_keys((array) ($dummy_infos['custom_post']['abprf_post'] ?? []));
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
										$cat_term = get_term_by('slug', $cat_key, 'abprf_category');
										if ($cat_term && !is_wp_error($cat_term)) {
											$post_cat = $cat_term->term_id;
										}
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
												if (array_key_exists('brand', $property)) {
													$brand_name = sanitize_text_field($property['brand']);
													if ('' === $brand_name) {
														$post_brand = '';
													} else {
														$brand_term = get_term_by('name', $brand_name, 'abprf_brand');
														if ($brand_term && !is_wp_error($brand_term)) {
															$post_brand = $brand_term->term_id;
														}
													}
												}
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
												'brand' => '',
												'features' => 'fec_id_4,fec_id_9,fec_id_10,fec_id_11',
												'rent_rule' => 'hourly',
												'gallery' => '',
												'others' => ['icon' => '🏍️'],
												'price_qty_info' => [
													'hourly' => ['qty' => 10, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 8, 'min' => 2, 'max' => 5],
													'daily' => ['qty' => 5, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 60, 'min' => 1, 'max' => 10],
													'multi_day' => ['qty' => 5, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 60, 'price_multi' => 10, 'min' => 1, 'max' => 10],
												],
											],
											1 => [
												'name' => 'Yamaha YZF-R1',
												'brand' => 'Yamaha',
												'features' => 'fec_id_4,fec_id_5,fec_id_9,fec_id_11',
												'rent_rule' => 'hourly',
												'gallery' => '',
												'others' => ['icon' => 'fas fa-motorcycle'],
												'price_qty_info' => [
													'hourly' => ['qty' => 12, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 7, 'min' => 2, 'max' => 10],
													'daily' => ['qty' => 6, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 55, 'min' => 1, 'max' => 10],
													'multi_day' => ['qty' => 6, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 55, 'price_multi' => 10, 'min' => 1, 'max' => 10],
												],
											],
											2 => [
												'name' => 'Kawasaki Ninja H2',
												'brand' => '',
												'features' => 'fec_id_4,fec_id_6,fec_id_9,fec_id_10',
												'rent_rule' => 'hourly',
												'gallery' => '',
												'others' => ['icon' => '🏍️'],
												'price_qty_info' => [
													'hourly' => ['qty' => 8, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 7, 'min' => 2, 'max' => 10],
													'daily' => ['qty' => 4, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 50, 'min' => 1, 'max' => 10],
													'multi_day' => ['qty' => 4, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 50, 'price_multi' => 10, 'min' => 1, 'max' => 10],
												],
											],
										],
									],
								],
								1 => [
									'name' => 'Car',
									'post_data' => [
										'common_data' => self::post_data(),
										'abprf_template' => 'grid',
										'rent_rule' => 'daily',
										'property' => [
											0 => [
												'name' => 'Tesla Model S Plaid',
												'brand' => 'Tesla',
												'features' => 'fec_id_1,fec_id_9,fec_id_10,fec_id_11',
												'rent_rule' => 'daily',
												'gallery' => '',
												'others' => ['icon' => '🚗'],
												'price_qty_info' => [
													'hourly' => ['qty' => 4, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 20, 'min' => 1, 'max' => 8],
													'daily' => ['qty' => 6, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 130, 'min' => 1, 'max' => 15],
													'multi_day' => ['qty' => 6, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 130, 'price_multi' => 25, 'min' => 1, 'max' => 21],
												],
											],
											1 => [
												'name' => 'BMW X5',
												'brand' => 'BMW',
												'features' => 'fec_id_2,fec_id_5,fec_id_13,fec_id_14',
												'rent_rule' => 'daily',
												'gallery' => '',
												'others' => ['icon' => 'fas fa-car'],
												'price_qty_info' => [
													'hourly' => ['qty' => 5, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 18, 'min' => 1, 'max' => 8],
													'daily' => ['qty' => 8, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 110, 'min' => 1, 'max' => 15],
													'multi_day' => ['qty' => 8, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 110, 'price_multi' => 20, 'min' => 1, 'max' => 21],
												],
											],
											2 => [
												'name' => 'Toyota Camry',
												'brand' => 'Toyota Motor',
												'features' => 'fec_id_4,fec_id_5,fec_id_7,fec_id_10',
												'rent_rule' => 'daily',
												'gallery' => '',
												'others' => ['icon' => '🚗'],
												'price_qty_info' => [
													'hourly' => ['qty' => 8, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 10, 'min' => 1, 'max' => 8],
													'daily' => ['qty' => 10, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 70, 'min' => 1, 'max' => 15],
													'multi_day' => ['qty' => 10, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 70, 'price_multi' => 15, 'min' => 1, 'max' => 21],
												],
											],
										],
									],
								],
								2 => [
									'name' => 'Bicycle',
									'post_data' => [
										'common_data' => self::post_data(),
										'abprf_template' => 'group',
										'rent_rule' => 'daily',
										'property' => [
											0 => [
												'name' => 'Trek Marlin 7',
												'brand' => '',
												'features' => 'fec_id_83,fec_id_85,fec_id_86',
												'rent_rule' => 'daily',
												'gallery' => '',
												'others' => ['icon' => '🚲'],
												'price_qty_info' => [
													'hourly' => ['qty' => 10, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 3, 'min' => 1, 'max' => 8],
													'daily' => ['qty' => 12, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 18, 'min' => 1, 'max' => 14],
													'multi_day' => ['qty' => 12, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 18, 'price_multi' => 5, 'min' => 1, 'max' => 14],
												],
											],
											1 => [
												'name' => 'Giant Escape 3',
												'brand' => '',
												'features' => 'fec_id_84,fec_id_85,fec_id_86',
												'rent_rule' => 'daily',
												'gallery' => '',
												'others' => ['icon' => '🚲'],
												'price_qty_info' => [
													'hourly' => ['qty' => 12, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 3, 'min' => 1, 'max' => 8],
													'daily' => ['qty' => 14, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 15, 'min' => 1, 'max' => 14],
													'multi_day' => ['qty' => 14, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 15, 'price_multi' => 5, 'min' => 1, 'max' => 14],
												],
											],
											2 => [
												'name' => 'Specialized Rockhopper',
												'brand' => '',
												'features' => 'fec_id_79,fec_id_83,fec_id_86',
												'rent_rule' => 'daily',
												'gallery' => '',
												'others' => ['icon' => '🚲'],
												'price_qty_info' => [
													'hourly' => ['qty' => 8, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 4, 'min' => 1, 'max' => 8],
													'daily' => ['qty' => 10, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 22, 'min' => 1, 'max' => 14],
													'multi_day' => ['qty' => 10, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 22, 'price_multi' => 5, 'min' => 1, 'max' => 14],
												],
											],
										],
									],
								],
							],
							'camera' => [
								0 => [
									'name' => 'Camera Kit',
									'post_data' => [
										'common_data' => self::post_data(),
										'abprf_template' => 'grid',
										'rent_rule' => 'daily',
										'property' => [
											0 => [
												'name' => 'Canon EOS R6 Mark II',
												'brand' => 'Canon',
												'features' => 'fec_id_15,fec_id_17,fec_id_19,fec_id_20',
												'rent_rule' => 'daily',
												'gallery' => '',
												'others' => ['icon' => '📷'],
												'price_qty_info' => [
													'hourly' => ['qty' => 8, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 6, 'min' => 1, 'max' => 8],
													'daily' => ['qty' => 6, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 45, 'min' => 1, 'max' => 10],
													'multi_day' => ['qty' => 6, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 45, 'price_multi' => 15, 'min' => 1, 'max' => 10],
												],
											],
											1 => [
												'name' => 'Sony A7 IV',
												'brand' => 'Sony',
												'features' => 'fec_id_15,fec_id_19,fec_id_20,fec_id_24',
												'rent_rule' => 'daily',
												'gallery' => '',
												'others' => ['icon' => '📷'],
												'price_qty_info' => [
													'hourly' => ['qty' => 7, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 7, 'min' => 1, 'max' => 8],
													'daily' => ['qty' => 5, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 50, 'min' => 1, 'max' => 10],
													'multi_day' => ['qty' => 5, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 50, 'price_multi' => 15, 'min' => 1, 'max' => 10],
												],
											],
											2 => [
												'name' => 'Nikon Z8',
												'brand' => 'Nikon',
												'features' => 'fec_id_15,fec_id_16,fec_id_19,fec_id_20',
												'rent_rule' => 'daily',
												'gallery' => '',
												'others' => ['icon' => 'fas fa-camera'],
												'price_qty_info' => [
													'hourly' => ['qty' => 6, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 8, 'min' => 1, 'max' => 8],
													'daily' => ['qty' => 4, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 55, 'min' => 1, 'max' => 10],
													'multi_day' => ['qty' => 4, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 55, 'price_multi' => 15, 'min' => 1, 'max' => 10],
												],
											],
										],
									],
								],
								1 => [
									'name' => 'Action Camera',
									'post_data' => [
										'common_data' => self::post_data(),
										'abprf_template' => 'grid',
										'rent_rule' => 'hourly',
										'property' => [
											0 => [
												'name' => 'GoPro HERO12 Black',
												'brand' => '',
												'features' => 'fec_id_16,fec_id_32,fec_id_34,fec_id_42',
												'rent_rule' => 'hourly',
												'gallery' => '',
												'others' => ['icon' => '📹'],
												'price_qty_info' => [
													'hourly' => ['qty' => 10, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 4, 'min' => 1, 'max' => 8],
													'daily' => ['qty' => 8, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 25, 'min' => 1, 'max' => 7],
													'multi_day' => ['qty' => 8, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 25, 'price_multi' => 10, 'min' => 1, 'max' => 7],
												],
											],
											1 => [
												'name' => 'Insta360 X4',
												'brand' => '',
												'features' => 'fec_id_16,fec_id_32,fec_id_34',
												'rent_rule' => 'hourly',
												'gallery' => '',
												'others' => ['icon' => '📹'],
												'price_qty_info' => [
													'hourly' => ['qty' => 8, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 5, 'min' => 1, 'max' => 8],
													'daily' => ['qty' => 6, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 30, 'min' => 1, 'max' => 7],
													'multi_day' => ['qty' => 6, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 30, 'price_multi' => 10, 'min' => 1, 'max' => 7],
												],
											],
											2 => [
												'name' => 'DJI Osmo Action 4',
												'brand' => 'DJI',
												'features' => 'fec_id_18,fec_id_32,fec_id_34,fec_id_41',
												'rent_rule' => 'hourly',
												'gallery' => '',
												'others' => ['icon' => '📹'],
												'price_qty_info' => [
													'hourly' => ['qty' => 9, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 4, 'min' => 1, 'max' => 8],
													'daily' => ['qty' => 7, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 28, 'min' => 1, 'max' => 7],
													'multi_day' => ['qty' => 7, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 28, 'price_multi' => 10, 'min' => 1, 'max' => 7],
												],
											],
										],
									],
								],
							],
							'drone' => [
								0 => [
									'name' => 'Drone',
									'post_data' => [
										'common_data' => self::post_data(),
										'abprf_template' => 'group',
										'rent_rule' => 'daily',
										'property' => [
											0 => [
												'name' => 'DJI Mavic 3 Pro',
												'brand' => 'DJI',
												'features' => 'fec_id_36,fec_id_37,fec_id_44,fec_id_46',
												'rent_rule' => 'daily',
												'gallery' => '',
												'others' => ['icon' => '🚁'],
												'price_qty_info' => [
													'hourly' => ['qty' => 5, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 12, 'min' => 1, 'max' => 8],
													'daily' => ['qty' => 4, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 75, 'min' => 1, 'max' => 10],
													'multi_day' => ['qty' => 4, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 75, 'price_multi' => 20, 'min' => 1, 'max' => 10],
												],
											],
											1 => [
												'name' => 'DJI Air 3',
												'brand' => 'DJI',
												'features' => 'fec_id_35,fec_id_41,fec_id_44,fec_id_47',
												'rent_rule' => 'daily',
												'gallery' => '',
												'others' => ['icon' => '🚁'],
												'price_qty_info' => [
													'hourly' => ['qty' => 6, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 9, 'min' => 1, 'max' => 8],
													'daily' => ['qty' => 5, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 55, 'min' => 1, 'max' => 10],
													'multi_day' => ['qty' => 5, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 55, 'price_multi' => 15, 'min' => 1, 'max' => 10],
												],
											],
											2 => [
												'name' => 'Autel EVO II Pro',
												'brand' => 'Autel Robotics',
												'features' => 'fec_id_16,fec_id_38,fec_id_44,fec_id_45',
												'rent_rule' => 'daily',
												'gallery' => '',
												'others' => ['icon' => '🚁'],
												'price_qty_info' => [
													'hourly' => ['qty' => 5, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 10, 'min' => 1, 'max' => 8],
													'daily' => ['qty' => 4, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 65, 'min' => 1, 'max' => 10],
													'multi_day' => ['qty' => 4, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 65, 'price_multi' => 20, 'min' => 1, 'max' => 10],
												],
											],
										],
									],
								],
							],
							'party-equipment' => [
								0 => [
									'name' => 'Party Equipment',
									'post_data' => [
										'common_data' => self::post_data(),
										'abprf_template' => 'group',
										'rent_rule' => 'daily',
										'property' => [
											0 => [
												'name' => 'DJ Sound System',
												'brand' => 'Pioneer DJ',
												'features' => 'fec_id_26,fec_id_27,fec_id_30,fec_id_31',
												'rent_rule' => 'daily',
												'gallery' => '',
												'others' => ['icon' => '🎧'],
												'price_qty_info' => [
													'hourly' => ['qty' => 3, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 25, 'min' => 1, 'max' => 8],
													'daily' => ['qty' => 3, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 120, 'min' => 1, 'max' => 7],
													'multi_day' => ['qty' => 3, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 120, 'price_multi' => 40, 'min' => 1, 'max' => 7],
												],
											],
											1 => [
												'name' => 'LED Stage Lighting',
												'brand' => '',
												'features' => 'fec_id_28,fec_id_29,fec_id_31',
												'rent_rule' => 'daily',
												'gallery' => '',
												'others' => ['icon' => '💡'],
												'price_qty_info' => [
													'hourly' => ['qty' => 4, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 15, 'min' => 1, 'max' => 8],
													'daily' => ['qty' => 4, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 80, 'min' => 1, 'max' => 7],
													'multi_day' => ['qty' => 4, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 80, 'price_multi' => 30, 'min' => 1, 'max' => 7],
												],
											],
											2 => [
												'name' => 'Wireless Microphone Set',
												'brand' => '',
												'features' => 'fec_id_25,fec_id_32,fec_id_33',
												'rent_rule' => 'daily',
												'gallery' => '',
												'others' => ['icon' => '🎤'],
												'price_qty_info' => [
													'hourly' => ['qty' => 6, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 8, 'min' => 1, 'max' => 8],
													'daily' => ['qty' => 6, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 35, 'min' => 1, 'max' => 7],
													'multi_day' => ['qty' => 6, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 35, 'price_multi' => 10, 'min' => 1, 'max' => 7],
												],
											],
										],
									],
								],
							],
							'resort' => [
								0 => [
									'name' => 'Resort Room',
									'post_data' => [
										'common_data' => self::post_data(),
										'abprf_template' => 'grid',
										'rent_rule' => 'daily',
										'property' => [
											0 => [
												'name' => 'Deluxe Room',
												'brand' => 'Hilton Worldwide',
												'features' => 'fec_id_53,fec_id_55,fec_id_56,fec_id_57',
												'rent_rule' => 'daily',
												'gallery' => '',
												'others' => ['icon' => '🛏️'],
												'price_qty_info' => [
													'hourly' => ['qty' => 5, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 25, 'min' => 1, 'max' => 8],
													'daily' => ['qty' => 10, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 140, 'min' => 1, 'max' => 21],
													'multi_day' => ['qty' => 10, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 140, 'price_multi' => 30, 'min' => 1, 'max' => 21],
												],
											],
											1 => [
												'name' => 'Executive Suite',
												'brand' => 'Marriott International',
												'features' => 'fec_id_49,fec_id_51,fec_id_57,fec_id_60',
												'rent_rule' => 'daily',
												'gallery' => '',
												'others' => ['icon' => '🛏️'],
												'price_qty_info' => [
													'hourly' => ['qty' => 4, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 40, 'min' => 1, 'max' => 8],
													'daily' => ['qty' => 6, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 220, 'min' => 1, 'max' => 21],
													'multi_day' => ['qty' => 6, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 220, 'price_multi' => 50, 'min' => 1, 'max' => 21],
												],
											],
											2 => [
												'name' => 'Pool Villa',
												'brand' => '',
												'features' => 'fec_id_52,fec_id_58,fec_id_59,fec_id_61',
												'rent_rule' => 'daily',
												'gallery' => '',
												'others' => ['icon' => '🏊'],
												'price_qty_info' => [
													'hourly' => ['qty' => 3, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 60, 'min' => 1, 'max' => 8],
													'daily' => ['qty' => 3, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 380, 'min' => 1, 'max' => 21],
													'multi_day' => ['qty' => 3, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 380, 'price_multi' => 80, 'min' => 1, 'max' => 21],
												],
											],
										],
									],
								],
							],
							'travel-equipment' => [
								0 => [
									'name' => 'Travel Equipment',
									'post_data' => [
										'common_data' => self::post_data(),
										'abprf_template' => 'group',
										'rent_rule' => 'daily',
										'property' => [
											0 => [
												'name' => 'Cabin Luggage 40L',
												'brand' => 'Samsonite International',
												'features' => 'fec_id_62,fec_id_64,fec_id_65,fec_id_67',
												'rent_rule' => 'daily',
												'gallery' => '',
												'others' => ['icon' => '🧳'],
												'price_qty_info' => [
													'hourly' => ['qty' => 10, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 4, 'min' => 1, 'max' => 8],
													'daily' => ['qty' => 12, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 18, 'min' => 1, 'max' => 21],
													'multi_day' => ['qty' => 12, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 18, 'price_multi' => 5, 'min' => 1, 'max' => 21],
												],
											],
											1 => [
												'name' => 'Camping Tent (4-Person)',
												'brand' => '',
												'features' => 'fec_id_69,fec_id_70,fec_id_72,fec_id_73',
												'rent_rule' => 'daily',
												'gallery' => '',
												'others' => ['icon' => '⛺'],
												'price_qty_info' => [
													'hourly' => ['qty' => 8, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 6, 'min' => 1, 'max' => 8],
													'daily' => ['qty' => 8, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 25, 'min' => 1, 'max' => 21],
													'multi_day' => ['qty' => 8, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 25, 'price_multi' => 8, 'min' => 1, 'max' => 21],
												],
											],
											2 => [
												'name' => 'Hiking Backpack 65L',
												'brand' => 'American Tourister',
												'features' => 'fec_id_63,fec_id_68,fec_id_72,fec_id_76',
												'rent_rule' => 'daily',
												'gallery' => '',
												'others' => ['icon' => '🎒'],
												'price_qty_info' => [
													'hourly' => ['qty' => 10, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 4, 'min' => 1, 'max' => 8],
													'daily' => ['qty' => 10, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 15, 'min' => 1, 'max' => 21],
													'multi_day' => ['qty' => 10, 'reserve' => 2, 'qty_min' => 1, 'qty_max' => 2, 'price' => 15, 'price_multi' => 5, 'min' => 1, 'max' => 21],
												],
											],
										],
									],
								],
							],
							'sport-equipment' => [
								0 => [
									'name' => 'Sport Equipment',
									'post_data' => [
										'common_data' => self::post_data(),
										'abprf_template' => 'group',
										'rent_rule' => 'daily',
										'property' => [
											0 => [
												'name' => 'Fishing Kayak',
												'brand' => '',
												'features' => 'fec_id_69,fec_id_78,fec_id_83,fec_id_86',
												'rent_rule' => 'daily',
												'gallery' => '',
												'others' => ['icon' => '🛶'],
												'price_qty_info' => [
													'hourly' => ['qty' => 6, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 8, 'min' => 1, 'max' => 8],
													'daily' => ['qty' => 6, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 45, 'min' => 1, 'max' => 14],
													'multi_day' => ['qty' => 6, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 45, 'price_multi' => 10, 'min' => 1, 'max' => 14],
												],
											],
											1 => [
												'name' => 'Alpine Ski Set',
												'brand' => '',
												'features' => 'fec_id_79,fec_id_85,fec_id_86,fec_id_87',
												'rent_rule' => 'daily',
												'gallery' => '',
												'others' => ['icon' => '⛷️'],
												'price_qty_info' => [
													'hourly' => ['qty' => 5, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 10, 'min' => 1, 'max' => 8],
													'daily' => ['qty' => 5, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 50, 'min' => 1, 'max' => 14],
													'multi_day' => ['qty' => 5, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 50, 'price_multi' => 12, 'min' => 1, 'max' => 14],
												],
											],
											2 => [
												'name' => 'Stand Up Paddle Board',
												'brand' => '',
												'features' => 'fec_id_78,fec_id_80,fec_id_83,fec_id_86',
												'rent_rule' => 'daily',
												'gallery' => '',
												'others' => ['icon' => '🏄'],
												'price_qty_info' => [
													'hourly' => ['qty' => 6, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 8, 'min' => 1, 'max' => 8],
													'daily' => ['qty' => 6, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 40, 'min' => 1, 'max' => 14],
													'multi_day' => ['qty' => 6, 'reserve' => 1, 'qty_min' => 1, 'qty_max' => 1, 'price' => 40, 'price_multi' => 10, 'min' => 1, 'max' => 14],
												],
											],
										],
									],
								],
							],
						],
					],
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
		new ABPRF_Dummy_Import();
	}
