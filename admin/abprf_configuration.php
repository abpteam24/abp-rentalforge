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
				$abprf_configuration = ABPRF_Function::get_option( 'abprf_configuration' );
				foreach ( $this->configuration_section( $abprf_configuration ) as $section ) {
					register_setting( $section['id'], $section['id'], array( $this, 'sanitize_options' ) );
				}
			}

			public function permalink_flush(): void {
				flush_rewrite_rules();
			}

			public function update_sanitize( $new, $old, $option ) {
				$abprf_configuration = ABPRF_Function::get_option( 'abprf_configuration' );
				$all_fields          = $this->configuration_data( $abprf_configuration );
				$field_infos         = array_key_exists( $option, $all_fields ) ? $all_fields[ $option ] : array();
				$remove_name         = [ 'group_start', 'group_end', 'collapse_start', 'collapse_end' ];
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
				$abprf_configuration = ABPRF_Function::get_option( 'abprf_configuration' );
				?>
                <div class="abprf_area" id="abprf_configuration">
                    <div class="_abp_panel_max_1200_mar_auto">
                        <div class="abprf_tabs tab_top">
                            <div class="_panel_head">
                                <ul class="_abprf tab_lists">
									<?php foreach ( $this->configuration_section( $abprf_configuration ) as $tab ) { ?>
                                        <li data-tabs-target="#<?php echo esc_attr( $tab['id'] ); ?>"><span class="<?php echo esc_attr( array_key_exists( 'icon', $tab ) ? $tab['icon'] : '' ); ?>"></span><?php echo esc_html( $tab['menu'] ); ?></li>
									<?php } ?>
                                </ul>
                            </div>
                            <div class="_panel_body tab_content">
								<?php
									do_action( 'abprf_configuration_content', $abprf_configuration );
									$this->show_tab_content( $abprf_configuration );
								?>
                            </div>
                        </div>
                    </div>
                </div>
				<?php
			}

			public function show_tab_content( $abprf_configuration ): void {
				$plugin_label = ABPRF_Function::label();
				$all_fields   = $this->configuration_data( $abprf_configuration );
				foreach ( $this->configuration_section( $abprf_configuration ) as $form ) {
					$section_id = $form['id'];
					$fields     = array_key_exists( $section_id, $all_fields ) ? $all_fields[ $section_id ] : array();
					if ( sizeof( $fields ) > 0 ) {
						?>
                        <div class="tab_item" data-tabs="#<?php echo esc_attr( $section_id ); ?>">
                            <h3 class="_abprf"><?php echo esc_html( $plugin_label . __( ' : ', 'abprf-rental-forge' ) . $form['menu'] . ' ' . __( 'Configuration', 'abprf-rental-forge' ) ); ?></h3>
                            <div class="_divider_xs"></div>
                            <form method="post" action="options.php">
								<?php
									settings_fields( $section_id );
									$options = ABPRF_Function::get_option( $section_id );
									foreach ( $fields as $option ) {
										$name = array_key_exists( 'name', $option ) ? $option['name'] : '';
										if ( $name == 'collapse_start' ) {
											$collapse      = $option['collapse'] ?? '';
											$collapse_data = $option['collapse_data'] ?? '';
											$target_value  = ABPRF_Function::get_options( $collapse_data['option'], $collapse_data['key'], $collapse );
											$radio_id = array_key_exists( 'id', $collapse_data ) ? $collapse_data['id'] : '';
                                            if(!empty($radio_id)){
											?>
                                            <div class="<?php echo esc_attr( $target_value == $radio_id ? 'rf_active' : '' ); ?>" data-close="<?php echo esc_attr( '#' . $collapse_data['option'] . '[' . $collapse_data['key'] . ']'.$radio_id ); ?>">
										<?php }else{										?>
                                                <div class="<?php echo esc_attr( $target_value == 'on' ? 'rf_active' : '' ); ?>" data-collapse="<?php echo esc_attr( '#' . $collapse_data['option'] . '[' . $collapse_data['key'] . ']' ); ?>">
		                                            <?php
                                            }
                                            } elseif ( $name == 'collapse_end' ) { ?>
                                            </div>
										<?php } elseif ( $name == 'group_start' ) {
											?><div class="group_setting"><?php
										} elseif ( $name == 'group_end' ) {
											?></div><?php
										} else {
											$type  = array_key_exists( 'type', $option ) ? $option['type'] : '';
											$label = array_key_exists( 'label', $option ) ? $option['label'] : '';
											if ( $name && $type && $label ) {
												$args  = array(
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
												$value = isset( $options[ $name ] ) && $options[ $name ] ? $options[ $name ] : ( $option['default'] ?? '' );
												$name  = $section_id . '[' . $name . ']';
												$this->$type( $args, $label, $name, $value );
											}
										}
									}
								?>
                                <div class="_divider_xs"></div>
                                <button type="submit" class="_btn_theme" value="submit"><span class="_mar_r_xxs">💾</span><?php echo esc_html( __( 'Save', 'abprf-rental-forge' ) . ' ' . $form['menu'] . ' ' . __( 'Configuration', 'abprf-rental-forge' ) ); ?></button>
                            </form>
                        </div>
						<?php
					}
				}
			}

			public function configuration_section(): array {
				$label         = ABPRF_Function::label();
				$brand_icon    = ABPRF_Function::icon();
				$configuration = apply_filters( 'abprf_configuration_after', array( array( 'id' => 'abprf_configuration', 'icon' => $brand_icon, 'menu' => $label ) ) );
				$contact       = apply_filters( 'abprf_contact_after', array(
					array( 'id' => 'abprf_slider', 'icon' => 'fas fa-photo-video', 'menu' => __( 'Slider', 'abprf-rental-forge' ) ),
					array( 'id' => 'abprf_contact', 'icon' => 'fas fa-id-card-alt', 'menu' => __( 'Contact Information', 'abprf-rental-forge' ) ),
					array( 'id' => 'abprf_css_var', 'icon' => 'fas fa-drafting-compass', 'menu' => __( 'CSS Property', 'abprf-rental-forge' ) ),
				) );

				return array_merge( $configuration, $contact );
			}

			public function configuration_data( $abprf_configuration ) {
				return apply_filters( 'abprf_configuration_data_filter', array(
					'abprf_configuration' => apply_filters( 'abprf_configuration_filter', array(
						array(
							'name' => 'booked_status',
							'label' => __( 'Booked Status', 'abprf-rental-forge' ),
							'desc' => __( 'Please choose the order status for which the seat will be reserved/decreased.', 'abprf-rental-forge' ),
							'type' => 'multi_check',
							'default' => 'wc-processing,wc-completed',
							'options' => in_array( 'woocommerce/woocommerce.php', get_option( 'active_plugins' ) ) ? wc_get_order_statuses() : []
						),
						array( 'name' => 'group_start', ),
						array(
							'name' => 'label',
							'label' => __( 'Label', 'abprf-rental-forge' ),
							'desc' => __( 'This is where you may modify the dashboard menu label if you would like.', 'abprf-rental-forge' ),
							'type' => 'text',
							'default' => __( 'RentalForge', 'abprf-rental-forge' ),
						),
						array(
							'name' => 'slug',
							'label' => __( 'Slug', 'abprf-rental-forge' ),
							'desc' => __( 'Please input the desired slug name. Do not forget, once you modify this slug, you must refresh the permalink by going to', 'abprf-rental-forge' ) . ' ' . '<strong class="_abprf_color_theme">' . __( 'configuration-> Permalinks', 'abprf-rental-forge' ) . '</strong> ' . __( 'and clicking on the Save configuration button.', 'abprf-rental-forge' ),
							'type' => 'text',
							'default' => 'rental-forge'
						),
						array( 'name' => 'group_end', ),
						array( 'name' => 'group_start', ),
						array(
							'name' => 'icon',
							'label' => __( 'Dashboard Menu Icon', 'abprf-rental-forge' ),
							'desc' => __( 'You can modify the icon in the dashboard menu from this location. The only icons that can be used on the dashboard are Dashicons. Kindly visit the ', 'abprf-rental-forge' ) . ' ' . '<a class="_abprf" href=https://developer.wordpress.org/resource/dashicons/ target=_blank>' . __( 'Dashicons Library,', 'abprf-rental-forge' ) . '</a>' . ' ' . __( 'retrieve your icon code, and paste it in this location. ', 'abprf-rental-forge' ),
							'type' => 'text',
							'default' => 'dashicons-hammer'
						),
						array(
							'name' => 'brand_icon',
							'label' => __( 'RentalForge Icon', 'abprf-rental-forge' ),
							'desc' => __( 'If you wish to alter the RentalForge you can do so from this location. ', 'abprf-rental-forge' ),
							'type' => 'fontawesome',
							'default' => 'fas fa-hammer'
						),
						array( 'name' => 'group_end', ),
						array( 'name' => 'group_start', ),
						array(
							'name' => 'category_label',
							'label' => __( 'Category Label', 'abprf-rental-forge' ),
							'desc' => __( 'If you wish to modify the category label you can do so here. ', 'abprf-rental-forge' ),
							'type' => 'text',
							'default' => __( 'Category', 'abprf-rental-forge' )
						),
						array(
							'name' => 'cat_slug',
							'label' => __( 'Category Slug', 'abprf-rental-forge' ),
							'desc' => __( 'Please input the desired slug name for the category. Do not forget, after updating this slug, you must refresh permalinks. Simply navigate to  ', 'abprf-rental-forge' ) . '<strong class="_abprf_color_theme">' . __( 'configuration-> Permalinks', 'abprf-rental-forge' ) . '</strong> ' . __( 'and click on the Save Configuration button. ', 'abprf-rental-forge' ),
							'type' => 'text',
							'default' => 'category'
						),
						array( 'name' => 'group_end', ),
					) ),
					'abprf_contact' => apply_filters( 'abprf_contact_filter', array(
						array(
							'name' => 'name',
							'label' => __( 'Company Name', 'abprf-rental-forge' ),
							'desc' => __( 'Kindly enter the name of your company here.', 'abprf-rental-forge' ),
							'type' => 'text',
							'default' => '',
							'placeholder' => __( 'Rental', 'abprf-rental-forge' ),
						),
						array(
							'name' => 'address',
							'label' => __( 'Address', 'abprf-rental-forge' ),
							'desc' => __( 'Add the whole address of your company, please.', 'abprf-rental-forge' ),
							'type' => 'textarea',
							'placeholder' => __( 'EX: Greene St, New York, NY 10003, USA', 'abprf-rental-forge' ),
						),
						array(
							'name' => 'phone',
							'label' => __( 'Contact Number', 'abprf-rental-forge' ),
							'desc' => __( 'Add your company`s phone number here, please.', 'abprf-rental-forge' ),
							'type' => 'text',
							'default' => '',
							'placeholder' => __( 'EX: +123456789', 'abprf-rental-forge' ),
						),
						array(
							'name' => 'email',
							'label' => __( 'E-Mail', 'abprf-rental-forge' ),
							'desc' => __( 'Kindly enter your business email address here.', 'abprf-rental-forge' ),
							'type' => 'text',
							'default' => '',
							'placeholder' => __( 'your mail address', 'abprf-rental-forge' ),
						),
					) ),
					'abprf_slider' => array(
						array( 'name' => 'group_start', ),
						array(
							'name' => 'slider_style',
							'label' => __( 'Slider Theme', 'abprf-rental-forge' ),
							'desc' => __( 'Please choose the theme style for the slider. ', 'abprf-rental-forge' ),
							'type' => 'radio',
							'default' => 'slider',
							'options' => array(
								'slider' => __( 'Slider', 'abprf-rental-forge' ),
								'gallery' => __( 'Gallery/Masonry', 'abprf-rental-forge' ),
							),
						),
						array(
							'name' => 'collapse_start',
							'collapse' => 'on',
							'collapse_data' => array( 'option' => 'abprf_slider', 'key' => 'slider_style' ,'id'=>'gallery'),
						),
						array(
							'name' => 'image_column',
							'label' => __( 'Image Num in line', 'abprf-rental-forge' ),
							'desc' => __( 'Please choose the Image Number in a row/line. ', 'abprf-rental-forge' ),
							'type' => 'number',
							'default' => '3',
							'min' => '1',
							'max' => '10',
							'validation' => 'validation_number',

						),
						array( 'name' => 'collapse_end' ),
						array( 'name' => 'group_end', ),
						array(
							'name' => 'collapse_start',
							'collapse' => 'on',
							'collapse_data' => array( 'option' => 'abprf_slider', 'key' => 'slider_style' ,'id'=>'slider'),
						),
						array( 'name' => 'group_start', ),
						array(
							'name' => 'indicator_visible',
							'label' => __( 'Image Indicator ?', 'abprf-rental-forge' ),
							'desc' => __( 'If you hide Indicator , please Switch ', 'abprf-rental-forge' ) . ' ' . ABPRF_Layout::off() . ' ' . __( 'or to Show Indicator Switch', 'abprf-rental-forge' ) . ' ' . ABPRF_Layout::on() . ' ' . __( '. Default is', 'abprf-rental-forge' ) . ' ' . ABPRF_Layout::on(),
							'type' => 'button_switch',
							'default' => 'on',
						),
						array(
							'name' => 'collapse_start',
							'collapse' => 'on',
							'collapse_data' => array( 'option' => 'abprf_slider', 'key' => 'indicator_visible' ),
						),
						array(
							'name' => 'indication_position',
							'label' => __( 'Indicator  Position', 'abprf-rental-forge' ),
							'desc' => __( 'Please Select Slider Image Showcase Position Default Right', 'abprf-rental-forge' ),
							'type' => 'radio',
							'default' => 'bottom',
							'options' => array(
								'top' => __( 'Top', 'abprf-rental-forge' ),
								'right' => __( 'Right', 'abprf-rental-forge' ),
								'bottom' => __( 'Bottom', 'abprf-rental-forge' ),
								'left' => __( 'Left', 'abprf-rental-forge' )
							)
						),
						array( 'name' => 'collapse_end' ),
						array( 'name' => 'group_end', ),
						array( 'name' => 'collapse_end' ),
						array( 'name' => 'group_start', ),
						array(
							'name' => 'visible_popup',
							'label' => __( 'Visible Popup ?', 'abprf-rental-forge' ),
							'desc' => __( 'If you hide popup slider , please Switch ', 'abprf-rental-forge' ) . '<strong class="_abprf_color_theme"> ' . __( 'OFF', 'abprf-rental-forge' ) . '</strong>&nbsp;' . __( 'or to Show popup slider Switch', 'abprf-rental-forge' ) . '&nbsp;<strong class="_abprf_color_theme"> ' . __( 'ON', 'abprf-rental-forge' ) . '</strong>' . __( '. Default is', 'abprf-rental-forge' ) . '&nbsp;<strong class="_abprf_color_theme">' . __( 'ON', 'abprf-rental-forge' ) . '</strong>',
							'type' => 'button_switch',
							'default' => 'on'
						),
						array(
							'name' => 'collapse_start',
							'collapse' => 'on',
							'collapse_data' => array( 'option' => 'abprf_slider', 'key' => 'visible_popup' ),
						),
						array(
							'name' => 'popup_image_indicator',
							'label' => __( 'Popup Image Indicator', 'abprf-rental-forge' ),
							'desc' => __( 'If you hide Popup Image Indicator , please Switch ', 'abprf-rental-forge' ) . '<strong class="_abprf_color_theme"> ' . __( 'OFF', 'abprf-rental-forge' ) . '</strong>&nbsp;' . __( 'or to Show Popup Image Indicator Switch', 'abprf-rental-forge' ) . '&nbsp;<strong class="_abprf_color_theme"> ' . __( 'ON', 'abprf-rental-forge' ) . '</strong>' . __( '. Default is', 'abprf-rental-forge' ) . '&nbsp;<strong class="_abprf_color_theme">' . __( 'ON', 'abprf-rental-forge' ) . '</strong>',
							'type' => 'button_switch',
							'default' => 'on'
						),
						array( 'name' => 'collapse_end' ),
						array( 'name' => 'group_end', ),
					),
					'abprf_css_var' => apply_filters( 'abprf_css_var_filter', array(
						array( 'name' => 'group_start', ),
						array(
							'name' => 'color_theme',
							'label' => __( 'Base Color', 'abprf-rental-forge' ),
							'desc' => __( 'Choose the Standard base color.', 'abprf-rental-forge' ),
							'type' => 'color',
							'default' => '#95951c'
						),
						array(
							'name' => 'color_theme_alternate',
							'label' => __( 'Alternate Color', 'abprf-rental-forge' ),
							'desc' => __( 'By choosing Default Theme Alternate Color, the text color will be used if the backdrop color is Base Color or alternately.', 'abprf-rental-forge' ),
							'type' => 'color',
							'default' => '#fff'
						),
						array( 'name' => 'group_end', ),
						array( 'name' => 'group_start', ),
						array(
							'name' => 'color_default',
							'label' => __( 'Default Color', 'abprf-rental-forge' ),
							'desc' => __( 'Select Default Text  Color.', 'abprf-rental-forge' ),
							'type' => 'color',
							'default' => '#303030'
						),
						array(
							'name' => 'br_default',
							'label' => __( 'Default Border Radios', 'abprf-rental-forge' ),
							'desc' => __( 'Type Default Border Radios(in PX Unit).', 'abprf-rental-forge' ),
							'type' => 'number',
							'default' => '0',
							'validation' => 'validation_number'
						),
						array( 'name' => 'group_end', ),
						array( 'name' => 'group_start', ),
						array(
							'name' => 'fs_default',
							'label' => __( 'Default Font Size', 'abprf-rental-forge' ),
							'desc' => __( 'Enter the default font size (in PX units).', 'abprf-rental-forge' ),
							'type' => 'number',
							'default' => '12',
							'validation' => 'validation_number'
						),
						array(
							'name' => 'fs_label',
							'label' => __( 'Label Font Size ', 'abprf-rental-forge' ),
							'desc' => __( 'Enter the label font size (in PX units).', 'abprf-rental-forge' ),
							'type' => 'number',
							'default' => '14',
							'validation' => 'validation_number'
						),
						array( 'name' => 'group_end', ),
						array( 'name' => 'group_start', ),
						array(
							'name' => 'fs_h1',
							'label' => __( 'Font Size h1 ', 'abprf-rental-forge' ),
							'desc' => __( 'Enter the h1 font size (in PX units).', 'abprf-rental-forge' ),
							'type' => 'number',
							'default' => '35',
							'validation' => 'validation_number'
						),
						array(
							'name' => 'fs_h2',
							'label' => __( 'Font Size h2', 'abprf-rental-forge' ),
							'desc' => __( 'Enter the h2 font size (in PX units).', 'abprf-rental-forge' ),
							'type' => 'number',
							'default' => '30',
							'validation' => 'validation_number'
						),
						array( 'name' => 'group_end', ),
						array( 'name' => 'group_start', ),
						array(
							'name' => 'fs_h3',
							'label' => __( 'Font Size h3', 'abprf-rental-forge' ),
							'desc' => __( 'Enter the h3 font size (in PX units).', 'abprf-rental-forge' ),
							'type' => 'number',
							'default' => '25',
							'validation' => 'validation_number'
						),
						array(
							'name' => 'fs_h4',
							'label' => __( 'Font Size h4', 'abprf-rental-forge' ),
							'desc' => __( 'Enter the h4 font size (in PX units).', 'abprf-rental-forge' ),
							'type' => 'number',
							'default' => '20',
							'validation' => 'validation_number'
						),
						array( 'name' => 'group_end', ),
						array( 'name' => 'group_start', ),
						array(
							'name' => 'fs_h5',
							'label' => __( 'Font Size h5', 'abprf-rental-forge' ),
							'desc' => __( 'Enter the h5 font size (in PX units).', 'abprf-rental-forge' ),
							'type' => 'number',
							'default' => '17',
							'validation' => 'validation_number'
						),
						array(
							'name' => 'fs_h6',
							'label' => __( 'Font Size h6 ', 'abprf-rental-forge' ),
							'desc' => __( 'Enter the h6 font size (in PX units).', 'abprf-rental-forge' ),
							'type' => 'number',
							'default' => '15',
							'validation' => 'validation_number'
						),
						array( 'name' => 'group_end', ),
						array( 'name' => 'group_start', ),
						array(
							'name' => 'fs_button',
							'label' => __( 'Button Font Size ', 'abprf-rental-forge' ),
							'desc' => __( 'Enter the button font size (in PX units).', 'abprf-rental-forge' ),
							'type' => 'number',
							'default' => '13',
							'validation' => 'validation_number'
						),
						array( 'name' => 'group_end', ),
						array( 'name' => 'group_start', ),
						array(
							'name' => 'color_button',
							'label' => __( 'Button Text Color', 'abprf-rental-forge' ),
							'desc' => __( 'Select Button Text  Color.', 'abprf-rental-forge' ),
							'type' => 'color',
							'default' => '#FFF'
						),
						array(
							'name' => 'bg_button',
							'label' => __( 'Button Background Color', 'abprf-rental-forge' ),
							'desc' => __( 'Select Button Background  Color.', 'abprf-rental-forge' ),
							'type' => 'color',
							'default' => '#222'
						),
						array( 'name' => 'group_end', ),
						array( 'name' => 'group_start', ),
						array(
							'name' => 'color_warning',
							'label' => __( 'Warning Color', 'abprf-rental-forge' ),
							'desc' => __( 'Select Warning  Color.', 'abprf-rental-forge' ),
							'type' => 'color',
							'default' => '#E67C30'
						),
						array(
							'name' => 'bg_section',
							'label' => __( 'Section Background color', 'abprf-rental-forge' ),
							'desc' => __( 'Here you can add Section Background color', 'abprf-rental-forge' ),
							'type' => 'color',
							'default' => '#FAFCFE'
						),
						array( 'name' => 'group_end', ),
					) )
				), $abprf_configuration
				);
			}

			public static function description( $args ): void {
				$desc = empty( $args['desc'] ) ? '' : $args['desc'];
				if ( $desc ) { ?>
                    <div class="_divider_xs"></div>
                    <div class="info_text">
                        <span class="_mar_r_xxs">ℹ️</span>
                        <span><?php echo wp_kses_post( $desc ); ?></span>
                    </div>
					<?php
				}
			}

			public function text( $args, $label, $name, $value ): void {
				$placeholder = array_key_exists( 'placeholder', $args ) && $args['placeholder'] ? $args['placeholder'] : '';
				?>
                <div class="_setting_item">
                    <label class="_f_wrap_fj_between_fa_center">
                        <span class="_mar_r_xs"><?php echo esc_html( $label ); ?></span>
                        <input type="text" name="<?php echo esc_attr( $name ); ?>" class="_form_control <?php echo esc_attr( array_key_exists( 'validation', $args ) && $args['validation'] ? $args['validation'] : '' ); ?>" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php echo esc_attr( $placeholder ); ?>"/>
                    </label>
					<?php self::description( $args ); ?>
                </div>
				<?php
			}

			public function url( $args, $label, $name, $value ): void {
				$this->text( $args, $label, $name, $value );
			}

			public function number( $args, $label, $name, $value ): void {
				$placeholder = array_key_exists( 'placeholder', $args ) ? $args['placeholder'] : '';
				?>
                <div class="_setting_item">
                    <label class="_f_wrap_fj_between_fa_center">
                        <span class="_mar_r_xs"><?php echo esc_html( $label ); ?></span>
                        <input type="number" name="<?php echo esc_attr( $name ); ?>" class="_form_control  <?php echo esc_attr( array_key_exists( 'validation', $args ) && $args['validation'] ? $args['validation'] : '' ); ?>" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php echo esc_attr( $placeholder ); ?>"
							<?php echo esc_attr( empty( $args['min'] ) ? '' : 'data-min=' . $args['min'] ); ?>
							<?php echo esc_attr( empty( $args['max'] ) ? '' : 'data-max=' . $args['max'] ); ?>
                        />
                    </label>
					<?php self::description( $args ); ?>
                </div>
				<?php
			}

			public function password( $args, $label, $name, $value ): void {
				$placeholder = empty( $args['placeholder'] ) ? '' : $args['placeholder'];
				?>
                <div class="_setting_item">
                    <label class="_f_wrap_fj_between_fa_center">
                        <span class="_mar_r_xs"><?php echo esc_html( $label ); ?></span>
                        <input type="password" name="<?php echo esc_attr( $name ); ?>" class="_form_control <?php echo esc_attr( array_key_exists( 'validation', $args ) && $args['validation'] ? $args['validation'] : '' ); ?>" value="<?php echo esc_attr( $value ); ?>" placeholder="<?php echo esc_attr( $placeholder ); ?>"/>
                    </label>
					<?php self::description( $args ); ?>
                </div>
				<?php
			}

			public function file( $args, $label, $name, $value ): void {
				?>
                <div class="_setting_item">
                    <div class="_f_wrap_fj_between_fa_center">
                        <span class="_fs_label_mar_r_xs"><?php echo esc_html( $label ); ?></span>
                        <div><?php do_action( 'abprf_add_image', $name, $value ); ?></div>
                    </div>
					<?php self::description( $args ); ?>
                </div>
				<?php
			}

			public function dashicons( $args, $label, $name, $value ): void {
				?>
                <div class="_setting_item">
                    <div class="_d_flex">
                        <div class="_f_wrap_fj_between_fa_center">
                            <span class="_fs_label"><?php echo esc_html( $label ); ?></span>
							<?php self::description( $args ); ?>
                        </div>
						<?php do_action( 'abprf_add_icon', $name, $value, 1 ); ?>
                    </div>
                </div>
				<?php
			}

			public function fontawesome( $args, $label, $name, $value ): void {
				?>
                <div class="_setting_item">
                    <div class="_f_wrap_fj_between_fa_center">
                        <span class="_fs_label_pad_r_xs"><?php echo esc_html( $label ); ?></span>
                        <div><?php do_action( 'abprf_add_icon', $name, $value ); ?></div>
                    </div>
					<?php self::description( $args ); ?>
                </div>
				<?php
			}

			public function datepicker( $args, $label, $name, $value ): void {
				?>
                <div class="_setting_item">
                    <div class="_f_wrap_fj_between_fa_center">
                        <span class="_fs_label_pad_r_xs"><?php echo esc_html( $label ); ?></span>
						<?php ABPRF_Layout::input_date( $name, $value ); ?>
                    </div>
					<?php self::description( $args ); ?>
                </div>
				<?php
			}

			public function textarea( $args, $label, $name, $value ): void {
				$placeholder = empty( $args['placeholder'] ) ? '' : $args['placeholder'];
				?>
                <div class="_setting_item">
                    <label class="_f_wrap_fj_between_fa_center">
                        <span class="_mar_r_xs"><?php echo esc_html( $label ); ?></span>
                        <textarea name="<?php echo esc_attr( $name ); ?>" rows="5" cols="55" class="_form_control <?php echo esc_attr( array_key_exists( 'validation', $args ) && $args['validation'] ? $args['validation'] : '' ); ?>" placeholder="<?php echo esc_attr( $placeholder ); ?>"><?php echo esc_html( $value ); ?></textarea>
                    </label>
					<?php self::description( $args ); ?>
                </div>
				<?php
			}

			public function select( $args, $label, $name, $value ): void {
				?>
                <div class="_setting_item">
                    <label class="_f_wrap_fj_between_fa_center">
                        <span class="_mar_r_xs"><?php echo esc_html( $label ); ?></span>
                        <select name="<?php echo esc_attr( $name ); ?>" class="_form_control">
							<?php foreach ( $args['options'] as $key => $label ) { ?>
                                <option value="<?php echo esc_attr( $key ); ?>" <?php echo esc_attr( $key == $value ? 'selected' : '' ); ?>><?php echo esc_html( $label ); ?></option>
							<?php } ?>
                        </select>
                    </label>
					<?php self::description( $args ); ?>
                </div>
				<?php
			}

			public function radio( $args, $label, $name, $value ): void {
				?>
                <div class="_setting_item ">
                    <div class="_f_wrap_fj_between_fa_center">
                        <span class="_fs_label_mar_r_xs"><?php echo esc_html( $label ); ?></span>
                        <div class="custom_radio">
                            <input type="hidden" class="_form_control" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>"/>
							<?php foreach ( $args['options'] as $key => $option ) { ?>
                                <div class="radio_item">
                                    <button type="button" class="_btn_light_info_xs <?php echo esc_attr( $key == $value ? 'rf_active' : '' ); ?>" data-close-target="#<?php echo esc_attr( $name.$key ); ?>" data-radio="<?php echo esc_attr( $key ); ?>" data-open-icon="far fa-check-circle" data-close-icon="far fa-circle">
                                        <span data-icon class="_mar_r_xs <?php echo esc_attr( $key == $value ? 'far fa-check-circle' : 'far fa-circle' ); ?>"></span><?php echo esc_html( $option ); ?>
                                    </button>
                                </div>
							<?php } ?>
                        </div>
                    </div>
					<?php self::description( $args ); ?>
                </div>
				<?php
			}

			public function checkbox( $args, $label, $name, $value ): void {
				$checked = checked( $value, 'on', false );
				?>
                <div class="_setting_item">
                    <div class="_f_wrap_fj_between_fa_center">
                        <span class="_fs_label_mar_r_xs"><?php echo esc_html( $label ); ?></span>
                        <label>
                            <input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="off"/>
                            <input type="checkbox" class="checkbox" name="<?php echo esc_attr( $name ); ?>" value="on" <?php echo esc_attr( $checked ); ?> />
							<?php echo esc_html( $args['desc'] ); ?>
                        </label>
                    </div>
					<?php self::description( $args ); ?>
                </div>
				<?php
			}

			public function button_switch( $args, $label, $name, $value ): void {
				?>
                <div class="_setting_item">
                    <label><?php ABPRF_Layout::switch_checkbox( $name, $value ); ?><span class="_mar_l_xs"><?php echo esc_html( $label ); ?></span></label>
					<?php self::description( $args ); ?>
                </div>
				<?php
			}

			public function multi_check( $args, $label, $name, $value ): void {
				$value_array = $value ? explode( ',', $value ) : [];
				?>
                <div class="_setting_item ">
                    <div class="_f_wrap_fj_between_fa_center">
                        <span class="_fs_label_mar_r_xs"><?php echo esc_html( $label ); ?></span>
                        <div class="custom_checkbox">
                            <input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>"/>
							<?php foreach ( $args['options'] as $key => $label ) { ?>
                                <div class="checkbox_item">
                                    <button type="button" class="_btn_light_info_xs <?php echo esc_attr( in_array( $key, $value_array ) ? 'rf_active' : '' ); ?>" data-checked="<?php echo esc_attr( $key ); ?>" data-open-icon="far fa-check-square" data-close-icon="far fa-square">
                                        <span data-icon class="_mar_r_xs <?php echo esc_attr( in_array( $key, $value_array ) ? 'far fa-check-square' : 'far fa-square' ); ?>"></span><?php echo esc_html( $label ); ?>
                                    </button>
                                </div>
							<?php } ?>
                        </div>
                    </div>
					<?php self::description( $args ); ?>
                </div>
				<?php
			}

			public function color( $args, $label, $name, $value ): void {
				?>
                <div class="_setting_item">
                    <div class="_f_wrap_fj_between_fa_center">
                        <span class="_fs_label_mar_r_xs"><?php echo esc_html( $label ); ?></span>
                        <label>
                            <input type="text" name="<?php echo esc_attr( $name ); ?>" disabled class="_form_control abprf_color_picker" value="<?php echo esc_attr( $value ); ?>" data-default-color="<?php echo esc_attr( $args['std'] ); ?>"/>
                        </label>
                    </div>
					<?php self::description( $args ); ?>
                </div>
				<?php
			}

			public function wp_role( $args, $label, $name, $value ): void {
				global $wp_roles;
				$value_array = $value ? explode( ',', $value ) : [];
				?>
                <div class="_setting_item ">
                    <div class="_f_wrap_fj_between_fa_center">
                        <span class="_fs_label_mar_r_xs"><?php echo esc_html( $label ); ?></span>
                        <div class="custom_checkbox">
                            <input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>"/>
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
					<?php self::description( $args ); ?>
                </div>
				<?php
			}

			public function wp_editor( $args, $label, $name, $value ): void {
				$value = html_entity_decode( $value );
				?>
                <div class="_setting_item">
                    <div class="_fd_column">
                        <span class="_fs_label_pad_r_xs_max_250"><?php echo esc_html( $label ); ?></span>
						<?php self::description( $args ); ?>
						<?php
							$editor_settings = array(
								'teeny' => true,
								'textarea_name' => $name,
								'textarea_rows' => 15
							);
							if ( isset( $args['options'] ) && is_array( $args['options'] ) ) {
								$editor_settings = array_merge( $editor_settings, $args['options'] );
							}
							wp_editor( $value, $args['section'] . '-' . $args['id'], $editor_settings );
						?>
                    </div>
                </div>
				<?php
			}

			public function pages( $args, $label, $name, $value ): void {
				?>
                <div class="_setting_item">
                    <label class="_f_wrap_fj_between_fa_center">
                        <span class="_mar_r_xs"><?php echo esc_html( $label ); ?></span>
						<?php
							$dropdown = wp_dropdown_pages( array(
								'selected' => esc_attr( $value ),
								'name' => esc_attr( $name ),
								'id' => esc_attr( $name ),
								'class' => '_form_control',
								'show_option_none' => esc_html__( 'Please Select', 'abprf-rental-forge' ),
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
					<?php self::description( $args ); ?>
                </div>
				<?php
			}

			public function sanitize_options( $options ) {
				if ( ! $options ) {
					return $options;
				}
				$abprf_configuration = ABPRF_Function::get_option( 'abprf_configuration' );
				foreach ( $options as $option_slug => $option_value ) {
					$sanitize_callback = $this->get_sanitize_callback( $abprf_configuration, $option_slug );
					if ( $sanitize_callback ) {
						$options[ $option_slug ] = call_user_func( $sanitize_callback, $option_value );
					}
				}

				return $options;
			}

			public function get_sanitize_callback( $abprf_configuration, $slug = '' ): callable|bool {
				if ( empty( $slug ) ) {
					return false;
				}
				foreach ( $this->configuration_data( $abprf_configuration ) as $options ) {
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