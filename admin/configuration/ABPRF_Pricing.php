<?php
	if (!defined('ABSPATH')) {
		exit; // Exit if accessed directly
	}
	if (!class_exists('ABPRF_Pricing')) {
		class ABPRF_Pricing {
			public function __construct() {
				add_action('abprf_post_content', [$this, 'tab_content']);
				//=============================//
				add_action('wp_ajax_abprf_reload_pricing', [$this, 'abptm_reload_pricing']);
				add_action('wp_ajax_nopriv_abprf_reload_pricing', [$this, 'abptm_reload_pricing']);
			}
			public function tab_content($abprf_infos): void {
				$abprf_configuration = array_key_exists('abprf_configuration', $abprf_infos) ? $abprf_infos['abprf_configuration'] : [];
				$post_title = array_key_exists('post_title', $abprf_infos) ? $abprf_infos['post_title'] : '';
				$transport_icon = isset($abprf_configuration['transport_icon']) && $abprf_configuration['transport_icon'] ? $abprf_configuration['transport_icon'] : 'fas fa-bus';
				$routing_infos = array_key_exists('routing_infos', $abprf_infos) ? $abprf_infos['routing_infos'] : [];
				$price_infos = array_key_exists('price_infos', $abprf_infos) ? $abprf_infos['price_infos'] : [];
				$ticket_types = ABPRF_Function::get_ticket_type_key($abprf_infos);
				$price_infos = sizeof($price_infos) > 0 ? $price_infos : ABPRF_Function::route_for_price($routing_infos, $price_infos, $ticket_types);
				?>
                <div class="tabsItem " data-tabs="#abprf_pricing">
                    <h4 class="_abprf_color_theme"><span class="<?php echo esc_attr($transport_icon); ?> _mar_r_xs"></span> <?php echo esc_html($post_title . ' ' . __(' : ', 'abprf-rental-forge') . ' ' . __('Price Configuration', 'abprf-rental-forge')); ?></h4>
                    <div class="_divider_xs"></div>
                    <div class="abprf_pricing">
						<?php $this->price_settings($price_infos, $ticket_types); ?>
                    </div>
                </div>
				<?php
			}
			public function price_settings($price_infos, $ticket_types): void {
				if (sizeof($price_infos) > 0) {
					?>
                    <div class="abprf_configuration_content _ov_auto">
                        <table class="_abprf">
                            <thead>
                            <tr>
                                <th class="_text_left_min_150"><span class="fas fa-route _mar_r_xs"></span><?php esc_html_e('From', 'abprf-rental-forge'); ?></th>
                                <th class="_text_left_min_150"><span class="fas fa-route _mar_r_xs"></span><?php esc_html_e('To', 'abprf-rental-forge'); ?></th>
								<?php if (sizeof($ticket_types) > 0) {
									foreach ($ticket_types as $key => $ticket_type) { ?>
                                        <th class="_min_100"><span data-input-change="<?php echo esc_attr($key); ?>"><?php echo esc_html($ticket_type); ?></span>&nbsp;<?php esc_html_e('Price', 'abprf-rental-forge'); ?></th>
									<?php }
								} else { ?>
                                    <th class="_w_125"><?php esc_html_e('Price', 'abprf-rental-forge'); ?></th>
								<?php } ?>
                            </tr>
                            </thead>
                            <tbody>
							<?php foreach ($price_infos as $price_info) { ?>
                                <tr class="pricing_item_row">
                                    <th><input type="hidden" name="abptm_from[]" value="<?php echo esc_attr($price_info['bp']); ?>"/> <?php echo esc_html($price_info['bp']); ?></th>
                                    <th><input type="hidden" name="abptm_to[]" value="<?php echo esc_attr($price_info['dp']); ?>"/> <?php echo esc_html($price_info['dp']); ?></th>
									<?php $price = array_key_exists('price', $price_info) ? (float)$price_info['price'] : '';
										if (sizeof($ticket_types) > 0) {
											foreach ($ticket_types as $key => $ticket_type) {
												$price = array_key_exists($key, $price_info) ? $price_info[$key] : ''; ?>
                                                <th><label> <input type="text" class="_form_control validation_price" name="abptm_<?php echo esc_attr($key); ?>_price[]" placeholder="Ex: 10" value="<?php echo esc_attr($price); ?>"/> </label></th>
											<?php }
										} else { ?>
                                            <th><label> <input type="text" class="_form_control validation_price" name="abptm_price[]" placeholder="Ex: 10" value="<?php echo esc_attr($price); ?>"/> </label></th>
										<?php } ?>
                                </tr>
							<?php } ?>
                            </tbody>
                        </table>
                    </div>
					<?php
				} else {
					ABPRF_LIB_Layout::layout_warning_info('abprf_pricing_warning');
				}
			}
			//=============================//
			public function abptm_reload_pricing() {
				if (is_admin() && isset($_POST['nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'abprf_admin_ajax_nonce')) {
					$seat_type = isset($_POST['seat_type']) ? sanitize_text_field(wp_unslash($_POST['seat_type'])) : 'seat_plan';
					$post_id = isset($_POST['post_id']) ? sanitize_text_field(wp_unslash($_POST['post_id'])) : 0;
					if ($seat_type == 'seat_plan') {
						$abprf_infos['post_id'] = $post_id;
						$abprf_infos['seat_type'] = $seat_type;
						$abprf_infos['ticket_type'] = isset($_POST['ticket_type']) ? sanitize_text_field(wp_unslash($_POST['ticket_type'])) : '';
						$ticket_types = ABPRF_Function::get_ticket_type_key($abprf_infos);
					} else {
						$ticket_types = isset($_POST['ticket_types']) ? array_map('sanitize_text_field', wp_unslash($_POST['ticket_types'])) : [];
					}
					// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
					$route_info = isset($_POST['route_infos']) ? wp_unslash($_POST['route_infos']) : [];
					$route_infos = ABPRF_LIB_Function::data_sanitize($route_info);
					$prices = ABPRF_LIB_Function::get_post_info($post_id, 'price_infos', []);
					$price_infos = ABPRF_Function::route_for_price($route_infos, $prices, $ticket_types);
					$this->price_settings($price_infos, $ticket_types);
				}
				wp_die();
			}
		}
		new ABPRF_Pricing();
	}