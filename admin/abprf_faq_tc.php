<?php
	if ( ! defined( 'ABSPATH' ) ) {
		die;
	} // Cannot access pages directly.
	if ( ! class_exists( 'ABPRF_FAQ' ) ) {
		class ABPRF_FAQ {
			public function __construct() {
				add_action( 'abprf_global_tc', array( $this, 'global_tc' ) );
				add_action( 'abprf_global_faq', array( $this, 'global_faq' ) );
				add_action( 'abprf_post_content', array( $this, 'post_faq' ) );
				add_action( 'abprf_post_content', array( $this, 'post_tc' ) );
				add_action( 'wp_ajax_abprf_save_faqs', array( $this, 'save_faqs' ) );
				add_action( 'wp_ajax_abprf_save_tc', array( $this, 'save_tc' ) );
				add_action( 'wp_ajax_abprf_import_faq', array( $this, 'import_faq' ) );
				add_action( 'wp_ajax_abprf_import_tc', array( $this, 'import_tc' ) );
				add_filter( 'abprf_get_faq_array', array( $this, 'get_faq_array' ) );
			}

			public function global_tc() {
				$tcs = ABPRF_Function::get_option( 'abprf_tc', '' );
				?>
                <div class="tab_item" data-tabs="#abprf_global_tc">
                    <form class=" save_tc " method="post" action="">
                        <h4 class="_abprf"><span class="_mar_r_xxs">🤝</span> <?php esc_html_e( 'Global Term & Conditions Configuration', 'abprf-rental-forge' ); ?></h4>
						<?php ABPRF_Layout::info_text( 'abprf_tc' ); ?>
                        <div class="_divider_xs"></div>
						<?php $this->tc( $tcs ); ?>
                        <div class="_divider_xs"></div>
                        <button type="submit" class="_btn_theme"><span class="_mar_r_xxs">💾</span><?php esc_html_e( 'Save Term & Conditions Configuration', 'abprf-rental-forge' ); ?></button>
                    </form>
                </div>
				<?php
			}

			public function global_faq() {
				$faqs = ABPRF_Function::get_option( 'abprf_faqs' );
				?>
                <div class="tab_item" data-tabs="#abprf_global_faq">
                    <form class=" save_faq " method="post" action="">
                        <h4 class="_abprf"><span class="_mar_r_xxs">❓</span> <?php esc_html_e( 'Global FAQ Configuration', 'abprf-rental-forge' ); ?></h4>
						<?php ABPRF_Layout::info_text( 'abprf_faqs' ); ?>
                        <div class="_divider_xs"></div>
						<?php $this->faq( $faqs ); ?>
                        <div class="_divider_xs"></div>
                        <button type="submit" class="_btn_theme"><span class="_mar_r_xxs">💾</span><?php esc_html_e( 'Save FAQs Configuration', 'abprf-rental-forge' ); ?></button>
                    </form>
                </div>
				<?php
			}

			public function post_tc( $abprf_infos ) {
				$post_id          = array_key_exists( 'post_id', $abprf_infos ) ? $abprf_infos['post_id'] : 0;
				$abprf_tc         = get_post_meta( $post_id, 'abprf_tc', true );
				$display          = array_key_exists( 'display_tc', $abprf_infos ) ? $abprf_infos['display_tc'] : 'on';
				$active_global_tc = array_key_exists( 'active_global_tc', $abprf_infos ) ? $abprf_infos['active_global_tc'] : 'on';
				?>
                <div class="tab_item tc_configuration" data-tabs="#abprf_tc">
                    <h4 class="_abprf_color_theme"><span class="_mar_r_xxs">🤝</span><?php esc_html_e( 'Term & Conditions', 'abprf-rental-forge' ); ?></h4>
                    <div class="_divider_xs"></div>
                    <div class="group_setting">
                        <div class="_setting_item">
                            <div class="_f_wrap_fj_between_fa_center">
                                <div class="_fa_center">
									<?php ABPRF_Layout::switch_checkbox( 'display_tc', $display ); ?>
                                    <span class="_fs_label_mar_l_xs"><?php esc_html_e( 'Active Term & Conditions ?', 'abprf-rental-forge' ); ?></span>
                                </div>
                            </div>
                            <div class="_divider_xs"></div>
							<?php ABPRF_Layout::info_text( 'display_tc' ); ?>
                        </div>
                        <div data-collapse="#display_faq" class="_setting_item <?php echo esc_attr( $display == 'on' ? 'rf_active' : '' ); ?>">
                            <div class="_fj_between">
                                <div class="_fa_center">
									<?php ABPRF_Layout::switch_checkbox( 'active_global_tc', $active_global_tc ); ?>
                                    <span class="_fs_label_mar_lr_xs"><?php esc_html_e( 'Use Global Term & Conditions ?', 'abprf-rental-forge' ); ?></span>
                                </div>
                                <div data-collapse="#active_global_tc" class=" <?php echo esc_attr( $active_global_tc == 'on' ? '' : 'rf_active' ); ?>">
                                    <button type="button" class="_btn_theme import_tc"><span class="fas fa-file-upload _mar_r_xs"></span><?php esc_html_e( 'Import Global Term & Conditions', 'abprf-rental-forge' ); ?></button>
                                </div>
                            </div>
                            <div class="_divider_xs"></div>
							<?php ABPRF_Layout::info_text( 'active_global_tc' ); ?>
                        </div>
                    </div>
                    <div class="<?php echo esc_attr( $active_global_tc == 'on' ? '' : 'rf_active' ); ?>" data-collapse="#active_global_tc">
                        <div class="tc_content">
							<?php $this->tc( $abprf_tc ); ?>
                        </div>
                    </div>
                </div>
				<?php
			}

			public function post_faq( $abprf_infos ) {
				$post_id           = array_key_exists( 'post_id', $abprf_infos ) ? $abprf_infos['post_id'] : 0;
				$display           = array_key_exists( 'display_faq', $abprf_infos ) ? $abprf_infos['display_faq'] : 'on';
				$active_global_faq = array_key_exists( 'active_global_faq', $abprf_infos ) ? $abprf_infos['active_global_faq'] : 'on';
				$faqs              = get_post_meta( $post_id, 'abprf_faqs', true );
				$faqs              = $faqs ?: [];
				?>
                <div class="tab_item faq_configuration" data-tabs="#abprf_faqs">
                    <h4 class="_abprf_color_theme"><span class="_mar_r_xxs">❓</span><?php esc_html_e( 'FAQs Configuration', 'abprf-rental-forge' ); ?></h4>
                    <div class="_divider_xs"></div>
                    <div class="group_setting">
                        <div class="_setting_item">
                            <div class="_f_wrap_fj_between_fa_center">
                                <div class="_fa_center">
									<?php ABPRF_Layout::switch_checkbox( 'display_faq', $display ); ?>
                                    <span class="_fs_label_mar_l_xs"><?php esc_html_e( 'Active FAQs ?', 'abprf-rental-forge' ); ?></span>
                                </div>
                            </div>
                            <div class="_divider_xs"></div>
							<?php ABPRF_Layout::info_text( 'display_faq' ); ?>
                        </div>
                        <div data-collapse="#display_faq" class="_setting_item <?php echo esc_attr( $display == 'on' ? 'rf_active' : '' ); ?>">
                            <div class="_fj_between">
                                <div class="_fa_center">
									<?php ABPRF_Layout::switch_checkbox( 'active_global_faq', $active_global_faq ); ?>
                                    <span class="_fs_label_mar_lr_xs"><?php esc_html_e( 'Use Global FAQ ?', 'abprf-rental-forge' ); ?></span>
                                </div>
                                <div data-collapse="#active_global_faq" class=" <?php echo esc_attr( $active_global_faq == 'on' ? '' : 'rf_active' ); ?>">
                                    <button type="button" class="_btn_theme import_faq"><span class="fas fa-file-upload _mar_r_xs"></span><?php esc_html_e( 'Import Global FAQ', 'abprf-rental-forge' ); ?></button>
                                </div>
                            </div>
                            <div class="_divider_xs"></div>
							<?php ABPRF_Layout::info_text( 'active_global_faq' ); ?>
                        </div>
                    </div>
                    <div class="<?php echo esc_attr( $active_global_faq == 'on' ? '' : 'rf_active' ); ?>" data-collapse="#active_global_faq">
                        <div class="faq_content">
							<?php $this->faq( $faqs ); ?>
                        </div>
                    </div>
                </div>
				<?php
			}

			public function save_tc() {
				if ( is_admin() && check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce' ) && current_user_can( 'manage_options' ) ) {
					$abprf_tc = isset( $_POST['tc_content'] ) ? wp_kses_post( wp_unslash( $_POST['tc_content'] ) ) : '';
					update_option( 'abprf_tc', $abprf_tc );
					wp_send_json_success( [ 'msg' => esc_html__( 'Term & Conditions  Saved Successfully..... !! ', 'abprf-rental-forge' ) ] );
				} else {
					wp_send_json_success( [ 'msg' => esc_html__( 'Term & Conditions  not Saved.... !! ', 'abprf-rental-forge' ) ] );
				}
				wp_die();
			}

			public function save_faqs() {
				if ( is_admin() && check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce' ) && current_user_can( 'manage_options' ) ) {
					$abprf_faqs = $this->get_faq_array();
					update_option( 'abprf_faqs', $abprf_faqs );
					wp_send_json_success( [ 'msg' => esc_html__( 'FAQs Configuration Saved Successfully..... !! ', 'abprf-rental-forge' ) ] );
				} else {
					wp_send_json_success( [ 'msg' => esc_html__( 'FAQs Configuration not Saved..... !! ', 'abprf-rental-forge' ) ] );
				}
				wp_die();
			}

			public function import_tc() {
				if ( is_admin() && check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce' ) && current_user_can( 'manage_options' ) ) {
					$tcs = ABPRF_Function::get_option( 'abprf_tc', '' );
					$this->tc( $tcs );
				}
				wp_die();
			}

			public function import_faq() {
				if ( is_admin() && check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce' ) && current_user_can( 'manage_options' ) ) {
					$faqs = ABPRF_Function::get_option( 'abprf_faqs' );
					$this->faq( $faqs );
				}
				wp_die();
			}

			public function get_faq_array() {
				$abprf_faqs = array();
				if ( is_admin() && ( ( isset( $_POST['abprf_post_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['abprf_post_nonce'] ) ), 'abprf_post_nonce' ) ) || check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce' ) ) ) {
					$titles      = isset( $_POST['faq_title'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['faq_title'] ) ) : [];
					$description = isset( $_POST['fag_description'] ) ? array_map( 'wp_kses_post', wp_unslash( $_POST['fag_description'] ) ) : [];
					if ( sizeof( $titles ) > 0 && sizeof( $description ) > 0 ) {
						foreach ( $titles as $key => $title ) {
							if ( $title && array_key_exists( $key, $description ) && $description[ $key ] ) {
								$abprf_faqs[ $key ]['title'] = $title;
								$abprf_faqs[ $key ]['des']   = $description[ $key ];
							}
						}
					}
				}

				return $abprf_faqs;
			}

			public function tc( $tcs = '' ) {
				$description = $tcs ? wp_kses_post( $tcs ) : '';
				$editor_id   = 'abprf_editor_tc_' . wp_rand( 0, 999 );
				?>
                <div class="_setting_item edit_area">
                    <div class="_fd_column_mar_t_xs">
                        <span class="_fs_label_mar_b_xs"><?php esc_html_e( 'Term & Conditions Content', 'abprf-rental-forge' ); ?></span>
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

			public function faq( $faqs ) {
				?>
                <div class="configuration_content">
                    <div class="insertable_area sortable_area">
						<?php
							if ( $faqs && is_array( $faqs ) && sizeof( $faqs ) > 0 ) {
								foreach ( $faqs as $faq ) {
									$this->faq_item( $faq );
								}
							}
						?>
                    </div>
                    <div class="_divider_xs"></div>
					<?php ABPRF_Layout::button_add( __( 'Add New FAQ Item', 'abprf-rental-forge' ) ); ?>
                    <div class="abprf_d_none">
                        <div class="hidden_content">
							<?php $this->faq_item(); ?>
                        </div>
                    </div>
                </div>
				<?php
			}

			public function faq_item( $faq = [] ) {
				$title       = array_key_exists( 'title', $faq ) ? $faq['title'] : __( 'NEW', 'abprf-rental-forge' );
				$description = array_key_exists( 'des', $faq ) ? $faq['des'] : '';
				$description = $description ? html_entity_decode( $description ) : '';
				$editor_id   = 'abprf_editor_faq' . wp_rand( 0, 999 );
				?>
                <div class="delete_area _abp_panel _mar_b_xs">
                    <div class="_panel_head _fj_between">
                        <h5 class="_abprf edit_hook" data-paste="#faq_title"><?php echo esc_html( $title ); ?></h5>
						<?php ABPRF_Layout::button_delete_sort_edit(); ?>
                    </div>
                    <div class="edit_area _panel_body">
						<?php ABPRF_Layout::info_text( 'faq_item' ); ?>
                        <div class="_divider_xs"></div>
                        <label class="_f_equal_f_wrap">
                            <span class="_mar_r_xs"><?php esc_html_e( 'FAQ Title', 'abprf-rental-forge' ); ?><sup class="_color_required">*</sup></span>
                            <input type="text" class="_form_control" name="faq_title[]" data-pass="#faq_title" placeholder="<?php esc_attr_e( 'EX: What is the check-in time?', 'abprf-rental-forge' ); ?>" value="<?php echo esc_attr( $title ); ?>"/>
                        </label>
                        <divl class="_fd_column_mar_t_xs">
                            <span class="_fs_label_mar_b_xs"><?php esc_html_e( 'Description', 'abprf-rental-forge' ); ?></span>
							<?php
								wp_editor(
									$description,
									$editor_id,
									array(
										'textarea_name' => 'fag_description[]',
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
				<?php
			}
		}
		new ABPRF_FAQ();
	}