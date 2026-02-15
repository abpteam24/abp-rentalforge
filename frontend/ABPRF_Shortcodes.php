<?php
	if (!defined('ABSPATH')) {
		exit; // Exit if accessed directly
	}
	if (!class_exists('ABPRF_Shortcodes')) {
		class ABPRF_Shortcodes {
			public function __construct() {
				add_shortcode('abprf-equipment', array($this, 'abptm_search'));
				add_shortcode('abprf-list', array($this, 'abptm_list'));
				add_shortcode('abptm-route', array($this, 'abptm_route'));
			}
			public function abptm_search($attribute): bool|string {
				$defaults = $this->default_attribute();
				$params = shortcode_atts($defaults, $attribute);
				ob_start();
				do_action('woocommerce_before_single_product');
				$form_data = ABPRF_Function::get_form_data();
				//echo '<pre>';print_r($form_data);echo '</pre>';
				?>
                <div id="abprf_area" class="abprf_area">
                    <div class="abprf_container">
						<?php do_action('abprf_search_form', [], $params, $form_data); ?>
                        <div class=" abprf_rental_result">
							<?php ABPRF_Layout::transport_list($form_data); ?>
                        </div>
                    </div>
                </div>
				<?php
				return ob_get_clean();
			}
			public function abptm_list($attribute): bool|string {
				$defaults = $this->default_attribute();
				$params = shortcode_atts($defaults, $attribute);
				$style = array_key_exists('style', $params) ? $params['style'] : 'grid';
				$file = ABPRF_Function::template_path('list/' . $style . '.php');
				ob_start();
				?>
                <div class="abprf_area">
                    <div class="abprf_container">
						<?php if (is_file($file)) {
							require $file;
						} else {
							require ABPRF_Function::template_path('list/grid.php');
						} ?>
                    </div>
                </div>
				<?php
				return ob_get_clean();
			}
			public function abptm_route($attribute): bool|string {
				$defaults = $this->default_attribute();
				$params = shortcode_atts($defaults, $attribute);
				$style = array_key_exists('style', $params) ? $params['style'] : 'grid';
				$file = ABPRF_Function::template_path('route/' . $style . '.php');
				ob_start();
				?>
                <div class="abprf_area">
                    <div class="abprf_container">
						<?php
							$from = array_key_exists('from', $params) ? $params['from'] : '';
							$to = array_key_exists('to', $params) ? $params['to'] : '';
							$cat = array_key_exists('cat', $params) ? $params['cat'] : '';
							$show_post = array_key_exists('post', $params) && $params['post'] ? $params['post'] : 50;
							$transports = ABPRF_Query::get_equipment_id($from, $to, $cat);
							if (sizeof($transports) > 0) {
								$post_count = 0;
								$args['total'] = sizeof($transports);
								$args['page_item'] = $show_post;
								$all_route = [];
								$dummy_route = [];
								$count = 0;
								foreach ($transports as $equipment_id) {
									$direction = ABPRF_LIB_Function::get_post_info($equipment_id, 'route_direction', []);
									if (sizeof($direction) > 0) {
										$start = current($direction);
										$end = end($direction);
										$location = $start . '-' . $end;
										if (!in_array($location, $dummy_route)) {
											$dummy_route[] = $location;
											$all_route[$count]['start'] = $start;
											$all_route[$count]['end'] = $end;
											$count++;
										}
									}
								}
								if (is_file($file)) {
									require $file;
								} else {
									require ABPRF_Function::template_path('route/button.php');
								}
							} else {
								ABPRF_Layout::layout_warning_info('not_found');
							}
						?>
                    </div>
                </div>
				<?php
				return ob_get_clean();
			}
			public function default_attribute(): array {
				return array(
					"cat" => '',
					"from" => '',
					"to" => '',
					"org" => '',
					"style" => 'grid',
					"post" => '',
					'sort' => 'ASC',
					"pagination" => "yes",
					"pagination-style" => "live",
					"column" => 3,
					'form' => 'inline',
					'transport' => '',
					'return' => '',
				);
			}
		}
		new ABPRF_Shortcodes();
	}