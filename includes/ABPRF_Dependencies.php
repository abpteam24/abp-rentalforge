<?php
	if (!defined('ABSPATH')) {
		exit; // Exit if accessed directly
	}
	if (!class_exists( 'ABPRF_Dependencies' )) {
		class ABPRF_Dependencies {
			public function __construct() {
				add_action('admin_enqueue_scripts', array($this, 'admin_enqueue'), 90);
				add_action('wp_enqueue_scripts', array($this, 'frontend_enqueue'), 90);
				$this->load_file();
				if (is_admin() && in_array('woocommerce/woocommerce.php', get_option('active_plugins'))) {
					add_filter('use_block_editor_for_post_type', [$this, 'disable_gutenberg'], 10, 2);
					add_filter('plugin_action_links', array($this, 'plugin_settings_link'), 10, 2);
				}
				add_action('upgrader_process_complete', [$this, 'flush_rewrite']);
			}

			public function admin_enqueue(): void {
				$this->lib_enqueue();
				wp_enqueue_editor();
				wp_enqueue_media();
				//admin script
				wp_enqueue_script('jquery-ui-sortable');
				wp_enqueue_style('wp-color-picker');
				wp_enqueue_script('wp-color-picker');
				wp_enqueue_style('wp-codemirror');
				wp_enqueue_script('wp-codemirror');
				//=============================//
				wp_enqueue_script('abprf_admin', ABPRF_URL . '/assets/js/abprf_admin.js', array('jquery'), time(), true);
				wp_localize_script('abprf_admin', 'abprf_admin_ajax', array('ajax_url' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('abprf_admin_ajax_nonce')));
				wp_enqueue_style('abprf_admin', ABPRF_URL . '/assets/css/abprf_admin.css', array(), time());
				wp_enqueue_script('abprf_lib_admin', ABPRF_URL . '/assets/js/abprf_lib_admin.js', array('jquery'), time(), true);
				//=============================//
				$this->global_enqueue();
				do_action('abprf_admin_enqueue');
			}
			public function frontend_enqueue(): void {
				if (in_array('woocommerce/woocommerce.php', get_option('active_plugins'))) {
					wp_enqueue_script('wc-checkout');
					wp_enqueue_style('select2');
					wp_enqueue_script('select2');
				}
				$this->lib_enqueue();
				$this->global_enqueue();
				do_action('abprf_frontend_enqueue');
			}
			public function lib_enqueue(): void {
				wp_enqueue_script('jquery');
				wp_enqueue_script('jquery-ui-core');
				wp_enqueue_script('jquery-ui-datepicker');
				wp_enqueue_style('abprf_jquery_ui', ABPRF_URL . '/assets/css/jquery-ui.min.css', array(), '1.13.2');
				wp_enqueue_style('abprf_font_awesome', ABPRF_URL . '/assets/css/font_awesome.min.css', array(), '5.15.4');
				wp_enqueue_style('abprf_lib', ABPRF_URL . '/assets/css/abprf_lib.css', array(), time());
				wp_enqueue_script('abprf_lib', ABPRF_URL . '/assets/js/abprf_lib.js', array('jquery'), time(), true);
				if (in_array('woocommerce/woocommerce.php', get_option('active_plugins'))) {
					wp_localize_script('abprf_lib', 'abprf_var', [
						'currency_symbol' => get_woocommerce_currency_symbol(),
						'currency_position' => get_option('woocommerce_currency_pos'),
						'currency_decimal' => wc_get_price_decimal_separator(),
						'thousands_separator' => wc_get_price_thousand_separator(),
						'decimal_num' => ABPRF_LIB_Function::get_option('woocommerce_price_num_decimals', 2),
						'currency_suffix' => ABPRF_LIB_Function::get_option('woocommerce_price_display_suffix', ''),
						'blank_image' => ABPRF_BLANK_IMG_URL,
						'date_picker_format' => ABPRF_LIB_Function::get_options('abprf_layout', 'date_format', 'D d M , yy'),
					]);
				} else {
					wp_localize_script('abprf_lib', 'abprf_var', [
						'currency_symbol' => '',
						'currency_position' => '',
						'currency_decimal' => '',
						'thousands_separator' => '',
						'decimal_num' => '',
						'wc_suffix' => '',
						'blank_image' => ABPRF_BLANK_IMG_URL,
						'date_picker_format' => ABPRF_LIB_Function::get_options('abprf_layout', 'date_format', 'D d M , yy'),
					]);
				}
			}
			public function global_enqueue(): void {
				wp_enqueue_script('abprf', ABPRF_URL . '/assets/js/abprf.js', array('jquery'), time(), true);
				wp_localize_script('abprf', 'abprf_ajax', array('ajax_url' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('abprf_ajax_nonce')));
				wp_enqueue_style('abprf', ABPRF_URL . '/assets/css/abprf.css', array(), time());
				do_action('abprf_global_script');
				$abprf_css_var = ABPRF_LIB_Function::get_option('abprf_css_var');
				$default_color = isset($abprf_css_var['color_default']) && $abprf_css_var['color_default'] ? $abprf_css_var['color_default'] : '#303030';
				$color_theme = isset($abprf_css_var['color_theme']) && $abprf_css_var['color_theme'] ? $abprf_css_var['color_theme'] : '#95951c';
				$color_theme_ee = $color_theme . 'ee';
				$color_theme_cc = $color_theme . 'cc';
				$color_theme_aa = $color_theme . 'aa';
				$color_theme_88 = $color_theme . '88';
				$color_theme_77 = $color_theme . '77';
				$alternate_color = isset($abprf_css_var['color_theme_alternate']) && $abprf_css_var['color_theme_alternate'] ? $abprf_css_var['color_theme_alternate'] : '#fff';
				$color_warning = isset($abprf_css_var['color_warning']) && $abprf_css_var['color_warning'] ? $abprf_css_var['color_warning'] : '#E67C30';
				$bg_section = isset($abprf_css_var['bg_section']) && $abprf_css_var['bg_section'] ? $abprf_css_var['bg_section'] : '#FAFCFE';
				$default_br = isset($abprf_css_var['br_default']) && $abprf_css_var['br_default'] ? $abprf_css_var['br_default'] . 'px' : '0px';
				$fs_h1 = isset($abprf_css_var['fs_h1']) && $abprf_css_var['fs_h1'] ? $abprf_css_var['fs_h1'] . 'px' : '35px';
				$fs_h2 = isset($abprf_css_var['fs_h2']) && $abprf_css_var['fs_h2'] ? $abprf_css_var['fs_h2'] . 'px' : '30px';
				$fs_h3 = isset($abprf_css_var['fs_h3']) && $abprf_css_var['fs_h3'] ? $abprf_css_var['fs_h3'] . 'px' : '25px';
				$fs_h4 = isset($abprf_css_var['fs_h4']) && $abprf_css_var['fs_h4'] ? $abprf_css_var['fs_h4'] . 'px' : '20px';
				$fs_h5 = isset($abprf_css_var['fs_h5']) && $abprf_css_var['fs_h5'] ? $abprf_css_var['fs_h5'] . 'px' : '17px';
				$fs_h6 = isset($abprf_css_var['fs_h6']) && $abprf_css_var['fs_h6'] ? $abprf_css_var['fs_h6'] . 'px' : '15px';
				$fs_label = isset($abprf_css_var['fs_label']) && $abprf_css_var['fs_label'] ? $abprf_css_var['fs_label'] . 'px' : '14px';
				$default_fs = isset($abprf_css_var['fs_default']) && $abprf_css_var['fs_default'] ? $abprf_css_var['fs_default'] . 'px' : '12px';
				$button_fs = isset($abprf_css_var['fs_button']) && $abprf_css_var['fs_button'] ? $abprf_css_var['fs_button'] . 'px' : '14px';
				$bg_button = isset($abprf_css_var['bg_button']) && $abprf_css_var['bg_button'] ? $abprf_css_var['bg_button'] : '#007CBA';
				$color_button = isset($abprf_css_var['color_button']) && $abprf_css_var['color_button'] ? $abprf_css_var['bg_button'] : $alternate_color;
				$off = __('OFF', 'abprf-rental-forge');
				$on = __('ON', 'abprf-rental-forge');
				$abprf_var =
					":root {
						--rf_container_max: 1320px;
						--rf_widget_left: 280px;
						--rf_widget_right: 300px;
						--rf_widget_main: calc(100% - 300px);
						--rf_gap: 20px;
						--rf_negative: -20px;
						--rf_gap_xs: 10px;
						--rf_gap_xxs: 5px;
						--rf_gap_xxs_neg: -5px;
						--rf_gap_xs_neg: -10px;
						--rf_br_xl: 10px;
						--rf_br: {$default_br};
						--rf_transition: all 500ms ease-in-out;
						--rf_shadow: 0 0 2px #665F5F7A;
						--rf_shadow_1: 0 5px 10px rgba(0, 44, 102, 0.2);
						--rf_shadow_2: 0 1px 2px rgb(3 54 63 / 40%), 0 -1px 2px rgb(3 54 63 / 4%);
						--rf_shadow_3: 0 2px 10px #444;
						--rf_shadow_4: 3px 3px 10px rgba(0, 0, 0, .5);
						--rf_shadow_5: 0 8px 12px rgb(51 65 80 / 6%), 0 14px 44px rgb(51 65 80 / 11%);
						--rf_shadow_6: 0 0 10px 0 rgba(0, 0, 0, 0.5);
						--rf_shadow_7: 5px 4px 30px rgba(189, 189, 189, 0.07);
						--rf_shadow_8: 2px 4px 40px rgba(103, 103, 103, 0.1);
						--rf_shadow_9: inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 8px rgba(102, 175, 233, 0.6);
						--rf_shadow_10: 0 0 0 3px rgba(0, 119, 204, 0.3);
						--rf_shadow_11: 0 0 15px 0 rgba(0, 0, 0, .15);
						--rf_shadow_12: 0px 0px 10px rgba(0, 0, 0, 0.1);
						--rf_text_off:'{$off}';
						--rf_text_on: '{$on}';
						--rf_fs: {$default_fs};						
						--rf_fs_small: 11px;
						--rf_fs_label: {$fs_label};
						--rf_fs_h6: {$fs_h6};
						--rf_fs_h5: {$fs_h5};
						--rf_fs_h4: {$fs_h4};
						--rf_fs_h3: {$fs_h3};
						--rf_fs_h2: {$fs_h2};
						--rf_fs_h1: {$fs_h1};
						--rf_fw: normal;
						--rf_fw-thin: 300; 
						--rf_fw-normal: 500;
						--rf_fw-medium: 600;
						--rf_fw-bold: bold;
						--rf_button_bg: {$bg_button};
						--rf_button_color: {$color_button};
						--rf_button_fs: {$button_fs};
						--rf_button_height: 40px;
						--rf_button_height_xs: 30px;
						--rf_button_width: 120px;
						--rf_color_default: {$default_color};
						--rf_color_border: #DDD;
						--rf_color_active: #0E6BB7;
						--rf_color_section: {$bg_section};
						--rf_color_theme: {$color_theme};
						--rf_color_theme_ee: {$color_theme_ee};
						--rf_color_theme_cc: {$color_theme_cc};
						--rf_color_theme_aa: {$color_theme_aa};
						--rf_color_theme_88: {$color_theme_88};
						--rf_color_theme_77: {$color_theme_77};
						--rf_color_theme_alter: {$alternate_color};
						--rf_color_warning:{$color_warning};
						--rf_color_black: #000;
						--rf_color_success: #006607;
						--rf_color_danger: #C00;
						--rf_color_alert: #d1ecf1;
						--rf_color_required: #C00;
						--rf_color_white: #FFFFFF;
						--rf_color_light: #f8f9fa;
						--rf_color_light_1: #BBB;
						--rf_color_light_2: #EAECEE;
						--rf_color_light_3: #878787;
						--rf_color_light_4: #f9f9f9;
						--rf_color_info: #666;
						--rf_color_yellow: #FEBB02;
						--rf_color_blue: #815DF2;
						--rf_color_navy_blue: #007CBA;
						--rf_color_wp_menu: #1d2327;
						--rf_color_wp_menu_sub: #2c3338;
						--rf_color_wp_bg: #f0f0f1;
						--rf_color_dark: #0C5460;
						--rf_color_green_light: #0CB32612;
						--rf_color_off_white: #FAFCFE;
						--rf_color_purple: #6148BA;
						--rf_color_green_pale: #BCB;
						--rf_color_rose_dusty: #d27f7f;
					}";
				wp_add_inline_style('abprf', $abprf_var);
			}
			private function load_file(): void {
				require_once ABPRF_DIR . '/includes/ABPRF_LIB_Function.php';
				require_once ABPRF_DIR . '/includes/ABPRF_LIB_Layout.php';
				require_once ABPRF_DIR . '/includes/ABPRF_Static_Array.php';
				//=============Global Configuration================//
				if (is_admin()) {
					require_once ABPRF_DIR . '/admin/ABPRF_Configuration.php';
					require_once ABPRF_DIR . '/admin/configuration/ABPRF_Tools.php';
					require_once ABPRF_DIR . '/admin/configuration/ABPRF_Stops.php';
					require_once ABPRF_DIR . '/admin/configuration/ABPRF_Additional.php';
				}
				if (in_array('woocommerce/woocommerce.php', get_option('active_plugins'))) {
					require_once ABPRF_DIR . '/includes/ABPRF_Function.php';
					require_once ABPRF_DIR . '/includes/ABPRF_Hooks.php';
					require_once ABPRF_DIR . '/includes/ABPRF_Ajax.php';
					require_once ABPRF_DIR . '/includes/ABPRF_Query.php';
					require_once ABPRF_DIR . '/includes/ABPRF_Layout.php';
					require_once ABPRF_DIR . '/frontend/ABPRF_Frontend.php';
					require_once ABPRF_DIR . '/admin/ABPRF_Taxonomy_CPT.php';
					require_once ABPRF_DIR . '/admin/ABPRF_Hidden_Post.php';
					if (is_admin()) {
						//=============Transport Configuration================//
						require_once ABPRF_DIR . '/admin/ABPRF_Configuration_Post.php';
						require_once ABPRF_DIR . '/admin/configuration/ABPRF_General.php';
						require_once ABPRF_DIR . '/admin/configuration/ABPRF_Dates.php';
						require_once ABPRF_DIR . '/admin/configuration/ABPRF_Seats.php';
						require_once ABPRF_DIR . '/admin/configuration/ABPRF_Pricing.php';
						require_once ABPRF_DIR . '/admin/configuration/ABPRF_Routing.php';
						require_once ABPRF_DIR . '/admin/configuration/ABPRF_Gallery.php';
						require_once ABPRF_DIR . '/admin/configuration/ABPRF_Tax.php';
					}
				}
			}
			public function plugin_settings_link($links_array, $plugin_file_name) {
				if (strpos($plugin_file_name, ABPRF_BASE)) {
					array_unshift($links_array, '<a class="_abprf" href="' . esc_url(admin_url()) . 'edit.php?post_type=abprf_post&page=configuration">' . __('Configuration', 'abprf-rental-forge') . '</a>');
				}
				return $links_array;
			}
			public function flush_rewrite(): void {
				flush_rewrite_rules();
			}
			public function disable_gutenberg($current_status, $post_type) {
				if ($post_type === ABPRF_Function::get_cpt()) {
					return false;
				}
				return $current_status;
			}
		}
		new ABPRF_Dependencies();
	}