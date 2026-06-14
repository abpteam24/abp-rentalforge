<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	if ( ! class_exists( 'ABPRF_Configuration' ) ) {
		class ABPRF_Configuration {
			public function __construct() {
				add_action( 'admin_init', array( $this, 'admin_init' ) );
				add_action( 'abprf_load_configuration', array( $this, 'load_configuration' ) );
				add_action( 'update_option_abprf_configuration', array( $this, 'permalink_flush' ) );
				add_filter( 'pre_update_option_abprf_configuration', array( $this, 'update_sanitize' ), 10, 3 );
				add_filter( 'pre_update_option_abprf_contact', array( $this, 'update_sanitize' ), 10, 3 );
				add_filter( 'pre_update_option_abprf_slider', array( $this, 'update_sanitize' ), 10, 3 );
				add_filter( 'pre_update_option_abprf_css_var', array( $this, 'update_sanitize' ), 10, 3 );
				add_filter( 'pre_update_option_abprf_mail', array( $this, 'update_sanitize' ), 10, 3 );
				add_filter( 'pre_update_option_abprf_pdf', array( $this, 'update_sanitize' ), 10, 3 );
				add_filter( 'pre_update_option_abprf_pdf_list', array( $this, 'update_sanitize' ), 10, 3 );
				add_filter( 'pre_update_option_abprf_csv', array( $this, 'update_sanitize' ), 10, 3 );
			}

			public function admin_init(): void {
				foreach ( $this->configuration_section() as $section ) {
					register_setting( $section['id'], $section['id'], array( $this, 'sanitize_options' ) );
				}
			}

			public function permalink_flush(): void {
				flush_rewrite_rules();
			}

			public function update_sanitize( $new, $old, $option ) {
				$all_fields  = $this->configuration_data();
				$field_infos = array_key_exists( $option, $all_fields ) ? $all_fields[ $option ] : array();
				$remove_name = [ 'collapse_start', 'collapse_end' ];
				if ( sizeof( $field_infos ) > 0 && is_array( $new ) ) {
					foreach ( $field_infos as $field_info ) {
						$name = array_key_exists( 'name', $field_info ) ? $field_info['name'] : '';
						if ( ! in_array( $name, $remove_name ) ) {
							$type = array_key_exists( 'type', $field_info ) ? $field_info['type'] : '';
							if ( $type == 'wp_editor' ) {
								$new[ $name ] = sanitize_text_field( htmlentities( $new[ $name ] ) );
							} else {
								$new[ $name ] = sanitize_text_field( $new[ $name ] );
							}
						}
					}
				}

				return sizeof( $new ) > 0 ? $new : $old;
			}

			public function load_configuration(): void {
				?>
                <div class="abprf_area" id="abprf_configuration">
                    <div class="_abp_panel_max_1200_mar_auto">
                        <div class="abprf_tabs tab_top">
                            <div class="_panel_head">
                                <ul class="_abprf tab_lists">
									<?php foreach ( $this->configuration_section() as $tab ) { ?>
                                        <li data-tabs-target="#<?php echo esc_attr( $tab['id'] ); ?>"><span class="<?php echo esc_attr( array_key_exists( 'icon', $tab ) ? $tab['icon'] : '' ); ?>"></span><?php echo esc_html( $tab['menu'] ); ?></li>
									<?php } ?>
                                </ul>
                            </div>
                            <div class="_panel_body tab_content">
								<?php
									do_action( 'abprf_configuration_content' );
									$this->show_tab_content();
								?>
                            </div>
                        </div>
                    </div>
                </div>
				<?php
			}

			public function show_tab_content(): void {
				$plugin_label = ABPRF_Function::label();
				$all_fields   = $this->configuration_data();
				foreach ( $this->configuration_section() as $form ) {
					$section_id = $form['id'];
					$fields     = array_key_exists( $section_id, $all_fields ) ? $all_fields[ $section_id ] : array();
					if ( sizeof( $fields ) > 0 ) {
						?>
                        <div class="tab_item <?php echo esc_attr( $section_id ); ?>" data-tabs="#<?php echo esc_attr( $section_id ); ?>">
                            <h3 class="_abprf"><?php echo esc_html( $plugin_label . __( ' : ', 'abp-rentalforge' ) . $form['menu'] . ' ' . __( 'Configuration', 'abp-rentalforge' ) ); ?></h3>
                            <div class="_divider_xs"></div>
                            <form method="post" action="options.php">
                                <div class="group_setting">
									<?php
										settings_fields( $section_id );
										$options = ABPRF_Function::get_option( $section_id );
										foreach ( $fields as $option ) {
											$name  = $option['name'] ?? '';
											$type  = $option['type'] ?? '';
											$label = $option['label'] ?? '';
											if ( $name && $type && $label ) {
												$value          = isset( $options[ $name ] ) && $options[ $name ] ? $options[ $name ] : ( $option['default'] ?? '' );
												$collapse       = $option['collapse_data'] ?? [];
												$add_class      = $option['class'] ?? '';
												$section_target = '';
												if ( ! empty( $collapse ) ) {
													$section        = $collapse['option'] ?? '';
													$section_key    = $collapse['key'] ?? '';
													$option_value   = $this->get_option_value( $section, $section_key );
													$add_class      = $option_value == 'on' ? $add_class . ' ' . 'rf_active' : $add_class;
													$section_target = $section . '[' . $section_key . ']';
												}
												$collapse_radio = $option['collapse_radio'] ?? [];
												$radio_pass     = 0;
												if ( ! empty( $collapse_radio ) ) {
													$span_class         = $option['class'] ?? '';
													$radio_section      = $collapse_radio['option'] ?? '';
													$radio_key          = $collapse_radio['key'] ?? '';
													$radio_value        = $collapse_radio['value'] ?? '';
													$radio_option_value = ABPRF_Function::get_options( $radio_section, $radio_key, $value );
													$radio_id           = $radio_section . '_' . $radio_key . '_' . $radio_value;
													if ( ! empty( $radio_id ) ) {
														$radio_pass ++;
														?><div class="<?php echo esc_attr( $radio_option_value == $radio_value ? $span_class . '  ' . 'rf_active' : $span_class ); ?>" data-close="<?php echo esc_attr( '#' . $radio_id ); ?>"><?php
													}
												}
												$option['collapse_target'] = $section_target;
												$option['class']           = $add_class;
												$option['section']         = $section_id;
												$option['key_name']        = $name;
												$option['name']            = $section_id . '[' . $name . ']';
												$option['value']           = $value;
												$this->$type( $option );
												if ( ! empty( $collapse_radio ) && $radio_pass > 0 ) {
													?></div><?php
												}
											}
										}
									?>
                                </div>
                                <div class="_divider_xs"></div>
                                <button type="submit" class="_btn_theme" value="submit"><span class="_mar_r_xxs">💾</span><?php echo esc_html( __( 'Save', 'abp-rentalforge' ) . ' ' . $form['menu'] . ' ' . __( 'Configuration', 'abp-rentalforge' ) ); ?></button>
                            </form>
                        </div>
						<?php
					}
				}
			}

			public function get_option_value( $section, $section_key ) {
				$option_value = ABPRF_Function::get_options( $section, $section_key );
				if ( empty( $option_value ) ) {
					$all_fields = $this->configuration_data();
					$fields     = $all_fields[ $section ] ?? [];
					if ( sizeof( $fields ) > 0 ) {
						foreach ( $fields as $option ) {
							$name = $option['name'] ?? '';
							if ( ! empty( $name ) && $name == $section_key ) {
								$option_value = $option['default'] ?? '';
							}
						}
					}
				}

				return $option_value;
			}

			public function configuration_section(): array {
				$label         = ABPRF_Function::label();
				$brand_icon    = ABPRF_Function::icon();
				$configuration = apply_filters( 'abprf_configuration_after', array( array( 'id' => 'abprf_configuration', 'icon' => $brand_icon, 'menu' => $label ) ) );
				$contact       = apply_filters( 'abprf_contact_after', array(
					array( 'id' => 'abprf_on_off', 'icon' => 'fa-solid fa-toggle-on', 'menu' => __( 'ON/OFF', 'abp-rentalforge' ) ),
					array( 'id' => 'abprf_slider', 'icon' => 'fas fa-photo-video', 'menu' => __( 'Slider', 'abp-rentalforge' ) ),
					array( 'id' => 'abprf_contact', 'icon' => 'fas fa-id-card-alt', 'menu' => __( 'Contact Information', 'abp-rentalforge' ) ),
					array( 'id' => 'abprf_css_var', 'icon' => 'fas fa-drafting-compass', 'menu' => __( 'CSS Property', 'abp-rentalforge' ) ),
				) );

				return array_merge( $configuration, $contact );
			}

			public function configuration_data() {
				return apply_filters( 'abprf_configuration_data_filter', array(
					'abprf_configuration' => apply_filters( 'abprf_configuration_filter', array(
						array(
							'name' => 'booked_status',
							'label' => __( 'Booked Status', 'abp-rentalforge' ),
							'desc' => __( 'Select the specific order statuses that will automatically trigger inventory deduction or reserve a seat.', 'abp-rentalforge' ),
							'class' => 'span_2',
							'type' => 'multi_check',
							'default' => 'wc-processing,wc-completed',
							'options' => in_array( 'woocommerce/woocommerce.php', get_option( 'active_plugins' ) ) ? wc_get_order_statuses() : []
						),
						array(
							'name' => 'label',
							'label' => __( 'Label', 'abp-rentalforge' ),
							'desc' => __( 'Customize the display label for the plugin menu item in the admin dashboard side navigation.', 'abp-rentalforge' ),
							'type' => 'text',
							'default' => __( 'RentalForge', 'abp-rentalforge' ),
						),
						array(
							'name' => 'slug',
							'label' => __( 'Slug', 'abp-rentalforge' ),
							'desc' => sprintf(
							/* translators: %s: Permalinks settings page link layout */
								__( 'Define the primary URL slug for rentals. Important: After changing this, you must flush your permalinks by visiting %s and clicking Save Changes.', 'abp-rentalforge' ),
								'<strong class="_abprf_color_theme">' . __( 'Settings → Permalinks', 'abp-rentalforge' ) . '</strong>'
							),
							'type' => 'text',
							'default' => 'rental-forge'
						),
						array(
							'name' => 'icon',
							'label' => __( 'Dashboard Menu Icon', 'abp-rentalforge' ),
							'desc' => sprintf(
							/* translators: %s: Dashicons library link */
								__( 'Choose a custom admin menu icon. Please browse the %s, copy the desired icon class name, and paste it here.', 'abp-rentalforge' ),
								'<a class="_abprf" href="https://developer.wordpress.org/resource/dashicons/" target="_blank">' . __( 'WordPress Dashicons Library', 'abp-rentalforge' ) . '</a>'
							),
							'type' => 'text',
							'default' => 'dashicons-hammer'
						),
						array(
							'name' => 'brand_icon',
							'label' => __( 'RentalForge Icon', 'abp-rentalforge' ),
							'desc' => __( 'Select a global FontAwesome vector icon to act as the primary visual brand identity throughout the plugin panels.', 'abp-rentalforge' ),
							'type' => 'fontawesome',
							'default' => 'fas fa-hammer'
						),
						array(
							'name' => 'category_label',
							'label' => __( 'Category Label', 'abp-rentalforge' ),
							'desc' => __( 'Provide a custom singular or plural naming convention for your equipment and rental categories.', 'abp-rentalforge' ),
							'type' => 'text',
							'default' => __( 'Category', 'abp-rentalforge' )
						),
						array(
							'name' => 'cat_slug',
							'label' => __( 'Category Slug', 'abp-rentalforge' ),
							'desc' => sprintf(
							/* translators: %s: Permalinks settings page link layout */
								__( 'Define the custom URL structure for category archives. Remember to update your rewrite rules under %s after any modifications.', 'abp-rentalforge' ),
								'<strong class="_abprf_color_theme">' . __( 'Settings → Permalinks', 'abp-rentalforge' ) . '</strong>'
							),
							'type' => 'text',
							'default' => 'category'
						),
					) ),
					'abprf_on_off' => apply_filters( 'abprf_on_off_filter', array(
						array(
							'name' => 'rent_rule',
							'label' => __( 'Rent Date Time Rule', 'abp-rentalforge' ),
							'desc' => __( 'Rent Date Time Rules allow you to control rental booking availability based on specific date and time conditions. Select one or more rules to apply custom restrictions. If no rule is selected, all available rules will remain active by default.', 'abp-rentalforge' ),
							'class' => 'span_2',
							'type' => 'multi_check',
							'default' =>ABPRF_Layout::rent_rules_string(),
							'options' => ABPRF_Layout::rent_rules()
						),
					) ),
                    'abprf_contact' =>array(
						array(
							'name' => 'name',
							'label' => __( 'Company Name', 'abp-rentalforge' ),
							'desc' => __( 'Enter the official commercial or business name used for invoice branding.', 'abp-rentalforge' ),
							'type' => 'text',
							'default' => '',
							'placeholder' => __( 'e.g., Rental Forge Ltd.', 'abp-rentalforge' ),
						),
						array(
							'name' => 'address',
							'label' => __( 'Address', 'abp-rentalforge' ),
							'desc' => __( 'Provide the full corporate physical location or street address to appear on booking logs.', 'abp-rentalforge' ),
							'type' => 'textarea',
							'placeholder' => __( 'e.g., 450 Greene St, New York, NY 10003, USA', 'abp-rentalforge' ),
						),
						array(
							'name' => 'phone',
							'label' => __( 'Contact Number', 'abp-rentalforge' ),
							'desc' => __( 'Specify the primary telephone or corporate helpline number for client inquiries.', 'abp-rentalforge' ),
							'type' => 'text',
							'default' => '',
							'placeholder' => __( 'e.g., +1 (234) 567-8901', 'abp-rentalforge' ),
						),
						array(
							'name' => 'email',
							'label' => __( 'E-Mail', 'abp-rentalforge' ),
							'desc' => __( 'Input the standard commercial email address reserved for direct customer support correspondence.', 'abp-rentalforge' ),
							'type' => 'text',
							'default' => '',
							'placeholder' => __( 'support@example.com', 'abp-rentalforge' ),
						),
					),
					'abprf_slider' => array(
						array(
							'name' => 'slider_style',
							'label' => __( 'Slider Theme', 'abp-rentalforge' ),
							'desc' => __( 'Select the preferred frontend presentation layout style for display item multimedia asset sheets.', 'abp-rentalforge' ),
							'collapse' => 'yes',
							'type' => 'radio',
							'default' => 'slider',
							'options' => array(
								'slider' => __( 'Carousel Slider', 'abp-rentalforge' ),
								'gallery' => __( 'Gallery / Masonry', 'abp-rentalforge' ),
							),
						),
						array(
							'name' => 'image_column',
							'label' => __( 'Images Per Row', 'abp-rentalforge' ),
							'desc' => __( 'Specify the total number of gallery thumbnails to render in a single horizontal grid line row layout.', 'abp-rentalforge' ),
							'type' => 'number',
							'default' => '3',
							'min' => '1',
							'max' => '10',
							'validation' => 'validation_number',
							'collapse_radio' => array( 'option' => 'abprf_slider', 'key' => 'slider_style', 'value' => 'gallery' ),
						),
						array(
							'name' => 'indicator_visible',
							'label' => __( 'Image Indicator?', 'abp-rentalforge' ),
							'desc' => sprintf(
							/* translators: %1$s: HTML markup for OFF layout switch, %2$s: HTML markup for ON layout switch, %3$s: Default status indicator layout */
								__( 'Manage pagination dot indicators on the slide viewport canvas. Toggle %1$s to hide or %2$s to display. (Default: %3$s)', 'abp-rentalforge' ),
								ABPRF_Layout::off(),
								ABPRF_Layout::on(),
								ABPRF_Layout::on()
							),
							'type' => 'button_switch',
							'default' => 'on',
							'collapse_radio' => array( 'option' => 'abprf_slider', 'key' => 'slider_style', 'value' => 'slider' ),
						),
						array(
							'name' => 'indication_position',
							'label' => __( 'Indicator Position', 'abp-rentalforge' ),
							'desc' => __( 'Choose the geometric layout alignment position anchor for the slide tracking dot interface elements.', 'abp-rentalforge' ),
							'type' => 'radio',
							'default' => 'bottom',
							'options' => array(
								'top' => __( 'Top', 'abp-rentalforge' ),
								'right' => __( 'Right', 'abp-rentalforge' ),
								'bottom' => __( 'Bottom', 'abp-rentalforge' ),
								'left' => __( 'Left', 'abp-rentalforge' )
							),
							'class' => 'span_2',
							'collapse_radio' => array( 'option' => 'abprf_slider', 'key' => 'slider_style', 'value' => 'slider' ),
							'collapse_data' => array( 'option' => 'abprf_slider', 'key' => 'indicator_visible' ),
						),
						array(
							'name' => 'visible_popup',
							'label' => __( 'Visible Popup?', 'abp-rentalforge' ),
							'desc' => sprintf(
							/* translators: %1$s: HTML markup for OFF layout switch, %2$s: HTML markup for ON layout switch, %3$s: Default status Visible Popup layout */
								__( 'Decide whether clicking on layout thumbnails triggers a full-screen image lightbox popup. Toggle %1$s to disable or %2$s to enable. (Default: %3$s)', 'abp-rentalforge' ),
								ABPRF_Layout::off(),
								ABPRF_Layout::on(),
								ABPRF_Layout::on(),
							),
							'type' => 'button_switch',
							'default' => 'on'
						),
						array(
							'name' => 'popup_image_indicator',
							'label' => __( 'Popup Image Indicator', 'abp-rentalforge' ),
							'desc' => sprintf(
							/* translators: %1$s: HTML markup for OFF Popup Image Indicator switch, %2$s: HTML markup for ON layout switch, %3$s: Default status Popup Image Indicator layout */
								__( 'Control navigation pagination tracking assets inside the lightbox modal container view. Toggle %1$s to hide or %2$s to reveal. (Default: %3$s)', 'abp-rentalforge' ),
								ABPRF_Layout::off(),
								ABPRF_Layout::on(),
								ABPRF_Layout::on(),
							),
							'type' => 'button_switch',
							'default' => 'on',
							'collapse_data' => array( 'option' => 'abprf_slider', 'key' => 'visible_popup' ),
						),
					),
					'abprf_css_var' =>array(
						array(
							'name' => 'color_theme',
							'label' => __( 'Base Color', 'abp-rentalforge' ),
							'desc' => __( 'Pick the primary corporate core signature theme color for accents, links, and buttons.', 'abp-rentalforge' ),
							'type' => 'color',
							'default' => '#95951c'
						),
						array(
							'name' => 'color_theme_alternate',
							'label' => __( 'Alternate Color', 'abp-rentalforge' ),
							'desc' => __( 'Define a secondary tone reserved mainly for high-contrast foreground text typography layers sitting over a Base Color background layout block.', 'abp-rentalforge' ),
							'type' => 'color',
							'default' => '#fff'
						),
						array(
							'name' => 'color_default',
							'label' => __( 'Default Color', 'abp-rentalforge' ),
							'desc' => __( 'Establish the primary body text typography canvas hexadecimal hex color code.', 'abp-rentalforge' ),
							'type' => 'color',
							'default' => '#303030'
						),
						array(
							'name' => 'br_default',
							'label' => __( 'Default Border Radius', 'abp-rentalforge' ),
							'desc' => __( 'Specify a global UI layout component corner sharpness curve boundary rounding threshold value in raw pixel units.', 'abp-rentalforge' ),
							'type' => 'number',
							'default' => '0',
							'validation' => 'validation_number'
						),
						array(
							'name' => 'fs_default',
							'label' => __( 'Default Font Size', 'abp-rentalforge' ),
							'desc' => __( 'Set the default baseline layout font scale size applied to normal paragraph blocks across UI views.', 'abp-rentalforge' ),
							'type' => 'number',
							'default' => '12',
							'validation' => 'validation_number'
						),
						array(
							'name' => 'fs_label',
							'label' => __( 'Label Font Size', 'abp-rentalforge' ),
							'desc' => __( 'Configure the responsive text scale sizing applied strictly to forms, field labels, and identifier descriptions.', 'abp-rentalforge' ),
							'type' => 'number',
							'default' => '14',
							'validation' => 'validation_number'
						),
						array(
							'name' => 'fs_h1',
							'label' => __( 'Font Size H1', 'abp-rentalforge' ),
							'desc' => __( 'Enter the typographical scale size target for the primary page-level H1 header assets.', 'abp-rentalforge' ),
							'type' => 'number',
							'default' => '35',
							'validation' => 'validation_number'
						),
						array(
							'name' => 'fs_h2',
							'label' => __( 'Font Size H2', 'abp-rentalforge' ),
							'desc' => __( 'Enter the typographical scale size target for subsection level H2 header assets.', 'abp-rentalforge' ),
							'type' => 'number',
							'default' => '30',
							'validation' => 'validation_number'
						),
						array(
							'name' => 'fs_h3',
							'label' => __( 'Font Size H3', 'abp-rentalforge' ),
							'desc' => __( 'Enter the typographical scale size target for inner panel modular level H3 header assets.', 'abp-rentalforge' ),
							'type' => 'number',
							'default' => '25',
							'validation' => 'validation_number'
						),
						array(
							'name' => 'fs_h4',
							'label' => __( 'Font Size H4', 'abp-rentalforge' ),
							'desc' => __( 'Enter the typographical scale size target for contextual metadata tracking level H4 header assets.', 'abp-rentalforge' ),
							'type' => 'number',
							'default' => '20',
							'validation' => 'validation_number'
						),
						array(
							'name' => 'fs_h5',
							'label' => __( 'Font Size H5', 'abp-rentalforge' ),
							'desc' => __( 'Enter the typographical scale size target for inner card grid elements level H5 header assets.', 'abp-rentalforge' ),
							'type' => 'number',
							'default' => '17',
							'validation' => 'validation_number'
						),
						array(
							'name' => 'fs_h6',
							'label' => __( 'Font Size H6', 'abp-rentalforge' ),
							'desc' => __( 'Enter the lowest micro-metadata element typographical scale sizing target for nested H6 headers.', 'abp-rentalforge' ),
							'type' => 'number',
							'default' => '15',
							'validation' => 'validation_number'
						),
						array(
							'name' => 'fs_button',
							'label' => __( 'Button Font Size', 'abp-rentalforge' ),
							'desc' => __( 'Specify the responsive scaling text size applied directly to interactive call-to-action control items.', 'abp-rentalforge' ),
							'type' => 'number',
							'default' => '13',
							'validation' => 'validation_number'
						),
						array(
							'name' => 'color_button',
							'label' => __( 'Button Text Color', 'abp-rentalforge' ),
							'desc' => __( 'Pick a default foreground color shade for interactive typography labels inside button boundaries.', 'abp-rentalforge' ),
							'type' => 'color',
							'default' => '#FFF'
						),
						array(
							'name' => 'bg_button',
							'label' => __( 'Button Background Color', 'abp-rentalforge' ),
							'desc' => __( 'Set the default fill backdrop hexadecimal color value for transactional action trigger containers.', 'abp-rentalforge' ),
							'type' => 'color',
							'default' => '#222'
						),
						array(
							'name' => 'color_warning',
							'label' => __( 'Warning Color', 'abp-rentalforge' ),
							'desc' => __( 'Define the system default color block layout rule reserved for error outputs, alerts, or pending notices.', 'abp-rentalforge' ),
							'type' => 'color',
							'default' => '#E67C30'
						),
						array(
							'name' => 'bg_section',
							'label' => __( 'Section Background Color', 'abp-rentalforge' ),
							'desc' => __( 'Determine the fallback structural base background color applied to global layout wrapper segment panels.', 'abp-rentalforge' ),
							'type' => 'color',
							'default' => '#FAFCFE'
						),
					)
				) );
			}

			public static function description( $option ): void {
				$desc = $option['desc'] ?? '';
				if ( $desc ) { ?>
                    <div class="_divider_xs"></div>
					<?php
					ABPRF_Layout::info_text( '', $desc );
				}
			}

			public function text( $option ): void {
				$section_target = $option['collapse_target'] ?? [];
				?>
                <div class="setting_item <?php echo esc_attr( $option['class'] ?? '' ); ?>" <?php if ( ! empty( $section_target ) ) { ?> data-collapse="#<?php echo esc_attr( $section_target ); ?>"  <?php } ?>>
                    <label class="_f_wrap_fj_between_fa_center">
                        <span class="_mar_r_xs"><?php echo esc_html( $option['label'] ?? '' ); ?></span>
                        <input type="text" name="<?php echo esc_attr( $option['name'] ?? '' ); ?>" class="_form_control <?php echo esc_attr( $option['validation'] ?? '' ); ?>" value="<?php echo esc_attr( $option['value'] ?? '' ); ?>" placeholder="<?php echo esc_attr( $option['placeholder'] ?? '' ); ?>"/>
                    </label>
					<?php self::description( $option ); ?>
                </div>
				<?php
			}

			public function url( $option ): void {
				$this->text( $option );
			}

			public function number( $option ): void {
				$section_target = $option['collapse_target'] ?? [];
				?>
                <div class="setting_item <?php echo esc_attr( $option['class'] ?? '' ); ?>" <?php if ( ! empty( $section_target ) ) { ?> data-collapse="#<?php echo esc_attr( $section_target ); ?>"  <?php } ?>>
                    <label class="_f_wrap_fj_between_fa_center">
                        <span class="_mar_r_xs"><?php echo esc_html( $option['label'] ?? '' ); ?></span>
                        <input type="number" name="<?php echo esc_attr( $option['name'] ?? '' ); ?>" class="_form_control  <?php echo esc_attr( $option['validation'] ?? '' ); ?>" value="<?php echo esc_attr( $option['value'] ?? '' ); ?>" placeholder="<?php echo esc_attr( $option['placeholder'] ?? '' ); ?>"
							<?php echo esc_attr( empty( $option['min'] ) ? '' : 'data-min=' . $option['min'] ); ?>
							<?php echo esc_attr( empty( $option['max'] ) ? '' : 'data-max=' . $option['max'] ); ?>
                        />
                    </label>
					<?php self::description( $option ); ?>
                </div>
				<?php
			}

			public function password( $option ): void {
				$section_target = $option['collapse_target'] ?? [];
				?>
                <div class="setting_item <?php echo esc_attr( $option['class'] ?? '' ); ?>" <?php if ( ! empty( $section_target ) ) { ?> data-collapse="#<?php echo esc_attr( $section_target ); ?>"  <?php } ?>>
                    <label class="_f_wrap_fj_between_fa_center">
                        <span class="_mar_r_xs"><?php echo esc_html( $option['label'] ?? '' ); ?></span>
                        <input type="password" name="<?php echo esc_attr( $option['name'] ?? '' ); ?>" class="_form_control <?php echo esc_attr( $option['validation'] ?? '' ); ?>" value="<?php echo esc_attr( $option['value'] ?? '' ); ?>" placeholder="<?php echo esc_attr( $option['placeholder'] ?? '' ); ?>"/>
                    </label>
					<?php self::description( $option ); ?>
                </div>
				<?php
			}

			public function file( $option ): void {
				$value          = $option['value'] ?? '';
				$name           = $option['name'] ?? '';
				$section_target = $option['collapse_target'] ?? [];
				?>
                <div class="setting_item <?php echo esc_attr( $option['class'] ?? '' ); ?>" <?php if ( ! empty( $section_target ) ) { ?> data-collapse="#<?php echo esc_attr( $section_target ); ?>"  <?php } ?>>
                    <div class="_f_wrap_fj_between_fa_center">
                        <span class="_fs_label_mar_r_xs"><?php echo esc_html( $option['label'] ?? '' ); ?></span>
                        <div><?php do_action( 'abprf_add_image', $name, $value ); ?></div>
                    </div>
					<?php self::description( $option ); ?>
                </div>
				<?php
			}

			public function dashicons( $option ): void {
				$value          = $option['value'] ?? '';
				$name           = $option['name'] ?? '';
				$section_target = $option['collapse_target'] ?? [];
				?>
                <div class="setting_item <?php echo esc_attr( $option['class'] ?? '' ); ?>" <?php if ( ! empty( $section_target ) ) { ?> data-collapse="#<?php echo esc_attr( $section_target ); ?>"  <?php } ?>>
                    <div class="_d_flex">
                        <div class="_f_wrap_fj_between_fa_center">
                            <span class="_fs_label"><?php echo esc_html( $option['label'] ?? '' ); ?></span>
							<?php self::description( $option ); ?>
                        </div>
						<?php do_action( 'abprf_add_icon', $name, $value, 1 ); ?>
                    </div>
                </div>
				<?php
			}

			public function fontawesome( $option ): void {
				$value          = $option['value'] ?? '';
				$name           = $option['name'] ?? '';
				$section_target = $option['collapse_target'] ?? [];
				?>
                <div class="setting_item <?php echo esc_attr( $option['class'] ?? '' ); ?>" <?php if ( ! empty( $section_target ) ) { ?> data-collapse="#<?php echo esc_attr( $section_target ); ?>"  <?php } ?>>
                    <div class="_f_wrap_fj_between_fa_center">
                        <span class="_fs_label_pad_r_xs"><?php echo esc_html( $option['label'] ?? '' ); ?></span>
                        <div><?php do_action( 'abprf_add_icon', $name, $value ); ?></div>
                    </div>
					<?php self::description( $option ); ?>
                </div>
				<?php
			}

			public function datepicker( $option ): void {
				$value          = $option['value'] ?? '';
				$name           = $option['name'] ?? '';
				$section_target = $option['collapse_target'] ?? [];
				?>
                <div class="setting_item <?php echo esc_attr( $option['class'] ?? '' ); ?>" <?php if ( ! empty( $section_target ) ) { ?> data-collapse="#<?php echo esc_attr( $section_target ); ?>"  <?php } ?>>
                    <div class="_f_wrap_fj_between_fa_center">
                        <span class="_fs_label_pad_r_xs"><?php echo esc_html( $option['label'] ?? '' ); ?></span>
						<?php ABPRF_Layout::input_date( $name, $value ); ?>
                    </div>
					<?php self::description( $option ); ?>
                </div>
				<?php
			}

			public function textarea( $option ): void {
				$section_target = $option['collapse_target'] ?? [];
				?>
                <div class="setting_item <?php echo esc_attr( $option['class'] ?? '' ); ?>" <?php if ( ! empty( $section_target ) ) { ?> data-collapse="#<?php echo esc_attr( $section_target ); ?>"  <?php } ?>>
                    <label class="_f_wrap_fj_between_fa_center">
                        <span class="_mar_r_xs"><?php echo esc_html( $option['label'] ?? '' ); ?></span>
                        <textarea name="<?php echo esc_attr( $option['name'] ?? '' ); ?>" rows="5" cols="55" class="_form_control <?php echo esc_attr( $option['validation'] ?? '' ); ?>" placeholder="<?php echo esc_attr( $option['placeholder'] ?? '' ); ?>"><?php echo esc_html( $option['value'] ?? '' ); ?></textarea>
                    </label>
					<?php self::description( $option ); ?>
                </div>
				<?php
			}

			public function select( $option ): void {
				$value          = $option['value'] ?? '';
				$option_data    = $option['options'] ?? [];
				$section_target = $option['collapse_target'] ?? [];
				?>
                <div class="setting_item <?php echo esc_attr( $option['class'] ?? '' ); ?>" <?php if ( ! empty( $section_target ) ) { ?> data-collapse="#<?php echo esc_attr( $section_target ); ?>"  <?php } ?>>
                    <label class="_f_wrap_fj_between_fa_center">
                        <span class="_mar_r_xs"><?php echo esc_html( $option['label'] ?? '' ); ?></span>
                        <select name="<?php echo esc_attr( $option['name'] ?? '' ); ?>" class="_form_control">
							<?php foreach ( $option_data as $key => $label ) { ?>
                                <option value="<?php echo esc_attr( $key ); ?>" <?php echo esc_attr( $key == $value ? 'selected' : '' ); ?>><?php echo esc_html( $label ); ?></option>
							<?php } ?>
                        </select>
                    </label>
					<?php self::description( $option ); ?>
                </div>
				<?php
			}

			public function radio( $option ): void {
				$value          = $option['value'] ?? '';
				$name           = $option['name'] ?? '';
				$option_data    = $option['options'] ?? [];
				$collapse       = $option['collapse'] ?? '';
				$key_name       = $option['key_name'] ?? '';
				$section        = $option['section'] ?? '';
				$section_target = $option['collapse_target'] ?? [];
				?>
                <div class="setting_item <?php echo esc_attr( $option['class'] ?? '' ); ?>" <?php if ( ! empty( $section_target ) ) { ?> data-collapse="#<?php echo esc_attr( $section_target ); ?>"  <?php } ?>>
                    <div class="_f_wrap_fj_between_fa_center">
                        <span class="_fs_label_mar_r_xs"><?php echo esc_html( $option['label'] ?? '' ); ?></span>
                        <div class="custom_radio">
                            <input type="hidden" class="_form_control" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>"/>
							<?php foreach ( $option_data as $key => $data ) { ?>
                                <div class="radio_item">
                                    <button type="button" class="_btn_light_info_xs <?php echo esc_attr( $key == $value ? 'rf_active' : '' ); ?>"
										<?php if ( ! empty( $collapse ) ) { ?> data-close-target="#<?php echo esc_attr( $section . '_' . $key_name . '_' . $key ); ?>" <?php } ?>
                                            data-radio="<?php echo esc_attr( $key ); ?>"
                                            data-open-icon="far fa-check-circle" data-close-icon="far fa-circle">
                                        <span data-icon class="_mar_r_xs <?php echo esc_attr( $key == $value ? 'far fa-check-circle' : 'far fa-circle' ); ?>"></span><?php echo esc_html( $data ); ?>
                                    </button>
                                </div>
							<?php } ?>
                        </div>
                    </div>
					<?php self::description( $option ); ?>
                </div>
				<?php
			}

			public function checkbox( $option ): void {
				$value          = $option['value'] ?? '';
				$checked        = checked( $value, 'on', false );
				$section_target = $option['collapse_target'] ?? [];
				?>
                <div class="setting_item <?php echo esc_attr( $option['class'] ?? '' ); ?>" <?php if ( ! empty( $section_target ) ) { ?> data-collapse="#<?php echo esc_attr( $section_target ); ?>"  <?php } ?>>
                    <div class="_f_wrap_fj_between_fa_center">
                        <span class="_fs_label_mar_r_xs"><?php echo esc_html( $option['label'] ?? '' ); ?></span>
                        <label>
                            <input type="hidden" name="<?php echo esc_attr( $option['name'] ?? '' ); ?>" value="off"/>
                            <input type="checkbox" class="checkbox" name="<?php echo esc_attr( $option['name'] ?? '' ); ?>" value="on" <?php echo esc_attr( $checked ); ?> />
                        </label>
                    </div>
					<?php self::description( $option ); ?>
                </div>
				<?php
			}

			public function button_switch( $option ): void {
				$value          = $option['value'] ?? '';
				$name           = $option['name'] ?? '';
				$section_target = $option['collapse_target'] ?? [];
				?>
                <div class="setting_item <?php echo esc_attr( $option['class'] ?? '' ); ?>" <?php if ( ! empty( $section_target ) ) { ?> data-collapse="#<?php echo esc_attr( $section_target ); ?>"  <?php } ?>>
                    <label><?php ABPRF_Layout::switch_checkbox( $name, $value ); ?><span class="_mar_l_xs"><?php echo esc_html( $option['label'] ?? '' ); ?></span></label>
					<?php self::description( $option ); ?>
                </div>
				<?php
			}

			public function multi_check( $option ): void {
				$value          = $option['value'] ?? '';
				$option_data    = $option['options'] ?? [];
				$value_array    = $value ? explode( ',', $value ) : [];
				$section_target = $option['collapse_target'] ?? [];
				?>
                <div class="setting_item <?php echo esc_attr( $option['class'] ?? '' ); ?>" <?php if ( ! empty( $section_target ) ) { ?> data-collapse="#<?php echo esc_attr( $section_target ); ?>"  <?php } ?>>
                    <div class="_f_wrap_fj_between_fa_center">
                        <span class="_fs_label_mar_r_xs"><?php echo esc_html( $option['label'] ?? '' ); ?></span>
                        <div class="custom_checkbox">
                            <input type="hidden" name="<?php echo esc_attr( $option['name'] ?? '' ); ?>" value="<?php echo esc_attr( $value ); ?>"/>
							<?php foreach ( $option_data as $key => $label ) { ?>
                                <div class="checkbox_item">
                                    <button type="button" class="_btn_light_info_xs <?php echo esc_attr( in_array( $key, $value_array ) ? 'rf_active' : '' ); ?>" data-checked="<?php echo esc_attr( $key ); ?>" data-open-icon="far fa-check-square" data-close-icon="far fa-square">
                                        <span data-icon class="_mar_r_xs <?php echo esc_attr( in_array( $key, $value_array ) ? 'far fa-check-square' : 'far fa-square' ); ?>"></span><?php echo esc_html( $label ); ?>
                                    </button>
                                </div>
							<?php } ?>
                        </div>
                    </div>
					<?php self::description( $option ); ?>
                </div>
				<?php
			}

			public function color( $option ): void {
				$section_target = $option['collapse_target'] ?? [];
				?>
                <div class="setting_item <?php echo esc_attr( $option['class'] ?? '' ); ?>" <?php if ( ! empty( $section_target ) ) { ?> data-collapse="#<?php echo esc_attr( $section_target ); ?>"  <?php } ?>>
                    <div class="_f_wrap_fj_between_fa_center">
                        <span class="_fs_label_mar_r_xs"><?php echo esc_html( $option['label'] ?? '' ); ?></span>
                        <label>
                            <input type="text" name="<?php echo esc_attr( $option['name'] ?? '' ); ?>" disabled class="_form_control abprf_color_picker" value="<?php echo esc_attr( $option['value'] ?? '' ); ?>" data-default-color="<?php echo esc_html( $option['default'] ?? '' ); ?>"/>
                        </label>
                    </div>
					<?php self::description( $option ); ?>
                </div>
				<?php
			}

			public function wp_role( $option ): void {
				global $wp_roles;
				$value          = $option['value'] ?? '';
				$value_array    = $value ? explode( ',', $value ) : [];
				$section_target = $option['collapse_target'] ?? [];
				?>
                <div class="setting_item <?php echo esc_attr( $option['class'] ?? '' ); ?>" <?php if ( ! empty( $section_target ) ) { ?> data-collapse="#<?php echo esc_attr( $section_target ); ?>"  <?php } ?>>
                    <div class="_f_wrap_fj_between_fa_center">
                        <span class="_fs_label_mar_r_xs"><?php echo esc_html( $option['label'] ?? '' ); ?></span>
                        <div class="custom_checkbox">
                            <input type="hidden" name="<?php echo esc_attr( $option['name'] ?? '' ); ?>" value="<?php echo esc_attr( $option['value'] ?? '' ); ?>"/>
							<?php foreach ( $wp_roles->roles as $key => $label ) { ?>
                                <div class="checkbox_item">
                                    <button type="button" class="_btn_light_info_xs <?php echo esc_attr( in_array( $key, $value_array ) ? 'rf_active' : '' ); ?>" data-checked="<?php echo esc_attr( $key ); ?>" data-open-icon="far fa-check-square" data-close-icon="far fa-square">
                                        <span data-icon class="_mar_r_xs <?php echo esc_attr( in_array( $key, $value_array ) ? 'far fa-check-square' : 'far fa-square' ); ?>"></span><?php echo esc_html( $label['name'] ); ?>
                                    </button>
                                </div>
							<?php } ?>
                        </div>
                    </div>
                    <div class="_divider_xs"></div>
					<?php self::description( $option ); ?>
                </div>
				<?php
			}

			public function wp_editor( $option ): void {
				$name           = $option['name'] ?? '';
				$value          = $option['value'] ?? '';
				$key_name       = $option['key_name'] ?? '';
				$section        = $option['section'] ?? '';
				$option_data    = $option['options'] ?? [];
				$value          = html_entity_decode( $value );
				$section_target = $option['collapse_target'] ?? [];
				?>
                <div class="setting_item <?php echo esc_attr( $option['class'] ?? '' ); ?>" <?php if ( ! empty( $section_target ) ) { ?> data-collapse="#<?php echo esc_attr( $section_target ); ?>"  <?php } ?>>
                    <div class="_fd_column">
                        <span class="_fs_label_pad_r_xs_max_250"><?php echo esc_html( $option['label'] ?? '' ); ?></span>
						<?php self::description( $option ); ?>
						<?php
							$editor_settings = array(
								'teeny' => true,
								'textarea_name' => $name,
								'textarea_rows' => 15
							);
							if ( ! empty( $option_data ) ) {
								$editor_settings = array_merge( $editor_settings, $option_data );
							}
							wp_editor( $value, $section . '-' . $key_name, $editor_settings );
						?>
                    </div>
                </div>
				<?php
			}

			public function pages( $option ): void {
				$name           = $option['name'] ?? '';
				$value          = $option['value'] ?? '';
				$section_target = $option['collapse_target'] ?? [];
				?>
                <div class="setting_item <?php echo esc_attr( $option['class'] ?? '' ); ?>" <?php if ( ! empty( $section_target ) ) { ?> data-collapse="#<?php echo esc_attr( $section_target ); ?>"  <?php } ?>>
                    <label class="_f_wrap_fj_between_fa_center">
                        <span class="_mar_r_xs"><?php echo esc_html( $option['label'] ?? '' ); ?></span>
						<?php
							$dropdown = wp_dropdown_pages( array(
								'selected' => esc_attr( $value ),
								'name' => esc_attr( $name ),
								'id' => esc_attr( $name ),
								'class' => '_form_control',
								'show_option_none' => esc_html__( 'Please Select', 'abp-rentalforge' ),
								'echo' => 0
							) );
							echo wp_kses(
								$dropdown,
								array(
									'select' => array( 'name' => true, 'id' => true, 'class' => true, 'required' => true, ),
									'option' => array( 'value' => true, 'selected' => true, ),
								)
							);
						?>
                    </label>
					<?php self::description( $option ); ?>
                </div>
				<?php
			}

			public function sanitize_options( $options ) {
				if ( ! $options ) {
					return $options;
				}
				foreach ( $options as $option_slug => $option_value ) {
					$sanitize_callback = $this->get_sanitize_callback( $option_slug );
					if ( $sanitize_callback ) {
						$options[ $option_slug ] = call_user_func( $sanitize_callback, $option_value );
					}
				}

				return $options;
			}

			public function get_sanitize_callback( $slug = '' ): callable|bool {
				if ( empty( $slug ) ) {
					return false;
				}
				foreach ( $this->configuration_data() as $options ) {
					foreach ( $options as $option ) {
						if ( $option['name'] != $slug ) {
							continue;
						}

						return isset( $option['sanitize_callback'] ) && is_callable( $option['sanitize_callback'] ) ? $option['sanitize_callback'] : false;
					}
				}

				return false;
			}
		}
		new  ABPRF_Configuration();
	}