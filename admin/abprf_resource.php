<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	if ( ! class_exists( 'ABPRF_Resource' ) ) {
		class ABPRF_Resource {
			public function __construct() {
				add_action( 'abprf_post_content', array( $this, 'post_resource' ) );
				add_action( 'abprf_global_resource', array( $this, 'global_resource' ) );
				add_filter( 'abprf_get_faq_array', array( $this, 'get_faq_array' ) );
				add_action( 'wp_ajax_abprf_save_faqs', array( $this, 'save_faqs' ) );
				add_action( 'wp_ajax_abprf_save_tc', array( $this, 'save_tc' ) );
				add_action( 'wp_ajax_abprf_import_faq', array( $this, 'import_faq' ) );
				add_action( 'wp_ajax_abprf_import_tc', array( $this, 'import_tc' ) );
			}
			public function post_resource( $abprf_infos = [] ): void {
				?>
                <div class="tab_item" data-tabs="#abprf_resource">
					<?php $this->tax( $abprf_infos );
						$this->post_faq( $abprf_infos );
						$this->post_tc( $abprf_infos ); ?>
                </div>
				<?php
			}
			public function global_resource(): void {
				$this->global_faq();
				$this->global_tc();
			}
			public function tax( $abprf_infos = [] ): void {
				$tax_status  = $abprf_infos['_tax_status'] ?? '';
				$tax_classes = WC_Tax::get_tax_rate_classes();
				$tax_class   = $abprf_infos['_tax_class'] ?? '';
				?>
                <div class="_abp_panel_xs">
                    <div class="_panel_head_xs" data-collapse-target="#abprf_tax"><h5 class="_abp"><span class="_mar_r_xxs">🧾</span> <?php esc_html_e( 'Tax Configuration', 'abp-rentalforge' ); ?></h5></div>
                    <div class="_panel_body_xs abp_active" data-collapse="#abprf_tax">
						<?php if ( get_option( 'woocommerce_calc_taxes' ) == 'yes' ) { ?>
                            <div class="group_setting">
                                <div class="setting_item">
                                    <label class="_f_equal_f_wrap">
                                        <span class="_mar_r_xs"><?php esc_html_e( 'Tax Status', 'abp-rentalforge' ); ?></span>
                                        <select class="_form_control" name="_tax_status">
                                            <option disabled selected><?php esc_html_e( 'Please Select', 'abp-rentalforge' ); ?></option>
                                            <option value="taxable" <?php echo esc_attr( $tax_status == 'taxable' ? 'selected' : '' ); ?>><?php esc_html_e( 'Taxable', 'abp-rentalforge' ); ?></option>
                                            <option value="shipping" <?php echo esc_attr( $tax_status == 'shipping' ? 'selected' : '' ); ?>><?php esc_html_e( 'Shipping only', 'abp-rentalforge' ); ?></option>
                                            <option value="none" <?php echo esc_attr( $tax_status == 'none' ? 'selected' : '' ); ?>><?php esc_html_e( 'None', 'abp-rentalforge' ); ?></option>
                                        </select>
                                    </label>
                                </div>
                                <div class="setting_item">
                                    <label class="_f_equal_f_wrap">
                                        <span class="_mar_r_xs"><?php esc_html_e( 'Tax Class', 'abp-rentalforge' ); ?></span>
                                        <select class="_form_control" name="_tax_class">
                                            <option disabled selected><?php esc_html_e( 'Please Select', 'abp-rentalforge' ); ?></option>
                                            <option value="standard" <?php echo esc_attr( $tax_class == 'standard' ? 'selected' : '' ); ?>><?php esc_html_e( 'Standard', 'abp-rentalforge' ); ?></option>
											<?php if ( sizeof( $tax_classes ) > 0 ) { ?>
												<?php foreach ( $tax_classes as $class ) { ?>
                                                    <option value="<?php echo esc_attr( $class->slug ); ?>" <?php echo esc_attr( $tax_class == $class->slug ? 'selected' : '' ); ?>> <?php echo esc_html( $class->name ); ?> </option>
												<?php } ?>
											<?php } ?>
                                        </select>
                                    </label>
                                    <div class="_divider_xs"></div>
									<?php ABPRF_Layout::info_text( '_tax_class' ); ?>
                                </div>
                            </div>
						<?php } else {
							ABPRF_Layout::layout_warning_info( 'enable_tax_msg' );
						} ?>
                    </div>
                </div>
				<?php
			}
			public function post_faq( $abprf_infos = [] ): void {
				if ( ABPRF_Function::on_off( 'faq' ) ) {
					$post_id           = absint( $abprf_infos['post_id'] ?? 0 );
					$display           = $abprf_infos['display_faq'] ?? 'on';
					$active_global_faq = $abprf_infos['active_global_faq'] ?? 'on';
					$faqs              = get_post_meta( $post_id, 'abprf_faqs', true );
					$faqs              = is_array( $faqs ) ? $faqs : [];
					?>
                    <div class="_abp_panel_xs faq_configuration _mar_t_xs">
                        <div class="_panel_head_xs" data-collapse-target="#abprf_faq"><h5 class="_abp"><span class="_mar_r_xxs">❓</span><?php esc_html_e( 'FAQs Configuration', 'abp-rentalforge' ); ?></h5></div>
                        <div class="_panel_body_xs abp_active" data-collapse="#abprf_faq">
                            <div class="group_setting">
                                <div class="setting_item">
                                    <div class="_f_wrap_fj_between_fa_center">
                                        <div class="_fa_center">
											<?php ABPRF_Layout::switch_checkbox( 'display_faq', $display ); ?>
                                            <span class="_fs_label_mar_l_xs"><?php esc_html_e( 'Active FAQs ?', 'abp-rentalforge' ); ?></span>
                                        </div>
                                    </div>
                                    <div class="_divider_xs"></div>
									<?php ABPRF_Layout::info_text( 'display_faq' ); ?>
                                </div>
                                <div data-collapse="#display_faq" class="setting_item <?php echo esc_attr( $display == 'on' ? 'abp_active' : '' ); ?>">
                                    <div class="_fj_between">
                                        <div class="_fa_center">
											<?php ABPRF_Layout::switch_checkbox( 'active_global_faq', $active_global_faq ); ?>
                                            <span class="_fs_label_mar_lr_xs"><?php esc_html_e( 'Use Global FAQ ?', 'abp-rentalforge' ); ?></span>
                                        </div>
                                        <div data-collapse="#active_global_faq" class=" <?php echo esc_attr( $active_global_faq == 'on' ? '' : 'abp_active' ); ?>">
                                            <button type="button" class="_btn_theme import_faq"><span class="fas fa-file-upload _mar_r_xs"></span><?php esc_html_e( 'Import Global FAQ', 'abp-rentalforge' ); ?></button>
                                        </div>
                                    </div>
                                    <div class="_divider_xs"></div>
									<?php ABPRF_Layout::info_text( 'active_global_faq' ); ?>
                                </div>
                            </div>
                            <div data-collapse="#display_faq" class="<?php echo esc_attr( $display == 'on' ? 'abp_active' : '' ); ?>">
                                <div class="_mar_t_xs <?php echo esc_attr( $active_global_faq == 'on' ? '' : 'abp_active' ); ?>" data-collapse="#active_global_faq">
                                    <div class="faq_content">
										<?php $this->faq( $faqs ); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
				<?php }
			}
			public function post_tc( $abprf_infos = [] ): void {
				if ( ABPRF_Function::on_off( 'tc' ) ) {
					$post_id          = absint( $abprf_infos['post_id'] ?? 0 );
					$abprf_tc         = get_post_meta( $post_id, 'abprf_tc', true );
					$display          = $abprf_infos['display_tc'] ?? 'on';
					$active_global_tc = $abprf_infos['active_global_tc'] ?? 'on';
					?>
                    <div class="_abp_panel_xs tc_configuration _mar_t_xs">
                        <div class="_panel_head_xs" data-collapse-target="#abprf_tc"><h5 class="_abp"><span class="_mar_r_xxs">🤝</span><?php esc_html_e( 'Term & Conditions', 'abp-rentalforge' ); ?></h5></div>
                        <div class="_panel_body_xs abp_active" data-collapse="#abprf_tc">
                            <div class="group_setting">
                                <div class="setting_item">
                                    <div class="_f_wrap_fj_between_fa_center">
                                        <div class="_fa_center">
											<?php ABPRF_Layout::switch_checkbox( 'display_tc', $display ); ?>
                                            <span class="_fs_label_mar_l_xs"><?php esc_html_e( 'Active Term & Conditions ?', 'abp-rentalforge' ); ?></span>
                                        </div>
                                    </div>
                                    <div class="_divider_xs"></div>
									<?php ABPRF_Layout::info_text( 'display_tc' ); ?>
                                </div>
                                <div data-collapse="#display_tc" class="setting_item <?php echo esc_attr( $display == 'on' ? 'abp_active' : '' ); ?>">
                                    <div class="_fj_between">
                                        <div class="_fa_center">
											<?php ABPRF_Layout::switch_checkbox( 'active_global_tc', $active_global_tc ); ?>
                                            <span class="_fs_label_mar_lr_xs"><?php esc_html_e( 'Use Global Term & Conditions ?', 'abp-rentalforge' ); ?></span>
                                        </div>
                                        <div data-collapse="#active_global_tc" class=" <?php echo esc_attr( $active_global_tc == 'on' ? '' : 'abp_active' ); ?>">
                                            <button type="button" class="_btn_theme import_tc"><span class="fas fa-file-upload _mar_r_xs"></span><?php esc_html_e( 'Import Global Term & Conditions', 'abp-rentalforge' ); ?></button>
                                        </div>
                                    </div>
                                    <div class="_divider_xs"></div>
									<?php ABPRF_Layout::info_text( 'active_global_tc' ); ?>
                                </div>
                                <div data-collapse="#display_tc" class="<?php echo esc_attr( $display == 'on' ? 'abp_active' : '' ); ?>">
                                    <div class="setting_item full_width <?php echo esc_attr( $active_global_tc == 'on' ? '' : 'abp_active' ); ?>" data-collapse="#active_global_tc">
                                        <div class="tc_content">
											<?php $this->tc( $abprf_tc ); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
					<?php
				}
			}
			public function global_tc(): void {
				if ( ABPRF_Function::on_off( 'tc' ) ) {
					$tcs = ABPRF_Function::get_option( 'abprf_tc', '' );
					?>
                    <div class="_abp_panel_xs">
                        <div class="_panel_head_xs _fd_column" data-collapse-target="#abprf_tc">
                            <h5 class="_abp"><span class="_mar_r_xxs">🤝</span><?php esc_html_e( 'Global Term & Conditions Configuration', 'abp-rentalforge' ); ?></h5>
							<?php ABPRF_Layout::info_text( 'abprf_tc' ); ?>
                        </div>
                        <div class="_panel_body_xs abp_active" data-collapse="#abprf_tc">
                            <form class="save_tc" method="post" action="">
								<?php $this->tc( $tcs ); ?>
                                <div class="_divider_xs"></div>
                                <button type="submit" class="_btn_theme_xs"><span class="_mar_r_xxs">💾</span><?php esc_html_e( 'Save Term & Conditions Configuration', 'abp-rentalforge' ); ?></button>
                            </form>
                        </div>
                    </div>
					<?php
				}
			}
			public function global_faq(): void {
				if ( ABPRF_Function::on_off( 'faq' ) ) {
					$faqs = ABPRF_Function::get_option( 'abprf_faqs' );
					?>
                    <div class="_abp_panel_xs faq_configuration _mar_b_xs">
                        <div class="_panel_head_xs _fd_column" data-collapse-target="#abprf_faq">
                            <h5 class="_abp"><span class="_mar_r_xxs">❓</span><?php esc_html_e( 'Global FAQ Configuration', 'abp-rentalforge' ); ?></h5>
							<?php ABPRF_Layout::info_text( 'abprf_faqs' ); ?>
                        </div>
                        <div class="_panel_body_xs abp_active" data-collapse="#abprf_faq">
                            <form class=" save_faq " method="post" action="">
								<?php $this->faq( $faqs ); ?>
                                <div class="_divider_xs"></div>
                                <button type="submit" class="_btn_theme_xs"><span class="_mar_r_xxs">💾</span><?php esc_html_e( 'Save FAQs Configuration', 'abp-rentalforge' ); ?></button>
                            </form>
                        </div>
                    </div>
					<?php
				}
			}
			public function tc( $tcs = '' ): void {
				$description = $tcs ? wp_kses_post( $tcs ) : '';
				$editor_id   = 'abprf_editor_tc_' . wp_rand( 0, 999 );
				?>
                <div class="edit_area">
                    <div class="_fd_column_mar_t_xs">
                        <span class="_fs_label_mar_b_xs"><?php esc_html_e( 'Term & Conditions Content', 'abp-rentalforge' ); ?></span>
						<?php
							wp_editor(
								$description,
								$editor_id,
								array(
									'textarea_name' => 'tc_content',
									'textarea_rows' => 12,
									'media_buttons' => true,
									'teeny' => false,
									'quicktags' => true
								)
							);
						?>
                    </div>
					<?php ABPRF_Layout::info_text( 'tc_item' ); ?>
                </div>
				<?php
			}
			public function faq( $faqs = [] ): void {
				?>
                <div class="configuration_content">
                    <div class="insertable_area sortable_area">
						<?php
							if ( ! empty( $faqs ) && sizeof( $faqs ) > 0 ) {
								foreach ( $faqs as $faq ) {
									$this->faq_item( $faq );
								}
							}
						?>
                    </div>
                    <div class="_divider_xs"></div>
					<?php ABPRF_Layout::button_add_xs( __( 'Add New FAQ Item', 'abp-rentalforge' ) ); ?>
                    <div class="abprf_d_none">
                        <div class="hidden_content">
							<?php $this->faq_item(); ?>
                        </div>
                    </div>
                </div>
				<?php
			}
			public function faq_item( $faq = [] ): void {
				$title       = $faq['title'] ?? __( 'NEW', 'abp-rentalforge' );
				$description = $faq['des'] ?? '';
				$description = $description ? html_entity_decode( $description ) : '';
				$editor_id   = 'abprf_editor_faq' . wp_rand( 0, 999 );
				?>
                <div class="delete_area faq_item _mar_b_xs <?php echo esc_attr( empty( $faq ) ? 'active' : '' ); ?>">
                    <div class="faq_question">
                        <h6 class="_abp edit_hook" data-paste="#faq_title"><?php echo esc_html( $title ); ?></h6>
						<?php ABPRF_Layout::button_delete_sort_edit(); ?>
                    </div>
                    <div class="edit_area">
                        <div class="faq_answer_content">
							<?php ABPRF_Layout::info_text( 'faq_item' ); ?>
                            <div class="_divider_xs"></div>
                            <label class="_f_equal_f_wrap">
                                <span class="_mar_r_xs"><?php esc_html_e( 'FAQ Title', 'abp-rentalforge' ); ?><sup class="_color_required">*</sup></span>
                                <input type="text" class="_form_control" name="faq_title[]" data-pass="#faq_title" placeholder="<?php esc_attr_e( 'EX: What is the check-in time?', 'abp-rentalforge' ); ?>" value="<?php echo esc_attr( $title ); ?>"/>
                            </label>
                            <divl class="_fd_column_mar_t_xs">
                                <span class="_fs_label_mar_b_xs"><?php esc_html_e( 'Description', 'abp-rentalforge' ); ?></span>
								<?php
									wp_editor(
										$description,
										$editor_id,
										array(
											'textarea_name' => 'faq_description[]',
											'textarea_rows' => 6,
											'media_buttons' => true,
											'teeny' => false,
											'quicktags' => true
										)
									);
								?>
                            </divl>
                        </div>
                    </div>
                </div>
				<?php
			}
			public function save_tc(): void {
				if ( ! check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce', false ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Invalid security token.', 'abp-rentalforge' ) ], 403 );
				}
				if ( ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Insufficient permissions.', 'abp-rentalforge' ) ], 403 );
				}
				$post_html = fn( $key, $default = '' ) => isset( $_POST[ $key ] ) ? wp_kses_post( wp_unslash( $_POST[ $key ] ) ) : $default;
				update_option( 'abprf_tc', $post_html( 'tc_content' ) );
				wp_send_json_success( [ 'msg' => __( 'Term & Conditions  Saved Successfully..... !! ', 'abp-rentalforge' ) ] );
			}
			public function get_faq_array( array $abprf_faqs = [] ): array {
				$has_post_nonce = isset( $_POST['abprf_post_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['abprf_post_nonce'] ) ), 'abprf_post_nonce' );
				$has_ajax_nonce = check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce', false );
				if ( ( $has_post_nonce || $has_ajax_nonce ) && current_user_can( 'manage_options' ) ) {
					$post_array      = fn( $key ) => ( isset( $_POST[ $key ] ) && is_array( $_POST[ $key ] ) ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST[ $key ] ) ) : [];
					$post_html_array = fn( $key ) => ( isset( $_POST[ $key ] ) && is_array( $_POST[ $key ] ) ) ? array_map( 'wp_kses_post', wp_unslash( $_POST[ $key ] ) ) : [];
					$titles          = $post_array( 'faq_title' );
					$descriptions    = $post_html_array( 'faq_description' );
					if ( ! empty( $titles ) ) {
						foreach ( $titles as $key => $title ) {
							if ( $title && ! empty( $descriptions[ $key ] ) ) {
								$abprf_faqs[ $key ] = [
									'title' => $title,
									'des' => $descriptions[ $key ],
								];
							}
						}
					}
				}

				return $abprf_faqs;
			}
			public function save_faqs(): void {
				if ( ! check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce', false ) ) {
					wp_send_json_error( [ 'msg' => __( 'Invalid security token.', 'abp-rentalforge' ) ], 403 );
				}
				if ( ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( [ 'msg' => __( 'Insufficient permissions.', 'abp-rentalforge' ) ], 403 );
				}
				$abprf_faqs = $this->get_faq_array();
				update_option( 'abprf_faqs', $abprf_faqs );
				wp_send_json_success( [ 'msg' => __( 'FAQs Configuration Saved Successfully..... !! ', 'abp-rentalforge' ) ] );
			}
			public function import_tc(): void {
				if ( ! check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce', false ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Invalid security token.', 'abp-rentalforge' ) ], 403 );
				}
				if ( ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Insufficient permissions.', 'abp-rentalforge' ) ], 403 );
				}
				$tcs = ABPRF_Function::get_option( 'abprf_tc', '' );
				ob_start();
				$this->tc( $tcs );
				$html_content = ob_get_clean();
				wp_send_json_success( [ 'html' => $html_content, 'msg' => __( 'Term & Conditions  Imported Successfully ..... !! ', 'abp-rentalforge' ) ] );
			}
			public function import_faq(): void {
				if ( ! check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce', false ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Invalid security token.', 'abp-rentalforge' ) ], 403 );
				}
				if ( ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Insufficient permissions.', 'abp-rentalforge' ) ], 403 );
				}
				$faqs = ABPRF_Function::get_option( 'abprf_faqs' );
				$faqs = is_array( $faqs ) ? $faqs : [];
				ob_start();
				$this->faq( $faqs );
				$html_content = ob_get_clean();
				wp_send_json_success( [ 'html' => $html_content, 'msg' => __( 'FAQ ImportedSuccessfully ..... !! ', 'abp-rentalforge' ) ] );
			}
		}
		new ABPRF_Resource();
	}