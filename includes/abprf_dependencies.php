<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	if ( ! class_exists( 'ABPRF_Dependencies' ) ) {
		class ABPRF_Dependencies {
			public function __construct() {
				add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue' ), 90 );
				add_action( 'wp_enqueue_scripts', array( $this, 'frontend_enqueue' ), 90 );
				$this->load_file();
				add_action( 'init', [ $this, 'register_cpt' ] );
				add_filter( 'use_block_editor_for_post_type', [ $this, 'disable_gutenberg' ], 10, 2 );
				add_filter( 'plugin_action_links', array( $this, 'plugin_settings_link' ), 10, 2 );
				add_action( 'upgrader_process_complete', [ $this, 'flush_rewrite' ] );
				add_action( 'admin_init', array( $this, 'activation_redirect' ) );
			}
			public function admin_enqueue(): void {
				$this->global_enqueue();
				wp_enqueue_editor();
				wp_enqueue_media();
				//admin script
				wp_enqueue_script( 'jquery-ui-sortable' );
				wp_enqueue_style( 'wp-color-picker' );
				wp_enqueue_script( 'wp-color-picker' );
				wp_enqueue_style( 'wp-codemirror' );
				wp_enqueue_script( 'wp-codemirror' );
				//=============================//
				wp_enqueue_script( 'abprf_admin', ABPRF_URL . 'assets/js/abprf_admin.js', array( 'jquery' ), ABPRF_VERSION, true );
				wp_localize_script( 'abprf_admin', 'abprf_admin_data', [
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'nonce' => wp_create_nonce( 'abprf_admin_ajax_nonce' ),
					'icon_url' => ABPRF_URL . 'assets/js/abprf_icons.json',
					'related_info' => wp_json_encode( ABPRF_Function::related_info_js( get_the_ID() ) ),
					'feature_data' => wp_json_encode( ABPRF_Function::get_option( 'abprf_feature_js' ) ),
					'msg' => [
						'confirm_delete' => __( 'Are you sure you want to delete this item?', 'abp-rentalforge' ),
						'confirm_ok' => __( '1. Ok : To Remove Item .', 'abp-rentalforge' ),
						'confirm_cancel' => __( '2. Cancel : To Cancel .', 'abp-rentalforge' ),
						'saving' => __( 'Saving.............!', 'abp-rentalforge' ),
						'saved' => __( 'Saved...............!', 'abp-rentalforge' ),
						'importing' => __( 'Importing........', 'abp-rentalforge' ),
						'imported' => __( 'Imported Successfully............. !', 'abp-rentalforge' ),
						'loading' => __( 'Loading........', 'abp-rentalforge' ),
						'loaded' => __( 'Loaded Successfully............. !', 'abp-rentalforge' ),
						'order_loading' => __( 'Order Loading........ !', 'abp-rentalforge' ),
						'error' => __( 'An error occurred. Please try again.', 'abp-rentalforge' ),
						'deleting' => __( 'Deleting.............', 'abp-rentalforge' ),
						'delete_success' => __( 'Delete Successfully.............', 'abp-rentalforge' ),
						'property_loading' => __( 'Property List Loading.............', 'abp-rentalforge' ),
						'post_loading' => __( 'Post List Loading.............', 'abp-rentalforge' ),
						'post_deleting' => __( 'Post Permanent Deleting.........!', 'abp-rentalforge' ),
						'post_trashing' => __( 'Post move to Trashing.........!', 'abp-rentalforge' ),
						'post_restoring' => __( 'Post Restoring.........!', 'abp-rentalforge' ),
						'wc_install' => __( 'Woocommerce Downloading And Installing.........!', 'abp-rentalforge' ),
						'wc_installing' => __( 'Woocommerce  Installing.........!', 'abp-rentalforge' ),
						'wc_installed_success' => __( 'Woocommerce Downloaded And Installed successfully ..... !! ', 'abp-rentalforge' ),
						'wc_installed' => __( 'Woocommerce Installed successfully.... !  ', 'abp-rentalforge' ),
						'create_page' => __( 'Page Creating ........!', 'abp-rentalforge' ),
						'no_item' => __( 'No More Item Found !', 'abp-rentalforge' ),
						'no_item_selected' => __( 'No Item selected !', 'abp-rentalforge' ),
					],
				] );
				wp_enqueue_style( 'abprf_admin', ABPRF_URL . 'assets/css/abprf_admin.css', array(), ABPRF_VERSION );
				//=============================//
				do_action( 'abprf_admin_enqueue' );
			}
			public function frontend_enqueue(): void {
				if ( in_array( 'woocommerce/woocommerce.php', get_option( 'active_plugins' ) ) ) {
					wp_enqueue_script( 'wc-checkout' );
					wp_enqueue_style( 'select2' );
					wp_enqueue_script( 'select2' );
				}
				wp_enqueue_script( 'abprf_frontend', ABPRF_URL . 'assets/js/abprf_frontend.js', array( 'jquery' ), ABPRF_VERSION, true );
				wp_enqueue_script( 'abprf_slick', ABPRF_URL . 'assets/js/slick.min.js', array( 'jquery' ), ABPRF_VERSION, true );
				$this->global_enqueue();
				do_action( 'abprf_frontend_enqueue' );
			}
			public function global_enqueue(): void {
				wp_enqueue_script( 'jquery' );
				wp_enqueue_script( 'jquery-ui-core' );
				wp_enqueue_script( 'jquery-ui-datepicker' );
				wp_enqueue_style( 'abprf_jquery_ui', ABPRF_URL . 'assets/css/jquery-ui.min.css', array(), '1.13.2' );
				wp_enqueue_style( 'abprf_font_awesome', ABPRF_URL . 'assets/css/font_awesome.min.css', array(), '5.15.4' );
				wp_enqueue_style( 'abprf_lib', ABPRF_URL . 'assets/css/abprf_lib.css', array(), ABPRF_VERSION );
				wp_enqueue_script( 'abprf_lib', ABPRF_URL . 'assets/js/abprf_lib.js', array( 'jquery' ), ABPRF_VERSION, true );
				if ( in_array( 'woocommerce/woocommerce.php', get_option( 'active_plugins' ) ) ) {
					wp_localize_script( 'abprf_lib', 'abprf_var', [
						'currency_symbol' => get_woocommerce_currency_symbol(),
						'currency_position' => get_option( 'woocommerce_currency_pos' ),
						'currency_decimal' => wc_get_price_decimal_separator(),
						'thousands_separator' => wc_get_price_thousand_separator(),
						'decimal_num' => ABPRF_Function::get_option( 'woocommerce_price_num_decimals', 2 ),
						'currency_suffix' => ABPRF_Function::get_option( 'woocommerce_price_display_suffix', '' ),
						'blank_image' => ABPRF_BLANK_IMG_URL,
						'date_format' => ABPRF_JS_Date_Format,
					] );
				} else {
					wp_localize_script( 'abprf_lib', 'abprf_var', [
						'currency_symbol' => '',
						'currency_position' => '',
						'currency_decimal' => '',
						'thousands_separator' => '',
						'decimal_num' => '',
						'wc_suffix' => '',
						'blank_image' => ABPRF_BLANK_IMG_URL,
						'date_format' => ABPRF_JS_Date_Format,
					] );
				}
				$abprf_css_var   = ABPRF_Function::get_option( 'abprf_css_var' );
				$default_color   = ( $abprf_css_var['color_default'] ?? null ) ?: '#303030';
				$color_theme     = ( $abprf_css_var['color_theme'] ?? null ) ?: '#95951c';
				$alternate_color = ( $abprf_css_var['color_theme_alternate'] ?? null ) ?: '#fff';
				$color_warning   = ( $abprf_css_var['color_warning'] ?? null ) ?: '#E67C30';
				$bg_section      = ( $abprf_css_var['bg_section'] ?? null ) ?: '#FAFCFE';
				$bg_button       = ( $abprf_css_var['bg_button'] ?? null ) ?: '#222';
				$color_button    = ( $abprf_css_var['color_button'] ?? null ) ?: $alternate_color;
				$color_theme_ee  = $color_theme . 'ee';
				$color_theme_cc  = $color_theme . 'cc';
				$color_theme_aa  = $color_theme . 'aa';
				$color_theme_88  = $color_theme . '88';
				$color_theme_77  = $color_theme . '77';
				$default_br      = ! empty( $abprf_css_var['br_default'] ) ? $abprf_css_var['br_default'] . 'px' : '0';
				$br_xl           = ! empty( $abprf_css_var['br_default'] ) ? $abprf_css_var['br_default'] * 2 . 'px' : '0';
				$fs_h1           = ! empty( $abprf_css_var['fs_h1'] ) ? $abprf_css_var['fs_h1'] . 'px' : '35px';
				$fs_h2           = ! empty( $abprf_css_var['fs_h2'] ) ? $abprf_css_var['fs_h2'] . 'px' : '30px';
				$fs_h3           = ! empty( $abprf_css_var['fs_h3'] ) ? $abprf_css_var['fs_h3'] . 'px' : '25px';
				$fs_h4           = ! empty( $abprf_css_var['fs_h4'] ) ? $abprf_css_var['fs_h4'] . 'px' : '20px';
				$fs_h5           = ! empty( $abprf_css_var['fs_h5'] ) ? $abprf_css_var['fs_h5'] . 'px' : '17px';
				$fs_h6           = ! empty( $abprf_css_var['fs_h6'] ) ? $abprf_css_var['fs_h6'] . 'px' : '15px';
				$fs_label        = ! empty( $abprf_css_var['fs_label'] ) ? $abprf_css_var['fs_label'] . 'px' : '14px';
				$default_fs      = ! empty( $abprf_css_var['fs_default'] ) ? $abprf_css_var['fs_default'] . 'px' : '12px';
				$button_fs       = ! empty( $abprf_css_var['fs_button'] ) ? $abprf_css_var['fs_button'] . 'px' : '14px';
				$off             = esc_html__( 'OFF', 'abp-rentalforge' );
				$on              = esc_html__( 'ON', 'abp-rentalforge' );
				$abprf_var       =
					":root {
						--rf_br: {$default_br};						
						--rf_br_xl: {$br_xl};						
						--rf_text_off:'{$off}';
						--rf_text_on: '{$on}';
						--rf_fs: {$default_fs};				
						--rf_fs_label: {$fs_label};
						--rf_fs_h6: {$fs_h6};
						--rf_fs_h5: {$fs_h5};
						--rf_fs_h4: {$fs_h4};
						--rf_fs_h3: {$fs_h3};
						--rf_fs_h2: {$fs_h2};
						--rf_fs_h1: {$fs_h1};						
						--rf_button_bg: {$bg_button};
						--rf_button_color: {$color_button};
						--rf_button_fs: {$button_fs};						
						--rf_color_default: {$default_color};						
						--rf_color_section: {$bg_section};
						--rf_color_theme: {$color_theme};
						--rf_color_theme_ee: {$color_theme_ee};
						--rf_color_theme_cc: {$color_theme_cc};
						--rf_color_theme_aa: {$color_theme_aa};
						--rf_color_theme_88: {$color_theme_88};
						--rf_color_theme_77: {$color_theme_77};
						--rf_color_theme_alter: {$alternate_color};
						--rf_color_warning:{$color_warning};						
					}";
				wp_add_inline_style( 'abprf_lib', wp_kses_post( $abprf_var ) );
				wp_enqueue_style( 'abprf', ABPRF_URL . 'assets/css/abprf.css', array(), ABPRF_VERSION );
				$all_time = ABPRF_Function::get_time( get_the_id(), 'js' );
				wp_enqueue_script( 'abprf_infos', ABPRF_URL . 'assets/js/abprf.js', array( 'jquery' ), ABPRF_VERSION, true );
				$rental_data = array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'nonce' => wp_create_nonce( 'abprf_ajax_nonce' ),
					'date_info' => wp_json_encode( $all_time ),
					'now' => current_time( 'Y-m-d H:i' ),
					'msg' => [
						'end_date_loading' => __( 'End Date  Loading.............', 'abp-rentalforge' ),
						'property_loading' => __( 'Property List Loading.............', 'abp-rentalforge' ),
						'select_post' => __( 'Please Select', 'abp-rentalforge' ) . ' ' . ABPRF_Function::label(),
						'select_rent_start_date' => __( 'Please Select rent Start Date', 'abp-rentalforge' ),
						'select_rent_end_date' => __( 'Please Select rent End Date', 'abp-rentalforge' ),
						'select_rent_start_time' => __( 'Please Select rent Start Time', 'abp-rentalforge' ),
						'select_rent_end_time' => __( 'Please Select rent End Time', 'abp-rentalforge' ),
						'free' => __( 'FREE', 'abp-rentalforge' ),
						'loading' => __( 'Loading..............!', 'abp-rentalforge' ),
					],
				);
				wp_localize_script( 'abprf_infos', 'abprf_infos', $rental_data );
				do_action( 'abprf_global_script' );
			}
			private function load_file(): void {
				require_once ABPRF_DIR . 'includes/abprf_function.php';
				require_once ABPRF_DIR . 'includes/abprf_query.php';
				require_once ABPRF_DIR . 'includes/abprf_layout.php';
				if ( is_admin() ) {
					require_once ABPRF_DIR . 'admin/abprf_admin.php';
					require_once ABPRF_DIR . 'admin/abprf_post.php';
					require_once ABPRF_DIR . 'admin/abprf_dashboard.php';
					require_once ABPRF_DIR . 'admin/abprf_properties.php';
					require_once ABPRF_DIR . 'admin/abprf_orders.php';
					require_once ABPRF_DIR . 'admin/abprf_dates.php';
					require_once ABPRF_DIR . 'admin/abprf_additional.php';
					require_once ABPRF_DIR . 'admin/abprf_form.php';
					require_once ABPRF_DIR . 'admin/abprf_resource.php';
					require_once ABPRF_DIR . 'admin/abprf_configuration.php';
					require_once ABPRF_DIR . 'admin/abprf_status.php';
					require_once ABPRF_DIR . 'admin/abprf_category.php';
					require_once ABPRF_DIR . 'admin/abprf_location.php';
					require_once ABPRF_DIR . 'admin/abprf_brand.php';
					require_once ABPRF_DIR . 'admin/abprf_feature.php';
				}
				if ( in_array( 'woocommerce/woocommerce.php', get_option( 'active_plugins' ) ) ) {
					require_once ABPRF_DIR . 'includes/abprf_hooks.php';
					require_once ABPRF_DIR . 'includes/abprf_ajax.php';
					require_once ABPRF_DIR . 'includes/abprf_frontend.php';
					require_once ABPRF_DIR . 'includes/abprf_shortcodes.php';
					require_once ABPRF_DIR . 'includes/abprf_woocommerce.php';
					require_once ABPRF_DIR . 'admin/abprf_hidden_post.php';
				}
			}
			public function register_cpt(): void {
				$cpt    = ABPRF_Function::get_cpt();
				$label  = ABPRF_Function::label();
				$slug   = ABPRF_Function::slug();
				$icon   = ABPRF_Function::icon_wp();
				$labels = [
					'name' => esc_html( $label ),
					'singular_name' => esc_html( $label ),
					'menu_name' => esc_html( $label ),
					'name_admin_bar' => esc_html( $label ),
					'archives' => __( 'Post List', 'abp-rentalforge' ),
					'attributes' => __( 'Post List', 'abp-rentalforge' ),
					'parent_item_colon' => __( 'Post Item:', 'abp-rentalforge' ),
					'all_items' => __( 'Post', 'abp-rentalforge' ),
					'add_new_item' => __( 'Add Post', 'abp-rentalforge' ),
					'add_new' => __( 'Add Post', 'abp-rentalforge' ),
					'new_item' => __( 'Add Post', 'abp-rentalforge' ),
					'edit_item' => __( 'Edit Post', 'abp-rentalforge' ),
					'update_item' => __( 'Update Post', 'abp-rentalforge' ),
					'view_item' => __( 'View Post', 'abp-rentalforge' ),
					'view_items' => __( 'View Post', 'abp-rentalforge' ),
					'search_items' => __( 'Search Post', 'abp-rentalforge' ),
					'not_found' => __( 'Post Not Found', 'abp-rentalforge' ),
					'not_found_in_trash' => __( 'Post Not found in Trash', 'abp-rentalforge' ),
					'featured_image' => __( 'Post Image', 'abp-rentalforge' ),
					'set_featured_image' => __( 'Post Image', 'abp-rentalforge' ),
					'remove_featured_image' => __( 'Remove Post Image', 'abp-rentalforge' ),
					'use_featured_image' => __( 'Use image Post as featured image', 'abp-rentalforge' ),
					'insert_into_item' => __( 'Insert  Post', 'abp-rentalforge' ),
					'uploaded_to_this_item' => __( 'Uploaded  Post', 'abp-rentalforge' ),
					'items_list' => __( 'Post List', 'abp-rentalforge' ),
					'items_list_navigation' => __( 'Category list navigation', 'abp-rentalforge' ),
					'filter_items_list' => __( 'Filter Post List', 'abp-rentalforge' )
				];
				$args   = [
					'public' => true,
					'labels' => $labels,
					'menu_icon' => esc_html( $icon ),
					'supports' => [ 'title', 'editor', 'thumbnail' ],
					'rewrite' => [ 'slug' => esc_html( $slug ), 'with_front' => true, 'pages' => true, 'feeds' => true, ],
					'show_in_rest' => true,
					'rest_base' => 'abprf_post',
					'capability_type' => 'post',
					'publicly_queryable' => true,  // you should be able to query it
					'show_ui' => true,  // you should be able to edit it in wp-admin
					'show_in_menu' => false,
					'exclude_from_search' => true,  // you should exclude it from search results
					'show_in_nav_menus' => true,  // you should be able to add it to menus
					'has_archive' => true,  // it should have archive page
				];
				register_post_type( $cpt, $args );
				$category_label = ABPRF_Function::category_label();
				$category_slug  = ABPRF_Function::category_slug();
				$full_text      = $label . ' ' . $category_label;
				$label_category = array(
					'name' => $full_text,
					'singular_name' => $full_text,
				);
				$args           = [
					'hierarchical' => true,
					"public" => true,
					'labels' => $label_category,
					'show_ui' => true,
					'show_admin_column' => false,
					'show_in_menu' => false,
					'query_var' => true,
					'rewrite' => [ 'slug' => $category_slug ],
					'show_in_rest' => true,
					'rest_base' => 'abprf_category',
					'meta_box_cb' => false,
				];
				register_taxonomy( 'abprf_category', $cpt, $args );
				$full_text      = $label . ' ' . __( 'Locations', 'abp-rentalforge' );
				$label_location = array(
					'name' => $full_text,
					'singular_name' => $full_text,
				);
				$args           = [
					'hierarchical' => true,
					"public" => true,
					'labels' => $label_location,
					'show_ui' => true,
					'show_admin_column' => false,
					'show_in_menu' => false,
					'query_var' => true,
					'rewrite' => [ 'slug' => 'location' ],
					'show_in_rest' => true,
					'rest_base' => 'abprf_location',
					'meta_box_cb' => false,
				];
				register_taxonomy( 'abprf_location', $cpt, $args );
				$full_text   = $label . ' ' . ABPRF_Function::brand_label();
				$label_brand = array(
					'name' => $full_text,
					'singular_name' => $full_text,
				);
				$args        = [
					'hierarchical' => true,
					"public" => true,
					'labels' => $label_brand,
					'show_ui' => true,
					'show_admin_column' => false,
					'show_in_menu' => false,
					'query_var' => true,
					'rewrite' => [ 'slug' => 'brand' ],
					'show_in_rest' => true,
					'rest_base' => 'abprf_feature',
					'meta_box_cb' => false,
				];
				register_taxonomy( 'abprf_brand', $cpt, $args );
				flush_rewrite_rules();
			}
			public static function activation(): void {
				self::create_table();
				flush_rewrite_rules();
			}
			public static function deactivate(): void {
				flush_rewrite_rules();
			}
			public static function create_table(): void {
				global $wpdb;
				$order_table    = $wpdb->prefix . 'abprf_orders';
				$property_table = $wpdb->prefix . 'abprf_property';
				$collate        = $wpdb->get_charset_collate();
				// Orders Table
				$abprf_orders = "CREATE TABLE $order_table (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        order_id bigint(20) unsigned NOT NULL,
        item_id bigint(20) unsigned NOT NULL,
        post_id bigint(20) unsigned NOT NULL,
        user_id bigint(20) unsigned NOT NULL,
        property_id longtext NOT NULL,
        ex_id longtext NOT NULL,
        pick_up varchar(100) DEFAULT NULL,
        drop_off varchar(100) DEFAULT NULL,
        category varchar(50) DEFAULT NULL,
        location varchar(50) DEFAULT NULL,
        brand varchar(50) DEFAULT NULL,
        start_time datetime DEFAULT NULL,
        end_time datetime DEFAULT NULL,
        book_from datetime DEFAULT NULL,
        book_to datetime DEFAULT NULL,
        price_info longtext NOT NULL,
        property_info longtext NOT NULL,
        ex_info longtext NOT NULL,
        pass_info longtext NOT NULL,
        delivery_option TINYINT UNSIGNED NOT NULL DEFAULT 0,
        book_status TINYINT UNSIGNED NOT NULL DEFAULT 0,
        order_status varchar(20) NOT NULL,
        payment_method varchar(100) DEFAULT NULL,
        billing_name varchar(100) DEFAULT NULL,
        billing_email varchar(100) DEFAULT NULL,
        billing_phone varchar(20) DEFAULT NULL,
        billing_address varchar(255) DEFAULT NULL,
        others longtext DEFAULT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        updated_at datetime DEFAULT NULL,
        PRIMARY KEY  (id),
        KEY order_id (order_id),
        KEY user_id (user_id),
        KEY item_id (item_id)
    ) $collate;";
				// Property Table
				$abprf_property = "CREATE TABLE $property_table (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        post_id bigint(20) unsigned DEFAULT NULL,
        rent_continue varchar(20) NOT NULL DEFAULT 'on',
        name varchar(100) NOT NULL,
        brand varchar(50) DEFAULT NULL,
        category varchar(255) DEFAULT NULL,
        location varchar(255) DEFAULT NULL,
        features longtext DEFAULT NULL,
        rent_rule varchar(20) DEFAULT NULL,
        price_qty_info longtext NOT NULL,
        gallery longtext DEFAULT NULL,
        status varchar(20) DEFAULT NULL,
        others longtext DEFAULT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
        updated_at datetime DEFAULT NULL,
        PRIMARY KEY  (id),
        KEY post_id (post_id)
    ) $collate;";
				if ( ! function_exists( 'dbDelta' ) ) {
					require_once ABSPATH . 'wp-admin/includes/upgrade.php';
				}
				dbDelta( $abprf_orders );
				dbDelta( $abprf_property );
				$row_count = ABPRF_Query::get_property( [], true );
				if ( 0 == $row_count ) {
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					$wpdb->insert(
						$property_table,
						array(
							'id' => 99,
							'name' => 'Dummy Init',
							'price_qty_info' => '[]',
						),
						array( '%d', '%s', '%s' )
					);
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					$wpdb->delete( $property_table, array( 'id' => 99 ), array( '%d' ) );
				}
			}
			public function plugin_settings_link( $links_array, $plugin_file_name ) {
				if ( strpos( $plugin_file_name, ABPRF_BASE ) ) {
					array_unshift( $links_array, '<a class="_abprf" href="' . esc_url( admin_url() ) . 'admin.php?page=rental-forge&tab=configuration">' . __( 'Configuration', 'abp-rentalforge' ) . '</a>' );
				}

				return $links_array;
			}
			public function flush_rewrite(): void {
				flush_rewrite_rules();
			}
			public function disable_gutenberg( $current_status, $post_type ) {
				if ( $post_type === ABPRF_Function::get_cpt() ) {
					return false;
				}

				return $current_status;
			}
			public function activation_redirect(): void {
				$active_tab = '';
				$page       = '';
				if ( isset( $_GET['_abprf_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_abprf_nonce'] ) ), 'abprf_url_action' ) ) {
					$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'status';
					$page       = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
				}
				if ( $page === 'rental-forge' && ABPRF_WC < 2 && $active_tab != 'status' ) {
					wp_safe_redirect( ABPRF_Function::build_url( 'status' ) );
					exit;
				}
			}
		}
		new ABPRF_Dependencies();
	}