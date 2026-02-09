<?php
	if (!defined('ABSPATH')) {
		exit; // Exit if accessed directly
	}
	if (!class_exists('ABPRF_Configuration')) {
		class ABPRF_Configuration {
			public function __construct() {
				add_action('admin_init', array($this, 'admin_init'));
				add_action('admin_menu', array($this, 'configuration_menu'));
				add_action('update_option_abprf_configuration', array($this, 'permalink_flush'));
				add_filter('pre_update_option_abprf_configuration', array($this, 'update_sanitize'), 10, 3);
				add_filter('pre_update_option_abprf_transport', array($this, 'update_sanitize'), 10, 3);
				add_filter('pre_update_option_abprf_layout', array($this, 'update_sanitize'), 10, 3);
				add_filter('pre_update_option_abprf_pdf', array($this, 'update_sanitize'), 10, 3);
				add_filter('pre_update_option_abprf_pdf_list', array($this, 'update_sanitize'), 10, 3);
				add_filter('pre_update_option_abprf_csv', array($this, 'update_sanitize'), 10, 3);
				add_filter('pre_update_option_abprf_mail', array($this, 'update_sanitize'), 10, 3);
				add_filter('pre_update_option_abprf_contact', array($this, 'update_sanitize'), 10, 3);
				add_filter('pre_update_option_abprf_slider', array($this, 'update_sanitize'), 10, 3);
				add_filter('pre_update_option_abprf_css_var', array($this, 'update_sanitize'), 10, 3);
			}
			public function permalink_flush(): void {
				flush_rewrite_rules();
			}
			public function update_sanitize($new, $old, $option) {
				$abprf_configuration = ABPRF_LIB_Function::get_option('abprf_configuration');
				$all_fields = $this->configuration_data($abprf_configuration);
				$field_infos = array_key_exists($option, $all_fields) ? $all_fields[$option] : array();
				if (sizeof($field_infos) > 0 && is_array($new)) {
					foreach ($field_infos as $field_info) {
						$name = array_key_exists('name', $field_info) ? $field_info['name'] : '';
						$type = array_key_exists('type', $field_info) ? $field_info['type'] : '';
						if ($type == 'wp_editor') {
							$new[$name] = sanitize_text_field(htmlentities($new[$name]));
						} else {
							$new[$name] = sanitize_text_field($new[$name]);
						}
					}
				}
				return sizeof($new) > 0 ? $new : $old;
			}
			public function admin_init(): void {
				$abprf_configuration = ABPRF_LIB_Function::get_option('abprf_configuration');
				foreach ($this->configuration_section($abprf_configuration) as $section) {
					register_setting($section['id'], $section['id'], array($this, 'sanitize_options'));
				}
			}
			public function configuration_menu(): void {
				if (in_array('woocommerce/woocommerce.php', get_option('active_plugins'))) {
					$label = __('Configuration', 'abprf-rental-forge');
					add_submenu_page('edit.php?post_type=abprf_post', $label, $label, 'manage_options', 'configuration', array($this, 'configuration'));
				} else {
					$abprf_configuration = ABPRF_LIB_Function::get_option('abprf_configuration');
					$label = isset($abprf_configuration['label']) && $abprf_configuration['label'] ? $abprf_configuration['label'] : __('Transportation', 'abprf-rental-forge');
					add_menu_page($label, $label, 'manage_options', 'configuration', array($this, 'configuration'), 'dashicons-car', 6);
				}
			}
			public function configuration(): void {
				$abprf_configuration = ABPRF_LIB_Function::get_option('abprf_configuration');
				?>
                <div class="abprf_area" id="abprf_configuration">
                    <div class="abprf_container">
                        <div class="_abprf_panel_margin">
                            <div class="abprf_tabs tab_left">
                                <ul class="_abprf tab_lists">
                                    <li class="_color_theme_padding_xs_fs_h3_text_center"><?php esc_html_e('Configuration', 'abprf-rental-forge'); ?></li>
                                    <li data-tabs-target="#abprf_tools"><span class="fas fa-tools"></span><?php esc_html_e('Tools & Info', 'abprf-rental-forge'); ?></li>
									<?php foreach ($this->configuration_section($abprf_configuration) as $tab) { ?>
                                        <li data-tabs-target="#<?php echo esc_attr($tab['id']); ?>"><span class="<?php echo esc_attr(array_key_exists('icon', $tab) ? $tab['icon'] : ''); ?>"></span><?php echo esc_html($tab['menu']); ?></li>
									<?php } ?>
                                </ul>
                                <div class="tab_content _bg_white">
									<?php
										do_action('abprf_configuration_content', $abprf_configuration);
										$this->show_tab_content($abprf_configuration);
									?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
				<?php
			}
			public function show_tab_content($abprf_configuration): void {
				$label = isset($abprf_configuration['label']) && $abprf_configuration['label'] ? $abprf_configuration['label'] : __('Transportation', 'abprf-rental-forge');
				$all_fields = $this->configuration_data($abprf_configuration);
				foreach ($this->configuration_section($abprf_configuration) as $form) {
					$section_id = $form['id'];
					$fields = array_key_exists($section_id, $all_fields) ? $all_fields[$section_id] : array();
					if (sizeof($fields) > 0) {
						?>
                        <div class="tabsItem" data-tabs="#<?php echo esc_attr($section_id); ?>">
                            <h3 class="_abprf"><?php echo esc_html($label . __(' : ', 'abprf-rental-forge') . $form['menu'] . ' ' . __('Configuration', 'abprf-rental-forge')); ?></h3>
                            <div class="_divider_xs"></div>
                            <form method="post" action="options.php">
								<?php settings_fields($section_id);
									$options = ABPRF_LIB_Function::get_option($section_id);
									foreach ($fields as $option) {
										$name = array_key_exists('name', $option) ? $option['name'] : '';
										if ($name == 'collapse_start') {
											$collapse = $option['collapse'] ?? '';
											$collapse_data = $option['collapse_data'] ?? '';
											$target_value = ABPRF_LIB_Function::get_options($collapse_data['option'], $collapse_data['key'], $collapse);
											?>
                                            <div class="<?php echo esc_attr($target_value == 'on' ? 'rf_active' : ''); ?>" data-collapse="<?php echo esc_attr('#' . $collapse_data['option'] . '[' . $collapse_data['key'] . ']'); ?>">
										<?php } elseif ($name == 'collapse_end') { ?>
                                            </div>
										<?php } else {
											$type = array_key_exists('type', $option) ? $option['type'] : '';
											$label = array_key_exists('label', $option) ? $option['label'] : '';
											if ($name && $type && $label) {
												$args = array(
													'id' => $name,
													'section' => $section_id,
													'std' => $option['default'] ?? '',
													'desc' => $option['desc'] ?? '',
													'options' => $option['options'] ?? '',
													'placeholder' => $option['placeholder'] ?? '',
													'validation' => $option['validation'] ?? '',
													'min' => $option['min'] ?? '',
													'max' => $option['max'] ?? '',
												);
												$value = isset($options[$name]) && $options[$name] ? $options[$name] : ($option['default'] ?? '');
												$name = $section_id . '[' . $name . ']';
												$this->$type($args, $label, $name, $value);
											}
										}
									}
								?>
                                <div class="_divider_xs"></div>
                                <button type="submit" class="_btn_theme" value="submit"><span class="far fa-save _mar_r_xs"></span><?php echo esc_html(__('Save', 'abprf-rental-forge') . ' ' . $form['menu'] . ' ' . __('Configuration', 'abprf-rental-forge')); ?></button>
                            </form>
                        </div>
						<?php
					}
				}
			}
			public function configuration_section($abprf_configuration): array {
				$label = isset($abprf_configuration['label']) && $abprf_configuration['label'] ? $abprf_configuration['label'] : __('Transportation', 'abprf-rental-forge');
				$transport_icon = isset($abprf_configuration['transport_icon']) && $abprf_configuration['transport_icon'] ? $abprf_configuration['transport_icon'] : 'fas fa-bus';
				$stops = apply_filters('abprf_stops_after', array(array('id' => 'abprf_stops', 'icon' => 'fas fa-map-pin', 'menu' => __('Stops List', 'abprf-rental-forge'))));
				$additional = apply_filters('abprf_slider_after', array(
					array('id' => 'abprf_additional', 'icon' => 'fas fa-hand-holding-usd', 'menu' => __('Additional services', 'abprf-rental-forge')),
					array('id' => 'abprf_configuration', 'icon' => 'fas fa-globe', 'menu' => $label),
					array('id' => 'abprf_rental', 'icon' => $transport_icon, 'menu' => __('Rental', 'abprf-rental-forge')),
					array('id' => 'abprf_layout', 'icon' => 'fas fa-layer-group', 'menu' => __('Layout', 'abprf-rental-forge')),
					array('id' => 'abprf_slider', 'icon' => 'fas fa-photo-video', 'menu' => __('Slider', 'abprf-rental-forge')),
				));
				$contact = apply_filters('abptm_contact_after', array(
					array('id' => 'abptm_contact', 'icon' => 'fas fa-id-card-alt', 'menu' => __('Contact Information', 'abprf-rental-forge')),
					array('id' => 'abprf_css_var', 'icon' => 'fas fa-drafting-compass', 'menu' => __('CSS Value', 'abprf-rental-forge')),
				));
				return array_merge($stops, $additional, $contact);
			}
			public function configuration_data($abprf_configuration) {
				$current_date = current_time('Y-m-d');
				return apply_filters('abprf_configuration_data_filter', array(
					'abprf_configuration' => apply_filters('abprf_configuration_filter', array(
						array(
							'name' => 'label',
							'label' => __('Label', 'abprf-rental-forge'),
							'desc' => __('This is where you may modify the dashboard menu label if you would like.', 'abprf-rental-forge'),
							'type' => 'text',
							'default' => __('Transportation', 'abprf-rental-forge'),
						),
						array(
							'name' => 'slug',
							'label' => __('Slug', 'abprf-rental-forge'),
							'desc' => __('Please input the desired slug name. Do not forget, once you modify this slug, you must refresh the permalink by going to', 'abprf-rental-forge') . ' ' . '<strong class="_abprf_color_theme">' . __('configuration-> Permalinks', 'abprf-rental-forge') . '</strong> ' . __('and clicking on the Save configuration button.', 'abprf-rental-forge'),
							'type' => 'text',
							'default' => 'transport'
						),
						array(
							'name' => 'icon',
							'label' => __('Dashboard Menu Icon', 'abprf-rental-forge'),
							'desc' => __('You can modify the icon in the dashboard menu from this location. The only icons that can be used on the dashboard are Dashicons. Kindly visit the ', 'abprf-rental-forge') . ' ' . '<a class="_abprf" href=https://developer.wordpress.org/resource/dashicons/ target=_blank>' . __('Dashicons Library,', 'abprf-rental-forge') . '</a>' . ' ' . __('retrieve your icon code, and paste it in this location. ', 'abprf-rental-forge'),
							'type' => 'text',
							'default' => 'dashicons-car'
						),
						array(
							'name' => 'transport_icon',
							'label' => __('Transport Icon', 'abprf-rental-forge'),
							'desc' => __('If you wish to alter the transportation symbol, you can do so from this location. ', 'abprf-rental-forge'),
							'type' => 'fontawesome',
							'default' => 'fas fa-bus'
						),
						array(
							'name' => 'category_label',
							'label' => __('Category Label', 'abprf-rental-forge'),
							'desc' => __('If you wish to modify the category label on the dashboard menu, you can do so here. ', 'abprf-rental-forge'),
							'type' => 'text',
							'default' => __('Category', 'abprf-rental-forge')
						),
						array(
							'name' => 'cat_slug',
							'label' => __('Category Slug', 'abprf-rental-forge'),
							'desc' => __('Please input the desired slug name for the category. Do not forget, after updating this slug, you must refresh permalinks. Simply navigate to  ', 'abprf-rental-forge') . '<strong class="_abprf_color_theme">' . __('configuration-> Permalinks', 'abprf-rental-forge') . '</strong> ' . __('and click on the Save Configuration button. ', 'abprf-rental-forge'),
							'type' => 'text',
							'default' => 'transport_category'
						),
						array(
							'name' => 'organizer_label',
							'label' => __('Organizer Label', 'abprf-rental-forge'),
							'desc' => __('You can modify the Organizer label in the dashboard menu within this section. ', 'abprf-rental-forge'),
							'type' => 'text',
							'default' => __('Organizer', 'abprf-rental-forge')
						),
						array(
							'name' => 'org_slug',
							'label' => __('Organizer Slug', 'abprf-rental-forge'),
							'desc' => __('Please input the desired slug name for the Organizer. Do not forget, after updating this slug, you must refresh permalinks. Simply navigate to  ', 'abprf-rental-forge') . '<strong class="_abprf_color_theme">' . __('configuration-> Permalinks', 'abprf-rental-forge') . '</strong> ' . __('and click on the Save Configuration button. ', 'abprf-rental-forge'),
							'type' => 'text',
							'default' => 'transport_organizer'
						),
					)),
					'abprf_rental' => apply_filters('abprf_rental_filter', array(
						array(
							'name' => 'booked_status',
							'label' => __('Booked Status', 'abprf-rental-forge'),
							'desc' => __('Please choose the order status for which the seat will be reserved/decreased.', 'abprf-rental-forge'),
							'type' => 'multi_check',
							'default' => 'wc-processing,wc-completed',
							'options' => in_array('woocommerce/woocommerce.php', get_option('active_plugins')) ? wc_get_order_statuses() : []
						),
						array(
							'name' => 'periodic_start_date',
							'label' => __('Sale Start after', 'abprf-rental-forge'),
							'desc' => __('If you want to begin selling tickets after a specific date, please choose that date. Otherwise, sales will proceed without restriction. ', 'abprf-rental-forge'),
							'type' => 'datepicker',
						),
						array(
							'name' => 'periodic_end_date',
							'label' => __('Sale close after', 'abprf-rental-forge'),
							'desc' => __('If you wish to stop ticket sales after a certain date, please indicate the chosen date. Otherwise, sales will proceed indefinitely. ', 'abprf-rental-forge'),
							'type' => 'datepicker',
						),
						array(
							'name' => 'advance_date_number',
							'label' => __('Number of advance booking date', 'abprf-rental-forge'),
							'desc' => ABPRF_Static_Array::array_info('advance_date_number'),
							'type' => 'number',
							'placeholder' => '28',
							'min' => 1,
							'default' => 28,
							'validation' => 'validation_number'
						),
						array(
							'name' => 'ticket_sale_close_before',
							'label' => __('Buffer time in MIN', 'abprf-rental-forge'),
							'desc' => __('Enter the time in minutes to close ticket sales before the transport starts. If not specified, it will default to 0 (e.g. 1 hour equals 60 minutes). ', 'abprf-rental-forge'),
							'type' => 'number',
							'placeholder' => '60',
							'min' => 0,
							'default' => 0,
							'validation' => 'validation_number'
						),
					)),
					'abprf_layout' => apply_filters('abprf_layout_filter', array(
						array(
							'name' => 'date_format',
							'label' => __('Date Picker Format', 'abprf-rental-forge'),
							'desc' => __('If you wish to edit the Date Picker Format, simply choose a different format. The default date is: ', 'abprf-rental-forge') . ' <strong class="_abprf_color_theme">' . date_i18n('D j M , Y', strtotime($current_date)) . '</strong>',
							'type' => 'select',
							'default' => 'D d M , yy',
							'options' => array(
								'yy-mm-dd' => $current_date,
								'yy/mm/dd' => date_i18n('Y/m/d', strtotime($current_date)),
								'yy-dd-mm' => date_i18n('Y-d-m', strtotime($current_date)),
								'yy/dd/mm' => date_i18n('Y/d/m', strtotime($current_date)),
								'dd-mm-yy' => date_i18n('d-m-Y', strtotime($current_date)),
								'dd/mm/yy' => date_i18n('d/m/Y', strtotime($current_date)),
								'mm-dd-yy' => date_i18n('m-d-Y', strtotime($current_date)),
								'mm/dd/yy' => date_i18n('m/d/Y', strtotime($current_date)),
								'd M , yy' => date_i18n('j M , Y', strtotime($current_date)),
								'D d M , yy' => date_i18n('D j M , Y', strtotime($current_date)),
								'M d , yy' => date_i18n('M  j, Y', strtotime($current_date)),
								'D M d , yy' => date_i18n('D M  j, Y', strtotime($current_date)),
							)
						),
						array(
							'name' => 'enable_transport_search',
							'label' => __('Enable Transport search by name ?', 'abprf-rental-forge'),
							'desc' => __('If you do not want to enable transport search by name, please switch ', 'abprf-rental-forge') . ' ' . ABPRF_LIB_Layout::off() . ' ' . __('or to make it show, select', 'abprf-rental-forge') . ' ' . ABPRF_LIB_Layout::on() . ' ' . __('. Default is', 'abprf-rental-forge') . ' ' . ABPRF_LIB_Layout::off(),
							'type' => 'button_switch',
							'default' => 'off'
						),
						array(
							'name' => 'enable_return',
							'label' => __('Enable Return  Search?', 'abprf-rental-forge'),
							'desc' => __('If you want to avoid returning search results, make sure to turn the search function ', 'abprf-rental-forge') . ' ' . ABPRF_LIB_Layout::off() . ' ' . __(' Alternatively, to make it visible, choose the option ', 'abprf-rental-forge') . ' ' . ABPRF_LIB_Layout::on() . ' ' . __('. The default setting is ', 'abprf-rental-forge') . ' ' . ABPRF_LIB_Layout::on(),
							'type' => 'button_switch',
							'default' => 'on'
						),
						array(
							'name' => 'checkout_system',
							'label' => __('Checkout System', 'abprf-rental-forge'),
							'desc' => __('If you want to Only Added in cart by ajax on a single page, please select Only Add to cart by ajax. If you want to directly checkout with just one click, please select direct checkout . If you want to directly cart with just one click, please select direct cart. Default WooCommerce checkout system.', 'abprf-rental-forge'),
							'type' => 'radio',
							'default' => 'default',
							'options' => array(
								'default' => __('Default', 'abprf-rental-forge'),
								'single' => __('Only Add to cart by ajax', 'abprf-rental-forge'),
								'cart' => __('Direct Cart', 'abprf-rental-forge'),
								'checkout' => __('Direct Checkout', 'abprf-rental-forge'),
							),
						),
						array(
							'name' => 'redirect_search',
							'label' => __('Search result Redirect to', 'abprf-rental-forge'),
							'desc' => __('If you want to redirect the search result page, please select the page below.', 'abprf-rental-forge'),
							'type' => 'pages',
							'default' => '',
						),
					)),
					'abptm_contact' => apply_filters('abptm_contact_filter', array(
						array(
							'name' => 'name',
							'label' => __('Company Name', 'abprf-rental-forge'),
							'desc' => __('Kindly enter the name of your company here.', 'abprf-rental-forge'),
							'type' => 'text',
							'default' => '',
							'placeholder' => __('Rental', 'abprf-rental-forge'),
						),
						array(
							'name' => 'address',
							'label' => __('Address', 'abprf-rental-forge'),
							'desc' => __('Add the whole address of your company, please.', 'abprf-rental-forge'),
							'type' => 'textarea',
							'placeholder' => __('EX: Greene St, New York, NY 10003, USA', 'abprf-rental-forge'),
						),
						array(
							'name' => 'phone',
							'label' => __('Contact Number', 'abprf-rental-forge'),
							'desc' => __('Add your company`s phone number here, please.', 'abprf-rental-forge'),
							'type' => 'text',
							'default' => '',
							'placeholder' => __('EX: +123456789', 'abprf-rental-forge'),
						),
						array(
							'name' => 'email',
							'label' => __('E-Mail', 'abprf-rental-forge'),
							'desc' => __('Kindly enter your business email address here.', 'abprf-rental-forge'),
							'type' => 'text',
							'default' => '',
							'placeholder' => __('your mail address', 'abprf-rental-forge'),
						),
					)),
					'abprf_slider' => array(
						array(
							'name' => 'slider_type',
							'label' => __('Slider/Thumbnail ?', 'abprf-rental-forge'),
							'desc' => __('Please turn the slider switch ', 'abprf-rental-forge') . ' ' . ABPRF_LIB_Layout::on() . ' ' . __('or', 'abprf-rental-forge') . ' ' . ABPRF_LIB_Layout::off() . ' ' . __(' if you are only showing the thumbnail. Default is', 'abprf-rental-forge') . ' ' . ABPRF_LIB_Layout::on(),
							'type' => 'button_switch',
							'default' => 'on',
						),
						array(
							'name' => 'collapse_start',
							'collapse' => 'on',
							'collapse_data' => array('option' => 'abprf_slider', 'key' => 'slider_type'),
						),
						array(
							'name' => 'slider_style',
							'label' => __('Slider Theme', 'abprf-rental-forge'),
							'desc' => __('Please choose the theme style for the slider. ', 'abprf-rental-forge'),
							'type' => 'radio',
							'default' => 'style_1',
							'options' => array(
								'style_1' => __('Default', 'abprf-rental-forge'),
								'style_2' => __('Flix', 'abprf-rental-forge'),
							),
						),
						array(
							'name' => 'indicator_visible',
							'label' => __('Visible Indicator ?', 'abprf-rental-forge'),
							'desc' => __('If you hide Indicator , please Switch ', 'abprf-rental-forge') . ' ' . ABPRF_LIB_Layout::off() . ' ' . __('or to Show Indicator Switch', 'abprf-rental-forge') . ' ' . ABPRF_LIB_Layout::on() . ' ' . __('. Default is', 'abprf-rental-forge') . ' ' . ABPRF_LIB_Layout::on(),
							'type' => 'button_switch',
							'default' => 'on',
						),
						array(
							'name' => 'collapse_start',
							'collapse' => 'on',
							'collapse_data' => array('option' => 'abprf_slider', 'key' => 'indicator_visible'),
						),
						array(
							'name' => 'indicator_type',
							'label' => __('Indicator Type', 'abprf-rental-forge'),
							'desc' => __('Please Select Slider Indicator Type Default Icon', 'abprf-rental-forge'),
							'type' => 'radio',
							'default' => 'icon',
							'options' => array(
								'icon' => __('Icon', 'abprf-rental-forge'),
								'image' => __('Image', 'abprf-rental-forge')
							)
						),
						array('name' => 'collapse_end'),
						array(
							'name' => 'showcase_visible',
							'label' => __('Visible Showcase ?', 'abprf-rental-forge'),
							'desc' => __('If you hide Showcase , please Switch ', 'abprf-rental-forge') . '<strong class="_abprf_color_theme"> ' . __('OFF', 'abprf-rental-forge') . '</strong>&nbsp;' . __('or to Show Showcase Switch', 'abprf-rental-forge') . '&nbsp;<strong class="_abprf_color_theme"> ' . __('ON', 'abprf-rental-forge') . '</strong>' . __('. Default is', 'abprf-rental-forge') . '&nbsp;<strong class="_abprf_color_theme">' . __('ON', 'abprf-rental-forge') . '</strong>',
							'type' => 'button_switch',
							'default' => 'on'
						),
						array(
							'name' => 'collapse_start',
							'collapse' => 'on',
							'collapse_data' => array('option' => 'abprf_slider', 'key' => 'showcase_visible'),
						),
						array(
							'name' => 'showcase_position',
							'label' => __('Showcase Position', 'abprf-rental-forge'),
							'desc' => __('Please Select Slider Showcase Position Default Right', 'abprf-rental-forge'),
							'type' => 'radio',
							'default' => 'right',
							'options' => array(
								'top' => __('Top', 'abprf-rental-forge'),
								'right' => __('Right', 'abprf-rental-forge'),
								'bottom' => __('Bottom', 'abprf-rental-forge'),
								'left' => __('Left', 'abprf-rental-forge')
							)
						),
						array(
							'name' => 'visible_popup',
							'label' => __('Visible Popup ?', 'abprf-rental-forge'),
							'desc' => __('If you hide popup slider , please Switch ', 'abprf-rental-forge') . '<strong class="_abprf_color_theme"> ' . __('OFF', 'abprf-rental-forge') . '</strong>&nbsp;' . __('or to Show popup slider Switch', 'abprf-rental-forge') . '&nbsp;<strong class="_abprf_color_theme"> ' . __('ON', 'abprf-rental-forge') . '</strong>' . __('. Default is', 'abprf-rental-forge') . '&nbsp;<strong class="_abprf_color_theme">' . __('ON', 'abprf-rental-forge') . '</strong>',
							'type' => 'button_switch',
							'default' => 'on'
						),
						array(
							'name' => 'collapse_start',
							'collapse' => 'on',
							'collapse_data' => array('option' => 'abprf_slider', 'key' => 'visible_popup'),
						),
						array(
							'name' => 'popup_image_indicator',
							'label' => __('Popup Image Indicator', 'abprf-rental-forge'),
							'desc' => __('If you hide Popup Image Indicator , please Switch ', 'abprf-rental-forge') . '<strong class="_abprf_color_theme"> ' . __('OFF', 'abprf-rental-forge') . '</strong>&nbsp;' . __('or to Show Popup Image Indicator Switch', 'abprf-rental-forge') . '&nbsp;<strong class="_abprf_color_theme"> ' . __('ON', 'abprf-rental-forge') . '</strong>' . __('. Default is', 'abprf-rental-forge') . '&nbsp;<strong class="_abprf_color_theme">' . __('ON', 'abprf-rental-forge') . '</strong>',
							'type' => 'button_switch',
							'default' => 'on'
						),
						array(
							'name' => 'popup_icon_indicator',
							'label' => __('Popup Icon Indicator', 'abprf-rental-forge'),
							'desc' => __('If you hide Popup Icon Indicator , please Switch ', 'abprf-rental-forge') . '<strong class="_abprf_color_theme"> ' . __('OFF', 'abprf-rental-forge') . '</strong>&nbsp;' . __('or to Show Popup Icon Indicator Switch', 'abprf-rental-forge') . '&nbsp;<strong class="_abprf_color_theme"> ' . __('ON', 'abprf-rental-forge') . '</strong>' . __('. Default is', 'abprf-rental-forge') . '&nbsp;<strong class="_abprf_color_theme">' . __('ON', 'abprf-rental-forge') . '</strong>',
							'type' => 'button_switch',
							'default' => 'on'
						),
						array('name' => 'collapse_end'),
						array('name' => 'collapse_end'),
						array('name' => 'collapse_end'),
					),
					'abprf_css_var' => apply_filters('abprf_css_var_filter', array(
						array(
							'name' => 'color_theme',
							'label' => __('Base Color', 'abprf-rental-forge'),
							'desc' => __('Choose the Standard base color.', 'abprf-rental-forge'),
							'type' => 'color',
							'default' => '#95951c'
						),
						array(
							'name' => 'color_theme_alternate',
							'label' => __('Alternate Color', 'abprf-rental-forge'),
							'desc' => __('By choosing Default Theme Alternate Color, the text color will be used if the backdrop color is Base Color or alternately.', 'abprf-rental-forge'),
							'type' => 'color',
							'default' => '#fff'
						),
						array(
							'name' => 'color_default',
							'label' => __('Default Color', 'abprf-rental-forge'),
							'desc' => __('Select Default Text  Color.', 'abprf-rental-forge'),
							'type' => 'color',
							'default' => '#303030'
						),
						array(
							'name' => 'br_default',
							'label' => __('Default Border Radios', 'abprf-rental-forge'),
							'desc' => __('Type Default Border Radios(in PX Unit).', 'abprf-rental-forge'),
							'type' => 'number',
							'default' => '0',
							'validation' => 'validation_number'
						),
						array(
							'name' => 'fs_default',
							'label' => __('Default Font Size', 'abprf-rental-forge'),
							'desc' => __('Enter the default font size (in PX units).', 'abprf-rental-forge'),
							'type' => 'number',
							'default' => '12',
							'validation' => 'validation_number'
						),
						array(
							'name' => 'fs_h1',
							'label' => __('Font Size h1 ', 'abprf-rental-forge'),
							'desc' => __('Enter the h1 font size (in PX units).', 'abprf-rental-forge'),
							'type' => 'number',
							'default' => '35',
							'validation' => 'validation_number'
						),
						array(
							'name' => 'fs_h2',
							'label' => __('Font Size h2', 'abprf-rental-forge'),
							'desc' => __('Enter the h2 font size (in PX units).', 'abprf-rental-forge'),
							'type' => 'number',
							'default' => '30',
							'validation' => 'validation_number'
						),
						array(
							'name' => 'fs_h3',
							'label' => __('Font Size h3', 'abprf-rental-forge'),
							'desc' => __('Enter the h3 font size (in PX units).', 'abprf-rental-forge'),
							'type' => 'number',
							'default' => '25',
							'validation' => 'validation_number'
						),
						array(
							'name' => 'fs_h4',
							'label' => __('Font Size h4', 'abprf-rental-forge'),
							'desc' => __('Enter the h4 font size (in PX units).', 'abprf-rental-forge'),
							'type' => 'number',
							'default' => '20',
							'validation' => 'validation_number'
						),
						array(
							'name' => 'fs_h5',
							'label' => __('Font Size h5', 'abprf-rental-forge'),
							'desc' => __('Enter the h5 font size (in PX units).', 'abprf-rental-forge'),
							'type' => 'number',
							'default' => '17',
							'validation' => 'validation_number'
						),
						array(
							'name' => 'fs_h6',
							'label' => __('Font Size h6 ', 'abprf-rental-forge'),
							'desc' => __('Enter the h6 font size (in PX units).', 'abprf-rental-forge'),
							'type' => 'number',
							'default' => '15',
							'validation' => 'validation_number'
						),
						array(
							'name' => 'fs_label',
							'label' => __('Label Font Size ', 'abprf-rental-forge'),
							'desc' => __('Enter the label font size (in PX units).', 'abprf-rental-forge'),
							'type' => 'number',
							'default' => '14',
							'validation' => 'validation_number'
						),
						array(
							'name' => 'fs_button',
							'label' => __('Button Font Size ', 'abprf-rental-forge'),
							'desc' => __('Enter the button font size (in PX units).', 'abprf-rental-forge'),
							'type' => 'number',
							'default' => '13',
							'validation' => 'validation_number'
						),
						array(
							'name' => 'color_button',
							'label' => __('Button Text Color', 'abprf-rental-forge'),
							'desc' => __('Select Button Text  Color.', 'abprf-rental-forge'),
							'type' => 'color',
							'default' => '#FFF'
						),
						array(
							'name' => 'bg_button',
							'label' => __('Button Background Color', 'abprf-rental-forge'),
							'desc' => __('Select Button Background  Color.', 'abprf-rental-forge'),
							'type' => 'color',
							'default' => '#222'
						),
						array(
							'name' => 'color_warning',
							'label' => __('Warning Color', 'abprf-rental-forge'),
							'desc' => __('Select Warning  Color.', 'abprf-rental-forge'),
							'type' => 'color',
							'default' => '#E67C30'
						),
						array(
							'name' => 'bg_section',
							'label' => __('Section Background color', 'abprf-rental-forge'),
							'desc' => __('Here you can add Section Background color', 'abprf-rental-forge'),
							'type' => 'color',
							'default' => '#FAFCFE'
						),
					))
				), $abprf_configuration
				);
			}
			public static function description($args): void {
				$desc = empty($args['desc']) ? '' : $args['desc'];
				if ($desc) { ?>
                    <div class="_divider_xs"></div>
                    <span class="info_text">
                        <i class="fas fa-info-circle"></i>
						<i><?php echo wp_kses_post($desc); ?></i>
                    </span>
					<?php
				}
			}
			public function text($args, $label, $name, $value): void {
				$placeholder = array_key_exists('placeholder', $args) && $args['placeholder'] ? $args['placeholder'] : '';
				?>
                <div class="_setting_item">
                    <label class="_f_equal_max_500_f_wrap">
                        <span class="_pad_r_xs"><?php echo esc_html($label); ?></span>
                        <input type="text" name="<?php echo esc_attr($name); ?>" class="_form_control <?php echo esc_attr(array_key_exists('validation', $args) && $args['validation'] ? $args['validation'] : ''); ?>" value="<?php echo esc_attr($value); ?>" placeholder="<?php echo esc_attr($placeholder); ?>"/>
                    </label>
					<?php self::description($args); ?>
                </div>
				<?php
			}
			public function url($args, $label, $name, $value): void {
				$this->text($args, $label, $name, $value);
			}
			public function number($args, $label, $name, $value): void {
				$placeholder = array_key_exists('placeholder', $args) ? $args['placeholder'] : '';
				?>
                <div class="_setting_item">
                    <label class="_f_equal_max_500_f_wrap">
                        <span class="_pad_r_xs"><?php echo esc_html($label); ?></span>
                        <input type="number" name="<?php echo esc_attr($name); ?>" class="_form_control  <?php echo esc_attr(array_key_exists('validation', $args) && $args['validation'] ? $args['validation'] : ''); ?>" value="<?php echo esc_attr($value); ?>" placeholder="<?php echo esc_attr($placeholder); ?>"
							<?php echo esc_attr(empty($args['min']) ? '' : 'data-min=' . $args['min']); ?>
							<?php echo esc_attr(empty($args['max']) ? '' : 'data-max=' . $args['max']); ?>
                        />
                    </label>
					<?php self::description($args); ?>
                </div>
				<?php
			}
			public function password($args, $label, $name, $value): void {
				$placeholder = empty($args['placeholder']) ? '' : $args['placeholder'];
				?>
                <div class="_setting_item">
                    <label class="_f_equal_pad_r_xs_max_500_f_wrap">
                        <span><?php echo esc_html($label); ?></span>
                        <input type="password" name="<?php echo esc_attr($name); ?>" class="_form_control <?php echo esc_attr(array_key_exists('validation', $args) && $args['validation'] ? $args['validation'] : ''); ?>" value="<?php echo esc_attr($value); ?>" placeholder="<?php echo esc_attr($placeholder); ?>"/>
                    </label>
					<?php self::description($args); ?>
                </div>
				<?php
			}
			public function file($args, $label, $name, $value): void {
				?>
                <div class="_setting_item">
                    <div class="_f_equal_pad_r_xs_max_500_f_wrap_fa_center">
                        <span class="_fs_label_mar_r_xs"><?php echo esc_html($label); ?></span>
                        <div><?php do_action('abprf_add_image', $name, $value); ?></div>
                    </div>
					<?php self::description($args); ?>
                </div>
				<?php
			}
			public function dashicons($args, $label, $name, $value): void {
				?>
                <div class="_setting_item">
                    <div class="_d_flex">
                        <div class="_fd_column_w_300">
                            <span class="_fs_label"><?php echo esc_html($label); ?></span>
							<?php self::description($args); ?>
                        </div>
						<?php do_action('abptm_add_icon', $name, $value, 1); ?>
                    </div>
                </div>
				<?php
			}
			public function fontawesome($args, $label, $name, $value): void {
				?>
                <div class="_setting_item">
                    <div class="_f_equal_max_500_f_wrap_fa_center">
                        <span class="_fs_label_pad_r_xs"><?php echo esc_html($label); ?></span>
                        <div><?php do_action('abptm_add_icon', $name, $value); ?></div>
                    </div>
					<?php self::description($args); ?>
                </div>
				<?php
			}
			public function datepicker($args, $label, $name, $value): void {
				?>
                <div class="_setting_item">
                    <div class="_f_equal_max_500_f_wrap_fa_center">
                        <span class="_fs_label_pad_r_xs"><?php echo esc_html($label); ?></span>
						<?php ABPRF_LIB_Layout::input_date($name, $value); ?>
                    </div>
					<?php self::description($args); ?>
                </div>
				<?php
			}
			public function textarea($args, $label, $name, $value): void {
				$placeholder = empty($args['placeholder']) ? '' : $args['placeholder'];
				?>
                <div class="_setting_item">
                    <div class="_f_wrap_fa_center">
                        <span class="_fs_label_pad_r_xs_max_250"><?php echo esc_html($label); ?></span>
                        <label>
                            <textarea name="<?php echo esc_attr($name); ?>" rows="5" cols="55" class="_form_control <?php echo esc_attr(array_key_exists('validation', $args) && $args['validation'] ? $args['validation'] : ''); ?>" placeholder="<?php echo esc_attr($placeholder); ?>"><?php echo esc_html($value); ?></textarea>
                        </label>
                    </div>
					<?php self::description($args); ?>
                </div>
				<?php
			}
			public function select($args, $label, $name, $value): void {
				?>
                <div class="_setting_item">
                    <label class="_f_equal_max_500_f_wrap">
                        <span class="_pad_r_xs_max_250"><?php echo esc_html($label); ?></span>
                        <select name="<?php echo esc_attr($name); ?>" class="_form_control">
							<?php foreach ($args['options'] as $key => $label) { ?>
                                <option value="<?php echo esc_attr($key); ?>" <?php echo esc_attr($key == $value ? 'selected' : ''); ?>><?php echo esc_html($label); ?></option>
							<?php } ?>
                        </select>
                    </label>
					<?php self::description($args); ?>
                </div>
				<?php
			}
			public function radio($args, $label, $name, $value): void {
				?>
                <div class="_setting_item ">
                    <div class="_fa_center">
                        <span class="_fs_label_pad_r_xs_max_250"><?php echo esc_html($label); ?></span>
                        <div class="abprf_radio _input_item _f_wrap">
                            <input type="hidden" class="_form_control" name="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr($value); ?>"/>
							<?php foreach ($args['options'] as $key => $option) { ?>
                                <div class="radio_item">
                                    <button type="button" class="_btn_white_xs <?php echo esc_attr($key == $value ? 'rf_active' : ''); ?>" data-radio="<?php echo esc_attr($key); ?>" data-open-icon="far fa-check-circle" data-close-icon="far fa-circle">
                                        <span data-icon class="_mar_r_xs <?php echo esc_attr($key == $value ? 'far fa-check-circle' : 'far fa-circle'); ?>"></span><?php echo esc_html($option); ?>
                                    </button>
                                </div>
							<?php } ?>
                        </div>
                    </div>
					<?php self::description($args); ?>
                </div>
				<?php
			}
			public function checkbox($args, $label, $name, $value): void {
				$checked = checked($value, 'on', false);
				?>
                <div class="_setting_item">
                    <div class="_f_equal_max_500_f_wrap_fa_center">
                        <span class="_fs_label_pad_r_xs"><?php echo esc_html($label); ?></span>
                        <label>
                            <input type="hidden" name="<?php echo esc_attr($name); ?>" value="off"/>
                            <input type="checkbox" class="checkbox" name="<?php echo esc_attr($name); ?>" value="on" <?php echo esc_attr($checked); ?> />
							<?php echo esc_html($args['desc']); ?>
                        </label>
                    </div>
					<?php self::description($args); ?>
                </div>
				<?php
			}
			public function button_switch($args, $label, $name, $value): void {
				?>
                <div class="_setting_item">
                    <div class="_f_equal_max_500_f_wrap_fa_center">
                        <span class="_fs_label_pad_r_xs"><?php echo esc_html($label); ?></span>
                        <label><?php ABPRF_LIB_Layout::switch_checkbox($name, $value); ?></label>
                    </div>
					<?php self::description($args); ?>
                </div>
				<?php
			}
			public function multi_check($args, $label, $name, $value): void {
				$value_array = $value ? explode(',', $value) : [];
				?>
                <div class="_setting_item ">
                    <div class="_d_flex">
                        <span class="_fs_label_pad_r_xs_max_250"><?php echo esc_html($label); ?></span>
                        <div class="abprf_checkbox">
                            <input type="hidden" name="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr($value); ?>"/>
							<?php foreach ($args['options'] as $key => $label) { ?>
                                <div class="checkbox_item">
                                    <button type="button" class="_btn_white_xs <?php echo esc_attr(in_array($key, $value_array) ? 'rf_active' : ''); ?>" data-checked="<?php echo esc_attr($key); ?>" data-open-icon="far fa-check-square" data-close-icon="far fa-square">
                                        <span data-icon class="_mar_r_xs <?php echo esc_attr(in_array($key, $value_array) ? 'far fa-check-square' : 'far fa-square'); ?>"></span><?php echo esc_html($label); ?>
                                    </button>
                                </div>
							<?php } ?>
                        </div>
                    </div>
					<?php self::description($args); ?>
                </div>
				<?php
			}
			public function color($args, $label, $name, $value): void {
				?>
                <div class="_setting_item">
                    <div class="_f_equal_max_500_f_wrap_fa_center">
                        <span class="_fs_labels_pad_r_xs"><?php echo esc_html($label); ?></span>
                        <label><input type="text" name="<?php echo esc_attr($name); ?>" class="_form_control abprf_color_picker" value="<?php echo esc_attr($value); ?>" data-default-color="<?php echo esc_attr($args['std']); ?>"/></label>
                    </div>
					<?php self::description($args); ?>
                </div>
				<?php
			}
			public function wp_role($args, $label, $name, $value): void {
				global $wp_roles;
				$value_array = $value ? explode(',', $value) : [];
				?>
                <div class="_setting_item ">
                    <div class="_d_flex">
                        <span class="_fs_label_pad_r_xs_max_250"><?php echo esc_html($label); ?></span>
                        <div class="abprf_checkbox">
                            <input type="hidden" name="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr($value); ?>"/>
							<?php foreach ($wp_roles->roles as $key => $label) { ?>
                                <div class="checkbox_item">
                                    <button type="button" class="_btn_white_xs <?php echo esc_attr(in_array($key, $value_array) ? 'rf_active' : ''); ?>" data-checked="<?php echo esc_attr($key); ?>" data-open-icon="far fa-check-square" data-close-icon="far fa-square">
                                        <span data-icon class="_mar_r_xs <?php echo esc_attr(in_array($key, $value_array) ? 'far fa-check-square' : 'far fa-square'); ?>"></span><?php echo esc_html($label['name']); ?>
                                    </button>
                                </div>
							<?php } ?>
                        </div>
                    </div>
                    <div class="_divider_xs"></div>
					<?php self::description($args); ?>
                </div>
				<?php
			}
			public function wp_editor($args, $label, $name, $value): void {
				$value = html_entity_decode($value);
				?>
                <div class="_setting_item">
                    <div class="_fd_column">
                        <span class="_fs_label_pad_r_xs_max_250"><?php echo esc_html($label); ?></span>
						<?php self::description($args); ?>
						<?php
							$editor_settings = array(
								'teeny' => true,
								'textarea_name' => $name,
								'textarea_rows' => 15
							);
							if (isset($args['options']) && is_array($args['options'])) {
								$editor_settings = array_merge($editor_settings, $args['options']);
							}
							wp_editor($value, $args['section'] . '-' . $args['id'], $editor_settings);
						?>
                    </div>
                </div>
				<?php
			}
			public function pages($args, $label, $name, $value): void {
				?>
                <div class="_setting_item">
                    <label class="_f_equal_max_500_f_wrap">
                        <span class="_pad_r_xs_max_250"><?php echo esc_html($label); ?></span>
						<?php
							$dropdown = wp_dropdown_pages(array(
								'selected' => esc_attr($value),
								'name' => esc_attr($name),
								'id' => esc_attr($name),
								'class' => '_form_control',
								'show_option_none' => esc_html__('Please Select', 'abprf-rental-forge'),
								'echo' => 0
							));
							echo wp_kses(
								$dropdown,
								array(
									'select' => array('name' => true, 'id' => true, 'class' => true, 'required' => true,),
									'option' => array('value' => true, 'selected' => true,),
								)
							);
						?>
                    </label>
					<?php self::description($args); ?>
                </div>
				<?php
			}
			public function sanitize_options($options) {
				if (!$options) {
					return $options;
				}
				$abprf_configuration = ABPRF_LIB_Function::get_option('abprf_configuration');
				foreach ($options as $option_slug => $option_value) {
					$sanitize_callback = $this->get_sanitize_callback($abprf_configuration, $option_slug);
					if ($sanitize_callback) {
						$options[$option_slug] = call_user_func($sanitize_callback, $option_value);
					}
				}
				return $options;
			}
			public function get_sanitize_callback($abprf_configuration, $slug = ''): callable|bool {
				if (empty($slug)) {
					return false;
				}
				foreach ($this->configuration_data($abprf_configuration) as $options) {
					foreach ($options as $option) {
						if ($option['name'] != $slug) {
							continue;
						}
						return isset($option['sanitize_callback']) && is_callable($option['sanitize_callback']) ? $option['sanitize_callback'] : false;
					}
				}
				return false;
			}
		}
		new  ABPRF_Configuration();
	}