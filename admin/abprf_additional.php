<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	if ( ! class_exists( 'ABPRF_Additional' ) ) {
		class ABPRF_Additional {
			public function __construct() {
				add_action( 'abprf_global_additional_service', array( $this, 'global_additional_service' ) );
				add_action( 'abprf_post_content', [ $this, 'post_additional_service' ] );
				add_filter( 'abprf_get_additional_array', array( $this, 'get_additional_array' ) );
				add_action( 'wp_ajax_abprf_save_additional_service', array( $this, 'save_global_additional_service' ) );
				add_action( 'wp_ajax_abprf_import_additional', array( $this, 'import_additional' ) );
			}

			public function global_additional_service(): void {
				$additional_services = ABPRF_Function::get_option( 'abprf_additional', ABPRF_Layout::static_additional() );
				?>
                <div class="tab_item" data-tabs="#abprf_global_additional_service">
                    <form class=" save_additional_service" method="post" action="">
                        <h4 class="_abprf_color"><span class="_mar_r_xxs">💰</span> <?php esc_html_e( 'Global Additional services Configuration', 'abp-rentalforge' ); ?></h4>
						<?php ABPRF_Layout::info_text( 'additional_services' ); ?>
                        <div class="_divider_xs"></div>
						<?php $this->additional_service( $additional_services ); ?>
                        <div class="_divider_xs"></div>
                        <button type="submit" class="_btn_theme"><span class="_mar_r_xxs">💾</span><?php esc_html_e( 'Save Global Additional services Configuration', 'abp-rentalforge' ); ?></button>
                    </form>
                </div>
				<?php
			}

			public function post_additional_service( $abprf_infos ): void {
				$additional_services      = array_key_exists( 'additional_services', $abprf_infos ) ? $abprf_infos['additional_services'] : array();
				$display                  = array_key_exists( 'display_additional_services', $abprf_infos ) ? $abprf_infos['display_additional_services'] : 'on';
				$active_global_additional = array_key_exists( 'active_global_additional', $abprf_infos ) ? $abprf_infos['active_global_additional'] : 'on';
				?>
                <div class="tab_item additional_configuration" data-tabs="#abprf_additional_service">
                    <h4 class="_abprf_color_theme"><span class="_mar_r_xxs">💰</span><?php esc_html_e( 'Additional services Configuration', 'abp-rentalforge' ); ?></h4>
                    <div class="_divider_xs"></div>
                    <div class="group_setting">
                        <div class="setting_item">
                            <div class="_f_wrap_fj_between_fa_center">
                                <div class="_fa_center">
									<?php ABPRF_Layout::switch_checkbox( 'display_additional_services', $display ); ?>
                                    <span class="_fs_label_mar_l_xs"><?php esc_html_e( 'Active Additional services ?', 'abp-rentalforge' ); ?></span>
                                </div>
                            </div>
                            <div class="_divider_xs"></div>
							<?php ABPRF_Layout::info_text( 'display_additional_services' ); ?>
                        </div>
                        <div data-collapse="#display_additional_services" class="setting_item <?php echo esc_attr( $display == 'on' ? 'rf_active' : '' ); ?>">
                            <div class="_fj_between">
                                <div class="_fa_center">
									<?php ABPRF_Layout::switch_checkbox( 'active_global_additional', $active_global_additional ); ?>
                                    <span class="_fs_label_mar_lr_xs"><?php esc_html_e( 'Use Global Additional Service ?', 'abp-rentalforge' ); ?></span>
                                </div>
                                <div data-collapse="#active_global_additional" class=" <?php echo esc_attr( $active_global_additional == 'on' ? '' : 'rf_active' ); ?>">
                                    <button type="button" class="_btn_theme import_additional"><span class="fas fa-file-upload _mar_r_xs"></span><?php esc_html_e( 'Import Additional Service', 'abp-rentalforge' ); ?></button>
                                </div>
                            </div>
                            <div class="_divider_xs"></div>
							<?php ABPRF_Layout::info_text( 'active_global_additional' ); ?>
                        </div>
                    </div>
                    <div class="<?php echo esc_attr( $active_global_additional == 'on' ? '' : 'rf_active' ); ?>" data-collapse="#active_global_additional">
                        <div class="additional_content _mar_t_xs">
							<?php $this->additional_service( $additional_services ); ?>
                        </div>
                    </div>
                </div>
				<?php
			}

			public function get_additional_array() {
				$additional_services = array();
				if ( is_admin() && ( ( isset( $_POST['abprf_post_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['abprf_post_nonce'] ) ), 'abprf_post_nonce' ) ) || check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce' ) ) ) {
					$additional_ids         = isset( $_POST['additional_id'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['additional_id'] ) ) : [];
					$additional_icon        = isset( $_POST['additional_icon'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['additional_icon'] ) ) : [];
					$additional_name        = isset( $_POST['additional_name'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['additional_name'] ) ) : [];
					$additional_qty         = isset( $_POST['additional_qty'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['additional_qty'] ) ) : [];
					$max_qty                = isset( $_POST['additional_max_qty'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['additional_max_qty'] ) ) : [];
					$returnable             = isset( $_POST['additional_returnable'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['additional_returnable'] ) ) : [];
					$additional_price       = isset( $_POST['additional_price'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['additional_price'] ) ) : [];
					$additional_description = isset( $_POST['additional_description'] ) ? array_map( 'sanitize_textarea_field', wp_unslash( $_POST['additional_description'] ) ) : [];
					if ( sizeof( $additional_ids ) > 0 ) {
						foreach ( $additional_ids as $key => $additional_id ) {
							if ( $additional_name[ $key ] ) {
								$additional_id                                        = array_key_exists( $additional_id, $additional_services ) ? uniqid() : $additional_id;
								$additional_services[ $additional_id ]['icon']        = $additional_icon[ $key ] ?? '';
								$additional_services[ $additional_id ]['name']        = $additional_name[ $key ];
								$additional_services[ $additional_id ]['qty']         = $additional_qty[ $key ];
								$additional_services[ $additional_id ]['max_qty']     = $max_qty[ $key ];
								$additional_services[ $additional_id ]['price']       = $additional_price[ $key ];
								$additional_services[ $additional_id ]['returnable']  = $returnable[ $key ];
								$additional_services[ $additional_id ]['description'] = $additional_description[ $key ] ?? '';
							}
						}
					}
					$additional_services = apply_filters( 'abprf_additional_services_filter', $additional_services );
				}

				return $additional_services;
			}



			public function additional_service( $services = [] ): void {
				?>
                <div class="configuration_content additional_service">
                    <div class="_ov_auto">
                        <table class="_abprf">
                            <thead>
                            <tr>
                                <th class="_w_125"><?php esc_html_e( 'Icon / Image', 'abp-rentalforge' ); ?></th>
                                <th class="_min_200"><?php esc_html_e( 'Name', 'abp-rentalforge' ); ?><sup class="_color_required">*</sup></th>
                                <th class="_min_100"><?php esc_html_e( 'Quantity', 'abp-rentalforge' ); ?></th>
                                <th class="_min_100"><?php esc_html_e( 'Price', 'abp-rentalforge' ); ?></th>
                                <th class="_min_100"><?php esc_html_e( 'Max qty', 'abp-rentalforge' ); ?></th>
                                <th class="_min_100"><?php esc_html_e( 'Returnable or Not', 'abp-rentalforge' ); ?></th>
                                <th class="_min_250"><?php esc_html_e( 'Description', 'abp-rentalforge' ); ?></th>
                                <th class="_w_75"><?php esc_html_e( 'Action', 'abp-rentalforge' ); ?></th>
                            </tr>
                            </thead>
                            <tbody class="insertable_area sortable_area">
							<?php
								if ( $services && is_array( $services ) && sizeof( $services ) > 0 ) {
									foreach ( $services as $key => $service ) {
										$this->service_item( $key, $service );
									}
								}
							?>
                            </tbody>
                        </table>
                    </div>
                    <div class="_divider_xs"></div>
					<?php ABPRF_Layout::button_add( __( 'Add New Additional services', 'abp-rentalforge' ) ); ?>
                    <div class="abprf_d_none">
                        <table class="_abprf">
                            <tbody class="hidden_content">
							<?php $this->service_item(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
				<?php
			}

			public function service_item( $key = '', $field = array() ): void {
				$field       = $field ?: array();
				$icon_image  = array_key_exists( 'icon', $field ) ? $field['icon'] : '';
				$name        = array_key_exists( 'name', $field ) ? $field['name'] : '';
				$qty         = array_key_exists( 'qty', $field ) ? $field['qty'] : '';
				$max_ty      = array_key_exists( 'max_qty', $field ) ? $field['max_qty'] : '';
				$price       = array_key_exists( 'price', $field ) ? $field['price'] : '';
				$returnable  = array_key_exists( 'returnable', $field ) ? $field['returnable'] : 'no';
				$description = array_key_exists( 'description', $field ) ? $field['description'] : '';
				?>
                <tr class="delete_area ">
                    <td> <?php do_action( 'abprf_add_image_icon', 'additional_icon[]', $icon_image ); ?>  </td>
                    <td>
                        <input type="hidden" name="additional_id[]" value="<?php echo esc_attr( $key ?: uniqid() ); ?>"/>
                        <label>
                            <input type="text" class="_form_control validation_name" name="additional_name[]" placeholder="<?php esc_attr_e( 'EX: Water Bottle', 'abp-rentalforge' ); ?>" value="<?php echo esc_attr( $name ); ?>"/>
                        </label>
                    </td>
                    <td>
                        <label>
                            <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="additional_qty[]" placeholder="<?php esc_attr_e( 'EX: 15', 'abp-rentalforge' ); ?>" value="<?php echo esc_attr( $qty ); ?>"/>
                        </label>
                    </td>
                    <td>
                        <label>
                            <input type="number" pattern="[0-9]*" step="0.01" class="_form_control validation_price" name="additional_price[]" placeholder="<?php esc_attr_e( 'EX: 15', 'abp-rentalforge' ); ?>" value="<?php echo esc_attr( $price ); ?>"/>
                        </label>
                    </td>
                    <td>
                        <label>
                            <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="additional_max_qty[]" placeholder="<?php esc_attr_e( 'EX: 15', 'abp-rentalforge' ); ?>" value="<?php echo esc_attr( $max_ty ); ?>"/>
                        </label>
                    </td>
                    <td>
                        <label>
                            <select class="_form_control" name="additional_returnable[]">
                                <option value="yes" <?php echo esc_attr( $returnable == 'yes' ? 'selected' : '' ); ?>><?php esc_html_e( 'Yes', 'abp-rentalforge' ); ?></option>
                                <option value="no" <?php echo esc_attr( $returnable == 'no' ? 'selected' : '' ); ?>><?php esc_html_e( 'No', 'abp-rentalforge' ); ?></option>
                            </select>
                        </label>
                    </td>
                    <td>
                        <label>
                            <textarea class="_form_control" name="additional_description[]" placeholder="<?php esc_attr_e( 'EX: Description', 'abp-rentalforge' ); ?>"><?php echo esc_html( $description ); ?></textarea>
                        </label>
                    </td>
                    <td><?php ABPRF_Layout::button_delete_sort(); ?></td>
                </tr>
				<?php
			}
			public function save_global_additional_service(): void {
				if ( ! check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce', false ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Invalid security token.', 'abp-rentalforge' ) ], 403 );
				}
				if ( ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Insufficient permissions.', 'abp-rentalforge' ) ], 403);
				}
				$additional_services = $this->get_additional_array();
				if ( is_array( $additional_services ) ) {
					update_option( 'abprf_additional', $additional_services );
					wp_send_json_success( [ 'msg' => __( 'Additional services Configuration Saved Successfully ..... !! ', 'abp-rentalforge' ) ] );
				}
				wp_send_json_error( [ 'msg' => __( 'Additional services Configuration not Saved  ..... !! ', 'abp-rentalforge' ) ], 400 );
			}

			public function import_additional(): void {
				if ( ! check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce', false ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Invalid security token.', 'abp-rentalforge' ) ], 403 );
				}
				if ( ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Insufficient permissions.', 'abp-rentalforge' ) ], 403);
				}
				$additional_services = ABPRF_Function::get_option( 'abprf_additional', ABPRF_Layout::static_additional() );
				$additional_services = is_array( $additional_services ) ? $additional_services : [];
				ob_start();
				$this->additional_service( $additional_services );
				$html_content = ob_get_clean();
				wp_send_json_success( ['html'=>$html_content, 'msg' => __( 'Additional services ImportedSuccessfully ..... !! ', 'abp-rentalforge' ) ] );
			}
		}
		new ABPRF_Additional();
	}