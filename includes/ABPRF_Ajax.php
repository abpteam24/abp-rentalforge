<?php
	if (!defined('ABSPATH')) {
		exit; // Exit if accessed directly
	}
	if (!class_exists('ABPRF_Ajax')) {
		class ABPRF_Ajax {
			public function __construct() {
				add_action('wp_ajax_abprf_get_bp', [$this, 'abptm_get_bp']);
				add_action('wp_ajax_nopriv_abprf_get_bp', [$this, 'abptm_get_bp']);
				add_action('wp_ajax_abprf_get_dp', [$this, 'abptm_get_dp']);
				add_action('wp_ajax_nopriv_abprf_get_dp', [$this, 'abptm_get_dp']);
				add_action('wp_ajax_abprf_get_date', [$this, 'abptm_get_date']);
				add_action('wp_ajax_nopriv_abprf_get_date', [$this, 'abptm_get_date']);
				add_action('wp_ajax_abprf_get_return_date', [$this, 'abptm_get_return_date']);
				add_action('wp_ajax_nopriv_abprf_get_return_date', [$this, 'abptm_get_return_date']);
				add_action('wp_ajax_abprf_get_transport', [$this, 'abprf_get_rental']);
				add_action('wp_ajax_nopriv_abprf_get_transport', [$this, 'abprf_get_rental']);
				//=============================//
				add_action('wp_ajax_abprf_book_continue', [$this, 'abprf_book_continue']);
				add_action('wp_ajax_nopriv_abprf_book_continue', [$this, 'abprf_book_continue']);
			}
			public function abptm_get_bp() {
				if (isset($_POST['nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'abprf_ajax_nonce')) {
					if (isset($_POST['form_data']) && is_array($_POST['form_data'])) {
						// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
						$raw_data = wp_unslash($_POST['form_data']);
						if (sizeof($raw_data) > 0) {
							$form_data = array();
							foreach ($raw_data as $field) {
								if (!isset($field['name'], $field['value'])) {
									continue;
								}
								$name = sanitize_key($field['name']);
								$value = sanitize_text_field($field['value']);
								$form_data[$name] = $value;
							}
							$post_id = array_key_exists('_post_id', $form_data) ? $form_data["_post_id"] : 0;
							$abptm_route_bp = ABPRF_Function::get_routes($post_id);
							ABPRF_Layout::boarding_from($abptm_route_bp);
						}
					}
				}
				wp_die();
			}
			public function abptm_get_dp() {
				if (isset($_POST['nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'abprf_ajax_nonce')) {
					if (isset($_POST['form_data']) && is_array($_POST['form_data'])) {
						// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
						$raw_data = wp_unslash($_POST['form_data']);
						$form_data = array();
						foreach ($raw_data as $field) {
							if (!isset($field['name'], $field['value'])) {
								continue;
							}
							$name = sanitize_key($field['name']);
							$value = sanitize_text_field($field['value']);
							$form_data[$name] = $value;
						}
						$post_id = array_key_exists('_post_id', $form_data) ? $form_data["_post_id"] : 0;
						$bp = array_key_exists('_bp', $form_data) ? $form_data["_bp"] : '';
						$abptm_route_dp = ABPRF_Function::get_routes($post_id, false, $bp);
						ABPRF_Layout::dropping_from($abptm_route_dp);
					}
				}
				wp_die();
			}
			public function abptm_get_date() {
				if (isset($_POST['nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'abprf_ajax_nonce')) {
					if (isset($_POST['form_data']) && is_array($_POST['form_data'])) {
						// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
						$raw_data = wp_unslash($_POST['form_data']);
						$form_data = array();
						foreach ($raw_data as $field) {
							if (!isset($field['name'], $field['value'])) {
								continue;
							}
							$name = sanitize_key($field['name']);
							$value = sanitize_text_field($field['value']);
							$form_data[$name] = $value;
						}
						$post_id = array_key_exists('_post_id', $form_data) ? $form_data["_post_id"] : 0;
						$bp = array_key_exists('_bp', $form_data) ? $form_data["_bp"] : '';
						ABPRF_Layout::departure_date($post_id, $bp);
					}
				}
				wp_die();
			}
			public function abptm_get_return_date() {
				if (isset($_POST['nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'abprf_ajax_nonce')) {
					if (isset($_POST['form_data']) && is_array($_POST['form_data'])) {
						// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
						$raw_data = wp_unslash($_POST['form_data']);
						$form_data = array();
						foreach ($raw_data as $field) {
							if (!isset($field['name'], $field['value'])) {
								continue;
							}
							$name = sanitize_key($field['name']);
							$value = sanitize_text_field($field['value']);
							$form_data[$name] = $value;
						}
						$bp = array_key_exists('_bp', $form_data) ? $form_data["_bp"] : '';
						$dp = array_key_exists('_dp', $form_data) ? $form_data["_dp"] : '';
						$bp_date = array_key_exists('_j_date', $form_data) ? $form_data["_j_date"] : '';
						ABPRF_Layout::return_date($bp, $dp, $bp_date);
					}
				}
				wp_die();
			}
			public function abprf_get_rental() {
				if (isset($_POST['nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'abprf_ajax_nonce')) {
					if (isset($_POST['form_data']) && is_array($_POST['form_data'])) {
						// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
						$raw_data = wp_unslash($_POST['form_data']);
						$form_data = array();
						foreach ($raw_data as $field) {
							if (!isset($field['name'], $field['value'])) {
								continue;
							}
							$name = sanitize_key($field['name']);
							$value = sanitize_text_field($field['value']);
							$form_data[$name] = $value;
						}
						$_bp = array_key_exists('_bp', $form_data) ? $form_data['_bp'] : '';
						$_dp = array_key_exists('_dp', $form_data) ? $form_data['_dp'] : '';
						$_j_date = array_key_exists('_j_date', $form_data) ? $form_data['_j_date'] : '';
						$_r_date = array_key_exists('_r_date', $form_data) ? $form_data['_r_date'] : '';
						if ($_bp && $_dp && ($_j_date || $_r_date)) {
							ABPRF_Layout::transport_list($form_data);
						} else {
							ABPRF_LIB_Layout::layout_warning_info('search_get_wrong_data_info');
						}
					}
				}
				wp_die();
			}
			//=============================//
			public function abprf_book_continue() {
				if (isset($_POST['nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'abprf_ajax_nonce')) {
					global $woocommerce;
					if (isset($_POST['form_data']) && is_array($_POST['form_data'])) {
						// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
						$raw_data = wp_unslash($_POST['form_data']);
						$form_data = array();
						foreach ($raw_data as $field) {
							if (!isset($field['name'], $field['value'])) {
								continue;
							}
							$name = sanitize_key($field['name']);
							$value = sanitize_text_field($field['value']);
							$form_data[$name] = $value;
						}
						foreach ($form_data as $key => $value) {
							$_POST[$key] = $value;
						}
						$_POST['form_data'] = '';
						$link_id = isset($_POST['wc_link_id']) ? sanitize_text_field(wp_unslash($_POST['wc_link_id'])) : '';
						$product_id = apply_filters('woocommerce_add_to_cart_product_id', $link_id);
						$quantity = 1;
						$passed_validation = apply_filters('woocommerce_add_to_cart_validation', true, $product_id, $quantity);
						$product_status = get_post_status($product_id);
						if ($passed_validation && $woocommerce->cart->add_to_cart($product_id) && 'publish' === $product_status) {
							$checkout_system = ABPRF_LIB_Function::get_options('abprf_layout', 'checkout_system', 'default');
							if ($checkout_system == 'checkout') {
								printf('%s', esc_url(wc_get_checkout_url()));
							} elseif ($checkout_system == 'cart') {
								printf('%s', esc_url(wc_get_cart_url()));
							}
						}
					}
				}
				wp_die();
			}
		}
		new ABPRF_Ajax();
	}