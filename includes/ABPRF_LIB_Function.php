<?php
	if (!defined('ABSPATH')) {
		exit; // Exit if accessed directly
	}
	if (!class_exists( 'ABPRF_LIB_Function' )) {
		class ABPRF_LIB_Function {
			public function __construct() {
				add_filter('wp_mail_content_type', [$this, 'mail_content_type']);
				add_action('abptm_load_date_picker', [$this, 'load_date_picker'], 10, 2);
			}
			public function mail_content_type(): string { return "text/html"; }
			public static function query_post_type($post_type, $show = -1, $page = 1): WP_Query {
				$args = array(
					'post_type' => $post_type,
					'posts_per_page' => $show,
					'paged' => $page,
					'post_status' => 'publish'
				);
				return new WP_Query($args);
			}
			public static function get_all_post_id($post_type, $show = -1, $page = 1, $status = 'publish'): array {
				$all_data = get_posts(array(
					'fields' => 'ids',
					'post_type' => $post_type,
					'posts_per_page' => $show,
					'paged' => $page,
					'post_status' => $status
				));
				return array_unique($all_data);
			}
			public static function get_post_info($post_id, $key, $default = '') {
				$data = get_post_meta($post_id, $key, true) ?: $default;
				return self::data_sanitize($data);
			}
			public static function get_all_meta($post_id = 0): array {
				$all_data = [];
				if ($post_id > 0) {
					$all_data['post_title'] = get_the_title($post_id);
					$all_data['post_id'] = $post_id;
					$metas = get_post_meta($post_id);
					if (sizeof($metas) > 0) {
						foreach ($metas as $key => $meta) {
							$all_data[$key] = self::data_sanitize($meta[0]);
						}
					}
					$all_data['abprf_configuration'] = ABPRF_LIB_Function::get_option('abprf_configuration');
				}
				return $all_data;
			}
			//=============================//
			public static function get_taxonomy($name): array|WP_Error|string {
				return get_terms(array('taxonomy' => $name, 'hide_empty' => false));
			}
			public static function get_term_meta($meta_id, $meta_key, $default = '') {
				$data = get_term_meta($meta_id, $meta_key, true) ?: $default;
				return self::data_sanitize($data);
			}
			public static function get_all_term_data($term_name, $value = 'name'): array {
				$all_data = [];
				$taxonomies = self::get_taxonomy($term_name);
				if ($taxonomies && is_array($taxonomies) && sizeof($taxonomies) > 0) {
					foreach ($taxonomies as $taxonomy) {
						$all_data[] = $taxonomy->$value;
					}
				}
				return $all_data;
			}
			//=============================//
			public static function data_sanitize($data) {
				$data = maybe_unserialize($data);
				if (is_string($data)) {
					$data = maybe_unserialize($data);
					if (is_array($data)) {
						$data = self::data_sanitize($data);
					} else {
						$data = sanitize_text_field(stripslashes(wp_strip_all_tags($data)));
					}
				} elseif (is_array($data)) {
					foreach ($data as &$value) {
						if (is_array($value)) {
							$value = self::data_sanitize($value);
						} else {
							$value = sanitize_text_field(stripslashes(wp_strip_all_tags($value)));
						}
					}
				}
				return $data;
			}
			//=============================//
			public static function get_option($option, $default = []) {
				$option_data = get_option(sanitize_key($option));
				if (is_array($default)) {
					$option_data = $option_data && is_array($option_data) ? $option_data : $default;
				} else {
					$option_data = $option_data ?: $default;
				}
				return $option_data;
			}
			public static function get_options($option, $key, $default = '') {
				$options = get_option(sanitize_key($option));
				if (isset($options[$key]) && $options[$key]) {
					$default = $options[$key];
				}
				return $default;
			}
			//============= Date Section================//
			public static function date_picker_format(): string {
				$format = ABPRF_Date_Format;
				$date_format = 'Y-m-d';
				$date_format = $format == 'yy/mm/dd' ? 'Y/m/d' : $date_format;
				$date_format = $format == 'yy-dd-mm' ? 'Y-d-m' : $date_format;
				$date_format = $format == 'yy/dd/mm' ? 'Y/d/m' : $date_format;
				$date_format = $format == 'dd-mm-yy' ? 'd-m-Y' : $date_format;
				$date_format = $format == 'dd/mm/yy' ? 'd/m/Y' : $date_format;
				$date_format = $format == 'mm-dd-yy' ? 'm-d-Y' : $date_format;
				$date_format = $format == 'mm/dd/yy' ? 'm/d/Y' : $date_format;
				$date_format = $format == 'd M , yy' ? 'j M , Y' : $date_format;
				$date_format = $format == 'D d M , yy' ? 'D j M , Y' : $date_format;
				$date_format = $format == 'M d , yy' ? 'M  j, Y' : $date_format;
				return $format == 'D M d , yy' ? 'D M  j, Y' : $date_format;
			}
			public function load_date_picker($selector, $dates): void {
				$start_date = current($dates);
				$start_year = gmdate('Y', strtotime($start_date));
				$start_month = (gmdate('n', strtotime($start_date)) - 1);
				$start_day = gmdate('j', strtotime($start_date));
				$end_date = end($dates);
				$end_year = gmdate('Y', strtotime($end_date));
				$end_month = (gmdate('n', strtotime($end_date)) - 1);
				$end_day = gmdate('j', strtotime($end_date));
				$all_date = [];
				foreach ($dates as $date) {
					$all_date[] = '"' . gmdate('j-n-Y', strtotime($date)) . '"';
				}
				?>
                <script>
                    jQuery(document).ready(function () {
                        jQuery("<?php echo esc_attr($selector); ?>").datepicker({
                            dateFormat: abprf_var.date_picker_format,
                            autoSize: true, changeMonth: true, changeYear: true,
                            minDate: new Date(<?php echo esc_attr($start_year); ?>, <?php echo esc_attr($start_month); ?>, <?php echo esc_attr($start_day); ?>),
                            maxDate: new Date(<?php echo esc_attr($end_year); ?>, <?php echo esc_attr($end_month); ?>, <?php echo esc_attr($end_day); ?>),
                            beforeShowDay: available_check,
                            onSelect: function (dateString, data) {
                                let date = data.selectedYear + '-' + ('0' + (parseInt(data.selectedMonth) + 1)).slice(-2) + '-' + ('0' + parseInt(data.selectedDay)).slice(-2);
                                jQuery(this).closest('label').find('input[type="hidden"]').val(date).trigger('change');
                            }
                        });
                        function available_check(date) {
                            let availableDates = [<?php echo wp_kses_post(implode(',', $all_date)); ?>];
                            let dmy = date.getDate() + "-" + (date.getMonth() + 1) + "-" + date.getFullYear();
                            if (jQuery.inArray(dmy, availableDates) !== -1) {
                                return [true, "", "<?php esc_attr_e('Available', 'abprf-rental-forge'); ?>"];
                            } else {
                                return [false, "", "<?php esc_attr_e('Unavailable', 'abprf-rental-forge'); ?>"];
                            }
                        }
                    });
                </script>
				<?php
			}
			public static function date_format($date, $format = 'date'): string {
				$date_format = get_option('date_format');
				$time_format = get_option('time_format');
				$wp_settings = $date_format . '  ' . $time_format;
				//$timezone = wp_timezone_string();
				$timestamp = strtotime($date);
				if ($format == 'date') {
					$date = date_i18n($date_format, $timestamp);
				} elseif ($format == 'time') {
					$date = date_i18n($time_format, $timestamp);
				} elseif ($format == 'full') {
					$date = date_i18n($wp_settings, $timestamp);
				} elseif ($format == 'day') {
					$date = date_i18n('d', $timestamp);
				} elseif ($format == 'month') {
					$date = date_i18n('M', $timestamp);
				} elseif ($format == 'year') {
					$date = date_i18n('Y', $timestamp);
				} else {
					$date = date_i18n($format, $timestamp);
				}
				return $date;
			}
			public static function date_separate_period($start_date, $end_date, $repeat = 1): DatePeriod {
				$repeat = max($repeat, 1);
				$_interval = "P" . $repeat . "D";
				$end_date = gmdate('Y-m-d', strtotime($end_date . ' +1 day'));
				return new DatePeriod(new DateTime($start_date), new DateInterval($_interval), new DateTime($end_date));
			}
			public static function check_time_exit_date($date): bool {
				if ($date) {
					$parse_date = date_parse($date);
					if (($parse_date['hour'] && $parse_date['hour'] > 0) || ($parse_date['minute'] && $parse_date['minute'] > 0) || ($parse_date['second'] && $parse_date['second'] > 0)) {
						return true;
					}
				}
				return false;
			}
			public static function sort_date($a, $b): int { return strtotime($a) - strtotime($b); }
			public static function sort_date_array($a, $b): int {
				$dateA = strtotime($a['time']);
				$dateB = strtotime($b['time']);
				if ($dateA == $dateB) {
					return 0;
				} elseif ($dateA > $dateB) {
					return 1;
				} else {
					return -1;
				}
			}
			public static function get_date_time_difference($date1, $date2): string {
				$text = '';
				if ($date1 && $date2) {
					$date1 = date_create($date1);
					$date2 = date_create($date2);
					$diff = date_diff($date1, $date2);
					$years = $diff->y;
					$months = $diff->m;
					$days = $diff->d;
					$hours = $diff->h;
					$minutes = $diff->i;
					$seconds = $diff->s;
					if ($years > 0) {
						$text = $years > 1 ? $years . ' ' . __('Years', 'abprf-rental-forge') : $years . ' ' . __('Year', 'abprf-rental-forge');
					}
					if ($months > 0) {
						$month_text = $months > 1 ? $months . ' ' . __('Months', 'abprf-rental-forge') : $months . ' ' . __('Month', 'abprf-rental-forge');
						$text .= $text ? ' , ' . $month_text : $month_text;
					}
					if ($days > 0) {
						$day_text = $days > 1 ? $days . ' ' . __('Days', 'abprf-rental-forge') : $days . ' ' . __('Day', 'abprf-rental-forge');
						$text .= $text ? ' , ' . $day_text : $day_text;
					}
					if ($hours > 0) {
						$hour_text = $hours > 1 ? $hours . ' ' . __('Hours', 'abprf-rental-forge') : $hours . ' ' . __('Hour', 'abprf-rental-forge');
						$text .= $text ? ' , ' . $hour_text : $hour_text;
					}
					if ($minutes > 0) {
						$minute_text = $minutes > 1 ? $minutes . ' ' . __('Minutes', 'abprf-rental-forge') : $minutes . ' ' . __('Minute', 'abprf-rental-forge');
						$text .= $text ? ' , ' . $minute_text : $minute_text;
					}
					if ($seconds > 0) {
						$second_text = $seconds > 1 ? $seconds . ' ' . __('Seconds', 'abprf-rental-forge') : $seconds . ' ' . __('Second', 'abprf-rental-forge');
						$text .= $text ? ' , ' . $second_text : $second_text;
					}
				}
				return $text;
			}
			//=============================//
			public static function price_convert_raw($price) {
				$price = wp_strip_all_tags($price);
				$price = str_replace(self::get_option('woocommerce_price_display_suffix', ''), '', $price);
				$price = str_replace(get_woocommerce_currency_symbol(), '', $price);
				$price = str_replace(wc_get_price_thousand_separator(), 't_s', $price);
				$price = str_replace(wc_get_price_decimal_separator(), 'd_s', $price);
				$price = str_replace('t_s', '', $price);
				$price = str_replace('d_s', '.', $price);
				$price = str_replace('&nbsp;', '', $price);
				return max($price, 0);
			}
			public static function wc_price($post_id, $price, $args = array()): string {
				$num_of_decimal = get_option('woocommerce_price_num_decimals', 2);
				$args = wp_parse_args($args, array('qty' => '', 'price' => '',));
				$_product = self::get_post_info($post_id, 'link_wc_id', $post_id);
				$product = wc_get_product($_product);
				$qty = '' !== $args['qty'] ? max(0.0, (float)$args['qty']) : 1;
				$tax_with_price = get_option('woocommerce_tax_display_shop');
				if ('' === $price) {
					return '';
				} elseif (empty($qty)) {
					return 0.0;
				}
				$line_price = (float)$price * (int)$qty;
				$return_price = $line_price;
				if ($product && $product->is_taxable()) {
					$tax_rates = WC_Tax::get_rates($product->get_tax_class());
					$base_tax_rates = WC_Tax::get_base_tax_rates($product->get_tax_class('unfiltered'));
					if (!empty(WC()->customer) && WC()->customer->get_is_vat_exempt()) {
						$remove_taxes = apply_filters('woocommerce_adjust_non_base_location_prices', true) ? WC_Tax::calc_tax($line_price, $base_tax_rates, true) : WC_Tax::calc_tax($line_price, $tax_rates, true);
						if ('yes' === get_option('woocommerce_tax_round_at_subtotal')) {
							$remove_taxes_total = array_sum($remove_taxes);
						} else {
							$remove_taxes_total = array_sum(array_map('wc_round_tax_total', $remove_taxes));
						}
						// $return_price = round( $line_price, $num_of_decimal);
						$return_price = round($line_price - $remove_taxes_total, $num_of_decimal);
					} else {
						$base_taxes = WC_Tax::calc_tax($line_price, $base_tax_rates, true);
						$modded_taxes = WC_Tax::calc_tax($line_price - array_sum($base_taxes), $tax_rates);
						if ('yes' === get_option('woocommerce_tax_round_at_subtotal')) {
							$base_taxes_total = array_sum($base_taxes);
							$modded_taxes_total = array_sum($modded_taxes);
						} else {
							$base_taxes_total = array_sum(array_map('wc_round_tax_total', $base_taxes));
							$modded_taxes_total = array_sum(array_map('wc_round_tax_total', $modded_taxes));
						}
						$return_price = $tax_with_price == 'excl' ? round($line_price - $base_taxes_total, $num_of_decimal) : round($line_price - $base_taxes_total + $modded_taxes_total, $num_of_decimal);
					}
				}
				$return_price = apply_filters('woocommerce_get_price_including_tax', $return_price, $qty, $product);
				$display_suffix = get_option('woocommerce_price_display_suffix') ? get_option('woocommerce_price_display_suffix') : '';
				return wc_price($return_price) . ' ' . $display_suffix;
			}
			public static function get_wc_raw_price($post_id, $price) {
				$price = self::wc_price($post_id, $price);
				return self::price_convert_raw($price);
			}
			//=============================//
			public static function get_image_url($post_id = '', $image_id = '', $size = 'full'): bool|string {
				$image_id = $post_id && $post_id > 0 ? get_post_thumbnail_id($post_id) : $image_id;
				return wp_get_attachment_image_url($image_id, $size);
			}
			public static function get_page_by_slug($slug): bool|WP_Post {
				if ($pages = get_pages()) {
					foreach ($pages as $page) {
						if ($slug === $page->post_name) {
							return $page;
						}
					}
				}
				return false;
			}
			public static function get_id_by_slug($page_slug): ?int {
				$page = get_page_by_path($page_slug);
				return $page?->ID;
			}
			//=============================//
			public static function check_wc(): int {
				include_once(ABSPATH . 'wp-admin/includes/plugin.php');
				$plugin_dir = ABSPATH . 'wp-content/plugins/woocommerce';
				if (in_array('woocommerce/woocommerce.php', get_option('active_plugins'))) {
					return 2;
				} elseif (is_dir($plugin_dir)) {
					return 1;
				} else {
					return 0;
				}
			}
			//=============================//
			public static function get_user_role($user_ID): string {
				global $wp_roles;
				$user_role_list = '';
				$user_data = get_userdata($user_ID);
				$user_role_slug = $user_data->roles;
				if (is_array($user_role_slug) && sizeof($user_role_slug) > 0) {
					$user_count = 0;
					foreach ($user_role_slug as $user_role) {
						$user_count++;
						if ($user_count > 1) {
							$user_role_list .= ", ";
						}
						$user_role_list .= translate_user_role($wp_roles->roles[$user_role]['name']);
					}
				}
				return $user_role_list;
			}
			//=============================//
			public static function get_transport_icon() { return ABPRF_LIB_Function::get_options('abprf_configuration', 'transport_icon'); }
			//=============================//
			public static function array_to_string($array) {
				$ids = '';
				if (sizeof($array) > 0) {
					foreach ($array as $data) {
						if ($data) {
							$ids = $ids ? $ids . ',' . $data : $data;
						}
					}
				}
				return $ids;
			}
			public static function serialize_array_convert($form_data): array {
				$infos = [];
				if (sizeof($form_data) > 0) {
					foreach ($form_data as $data) {
						$_name = is_array($data) && array_key_exists('name', $data) ? sanitize_text_field($data['name']) : '';
						$name = explode('[]', $_name)[0];
						$value = is_array($data) && array_key_exists('value', $data) ? sanitize_text_field($data['value']) : '';
						if ($name) {
							if ($_name !== $name) {
								$infos[$name][] = $value;
							} else {
								$infos[$name] = $value;
							}
						}
					}
				}
				return $infos;
			}
		}
		new ABPRF_LIB_Function();
	}