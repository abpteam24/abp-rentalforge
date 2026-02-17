<?php
	if (!defined('ABSPATH')) {
		exit;
	}  // if direct access
	if (!class_exists('ABPRF_Hidden_Post')) {
		class ABPRF_Hidden_Post {
			public function __construct() {
				add_action('wp_insert_post', array($this, 'insert_wc_hidden_post'), 10, 3);
				add_action('save_post', array($this, 'save_new_hidden_post'), 99);
				add_action('parse_query', array($this, 'hide_hidden_post'));
				add_action('wp', array($this, 'hide_hidden_post_frontend'));
				//******************//
				add_action('wp_head', [$this, 'exclude_url_from_search_engine']);
				add_filter('wpseo_exclude_from_sitemap_by_post_ids', [$this, 'get_all_hidden_product_id']);
			}
			public function insert_wc_hidden_post($post_id, $post): void {
				if ($post->post_type == ABPRF_Function::get_cpt() && $post->post_status == 'publish' && empty(ABPRF_Function::get_post_info($post_id, 'exit_wc_hidden_post'))) {
					$this->create_wc_hidden_post($post_id, $post->post_title);
				}
			}
			public function save_new_hidden_post($post_id): void {
				if (get_post_type($post_id) == ABPRF_Function::get_cpt()) {
					if (!isset($_POST['_wpnonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['_wpnonce'])), 'abprf_post_nonce') && defined('DOING_AUTOSAVE') && DOING_AUTOSAVE && !current_user_can('edit_post', $post_id)) {
						return;
					}
					$title = get_the_title($post_id);
					if ($this->count_hidden_post($post_id) == 0 || empty(ABPRF_Function::get_post_info($post_id, 'link_wc_id'))) {
						$this->create_wc_hidden_post($post_id, $title);
					}
					$product_id = ABPRF_Function::get_post_info($post_id, 'link_wc_id', $post_id);
					set_post_thumbnail($product_id, get_post_thumbnail_id($post_id));
					wp_publish_post($product_id);
					$product_type = 'yes';
					$_tax_status = isset($_POST['_tax_status']) ? sanitize_text_field(wp_unslash($_POST['_tax_status'])) : 'none';
					$_tax_class = isset($_POST['_tax_class']) ? sanitize_text_field(wp_unslash($_POST['_tax_class'])) : '';
					update_post_meta($product_id, '_tax_status', $_tax_status);
					update_post_meta($product_id, '_tax_class', $_tax_class);
					update_post_meta($product_id, '_stock_status', 'instock');
					update_post_meta($product_id, '_manage_stock', 'no');
					update_post_meta($product_id, '_virtual', $product_type);
					update_post_meta($product_id, '_sold_individually', 'yes');
					$my_post = array('ID' => $product_id, 'post_title' => $title, 'post_name' => uniqid());
					wp_update_post($my_post);
				}
			}
			public function hide_hidden_post($query): void {
				global $pagenow;
				$q_vars = &$query->query_vars;
				if ($pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == 'product') {
					$tax_query = array(['taxonomy' => 'product_visibility', 'field' => 'slug', 'terms' => 'exclude-from-catalog', 'operator' => 'NOT IN',]);
					$query->set('tax_query', $tax_query);
				}
			}
			public function hide_hidden_post_frontend(): void {
				global $post, $wp_query;
				if (is_product()) {
					$post_id = $post->ID;
					$visibility = get_the_terms($post_id, 'product_visibility');
					if (is_object($visibility)) {
						if ($visibility[0]->name == 'exclude-from-catalog') {
							$check_event_hidden = ABPRF_Function::get_post_info($post_id, 'link_abprf_id', 0);
							if ($check_event_hidden > 0) {
								$wp_query->set_404();
								status_header(404);
								get_template_part(404);
								exit();
							}
						}
					}
				}
			}
			//=============================//
			public function create_wc_hidden_post($post_id, $title): void {
				$new_post = array('post_title' => $title, 'post_content' => '', 'post_name' => uniqid(), 'post_category' => array(), 'tags_input' => array(), 'post_status' => 'publish', 'post_type' => 'product');
				$pid = wp_insert_post($new_post);
				update_post_meta($post_id, 'link_wc_id', $pid);
				update_post_meta($pid, 'link_abprf_id', $post_id);
				update_post_meta($pid, '_price', 0.01);
				update_post_meta($pid, '_sold_individually', 'yes');
				update_post_meta($pid, '_virtual', 'yes');
				$terms = array('exclude-from-catalog', 'exclude-from-search');
				wp_set_object_terms($pid, $terms, 'product_visibility');
				update_post_meta($post_id, 'exit_wc_hidden_post', true);
			}
			public function count_hidden_post($post_id): int {
				$args = array(
					'post_type' => 'product',
					'posts_per_page' => -1,
					'meta_query' => array(
						array('key' => 'link_abprf_id', 'value' => $post_id, 'compare' => '=')
					)
				);
				$loop = new WP_Query($args);
				return $loop->post_count;
			}
			//**************Google search url hidden*********************//
			public function exclude_url_from_search_engine(): void {
				global $post;
				if (is_single() && is_product()) {
					$post_id = $post->ID;
					$visibility = get_the_terms($post_id, 'product_visibility') ? get_the_terms($post_id, 'product_visibility') : [0];
					if (is_object($visibility[0]) && $visibility[0]->name == 'exclude-from-catalog') {
						$check_hidden = ABPRF_Function::get_post_info($post_id, 'link_abprf_id', 0);
						if ($check_hidden > 0) {
							?>
                            <meta name="robots" content="noindex, nofollow">
							<?php
						}
					}
				}
			}
			public function get_all_hidden_product_id(): array {
				$product_id = [];
				$query = ABPRF_Function::query_post_type(ABPRF_Function::get_cpt());
				foreach ($query->posts as $result) {
					$post_id = $result->ID;
					$product_id[] = ABPRF_Function::get_post_info($post_id, 'link_wc_id');
				}
				return array_filter($product_id);
			}
		}
		new ABPRF_Hidden_Post();
	}