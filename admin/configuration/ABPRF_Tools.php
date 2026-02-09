<?php
	if (!defined('ABSPATH')) {
		die;
	} // Cannot access pages directly.
	if (!class_exists('ABPRF_Tools')) {
		class ABPRF_Tools {
			public function __construct() {
				add_action('abprf_configuration_content', array($this, 'tools_info_configuration'));
				//=============================//
				add_action('wp_ajax_abprf_install_and_active_wc', array($this, 'abprf_install_and_active_wc'));
				add_action('wp_ajax_abprf_active_wc', array($this, 'rf_active_wc'));
				//=============================//
				add_action('wp_ajax_abprf_create_transport_search_page', array($this, 'abprf_create_transport_search_page'));
				add_action('wp_ajax_abprf_create_search_result_page', array($this, 'abprf_create_search_result_page'));
				//=============================//
				add_action('wp_ajax_abprf_import_bus', array($this, 'abprf_import_bus'));
			}
			public function tools_info_configuration($abprf_configuration): void {
				$label = isset($abprf_configuration['label']) && $abprf_configuration['label'] ? $abprf_configuration['label'] : __('Transportation', 'abprf-rental-forge');
				$label = $label . ' ' . __(' : ', 'abprf-rental-forge') . ' ' . __('Tools Management  & Information', 'abprf-rental-forge');
				?>
                <div class="tabsItem abprf_tools" data-tabs="#abprf_tools">
                    <h3 class="_abprf"><?php echo esc_html($label); ?></h3>
                    <div class="_divider_xs"></div>
					<?php $this->abptm_version(); ?>
					<?php $this->wordpress(); ?>
					<?php $this->php(); ?>
					<?php $this->wc_setup(); ?>
					<?php do_action('abptm_add_tools'); ?>
					<?php $this->page_create(); ?>
					<?php $this->dummy_import(); ?>
                </div>
				<?php
			}
			//=============================//
			public function abptm_version(): void {
				?>
                <div class="_section_xs_mar_t_xs">
                    <div class="_fa_center_fj_between">
                        <h6 class="_abprf"> <?php esc_html_e('TransportTicket - Bus, Ferry, Shuttle Booking Version', 'abprf-rental-forge') ?> </h6>
                        <button class="_btn_success_light_xs_min_125" type="button"><span class="fas fa-check _mar_r_xxs"></span><?php echo esc_html(get_plugin_data(ABPRF_PLUGIN_FILE) ['Version']); ?></button>
                    </div>
                </div>
				<?php
			}
			public function wordpress(): void {
				$version = get_bloginfo('version');
				?>
                <div class="_section_xs">
                    <div class="_fa_center_fj_between">
                        <h6 class="_abprf"> <?php esc_html_e('WordPress Version', 'abprf-rental-forge'); ?> </h6>
						<?php if ($version > 5.5) { ?>
                            <button class="_btn_success_light_xs_min_125" type="button"><span class="fas fa-check _mar_r_xxs"></span><?php echo esc_html($version); ?></button>
						<?php } else { ?>
                            <button class="_btn_warning_light_xs_min_125" type="button"><span class="fas fa-exclamation-triangle _mar_r_xxs"></span><?php echo esc_html($version); ?></button>
						<?php } ?>
                    </div>
                </div>
				<?php
			}
			public function php(): void {
				$version = phpversion();
				?>
                <div class="_section_xs">
                    <div class="_fa_center_fj_between">
                        <h6 class="_abprf"> <?php esc_html_e('Php Version', 'abprf-rental-forge'); ?> </h6>
						<?php if ($version > 7.4) { ?>
                            <button class="_btn_success_light_xs_min_125" type="button"><span class="fas fa-check _mar_r_xxs"></span><?php echo esc_html($version); ?></button>
						<?php } else { ?>
                            <button class="_btn_warning_light_xs_min_125" type="button"><span class="fas fa-exclamation-triangle _mar_r_xxs"></span><?php echo esc_html($version); ?></button>
						<?php } ?>
                    </div>
                </div>
				<?php
			}
			//=============================//
			public function wc_setup(): void {
				$wc_status = ABPRF_LIB_Function::check_wc();
				$title = $wc_status == 2 ? __('Woocommerce Plugin', 'abprf-rental-forge') : __('Woocommerce need to install and active', 'abprf-rental-forge');
				$title = $wc_status == 1 ? __('Woocommerce already installed but  not  activated', 'abprf-rental-forge') : $title;
				?>
                <form class="_section_xs" method="post" action="">
                    <div class="_fa_center_fj_between">
                        <h6 class="_abprf"> <?php echo esc_html($title); ?></h6>
						<?php if ($wc_status == 2) { ?>
                            <button class="_btn_success_light_xs_min_125" type="button"><span class="fas fa-check _mar_r_xxs"></span><?php esc_html_e('Activated', 'abprf-rental-forge'); ?></button>
						<?php } elseif ($wc_status == 1) { ?>
                            <button class="_btn_theme_xs_min_125 rf_active_wc" type="button"><span class="fas fa-tasks _mar_r_xxs"></span><?php esc_html_e('Active Now', 'abprf-rental-forge'); ?></button>
						<?php } else { ?>
                            <button class="_btn_warning_xs_min_125 abprf_install_and_active_wc" type="button"><span class="fas fa-file-download _mar_r_xxs"></span><?php esc_html_e('Install & Active Now', 'abprf-rental-forge'); ?></button>
						<?php } ?>
                    </div>
                    <div class="_divider_xs"></div>
					<?php if ($wc_status == 2 && defined('WC_VERSION')) { ?>
                        <div class="_fa_center_fj_between">
                            <h6 class="_abprf"><?php esc_html_e('Woocommerce Version', 'abprf-rental-forge'); ?></h6>
							<?php if (version_compare(WC_VERSION, '8.0', '>')) { ?>
                                <button class="_btn_success_light_xs_min_125" type="button"><span class="fas fa-check _mar_r_xxs"></span><?php echo esc_html(WC_VERSION); ?></button>
							<?php } else { ?>
                                <button class="_btn_warning_light_xs_min_125" type="button"><span class="fas fa-exclamation-triangle _mar_r_xxs"></span><?php echo esc_html(WC_VERSION); ?></button>
							<?php } ?>
                        </div>
                        <div class="_divider_xs"></div>
                        <div class="_fa_center_fj_between">
                            <h6 class="_abprf"><?php esc_html_e('Name', 'abprf-rental-forge'); ?></h6>
                            <button class="_btn_success_light_xs_min_125" type="button"><?php echo esc_html(get_option('woocommerce_email_from_name')); ?></button>
                        </div>
                        <div class="_divider_xs"></div>
                        <div class="_fa_center_fj_between">
                            <h6 class="_abprf"><?php esc_html_e('Email Address', 'abprf-rental-forge'); ?></h6>
                            <button class="_btn_success_light_xs_min_125_text_inherit" type="button"><?php echo esc_html(get_option('woocommerce_email_from_address')); ?></button>
                        </div>
					<?php } else { ?>
                        <div class="_color_warning"><span class=" _abprf_mar_r_xxs  fas fa-exclamation-triangle"></span><?php esc_html_e('TransportTicket - Bus, Ferry, Shuttle Booking is entirely dependent on the WooCommerce plugin. Please install and activate the WooCommerce plugin otherwise the plugin will not work. Installing this tool may take some time', 'abprf-rental-forge'); ?></div>
					<?php } ?>
                </form>
				<?php
			}
			public function abprf_install_and_active_wc() {
				if (is_admin() && isset($_POST['nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'abprf_admin_ajax_nonce')) {
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
					$title = 'title';
					$url = 'url';
					$nonce = 'nonce';
					$woocommerce_plugin = new Plugin_Upgrader(new Plugin_Installer_Skin(compact('title', 'url', 'nonce', 'plugin', 'api')));
					$woocommerce_plugin->install($api->download_link);
					activate_plugin('woocommerce/woocommerce.php');
				}
				wp_die();
			}
			public function rf_active_wc() {
				if (is_admin() && isset($_POST['nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'abprf_admin_ajax_nonce')) {
					if (is_dir(ABSPATH . 'wp-content/plugins/woocommerce')) {
						activate_plugin('woocommerce/woocommerce.php');
					}
				}
				wp_die();
			}
			//=============================//
			public function page_create(): void {
				?>
                <form class="_section_xs" method="post" action="">
                    <div class="_fa_center_fj_between">
                        <h6 class="_abprf"><?php esc_html_e('Transport Search Page', 'abprf-rental-forge'); ?></h6>
						<?php if (ABPRF_LIB_Function::get_page_by_slug('transport_search')) { ?>
                            <button class="_btn_success_light_xs_min_125" type="button"><span class="fas fa-check _mar_r_xxs"></span><?php esc_html_e('Activated', 'abprf-rental-forge'); ?></button>
						<?php } else { ?>
                            <button class="_btn_warning_xs_min_125 abprf_create_transport_search_page" type="button"><span class="fas fa-plus _mar_r_xxs"></span><?php esc_html_e('Add Transport Search Page', 'abprf-rental-forge'); ?></button>
						<?php } ?>
                    </div>
                    <div class="_divider_xs"></div>
                    <div class="_fa_center_fj_between">
                        <h6 class="_abprf"><?php esc_html_e('Transport Search Result Page', 'abprf-rental-forge'); ?></h6>
						<?php if (ABPRF_LIB_Function::get_page_by_slug('transport_result')) { ?>
                            <button class="_btn_success_light_xs_min_125" type="button"><span class="fas fa-check _mar_r_xxs"></span><?php esc_html_e('Activated', 'abprf-rental-forge'); ?></button>
						<?php } else { ?>
                            <button class="_btn_warning_xs_min_125 abprf_create_search_result_page" type="button"><span class="fas fa-plus _mar_r_xxs"></span><?php esc_html_e('Add Transport Search Result Page', 'abprf-rental-forge'); ?></button>
						<?php } ?>
                    </div>
					<?php do_action('abptm_page_create'); ?>
                </form>
				<?php
			}
			public function abprf_create_transport_search_page() {
				if (is_admin() && isset($_POST['nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'abprf_admin_ajax_nonce')) {
					if (!ABPRF_LIB_Function::get_page_by_slug('transport_search')) {
						$abptm_search = array(
							'post_type' => 'page',
							'post_name' => 'transport_search',
							'post_title' => __('Search', 'abprf-rental-forge'),
							'post_content' => '[abptm-search]',
							'post_status' => 'publish',
						);
						wp_insert_post($abptm_search);
						flush_rewrite_rules();
					}
				}
				wp_die();
			}
			public function abprf_create_search_result_page() {
				if (is_admin() && isset($_POST['nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'abprf_admin_ajax_nonce')) {
					if (!ABPRF_LIB_Function::get_page_by_slug('transport_result')) {
						$abptm_search = array(
							'post_type' => 'page',
							'post_name' => 'transport_result',
							'post_title' => __('Search Result', 'abprf-rental-forge'),
							'post_content' => '[abptm-search]',
							'post_status' => 'publish',
						);
						wp_insert_post($abptm_search);
						flush_rewrite_rules();
					}
				}
				wp_die();
			}
			//=============================//
			public function dummy_import(): void {
				$total_transport = sizeof(ABPRF_LIB_Function::get_all_post_id('abprf_post'));
				?>
                <form class="_section_xs" method="post" action="">
                    <div class="_fa_center_fj_between">
                        <h6 class="_abprf"> <?php esc_html_e('Number of Transport', 'abprf-rental-forge'); ?> </h6>
						<?php if ($total_transport > 0) { ?>
                            <button class="_btn_success_light_xs_min_125" type="button"><span class="fas fa-check _mar_r_xxs"></span><?php echo esc_html($total_transport); ?></button>
						<?php } else { ?>
                            <button class="_btn_warning_light_xs_min_125" type="button"><span class="fas fa-exclamation-triangle _mar_r_xxs"></span><?php esc_html_e('Can Not Find Transport', 'abprf-rental-forge'); ?></button>
						<?php } ?>
                    </div>
                    <div class="_divider_xs"></div>
                    <div class="_fa_center_fj_between">
                        <h6 class="_abprf"> <?php esc_html_e('Dummy Import', 'abprf-rental-forge'); ?> </h6>
                        <button class="<?php echo esc_attr($total_transport > 0 ? '_btn_success_light_xs' : '_btn_warning_xs'); ?>_btn_theme_min_125 abprf_import_bus" type="button"><span class="fas fa-plus _mar_r_xxs"></span><?php esc_html_e('Add New Dummy Transport', 'abprf-rental-forge'); ?></button>
                    </div>
                </form>
				<?php
			}
			public function abprf_import_bus() {
				if (is_admin() && isset($_POST['nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'abprf_admin_ajax_nonce')) {
					$this->add_data($this->dummy_data());
					flush_rewrite_rules();
				}
				wp_die();
			}
			public static function add_data($dummy_infos): void {
				if (array_key_exists('taxonomy', $dummy_infos)) {
					foreach ($dummy_infos['taxonomy'] as $taxonomy => $taxonomy_option) {
						if (taxonomy_exists($taxonomy)) {
							$check_terms = get_terms(array('taxonomy' => $taxonomy, 'hide_empty' => false));
							if (is_string($check_terms) || sizeof($check_terms) == 0) {
								foreach ($taxonomy_option as $taxonomy_data) {
									unset($term);
									$term = wp_insert_term($taxonomy_data['name'], $taxonomy);
								}
							}
						}
					}
				}
				if (array_key_exists('options', $dummy_infos)) {
					foreach ($dummy_infos['options'] as $option => $dummy_option) {
						$option_data = get_option($option);
						if (!$option_data || sizeof($option_data) == 0) {
							update_option($option, $dummy_option);
						}
					}
				}
				if (array_key_exists('custom_post', $dummy_infos)) {
					foreach ($dummy_infos['custom_post'] as $custom_post => $dummy_post) {
						foreach ($dummy_post as $dummy_data) {
							$args = array();
							if (isset($dummy_data['name'])) {
								$args['post_title'] = $dummy_data['name'];
							}
							$args['post_status'] = 'publish';
							$args['post_type'] = $custom_post;
							$post_id = wp_insert_post($args);
							if (array_key_exists('post_data', $dummy_data)) {
								foreach ($dummy_data['post_data'] as $meta_key => $data) {
									update_post_meta($post_id, $meta_key, $data);
								}
							}
						}
					}
				}
			}
			public function dummy_data(): array {
				return [
					'taxonomy' => [
						'abprf_category' => [
							0 => ['name' => 'AC'],
							1 => ['name' => 'Non AC'],
							2 => ['name' => 'AC Sleeper'],
						],
						'abprf_organizer' => [
							0 => ['name' => 'Flix'],
							1 => ['name' => 'Golden Tours'],
							2 => ['name' => 'TNS Travel'],
							7 => ['name' => 'LuxBus'],
						],
					],
					'options' => [
						'abprf_additional' => ABPRF_Static_Array::static_additional(),
						'abprf_traveller_pattern' => ABPRF_Static_Array::static_form(),
						'abprf_stops' => ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K',],
					],
					'custom_post' => [
						'abprf_post' => [
							0 => [
								'name' => 'Bucharest-Izmail',
								'post_data' => [
									//General
									'display_transport_id' => 'on',
									'transport_id' => wp_rand(100, 999),
									'display_category' => 'on',
									'category' => 'AC',
									'display_organizer' => 'on',
									'organizer' => 'LuxBus',
									'sale_continue' => 'on',
									//Date_settings
									'date_type' => 'periodic_date',
									'specific_dates' => [],
									'periodic_start_date' => gmdate('Y-m-d', strtotime(' +1 day')),
									'periodic_end_date' => '',
									'periodic_after' => 1,
									'advance_date_number' => 15,
									'weekend' => 'sunday',
									'specific_off_dates' => [
										gmdate('Y-m-d', strtotime(' +15 day')),
									],
									'off_date_range' => [
										0 => [
											'from' => gmmktime('Y-m-d', strtotime(' +25 day')),
											'to' => gmdate('Y-m-d', strtotime(' +28 day')),
										],
									],
									//seat_settings
									'display_ticket_type' => '',
									'ticket_type' => '',
									'seat_type' => 'seat_plan',
									'ld_infos' => $this->seat_40_text(),
									'ld_rows' => '12',
									'ld_columns' => '5',
									'display_ud' => '',
									'ud_infos' => [],
									'ud_rows' => '',
									'ud_columns' => '',
									'total_seat' => '40',
									//Route_settings
									'routing_infos' => [
										0 => ['stop' => 'A', 'type' => 'bp', 'time' => '08:00'],
										1 => ['stop' => 'B', 'type' => 'bp', 'time' => '09:00'],
										2 => ['stop' => 'C', 'type' => 'bp', 'time' => '11:00'],
										3 => ['stop' => 'D ', 'type' => 'both', 'time' => '12:00'],
										4 => ['stop' => 'E', 'type' => 'both', 'time' => '14:00'],
										5 => ['stop' => 'F', 'type' => 'dp', 'time' => '15:45'],
										6 => ['stop' => 'G', 'type' => 'dp', 'time' => '17:00'],
									],
									'route_direction' => ['A', 'B', 'C', 'D', 'E', 'F', 'G'],
									'abptm_bp' => ['A', 'B', 'C', 'D', 'E'],
									'abptm_dp' => ['D', 'E', 'F', 'G'],
									//price_settings
									'price_infos' => [
										0 => ['bp' => 'A', 'dp' => 'D ', 'price' => '750', 'adult' => '', 'child' => '', 'infant' => ''],
										1 => ['bp' => 'A', 'dp' => 'E', 'price' => '850', 'adult' => '', 'child' => '', 'infant' => ''],
										2 => ['bp' => 'A', 'dp' => 'F', 'price' => '1000', 'adult' => '', 'child' => '', 'infant' => ''],
										3 => ['bp' => 'A', 'dp' => 'G', 'price' => '1200', 'adult' => '', 'child' => '', 'infant' => ''],
										4 => ['bp' => 'B', 'dp' => 'D ', 'price' => '1100', 'adult' => '', 'child' => '', 'infant' => ''],
										5 => ['bp' => 'B', 'dp' => 'E', 'price' => '900', 'adult' => '', 'child' => '', 'infant' => ''],
										6 => ['bp' => 'B', 'dp' => 'F', 'price' => '800', 'adult' => '', 'child' => '', 'infant' => ''],
										7 => ['bp' => 'B', 'dp' => 'G', 'price' => '700', 'adult' => '', 'child' => '', 'infant' => ''],
										8 => ['bp' => 'C', 'dp' => 'D ', 'price' => '1000', 'adult' => '', 'child' => '', 'infant' => ''],
										9 => ['bp' => 'C', 'dp' => 'E', 'price' => '900', 'adult' => '', 'child' => '', 'infant' => ''],
										10 => ['bp' => 'C', 'dp' => 'F', 'price' => '800', 'adult' => '', 'child' => '', 'infant' => ''],
										11 => ['bp' => 'C', 'dp' => 'G', 'price' => '700', 'adult' => '', 'child' => '', 'infant' => ''],
										12 => ['bp' => 'D ', 'dp' => 'E', 'price' => '800', 'adult' => '', 'child' => '', 'infant' => ''],
										13 => ['bp' => 'D ', 'dp' => 'F', 'price' => '600', 'adult' => '', 'child' => '', 'infant' => ''],
										14 => ['bp' => 'D ', 'dp' => 'G', 'price' => '300', 'adult' => '', 'child' => '', 'infant' => ''],
										15 => ['bp' => 'E ', 'dp' => 'F', 'price' => '400', 'adult' => '', 'child' => '', 'infant' => ''],
										16 => ['bp' => 'E ', 'dp' => 'G', 'price' => '300', 'adult' => '', 'child' => '', 'infant' => ''],
									],
									//Reg form
									'display_passenger_form' => 'on',
									'display_single_form' => 'on',
									'passenger_form' => ABPRF_Static_Array::static_form(),
									//additional service
									'display_additional_services' => 'on',
									'additional_services' => ABPRF_Static_Array::static_additional(),
									//slider_settings
									'display_slider' => 'on',
									'abprf_sliders' => [200, 300, 400, 500, 600, 700, 800, 900, 1000],
								]
							],
						]
					]
				];
			}
			public function seat_40_text(): array {
				return [
					0 => ['blank_@@__&&_2', '_@@__&&_0', 'text_@@_Engine_&&_1', 'driver_@@__&&_2', '_@@__&&_0'],
					1 => ['ticket_@@_A1_&&_1', 'ticket_@@_A2_&&_1', 'blank_@@__&&_1', 'ticket_@@_A3_&&_1', 'ticket_@@_A4_&&_1'],
					2 => ['ticket_@@_B1_&&_1', 'ticket_@@_B2_&&_1', 'blank_@@__&&_1', 'ticket_@@_B3_&&_1', 'ticket_@@_B4_&&_1'],
					3 => ['ticket_@@_C1_&&_1', 'ticket_@@_C2_&&_1', 'blank_@@__&&_1', 'ticket_@@_C3_&&_1', 'ticket_@@_C4_&&_1'],
					4 => ['ticket_@@_D1_&&_1', 'ticket_@@_D2_&&_1', 'blank_@@__&&_1', 'ticket_@@_D3_&&_1', 'ticket_@@_D4_&&_1'],
					5 => ['ticket_@@_E1_&&_1', 'ticket_@@_E2_&&_1', 'blank_@@__&&_1', 'ticket_@@_E3_&&_1', 'ticket_@@_E4_&&_1'],
					6 => ['ticket_@@_F1_&&_1', 'ticket_@@_F2_&&_1', 'blank_@@__&&_1', 'ticket_@@_F3_&&_1', 'ticket_@@_F4_&&_1'],
					7 => ['ticket_@@_G1_&&_1', 'ticket_@@_G2_&&_1', 'blank_@@__&&_1', 'ticket_@@_G3_&&_1', 'ticket_@@_G4_&&_1'],
					8 => ['ticket_@@_H1_&&_1', 'ticket_@@_H2_&&_1', 'blank_@@__&&_1', 'ticket_@@_H3_&&_1', 'ticket_@@_H4_&&_1'],
					9 => ['ticket_@@_I1_&&_1', 'ticket_@@_I2_&&_1', 'blank_@@__&&_1', 'ticket_@@_I3_&&_1', 'ticket_@@_I4_&&_1'],
					10 => ['ticket_@@_J1_&&_1', 'ticket_@@_J2_&&_1', 'blank_@@__&&_1', 'ticket_@@_J3_&&_1', 'ticket_@@_J4_&&_1'],
					11 => ['text_@@_Luggage_&&_5', '_@@__&&_0', '_@@__&&_0', '_@@__&&_0', '_@@__&&_0'],
				];
			}
		}
		new ABPRF_Tools();
	}