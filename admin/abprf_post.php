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
			public function load_posts( $abprf_info = [] ): void {
				$brand_icon    = ABPRF_Function::icon();
				$total_posts   = $abprf_info['total_post'] ?? 0;
				$total_publish = $abprf_info['total_publish'] ?? 0;
				$total_draft   = $abprf_info['total_draft'] ?? 0;
				$total_private = $abprf_info['total_private'] ?? 0;
				$total_trash   = $abprf_info['total_trash'] ?? 0;
				$status        = 'publish';
				if ( isset( $_GET['_abprf_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_abprf_nonce'] ) ), 'abprf_url_action' ) ) {
					$status = isset( $_GET['status'] ) ? sanitize_text_field( wp_unslash( $_GET['status'] ) ) : 'publish';
				}
				$status                = $status ?? 'publish';
				$filter_args['status'] = $status;
				?>
                <div class="abprf_posts _abp_panel">
                    <div class="_panel_head _fj_between_f_wrap">
                        <h4 class="_abprf_color_white"><?php ABPRF_Layout::image_icon( $brand_icon ); ?><?php esc_html_e( 'Post Lists', 'abp-rentalforge' ); ?></h4>
                        <div class="_group_content">
                            <input type="hidden" name="select_hidden_post_status" value="<?php echo esc_attr( $status ); ?>"/>
                            <button type="button" class="_btn_white_xs <?php echo esc_attr( $status == 'all' ? 'abp_active' : '' ); ?>" data-href="<?php echo esc_url( ABPRF_Function::build_url( 'posts', [ 'status' => 'all' ] ) ); ?>"><?php esc_html_e( 'All', 'abp-rentalforge' ); ?> ( <?php echo esc_html( $total_posts ); ?> )</button>
                            <button type="button" class="_btn_white_xs <?php echo esc_attr( $status == 'publish' ? 'abp_active' : '' ); ?>" data-href="<?php echo esc_url( ABPRF_Function::build_url( 'posts', [ 'status' => 'publish' ] ) ); ?>"><?php esc_html_e( 'Published', 'abp-rentalforge' ); ?> ( <?php echo esc_html( $total_publish ); ?> )</button>
                            <button type="button" class="_btn_white_xs <?php echo esc_attr( $status == 'private' ? 'abp_active' : '' ); ?>" data-href="<?php echo esc_url( ABPRF_Function::build_url( 'posts', [ 'status' => 'private' ] ) ); ?>"><?php esc_html_e( 'Private', 'abp-rentalforge' ); ?> ( <?php echo esc_html( $total_private ); ?> )</button>
                            <button type="button" class="_btn_white_xs <?php echo esc_attr( $status == 'draft' ? 'abp_active' : '' ); ?>" data-href="<?php echo esc_url( ABPRF_Function::build_url( 'posts', [ 'status' => 'draft' ] ) ); ?>"><?php esc_html_e( 'Draft', 'abp-rentalforge' ); ?> ( <?php echo esc_html( $total_draft ); ?> )</button>
                            <button type="button" class="_btn_white_xs <?php echo esc_attr( $status == 'trash' ? 'abp_active' : '' ); ?>" data-href="<?php echo esc_url( ABPRF_Function::build_url( 'posts', [ 'status' => 'trash' ] ) ); ?>"><?php esc_html_e( 'Trash', 'abp-rentalforge' ); ?> ( <?php echo esc_html( $total_trash ); ?> )</button>
                        </div>
                        <a class="_btn_light_white_xs" href="<?php echo esc_url( admin_url( 'post-new.php?post_type=' . ABPRF_Function::get_cpt() ) ); ?>"><span class="_mar_r_xs">➕</span><?php esc_html_e( 'Add New Post', 'abp-rentalforge' ); ?></a>
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
				$page_number               = absint( $filter_args['page_number'] ?? 1 ) ?: 1;
				$limit                     = absint( ( $filter_args['page_item'] ?? 0 ) ?: ABPRF_Function::get_option( 'abprf_per_page_item', 20 ) );
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
                                            <h5 class="_abprf_color_warning"><?php
													if ( ABPRF_Function::on_off( 'post_icon' ) ) {
														ABPRF_Layout::image_icon( ABPRF_Function::get_post_info( $post_id, 'post_icon' ) );
													}
													echo esc_html( $title );
												?></h5>
										<?php } else { ?>
                                            <a href="<?php echo esc_url( $edit_link ); ?>" class="_abprf_fs_h5 _color_theme">
												<?php if ( ABPRF_Function::on_off( 'post_icon' ) ) {
													ABPRF_Layout::image_icon( ABPRF_Function::get_post_info( $post_id, 'post_icon' ) );
												}
													echo esc_html( $title ); ?>
                                            </a>
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
					?>
                    <input type="hidden" name="abprf_copy_post" value="<?php echo esc_attr( $copy_post_id ); ?>"/>
					<?php
					$abprf_infos['copy_post_id'] = $copy_post_id;
					$new_post_id                 = $copy_post_id;
				} else {
					$new_post_id = $post_id;
				}
				$abprf_infos = ABPRF_Function::get_all_meta( $new_post_id );
				wp_nonce_field( 'abprf_post_nonce', 'abprf_post_nonce' );
				?>
                <div class="abprf_area abprf_admin abp_post_config">
                    <input type="hidden" name="abprf_post_id" value="<?php echo esc_attr( $post_id ); ?>"/>
                    <div class="_abp_panel">
                        <div class="abprf_tabs tab_top">
                            <div class="_panel_head">
                                <ul class="_abprf tab_lists">
                                    <li data-tabs-target="#abprf_general"><span class="fas fa-rainbow"></span><?php esc_html_e( 'General', 'abp-rentalforge' ); ?></li>
                                    <li data-tabs-target="#abprf_equipment_price"><span>🏠</span><?php esc_html_e( 'Properties and Price', 'abp-rentalforge' ); ?></li>
                                    <li data-tabs-target="#abprf_dates"><span>🗓️</span><?php esc_html_e( 'Date', 'abp-rentalforge' ); ?></li>
									<?php if ( ABPRF_Function::on_off( 'additional_info' ) ) { ?>
                                        <li data-tabs-target="#abprf_additional_service"><span>💰</span><?php esc_html_e( 'Additional services', 'abp-rentalforge' ); ?></li>
									<?php } ?>
									<?php if ( ABPRF_Function::on_off( 'client_info' ) ) { ?>
                                        <li data-tabs-target="#abprf_client_form"><span>📋</span><?php esc_html_e( 'Client Form', 'abp-rentalforge' ); ?></li>
									<?php } ?>
									<?php do_action( 'abprf_post_tab_menu', $abprf_infos ); ?>
                                        <li data-tabs-target="#abprf_resource"><span>📚</span><?php esc_html_e( 'Resources', 'abp-rentalforge' ); ?></li>
                                </ul>
                            </div>
                            <div class="tab_content _panel_body">
								<?php
									$this->general_configuration( $abprf_infos );
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
				$abprf_template    = $abprf_infos['abprf_template'] ?? 'grid';
				$display_sub_title = $abprf_infos['display_sub_title'] ?? 'off';
				?>
                <div class="tab_item" data-tabs="#abprf_general">
                    <h4 class="_abprf_color_theme"><?php esc_html_e( 'General Configuration', 'abp-rentalforge' ); ?></h4>
                    <div class="_divider_xs"></div>
                    <div class="group_setting">
                        <div class="setting_item">
                            <div class="_fa_center">
								<?php ABPRF_Layout::switch_checkbox( 'rent_continue', ( $abprf_infos['rent_continue'] ?? 'on' ) ); ?>
                                <span class="_abp_label"><?php esc_html_e( 'Rent continue?', 'abp-rentalforge' ); ?></span>
                            </div>
                            <div class="_divider_xs"></div>
							<?php ABPRF_Layout::info_text( 'rent_continue' ); ?>
                        </div>
                        <div class="setting_item">
                            <label class="_f_equal_f_wrap">
                                <span class="_abp_label"><?php esc_html_e( 'Template', 'abp-rentalforge' ); ?></span>
                                <select class="_form_control " name="abprf_template" data-collapse-target required>
                                    <option disabled selected><?php esc_html_e( 'Please Select', 'abp-rentalforge' ); ?></option>
                                    <option value="grid" <?php echo esc_attr( $abprf_template == 'grid' ? 'selected' : '' ); ?>><?php esc_html_e( 'Grid/List Template', 'abp-rentalforge' ); ?></option>
                                    <option value="group" <?php echo esc_attr( $abprf_template == 'group' ? 'selected' : '' ); ?>><?php esc_html_e( 'Group Template', 'abp-rentalforge' ); ?></option>
                                </select>
                            </label>
                            <div class="_divider_xs"></div>
							<?php ABPRF_Layout::info_text( 'abprf_template' ); ?>
                        </div>
						<?php if ( ABPRF_Function::on_off( 'sku' ) ) { ?>
                            <div class="setting_item">
                                <div class="_fj_between">
                                    <div class="_fa_center">
										<?php ABPRF_Layout::switch_checkbox( 'display_sku', ( $abprf_infos['display_sku'] ?? 'off' ) ); ?>
                                        <span class="_abp_label"><?php esc_html_e( 'SKU', 'abp-rentalforge' ); ?></span>
                                    </div>
                                    <label>
                                        <input class="_form_control" name="post_sku" value="<?php echo esc_attr( $abprf_infos['post_sku'] ?? '' ); ?>" placeholder="<?php esc_attr_e( 'Post SKU', 'abp-rentalforge' ); ?>"/>
                                    </label>
                                </div>
                                <div class="_divider_xs"></div>
								<?php ABPRF_Layout::info_text( 'post_sku' ); ?>
                            </div>
						<?php } ?>
						<?php if ( ABPRF_Function::on_off( 'sub_title' ) ) { ?>
                            <div class="setting_item">
                                <div class="_fj_between">
                                    <div class="_fa_center">
										<?php ABPRF_Layout::switch_checkbox( 'display_sub_title', $display_sub_title ); ?>
                                        <span class="_abp_label"><?php esc_html_e( 'Sub Title', 'abp-rentalforge' ); ?></span>
                                    </div>
                                    <div data-collapse="#display_sub_title" class=" <?php echo esc_attr( $display_sub_title == 'on' ? 'abp_active' : '' ); ?>">
                                        <label>
                                            <textarea class="_form_control" name="sub_title" placeholder="<?php esc_attr_e( 'Post Sub Title', 'abp-rentalforge' ); ?>"><?php echo esc_html( $abprf_infos['sub_title'] ?? '' ); ?></textarea>
                                        </label>
                                    </div>
                                </div>
                                <div class="_divider_xs"></div>
								<?php ABPRF_Layout::info_text( 'sub_title' ); ?>
                            </div>
						<?php } ?>
						<?php if ( ABPRF_Function::on_off( 'post_icon' ) ) { ?>
                            <div class="setting_item">
                                <divl class="_fj_between">
                                    <span class="_abp_label"><?php esc_html_e( 'Post Icon', 'abp-rentalforge' ); ?></span>
									<?php do_action( 'abprf_add_icon', 'post_icon', ( $abprf_infos['post_icon'] ?? '' ) ); ?>
                                </divl>
                                <div class="_divider_xs"></div>
								<?php ABPRF_Layout::info_text( 'post_icon' ); ?>
                            </div>
						<?php } ?>
						<?php if ( ABPRF_Function::on_off( 'post_des' ) ) { ?>
                            <div class="setting_item">
                                <label class="_f_equal_f_wrap">
                                    <span class="_abp_label"><?php esc_html_e( 'Post Short Description', 'abp-rentalforge' ); ?></span>
                                    <textarea class="_form_control" name="post_description" placeholder="<?php esc_attr_e( 'EX: Description', 'abp-rentalforge' ); ?>"><?php echo esc_html( $abprf_infos['post_description'] ?? '' ); ?></textarea>
                                </label>
                                <div class="_divider_xs"></div>
								<?php ABPRF_Layout::info_text( 'post_description' ); ?>
                            </div>
						<?php } ?>
						<?php if ( ABPRF_Function::on_off( 'related' ) ) { ?>
                            <div class="setting_item full_width related_item">
                                <span class="_abp_label"><?php esc_html_e( 'Related Post', 'abp-rentalforge' ); ?></span>
								<?php ABPRF_Layout::info_text( 'related_item' ); ?>
                                <div class="_divider_xs"></div>
                                <div class="_d_flex">
                                    <div class="selection_area">
                                        <label>
                                            <input class="_form_control item_search" type="text" placeholder="<?php esc_attr_e( 'Search Related Post ....', 'abp-rentalforge' ); ?>"/>
                                        </label>
                                        <div class="selection_list"></div>
                                    </div>
                                    <div class="selected_area">
                                        <input type="hidden" name="related_item" value="<?php echo esc_attr( $abprf_infos['related_item'] ?? '' ); ?>"/>
                                        <div class="selected_list"></div>
                                    </div>
                                </div>
                            </div>
						<?php } ?>
						<?php if ( ABPRF_Function::on_off( 'category' ) ) { ?>
                            <div class="setting_item full_width">
                                <div class="_fj_between_fa_start">
                                    <div class="_fa_center">
										<?php ABPRF_Layout::switch_checkbox( 'display_category', ( $abprf_infos['display_category'] ?? 'on' ) ); ?>
                                        <span class="_abp_label"><?php echo esc_html( ABPRF_Function::category_label() ); ?></span>
                                    </div>
                                    <div class="category_selection">
										<?php ABPRF_Category::category_selection( $abprf_infos['abprf_category'] ?? '' ); ?>
                                    </div>
                                </div>
                                <div class="_divider_xs"></div>
								<?php ABPRF_Layout::info_text( 'display_category' ); ?>
                            </div>
						<?php } ?>
						<?php if ( ABPRF_Function::on_off( 'location' ) ) { ?>
                            <div class="setting_item full_width">
                                <div class="_fj_between">
                                    <div class="_fa_center">
										<?php ABPRF_Layout::switch_checkbox( 'display_location', ( $abprf_infos['display_location'] ?? 'on' ) ); ?>
                                        <span class="_abp_label"><?php echo esc_html( ABPRF_Function::location_label() ); ?></span>
                                    </div>
                                    <div class="location_selection">
										<?php ABPRF_Location::location_selection( $abprf_infos['abprf_location'] ?? '' ); ?>
                                    </div>
                                </div>
                                <div class="_divider_xs"></div>
								<?php ABPRF_Layout::info_text( 'display_location' ); ?>
                            </div>
						<?php } ?>
                    </div>
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
					//$post_int            = fn( $key, $default = 0 ) => isset( $_POST[ $key ] ) ? absint( $_POST[ $key ] ) : $default;
					$post_val       = fn( $key, $default = '' ) => isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : $default;
					$post_textarea  = fn( $key, $default = '' ) => isset( $_POST[ $key ] ) ? sanitize_textarea_field( wp_unslash( $_POST[ $key ] ) ) : $default;
					$post_html      = fn( $key, $default = '' ) => isset( $_POST[ $key ] ) ? wp_kses_post( wp_unslash( $_POST[ $key ] ) ) : $default;
					$post_int_array = fn( $key ) => ( isset( $_POST[ $key ] ) && is_array( $_POST[ $key ] ) ) ? array_map( 'absint', wp_unslash( $_POST[ $key ] ) ) : [];
					//$post_array          = fn( $key ) => ( isset( $_POST[ $key ] ) && is_array( $_POST[ $key ] ) ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST[ $key ] ) ) : [];
					//$post_textarea_array = fn( $key ) => ( isset( $_POST[ $key ] ) && is_array( $_POST[ $key ] ) ) ? array_map( 'sanitize_textarea_field', wp_unslash( $_POST[ $key ] ) ) : [];
					//$post_html_array     = fn( $key ) => ( isset( $_POST[ $key ] ) && is_array( $_POST[ $key ] ) ) ? array_map( 'wp_kses_post', wp_unslash( $_POST[ $key ] ) ) : [];
					//$format_date         = fn( $date ) => $date ? gmdate( 'Y-m-d', strtotime( $date ) ) : '';
					$meta_info = [
						'rent_continue' => $post_val( 'rent_continue', 'on' ),
						'display_sub_title' => $post_val( 'display_sub_title', 'off' ),
						'sub_title' => $post_textarea( 'sub_title' ),
						'display_sku' => $post_val( 'display_sku', 'off' ),
						'post_sku' => $post_val( 'post_sku' ),
						'post_icon' => $post_val( 'post_icon' ),
						'post_description' => $post_textarea( 'post_description' ),
						'abprf_template' => $post_val( 'abprf_template', 'grid' ),
						'related_item' => $post_val( 'related_item' ),
						'display_category' => $post_val( 'display_category', 'off' ),
						'abprf_category' => $post_val( 'abprf_category' ),
						'display_location' => $post_val( 'display_location', 'off' ),
						'abprf_location' => $post_val( 'abprf_location' ),
						'rent_rule' => $post_val( 'rent_rule', 'hourly' ),
						'day_time_start' => $post_val( 'day_time_start' ),
						'day_time_end' => $post_val( 'day_time_end' ),
						'hour_threshold' => $post_val( 'hour_threshold', '24' ),
						'cut_off_date' => $post_val( 'cut_off_date', '10' ),
						'day_threshold' => $post_val( 'day_threshold', '30' ),
						'active_global_dates' => $post_val( 'active_global_dates', 'on' ),
						'abprf_dates' => apply_filters( 'abprf_get_date_array', [] ),
						'display_additional_services' => $post_val( 'display_additional_services', 'off' ),
						'active_global_additional' => $post_val( 'active_global_additional', 'on' ),
						'additional_services' => apply_filters( 'abprf_get_additional_array', [] ),
						'display_client_form' => $post_val( 'display_client_form', 'off' ),
						'active_global_form' => $post_val( 'active_global_form', 'on' ),
						'abprf_forms' => apply_filters( 'abprf_get_form_array', [] ),
						'display_faq' => $post_val( 'display_faq', 'on' ),
						'active_global_faq' => $post_val( 'active_global_faq', 'on' ),
						'abprf_faqs' => apply_filters( 'abprf_get_faq_array', [] ),
						'display_tc' => $post_val( 'display_tc', 'on' ),
						'active_global_tc' => $post_val( 'active_global_tc', 'on' ),
						'abprf_tc' => $post_html( 'tc_content' ),
					];
					//=============tax================//
					if ( get_option( 'woocommerce_calc_taxes' ) == 'yes' ) {
						$meta_info['_tax_status'] = $post_val( '_tax_status', 'none' );
						$meta_info['_tax_class']  = $post_val( '_tax_class' );
					}
					//=============================//
					$old_rent_continue = ABPRF_Function::get_post_info( $post_id, 'rent_continue', 'on' );
					//=============================//
					$meta_info = apply_filters( 'abprf_meta_info_update', $meta_info, $post_id );
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
							$copy_property_ids = $post_int_array( 'copy_property_id' );
							if ( ! empty( $copy_property_ids ) && sizeof( $copy_property_ids ) > 0 ) {
								foreach ( $copy_property_ids as $property_id ) {
									$properties = ABPRF_Query::get_property( [ 'property_id' => $property_id ] );
									if ( ! empty( $properties ) && is_array( $properties ) && sizeof( $properties ) > 0 ) {
										$property = current( $properties );
										$data     = [
											'post_id' => intval( $post_id ),
											'rent_continue' => $property['rent_continue'] ?? '',
											'name' => $property['name'] ?? '',
											'brand' => $property['brand'] ?? '',
											'category' => $property['category'] ?? '',
											'location' => $property['location'] ?? '',
											'features' => $property['features'] ?? '',
											'rent_rule' => $property['rent_rule'] ?? '',
											'price_qty_info' => $property['price_qty_info'] ?? '',
											'gallery' => $property['gallery'] ?? '',
											'status' => get_post_status( $post_id ),
											'others' => $property['others'] ?? '',
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
				if ( $post_id <= 0 ) {
					wp_send_json_error( [ 'html' => '', 'msg' => __( 'Invalid Post ID ..... !! ', 'abp-rentalforge' ) ], 400 );
				}
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
				$post_array  = fn( $key ) => ( isset( $_POST[ $key ] ) && is_array( $_POST[ $key ] ) ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST[ $key ] ) ) : [];
				$filter_args = $post_array( 'filter_args' );
				ob_start();
				$this->post_table( $filter_args );
				$table_html = ob_get_clean();
				wp_send_json_success( [
					'html' => $table_html,
					'msg' => __( 'Post List Loaded successfully...... !! ', 'abp-rentalforge' )
				] );
			}
		}
		new ABPRF_Post();
	}

