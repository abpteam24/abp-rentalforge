<?php
	if (!defined('ABSPATH')) {
		exit; // Exit if accessed directly
	}
	if (!class_exists('ABPRF_Additional')) {
		class ABPRF_Additional {
			public function __construct() {
				add_action('abprf_configuration_content', array($this, 'additional_service_global'));
				add_filter('pre_update_option_abprf_additional', array($this, 'update_abprf_additional'), 10, 2);
				//=============================//
				add_action('abprf_post_content', [$this, 'additional_configuration']);
				//=============================//
				add_action('wp_ajax_abprf_import_additional', array($this, 'abprf_import_additional'));
			}
			public function additional_service_global($abprf_configuration): void {
				$label = isset($abprf_configuration['label']) && $abprf_configuration['label'] ? $abprf_configuration['label'] : __('RentalForge', 'abprf-rental-forge');
				$label = $label . ' ' . __(' : ', 'abprf-rental-forge') . ' ' . __('Additional services', 'abprf-rental-forge');
				$additional_services = ABPRF_Function::get_option('abprf_additional', ABPRF_Layout::static_additional());
				?>
                <div class="tab_item additional_configuration" data-tabs="#abprf_additional">
                    <h3 class="_abprf"><?php echo esc_html($label); ?></h3>
					<?php ABPRF_Layout::info_text('additional_services'); ?>
                    <div class="_divider_xs"></div>
                    <form method="post" action="options.php">
						<?php settings_fields('abprf_additional'); ?>
						<?php $this->additional_service($additional_services, true); ?>
                    </form>
                </div>
				<?php
			}
			public function update_abprf_additional() {
				return self::service_info();
			}
			//=============================//
			public function additional_configuration($abprf_infos): void {
				$abprf_configuration = array_key_exists('abprf_configuration', $abprf_infos) ? $abprf_infos['abprf_configuration'] : [];
				$post_title = array_key_exists('post_title', $abprf_infos) ? $abprf_infos['post_title'] : '';
				$equipment_icon = isset($abprf_configuration['equipment_icon']) && $abprf_configuration['equipment_icon'] ? $abprf_configuration['equipment_icon'] : 'fas fa-hammer';
				$additional_services = array_key_exists('additional_services', $abprf_infos) ? $abprf_infos['additional_services'] : array();
				$display = array_key_exists('display_additional_services', $abprf_infos) ? $abprf_infos['display_additional_services'] : 'on';
				?>
                <div class="tab_item additional_configuration" data-tabs="#abprf_additional_service">
                    <h4 class="_abprf_color_theme"><span class="<?php echo esc_attr($equipment_icon); ?> _mar_r_xs"></span> <?php echo esc_html($post_title . ' ' . __(' : ', 'abprf-rental-forge') . ' ' . __('Additional services Configuration', 'abprf-rental-forge')); ?></h4>
                    <div class="_divider_xs"></div>
                    <div class="_setting_item">
                        <div class="_f_equal_f_wrap">
                            <div class="_fa_center">
								<?php ABPRF_Layout::switch_checkbox('display_additional_services', $display); ?>
                                <span class="_fs_label_mar_lr_xs"><?php esc_html_e('Additional services', 'abprf-rental-forge'); ?></span>
                            </div>
                            <div data-collapse="#display_additional_services" class="<?php echo esc_attr($display == 'on' ? 'rf_active' : ''); ?>">
                                <button type="button" class="_btn_theme abprf_import_additional"><span class="fas fa-file-upload _mar_r_xs"></span><?php esc_html_e('Import Additional Service', 'abprf-rental-forge'); ?></button>
                            </div>
                        </div>
                        <div class="_divider_xs"></div>
						<?php ABPRF_Layout::info_text('display_additional_services'); ?>
                    </div>
                    <div class="<?php echo esc_attr($display == 'on' ? 'rf_active' : ''); ?>" data-collapse="#display_additional_services">
                        <div class="abprf_additional_content">
							<?php $this->additional_service($additional_services); ?>
                        </div>
                    </div>
                </div>
				<?php
			}
			public function additional_service($services = [], $global = ''): void {
				wp_nonce_field('abprf_additional_nonce', 'abprf_additional_nonce');
				?>
                <div class="abprf_configuration_content">
                    <div class="_ov_auto">
                        <table class="_abprf">
                            <thead>
                            <tr>
                                <th class="_w_125"><?php esc_html_e('Icon / Image', 'abprf-rental-forge'); ?></th>
                                <th class="_min_200"><?php esc_html_e('Name', 'abprf-rental-forge'); ?><sup class="_color_required">*</sup></th>
                                <th class="_min_100"><?php esc_html_e('Quantity', 'abprf-rental-forge'); ?><sup class="_color_required">*</sup></th>
                                <th class="_min_100"><?php esc_html_e('Price', 'abprf-rental-forge'); ?><sup class="_color_required">*</sup></th>
                                <th class="_min_100"><?php esc_html_e('Max qty/Order', 'abprf-rental-forge'); ?></th>
                                <th class="_min_250"><?php esc_html_e('Description', 'abprf-rental-forge'); ?></th>
                                <th class="_w_75"><?php esc_html_e('Action', 'abprf-rental-forge'); ?></th>
                            </tr>
                            </thead>
                            <tbody class="abprf_insert_item abprf_sortable">
							<?php
								if ($services && is_array($services) && sizeof($services) > 0) {
									foreach ($services as $key => $service) {
										$this->service_item($key, $service);
									}
								}
							?>
                            </tbody>
                        </table>
                    </div>
                    <div class="_divider_xs"></div>
                    <div class="_fj_between">
						<?php ABPRF_Layout::button_add(__('Add New Additional services', 'abprf-rental-forge')); ?>
						<?php if ($global) { ?>
                            <button type="submit" class="_btn_success_br"><span class="far fa-save _mar_r_xs"></span><?php esc_html_e('Save Additional Service', 'abprf-rental-forge'); ?></button>
						<?php } ?>
                    </div>
                    <div class="abprf_d_none">
                        <table class="_abprf">
                            <tbody class="abprf_hidden_item">
							<?php $this->service_item(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
				<?php
			}
			public function service_item($key = '', $field = array()): void {
				$field = $field ?: array();
				$icon_image = array_key_exists('icon', $field) ? $field['icon'] : '';
				$name = array_key_exists('name', $field) ? $field['name'] : '';
				$qty = array_key_exists('qty', $field) ? $field['qty'] : '';
				$max_ty = array_key_exists('max_qty', $field) ? $field['max_qty'] : '';
				$price = array_key_exists('price', $field) ? $field['price'] : '';
				$description = array_key_exists('description', $field) ? $field['description'] : '';
				?>
                <tr class="abprf_delete_area ">
                    <td> <?php do_action('abprf_add_image_icon', 'additional_icon[]', $icon_image); ?>  </td>
                    <td>
                        <input type="hidden" name="additional_id[]" value="<?php echo esc_attr($key ?: uniqid()); ?>"/>
                        <label>
                            <input type="text" class="_form_control validation_name" name="additional_name[]" placeholder="<?php esc_attr_e('EX: Water Bottle', 'abprf-rental-forge'); ?>" value="<?php echo esc_attr($name); ?>"/>
                        </label>
                    </td>
                    <td>
                        <label>
                            <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="additional_qty[]" placeholder="<?php esc_attr_e('EX: 15', 'abprf-rental-forge'); ?>" value="<?php echo esc_attr($qty); ?>"/>
                        </label>
                    </td>
                    <td>
                        <label>
                            <input type="number" pattern="[0-9]*" step="0.01" class="_form_control validation_price" name="additional_price[]" placeholder="<?php esc_attr_e('EX: 15', 'abprf-rental-forge'); ?>" value="<?php echo esc_attr($price); ?>"/>
                        </label>
                    </td>
                    <td>
                        <label>
                            <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="additional_max_qty[]" placeholder="<?php esc_attr_e('EX: 15', 'abprf-rental-forge'); ?>" value="<?php echo esc_attr($max_ty); ?>"/>
                        </label>
                    </td>
                    <td>
                        <label>
                            <textarea class="_form_control" name="additional_description[]" placeholder="<?php esc_attr_e('EX: Description', 'abprf-rental-forge'); ?>"><?php echo esc_html($description); ?></textarea>
                        </label>
                    </td>
                    <td><?php ABPRF_Layout::button_delete_sort(); ?></td>
                </tr>
				<?php
			}
			//=============================//
			public static function service_info() {
				$additional_services = array();
				if (isset($_POST['abprf_additional_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['abprf_additional_nonce'])), 'abprf_additional_nonce')) {
					$additional_ids = isset($_POST['additional_id']) ? array_map('sanitize_text_field', wp_unslash($_POST['additional_id'])) : [];
					$additional_icon = isset($_POST['additional_icon']) ? array_map('sanitize_text_field', wp_unslash($_POST['additional_icon'])) : [];
					$additional_name = isset($_POST['additional_name']) ? array_map('sanitize_text_field', wp_unslash($_POST['additional_name'])) : [];
					$additional_qty = isset($_POST['additional_qty']) ? array_map('sanitize_text_field', wp_unslash($_POST['additional_qty'])) : [];
					$max_qty = isset($_POST['additional_max_qty']) ? array_map('sanitize_text_field', wp_unslash($_POST['additional_max_qty'])) : [];
					$additional_price = isset($_POST['additional_price']) ? array_map('sanitize_text_field', wp_unslash($_POST['additional_price'])) : [];
					$additional_description = isset($_POST['additional_description']) ? array_map('sanitize_textarea_field', wp_unslash($_POST['additional_description'])) : [];
					if (sizeof($additional_ids) > 0) {
						foreach ($additional_ids as $key => $additional_id) {
							if ($additional_name[$key] && $additional_price[$key] >= 0) {
								$additional_id = array_key_exists($additional_id, $additional_services) ? uniqid() : $additional_id;
								$additional_services[$additional_id]['icon'] = $additional_icon[$key] ?? '';
								$additional_services[$additional_id]['name'] = $additional_name[$key];
								$additional_services[$additional_id]['qty'] = $additional_qty[$key];
								$additional_services[$additional_id]['max_qty'] = $max_qty[$key];
								$additional_services[$additional_id]['price'] = $additional_price[$key];
								$additional_services[$additional_id]['description'] = $additional_description[$key] ?? '';
							}
						}
					}
				}
				return apply_filters('additional_services_filter', $additional_services);
			}
			//=============================//
			public function abprf_import_additional(): void {
				if (is_admin() && isset($_POST['nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'abprf_admin_ajax_nonce')) {
					$additional_services = get_option('abprf_additional');
					$additional_services = $additional_services && is_array($additional_services) && sizeof($additional_services)>0 ? $additional_services : ABPRF_Layout::static_additional();
					$this->additional_service($additional_services);
				}
				wp_die();
			}
		}
		new ABPRF_Additional();
	}