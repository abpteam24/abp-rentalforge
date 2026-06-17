<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	if ( ! class_exists( 'ABPRF_Feature' ) ) {
		class ABPRF_Feature {
			public function __construct() {
				add_action( 'abprf_global_feature', array( $this, 'global_feature' ) );
				add_action( 'wp_ajax_abprf_save_feature', array( $this, 'save_feature' ) );
				add_action( 'wp_ajax_abprf_delete_feature', array( $this, 'delete_feature' ) );
				add_action( 'wp_ajax_abprf_edit_feature', array( $this, 'edit_feature' ) );
			}
			public function global_feature(): void {
				?>
                <div class="feature_area">
                    <div class="feature_list _group_list">
						<?php $this->feature_list(); ?>
                    </div>
                    <div class="_divider_xs"></div>
                    <div class="configuration_content">
                        <div class="form_area">
                            <div class="hide_on_load">
                                <table class="_abprf ">
                                    <thead>
                                    <tr>
                                        <th><?php esc_html_e( 'Icon', 'abp-rentalforge' ); ?></th>
                                        <th><?php esc_html_e( 'Label', 'abp-rentalforge' ); ?><sup class="_color_required">*</sup></th>
                                        <th><?php esc_html_e( 'Value', 'abp-rentalforge' ); ?><sup class="_color_required">*</sup></th>
                                        <th class="_w_10"><?php esc_html_e( 'Action', 'abp-rentalforge' ); ?></th>
                                    </tr>
                                    </thead>
                                    <tbody class="insertable_area sortable_area">
                                    </tbody>
                                </table>
                                <div class="_divider_xs"></div>
                            </div>
                            <div class="_fj_between">
								<?php ABPRF_Layout::button_add( __( 'Add New Feature', 'abp-rentalforge' ) ); ?>
                                <button type="button" class="_btn_theme hide_on_load save_feature"><span class="_mar_r_xxs">💾</span><?php esc_html_e( 'Save Feature', 'abp-rentalforge' ); ?></button>
                            </div>
                        </div>
                        <div class="abprf_d_none">
                            <table class="_abprf">
                                <tbody class="hidden_content">
								<?php self::form_feature(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
				<?php
			}
			public function feature_list(): void {
				$features = ABPRF_Function::get_option( 'abprf_feature' );
				//echo '<pre>';				print_r( $features );				echo '</pre>';
				if ( sizeof( $features ) > 0 ) {
					foreach ( $features as $key => $feature ) {
						$label = $feature['label'] ?? '';
						?>
                        <div class="_list_item">
                            <h6 class="_abprf"> <?php ABPRF_Layout::image_icon( $feature['icon'] ?? '' ); ?> <?php echo esc_html( $label ); ?> - <?php echo esc_html( $feature['value'] ?? '' ); ?></h6>
                            <div class="_d_flex">
                                <button type="button" class="_btn_light_yellow_mar_r_xxs edit_feature" data-id="<?php echo esc_attr( $key ); ?>" title="<?php echo esc_attr__( 'Edit : ', 'abp-rentalforge' ) . ' ' . esc_attr( $label ); ?>">✍️</button>
                                <button type="button" class="_btn_light_danger_xxs delete_feature" data-fec_id="<?php echo esc_attr( $key ); ?>" title="<?php echo esc_attr__( 'Trash : ', 'abp-rentalforge' ) . ' ' . esc_attr( $label ); ?>">❌</button>
                            </div>
                        </div>
						<?php
					}
				} else {
					ABPRF_Layout::layout_warning_info( 'no_feature' );
				}
			}
			public static function form_feature( $feature = [], $id = '' ): void {
				$label = is_array( $feature ) && array_key_exists( 'label', $feature ) ? $feature['label'] : '';
				$value = is_array( $feature ) && array_key_exists( 'value', $feature ) ? $feature['value'] : '';
				$icon  = is_array( $feature ) && array_key_exists( 'icon', $feature ) ? $feature['icon'] : '';
				?>
                <tr class="delete_area">
                    <th><?php do_action( 'abprf_add_icon', 'feature_icon[]', $icon ); ?></th>
                    <th>
                        <label>
                            <input type="hidden" name="feature_id[]" value="<?php echo esc_attr( $id ); ?>"/>
                            <input type="text" class="_form_control validation_name" name="feature_name[]" placeholder="<?php esc_attr_e( 'EX: Feature Title', 'abp-rentalforge' ); ?>" value="<?php echo esc_attr( $label ); ?>" required/>
                        </label>
                    </th>
                    <th>
                        <label>
                            <input type="text" class="_form_control validation_name" name="feature_value[]" placeholder="<?php esc_attr_e( 'EX: Feature Value', 'abp-rentalforge' ); ?>" value="<?php echo esc_attr( $value ); ?>" required/>
                        </label>
                    </th>
                    <td><?php ABPRF_Layout::button_delete_sort(); ?></td>
                </tr>
				<?php
			}
			public function save_feature(): void {
				if ( ! check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce', false ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Invalid security token.', 'abp-rentalforge' ) ], 403 );
				}
				if ( ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Insufficient permissions.', 'abp-rentalforge' ) ], 403 );
				}
				$old_features   = ABPRF_Function::get_option( 'abprf_feature' );
				$old_features   = is_array( $old_features ) ? $old_features : [];
				$id             = ! empty( $old_features ) ? array_key_last( $old_features ) : 'fec_id_1';
				$feature_ids    = isset( $_POST['feature_id'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['feature_id'] ) ) : [];
				$feature_names  = isset( $_POST['feature_name'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['feature_name'] ) ) : [];
				$feature_values = isset( $_POST['feature_value'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['feature_value'] ) ) : [];
				$feature_icon   = isset( $_POST['feature_icon'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['feature_icon'] ) ) : [];
				$property_add   = isset( $_POST['property_add'] ) ? sanitize_text_field( wp_unslash( $_POST['property_add'] ) ) : 0;
				if ( count( $feature_names ) > 0 && count( $feature_values ) > 0 ) {
					foreach ( $feature_names as $key => $feature_name ) {
						if ( ! empty( $feature_name ) && ! empty( $feature_values[ $key ] ) ) {
							$old_id = array_key_exists( $key, $feature_ids ) ? $feature_ids[ $key ] : '';
							if ( ! empty( $old_id ) && array_key_exists( $old_id, $old_features ) ) {
								$id = $old_id;
							} else {
								if ( array_key_exists( $id, $old_features ) ) {
									$number    = (int) str_replace( 'fec_id_', '', $id );
									$new_count = $number + 1;
									$id        = 'fec_id_' . $new_count;
									while ( array_key_exists( $id, $old_features ) ) {
										$number    = (int) str_replace( 'fec_id_', '', $id );
										$new_count = $number + 1;
										$id        = 'fec_id_' . $new_count;
									}
								}
							}
							$old_features[ $id ]['label'] = $feature_name;
							$old_features[ $id ]['value'] = $feature_values[ $key ];
							$old_features[ $id ]['icon']  = $feature_icon[ $key ] ?? '';
						}
					}
				}
				update_option( 'abprf_feature', $old_features );
				self::update_feature_js();
				ob_start();
				if ( ! empty( $property_add ) && (int) $property_add > 0 ) {
					echo '';
				} else {
					$this->feature_list();
				}
				$html = ob_get_clean();
				wp_send_json_success( [
					'html' => $html,
					'feature_js' => ABPRF_Function::get_option( 'abprf_feature_js' ),
					'msg' => __( 'Feature Saved Successfully..........!!', 'abp-rentalforge' ),
				] );
			}
			public function delete_feature(): void {
				if ( ! check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce', false ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Invalid security token.', 'abp-rentalforge' ) ], 403 );
				}
				if ( ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Insufficient permissions.', 'abp-rentalforge' ) ], 403 );
				}
				$fec_id   = isset( $_POST['fec_id'] ) ? sanitize_text_field( wp_unslash( $_POST['fec_id'] ) ) : '';
				$features = ABPRF_Function::get_option( 'abprf_feature' );
				$features = is_array( $features ) ? $features : [];
				if ( ! empty( $fec_id ) && array_key_exists( $fec_id, $features ) ) {
					unset( $features[ $fec_id ] );
					update_option( 'abprf_feature', $features );
					self::update_feature_js();
				}
				ob_start();
				$this->feature_list();
				$html = ob_get_clean();
				wp_send_json_success( [
					'html' => $html,
					'msg' => __( 'Feature Deleted Successfully!', 'abp-rentalforge' ),
				] );
			}
			public function edit_feature(): void {
				if ( ! check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce', false ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Invalid security token.', 'abp-rentalforge' ) ], 403 );
				}
				if ( ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Insufficient permissions.', 'abp-rentalforge' ) ], 403 );
				}
				$fec_id   = isset( $_POST['fec_id'] ) ? sanitize_text_field( wp_unslash( $_POST['fec_id'] ) ) : '';
				$features = ABPRF_Function::get_option( 'abprf_feature' );
				$features = is_array( $features ) ? $features : [];
				$feature  = $features[ $fec_id ] ?? [];
				ob_start();
				self::form_feature( $feature, $fec_id );
				$html = ob_get_clean();
				wp_send_json_success( [
					'html' => $html,
					'msg' => __( 'Feature Ready to Edit...... !', 'abp-rentalforge' ),
				] );
			}
			public static function update_feature_js(): void {
				$features   = ABPRF_Function::get_option( 'abprf_feature' );
				$features   = is_array( $features ) ? $features : [];
				$feature_js = [];
				if ( sizeof( $features ) > 0 ) {
					foreach ( $features as $key => $feature ) {
						$feature_js[] = [ 'id' => $key, 'icon' => ( $feature['icon'] ?? '' ), 'label' => ( $feature['label'] ?? '' ), 'value' => ( $feature['value'] ?? '' ) ];
					}
					update_option( 'abprf_feature_js', $feature_js );
				}
			}
		}
		new ABPRF_Feature();
	}