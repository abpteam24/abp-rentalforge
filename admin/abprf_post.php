<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	if ( ! class_exists( 'ABPRF_Post' ) ) {
		class ABPRF_Post {
			public function __construct() {
				add_action( 'abprf_load_posts', array( $this, 'load_posts' ) );
				add_action( 'add_meta_boxes', [ $this, 'settings_meta' ] );
				add_action( 'save_post', array( $this, 'save_settings' ) );
				add_action( 'wp_ajax_abprf_post_permanent_remove', array( $this, 'post_permanent_remove' ) );
				add_action( 'wp_ajax_abprf_post_move_trash', array( $this, 'post_move_trash' ) );
				add_action( 'wp_ajax_abprf_post_restore', array( $this, 'post_restore' ) );
				add_action( 'wp_ajax_abprf_reload_post_list', array( $this, 'reload_post_list' ) );
			}

			public function load_posts( $abprf_info ): void {
				$brand_icon            = ABPRF_Function::icon();
				$total_posts           = isset( $abprf_info['total_post'] ) && $abprf_info['total_post'] ? $abprf_info['total_post'] : 0;
				$total_publish         = isset( $abprf_info['total_publish'] ) && $abprf_info['total_publish'] ? $abprf_info['total_publish'] : 0;
				$total_draft           = isset( $abprf_info['total_draft'] ) && $abprf_info['total_draft'] ? $abprf_info['total_draft'] : 0;
				$total_private         = isset( $abprf_info['total_private'] ) && $abprf_info['total_private'] ? $abprf_info['total_private'] : 0;
				$total_trash           = isset( $abprf_info['total_trash'] ) && $abprf_info['total_trash'] ? $abprf_info['total_trash'] : 0;
				$new_post_url          = isset( $abprf_info['new_post_url'] ) && $abprf_info['new_post_url'] ? $abprf_info['new_post_url'] : '';
				$rf_status             = filter_input( INPUT_GET, 'rf_status', FILTER_SANITIZE_SPECIAL_CHARS );
				$rf_status             = $rf_status ?? 'publish';
				$filter_args['status'] = $rf_status;
				?>
                <div class="abprf_posts _abp_panel">
                    <div class="_panel_head _fj_between_f_wrap">
                        <h4 class="_abprf_color_white"><?php ABPRF_Layout::image_icon( $brand_icon, '_mar_r_xxs' ); ?><?php esc_html_e( 'Post Lists', 'abp-rentalforge' ); ?></h4>
                        <div class="_group_content">
                            <input type="hidden" name="select_hidden_post_status" value="<?php echo esc_attr( $rf_status ); ?>"/>
                            <button type="button" class="_btn_white_xs <?php echo esc_attr( $rf_status == 'all' ? 'rf_active' : '' ); ?>" data-href="<?php echo esc_url( add_query_arg( 'rf_status', 'all' ) ); ?>"><?php esc_html_e( 'All', 'abp-rentalforge' ); ?> ( <?php echo esc_html( $total_posts ); ?> )</button>
                            <button type="button" class="_btn_white_xs <?php echo esc_attr( $rf_status == 'publish' ? 'rf_active' : '' ); ?>" data-href="<?php echo esc_url( add_query_arg( 'rf_status', 'publish' ) ); ?>"><?php esc_html_e( 'Published', 'abp-rentalforge' ); ?> ( <?php echo esc_html( $total_publish ); ?> )</button>
                            <button type="button" class="_btn_white_xs <?php echo esc_attr( $rf_status == 'private' ? 'rf_active' : '' ); ?>" data-href="<?php echo esc_url( add_query_arg( 'rf_status', 'private' ) ); ?>"><?php esc_html_e( 'Private', 'abp-rentalforge' ); ?> ( <?php echo esc_html( $total_private ); ?> )</button>
                            <button type="button" class="_btn_white_xs <?php echo esc_attr( $rf_status == 'draft' ? 'rf_active' : '' ); ?>" data-href="<?php echo esc_url( add_query_arg( 'rf_status', 'draft' ) ); ?>"><?php esc_html_e( 'Draft', 'abp-rentalforge' ); ?> ( <?php echo esc_html( $total_draft ); ?> )</button>
                            <button type="button" class="_btn_white_xs <?php echo esc_attr( $rf_status == 'trash' ? 'rf_active' : '' ); ?>" data-href="<?php echo esc_url( add_query_arg( 'rf_status', 'trash' ) ); ?>"><?php esc_html_e( 'Trash', 'abp-rentalforge' ); ?> ( <?php echo esc_html( $total_trash ); ?> )</button>
                        </div>
                        <a class="_btn_light_white_xs" href="<?php echo esc_url( $new_post_url ); ?>"><span class="_mar_r_xs">➕</span><?php esc_html_e( 'Add New Post', 'abp-rentalforge' ); ?></a>
                    </div>
                    <div class="_panel_body post_list">
						<?php $this->post_table( $filter_args ); ?>
                    </div>
                </div>
				<?php
			}

			public function settings_meta(): void {
				$label      = ABPRF_Function::label();
				$brand_icon = ABPRF_Function::icon();
				$label      = $label . ' ' . __( 'Configuration', 'abp-rentalforge' ) . get_the_title( get_the_id() );
				add_meta_box( 'abprf_configuration', '<span class="' . esc_attr( $brand_icon ?: '' ) . '"></span>' . esc_html( $label ), array( $this, 'settings' ), esc_attr( ABPRF_Function::get_cpt() ), 'normal', 'high' );
			}

			//=============================//
			public function post_table( $filter_args ): void {
				//echo '<pre>';print_r($filter_args);echo '</pre>';
				$status = $filter_args['status'] ?? '';
				if ( empty( $status ) || $status == 'all' ) {
					$status = [ 'publish', 'draft', 'private', 'trash' ];
				}
				$page_number               = array_key_exists( 'page_number', $filter_args ) && is_numeric( $filter_args['page_number'] ) ? (int) $filter_args['page_number'] : 1;
				$limit                     = array_key_exists( 'page_item', $filter_args ) && is_numeric( $filter_args['page_item'] ) ? (int) $filter_args['page_item'] : ABPRF_Function::get_option( 'abprf_per_page_item', 20 );
				$count                     = ( $page_number - 1 ) * $limit + 1;
				$offset                    = $count - 1;
				$cpt                       = ABPRF_Function::get_cpt();
				$filters['status']         = $status;
				$filters['posts_per_page'] = $limit;
				$filters['paged']          = $offset;
				$post_ids                  = ABPRF_Query::get_post_id( $filters );
				if ( ! empty( $post_ids ) && sizeof( $post_ids ) > 0 ) {
					$total_post   = sizeof( ABPRF_Query::get_post_id( [ 'status' => $status ] ) );
					$new_post_url = admin_url( 'post-new.php?post_type=' . $cpt );
					?>
                    <table class="_abprf">
                        <thead>
                        <tr>
                            <th class="_w_50"><?php esc_html_e( 'SI', 'abp-rentalforge' ); ?></th>
                            <th class="_w_100"><?php esc_html_e( 'Image', 'abp-rentalforge' ); ?></th>
                            <th><?php esc_html_e( 'Post', 'abp-rentalforge' ); ?></th>
                            <th><?php esc_html_e( 'Rent Rule', 'abp-rentalforge' ); ?></th>
                            <th class="_w_100"><?php esc_html_e( 'Property', 'abp-rentalforge' ); ?></th>
                            <th><?php esc_html_e( 'Shortcode', 'abp-rentalforge' ); ?></th>
                            <th class="_w_175"><?php esc_html_e( 'Actions', 'abp-rentalforge' ); ?></th>
                        </tr>
                        </thead>
                        <tbody>
						<?php
							foreach ( $post_ids as $post_id ) {
								$title              = get_the_title( $post_id );
								$edit_link          = get_edit_post_link( $post_id );
								$post_rent_continue = ABPRF_Function::get_post_info( $post_id, 'rent_continue', 'on' );
								$rent_rule          = ABPRF_Function::get_post_info( $post_id, 'rent_rule' );
								$post_status        = get_post_status( $post_id );
								$new_post_url       = add_query_arg( array( 'copy_post' => $post_id, '_abprf_nonce' => wp_create_nonce( 'abprf_copy_post_action' ), ), $new_post_url );
								?>
                                <tr>
                                    <th><?php echo esc_html( $count ); ?>.</th>
                                    <td><?php ABPRF_Layout::image( $post_id ); ?></td>
                                    <td>
										<?php if ( $post_status == 'trash' ) { ?>
                                            <h5 class="_abprf_color_theme"><?php echo esc_html( $title ); ?></h5>
										<?php } else { ?>
                                            <a href="<?php echo esc_url( $edit_link ); ?>" class="_abprf_fs_h5 _color_theme"><?php echo esc_html( $title ); ?></a>
										<?php } ?>
                                        <div class="_d_flex">
                                            <span class="_mar_r_xxs publish"><?php echo esc_html( __( 'ID : ', 'abp-rentalforge' ) . ' ' . $post_id ); ?></span>
                                            <span class="_mar_r_xxs <?php echo esc_attr( $post_rent_continue == 'on' ? 'publish' : 'trash' ); ?>"><?php echo esc_html( $post_rent_continue == 'on' ? __( 'Rent On', 'abp-rentalforge' ) : __( 'Rent Off', 'abp-rentalforge' ) ); ?></span>
                                            <span class="_mar_r_xxs <?php echo esc_attr( $post_status ); ?>"><?php echo esc_html( $post_status ); ?></span>
                                        </div>
                                    </td>
                                    <th><?php echo esc_html( ! empty( $rent_rule ) ? ABPRF_Layout::rent_rules( $rent_rule ) : '' ); ?></th>
                                    <th><?php echo esc_html( ABPRF_Query::get_property( [ 'post_id' => $post_id ], true ) ); ?></th>
                                    <th>
                                        <p class="_abprf"><code> [abprf-post post_id="<?php echo esc_attr( $post_id ); ?>"]</code></p>
                                        <p class="_abprf"><code> [abprf-property post_id="<?php echo esc_attr( $post_id ); ?>"]</code></p>
                                    </th>
                                    <th>
                                        <div class="_f_wrap">
                                            <button type="button" class="_btn_light_navy_blue _mar_r_xxs" data-href="<?php echo esc_url( $new_post_url ); ?>" data-blank="_blank" title="<?php echo esc_html__( 'Copy/Clone : ', 'abp-rentalforge' ) . ' ' . esc_html( $title ); ?>">🔁</button>
											<?php if ( $post_status == 'trash' ) { ?>
                                                <button type="button" class="_btn_light_success_mar_r_xxs post_restore" data-post_id="<?php echo esc_attr( $post_id ); ?>" title="<?php echo esc_html__( 'Restore : ', 'abp-rentalforge' ) . ' ' . esc_html( $title ); ?>">♻️</button>
                                                <button type="button" class="_btn_light_danger_xxs post_permanent_remove" data-post_id="<?php echo esc_attr( $post_id ); ?>" title="<?php echo esc_html__( 'Permanent Remove : ', 'abp-rentalforge' ) . ' ' . esc_html( $title ); ?>">❌</button>
											<?php } else { ?>
                                                <button type="button" class="_btn_light_yellow_mar_r_xxs" data-href="<?php echo esc_url( $edit_link ); ?>" data-blank="_blank" title="<?php echo esc_html__( 'Edit : ', 'abp-rentalforge' ) . ' ' . esc_html( $title ); ?>">✍️</button>
                                                <button type="button" class="_btn_light_theme_mar_r_xxs" data-href="<?php echo esc_url( get_permalink( $post_id ) ); ?>" data-blank="_blank" title="<?php echo esc_html__( 'View : ', 'abp-rentalforge' ) . ' ' . esc_html( $title ); ?>">👁️</button>
                                                <button type="button" class="_btn_light_danger_xxs post_move_trash" data-post_id="<?php echo esc_attr( $post_id ); ?>" title="<?php echo esc_html__( 'Move to Trash : ', 'abp-rentalforge' ) . ' ' . esc_html( $title ); ?>"><span class="fas fa-trash"></span></button>
											<?php } ?>
                                        </div>
                                    </th>
                                </tr>
								<?php
								$count ++;
							}
						?>
                        </tbody>
                    </table>
					<?php
					do_action( 'abprf_pagination', [ 'page_item' => $limit, 'page_number' => $page_number, 'total' => $total_post, 'style' => 'ajax' ] );
				} else {
					ABPRF_Layout::layout_warning_info( 'not_post_found' );
				}
			}

			public function settings(): void {
				$post_id      = get_the_id();
				$copy_post_id = isset( $_GET['copy_post'] ) ? absint( $_GET['copy_post'] ) : '';
				if ( ! empty( $copy_post_id ) && isset( $_GET['_abprf_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_abprf_nonce'] ) ), 'abprf_copy_post_action' ) && current_user_can( 'edit_post', $copy_post_id ) ) {
					?> <input type="hidden" name="abprf_copy_post" value="<?php echo esc_attr( $copy_post_id ); ?>"/><?php
					$abprf_infos['copy_post_id'] = $copy_post_id;
					$new_post_id                 = $copy_post_id;
				} else {
					$new_post_id = $post_id;
				}
				$abprf_infos = ABPRF_Function::get_all_meta( $new_post_id );
				wp_nonce_field( 'abprf_post_nonce', 'abprf_post_nonce' );
				?>
                <div class="abprf_area abprf_admin rf_post_config">
                    <input type="hidden" name="abprf_post_id" value="<?php echo esc_attr( $post_id ); ?>"/>
                    <div class="_abp_panel">
                        <div class="abprf_tabs tab_top">
                            <div class="_panel_head">
                                <ul class="_abprf tab_lists">
                                    <li data-tabs-target="#abprf_general"><span class="fas fa-rainbow"></span><?php esc_html_e( 'General', 'abp-rentalforge' ); ?></li>
                                    <li data-tabs-target="#abprf_equipment_price"><span>🏠</span><?php esc_html_e( 'Properties and Price', 'abp-rentalforge' ); ?></li>
                                    <li data-tabs-target="#abprf_dates"><span>🗓️</span><?php esc_html_e( 'Date', 'abp-rentalforge' ); ?></li>
                                    <li data-tabs-target="#abprf_additional_service"><span>💰</span><?php esc_html_e( 'Additional services', 'abp-rentalforge' ); ?></li>
                                    <li data-tabs-target="#abprf_client_form"><span>📋</span><?php esc_html_e( 'Client Form', 'abp-rentalforge' ); ?></li>
                                    <li data-tabs-target="#abprf_tc"><span>🤝</span><?php esc_html_e( 'Term & Conditions', 'abp-rentalforge' ); ?></li>
                                    <li data-tabs-target="#abprf_faqs"><span>❓</span><?php esc_html_e( 'FAQs', 'abp-rentalforge' ); ?></li>
									<?php do_action( 'abprf_post_tab_menu', $abprf_infos ); ?>
                                    <li data-tabs-target="#abprf_tax"><span>🧾</span><?php esc_html_e( 'Tax', 'abp-rentalforge' ); ?></li>
                                </ul>
                            </div>
                            <div class="tab_content _panel_body">
								<?php
									$this->general_configuration( $abprf_infos );
									$this->tax_configuration( $abprf_infos );
									do_action( 'abprf_post_content', $abprf_infos );
								?>
                            </div>
                        </div>
                    </div>
					<?php ABPRF_Layout::load_admin_globally(); ?>
                </div>
				<?php
			}

			public function general_configuration( $abprf_infos ): void {
				$rent_continue     = array_key_exists( 'rent_continue', $abprf_infos ) ? $abprf_infos['rent_continue'] : 'on';
				$abprf_template    = array_key_exists( 'abprf_template', $abprf_infos ) ? $abprf_infos['abprf_template'] : 'grid';
				$sub_title         = array_key_exists( 'sub_title', $abprf_infos ) ? $abprf_infos['sub_title'] : '';
				$display_sku       = array_key_exists( 'display_sku', $abprf_infos ) ? $abprf_infos['display_sku'] : 'off';
				$display_sub_title = array_key_exists( 'display_sub_title', $abprf_infos ) ? $abprf_infos['display_sub_title'] : 'off';
				$post_sku          = array_key_exists( 'post_sku', $abprf_infos ) ? $abprf_infos['post_sku'] : '';
				$category_label    = ABPRF_Function::category_label();
				$category          = array_key_exists( 'abprf_category', $abprf_infos ) ? $abprf_infos['abprf_category'] : '';
				$display_category  = array_key_exists( 'display_category', $abprf_infos ) ? $abprf_infos['display_category'] : 'on';
				$location          = array_key_exists( 'abprf_location', $abprf_infos ) ? $abprf_infos['abprf_location'] : '';
				$display_location  = array_key_exists( 'display_location', $abprf_infos ) ? $abprf_infos['display_location'] : 'on';
				?>
                <div class="tab_item" data-tabs="#abprf_general">
                    <h4 class="_abprf_color_theme"><?php esc_html_e( 'General Configuration', 'abp-rentalforge' ); ?></h4>
                    <div class="_divider_xs"></div>
                    <div class="group_setting">
                        <div class="setting_item">
                            <div class="_fa_center">
								<?php ABPRF_Layout::switch_checkbox( 'rent_continue', $rent_continue ); ?>
                                <span class="_fs_label_mar_lr_xs"><?php esc_html_e( 'Rent continue?', 'abp-rentalforge' ); ?></span>
                            </div>
                            <div class="_divider_xs"></div>
							<?php ABPRF_Layout::info_text( 'rent_continue' ); ?>
                        </div>
                        <div class="setting_item">
                            <label class="_f_equal_f_wrap">
                                <span class="_mar_r_xs"><?php esc_html_e( 'Template', 'abp-rentalforge' ); ?></span>
                                <select class="_form_control " name="abprf_template" data-collapse-target required>
                                    <option disabled selected><?php esc_html_e( 'Please Select', 'abp-rentalforge' ); ?></option>
                                    <option value="grid" <?php echo esc_attr( $abprf_template == 'grid' ? 'selected' : '' ); ?>><?php esc_html_e( 'Grid/List Template', 'abp-rentalforge' ); ?></option>
                                    <option value="group" <?php echo esc_attr( $abprf_template == 'group' ? 'selected' : '' ); ?>><?php esc_html_e( 'Group Template', 'abp-rentalforge' ); ?></option>
                                </select>
                            </label>
                            <div class="_divider_xs"></div>
							<?php ABPRF_Layout::info_text( 'abprf_template' ); ?>
                        </div>
                        <div class="setting_item">
                            <div class="_fj_between">
                                <div class="_fa_center">
									<?php ABPRF_Layout::switch_checkbox( 'display_sku', $display_sku ); ?>
                                    <span class="_fs_label_mar_lr_xs"><?php esc_html_e( 'SKU', 'abp-rentalforge' ); ?></span>
                                </div>
                                <label>
                                    <input class="_form_control" name="post_sku" value="<?php echo esc_attr( $post_sku ); ?>" placeholder="<?php esc_attr_e( 'Post SKU', 'abp-rentalforge' ); ?>"/>
                                </label>
                            </div>
                            <div class="_divider_xs"></div>
							<?php ABPRF_Layout::info_text( 'post_sku' ); ?>
                        </div>
                        <div class="setting_item">
                            <div class="_fj_between">
                                <div class="_fa_center">
									<?php ABPRF_Layout::switch_checkbox( 'display_sub_title', $display_sub_title ); ?>
                                    <span class="_fs_label_mar_lr_xs"><?php esc_html_e( 'Sub Title', 'abp-rentalforge' ); ?></span>
                                </div>
                                <div data-collapse="#display_sub_title" class=" <?php echo esc_attr( $display_sub_title == 'on' ? 'rf_active' : '' ); ?>">
                                    <label>
                                        <textarea class="_form_control" name="sub_title" placeholder="<?php esc_attr_e( 'Post Sub Title', 'abp-rentalforge' ); ?>"><?php echo esc_html( $sub_title ); ?></textarea>
                                    </label>
                                </div>
                            </div>
                            <div class="_divider_xs"></div>
							<?php ABPRF_Layout::info_text( 'sub_title' ); ?>
                        </div>
                        <div class="setting_item span_2">
                            <div class="_fj_between_fa_start">
                                <div class="_fa_center">
									<?php ABPRF_Layout::switch_checkbox( 'display_category', $display_category ); ?>
                                    <span class="_fs_label_mar_lr_xs"><?php echo esc_html( $category_label ); ?></span>
                                </div>
                                <div class="category_selection">
									<?php ABPRF_Category::category_selection( $category ); ?>
                                </div>
                            </div>
                            <div class="_divider_xs"></div>
							<?php ABPRF_Layout::info_text( 'display_category' ); ?>
                        </div>
                        <div class="setting_item span_2">
                            <div class="_fj_between">
                                <div class="_fa_center">
									<?php ABPRF_Layout::switch_checkbox( 'display_location', $display_location ); ?>
                                    <span class="_fs_label_mar_lr_xs"><?php esc_html_e( 'Location', 'abp-rentalforge' ); ?></span>
                                </div>
                                <div class="location_selection">
									<?php ABPRF_Location::location_selection( $location ); ?>
                                </div>
                            </div>
                            <div class="_divider_xs"></div>
							<?php ABPRF_Layout::info_text( 'display_location' ); ?>
                        </div>
                    </div>
                </div>
				<?php
			}

			public function tax_configuration( $abprf_infos ): void {
				$tax_status  = array_key_exists( '_tax_status', $abprf_infos ) ? $abprf_infos['_tax_status'] : '';
				$tax_classes = WC_Tax::get_tax_rate_classes();
				$tax_class   = array_key_exists( '_tax_class', $abprf_infos ) ? $abprf_infos['_tax_class'] : '';
				?>
                <div class="tab_item" data-tabs="#abprf_tax">
                    <h4 class="_abprf_color_theme"><span class="_mar_r_xs">🧾</span> <?php esc_html_e( 'Tax Configuration', 'abp-rentalforge' ); ?></h4>
                    <div class="_divider_xs"></div>
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
					<?php } else { ?>
						<?php ABPRF_Layout::layout_warning_info( 'enable_tax_msg' ); ?>
					<?php } ?>
                </div>
				<?php
			}

			//====================================//
			public function save_settings( $post_id ): void {
				if ( ! isset( $_POST['abprf_post_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['abprf_post_nonce'] ) ), 'abprf_post_nonce' ) ) {
					return;
				}
				if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
					return;
				}
				if ( ! current_user_can( 'edit_post', $post_id ) ) {
					return;
				}
				if ( get_post_type( $post_id ) == ABPRF_Function::get_cpt() ) {
					$meta_info                      = [];
					$meta_info['rent_continue']     = isset( $_POST['rent_continue'] ) ? sanitize_text_field( wp_unslash( $_POST['rent_continue'] ) ) : 'on';
					$meta_info['sub_title']         = isset( $_POST['sub_title'] ) ? sanitize_text_field( wp_unslash( $_POST['sub_title'] ) ) : '';
					$meta_info['display_sub_title'] = isset( $_POST['display_sub_title'] ) ? sanitize_text_field( wp_unslash( $_POST['display_sub_title'] ) ) : 'off';
					$meta_info['display_sku']       = isset( $_POST['display_sku'] ) ? sanitize_text_field( wp_unslash( $_POST['display_sku'] ) ) : 'off';
					$meta_info['post_sku']          = isset( $_POST['post_sku'] ) ? sanitize_text_field( wp_unslash( $_POST['post_sku'] ) ) : '';
					$meta_info['abprf_template']    = isset( $_POST['abprf_template'] ) ? sanitize_text_field( wp_unslash( $_POST['abprf_template'] ) ) : 'grid';
					$meta_info['display_category']  = isset( $_POST['display_category'] ) ? sanitize_text_field( wp_unslash( $_POST['display_category'] ) ) : 'off';
					$meta_info['abprf_category']    = isset( $_POST['abprf_category'] ) ? sanitize_text_field( wp_unslash( $_POST['abprf_category'] ) ) : '';
					$meta_info['display_location']  = isset( $_POST['display_location'] ) ? sanitize_text_field( wp_unslash( $_POST['display_location'] ) ) : 'off';
					$meta_info['abprf_location']    = isset( $_POST['abprf_location'] ) ? sanitize_text_field( wp_unslash( $_POST['abprf_location'] ) ) : '';
					//=============rent_rule================//
					$meta_info['rent_rule']      = isset( $_POST['rent_rule'] ) ? sanitize_text_field( wp_unslash( $_POST['rent_rule'] ) ) : 'hourly';
					$meta_info['day_time_start'] = isset( $_POST['day_time_start'] ) ? sanitize_text_field( wp_unslash( $_POST['day_time_start'] ) ) : '';
					$meta_info['day_time_end']   = isset( $_POST['day_time_end'] ) ? sanitize_text_field( wp_unslash( $_POST['day_time_end'] ) ) : '';
					$meta_info['hour_threshold'] = isset( $_POST['hour_threshold'] ) ? sanitize_text_field( wp_unslash( $_POST['hour_threshold'] ) ) : '24';
					$meta_info['cut_off_date']   = isset( $_POST['cut_off_date'] ) ? sanitize_text_field( wp_unslash( $_POST['cut_off_date'] ) ) : '10';
					$meta_info['day_threshold']  = isset( $_POST['day_threshold'] ) ? sanitize_text_field( wp_unslash( $_POST['day_threshold'] ) ) : '30';
					//=============date================//
					$meta_info['abprf_dates']         = apply_filters( 'abprf_get_date_array', [] );
					$meta_info['active_global_dates'] = isset( $_POST['active_global_dates'] ) ? sanitize_text_field( wp_unslash( $_POST['active_global_dates'] ) ) : 'on';
					//=============additional================//
					$meta_info['display_additional_services'] = isset( $_POST['display_additional_services'] ) ? sanitize_text_field( wp_unslash( $_POST['display_additional_services'] ) ) : 'off';
					$meta_info['active_global_additional']    = isset( $_POST['active_global_additional'] ) ? sanitize_text_field( wp_unslash( $_POST['active_global_additional'] ) ) : 'on';
					$additional_services                      = [];
					$additional_ids                           = isset( $_POST['additional_id'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['additional_id'] ) ) : [];
					$additional_icon                          = isset( $_POST['additional_icon'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['additional_icon'] ) ) : [];
					$additional_name                          = isset( $_POST['additional_name'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['additional_name'] ) ) : [];
					$additional_qty                           = isset( $_POST['additional_qty'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['additional_qty'] ) ) : [];
					$max_qty                                  = isset( $_POST['additional_max_qty'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['additional_max_qty'] ) ) : [];
					$returnable                               = isset( $_POST['additional_returnable'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['additional_returnable'] ) ) : [];
					$additional_price                         = isset( $_POST['additional_price'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['additional_price'] ) ) : [];
					$additional_description                   = isset( $_POST['additional_description'] ) ? array_map( 'sanitize_textarea_field', wp_unslash( $_POST['additional_description'] ) ) : [];
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
					$meta_info['additional_services'] = apply_filters( 'abprf_additional_services_filter', $additional_services );
					//=============form================//
					$meta_info['display_client_form'] = isset( $_POST['display_client_form'] ) ? sanitize_text_field( wp_unslash( $_POST['display_client_form'] ) ) : 'off';
					$meta_info['active_global_form']  = isset( $_POST['active_global_form'] ) ? sanitize_text_field( wp_unslash( $_POST['active_global_form'] ) ) : 'on';
					$form_infos                       = [];
					$form_title                       = isset( $_POST['client_form_title'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['client_form_title'] ) ) : [];
					$form_ids                         = isset( $_POST['client_form_id'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['client_form_id'] ) ) : [];
					$types                            = isset( $_POST['client_form_type'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['client_form_type'] ) ) : [];
					$option                           = isset( $_POST['client_form_option'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['client_form_option'] ) ) : [];
					$d_value                          = isset( $_POST['client_form_value'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['client_form_value'] ) ) : [];
					$date_value                       = isset( $_POST['client_form_value_date'] ) ? array_map( 'sanitize_textarea_field', wp_unslash( $_POST['client_form_value_date'] ) ) : [];
					$required                         = isset( $_POST['client_form_required'] ) ? array_map( 'sanitize_textarea_field', wp_unslash( $_POST['client_form_required'] ) ) : [];
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
					$meta_info['abprf_forms'] = $form_infos;
					//=============Faq =TC================//
					$meta_info['display_faq']       = isset( $_POST['display_faq'] ) ? sanitize_text_field( wp_unslash( $_POST['display_faq'] ) ) : 'on';
					$meta_info['active_global_faq'] = isset( $_POST['active_global_faq'] ) ? sanitize_text_field( wp_unslash( $_POST['active_global_faq'] ) ) : 'on';
					$abprf_faqs                     = [];
					$titles                         = isset( $_POST['faq_title'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['faq_title'] ) ) : [];
					$description                    = isset( $_POST['fag_description'] ) ? array_map( 'wp_kses_post', wp_unslash( $_POST['fag_description'] ) ) : [];
					if ( sizeof( $titles ) > 0 && sizeof( $description ) > 0 ) {
						foreach ( $titles as $key => $title ) {
							if ( $title && array_key_exists( $key, $description ) && $description[ $key ] ) {
								$abprf_faqs[ $key ]['title'] = $title;
								$abprf_faqs[ $key ]['des']   = $description[ $key ];
							}
						}
					}
					$meta_info['abprf_faqs']       = $abprf_faqs;
					$meta_info['display_tc']       = isset( $_POST['display_tc'] ) ? sanitize_text_field( wp_unslash( $_POST['display_tc'] ) ) : 'on';
					$meta_info['active_global_tc'] = isset( $_POST['active_global_tc'] ) ? sanitize_text_field( wp_unslash( $_POST['active_global_tc'] ) ) : 'on';
					$meta_info['abprf_tc']         = isset( $_POST['tc_content'] ) ? wp_kses_post( wp_unslash( $_POST['tc_content'] ) ) : '';
					//=============tax================//
					if ( get_option( 'woocommerce_calc_taxes' ) == 'yes' ) {
						$meta_info['_tax_status'] = isset( $_POST['_tax_status'] ) ? sanitize_text_field( wp_unslash( $_POST['_tax_status'] ) ) : 'none';
						$meta_info['_tax_class']  = isset( $_POST['_tax_class'] ) ? sanitize_text_field( wp_unslash( $_POST['_tax_class'] ) ) : '';
					}
					//=============================//
					$old_rent_continue = ABPRF_Function::get_post_info( $post_id, 'rent_continue', 'on' );
					//=============================//
					$meta_info = apply_filters( 'abprf_meta_info_update', $meta_info );
					if ( sizeof( $meta_info ) > 0 ) {
						foreach ( $meta_info as $key => $value ) {
							update_post_meta( $post_id, sanitize_key( $key ), $value );
						}
					}
					//=============================//
					if ( ! empty( get_the_title( $post_id ) ) ) {
						global $wpdb;
						$table_name   = $wpdb->prefix . 'abprf_property';
						$copy_post_id = isset( $_POST['abprf_copy_post'] ) ? sanitize_text_field( wp_unslash( $_POST['abprf_copy_post'] ) ) : '';
						if ( ! empty( $copy_post_id ) && current_user_can( 'edit_post', $copy_post_id ) ) {
							$copy_property_ids = isset( $_POST['copy_property_id'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['copy_property_id'] ) ) : [];
							if ( ! empty( $copy_property_ids ) && sizeof( $copy_property_ids ) > 0 ) {
								foreach ( $copy_property_ids as $property_id ) {
									//echo '<pre>';print_r($property_id);echo '</pre>';
									$properties = ABPRF_Query::get_property( [ 'property_id' => $property_id ] );
									if ( ! empty( $properties ) && is_array( $properties ) && sizeof( $properties ) > 0 ) {
										$property = current( $properties );
										$data     = [
											'post_id' => intval( $post_id ),
											'rent_continue' => array_key_exists( 'rent_continue', $property ) ? $property['rent_continue'] : '',
											'name' => array_key_exists( 'name', $property ) ? $property['name'] : '',
											'brand' => array_key_exists( 'brand', $property ) ? $property['brand'] : '',
											'category' => array_key_exists( 'category', $property ) ? $property['category'] : '',
											'location' => array_key_exists( 'location', $property ) ? $property['location'] : '',
											'features' => array_key_exists( 'features', $property ) ? $property['features'] : '',
											'rent_rule' => array_key_exists( 'rent_rule', $property ) ? $property['rent_rule'] : '',
											'price_qty_info' => array_key_exists( 'price_qty_info', $property ) ? $property['price_qty_info'] : '',
											'gallery' => array_key_exists( 'gallery', $property ) ? $property['gallery'] : '',
											'status' => get_post_status( $post_id ),
											'others' => array_key_exists( 'others', $property ) ? $property['others'] : '',
											'updated_at' => current_time( 'Y-m-d H:i' )
										];
										// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
										$wpdb->insert( $table_name, $data );
									}
								}
							}
						} else {
							if ( $old_rent_continue !== $meta_info['rent_continue'] ) {
								$data['rent_continue'] = $meta_info['rent_continue'];
							}
							$data['status']    = get_post_status( $post_id );
							$data['rent_rule'] = $meta_info['rent_rule'];
							$data['category']  = $meta_info['abprf_category'];
							$data['location']  = $meta_info['abprf_location'];
							$where             = [ 'post_id' => $post_id ];
							// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
							$wpdb->update( $table_name, $data, $where, [ '%s', '%s', '%s' ], [ '%d' ] );
						}
						//=============================//
						ABPRF_Function::update_dates( $post_id );
						ABPRF_Function::update_time_slot( $post_id );
						ABPRF_Function::update_global_data( $post_id );
					}
				}
			}

			public function post_permanent_remove(): void {
				if ( ! check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce', false ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Invalid security token.', 'abp-rentalforge' ) ], 403 );
				}
				if ( ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Insufficient permissions.', 'abp-rentalforge' ) ], 403 );
				}
				$post_id = isset( $_POST['post_id'] ) ? absint( wp_unslash( $_POST['post_id'] ) ) : 0;
				if ( $post_id > 0 ) {
					$link_wc_id = absint( ABPRF_Function::get_post_info( $post_id, 'link_wc_id' ) );
					if ( $link_wc_id > 0 ) {
						wp_delete_post( $link_wc_id, true );
					}
					wp_delete_post( $post_id, true );
					global $wpdb;
					$table_name = $wpdb->prefix . 'abprf_property';
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					$wpdb->delete( $table_name, [ 'post_id' => $post_id ], [ '%d' ] );
					wp_send_json_success( [ 'html' => '', 'msg' => __( 'Post permanently removed. ..... !! ', 'abp-rentalforge' ) ] );
				}
				wp_send_json_error( [ 'html' => '', 'msg' => __( 'Invalid Post ID ..... !! ', 'abp-rentalforge' ) ], 400 );
			}

			public function post_move_trash(): void {
				if ( ! check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce', false ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Invalid security token.', 'abp-rentalforge' ) ], 403 );
				}
				if ( ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Insufficient permissions.', 'abp-rentalforge' ) ], 403 );
				}
				$post_id = isset( $_POST['post_id'] ) ? absint( wp_unslash( $_POST['post_id'] ) ) : 0;
				if ( $post_id > 0 ) {
					$link_wc_id = absint( ABPRF_Function::get_post_info( $post_id, 'link_wc_id' ) );
					if ( $link_wc_id > 0 ) {
						wp_trash_post( $link_wc_id );
					}
					wp_trash_post( $post_id );
					global $wpdb;
					$table_name     = $wpdb->prefix . 'abprf_property';
					$current_status = get_post_status( $post_id ) ? get_post_status( $post_id ) : 'trash';
					$data           = [ 'status' => $current_status ];
					$where          = [ 'post_id' => $post_id ];
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					$wpdb->update( $table_name, $data, $where, [ '%s', '%s', '%s' ], [ '%d' ] );
					wp_send_json_success( [ 'html' => '', 'msg' => __( 'Post moved to trash successfully...... !! ', 'abp-rentalforge' ) ] );
				}
				wp_send_json_error( [ 'html' => '', 'msg' => __( 'Invalid Post ID ..... !! ', 'abp-rentalforge' ) ], 400 );
			}

			public function post_restore(): void {
				if ( ! check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce', false ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Invalid security token.', 'abp-rentalforge' ) ], 403 );
				}
				if ( ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Insufficient permissions.', 'abp-rentalforge' ) ], 403 );
				}
				$post_id = isset( $_POST['post_id'] ) ? absint( wp_unslash( $_POST['post_id'] ) ) : 0;
				if ( $post_id > 0 ) {
					$link_wc_id = absint( ABPRF_Function::get_post_info( $post_id, 'link_wc_id' ) );
					if ( $link_wc_id > 0 ) {
						wp_untrash_post( $link_wc_id );
					}
					wp_untrash_post( $post_id );
					$updated_post = [
						'ID' => $post_id,
						'post_status' => 'publish',
					];
					wp_update_post( $updated_post );
					global $wpdb;
					$table_name     = $wpdb->prefix . 'abprf_property';
					$current_status = get_post_status( $post_id ) ? get_post_status( $post_id ) : 'publish';
					$data           = [ 'status' => $current_status ];
					$where          = [ 'post_id' => $post_id ];
					// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
					$wpdb->update( $table_name, $data, $where, [ '%s', '%s', '%s' ], [ '%d' ] );
					wp_send_json_success( [ 'html' => '', 'msg' => __( 'Property restored successfully...... !! ', 'abp-rentalforge' ) ] );
				}
				wp_send_json_error( [ 'html' => '', 'msg' => __( 'Invalid Post ID ..... !! ', 'abp-rentalforge' ) ], 400 );
			}

			public function reload_post_list(): void {
				if ( ! check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce', false ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Invalid security token.', 'abp-rentalforge' ) ], 403 );
				}
				if ( ! current_user_can( 'manage_options' ) ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Insufficient permissions.', 'abp-rentalforge' ) ], 403 );
				}
				$filter_args = [];
				if ( isset( $_POST['filter_args'] ) && is_array( $_POST['filter_args'] ) ) {
					$filter_args = array_map( 'sanitize_text_field', wp_unslash( $_POST['filter_args'] ) );
				}
				ob_start();
				$this->post_table( $filter_args );
				$table_html = ob_get_clean();
				wp_send_json_success( [ 'html' => $table_html, 'msg' => __( 'Post List Loaded successfully...... !! ', 'abp-rentalforge' ) ] );
			}
		}
		new ABPRF_Post();
	}

