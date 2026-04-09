<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	if ( ! class_exists( 'ABPRF_Form' ) ) {
		class ABPRF_Form {
			public function __construct() {
				add_action( 'abprf_load_client_form', array( $this, 'load_client_global_form' ) );
				add_action( 'abprf_post_content', [ $this, 'post_client_form' ] );
				add_filter( 'abprf_get_form_array', array( $this, 'get_form_array' ) );
				add_action( 'wp_ajax_abprf_save_client_form', array( $this, 'save_global_client_form' ) );
				add_action( 'wp_ajax_abprf_import_global_form', array( $this, 'import_global_form' ) );
			}

			public function load_client_global_form(): void {
				$abprf_forms = ABPRF_Function::get_option( 'abprf_forms', ABPRF_Layout::static_form() );
				?>
                <form class="_section_xs abprf_save_client_form" method="post" action="">
                    <h4 class="_abprf_color_theme"><span class="_mar_r_xxs">📋</span> <?php esc_html_e( 'Global Client Form Configuration', 'abprf-rental-forge' ); ?></h4>
					<?php ABPRF_Layout::info_text( 'global_client_forms' ); ?>
                    <div class="_divider_xs"></div>
					<?php $this->passenger_form_settings( $abprf_forms ); ?>
                    <div class="_divider_xs"></div>
                    <button type="submit" class="_btn_theme"><span class="_mar_r_xxs">💾</span><?php esc_html_e( 'Save Global Client Form Configuration', 'abprf-rental-forge' ); ?></button>
                </form>
				<?php
			}

			public function post_client_form( $abprf_infos ): void {
				$client_forms       = array_key_exists( 'client_forms', $abprf_infos ) ? $abprf_infos['client_forms'] : [];
				$display            = array_key_exists( 'display_client_form', $abprf_infos ) ? $abprf_infos['display_client_form'] : 'off';
				$active_global_form = array_key_exists( 'active_global_form', $abprf_infos ) ? $abprf_infos['active_global_form'] : 'on';
				?>
                <div class="tab_item abprf_client_form" data-tabs="#abprf_client_form">
                    <h4 class=" _abprf_color_theme"><span class="_mar_r_xxs">📋</span> <?php esc_html_e( 'Client Forms Configuration', 'abprf-rental-forge' ); ?></h4>
                    <div class="_divider_xs"></div>
                    <div class="group_setting">
                        <div class="_setting_item">
                            <div class="_f_wrap_fj_between_fa_center">
                                <div class="_fa_center">
									<?php ABPRF_Layout::switch_checkbox( 'display_client_form', $display ); ?>
                                    <span class="_fs_label_mar_l_xs"><?php esc_html_e( 'Active Client Form ?', 'abprf-rental-forge' ); ?></span>
                                </div>
                            </div>
                            <div class="_divider_xs"></div>
							<?php ABPRF_Layout::info_text( 'display_client_form' ); ?>
                        </div>
                        <div data-collapse="#display_client_form" class="_setting_item <?php echo esc_attr( $display == 'on' ? 'rf_active' : '' ); ?>">
                            <div class="_fj_between">
                                <div class="_fa_center">
									<?php ABPRF_Layout::switch_checkbox( 'active_global_form', $active_global_form ); ?>
                                    <span class="_fs_label_mar_lr_xs"><?php esc_html_e( 'Use Global Client Form ?', 'abprf-rental-forge' ); ?></span>
                                </div>
                                <div data-collapse="#active_global_form" class=" <?php echo esc_attr( $active_global_form == 'on' ? '' : 'rf_active' ); ?>">
                                    <button type="button" class="_btn_theme abprf_import_global_form"><span class="fas fa-file-upload _mar_r_xs"></span><?php esc_html_e( 'Import Global Client Form', 'abprf-rental-forge' ); ?></button>
                                </div>
                            </div>
                            <div class="_divider_xs"></div>
							<?php ABPRF_Layout::info_text( 'active_global_form' ); ?>
                        </div>
                    </div>
                    <div class="<?php echo esc_attr( $active_global_form == 'on' ? '' : 'rf_active' ); ?>" data-collapse="#active_global_form">
                        <div class="client_form_content">
							<?php $this->passenger_form_settings( $client_forms ); ?>
                        </div>
                    </div>
                </div>
				<?php
			}

			public function save_global_client_form() {
				if ( is_admin() && check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce' ) && current_user_can( 'manage_options' ) ) {
					$client_forms = $this->get_form_array();
					update_option( 'abprf_forms', $client_forms );
					wp_send_json_success( esc_html__( 'Client Form Configuration Saved Successfully ! ', 'abprf-rental-forge' ) );
				} else {
					wp_send_json_success( esc_html__( 'Client Form Configuration not Saved !', 'abprf-rental-forge' ) );
				}
				wp_die();
			}

			//=============================//
			public function passenger_form_settings( $passenger_forms ): void {
				?>
                <div class="configuration_content">
                    <div class="_ov_auto">
                        <table class=" _abprf">
                            <thead>
                            <tr>
                                <th class="_text_table_center"><?php esc_html_e( 'Form Title', 'abprf-rental-forge' ); ?><sup class="_color_required">*</sup></th>
                                <th class="_text_table_center"><?php esc_html_e( 'Unique ID', 'abprf-rental-forge' ); ?><sup class="_color_required">*</sup></th>
                                <th class="_text_table_center"><?php esc_html_e( 'Form Type', 'abprf-rental-forge' ); ?><sup class="_color_required">*</sup></th>
                                <th class="_text_table_center">
									<?php esc_html_e( 'Value Option', 'abprf-rental-forge' ); ?><sup class="_color_required">*</sup>
									<?php ABPRF_Layout::info_text( 'client_form_option' ); ?>
                                </th>
                                <th class="_text_table_center"><?php esc_html_e( 'Default Value', 'abprf-rental-forge' ); ?></th>
                                <th class="_w_100_text_table_center"><?php esc_html_e( 'Required', 'abprf-rental-forge' ); ?><sup class="_color_required">*</sup></th>
                                <th class="_w_75_text_table_center"><?php esc_html_e( 'Action', 'abprf-rental-forge' ); ?></th>
                            </tr>
                            </thead>
                            <tbody class="insertable_area sortable_area">
							<?php
								if ( $passenger_forms && is_array( $passenger_forms ) && sizeof( $passenger_forms ) > 0 ) {
									foreach ( $passenger_forms as $id => $form ) {
										$this->form_item( $form, $id );
									}
								}
							?>
                            </tbody>
                        </table>
                    </div>
                    <div class="_divider_xs"></div>
                    <div class="_fj_between">
						<?php ABPRF_Layout::button_add( __( 'Add New Form', 'abprf-rental-forge' ) ); ?>
                    </div>
                    <div class="abprf_d_none">
                        <table class=" _abprf">
                            <tbody class="hidden_content">
							<?php $this->form_item(); ?>
                            </tbody>
                        </table>
                    </div>
                </div>
				<?php
			}

			public function form_item( $form = [], $id = '' ): void {
				$form         = $form ?: array();
				$type         = array_key_exists( 'type', $form ) ? $form['type'] : 'text';
				$required     = array_key_exists( 'required', $form ) ? $form['required'] : 'off';
				$label        = array_key_exists( 'label', $form ) ? $form['label'] : '';
				$options      = array_key_exists( 'option', $form ) ? $form['option'] : '';
				$d_value      = array_key_exists( 'd_value', $form ) ? $form['d_value'] : '';
				$active_type  = ( $type == 'select' || $type == 'checkbox' || $type == 'radio' ) ? 'rf_active' : '';
				$active_value = $type != 'date' ? 'rf_active' : '';
				$date         = $type == 'date' ? $d_value : '';
				$date_format  = ABPRF_Function::date_picker_format();
				$now          = date_i18n( $date_format, strtotime( current_time( 'Y-m-d' ) ) );
				$hidden_date  = $date ? gmdate( 'Y-m-d', strtotime( $date ) ) : '';
				$visible_date = $date ? date_i18n( $date_format, strtotime( $date ) ) : '';
				$active_date  = $type == 'date' ? 'rf_active' : '';
				?>
                <tr class="delete_area data_single_collapse">
                    <td>
                        <label>
                            <input type="text" class="_form_control validation_name" name="client_form_title[]" placeholder="<?php esc_attr_e( 'Name', 'abprf-rental-forge' ); ?>" value="<?php echo esc_attr( $label ); ?>"/>
                        </label>
                    </td>
                    <th class="_text_table_center">
						<?php if ( $id ) { ?>
                            <input type="hidden" value="<?php echo esc_attr( $id ); ?>" name="client_form_id[]" /><?php echo esc_html( $id ); ?>
						<?php } else { ?>
                            <label>
                                <input type="text" class="_form_control validation_id" name="client_form_id[]" placeholder="<?php esc_attr_e( 'Unique ID', 'abprf-rental-forge' ); ?>" value="<?php echo esc_attr( $id ); ?>"/>
                            </label>
						<?php } ?>
                    </th>
                    <td>
                        <label>
                            <select class="_form_control" name="client_form_type[]" data-collapse-target data-collapse-target-multi>
                                <option value="text" data-option-target-multi="#client_form_value" <?php echo esc_attr( $type == 'text' ? 'selected' : '' ); ?>><?php esc_html_e( 'Text', 'abprf-rental-forge' ); ?></option>
                                <option value="email" data-option-target-multi="#client_form_value" <?php echo esc_attr( $type == 'email' ? 'selected' : '' ); ?>><?php esc_html_e( 'E-Mail', 'abprf-rental-forge' ); ?></option>
                                <option value="number" data-option-target-multi="#client_form_value" <?php echo esc_attr( $type == 'number' ? 'selected' : '' ); ?>><?php esc_html_e( 'Number', 'abprf-rental-forge' ); ?></option>
                                <option value="select" data-option-target-multi="#client_form_type #client_form_value" <?php echo esc_attr( $type == 'select' ? 'selected' : '' ); ?>><?php esc_html_e( 'Select', 'abprf-rental-forge' ); ?></option>
                                <option value="checkbox" data-option-target-multi="#client_form_type #client_form_value" <?php echo esc_attr( $type == 'checkbox' ? 'selected' : '' ); ?>><?php esc_html_e( 'Checkbox', 'abprf-rental-forge' ); ?></option>
                                <option value="radio" data-option-target-multi="#client_form_type #client_form_value" <?php echo esc_attr( $type == 'radio' ? 'selected' : '' ); ?>><?php esc_html_e( 'Radio', 'abprf-rental-forge' ); ?></option>
                                <option value="textarea" data-option-target-multi="#client_form_value" <?php echo esc_attr( $type == 'textarea' ? 'selected' : '' ); ?>><?php esc_html_e( 'Textarea', 'abprf-rental-forge' ); ?></option>
                                <option value="date" data-option-target-multi="#client_form_type_date" <?php echo esc_attr( $type == 'date' ? 'selected' : '' ); ?>><?php esc_html_e( 'Date', 'abprf-rental-forge' ); ?></option>
                            </select>
                        </label>
                    </td>
                    <td>
                        <label data-collapse="#client_form_type" class="<?php echo esc_attr( $active_type ); ?>">
                            <input type="text" class="_form_control validation_name" name="client_form_option[]" placeholder="<?php esc_attr_e( 'Value Option', 'abprf-rental-forge' ); ?>" value="<?php echo esc_attr( $options ); ?>"/>
                        </label>
                    </td>
                    <td>
                        <div class="<?php echo esc_attr( $active_value ); ?>" data-collapse="#client_form_value">
                            <label>
                                <input type="text" class="_form_control validation_name" name="client_form_value[]" placeholder="<?php esc_attr_e( 'Default Value', 'abprf-rental-forge' ); ?>" value="<?php echo esc_attr( $d_value ); ?>"/>
                            </label>
                        </div>
                        <div class="<?php echo esc_attr( $active_date ); ?>" data-collapse="#client_form_type_date">
                            <label>
                                <input type="hidden" name="client_form_value_date[]" value="<?php echo esc_attr( $hidden_date ); ?>"/>
                                <input type="text" readonly name="" class="_form_control abprf_datepicker" value="<?php echo esc_attr( $visible_date ); ?>" placeholder="<?php echo esc_attr( $now ); ?>"/>
                                <span class="fas fa-times date_close_icon" title="<?php esc_attr_e( 'Clear Date', 'abprf-rental-forge' ); ?>"></span>
                            </label>
                        </div>
                    </td>
                    <td>
						<?php ABPRF_Layout::switch_checkbox( 'client_form_required[]', $required ); ?>
                    </td>
                    <td><?php ABPRF_Layout::button_delete_sort(); ?></td>
                </tr>
				<?php
			}

			//=============================//
			public static function get_form_array() {
				$form_infos = array();
				if ( is_admin() && ( ( isset( $_POST['abprf_post_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['abprf_post_nonce'] ) ), 'abprf_post_nonce' ) ) || check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce' ) ) ) {
					$form_title = isset( $_POST['client_form_title'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['client_form_title'] ) ) : [];
					$form_ids   = isset( $_POST['client_form_id'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['client_form_id'] ) ) : [];
					$types      = isset( $_POST['client_form_type'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['client_form_type'] ) ) : [];
					$option     = isset( $_POST['client_form_option'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['client_form_option'] ) ) : [];
					$d_value    = isset( $_POST['client_form_value'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['client_form_value'] ) ) : [];
					$date_value = isset( $_POST['client_form_value_date'] ) ? array_map( 'sanitize_textarea_field', wp_unslash( $_POST['client_form_value_date'] ) ) : [];
					$required   = isset( $_POST['client_form_required'] ) ? array_map( 'sanitize_textarea_field', wp_unslash( $_POST['client_form_required'] ) ) : [];
					if ( sizeof( $form_ids ) > 0 ) {
						foreach ( $form_ids as $key => $form_id ) {
							$title = $form_title[ $key ];
							$type  = $types[ $key ];
							if ( $form_id && $title && $type ) {
								$value = $d_value[ $key ];
								if ( $type == 'date' ) {
									$value = $date_value[ $key ];
								}
								$form_infos[ $form_id ]['label']    = $title;
								$form_infos[ $form_id ]['type']     = $type;
								$form_infos[ $form_id ]['option']   = $option[ $key ];
								$form_infos[ $form_id ]['d_value']  = $value;
								$form_infos[ $form_id ]['required'] = $required[ $key ];
							}
						}
					}
				}

				return apply_filters( 'abprf_form_infos_filter', $form_infos );
			}

			public function import_global_form(): void {
				if ( is_admin() && check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce' ) && current_user_can( 'manage_options' ) ) {
					$forms = ABPRF_Function::get_option( 'abprf_forms', ABPRF_Layout::static_form() );
					$forms = $forms && is_array( $forms ) ? $forms : [];
					$this->passenger_form_settings( $forms );
				}
				wp_die();
			}
		}
		new ABPRF_Form();
	}