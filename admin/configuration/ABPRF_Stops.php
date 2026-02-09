<?php
	if (!defined('ABSPATH')) {
		exit; // Exit if accessed directly
	}
	if (!class_exists('ABPRF_Stops')) {
		class ABPRF_Stops {
			public function __construct() {
				add_action('abprf_configuration_content', array($this, 'stops_configuration'));
				add_filter('pre_update_option_abprf_stops', array($this, 'update_abprf_stops'), 10, 2);
			}
			public function stops_configuration($abprf_configuration): void {
				$label = isset($abprf_configuration['label']) && $abprf_configuration['label'] ? $abprf_configuration['label'] : __('Transportation', 'abprf-rental-forge');
				$label = $label . ' ' . __(' : ', 'abprf-rental-forge') . ' ' . __('Stops List', 'abprf-rental-forge');
				$abprf_stops = ABPRF_LIB_Function::get_option('abprf_stops');
				asort($abprf_stops);
				?>
                <div class="tabsItem abprf_stops" data-tabs="#abprf_stops">
                    <h3 class="_abprf"><?php echo esc_html($label); ?></h3>
					<?php ABPRF_LIB_Layout::info_text('abprf_stops'); ?>
                    <div class="_divider_xs"></div>
                    <form method="post" action="options.php">
						<?php settings_fields('abprf_stops'); ?>
						<?php wp_nonce_field('abprf_stops_nonce', 'abprf_stops_nonce'); ?>
                        <div class="abprf_configuration_content">
                            <div class="_abprf_row_mar_lr_xs_neg abprf_insert_item abprf_sortable">
								<?php
									if (sizeof($abprf_stops) > 0) {
										foreach ($abprf_stops as $abptm_stop) {
											if ($abptm_stop) {
												$this->stops_item($abptm_stop);
											}
										}
									}
								?>
                            </div>
                            <div class="abprf_d_none">
                                <div class="abprf_hidden_item"><?php $this->stops_item(); ?></div>
                            </div>
                            <div class="_divider_xs"></div>
                            <div class="_fj_between">
								<?php ABPRF_LIB_Layout::button_add(__('Add New stops', 'abprf-rental-forge'), '', '_btn_navy_blue'); ?>
                                <button type="submit" class="_btn_success_br"><span class="far fa-save _mar_r_xs"></span><?php esc_html_e('Save Stops List', 'abprf-rental-forge'); ?></button>
                            </div>
                        </div>
                    </form>
                </div>
				<?php
			}
			public function stops_item($field = ''): void {
				?>
                <div class="abprf_delete_area _col_3_4_1000_padding_xxs">
                    <div class="_group_content_full_width">
                        <label class="_full_width">
                            <input type="text" class="_form_control validation_name" name=abprf_stops[]" placeholder="<?php esc_attr_e('EX: Boston', 'abprf-rental-forge'); ?>" value="<?php echo esc_attr($field); ?>"/>
                        </label>
						<?php ABPRF_LIB_Layout::button_delete(); ?>
                    </div>
                </div>
				<?php
			}
			public function update_abprf_stops($new_stops, $old_stops): array {
				$abprf_stops = array();
				$nonce = isset($_POST['abprf_stops_nonce']) ? sanitize_text_field(wp_unslash($_POST['abprf_stops_nonce'])) : '';
				if ($nonce && wp_verify_nonce($nonce, 'abprf_stops_nonce')) {
					$abptm_names = isset($_POST['abprf_stops']) ? array_map('sanitize_text_field', wp_unslash($_POST['abprf_stops'])) : [];
					$count = count($abptm_names);
					for ($i = 0; $i < $count; $i++) {
						if ($abptm_names[$i]) {
							$abprf_stops[] = $abptm_names[$i];
						}
					}
					return array_unique($abprf_stops);
				} else {
					$old_stops = is_array($old_stops) ? $old_stops : ABPRF_LIB_Function::get_option('abprf_stops');
					return array_unique(array_merge($old_stops, $new_stops));
				}
			}
		}
		new ABPRF_Stops();
	}