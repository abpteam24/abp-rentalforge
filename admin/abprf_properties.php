<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	if ( ! class_exists( 'ABPRF_Properties' ) ) {
		class ABPRF_Properties {
			public function __construct() {
				add_action( 'abprf_load_properties', array( $this, 'load_properties' ) );
				add_action( 'abprf_post_content', [ $this, 'tab_content' ] );
				add_action( 'wp_ajax_abprf_save_property', array( $this, 'save_property' ) );
				add_action( 'wp_ajax_abprf_property_add_edit', array( $this, 'property_add_edit' ) );
				add_action( 'wp_ajax_abprf_reload_property_list', array( $this, 'reload_property_list' ) );
			}

			public function load_properties( $abprf_info ): void {
				//$total_property = isset( $abprf_info['total_property'] ) && $abprf_info['total_property'] ? $abprf_info['total_property'] : 0;
				$post_ids               = isset( $abprf_info['post_ids'] ) && $abprf_info['post_ids'] ? $abprf_info['post_ids'] : [];
				$filter_args['post_id'] = 'all';
				?>
                <div class="abprf_properties">
                    <div class="_fj_between_f_wrap">
                        <h4 class="_abprf_color_theme"><span class="_mar_r_xxs">🏠</span> <?php esc_html_e( 'Properties', 'abprf-rental-forge' ); ?></h4>
                        <div class="dropdown_area _max_400">
                            <label class="_abprf_all_center">
                                <input type="hidden" name="select_property_hidden" value=""/>
                                <input type="text" class="_form_control_text_center validation_name" name="select_property" placeholder="<?php esc_attr_e( 'Search  Category/Post', 'abprf-rental-forge' ); ?>" value=""/>
                            </label>
                            <ul class="_abprf dropdown_input">
                                <li data-value="all"><span data-text><?php esc_html_e( 'All Category/Post', 'abprf-rental-forge' ); ?></span></li>
                                <li data-value="on"><span data-text><?php esc_html_e( 'Rent Active', 'abprf-rental-forge' ); ?></span></li>
                                <li data-value="off"><span data-text><?php esc_html_e( 'Rent De-active', 'abprf-rental-forge' ); ?></span></li>
								<?php if ( ! empty( $post_ids ) && is_array( $post_ids ) && sizeof( $post_ids ) > 0 ) { ?>
									<?php foreach ( $post_ids as $post_id ) { ?>
                                        <li data-value="<?php echo esc_attr( $post_id ); ?>"><span data-text><?php echo esc_html( get_the_title( $post_id ) ); ?></span></li>
									<?php } ?>
								<?php } ?>
                            </ul>
                        </div>
                        <button type="button" class="_btn_default" data-property_id="" data-target-popup="#abprf_property_popup"><span class="_mar_r_xs">➕</span><?php esc_html_e( 'Add New Property', 'abprf-rental-forge' ); ?></button>
                    </div>
                    <div class="_divider_xs"></div>
                </div>
                <div class="_section_xs properties_list">
					<?php $this->properties_table( $filter_args ); ?>
                </div>
				<?php
			}

			public function tab_content( $abprf_infos ): void {
				$copy_post_id                = array_key_exists( 'copy_post_id', $abprf_infos ) ? $abprf_infos['copy_post_id'] : '';
				$filter_args['copy_post_id'] = $copy_post_id;
				$filter_args['post_id']      = array_key_exists( 'post_id', $abprf_infos ) ? $abprf_infos['post_id'] : '';
				?>
                <div class="tab_item abprf_equipment_price" data-tabs="#abprf_equipment_price">
                    <h4 class="_abprf_color_theme"><span class="_mar_r_xxs">🏠</span> <?php esc_html_e( 'Properties and Price Configuration', 'abprf-rental-forge' ); ?></h4>
                    <div class="_divider_xs"></div>
                    <div class="properties_list">
						<?php $this->properties_table( $filter_args ); ?>
                    </div>
					<?php if ( empty( $copy_post_id ) ) { ?>
                        <div class="_divider_xs"></div>
                        <button type="button" class="_btn_default" data-property_id="" data-target-popup="#abprf_property_popup"><span class="_mar_r_xs">➕</span><?php esc_html_e( 'Add New Property', 'abprf-rental-forge' ); ?></button>
					<?php } ?>
                </div>
				<?php
			}

			public function save_property() {
				if ( is_admin() && check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce' ) && current_user_can( 'manage_options' ) ) {
					$post_id     = isset( $_POST['post_id'] ) ? sanitize_text_field( wp_unslash( $_POST['post_id'] ) ) : '';
					$property_id = isset( $_POST['property_id'] ) ? sanitize_text_field( wp_unslash( $_POST['property_id'] ) ) : '';
					$name        = isset( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
					$qty         = isset( $_POST['qty'] ) ? sanitize_text_field( wp_unslash( $_POST['qty'] ) ) : '';
					$price_rule  = isset( $_POST['price_rule'] ) ? sanitize_text_field( wp_unslash( $_POST['price_rule'] ) ) : '';
					if ( $post_id && $name && $qty > 0 && $price_rule ) {
						$rent_continue                        = isset( $_POST['rent_continue'] ) ? sanitize_text_field( wp_unslash( $_POST['rent_continue'] ) ) : 'on';
						$qty_info['qty']                      = intval( $qty );
						$qty_info['reserve']                  = intval( isset( $_POST['qty_reserve'] ) ? sanitize_text_field( wp_unslash( $_POST['qty_reserve'] ) ) : 0 );
						$qty_info['min']                      = isset( $_POST['qty_min'] ) ? sanitize_text_field( wp_unslash( $_POST['qty_min'] ) ) : '';
						$qty_info['max']                      = isset( $_POST['qty_max'] ) ? sanitize_text_field( wp_unslash( $_POST['qty_max'] ) ) : '';
						$price_info['price_hourly']['price']  = isset( $_POST['price_hourly'] ) ? sanitize_text_field( wp_unslash( $_POST['price_hourly'] ) ) : '';
						$price_info['price_hourly']['min']    = isset( $_POST['min_hour'] ) ? sanitize_text_field( wp_unslash( $_POST['min_hour'] ) ) : '';
						$price_info['price_daily']['price']   = isset( $_POST['price_daily'] ) ? sanitize_text_field( wp_unslash( $_POST['price_daily'] ) ) : '';
						$price_info['price_daily']['min']     = isset( $_POST['min_day'] ) ? sanitize_text_field( wp_unslash( $_POST['min_day'] ) ) : '';
						$price_info['price_monthly']['price'] = isset( $_POST['price_monthly'] ) ? sanitize_text_field( wp_unslash( $_POST['price_monthly'] ) ) : '';
						$price_info['price_monthly']['min']   = isset( $_POST['min_month'] ) ? sanitize_text_field( wp_unslash( $_POST['min_month'] ) ) : '';
						$others                               = [];
						$features                             = [];
						$feature_names                        = isset( $_POST['feature_name'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['feature_name'] ) ) : [];
						$feature_values                       = isset( $_POST['feature_value'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['feature_value'] ) ) : [];
						if ( is_array( $feature_names ) && sizeof( $feature_names ) > 0 && is_array( $feature_values ) && sizeof( $feature_values ) > 0 ) {
							foreach ( $feature_names as $key => $feature_name ) {
								if ( $feature_name && $feature_values[ $key ] ) {
									$features[ $key ]['label'] = $feature_name;
									$features[ $key ]['value'] = $feature_values[ $key ];
								}
							}
						}
						$data = [
							'post_id' => intval( $post_id ),
							'rent_continue' => $rent_continue,
							'name' => sanitize_text_field( $name ),
							'icon' => isset( $_POST['icon'] ) ? sanitize_text_field( wp_unslash( $_POST['icon'] ) ) : '',
							'qty_info' => json_encode( $qty_info ),
							'brand' => isset( $_POST['brand'] ) ? sanitize_text_field( wp_unslash( $_POST['brand'] ) ) : '',
							'description' => isset( $_POST['description'] ) ? sanitize_text_field( wp_unslash( $_POST['description'] ) ) : '',
							'price_rule' => $price_rule,
							'price_info' => json_encode( $price_info ),
							'features' => json_encode( $features ),
							'gallery' => isset( $_POST['abprf_sliders'] ) ? sanitize_text_field( wp_unslash( $_POST['abprf_sliders'] ) ) : '',
							'status' => get_post_status( $post_id ),
							'others' => json_encode( $others ),
							'updated_at' => current_time( 'Y-m-d H:i' )
						];
						global $wpdb;
						$table_name = $wpdb->prefix . 'abprf_property';
						if ( $property_id ) {
							$where = [ 'id' => $property_id ];
							// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
							$wpdb->update( $table_name, $data, $where, [ '%s', '%s', '%s' ], [ '%d' ] );
							wp_send_json_success( esc_html__( 'Property Updated Successfully ! ', 'abprf-rental-forge' ) );
						} else {
							// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
							$wpdb->insert( $table_name, $data );
							wp_send_json_success( esc_html__( 'Property Saved Successfully ! ', 'abprf-rental-forge' ) );
						}
					} else {
						wp_send_json_success( esc_html__( 'Property not Saved !', 'abprf-rental-forge' ) );
					}
				} else {
					wp_send_json_success( esc_html__( 'Property not Saved !', 'abprf-rental-forge' ) );
				}
				wp_die();
			}

			public function property_add_edit() {
				if ( is_admin() && check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce' ) && current_user_can( 'manage_options' ) ) {
					$property_id   = isset( $_POST['property_id'] ) ? sanitize_text_field( wp_unslash( $_POST['property_id'] ) ) : '';
					$property_copy = isset( $_POST['property_copy'] ) ? sanitize_text_field( wp_unslash( $_POST['property_copy'] ) ) : '';
					$this->add_property( $property_id, $property_copy );
				}
				wp_die();
			}

			public function reload_property_list() {
				if ( is_admin() && check_ajax_referer( 'abprf_admin_ajax_nonce', 'nonce' ) && current_user_can( 'manage_options' ) ) {
					$filter_args            = isset( $_POST['filter_args'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['filter_args'] ) ) : [];
					$post_id                = array_key_exists( 'post_id', $filter_args ) && $filter_args['post_id'] != '' ? $filter_args['post_id'] : 'all';
					$filter_args['post_id'] = $post_id;
					$this->properties_table( $filter_args );
				}
				wp_die();
			}

			public function properties_table( $filter_args ) {
				// echo '<pre>';print_r($filter_args);echo '</pre>';
				$page_number    = array_key_exists( 'page_number', $filter_args ) && is_numeric( $filter_args['page_number'] ) ? (int) $filter_args['page_number'] : 1;
				$limit          = array_key_exists( 'page_item', $filter_args ) && is_numeric( $filter_args['page_item'] ) ? (int) $filter_args['page_item'] : ABPRF_Function::get_option( 'abprf_per_page_item', 20 );
				$count          = ( $page_number - 1 ) * $limit + 1;
				$offset         = $count - 1;
				$properties     = ABPRF_Query::get_property( $filter_args, $limit, $offset );
				$total_property = ABPRF_Query::get_property( $filter_args, 0, 0, true );
				if ( ! empty( $properties ) && is_array( $properties ) && sizeof( $properties ) > 0 ) {
					$filter_post_id = array_key_exists( 'post_id', $filter_args ) ? $filter_args['post_id'] : '';
					$copy_post_id   = array_key_exists( 'copy_post_id', $filter_args ) ? $filter_args['copy_post_id'] : '';
					?>
                    <table class="_abprf">
                        <thead>
                        <tr>
                            <th class="_w_50"><?php esc_html_e( 'SI', 'abprf-rental-forge' ); ?></th>
                            <th class="_w_100"><?php esc_html_e( 'Image/icon', 'abprf-rental-forge' ); ?></th>
                            <th><?php esc_html_e( 'Property', 'abprf-rental-forge' ); ?></th>
							<?php if ( ( empty( $filter_post_id ) || is_string( $filter_post_id ) ) && empty( $copy_post_id ) ) { ?>
                                <th><?php esc_html_e( 'Category/Post', 'abprf-rental-forge' ); ?></th>
							<?php } ?>
                            <th class="_w_100"><?php esc_html_e( 'On Rent', 'abprf-rental-forge' ); ?></th>
                            <th class="_w_100"><?php esc_html_e( 'In House', 'abprf-rental-forge' ); ?></th>
                            <th class="_w_150"><?php esc_html_e( 'Actions', 'abprf-rental-forge' ); ?></th>
                        </tr>
                        </thead>
                        <tbody>
						<?php foreach ( $properties as $property ) {
							$icon               = array_key_exists( 'icon', $property ) ? $property['icon'] : '';
							$name               = array_key_exists( 'name', $property ) ? $property['name'] : '';
							$property_id        = array_key_exists( 'id', $property ) ? $property['id'] : '';
							$post_id            = array_key_exists( 'post_id', $property ) ? $property['post_id'] : '';
							$status             = array_key_exists( 'status', $property ) ? $property['status'] : '';
							$rent_continue      = array_key_exists( 'rent_continue', $property ) ? $property['rent_continue'] : '';
							$qty                = array_key_exists( 'qty', $property ) ? $property['qty'] : 0;
							$post_rent_continue = ABPRF_Function::get_post_info( $post_id, 'rent_continue', 'on' );
							$post_status        = get_post_status( $post_id );
							?>
                            <tr class="delete_area">
                                <th><?php echo esc_html( $count ); ?>.</th>
                                <th class="_fs_h2"><?php ABPRF_Layout::image_icon( $icon ); ?></th>
                                <td>
                                    <div class="_fd_column">
                                        <h5 class="_abprf_color_theme"><?php echo esc_html( $name ); ?></h5>
                                        <div class="_d_flex">
											<?php if ( ! empty( $copy_post_id ) ) { ?>
                                                <input type="hidden" name="copy_property_id[]" value="<?php echo esc_attr( $property_id ); ?>"/>
											<?php } else { ?>
                                                <span class="_mar_r_xxs publish"><?php echo esc_html( __( 'Property Id : ', 'abprf-rental-forge' ) . ' ' . $property_id ); ?></span>
											<?php } ?>
                                            <span class="_mar_r_xxs <?php echo esc_attr( $rent_continue == 'on' ? 'publish' : 'trash' ); ?>"><?php echo esc_html( $rent_continue == 'on' ? __( 'Rent On', 'abprf-rental-forge' ) : __( 'Rent Off', 'abprf-rental-forge' ) ); ?></span>
                                            <span class="_mar_r_xxs <?php echo esc_attr( $status ); ?>"><?php echo esc_html( $status ); ?></span>
                                        </div>
                                    </div>
                                </td>
								<?php if ( ( empty( $filter_post_id ) || is_string( $filter_post_id ) ) && empty( $copy_post_id ) ) { ?>
                                    <td>
                                        <div class="_fd_column">
                                            <a href="<?php echo esc_url( get_edit_post_link( $post_id ) ); ?>" class="_abprf_fs_h5 _color_theme"><?php echo esc_html( get_the_title( $post_id ) ); ?></a>
                                            <div class="_d_flex">
                                                <span class="_mar_r_xxs publish"><?php echo esc_html( __( 'Category Id : ', 'abprf-rental-forge' ) . ' ' . $post_id ); ?></span>
                                                <span class="_mar_r_xxs <?php echo esc_attr( $post_rent_continue == 'on' ? 'publish' : 'trash' ); ?>"><?php echo esc_html( $post_rent_continue == 'on' ? __( 'Rent On', 'abprf-rental-forge' ) : __( 'Rent Off', 'abprf-rental-forge' ) ); ?></span>
                                                <span class="_mar_r_xxs <?php echo esc_attr( $post_status ); ?>"><?php echo esc_html( $post_status ); ?></span>
                                            </div>
                                        </div>
                                    </td>
								<?php } ?>
                                <th>0/<?php echo esc_html( $qty ); ?></th>
                                <th>0/<?php echo esc_html( $qty ); ?></th>
                                <th>
									<?php if ( empty( $copy_post_id ) ) { ?>
                                        <div class="_f_wrap">
                                            <button type="button" class="_btn_light_yellow_mar_r_xxs" data-property_id="<?php echo esc_attr( $property_id ); ?>" data-target-popup="#abprf_property_popup" title="<?php echo esc_html__( 'Edit : ', 'abprf-rental-forge' ) . ' ' . esc_html( $name ); ?>">✍️</button>
                                            <button type="button" class="_btn_light_navy_blue _mar_r_xxs property_copy" data-property_id="<?php echo esc_attr( $property_id ); ?>" data-target-popup="#abprf_property_popup" title="<?php echo esc_html__( 'Copy/Clone : ', 'abprf-rental-forge' ) . ' ' . esc_html( $name ); ?>">🔁</button>
                                            <button type="button" class="_btn_light_danger_xxs abprf_property_delete" data-property_id="<?php echo esc_attr( $property_id ); ?>" title="<?php echo esc_html__( 'Trash : ', 'abprf-rental-forge' ) . ' ' . esc_html( $name ); ?>">❌</button>
                                        </div>
									<?php } else { ?>
										<?php ABPRF_Layout::button_delete(); ?>
									<?php } ?>
                                </th>
                            </tr>
							<?php
							$count ++;
						} ?>
                        </tbody>
                    </table>
					<?php
					do_action( 'abprf_pagination', [ 'page_item' => $limit, 'page_number' => $page_number, 'total' => $total_property, 'style' => 'ajax' ] );
				} else {
					ABPRF_Layout::layout_warning_info( 'not_property_found' );
				}
				//echo '<pre>';				print_r( $properties );				echo '</pre>';
			}

			public function add_property( $property_id = '', $property_copy = false ) {
				$cpt      = ABPRF_Function::get_cpt();
				$post_ids = ABPRF_Query::get_all_post_id( $cpt, - 1, 1, [ 'publish', 'draft', 'private', 'trash' ] );
				if ( ! empty( $post_ids ) && is_array( $post_ids ) && sizeof( $post_ids ) > 0 ) {
					$current_post_id = $name = $icon_image = $qty = $qty_reserve = $qty_max = $brand = $description = $price_hourly = $min_hour = $price_daily = $min_day = $price_monthly = $min_month = $sliders = '';
					$rent_continue   = 'on';
					$features        = [];
					$price_rule      = 'hourly,daily';
					$save_text       = __( 'Save Property Configuration', 'abprf-rental-forge' );
					if ( $property_id ) {
						$properties = ABPRF_Query::get_property( [ 'property_id' => $property_id ] );
						if ( ! empty( $properties ) && is_array( $properties ) && sizeof( $properties ) > 0 ) {
							$property        = current( $properties );
							$icon_image      = array_key_exists( 'icon', $property ) ? $property['icon'] : '';
							$name            = array_key_exists( 'name', $property ) ? $property['name'] : '';
							$current_post_id = array_key_exists( 'post_id', $property ) ? $property['post_id'] : '';
							$rent_continue   = array_key_exists( 'rent_continue', $property ) ? $property['rent_continue'] : '';
							$qty_info        = array_key_exists( 'qty_info', $property ) ? $property['qty_info'] : '';
							$qty_info        = ! empty( $qty_info ) ? json_decode( $qty_info, true ) : [];
							$qty             = array_key_exists( 'qty', $qty_info ) ? $qty_info['qty'] : '';
							$qty_reserve     = array_key_exists( 'reserve', $qty_info ) ? $qty_info['reserve'] : '';
							$qty_min         = array_key_exists( 'min', $qty_info ) ? $qty_info['min'] : '';
							$qty_max         = array_key_exists( 'max', $qty_info ) ? $qty_info['max'] : '';
							$brand           = array_key_exists( 'brand', $property ) ? $property['brand'] : '';
							$description     = array_key_exists( 'description', $property ) ? $property['description'] : '';
							$price_rule      = array_key_exists( 'price_rule', $property ) ? $property['price_rule'] : '';
							$price_info      = array_key_exists( 'price_info', $property ) ? $property['price_info'] : '';
							$price_info      = ! empty( $price_info ) ? json_decode( $price_info, true ) : [];
							$hourly_info     = array_key_exists( 'price_hourly', $price_info ) ? $price_info['price_hourly'] : [];
							$price_hourly    = is_array( $hourly_info ) && array_key_exists( 'price', $price_info ) ? $price_info['price'] : '';
							$min_hour        = is_array( $hourly_info ) && array_key_exists( 'min', $price_info ) ? $price_info['min'] : '';
							$daily_info      = array_key_exists( 'price_daily', $price_info ) ? $price_info['price_daily'] : [];
							$price_daily     = is_array( $daily_info ) && array_key_exists( 'price', $daily_info ) ? $daily_info['price'] : '';
							$min_day         = is_array( $daily_info ) && array_key_exists( 'min', $daily_info ) ? $daily_info['min'] : '';
							$monthly_info    = array_key_exists( 'price_monthly', $price_info ) ? $price_info['price_monthly'] : [];
							$price_monthly   = is_array( $monthly_info ) && array_key_exists( 'price', $monthly_info ) ? $monthly_info['price'] : '';
							$min_month       = is_array( $monthly_info ) && array_key_exists( 'min', $monthly_info ) ? $monthly_info['min'] : '';
							$features        = array_key_exists( 'features', $property ) ? $property['features'] : '';
							$sliders         = array_key_exists( 'gallery', $property ) ? $property['gallery'] : '';
							$features        = ! empty( $features ) ? json_decode( $features, true ) : [];
							$save_text       = __( 'Update Property Configuration', 'abprf-rental-forge' );
							// echo '<pre>';print_r( $properties );echo '</pre>';
						}
						if ( $property_copy ) {
							$property_id = '';
							$save_text   = __( 'Copy Property Configuration', 'abprf-rental-forge' );
						}
					}
					$price_rule_array = $price_rule ? explode( ',', $price_rule ) : [ 'hourly', 'daily' ];
					$price_rules      = ABPRF_Layout::price_rules();
					?>
                    <form class="abprf_save_property" method="post" action="">
                        <input type="hidden" name="property_id" value="<?php echo esc_attr( $property_id ); ?>">
                        <h5 class="_abprf_color_theme"><?php esc_html_e( 'Property General Configuration', 'abprf-rental-forge' ); ?></h5>
                        <div class="_divider_xs"></div>
                        <div class="group_setting">
                            <div class="_setting_item">
                                <label class="_f_equal_f_wrap">
                                    <span class="_mar_r_xs"><?php esc_html_e( 'Select Category/Post', 'abprf-rental-forge' ); ?><sup class="_color_required">*</sup></span>
                                    <select class="_form_control " name="post_id" required>
                                        <option disabled selected><?php esc_html_e( 'Please Select', 'abprf-rental-forge' ); ?></option>
										<?php foreach ( $post_ids as $post_id ) { ?>
                                            <option value="<?php echo esc_attr( $post_id ); ?>" <?php echo esc_attr( $post_id == $current_post_id ? 'selected' : '' ); ?>><?php echo esc_html( get_the_title( $post_id ) ); ?></option>
										<?php } ?>
                                    </select>
                                </label>
                                <div class="_divider_xs"></div>
								<?php ABPRF_Layout::info_text( 'post_id' ); ?>
                            </div>
                            <div class="_setting_item">
                                <div class="_fa_center">
									<?php ABPRF_Layout::switch_checkbox( 'rent_continue', $rent_continue ); ?>
                                    <span class="_fs_label_mar_lr_xs"><?php esc_html_e( 'Rent continue?', 'abprf-rental-forge' ); ?></span>
                                </div>
                                <div class="_divider_xs"></div>
								<?php ABPRF_Layout::info_text( 'rent_continue' ); ?>
                            </div>
                        </div>
                        <div class="group_setting">
                            <div class="_setting_item">
                                <label class="_f_equal_f_wrap">
                                    <span class="_mar_r_xs"><?php esc_html_e( 'Property Name', 'abprf-rental-forge' ); ?><sup class="_color_required">*</sup></span>
                                    <input type="text" class="_form_control validation_name" name="name" placeholder="<?php esc_attr_e( 'EX: Bike', 'abprf-rental-forge' ); ?>" value="<?php echo esc_attr( $name ); ?>" required/>
                                </label>
                                <div class="_divider_xs"></div>
								<?php ABPRF_Layout::info_text( 'name' ); ?>
                            </div>
                            <div class="_setting_item">
                                <divl class="_f_equal_f_wrap">
                                    <span class="_fs_label_mar_lr_xs"><?php esc_html_e( 'Property Icon/Image', 'abprf-rental-forge' ); ?></span>
									<?php do_action( 'abprf_add_image_icon', 'icon', $icon_image ); ?>
                                </divl>
                                <div class="_divider_xs"></div>
								<?php ABPRF_Layout::info_text( 'icon' ); ?>
                            </div>
                        </div>
                        <div class="group_setting">
                            <div class="_setting_item">
                                <label class="_f_equal_f_wrap">
                                    <span class="_mar_r_xs"><?php esc_html_e( 'Property Quantity', 'abprf-rental-forge' ); ?><sup class="_color_required">*</sup></span>
                                    <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="qty" placeholder="<?php esc_attr_e( 'EX: 15', 'abprf-rental-forge' ); ?>" value="<?php echo esc_attr( $qty ); ?>" required/>
                                </label>
                                <div class="_divider_xs"></div>
								<?php ABPRF_Layout::info_text( 'qty' ); ?>
                            </div>
                            <div class="_setting_item">
                                <label class="_f_equal_f_wrap">
                                    <span class="_mar_r_xs"><?php esc_html_e( 'Reserve Quantity', 'abprf-rental-forge' ); ?></span>
                                    <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="qty_reserve" placeholder="<?php esc_attr_e( 'EX: 15', 'abprf-rental-forge' ); ?>" value="<?php echo esc_attr( $qty_reserve ); ?>"/>
                                </label>
                                <div class="_divider_xs"></div>
								<?php ABPRF_Layout::info_text( 'qty_reserve' ); ?>
                            </div>
                        </div>
                        <div class="group_setting">
                            <div class="_setting_item">
                                <label class="_f_equal_f_wrap">
                                    <span class="_mar_r_xs"><?php esc_html_e( 'Minimum Quantity', 'abprf-rental-forge' ); ?></span>
                                    <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="qty_min" placeholder="<?php esc_attr_e( 'EX: 15', 'abprf-rental-forge' ); ?>" value="<?php echo esc_attr( $qty_min ); ?>" required/>
                                </label>
                                <div class="_divider_xs"></div>
								<?php ABPRF_Layout::info_text( 'qty_min' ); ?>
                            </div>
                            <div class="_setting_item">
                                <label class="_f_equal_f_wrap">
                                    <span class="_mar_r_xs"><?php esc_html_e( 'Property Max Quantity', 'abprf-rental-forge' ); ?></span>
                                    <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="qty_max" placeholder="<?php esc_attr_e( 'EX: 15', 'abprf-rental-forge' ); ?>" value="<?php echo esc_attr( $qty_max ); ?>"/>
                                </label>
                                <div class="_divider_xs"></div>
								<?php ABPRF_Layout::info_text( 'qty_max' ); ?>
                            </div>
                        </div>
                        <div class="group_setting">
                            <div class="_setting_item">
                                <label class="_f_equal_f_wrap">
                                    <span class="_mar_r_xs"><?php esc_html_e( 'Property Brand', 'abprf-rental-forge' ); ?></span>
                                    <input type="text" class="_form_control validation_name" name="brand" placeholder="<?php esc_attr_e( 'EX: Yamaha R15', 'abprf-rental-forge' ); ?>" value="<?php echo esc_attr( $brand ); ?>"/>
                                </label>
                                <div class="_divider_xs"></div>
								<?php ABPRF_Layout::info_text( 'brand' ); ?>
                            </div>
                            <div class="_setting_item">
                                <label class="_f_equal_f_wrap">
                                    <span class="_mar_r_xs"><?php esc_html_e( 'Property Short Description', 'abprf-rental-forge' ); ?></span>
                                    <textarea class="_form_control" name="description" placeholder="<?php esc_attr_e( 'EX: Description', 'abprf-rental-forge' ); ?>"><?php echo esc_html( $description ); ?></textarea>
                                </label>
                                <div class="_divider_xs"></div>
								<?php ABPRF_Layout::info_text( 'description' ); ?>
                            </div>
                        </div>
                        <div class="_divider_xs"></div>
                        <div class="_fj_between">
                            <h5 class="_abprf_color_theme"><?php esc_html_e( 'Pricing Configuration', 'abprf-rental-forge' ); ?></h5>
                            <div class="abprf_checkbox">
                                <input type="hidden" name="price_rule" value="<?php echo esc_attr( $price_rule ); ?>"/>
								<?php foreach ( $price_rules as $key => $rule ) { ?>
                                    <div class="checkbox_item _min_100">
                                        <button type="button" class="_btn_white_xs <?php echo esc_attr( in_array( $key, $price_rule_array ) ? 'rf_active' : '' ); ?>" data-collapse-target="#<?php echo esc_attr( $key ); ?>" data-checked="<?php echo esc_attr( $key ); ?>" data-open-icon="fa-check-square" data-close-icon="fa-square">
                                            <span data-icon class="_mar_r_xs far <?php echo esc_attr( in_array( $key, $price_rule_array ) ? 'far fa-check-square' : 'fa-square' ); ?>"></span><?php echo esc_html( $rule ); ?>
                                        </button>
                                    </div>
								<?php } ?>
                            </div>
                        </div>
						<?php ABPRF_Layout::info_text( 'price_rule' ); ?>
                        <div class="_divider_xs"></div>
                        <div data-collapse="#hourly" class=" <?php echo esc_attr( in_array( 'hourly', $price_rule_array ) ? 'rf_active' : '' ); ?>">
                            <div class="group_setting">
                                <div class="_setting_item">
                                    <label class="_f_equal_f_wrap">
                                        <span class="_mar_r_xs"><?php esc_html_e( 'Hourly Rate', 'abprf-rental-forge' ); ?></span>
                                        <input type="text" class="_form_control validation_price" name="price_hourly" placeholder="Ex: 10" value="<?php echo esc_attr( $price_hourly ); ?>"/>
                                    </label>
                                    <div class="_divider_xs"></div>
									<?php ABPRF_Layout::info_text( 'price_hourly' ); ?>
                                </div>
                                <div class="_setting_item">
                                    <label class="_f_equal_f_wrap">
                                        <span class="_mar_r_xs"><?php esc_html_e( 'Minimum Hours', 'abprf-rental-forge' ); ?></span>
                                        <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="min_hour" placeholder="<?php esc_attr_e( 'EX: 15', 'abprf-rental-forge' ); ?>" value="<?php echo esc_attr( $min_hour ); ?>"/>
                                    </label>
                                    <div class="_divider_xs"></div>
									<?php ABPRF_Layout::info_text( 'min_hour' ); ?>
                                </div>
                            </div>
                        </div>
                        <div data-collapse="#daily" class=" <?php echo esc_attr( in_array( 'daily', $price_rule_array ) ? 'rf_active' : '' ); ?>">
                            <div class="group_setting">
                                <div class="_setting_item">
                                    <label class="_f_equal_f_wrap">
                                        <span class="_mar_r_xs"><?php esc_html_e( 'Daily Rate', 'abprf-rental-forge' ); ?></span>
                                        <input type="text" class="_form_control validation_price" name="price_daily" placeholder="Ex: 10" value="<?php echo esc_attr( $price_daily ); ?>"/>
                                    </label>
                                    <div class="_divider_xs"></div>
									<?php ABPRF_Layout::info_text( 'price_daily' ); ?>
                                </div>
                                <div class="_setting_item">
                                    <label class="_f_equal_f_wrap">
                                        <span class="_mar_r_xs"><?php esc_html_e( 'Minimum Days', 'abprf-rental-forge' ); ?></span>
                                        <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="min_day" placeholder="<?php esc_attr_e( 'EX: 15', 'abprf-rental-forge' ); ?>" value="<?php echo esc_attr( $min_day ); ?>"/>
                                    </label>
                                    <div class="_divider_xs"></div>
									<?php ABPRF_Layout::info_text( 'min_day' ); ?>
                                </div>
                            </div>
                        </div>
                        <div data-collapse="#monthly" class=" <?php echo esc_attr( in_array( 'monthly', $price_rule_array ) ? 'rf_active' : '' ); ?>">
                            <div class="group_setting">
                                <div class="_setting_item">
                                    <label class="_f_equal_f_wrap">
                                        <span class="_mar_r_xs"><?php esc_html_e( 'monthly Rate', 'abprf-rental-forge' ); ?></span>
                                        <input type="text" class="_form_control validation_price" name="price_monthly" placeholder="Ex: 10" value="<?php echo esc_attr( $price_monthly ); ?>"/>
                                    </label>
                                    <div class="_divider_xs"></div>
									<?php ABPRF_Layout::info_text( 'price_monthly' ); ?>
                                </div>
                                <div class="_setting_item">
                                    <label class="_f_equal_f_wrap">
                                        <span class="_mar_r_xs"><?php esc_html_e( 'Minimum Months', 'abprf-rental-forge' ); ?></span>
                                        <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="min_month" placeholder="<?php esc_attr_e( 'EX: 15', 'abprf-rental-forge' ); ?>" value="<?php echo esc_attr( $min_month ); ?>"/>
                                    </label>
                                    <div class="_divider_xs"></div>
									<?php ABPRF_Layout::info_text( 'min_month' ); ?>
                                </div>
                            </div>
                        </div>
                        <div class="_divider_xs"></div>
                        <h5 class="_abprf_color_theme"><?php esc_html_e( 'Feature Configuration', 'abprf-rental-forge' ); ?></h5>
						<?php ABPRF_Layout::info_text( 'property_feature' ); ?>
                        <div class="_divider_xs"></div>
                        <div class="configuration_content">
                            <table class="_abprf">
                                <thead>
                                <tr>
                                    <th><?php esc_html_e( 'Label', 'abprf-rental-forge' ); ?><sup class="_color_required">*</sup></th>
                                    <th><?php esc_html_e( 'Value', 'abprf-rental-forge' ); ?><sup class="_color_required">*</sup></th>
                                    <th class="_w_10"><?php esc_html_e( 'Action', 'abprf-rental-forge' ); ?></th>
                                </tr>
                                </thead>
                                <tbody class="insertable_area sortable_area">
								<?php
									if ( is_array( $features ) && sizeof( $features ) > 0 ) {
										foreach ( $features as $feature ) {
											$this->feature_item( $feature );
										}
									}
								?>
                                </tbody>
                            </table>
                            <div class="_divider_xs"></div>
							<?php ABPRF_Layout::button_add( __( 'Add New Feature', 'abprf-rental-forge' ) ); ?>
                            <div class="abprf_d_none">
                                <table class="_abprf">
                                    <tbody class="hidden_content">
									<?php $this->feature_item(); ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="_divider_xs"></div>
                        <h5 class="_abprf_color_theme"><?php esc_html_e( 'Gallery Configuration', 'abprf-rental-forge' ); ?></h5>
						<?php ABPRF_Layout::info_text( 'abprf_sliders' ); ?>
                        <div class="_divider_xs"></div>
                        <div class="_setting_item">
							<?php do_action( 'abprf_add_image_multiple', 'abprf_sliders', $sliders ); ?>
                        </div>
                        <div class="_divider_xs"></div>
                        <button type="submit" class="_btn_theme"><span class="_mar_r_xxs">💾</span><?php echo esc_html( $save_text ); ?></button>
                    </form>
					<?php
				} else {
					ABPRF_Layout::layout_warning_info( 'not_post_found' );
					?>
                    <div class="_divider_xs"></div>
                    <a class="_btn_theme" href="<?php echo esc_url( admin_url( 'post-new.php?post_type=' . $cpt ) ); ?>"><span class="_mar_r_xs">➕</span><?php esc_html_e( 'Add New Category/Post', 'abprf-rental-forge' ); ?></a>
					<?php
				}
			}

			public function feature_item( $feature = [] ) {
				$label = is_array( $feature ) && array_key_exists( 'label', $feature ) ? $feature['label'] : '';
				$value = is_array( $feature ) && array_key_exists( 'value', $feature ) ? $feature['value'] : '';
				?>
                <tr class="delete_area">
                    <th>
                        <label>
                            <input type="text" class="_form_control validation_name" name="feature_name[]" placeholder="<?php esc_attr_e( 'EX: Model', 'abprf-rental-forge' ); ?>" value="<?php echo esc_attr( $label ); ?>"/>
                        </label>
                    </th>
                    <th>
                        <label>
                            <input type="text" class="_form_control validation_name" name="feature_value[]" placeholder="<?php esc_attr_e( 'EX: 2005', 'abprf-rental-forge' ); ?>" value="<?php echo esc_attr( $value ); ?>"/>
                        </label>
                    </th>
                    <td><?php ABPRF_Layout::button_delete_sort(); ?></td>
                </tr>
				<?php
			}
		}
		new ABPRF_Properties();
	}