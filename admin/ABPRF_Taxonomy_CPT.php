<?php
	if (!defined('ABSPATH')) {
		exit; // Exit if accessed directly
	}
	if (!class_exists('ABPRF_Taxonomy_CPT')) {
		class ABPRF_Taxonomy_CPT {
			public function __construct() {
				add_action('init', [$this, 'taxonomy_cpt']);
				add_action('manage_abprf_post_posts_columns', [$this, 'posts_columns']);
				add_action('manage_abprf_post_posts_custom_column', [$this, 'custom_column'], 10, 2);
				add_filter('post_row_actions', [$this, 'clone_transport'], 10, 2);
				add_action('admin_action_abprf_clone', [$this, 'abprf_clone']);
				register_activation_hook(__FILE__, [$this, 'activation']);
				add_action('activated_plugin', array($this, 'activation_redirect'));
			}
			public function taxonomy_cpt(): void {
				$abprf_configuration = ABPRF_LIB_Function::get_option('abprf_configuration');
				$this->add_cpt($abprf_configuration);
				$this->taxonomy_category($abprf_configuration);
			}
			public function add_cpt($abprf_configuration): void {
				$cpt = ABPRF_Function::get_cpt();
				$label = isset($abprf_configuration['label']) && $abprf_configuration['label'] ? $abprf_configuration['label'] : __('RentalForge', 'abprf-rental-forge');
				$slug = isset($abprf_configuration['slug']) && $abprf_configuration['slug'] ? $abprf_configuration['slug'] : 'rental-forge';
				$icon = isset($abprf_configuration['icon']) && $abprf_configuration['icon'] ? $abprf_configuration['icon'] : 'dashicons-hammer';
				$labels = [
					'name' => esc_html($label),
					'singular_name' => esc_html($label),
					'menu_name' => esc_html($label),
					'name_admin_bar' => esc_html($label),
					'archives' => __('Equipment Group List', 'abprf-rental-forge'),
					'attributes' => __('Equipment Group List', 'abprf-rental-forge'),
					'parent_item_colon' => __('Equipment Group Item:', 'abprf-rental-forge'),
					'all_items' => __('Equipment Groups', 'abprf-rental-forge'),
					'add_new_item' => __('Add Equipment Group', 'abprf-rental-forge'),
					'add_new' => __('Add Equipment Group', 'abprf-rental-forge'),
					'new_item' => __('Add Equipment Group', 'abprf-rental-forge'),
					'edit_item' => __('Edit Equipment', 'abprf-rental-forge'),
					'update_item' => __('Update Equipment', 'abprf-rental-forge'),
					'view_item' => __('View Equipment', 'abprf-rental-forge'),
					'view_items' => __('View Equipment', 'abprf-rental-forge'),
					'search_items' => __('Search Equipment', 'abprf-rental-forge'),
					'not_found' => __('Equipment Not Found', 'abprf-rental-forge'),
					'not_found_in_trash' => __('Equipment Not found in Trash', 'abprf-rental-forge'),
					'featured_image' => __('Equipment Groups Image', 'abprf-rental-forge'),
					'set_featured_image' => __('Equipment Groups Image', 'abprf-rental-forge'),
					'remove_featured_image' => __('Remove Equipment Groups Image', 'abprf-rental-forge'),
					'use_featured_image' => __('Use image Equipment Group as featured image', 'abprf-rental-forge'),
					'insert_into_item' => __('Insert  Equipment Group', 'abprf-rental-forge'),
					'uploaded_to_this_item' => __('Uploaded  Equipment Group', 'abprf-rental-forge'),
					'items_list' => __('Equipment Groups List', 'abprf-rental-forge'),
					'items_list_navigation' => __('Category list navigation', 'abprf-rental-forge'),
					'filter_items_list' => __('Filter Equipment Group List', 'abprf-rental-forge')
				];
				$rewrite = array(
					'slug' => esc_html($slug),
					'with_front' => true,
					'pages' => true,
					'feeds' => true,
				);
				$args = [
					'public' => true,
					'labels' => $labels,
					'menu_icon' =>esc_html($icon),
					'supports' => ['title', 'editor', 'thumbnail'],
					'rewrite' => $rewrite,
					'show_in_rest' => true,
					'rest_base' => 'abprf_post',
					'capability_type' => 'post',
					'publicly_queryable' => true,  // you should be able to query it
					'show_ui' => true,  // you should be able to edit it in wp-admin
					'exclude_from_search' => true,  // you should exclude it from search results
					'show_in_nav_menus' => false,  // you shouldn't be able to add it to menus
					'has_archive' => true,  // it shouldn't have archive page
				];
				register_post_type($cpt, $args);
			}
			public function taxonomy_category($abprf_configuration): void {
				$label = isset($abprf_configuration['label']) && $abprf_configuration['label'] ? $abprf_configuration['label'] : __('RentalForge', 'abprf-rental-forge');
				$category_text = isset($abprf_configuration['category_label']) && $abprf_configuration['category_label'] ? $abprf_configuration['category_label'] : __('Category', 'abprf-rental-forge');
				$category_slug = isset($abprf_configuration['cat_slug']) && $abprf_configuration['cat_slug'] ? $abprf_configuration['cat_slug'] : 'rental_category';
				$full_text = $label . ' ' . $category_text;
				$label_category = array(
					'name' => $full_text,
					'singular_name' => $full_text,
					'menu_name' => $category_text,
					'all_items' => __('All', 'abprf-rental-forge') . ' ' . $full_text,
					'parent_item' => __('Parent', 'abprf-rental-forge') . ' ' . $full_text,
					'parent_item_colon' => __('Parent', 'abprf-rental-forge') . ' ' . $full_text,
					'new_item_name' => __('New type name of', 'abprf-rental-forge') . ' ' . $label,
					'add_new_item' => __('Add New', 'abprf-rental-forge') . ' ' . $full_text,
					'edit_item' => __('Edit', 'abprf-rental-forge') . ' ' . $full_text,
					'update_item' => __('Update', 'abprf-rental-forge') . ' ' . $full_text,
					'view_item' => __('View', 'abprf-rental-forge') . ' ' . $full_text,
					'add_or_remove_items' => __('Add / Remove', 'abprf-rental-forge') . ' ' . $full_text,
					'popular_items' => __('Popular', 'abprf-rental-forge') . ' ' . $full_text,
					'search_items' => __('Search', 'abprf-rental-forge') . ' ' . $full_text,
					'no_terms' => __('No', 'abprf-rental-forge') . ' ' . $full_text,
					'items_list' => $full_text . ' ' . __('List', 'abprf-rental-forge'),
					'items_list_navigation' => $full_text . ' ' . __('List navigation', 'abprf-rental-forge'),
					'separate_items_with_commas' => __('Separated with commas', 'abprf-rental-forge'),
					'choose_from_most_used' => __('Choose from the most used', 'abprf-rental-forge'),
					'not_found' => __('Not Found !', 'abprf-rental-forge'),
				);
				$args = [
					'hierarchical' => true,
					"public" => true,
					'labels' => $label_category,
					'show_ui' => true,
					'show_admin_column' => true,
					'query_var' => true,
					'rewrite' => ['slug' => $category_slug],
					'show_in_rest' => true,
					'rest_base' => 'abprf_category',
					'meta_box_cb' => false,
				];
				register_taxonomy('abprf_category', 'abprf_post', $args);
			}
			public function activation() {
				$this->taxonomy_cpt();
				//$this->create_order_table();
				flush_rewrite_rules();
			}
			public function create_order_table() {
				global $wpdb;
				$table_name = $wpdb->prefix . 'abprf_orders';
				$collate = $wpdb->has_cap('collation') ? $wpdb->get_charset_collate() : '';
				$sql = "CREATE TABLE IF NOT EXISTS $table_name (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,order_id BIGINT UNSIGNED NOT NULL,item_id BIGINT UNSIGNED NOT NULL,post_id BIGINT UNSIGNED NOT NULL,user_id BIGINT UNSIGNED NOT NULL,
    origin varchar(100) NOT NULL,origin_time TIMESTAMP  NULL,
    bp varchar(100) NOT NULL,bp_time TIMESTAMP  NULL,
    dp varchar(100) NOT NULL,dp_time TIMESTAMP NULL,
    pick_up varchar(100)  NULL,drop_off varchar(100)  NULL,
    ticket JSON NOT NULL,ticket_info JSON NOT NULL,additional_info JSON NOT NULL,pass_info JSON NOT NULL,
    order_status varchar(20) NOT NULL,payment_method varchar(100) NOT NULL,checkin TINYINT(1) NOT NULL DEFAULT 0,female TINYINT(1) NOT NULL DEFAULT 0,
    billing_name varchar(100) NOT NULL,billing_email varchar(100) NOT NULL,billing_phone varchar(20) NOT NULL,billing_address varchar(255) NOT NULL,
     item_total DECIMAL(10,4) NOT NULL DEFAULT 0.0000,qty int(5) NOT NULL DEFAULT 1,others JSON NULL,
     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,updated_at TIMESTAMP  NULL,
     PRIMARY KEY  (id), KEY order_id_idx (order_id),KEY user_id_idx (user_id))$collate;";
				if (!function_exists('dbDelta')) {
					require_once ABSPATH . 'wp-admin/includes/upgrade.php';
				}
				dbDelta($sql);
			}
			public function activation_redirect($plugin): void {
				if ($plugin == plugin_basename(__FILE__)) {
					if ( ABPRF_LIB_Function::check_wc() < 2) {
						wp_safe_redirect( admin_url( 'admin.php?page=rf_configuration' ) );
						exit;
					}
				}
			}
			public function posts_columns($column) {
				$abprf_configuration = ABPRF_LIB_Function::get_option('abprf_configuration');
				$date = $column['date'];
				unset($column['taxonomy-abprf_category']);
				unset($column['date']);
				$column['equipment_id'] = __('Equipment Groups ID', 'abprf-rental-forge');
				$column['total_seat'] = __('Total Ticket', 'abprf-rental-forge');
				$column['seat_type'] = __('Seat Plan', 'abprf-rental-forge');
				$column['category'] = isset($abprf_configuration['category_label']) && $abprf_configuration['category_label'] ? esc_html($abprf_configuration['category_label']) : __('Category', 'abprf-rental-forge');
				$column['abprf_admin'] = __('Author', 'abprf-rental-forge');
				$column['date'] = $date;
				return $column;
			}
			public function custom_column($column, $post_id): void {
				$abprf_infos = ABPRF_LIB_Function::get_all_meta($post_id);
				$seat_type = array_key_exists('seat_type', $abprf_infos) ? $abprf_infos['seat_type'] : '';
				$equipment_id = array_key_exists('equipment_id', $abprf_infos) ? $abprf_infos['equipment_id'] : '';
				$total_seat = array_key_exists('total_seat', $abprf_infos) ? $abprf_infos['total_seat'] : '';
				$seat_type_text = $seat_type == 'seat_plan' ? __('Seat Plan', 'abprf-rental-forge') : __('Ticket', 'abprf-rental-forge');
				$category = array_key_exists('category', $abprf_infos) ? $abprf_infos['category'] : '';
				$organizer = array_key_exists('organizer', $abprf_infos) ? $abprf_infos['organizer'] : '';
				switch ($column) {
					case 'equipment_id':
						echo esc_html($equipment_id);
						break;
					case 'total_seat':
						echo esc_html($total_seat);
						break;
					case 'seat_type':
						echo esc_html($seat_type_text);
						break;
					case 'category':
						echo esc_html($category);
						break;
					case 'organizer':
						echo esc_html($organizer);
						break;
					case 'abprf_admin':
						$user_id = get_post_field('post_author', $post_id);
						echo esc_html(get_the_author_meta('display_name', $user_id) . ' [' . ABPRF_LIB_Function::get_user_role($user_id)) . "]";
						break;
				}
			}
			public function clone_transport($actions, $post) {
				$post_id = $post->ID;
				if (current_user_can('edit_posts') && ABPRF_Function::get_cpt() == get_post_type($post_id)) {
					$actions['abprf_clone'] = '<a href="' . wp_nonce_url('admin.php?action=abprf_clone&post=' . $post->ID, basename(__FILE__), 'abprf_clone_nonce') . '" title="' . __('Clone Equipment', 'abprf-rental-forge') . '" rel="permalink">' . __('Clone Equipment', 'abprf-rental-forge') . '</a>';
				}
				return $actions;
			}
			public function abprf_clone(): void {
				if (!(isset($_GET['post']) || isset($_POST['post']) || (isset($_REQUEST['action']) && 'abprf_clone' == $_REQUEST['action'])) && (!isset($_GET['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_GET['_wpnonce'])), basename(__FILE__)))) {
					return;
				}
				$post_id = (isset($_GET['post']) ? absint(wp_unslash($_GET['post'])) : absint(wp_unslash($_POST['post'])));
				$post = get_post($post_id);
				$user = wp_get_current_user();
				if (isset($post) && $post != null && ABPRF_Function::get_cpt() == get_post_type($post_id)) {
					$post_meta_infos = get_post_custom($post_id);
					if (count($post_meta_infos) != 0) {
						$args = array('comment_status' => $post->comment_status, 'ping_status' => $post->ping_status, 'post_author' => $user->ID, 'post_content' => $post->post_content, 'post_excerpt' => $post->post_excerpt, 'post_name' => $post->post_name, 'post_parent' => $post->post_parent, 'post_password' => $post->post_password, 'post_status' => 'draft', 'post_title' => $post->post_title, 'post_type' => $post->post_type, 'to_ping' => $post->to_ping, 'menu_order' => $post->menu_order);
						$new_post_id = wp_insert_post($args);
						foreach ($post_meta_infos as $key => $values) {
							foreach ($values as $value) {
								add_post_meta($new_post_id, $key, $value);
							}
						}
						wp_safe_redirect(admin_url('post.php?action=edit&post=' . $new_post_id));
					}
					exit;
				}
			}
		}
		new ABPRF_Taxonomy_CPT();
	}