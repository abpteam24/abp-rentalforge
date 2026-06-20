<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	if ( ! class_exists( 'ABPRF_Layout' ) ) {
		class ABPRF_Layout {
			public function __construct() {
				add_action( 'abprf_load_date_picker', [ $this, 'load_date_picker' ], 10, 2 );
				//==============================//
				add_action( 'abprf_add_icon', array( $this, 'load_icon' ), 10, 2 );
				add_action( 'abprf_add_image', array( $this, 'add_single_image' ), 10, 2 );
				add_action( 'abprf_add_image_multiple', array( $this, 'add_image_multi' ), 10, 2 );
				add_action( 'abprf_add_image_icon', array( $this, 'selection_icon_image' ), 10, 3 );
			}
			public function load_date_picker( $selector, $dates ): void {
				if ( empty( $dates ) || ! is_array( $dates ) ) {
					return;
				}
				$picker_data    = self::create_datepicker_array( $dates );
				$json_selector = wp_json_encode( sanitize_text_field( $selector ) );
				$json_data     = wp_json_encode( $picker_data );
				$inline_js = "window.abprf_picker_data = window.abprf_picker_data || {}; window.abprf_picker_data[{$json_selector}] = {$json_data};";
				wp_add_inline_script( 'jquery-ui-datepicker', $inline_js );
			}
			public static function create_datepicker_array( $dates ): array {
				$start_date  = current( $dates );
				$start_year  = (int) gmdate( 'Y', strtotime( $start_date ) );
				$start_month = (int) ( gmdate( 'n', strtotime( $start_date ) ) - 1 );
				$start_day   = (int) gmdate( 'j', strtotime( $start_date ) );
				$end_date    = end( $dates );
				$end_year    = (int) gmdate( 'Y', strtotime( $end_date ) );
				$end_month   = (int) ( gmdate( 'n', strtotime( $end_date ) ) - 1 );
				$end_day     = (int) gmdate( 'j', strtotime( $end_date ) );
				$all_dates   = [];
				foreach ( $dates as $date ) {
					$all_dates[] = gmdate( 'j-n-Y', strtotime( $date ) );
				}

				return [
					'minYear' => $start_year,
					'minMonth' => $start_month,
					'minDay' => $start_day,
					'maxYear' => $end_year,
					'maxMonth' => $end_month,
					'maxDay' => $end_day,
					'activeDates' => $all_dates,
					'txtAvail' => esc_js( __( 'Available', 'abp-rentalforge' ) ),
					'txtUnavail' => esc_js( __( 'Unavailable', 'abp-rentalforge' ) )
				];
			}
			//==============================//
			public static function load_admin_globally(): void {
				ABPRF_Layout::popup_empty( '#abprf_global_popup' );
				ABPRF_Layout::icon_popup(); ?>
				<?php
			}
			//==============================//
			public static function button_add( $button_text, $class = '', $button_class = '', $icon_class = '', $change_input_name = '' ): void {
				$class        = $class ?: 'add_new_hook';
				$button_class = $button_class ?: '_btn_default';
				$icon_class   = $icon_class ?: 'fas fa-plus';
				?>
                <button class="<?php echo esc_attr( $button_class . ' ' . $class ); ?>" type="button">
                    <span class="_mar_r_xs <?php echo esc_attr( $icon_class ); ?>"></span><span data-input-change="<?php echo esc_attr( $change_input_name ); ?>"><?php echo esc_html( $button_text ); ?></span>
                </button>
				<?php
			}
			public static function button_add_xs( $button_text, $class = '', $button_class = '' ): void {
				$class        = $class ?: 'add_new_hook';
				$button_class = $button_class ?: '_btn_default_xs';
				?>
                <button class="<?php echo esc_attr( $button_class . ' ' . $class ); ?>" type="button">
                    <span class="_mar_r_xxs">➕</span><?php echo esc_html( $button_text ); ?>
                </button>
				<?php
			}
			public static function button_delete_sort_edit(): void {
				?>
                <div class="_all_center">
                    <div class="_group_content">
						<?php
							self::button_edit();
							self::button_sort();
							self::button_delete();
						?>
                    </div>
                </div>
				<?php
			}
			public static function button_delete_sort(): void {
				?>
                <div class="_all_center">
                    <div class="_group_content">
						<?php
							self::button_sort();
							self::button_delete();
						?>
                    </div>
                </div>
				<?php
			}
			public static function button_edit( $class_edit = 'edit_hook' ): void {
				?>
                <button class="_btn_navy_blue_xs <?php echo esc_attr( $class_edit ); ?>" type="button" title="<?php esc_attr_e( 'Edit This Item', 'abp-rentalforge' ); ?>">
                    <span class="fas fa-edit"></span>
                </button>
				<?php
			}
			public static function button_delete( $class = 'delete_hook' ): void {
				?>
                <button class="_btn_danger_xs <?php echo esc_attr( $class ); ?>" type="button" title="<?php esc_attr_e( 'Delete This Item', 'abp-rentalforge' ); ?>">
                    <span class="fas fa-times"></span>
                </button>
				<?php
			}
			public static function button_sort(): void {
				?>
                <div class="_btn_warning_xs sortable_handle" type="button" title="<?php esc_attr_e( 'Move This Item', 'abp-rentalforge' ); ?>">
                    <span class="fas fa-arrows-alt"></span>
                </div>
				<?php
			}
			//=============================//
			public static function popup_button( $target_popup_id, $text ): void {
				?>
                <button type="button" class="_btn_default_bg_blue" data-target-popup="<?php echo esc_attr( $target_popup_id ); ?>"><span class="fas fa-plus-square"></span> <?php echo esc_html( $text ); ?></button>
				<?php
			}
			public static function popup_button_xs( $target_popup_id, $text ): void {
				?>
                <button type="button" class="_btn_default_xs_bg_blue" data-target-popup="<?php echo esc_attr( $target_popup_id ); ?>"><span class="fas fa-plus-square"></span> <?php echo esc_html( $text ); ?></button>
				<?php
			}
			public static function popup_empty( $target_popup_id, $class = '' ): void {
				?>
                <div class="abprf_popup <?php echo esc_attr( $class ); ?>" data-popup="<?php echo esc_attr( $target_popup_id ); ?>">
                    <div class="popup_area">
                        <span class="popup_close"><i class="fas fa-times"></i></span>
                        <div class="popup_body"></div>
                    </div>
                </div>
				<?php
			}
			public static function icon_popup(): void {
				?>
                <div class="popup_icon abprf_popup" data-popup="#abprf_popup_icon">
                    <div class="popup_area">
                        <div class="popup_head _all_center">
                            <div class="abp_dropdown _max_400">
                                <label class="_abprf_all_center">
                                    <input type="hidden" class="abp_icon_search_hidden" name="abp_icon_search" value=""/>
                                    <input type="text" class="_form_control_text_center validation_name abprf_allow abp_icon_search" name="" placeholder="<?php esc_attr_e( 'Search  icon', 'abp-rentalforge' ); ?>" value=""/>
                                </label>
                                <div class="dropdown_list"></div>
                            </div>
                            <span class="popup_close"><i class="fas fa-times"></i></span>
                        </div>
                        <div class="popup_body">
                            <h4 class="_abprf_text_center item_icon_title"></h4>
                            <div class="item_icon_area"></div>
                        </div>
                    </div>
                </div>
				<?php
			}
			//=============================//
			public static function info_text( $key = '', $data = '' ): void {
				$data = empty( $data ) ? ABPRF_Layout::array_info( $key ) : $data;
				if ( $data ) {
					?>
                    <div class="info_text load_more">
                        <span class="load_more_content">ℹ️ &nbsp;<?php echo wp_kses_post( $data ); ?></span>
                        <span class="load_more_action" data-less="<?php esc_html_e( '.... Less ', 'abp-rentalforge' ); ?>" data-more="<?php esc_html_e( '.... More', 'abp-rentalforge' ); ?>"><?php esc_html_e( '.... More', 'abp-rentalforge' ); ?></span>
                    </div>
					<?php
				}
			}
			public static function layout_warning_info( $key ): void {
				$data = ABPRF_Layout::array_info( $key );
				if ( $data ) {
					echo '<div class="_section_bg_warning_mar_zero"><h4 class="_abprf_text_center_color_white">' . esc_html( $data ) . '</h4></div>';
				}
			}
			public static function layout_warning_info_xs( $key ): void {
				$data = ABPRF_Layout::array_info( $key );
				if ( $data ) {
					echo '<div class="_abprf_text_center_color_white_bg_warning_padding_xxs_fs_label">' . esc_html( $data ) . '</div>';
				}
			}
			public static function image( $post_id = '', $image_id = '', $url = '', $class = '' ): void {
				$image_url = ( $post_id > 0 || $image_id ) ? ABPRF_Function::get_image_url( $post_id, $image_id ) : $url;
				$post_url  = $post_id > 0 ? get_the_permalink( $post_id ) : '';
				$image_url = $image_url ?: ABPRF_BLANK_IMG_URL;
				if ( $image_url ) {
					?>
                    <div class="rf_image <?php echo esc_attr( $class ); ?>" data-image-href="<?php echo esc_url( $image_url ); ?>" <?php if ( ! empty( $post_url ) ) { ?> data-href="<?php echo esc_url( $post_url ); ?>" <?php } ?> >
                        <img class="_img_control" src="#" alt="<?php echo esc_attr( max( $post_id, $image_id ) ); ?>">
                    </div>
					<?php
				}
			}
			public static function image_icon( $icon_image, $class = '' ): void {
				if ( ! empty( $icon_image ) ) {
					$icon = $image = $emoji = '';
					if ( is_numeric( $icon_image ) ) {
						$image = $icon_image;
					} elseif ( preg_match( '/\s/', $icon_image ) ) {
						$icon = $icon_image;
					} else {
						$emoji = $icon_image;
					}
					if ( $image ) {
						ABPRF_Layout::image( '', $image );
					} else { ?>
                        <i class="<?php echo esc_attr( $icon . ' ' . $class ); ?>"><?php echo esc_html( $emoji ); ?></i>
					<?php }
				}
			}
			public static function on(): bool|string {
				ob_start();
				?>
                <strong class="_abprf_color_theme"> <?php esc_html_e( 'ON', 'abp-rentalforge' ); ?></strong>
				<?php
				return ob_get_clean();
			}
			public static function off(): bool|string {
				ob_start();
				?>
                <strong class="_abprf_color_theme"> <?php esc_html_e( 'OFF', 'abp-rentalforge' ); ?></strong>
				<?php
				return ob_get_clean();
			}
			//==============Input field===============//
			public static function input_dropdown( $infos, $icon = '' ): void {
				if ( is_array( $infos ) && sizeof( $infos ) > 0 ) {
					asort( $infos );
					?>
                    <div class="dropdown_list">
                        <ul class="_abprf">
							<?php foreach ( $infos as $info ) { ?>
                                <li data-value="<?php echo esc_attr( $info ); ?>"><span class="<?php echo esc_attr( $icon ); ?> _mar_r_xxs"></span><span data-text><?php echo esc_html( $info ); ?></span></li>
							<?php } ?>
                        </ul>
                    </div>
					<?php
				}
			}
			public static function quantity_input( $input_info = [] ): void {
				$name        = array_key_exists( 'name', $input_info ) ? $input_info['name'] : '';
				$price       = array_key_exists( 'price', $input_info ) ? $input_info['price'] : 0;
				$min_qty     = array_key_exists( 'min_qty', $input_info ) ? $input_info['min_qty'] : 1;
				$max_qty     = array_key_exists( 'max_qty', $input_info ) ? $input_info['max_qty'] : 1;
				$class       = array_key_exists( 'class', $input_info ) ? $input_info['class'] : '';
				$collapse_id = array_key_exists( 'collapse_id', $input_info ) ? $input_info['collapse_id'] : '';
				if ( $name && $max_qty >= $min_qty ) {
					if ( ! empty( $collapse_id ) ) {
						?> <div data-collapse="<?php echo esc_attr( $collapse_id ); ?>"><?php
					}
					?>
                    <div class="_group_content qty_input">
                        <div class="qty_decrease _ag_content"> ➖</div>
                        <label>
                            <input type="text" class="_form_control  validation_number <?php echo esc_attr( $class ); ?>"
                                   name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $min_qty ); ?>"
                                   data-price="<?php echo esc_attr( $price ); ?>" data-min="<?php echo esc_attr( $min_qty ); ?>" data-max="<?php echo esc_attr( $max_qty ); ?>"
                            />
                        </label>
                        <div class="qty_increase _ag_content">➕</div>
                    </div>
					<?php
					if ( ! empty( $collapse_id ) ) {
						?></div><?php
					}
				}
			}
			public static function switch_checkbox( $name, $value = '' ): void {
				$value = in_array( $value, [ 'on', 'off', '' ], true ) ? $value : '';
				?>
                <div class="_br <?php echo esc_attr( $value === 'on' ? 'rf_active' : '' ); ?>" data-switch data-collapse-target="#<?php echo esc_attr( $name ); ?>">
                    <input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>">
                </div>
				<?php
			}
			public static function input_title( $label = '', $required = '' ): void {
				if ( $label ) { ?>
                    <span class="_mar_b_xxs">
							<?php echo esc_html( $label ); ?>
						<?php if ( $required ) { ?>
                            <sup class="_color_required">*</sup>
						<?php } ?>
						</span>
					<?php
				}
			}
			public static function input_date( $name, $date = '', $label = '', $required = '' ): void {
				$date_format  = ABPRF_Function::date_format_php();
				$now          = date_i18n( $date_format, strtotime( current_time( 'Y-m-d' ) ) );
				$hidden_date  = $date ? gmdate( 'Y-m-d', strtotime( $date ) ) : '';
				$visible_date = $date ? date_i18n( $date_format, strtotime( $date ) ) : '';
				?>
                <label class="_input_item">
					<?php self::input_title( $label, $required ); ?>
                    <input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $hidden_date ); ?>" <?php echo esc_attr( $required ); ?>/>
                    <input type="text" name="" class="_form_control abp_datepicker" value="<?php echo esc_attr( $visible_date ); ?>" placeholder="<?php echo esc_attr( $now ); ?>" readonly/>
                    <span class="fas fa-times date_close_icon" title="<?php esc_attr_e( 'Clear Date', 'abp-rentalforge' ); ?>"></span>
                </label>
				<?php
			}
			public static function input_time( $name, $time = '', $label = '', $required = '' ): void {
				?>
                <label class="_input_item">
					<?php self::input_title( $label, $required ); ?>
                    <input type="time" class="_form_control" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $time ); ?>" <?php echo esc_attr( $required ); ?>/>
                    <span class="fas fa-times time_close_icon" title="<?php esc_attr_e( 'Clear Time', 'abp-rentalforge' ); ?>"></span>
                </label>
				<?php
			}
			public static function textarea( $name, $value = '', $label = '', $required = '' ): void {
				?>
                <label class="abprf_textarea _input_item">
					<?php self::input_title( $label, $required ); ?>
                    <textarea name="<?php echo esc_attr( $name ); ?>" rows="3" class="_form_control" placeholder="<?php echo esc_attr( $label ); ?>" title="<?php echo esc_attr( $label ); ?>"  <?php echo esc_attr( $required ); ?>><?php echo esc_textarea( $value ); ?></textarea>
                </label>
				<?php
			}
			public static function select( $name, $value = '', $label = '', $required = '', $options = [] ): void {
				if ( is_array( $options ) && sizeof( $options ) > 0 ) {
					?>
                    <label class="_input_item">
						<?php self::input_title( $label, $required ); ?>
                        <select name="<?php echo esc_attr( $name ); ?>" class="_form_control" title="<?php echo esc_attr( $label ); ?>" <?php echo esc_attr( $required ); ?>>
                            <option value="" disabled selected><?php echo esc_html__( 'Please Select', 'abp-rentalforge' ) . ' ' . esc_html( $label ); ?></option>
							<?php foreach ( $options as $option ) { ?>
                                <option value="<?php echo esc_attr( $option ); ?>" <?php echo esc_attr( $option == $value ? 'selected' : '' ); ?>><?php echo esc_html( $option ); ?></option>
							<?php } ?>
                        </select>
                    </label>
					<?php
				}
			}
			public static function checkbox( $name, $value = '', $label = '', $required = '', $options = [] ): void {
				if ( is_array( $options ) && sizeof( $options ) > 0 ) {
					?>
                    <div class=" _input_item">
                        <span class="_fs_label"> <?php self::input_title( $label, $required ); ?></span>
                        <div class="custom_checkbox">
                            <input type="hidden" class="_form_control" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>"/>
							<?php foreach ( $options as $option ) { ?>
                                <div class="checkbox_item">
                                    <button type="button" class="_btn_white_xs <?php echo esc_attr( $option == $value ? 'rf_active' : '' ); ?>" data-checked="<?php echo esc_attr( $option ); ?>" data-open-icon="far fa-check-square" data-close-icon="far fa-square">
                                        <span data-icon class="_mar_r_xs <?php echo esc_attr( $option == $value ? 'far fa-check-square' : 'far fa-square' ); ?>"></span><?php echo esc_html( $option ); ?>
                                    </button>
                                </div>
							<?php } ?>
                        </div>
                    </div>
					<?php
				}
			}
			public static function radio( $name, $value = '', $label = '', $required = '', $options = [] ): void {
				if ( is_array( $options ) && sizeof( $options ) > 0 ) {
					?>
                    <div class=" _input_item">
                        <span class="_fs_label"> <?php self::input_title( $label, $required ); ?></span>
                        <div class="custom_radio">
                            <input type="hidden" class="_form_control" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>"/>
							<?php foreach ( $options as $option ) { ?>
                                <div class="radio_item">
                                    <button type="button" class="_btn_white_xs <?php echo esc_attr( $option == $value ? 'rf_active' : '' ); ?>" data-radio="<?php echo esc_attr( $option ); ?>" data-open-icon="far fa-check-circle" data-close-icon="far fa-circle">
                                        <span data-icon class="_mar_r_xs <?php echo esc_attr( $option == $value ? 'far fa-check-circle' : 'far fa-circle' ); ?>"></span><?php echo esc_html( $option ); ?>
                                    </button>
                                </div>
							<?php } ?>
                        </div>
                    </div>
					<?php
				}
			}
			//=============Add  Image / Icon================//
			public function load_icon( $name, $value = '' ): void {
				$button_active_class = $value ? '_d_none' : '';
				$icon                = $emoji = '';
				if ( preg_match( '/\s/', $value ) ) {
					$icon = $value;
				} else {
					$emoji = $value;
				}
				$icon_class = ( $icon || $emoji ) ? '' : '_d_none';
				?>
                <div class="icon_image_selection_area">
                    <input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>"/>
                    <div class="icon_item  <?php echo esc_attr( $icon_class ); ?>">
                        <div class="_all_center"><span class="<?php echo esc_attr( $icon ); ?>" data-add-icon><?php echo esc_html( $emoji ); ?></span></div>
                        <span class="fas fa-times icon_close icon_delete" title="<?php esc_html_e( 'Remove Icon', 'abp-rentalforge' ); ?>"></span>
                    </div>
                    <div class="image_icon_select_area <?php echo esc_attr( $button_active_class ); ?>">
                        <button class="_btn_info_xs icon_add" type="button" data-target-popup="#abprf_popup_icon"><span class="fas fa-icons _fs_h6"></span></button>
                    </div>
                </div>
				<?php
			}
			public function add_single_image( $name, $image_id = '' ): void {
				?>
                <div class="add_image">
                    <input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $image_id ); ?>"/>
					<?php if ( $image_id ) { ?>
                        <div class="add_image_item" data-image-id="<?php echo esc_attr( $image_id ); ?>'">
                            <span class="fas fa-times _circle_icon_xs remove_image"></span>
                            <img class="_img_control" src="<?php echo esc_url( wp_get_attachment_image_url( $image_id, 'medium' ) ); ?>" alt="<?php echo esc_attr( $image_id ); ?>"/>
                        </div>
					<?php } ?>
                    <button type="button" class="_btn_default_xs_bg_color_5_w_full <?php echo esc_attr( $image_id ? '_d_none' : '' ); ?>">
                        <span class="fas fa-image _mar_r_xs"></span><?php esc_html_e( 'Image', 'abp-rentalforge' ); ?>
                    </button>
                </div>
				<?php
			}
			public function add_image_multi( $name, $images ): void {
				$images = is_array( $images ) ? ABPRF_Function::array_to_string( $images ) : $images;
				?>
                <div class="multiple_image_area">
                    <input type="hidden" class="multiple_image_ids" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $images ); ?>"/>
                    <div class="multiple_image">
						<?php
							$all_images = explode( ',', $images );
							if ( $images && sizeof( $all_images ) > 0 ) {
								foreach ( $all_images as $image ) {
									$img_url = ABPRF_Function::get_image_url( '', $image, 'medium' ) ?: ABPRF_BLANK_IMG_URL;
									?>
                                    <div class="multiple_image_item" data-image-id="<?php echo esc_attr( $image ); ?>">
                                        <span class="fas fa-times _circle_icon_xs remove_image_multi"></span>
                                        <img class="_img_control" src="<?php echo esc_attr( $img_url ); ?>" alt="<?php echo esc_attr( $image ); ?>"/>
                                    </div>
									<?php
								}
							}
						?>
                    </div>
					<?php ABPRF_Layout::button_add_xs( __( 'Add  Image', 'abp-rentalforge' ), 'add_image_multi _mar_t_xs' ); ?>
                </div>
				<?php
			}
			public function selection_icon_image( $name, $value = '' ): void {
				$icon = $image = $emoji = '';
				if ( is_numeric( $value ) ) {
					$image = $value;
				} elseif ( preg_match( '/\s/', $value ) ) {
					$icon = $value;
				} else {
					$emoji = $value;
				}
				$icon_class          = ( $icon || $emoji ) ? '' : '_d_none';
				$image_class         = $image ? '' : '_d_none';
				$button_active_class = ( $icon || $image || $emoji ) ? '_d_none' : '';
				?>
                <div class="icon_image_selection_area _fd_column">
                    <input type="hidden" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $value ); ?>"/>
                    <div class="icon_item <?php echo esc_attr( $icon_class ); ?>">
                        <div class="_all_center"><span class="<?php echo esc_attr( $icon ); ?>" data-add-icon><?php echo esc_html( $emoji ); ?></span></div>
                        <span class="fas fa-times icon_close icon_delete" title="<?php esc_html_e( 'Remove Icon', 'abp-rentalforge' ); ?>"></span>
                    </div>
                    <div class="image_item <?php echo esc_attr( $image_class ); ?>">
                        <img class="_img_control" src="<?php echo esc_url( ABPRF_Function::get_image_url( '', $image, 'medium' ) ); ?>" alt="image">
                        <span class="fas fa-times icon_close image_delete" title="<?php esc_html_e( 'Remove Image', 'abp-rentalforge' ); ?>"></span>
                    </div>
                    <div class="image_icon_select_area <?php echo esc_attr( $button_active_class ); ?>">
                        <div class="_group_content_f_equal_w_full">
                            <button class="_btn_info_xs image_select" type="button"><span class="fas fa-image _fs_h6"></span></button>
                            <button class="_btn_info_xs icon_add" type="button" data-target-popup="#abprf_popup_icon"><span class="fas fa-icons _fs_h6"></span></button>
                        </div>
                    </div>
                </div>
				<?php
			}
			//=============static array================//
			public static function status_text( $status ): string {
				$status_array = wc_get_order_statuses();

				return array_key_exists( $status, $status_array ) ? $status_array[ $status ] : '';
			}
			public static function book_status_text( $key ) {
				$rules = [
					'0' => __( 'Pending', 'abp-rentalforge' ),
					'1' => __( 'Waiting', 'abp-rentalforge' ),
					'2' => __( 'In Rent', 'abp-rentalforge' ),
					'3' => __( 'Completed', 'abp-rentalforge' ),
					'4' => __( 'Delay', 'abp-rentalforge' ),
					'5' => __( 'Canceled', 'abp-rentalforge' )
				];
				$rules = apply_filters( 'abprf_filter_book_status_rule', $rules );

				return ! empty( $key ) && array_key_exists( $key, $rules ) ? $rules[ $key ] : $key;
			}
			public static function get_book_status( $order_id, $start_time, $end_time, $book_status ): int {
				$now = current_time( 'Y-m-d H:i:s' );
				if ( ! empty( $book_status ) && $book_status < 5 && $book_status > 0 ) {
					$_book_status = 0;
					if ( strtotime( $now ) < strtotime( $start_time ) ) {
						$_book_status = $book_status;
					} elseif ( strtotime( $now ) > strtotime( $start_time ) && strtotime( $now ) < strtotime( $end_time ) ) {
						$_book_status = 2;
					} elseif ( strtotime( $now ) > strtotime( $start_time ) && strtotime( $now ) > strtotime( $end_time ) ) {
						$_book_status = 3;
					}
					if ( $_book_status > $book_status ) {
						$book_status = $_book_status;
						global $wpdb;
						$table_name    = $wpdb->prefix . 'abprf_orders';
						$booking_lists = ABPRF_Query::get_booking_query( [ 'order_id' => $order_id ] );
						if ( ! empty( $booking_lists ) && is_array( $booking_lists ) ) {
							$data  = [
								'book_status' => intval( $book_status ),
								'updated_at' => current_time( 'Y-m-d H:i:s' )
							];
							$where = [ 'order_id' => (int) $order_id ];
							// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
							$wpdb->update( $table_name, $data, $where, [ '%s', '%s', '%s' ], [ '%d' ] );
						}
					}
				}

				return $book_status;
			}
			public static function week_day(): array {
				return [
					'monday' => __( 'Monday', 'abp-rentalforge' ),
					'tuesday' => __( 'Tuesday', 'abp-rentalforge' ),
					'wednesday' => __( 'Wednesday', 'abp-rentalforge' ),
					'thursday' => __( 'Thursday', 'abp-rentalforge' ),
					'friday' => __( 'Friday', 'abp-rentalforge' ),
					'saturday' => __( 'Saturday', 'abp-rentalforge' ),
					'sunday' => __( 'Sunday', 'abp-rentalforge' ),
				];
			}
			public static function date_option_rules(): array {
				$rules = [
					'weekend' => __( 'Weekend', 'abp-rentalforge' ),
					'specific_of_date' => __( 'Specific Off Dates', 'abp-rentalforge' ),
					'off_date_range' => __( 'Off Dates Range', 'abp-rentalforge' ),
					'special_on_dates' => __( 'Special On Dates', 'abp-rentalforge' ),
					'day_wise_time' => __( 'Operation Time day Wise', 'abp-rentalforge' ),
				];

				return apply_filters( 'abprf_filter_rent_rule', $rules );
			}
			public static function rent_rules_options(): array {
				$options    = ( ABPRF_On_Off['rent_rule'] ?? null ) ?: self::rent_rules_string();
				$options    = ! empty( $options ) ? explode( ',', $options ) : [];
				$rent_rules = [];
				foreach ( $options as $option ) {
					if ( ! empty( $option ) ) {
						$rent_rules[ $option ] = self::rent_rules( $option );
					}
				}

				return $rent_rules;
			}
			public static function rent_rules( $key = '' ) {
				$rules = [
					'hourly' => __( 'Hourly Rate', 'abp-rentalforge' ),
					'daily' => __( 'Daily Rate', 'abp-rentalforge' ),
					'multi_day' => __( 'Daily & Hourly Rate', 'abp-rentalforge' ),
					'monthly' => __( 'Monthly Rate', 'abp-rentalforge' ),
					'multi_month' => __( 'Monthly & Daily Rate', 'abp-rentalforge' )
				];
				$rules = apply_filters( 'abprf_filter_rent_rule', $rules );

				return ! empty( $key ) && array_key_exists( $key, $rules ) ? $rules[ $key ] : $rules;
			}
			public static function rent_rules_string() {
				return apply_filters( 'abprf_filter_rent_rule_string', 'hourly,daily,multi_day,monthly,multi_month' );
			}
			public static function per_rent_rules( $key = '' ) {
				$rules = [
					'hourly' => __( '/hr', 'abp-rentalforge' ),
					'daily' => __( '/day', 'abp-rentalforge' ),
					'multi_day' => __( '/day', 'abp-rentalforge' ),
					'monthly' => __( '/month', 'abp-rentalforge' ),
					'multi_month' => __( '/month', 'abp-rentalforge' )
				];
				$rules = apply_filters( 'abprf_filter_per_rent_rule', $rules );

				return ! empty( $key ) && array_key_exists( $key, $rules ) ? $rules[ $key ] : $rules;
			}
			public static function rent_rules_sin_plu( $key = '' ) {
				$rules = [
					'hourly' => [ 'sin' => __( 'Hour', 'abp-rentalforge' ), 'plu' => __( 'Hours', 'abp-rentalforge' ) ],
					'daily' => [ 'sin' => __( 'Day', 'abp-rentalforge' ), 'plu' => __( 'Days', 'abp-rentalforge' ) ],
					'multi_day' => [ 'sin' => __( 'Day', 'abp-rentalforge' ), 'plu' => __( 'Days', 'abp-rentalforge' ) ],
					'monthly' => [ 'sin' => __( 'Month', 'abp-rentalforge' ), 'plu' => __( 'Months', 'abp-rentalforge' ) ],
					'multi_month' => [ 'sin' => __( 'Month', 'abp-rentalforge' ), 'plu' => __( 'Months', 'abp-rentalforge' ) ]
				];
				$rules = apply_filters( 'abprf_filter_sin_plu_rent_rule', $rules );

				return ! empty( $key ) && array_key_exists( $key, $rules ) ? $rules[ $key ] : $rules;
			}
			public static function array_date_format(): array {
				$current_date = current_time( 'Y-m-d' );

				return [
					'yy-mm-dd' => $current_date,
					'yy/mm/dd' => date_i18n( 'Y/m/d', strtotime( $current_date ) ),
					'yy-dd-mm' => date_i18n( 'Y-d-m', strtotime( $current_date ) ),
					'yy/dd/mm' => date_i18n( 'Y/d/m', strtotime( $current_date ) ),
					'dd-mm-yy' => date_i18n( 'd-m-Y', strtotime( $current_date ) ),
					'dd/mm/yy' => date_i18n( 'd/m/Y', strtotime( $current_date ) ),
					'mm-dd-yy' => date_i18n( 'm-d-Y', strtotime( $current_date ) ),
					'mm/dd/yy' => date_i18n( 'm/d/Y', strtotime( $current_date ) ),
					'd M , yy' => date_i18n( 'j M , Y', strtotime( $current_date ) ),
					'D d M , yy' => date_i18n( 'D j M , Y', strtotime( $current_date ) ),
					'M d , yy' => date_i18n( 'M  j, Y', strtotime( $current_date ) ),
					'D M d , yy' => date_i18n( 'D M  j, Y', strtotime( $current_date ) ),
				];
			}
			public static function array_info( $key ) {
				$current_date = current_time( 'Y-m-d H:i' );
				$des          = array(
					'sub_title' => __( 'Note: Add a Sub-title to enable the Post sub-tile. Leave this blank if you dont want to show any Sub-title information for this Post.', 'abp-rentalforge' ),
					'rent_continue' => __( 'Note: This switch indicate property rent close/continue . You can  rent close/continue  by this switch. By default rent will be  continue', 'abp-rentalforge' ),
					'post_sku' => __( 'Note: Here you can add an SKU for this post. You can also show or hide it on the frontend by turning the switch On or Off.', 'abp-rentalforge' ),
					'abprf_template' => __( 'Note: Here You can change your details page template.', 'abp-rentalforge' ),
					'display_category' => __( 'Note : This switch indicate Post/Property Category . You can also show or hide it on the frontend by turning the switch On or Off.', 'abp-rentalforge' ),
					'display_location' => __( 'Note : Enable or disable store locations on the frontend using this switch. If multiple locations are selected, property stock will be managed separately for each location.', 'abp-rentalforge' ),
					'cat_name' => __( 'Note: Please enter a category name — the field cannot be empty. ', 'abp-rentalforge' ),
					'cat_slug' => __( 'Note: Category slug is optional — leave it blank to auto-generate from the name. ', 'abp-rentalforge' ),
					'cat_des' => __( 'Note: Category description is optional — you can add details to better explain this category. ', 'abp-rentalforge' ),
					'loc_name' => __( 'Note: Please enter a Location name — the field cannot be empty. ', 'abp-rentalforge' ),
					'loc_slug' => __( 'Note: Location slug is optional — leave it blank to auto-generate from the name. ', 'abp-rentalforge' ),
					'loc_des' => __( 'Note: Location Address is optional — you can add details to better explain this Location Full  Address. ', 'abp-rentalforge' ),
					'feature_name' => __( 'Note: Please enter a Feature Label — the field cannot be empty. ', 'abp-rentalforge' ),
					'feature_slug' => __( 'Note: Feature slug is optional — leave it blank to auto-generate from the name. ', 'abp-rentalforge' ),
					'feature_des' => __( 'Note: Please enter a Feature Value  — the field cannot be empty. ', 'abp-rentalforge' ),
					//=============================//
					'date_format' => __( 'Note:  If you want to change the Date  Format, simply choose a different format. The default date is: ', 'abp-rentalforge' ) . ' ' . date_i18n( 'D j M , Y', strtotime( $current_date ) ),
					'time_format' => __( 'Note : If you want to change the Time Format, simply choose a different format. The default Time Format is: ', 'abp-rentalforge' ) . ' ' . date_i18n( get_option( 'time_format' ), strtotime( $current_date ) ),
					'sale_close_before' => __( 'Note: Enter the time in minutes to close  rent before current time. If not specified, it will default to 0 (e.g. 1 hour equals 60 minutes).', 'abp-rentalforge' ),
					'sale_close_after' => __( 'Note: Enter the time in minutes to close  rent after current time. If not specified, it will default to 0 (e.g. 1 hour equals 60 minutes).', 'abp-rentalforge' ),
					'advance_date_number' => __( 'Note: Kindly provide the number of days in advance for booking. By default, the advance booking period is set to 28 days.(optional) ', 'abp-rentalforge' ),
					'active_global_dates' => __( 'Note: Keep this switch ON to apply the global date settings.Switch it OFF if you want to set special date rules for this property.Date configuration options will open when turned OFF. ', 'abp-rentalforge' ),
					'date_type' => __( 'Note: Please Select your property operational date type. Default operational date will be Periodic', 'abp-rentalforge' ),
					'specific_dates' => __( 'Note: Please add your property operational Specific Date lists and Operation time length(optional). If operation time empty that means it will be default operation time.', 'abp-rentalforge' ),
					'operation_time' => __( 'Note: Please add your property rent  Operation time length(optional). If operation time empty that means it will be 24 hours(optional)', 'abp-rentalforge' ),
					'periodic_start_date' => __( 'Note: Please add your property rent Launching Date otherwise it will be start today ', 'abp-rentalforge' ),
					'periodic_end_date' => __( 'Note: Please add your property rent Terminate  Date otherwise it will be Continuously running periodically', 'abp-rentalforge' ),
					'periodic_after' => __( 'Note: Please add your periodically after days. if  your property rent operation day everyday this will be one(1).(optional)', 'abp-rentalforge' ),
					'date_rule' => __( 'Note: Enable this checkbox to configure special on/off date and time settings. This option is optional. If you set a date/time in the special “On” date, that date will remain active even if it falls within an “Off” date range or on weekends.', 'abp-rentalforge' ),
					'special_on_dates' => __( 'Note: If you add any date and time in Special On Dates, it will always remain active—even if that date falls within an off date range or on weekends.', 'abp-rentalforge' ),
					'weekend' => __( 'Note: Please select your weekend.Default all days open(optional)', 'abp-rentalforge' ),
					'day_wise_time' => __( 'Note: Day-wise operation time will apply only if the date does not fall within any Special On Date range. If the time field is left empty in Special On Dates, then the day-wise operation time will be applied for that date.', 'abp-rentalforge' ),
					'specific_off_dates' => __( 'Note: please add your specific Operation off dates.(optional)', 'abp-rentalforge' ),
					'off_date_range' => __( 'Note: If you have off days between two dates which can add here.(optional)', 'abp-rentalforge' ),
					'abprf_dates' => __( 'Note: Set a global date configuration for your property rentals that can be reused across all posts, with options to import and customize anytime.', 'abp-rentalforge' ),
					//=============================//
					'post_id' => __( 'Note: You must select the Post under which this property belongs here. Selecting a Post is required — the data will not be saved if no Post is selected.', 'abp-rentalforge' ),
					'name' => __( 'Note: You must enter the property name in the field above. This field is required — the data will not be saved if the property name is not provided.', 'abp-rentalforge' ),
					'icon' => __( 'Note: Here You can set an image, icon, or emoji for each property directly', 'abp-rentalforge' ),
					'qty_reserve_min_max' => __( 'Note: Set the total stock quantity available for rent. This field is required to save the property. You can also set reserve, minimum, and maximum quantity limits for customer bookings. Reserve quantity keeps specific items unavailable, minimum quantity defaults to 1, and maximum quantity will follow the available stock if left empty.', 'abp-rentalforge' ),
					'hourly_min_max' => __( 'Note: Enter the hourly rental rate to enable hourly booking for this property. You can also set minimum and maximum rental hours for customers. The default minimum is 1 hour, while the maximum will follow available time slots if left empty.', 'abp-rentalforge' ),
					'daily_min_max' => __( 'Note: Enter the daily rental rate to enable daily booking for this property.  You can also set minimum and maximum rental days for customers. The minimum defaults to 1 day if left empty, while the maximum depends on available booking dates. If no daily rate is provided, daily rental will remain disabled.', 'abp-rentalforge' ),
					'monthly_min_max' => __( 'Note: Enter the monthly rental rate to enable monthly booking for this property. This rate will apply only for the Monthly rent rule. You can also set minimum and maximum rental months. The default minimum is 1 month, while the maximum depends on available booking months.', 'abp-rentalforge' ),
					'deposit_type' => __( 'Note: There are three(3) types of deposit options: Fixed Amount (a set deposit regardless of quantity), Percentage of Total Price (calculated based on the total rental cost), and Fixed Amount per Quantity (applied for each item rented).', 'abp-rentalforge' ),
					'brand' => __( 'Note: Add a brand name to enable the property sub-tile. Leave this blank if you dont want to show any brand information for this item.', 'abp-rentalforge' ),
					'description' => __( 'Note: Add short description about this property. Leave this blank if you dont want to show any property description for this item.', 'abp-rentalforge' ),
					'price_rule' => __( 'Note: Select the Property Date & Time Rule that matches this post. If an incorrect rule is chosen, the system will automatically update and apply the post Date & Time Rule to the property upon publishing.', 'abp-rentalforge' ),
					'property_feature' => __( 'Note: If you want to add feature for this property, you can add Here. These feature will be show with this properties . You may leave this section empty if you do not want to show frontend. ', 'abp-rentalforge' ),
					'abprf_sliders' => __( 'Note: If you want to add an image gallery for this property, you can upload images below. These images will be merged with all properties under the same category. You may leave this section empty if you do not want to add images. ', 'abp-rentalforge' ),
					'time_slot_length' => __( 'Note: You can define the time slot interval for frontend time selection here. This controls how frequently time options will appear for users. By default, it is set to 60 minutes, meaning time slots will be available in 1-hour intervals.', 'abp-rentalforge' ),
					'day_time_start_end' => __( 'Note: You can define the start and end time of a rental day here. By default, a rental day runs from 10:00 AM to 10:00 AM the next day. The first time applies to the start of the first day, and the second time applies to the end of the following day. The total duration between these times must not exceed 24 hours.', 'abp-rentalforge' ),
					'hour_threshold' => __( 'Note: You can define how many hours will be counted as one full day here. By default, it is set to 24 hours. Adjust this value to control when a booking duration should be considered as a full day.', 'abp-rentalforge' ),
					'cut_off_date' => __( 'Note: You can set the cutoff date for allowing bookings in the next month. By default, users can make bookings for the current month up to the 10th. After this date, next month’s rental slots will become available for booking.', 'abp-rentalforge' ),
					'day_threshold' => __( 'Note: You can define how many days will be counted as one full month here. By default, it is set to 30 days. Adjust this value to control when a booking duration should be considered as a full month.', 'abp-rentalforge' ),
					'rent_rule' => __( 'Note: The items displayed are filtered by your selected Rent Time Rules. Properties not matching these rules are still available via the main Property List.', 'abp-rentalforge' ),
					//=============================//
					'_tax_class' => __( 'Note: If you want to add any new tax class , Please go to WooCommerce ->configuration->Tax Area', 'abp-rentalforge' ),
					'enable_tax_msg' => __( 'Note: Your Woo-commerce Tax setting already disable. If you want to enable tax please enable woo-commerce tax.', 'abp-rentalforge' ),
					//=============================//
					'display_additional_services' => __( 'Note: If you want sale/rent additional product/equipment with regular property then active this button and add additional service. Additional item not depends on  operation time.', 'abp-rentalforge' ),
					'additional_services' => __( 'Note: Add extra services for products/equipment with your property—import or set per Post (also usable globally); stock applies per Post, empty quantity = unlimited, empty max qty = no limit, empty/Zero price = free.', 'abp-rentalforge' ),
					'active_global_additional' => __( 'Note: Keep this switch ON to apply the global additional settings.Switch it OFF if you want to set special additional rules for this property.additional configuration options will open when turned OFF. ', 'abp-rentalforge' ),
					//=============================//
					'client_form_option' => __( 'Use comma( , ) to separate option.', 'abp-rentalforge' ),
					'display_client_form' => __( 'Note: If you want to get Client information then active this button and add form/import global form or use global form as a client form', 'abp-rentalforge' ),
					'active_global_form' => __( 'Note: Keep this switch ON to apply the global Client Form settings.Switch it OFF if you want to set special  Client Form rules for this property. Client Form configuration options will open when turned OFF. ', 'abp-rentalforge' ),
					'global_client_forms' => __( 'Note: This is a flexibility global form system. Once you design the structure here, it serves as a global form. You can effortlessly import this form into any property or use this setting at any property,', 'abp-rentalforge' ),
					//=============================//
					'abprf_tc' => __( 'You can set all rental-related Term & Condition here and use them globally across all properties. You can also import these Term & Condition into any individual property and customize them as needed.', 'abp-rentalforge' ),
					'tc_item' => __( 'Use the editor to customize and design your Terms & Conditions as you prefer. The content and formatting you create here will be displayed the same way on the frontend.', 'abp-rentalforge' ),
					'display_tc' => __( 'Use this switch to control whether the Term & Condition is displayed on the frontend. Turn the switch ON to show the Term & Condition, and OFF to hide it. By default, this option is set to ON.', 'abp-rentalforge' ),
					'active_global_tc' => __( 'Enable this switch to apply the global Term & Condition to this post. If you want to add custom Term & Condition specifically for this post, turn the switch OFF and add your custom Term & Condition below.You can also use the Import button to bring in global Term & Condition, which you can then edit or delete based on your needs.', 'abp-rentalforge' ),
					//=============================//
					'abprf_faqs' => __( 'You can set all rental-related FAQs here and use them globally across all properties. You can also import these FAQs into any individual property and customize them as needed.', 'abp-rentalforge' ),
					'faq_item' => __( 'Both the Title and Description fields are required. If either field is left empty, this FAQ item will not be displayed on the frontend.', 'abp-rentalforge' ),
					'display_faq' => __( 'Use this switch to control whether the FAQ is displayed on the frontend. Turn the switch ON to show the FAQ, and OFF to hide it. By default, this option is set to ON.', 'abp-rentalforge' ),
					'active_global_faq' => __( 'Enable this switch to apply the global FAQ to this post. If you want to add custom FAQs specifically for this post, turn the switch OFF and add your custom FAQs below.You can also use the Import button to bring in global FAQs, which you can then edit or delete based on your needs.', 'abp-rentalforge' ),
					//=============================//
					'search_get_wrong_data_info' => __( 'Somethings went Wrong ! Please Try again', 'abp-rentalforge' ),
					'sale_close_msg' => __( 'This Property rent close shortly. please try another Property.', 'abp-rentalforge' ),
					'not_date' => __( 'No Dates Found !', 'abp-rentalforge' ),
					'not_match' => __( 'No Results Found !', 'abp-rentalforge' ),
					'not_found' => __( 'No Post Found !', 'abp-rentalforge' ),
					'not_post_found' => __( 'No Post Found !', 'abp-rentalforge' ),
					'not_property_found' => __( 'No Property Found !', 'abp-rentalforge' ),
					'no_category' => __( 'No Category Found ! Please add Category to use Category feature', 'abp-rentalforge' ),
					'no_brand' => __( 'No Brand Found ! Please add Brand to use Brand feature', 'abp-rentalforge' ),
					'no_location' => __( 'No Location Found ! Please add Location to use Location feature', 'abp-rentalforge' ),
					'no_feature' => __( 'No Feature Found ! Please add Feature to use Feature', 'abp-rentalforge' ),
					'property_not_available' => __( 'The property is not available for the selected date and time. Please choose a different schedule.', 'abp-rentalforge' ),
					//=============================//
					'must_wc' => __( 'RentalForge is entirely dependent on the WooCommerce plugin. Please install and activate the WooCommerce plugin otherwise the plugin will not work. Installing this tool may take some time', 'abp-rentalforge' ),
					//=============================//
					'display_pickup' => __( 'Here you can set Multiple Pickup Point . If you want visible Multiple Pickup point select option  , please switch on. default pickup point off', 'abp-rentalforge' ),
					'display_drop' => __( 'Here you can set Multiple Drop-off Point . If you want visible Multiple Drop-off point select option  , please switch on. default Drop-off point off', 'abp-rentalforge' ),
					//=============================//
					'sign_up_msg' => __( 'Please Login your account to Download/View ticket !', 'abp-rentalforge' ),
					'no_permit_msg' => __( 'You are not permitted to Download/View this ticket !', 'abp-rentalforge' ),
					'wrong_msg_id' => __( 'We see, this id are not valid !', 'abp-rentalforge' ),
					'no_property_found' => __( 'Property not found or  rent close shortly', 'abp-rentalforge' ),
					'no_order_found' => __( 'Sorry ! We can not find any Order in your criteria.', 'abp-rentalforge' ),
					//''          => __( '', 'abp-rentalforge' ),
				);
				$des          = apply_filters( 'abprf_info_array_filter', $des );

				return $des[ $key ]??'';
			}
			public static function static_form( $key = '' ): array {
				$form['pass_name']    = [ 'type' => 'text', 'required' => 'on', 'label' => __( 'First Name', 'abp-rentalforge' ) ];
				$form['pass_name_2']  = [ 'type' => 'text', 'required' => 'on', 'label' => __( 'Last Name', 'abp-rentalforge' ) ];
				$form['pass_email']   = [ 'type' => 'email', 'required' => 'on', 'label' => __( 'E-Mail', 'abp-rentalforge' ) ];
				$form['pass_phone']   = [ 'type' => 'text', 'required' => 'on', 'label' => __( 'Phone', 'abp-rentalforge' ) ];
				$form['pass_gender']  = [ 'type' => 'select', 'required' => 'off', 'label' => __( 'Gender', 'abp-rentalforge' ), 'option' => 'male,female' ];
				$form['pass_date']    = [ 'type' => 'date', 'required' => 'off', 'label' => __( 'Date of Birth', 'abp-rentalforge' ) ];
				$form['pass_address'] = [ 'type' => 'textarea', 'required' => 'off', 'label' => __( 'Address', 'abp-rentalforge' ) ];

				return $key && array_key_exists( $key, $form ) ? $form[ $key ] : $form;
			}
			public static function static_additional(): array {
				return [
					'additional_service_1' => [ 'icon' => 'fas fa-helmet-un', 'name' => 'Helmet', 'qty' => 50, 'max_qty' => 1, 'price' => 0, 'returnable' => 'yes', 'description' => '1x Safety Helmet per order. Keep your head protected at no extra cost. Your safety is our priority!', ],
					'additional_service_2' => [ 'icon' => 'fas fa-suitcase', 'name' => 'Storage', 'qty' => 30, 'max_qty' => 3, 'price' => 2.99, 'returnable' => 'no', 'description' => 'Optional baggage support is available as a paid service to help carry your essentials with ease.', ],
					'additional_service_3' => [ 'icon' => 'fas fa-user-tie', 'name' => 'Tie', 'qty' => 100, 'price' => 1.00, 'returnable' => 'no', 'description' => 'Multiple color available', ],
					'additional_service_4' => [ 'icon' => 'fas fa-shoe-prints', 'name' => 'Shoes', 'qty' => 100, 'price' => 1.00, 'returnable' => 'yes', 'description' => 'Multiple Size available', ]
				];
			}
			public static function static_feature(): array {
				return [
					'fec_id_1' => [ 'icon' => '⛽', 'label' => 'Fuel Type', 'value' => 'Electric' ],
					'fec_id_2' => [ 'icon' => '⛽', 'label' => 'Fuel Type', 'value' => ' Hybrid' ],
					'fec_id_3' => [ 'icon' => '⛽', 'label' => 'Fuel Type', 'value' => 'Diesel' ],
					'fec_id_4' => [ 'icon' => '⛽', 'label' => 'Fuel Type', 'value' => 'Petrol' ],
					'fec_id_5' => [ 'icon' => 'fas fa-cog', 'label' => 'Transmission', 'value' => 'Automatic' ],
					'fec_id_6' => [ 'icon' => 'fas fa-tachometer-alt', 'label' => 'Mileage', 'value' => '15 km/L' ],
					'fec_id_7' => [ 'icon' => 'fas fa-tachometer-alt', 'label' => 'Mileage', 'value' => '40 km/L' ],
					'fec_id_8' => [ 'icon' => 'fas fa-tachometer-alt', 'label' => 'Mileage', 'value' => '10 km/L' ],
					'fec_id_9' => [ 'icon' => '⚡', 'label' => 'Performance', 'value' => 'High efficiency & smooth driving systems' ],
					'fec_id_10' => [ 'icon' => '🛡️', 'label' => 'Safety', 'value' => 'ABS, Airbags, ADAS systems' ],
					'fec_id_11' => [ 'icon' => '🧭', 'label' => 'Navigation', 'value' => 'Smart GPS & route optimization' ],
					'fec_id_12' => [ 'icon' => 'fas fa-users', 'label' => 'Capacity', 'value' => '4 Persons' ],
					'fec_id_13' => [ 'icon' => 'fas fa-users', 'label' => 'Capacity', 'value' => '5 Seater' ],
					'fec_id_14' => [ 'icon' => 'fas fa-suitcase', 'label' => 'Boot Space', 'value' => '400L' ],
					'fec_id_15' => [ 'icon' => '📸', 'label' => 'Sensor Quality', 'value' => 'Full-frame / APS-C high resolution sensors' ],
					'fec_id_16' => [ 'icon' => '🎥', 'label' => 'Resolution', 'value' => '4K / 8K cinematic recording' ],
					'fec_id_17' => [ 'icon' => '🎥', 'label' => 'Resolution', 'value' => '24 MP' ],
					'fec_id_18' => [ 'icon' => '🎥', 'label' => 'Resolution', 'value' => '4K UHD' ],
					'fec_id_19' => [ 'icon' => '🔍', 'label' => 'Lens System', 'value' => 'Interchangeable professional lenses' ],
					'fec_id_20' => [ 'icon' => '⚙️', 'label' => 'Autofocus', 'value' => 'Fast AI-based tracking focus system' ],
					'fec_id_21' => [ 'icon' => '🔋', 'label' => 'Battery Life', 'value' => 'Long-lasting rechargeable battery system' ],
					'fec_id_22' => [ 'icon' => 'fas fa-search-plus', 'label' => 'Zoom', 'value' => '30x Optical' ],
					'fec_id_23' => [ 'icon' => 'fas fa-battery-full', 'label' => 'Battery Life', 'value' => '500 Shots' ],
					'fec_id_24' => [ 'icon' => 'fas fa-wifi', 'label' => 'Connectivity', 'value' => 'Built-in Wi-Fi/NFC' ],
					'fec_id_25' => [ 'icon' => '🔊', 'label' => 'Sound', 'value' => 'High-power bass boosted speakers' ],
					'fec_id_26' => [ 'icon' => '🔊', 'label' => 'Sound', 'value' => '1000W' ],
					'fec_id_27' => [ 'icon' => '🎚️', 'label' => 'DJ Control', 'value' => 'Professional mixers & controllers support' ],
					'fec_id_28' => [ 'icon' => '💡', 'label' => 'Lighting', 'value' => 'RGB LED & stage lighting systems' ],
					'fec_id_29' => [ 'icon' => '💡', 'label' => 'Lighting', 'value' => 'LED RGB' ],
					'fec_id_30' => [ 'icon' => '📡', 'label' => 'Connectivity', 'value' => 'Bluetooth / USB / AUX / Wireless sync' ],
					'fec_id_31' => [ 'icon' => '🔌', 'label' => 'Power Efficiency', 'value' => 'Energy-efficient high-output systems' ],
					'fec_id_32' => [ 'icon' => 'fas fa-battery-full', 'label' => 'Battery Life', 'value' => 'Up to 12 Hours' ],
					'fec_id_33' => [ 'icon' => 'fas fa-microphone', 'label' => 'Input', 'value' => 'Wireless Mic Support' ],
					'fec_id_34' => [ 'icon' => 'fas fa-water', 'label' => 'Durability', 'value' => 'Splash Proof (IPX4)' ],
					'fec_id_35' => [ 'icon' => '🚁', 'label' => 'Flight Stability', 'value' => 'GPS-based stable hovering system' ],
					'fec_id_36' => [ 'icon' => '🧠', 'label' => 'AI Features', 'value' => 'Smart tracking & auto flight modes' ],
					'fec_id_37' => [ 'icon' => '🔋', 'label' => 'Battery Range', 'value' => 'Extended flight time with fast charging' ],
					'fec_id_38' => [ 'icon' => '📡', 'label' => 'Range', 'value' => '15KM' ],
					'fec_id_39' => [ 'icon' => 'fas fa-clock', 'label' => 'Flight Time', 'value' => '30 Minutes' ],
					'fec_id_40' => [ 'icon' => '📡', 'label' => 'Range', 'value' => '5 km' ],
					'fec_id_41' => [ 'icon' => 'fas fa-camera', 'label' => 'Camera', 'value' => '4K / 12MP' ],
					'fec_id_42' => [ 'icon' => 'fas fa-wind', 'label' => 'Wind Resistance', 'value' => 'Level 5' ],
					'fec_id_43' => [ 'icon' => 'fas fa-weight-hanging', 'label' => 'Max Payload', 'value' => '500g' ],
					'fec_id_44' => [ 'icon' => 'fas fa-clock', 'label' => 'Flight Time', 'value' => '30-40 Mins' ],
					'fec_id_45' => [ 'icon' => 'fas fa-tachometer-alt', 'label' => 'Max Speed', 'value' => '70 KM/H' ],
					'fec_id_46' => [ 'icon' => 'fas fa-eye', 'label' => 'Obstacle Avoidance', 'value' => 'Omnidirectional' ],
					'fec_id_47' => [ 'icon' => 'fas fa-weight-hanging', 'label' => 'Takeoff Weight', 'value' => '249g' ],
					'fec_id_48' => [ 'icon' => '🏝️', 'label' => 'Location', 'value' => 'Beachfront / Mountain / Island premium spots' ],
					'fec_id_49' => [ 'icon' => '🏨', 'label' => 'Luxury Level', 'value' => '5-star ultra luxury hospitality service' ],
					'fec_id_50' => [ 'icon' => '🍽️', 'label' => 'Dining', 'value' => 'International gourmet food experience' ],
					'fec_id_51' => [ 'icon' => '🧖', 'label' => 'Facilities', 'value' => 'Spa, pool, gym, wellness center' ],
					'fec_id_52' => [ 'icon' => '🌅', 'label' => 'Experience', 'value' => 'Scenic views & premium relaxation environment' ],
					'fec_id_53' => [ 'icon' => 'fas fa-bed', 'label' => 'Room Type', 'value' => 'Deluxe Suite' ],
					'fec_id_54' => [ 'icon' => 'fas fa-swimming-pool', 'label' => 'Pool', 'value' => 'Infinity Pool' ],
					'fec_id_55' => [ 'icon' => 'fas fa-utensils', 'label' => 'Meals', 'value' => 'All Inclusive' ],
					'fec_id_56' => [ 'icon' => 'fas fa-wifi', 'label' => 'WiFi', 'value' => 'Free / High Speed' ],
					'fec_id_57' => [ 'icon' => 'fas fa-concierge-bell', 'label' => 'Service', 'value' => '24/7 Concierge' ],
					'fec_id_58' => [ 'icon' => 'fas fa-swimming-pool', 'label' => 'Pool', 'value' => 'Private Infinity Pool' ],
					'fec_id_59' => [ 'icon' => 'fas fa-utensils', 'label' => 'Dining', 'value' => 'Fine Dining Included' ],
					'fec_id_60' => [ 'icon' => 'fas fa-spa', 'label' => 'Wellness', 'value' => 'Luxury Spa & Sauna' ],
					'fec_id_61' => [ 'icon' => 'fas fa-mountain', 'label' => 'View', 'value' => 'Ocean Front' ],
					'fec_id_62' => [ 'icon' => '🧳', 'label' => 'Durability', 'value' => 'Shock-resistant strong build quality' ],
					'fec_id_63' => [ 'icon' => '🎒', 'label' => 'Portability', 'value' => 'Lightweight and easy carry design' ],
					'fec_id_64' => [ 'icon' => '🔒', 'label' => 'Security', 'value' => 'TSA lock & anti-theft protection' ],
					'fec_id_65' => [ 'icon' => '💼', 'label' => 'Storage Capacity', 'value' => 'Expandable smart packing space' ],
					'fec_id_66' => [ 'icon' => '⚙️', 'label' => 'Build Material', 'value' => 'Polycarbonate / premium fabric construction' ],
					'fec_id_67' => [ 'icon' => 'fas fa-weight', 'label' => 'Weight', 'value' => '2.5 kg' ],
					'fec_id_68' => [ 'icon' => 'fas fa-box', 'label' => 'Storage Capacity', 'value' => '60 Liters' ],
					'fec_id_69' => [ 'icon' => 'fas fa-tint-slash', 'label' => 'Waterproof', 'value' => 'IPX6 Rated' ],
					'fec_id_70' => [ 'icon' => 'fas fa-expand-arrows-alt', 'label' => 'Dimensions', 'value' => '70×35×25 cm' ],
					'fec_id_71' => [ 'icon' => 'fas fa-lock', 'label' => 'Security Lock', 'value' => 'TSA Approved' ],
					'fec_id_72' => [ 'icon' => 'fas fa-feather', 'label' => 'Weight', 'value' => 'Ultra Lightweight' ],
					'fec_id_73' => [ 'icon' => 'fas fa-shield-virus', 'label' => 'Material', 'value' => 'Waterproof Carbon Fiber' ],
					'fec_id_74' => [ 'icon' => 'fas fa-lock', 'label' => 'Security', 'value' => 'TSA Approved Lock' ],
					'fec_id_75' => [ 'icon' => 'fas fa-expand', 'label' => 'Space', 'value' => 'Expandable Capacity' ],
					'fec_id_76' => [ 'icon' => 'fas fa-compass', 'label' => 'Utility', 'value' => 'Smart Tracking GPS' ],
					'fec_id_77' => [ 'icon' => '🏅', 'label' => 'Performance', 'value' => 'Professional-grade sports efficiency' ],
					'fec_id_78' => [ 'icon' => '⚽', 'label' => 'Multi-sport Use', 'value' => 'Suitable for multiple sports categories' ],
					'fec_id_79' => [ 'icon' => '🛡️', 'label' => 'Safety', 'value' => 'Injury protection & ergonomic design' ],
					'fec_id_80' => [ 'icon' => '🏋️', 'label' => 'Strength Build', 'value' => 'High durability & impact resistance' ],
					'fec_id_81' => [ 'icon' => '🎯', 'label' => 'Precision', 'value' => 'Accurate control & performance tuning' ],
					'fec_id_82' => [ 'icon' => 'fas fa-dumbbell', 'label' => 'Equipment Type', 'value' => 'Multi-Use' ],
					'fec_id_83' => [ 'icon' => 'fas fa-weight-hanging', 'label' => 'Max Load', 'value' => '150 kg' ],
					'fec_id_84' => [ 'icon' => 'fas fa-ruler', 'label' => 'Size', 'value' => 'Adjustable' ],
					'fec_id_85' => [ 'icon' => 'fas fa-tools', 'label' => 'Material', 'value' => 'Steel Alloy' ],
					'fec_id_86' => [ 'icon' => '🏅', 'label' => 'Skill Level', 'value' => 'All Levels' ],
					'fec_id_87' => [ 'icon' => 'fas fa-running', 'label' => 'Performance', 'value' => 'Pro-Grade Grip' ],
					'fec_id_88' => [ 'icon' => 'fas fa-heartbeat', 'label' => 'Tracking', 'value' => 'Built-in Heart Sensors' ],
					'fec_id_89' => [ 'icon' => 'fas fa-dumbbell', 'label' => 'Strength', 'value' => 'Heavy-Duty Build' ],
					'fec_id_90' => [ 'icon' => 'fas fa-wind', 'label' => 'Aerodynamics', 'value' => 'High Speed Optimized' ],
					'fec_id_91' => [ 'icon' => 'fas fa-check-circle', 'label' => 'Certification', 'value' => 'FIFA/Olympic Standard' ],
				];
			}
			//=============================//
			public static function location_select( $post_id = '', $location = '' ): void {
				if ( ABPRF_Function::on_off( 'location' ) ) {
					$all_locations = ABPRF_Locations;
					if ( ! empty( $all_locations ) ) {
						if ( ! empty( $post_id ) ) {
							$location_array = ! empty( $location ) ? explode( ',', $location ) : [];
							if ( ! empty( $location_array ) ) {
								if ( sizeof( $location_array ) > 1 ) {
									?>
                                    <div class="_input_item">
                                        <label>
                                            <span><i class="fas fa-location _mar_r_xxs"></i><?php esc_html_e( 'Location', 'abp-rentalforge' ); ?><sup class="_color_required">*</sup></span>
                                            <select class="_form_control" name="location">
												<?php foreach ( $location_array as $loc_id ) {
													if ( in_array( $loc_id, $location_array ) ) {
														?>
                                                        <option value="<?php echo esc_attr( $loc_id ); ?>"><?php echo esc_html( array_key_exists( $loc_id, $all_locations ) && array_key_exists( 'name', $all_locations[ $loc_id ] ) ? $all_locations[ $loc_id ]['name'] : '' ); ?></option>
													<?php }
												} ?>
                                            </select>
                                        </label>
                                    </div>
									<?php
								} else {
									?><input type="hidden" name="location" value="<?php echo esc_attr( $location ); ?>" /><?php
								}
							}
						}
					}
				}
			}
			public static function rent_start_month( $all_dates ): void {
				if ( sizeof( $all_dates ) > 0 ) {
					?>
                    <label>
                        <span><i class="fas fa-calendar-check _mar_r_xxs"></i><?php esc_html_e( 'Pickup Month', 'abp-rentalforge' ); ?><sup class="_color_required">*</sup></span>
                        <select name="rent_start_date" class="_form_control">
                            <option value=""><?php esc_html_e( 'Select Pickup Month', 'abp-rentalforge' ); ?></option>
							<?php foreach ( $all_dates as $option ) { ?>
                                <option value="<?php echo esc_attr( $option['value'] ); ?>">
									<?php echo esc_html( $option['label'] ); ?>
                                </option>
							<?php } ?>
                        </select>
                    </label>
					<?php
				} else {
					esc_html_e( 'Month Configuration not complete', 'abp-rentalforge' );
				}
			}
			public static function rent_end_month( $post_id, $start_date ): void {
				$all_dates = ABPRF_Function::get_end_month( $post_id, $start_date );
				//echo '<pre>';print_r($all_dates);echo '</pre>';
				if ( sizeof( $all_dates ) > 0 ) {
					?>
                    <label>
                        <span><i class="fas fa-calendar-check _mar_r_xxs"></i><?php esc_html_e( 'Drop-Off Month', 'abp-rentalforge' ); ?><sup class="_color_required">*</sup></span>
                        <select name="rent_end_date" class="_form_control">
                            <option value=""><?php esc_html_e( 'Select Drop-Off Month', 'abp-rentalforge' ); ?></option>
							<?php foreach ( $all_dates as $option ) { ?>
                                <option value="<?php echo esc_attr( $option['value'] ); ?>">
									<?php echo esc_html( $option['label'] ); ?>
                                </option>
							<?php } ?>
                        </select>
                    </label>
					<?php
				} else {
					esc_html_e( 'Month Configuration not complete', 'abp-rentalforge' );
				}
			}
			public static function rent_start_date( $all_dates, $date = '', $post_id = '' ): void {
				//echo '<pre>';print_r($all_dates);					echo '</pre>';
				if ( sizeof( $all_dates ) > 0 ) {
					$date_format = ABPRF_Function::date_format_php();
					$now         = date_i18n( $date_format, strtotime( current_time( 'Y-m-d' ) ) );
					$date        = $date ?: current( $all_dates );
					//if ( sizeof( $all_dates ) > 10 ) {
					$hidden_date  = ! empty( $date ) ? gmdate( 'Y-m-d', strtotime( $date ) ) : '';
					$visible_date = ! empty( $date ) ? date_i18n( $date_format, strtotime( $date ) ) : '';
					?>
                    <label>
                        <span>📆<i class="_mar_r_xxs"></i><?php esc_html_e( 'Pickup Date', 'abp-rentalforge' ); ?><sup class="_color_required">*</sup></span>
                        <input type="hidden" name="rent_start_date" value="<?php echo esc_attr( $hidden_date ); ?>" required/>
                        <input id="start_date" type="text" value="<?php echo esc_attr( $visible_date ); ?>" class="_form_control" placeholder="<?php echo esc_attr( $now ); ?>" data-alert="<?php esc_attr_e( 'Please Select Pickup Date', 'abp-rentalforge' ); ?>" readonly required/>
                        <span class="fas fa-times date_close_icon" title="<?php esc_attr_e( 'Clear Date', 'abp-rentalforge' ); ?>"></span>
                    </label>
					<?php
					do_action( 'abprf_load_date_picker', '#start_date', $all_dates );
					//}
				} else {
					if ( ! empty( $post_id ) ) {
						ABPRF_Layout::layout_warning_info_xs( 'not_date' );
					} else {
						$date_format = ABPRF_Function::date_format_php();
						$now         = date_i18n( $date_format, strtotime( current_time( 'Y-m-d' ) ) );
						?>
                        <label>
                            <span>📆<i class="_mar_r_xxs"></i><?php esc_html_e( 'Pickup Date', 'abp-rentalforge' ); ?><sup class="_color_required">*</sup></span>
                            <input type="hidden" name="rent_start_date" value="" required/>
                            <input type="text" id="start_date" value="" class="_form_control abp_datepicker" placeholder="<?php echo esc_attr( $now ); ?>" readonly/>
                            <span class="fas fa-times date_close_icon" title="<?php esc_attr_e( 'Clear Date', 'abp-rentalforge' ); ?>"></span>
                        </label>
						<?php
					}
				}
			}
			public static function rent_end_date( $all_dates, $post_id = '' ): void {
				$date_format = ABPRF_Function::date_format_php();
				$now         = date_i18n( $date_format, strtotime( current_time( 'Y-m-d' ) ) );
				if ( sizeof( $all_dates ) > 0 ) {
					$date = current( $all_dates );
					//if ( sizeof( $all_dates ) > 10 ) {
					$hidden_date  = ! empty( $date ) ? gmdate( 'Y-m-d', strtotime( $date ) ) : '';
					$visible_date = ! empty( $date ) ? date_i18n( $date_format, strtotime( $date ) ) : '';
					?>
                    <label>
                        <span>🗓️<i class=" _mar_r_xxs"></i><?php esc_html_e( 'Drop-Off Date', 'abp-rentalforge' ); ?><sup class="_color_required">*</sup></span>
                        <input type="hidden" name="rent_end_date" value="<?php echo esc_attr( $hidden_date ); ?>" required/>
                        <input id="end_date" type="text" value="<?php echo esc_attr( $visible_date ); ?>" class="_form_control" placeholder="<?php echo esc_attr( $now ); ?>" data-alert="<?php esc_attr_e( 'Please Select Drop-Off  Date', 'abp-rentalforge' ); ?>" readonly required/>
                        <span class="fas fa-times date_close_icon" title="<?php esc_attr_e( 'Clear Date', 'abp-rentalforge' ); ?>"></span>
                    </label>
					<?php
					do_action( 'abprf_load_date_picker', '#end_date', $all_dates );
					//}
				} else {
					if ( ! empty( $post_id ) ) {
						ABPRF_Layout::layout_warning_info_xs( 'not_date' );
					} else {
						$date_format = ABPRF_Function::date_format_php();
						$now         = date_i18n( $date_format, strtotime( current_time( 'Y-m-d' ) ) );
						?>
                        <label>
                            <span>🗓️<i class=" _mar_r_xxs"></i><?php esc_html_e( 'Drop-Off Date', 'abp-rentalforge' ); ?><sup class="_color_required">*</sup></span>
                            <input type="hidden" name="rent_end_date" value="" required/>
                            <input type="text" id="end_date" value="" class="_form_control abp_datepicker" placeholder="<?php echo esc_attr( $now ); ?>" readonly/>
                            <span class="fas fa-times date_close_icon" title="<?php esc_attr_e( 'Clear Date', 'abp-rentalforge' ); ?>"></span>
                        </label>
						<?php
					}
				}
			}
			public static function title( $post_id ): void {
				$post_sku = ABPRF_Function::get_post_info( $post_id, 'post_sku' );
				echo esc_html( get_the_title( $post_id ) ); ?>
                <p class="_abprf">
					<?php if ( ! empty( $post_sku ) ) { ?>
                        <small class=" _abprf_color_gray"><?php echo esc_html__( 'SKU : ', 'abp-rentalforge' ) . esc_html( $post_sku ); ?></small>
					<?php } ?>
                </p>
				<?php
			}
			public static function item_feature( $features = '' ): void {
				$features      = ! empty( $features ) ? explode( ',', $features ) : [];
				$abprf_feature = ABPRF_Features;
				if ( ! empty( $features ) && is_array( $features ) && sizeof( $features ) > 0 ) {
					$unique_id = 'abprf_fea_' . uniqid();
					$count     = 0;
					?>
                    <div class="item_spec">
						<?php foreach ( $features as $fec_id ) {
							if ( is_array( $abprf_feature ) && array_key_exists( $fec_id, $abprf_feature ) ) {
								$feature = $abprf_feature[ $fec_id ];
								$label   = is_array( $feature ) && array_key_exists( 'label', $feature ) ? $feature['label'] : '';
								$value   = is_array( $feature ) && array_key_exists( 'value', $feature ) ? $feature['value'] : '';
								$icon    = is_array( $feature ) && array_key_exists( 'icon', $feature ) ? $feature['icon'] : '';
								if ( $value ) { ?>
                                    <span class="spec_badge" title="<?php echo esc_attr( $label ); ?>" <?php if ( $count > 3 ) { ?> data-collapse="<?php echo esc_attr( $unique_id ); ?>" <?php } ?>><?php ABPRF_Layout::image_icon( $icon, '_mar_r_xxs' ); ?><?php echo esc_html( $value ); ?></span>
									<?php $count ++;
								}
							}
						}
							if ( $count > 4 ) { ?>
                                <button type="button" class="_btn_info_xxs" data-collapse-target="<?php echo esc_attr( $unique_id ); ?>" data-open-text="- <?php echo esc_attr( $count - 4 ); ?> <?php esc_attr_e( 'Less', 'abp-rentalforge' ); ?>" data-close-text="+ <?php echo esc_attr( $count - 4 ); ?> <?php esc_attr_e( 'More', 'abp-rentalforge' ); ?>">
                                    <span data-text> + <?php echo esc_attr( $count - 4 ); ?><?php esc_html_e( 'More', 'abp-rentalforge' ); ?></span>
                                </button>
							<?php } ?>
                    </div>
				<?php }
			}
			public static function item_condition( $rent_rule, $price_info ): string {
				$condition = '';
				$min       = array_key_exists( 'min', $price_info ) ? $price_info['min'] : '';
				$max       = array_key_exists( 'max', $price_info ) ? $price_info['max'] : '';
				if ( ! empty( $min ) || ! empty( $max ) ) {
					$rule_info = self::rent_rules_sin_plu( $rent_rule );
					$sin_text  = ! empty( $rule_info['sin'] ) ? $rule_info['sin'] : '';
					$plu_text  = ! empty( $rule_info['plu'] ) ? $rule_info['plu'] : '';
					$unit_text = ( 1 === (int) $min ) ? $sin_text : $plu_text;
					if ( $min == $max ) {
						$condition .= sprintf(
						// translators: 1: minimum number, 2: time unit (e.g. hours)
							__( 'Rental is available for %1$s %2$s Only', 'abp-rentalforge' ),
							$min,
							$unit_text
						);
					} else {
						$condition .= '📉 ';
						$condition .= sprintf(
						// translators: 1: The minimum number, 2: The unit text (e.g., "Hours").
							__( 'Min. %1$s %2$s', 'abp-rentalforge' ), $min, $unit_text );
						if ( ! empty( $max ) ) {
							$condition .= '  📈  ';
							$condition .= sprintf(
							// translators: 1: The minimum number, 2: The unit text (e.g., "Hours").
								__( 'Max. %1$s %2$s', 'abp-rentalforge' ), $max, $unit_text );
						}
					}
				} else {
					$text = self::rent_rules( $rent_rule );
					// translators: %s is the user role or restriction text .
					$condition .= sprintf( __( 'Rental is available for %s  only', 'abp-rentalforge' ), $text );
				}

				return $condition;
			}
			public static function item_deposit( $price_info ): void {
				$deposit_info  = array_key_exists( 'deposit', $price_info ) ? $price_info['deposit'] : [];
				$deposit_type  = is_array( $deposit_info ) && array_key_exists( 'type', $deposit_info ) ? $deposit_info['type'] : '';
				$deposit_value = is_array( $deposit_info ) && array_key_exists( 'value', $deposit_info ) ? $deposit_info['value'] : '';
				if ( ! empty( $deposit_type ) && ! empty( $deposit_value ) ) {
					?>
                    <div class="item_condition"><?php
					if ( $deposit_type == 'fixed' ) {
						echo wp_kses_post( sprintf(
						/* translators: %s = deposit label' */
							_x( '• Deposit: %s Fixed', 'deposit label', 'abp-rentalforge' ), wc_price( $deposit_value ) ) );
					} elseif ( $deposit_type == 'percent' ) {
						echo esc_html( sprintf(
						/* translators: %s = deposit label' */
							_x( '• Deposit: %s of Total Price', 'deposit label', 'abp-rentalforge' ), esc_html( $deposit_value . '%' ) ) );
					} else {
						echo wp_kses_post( sprintf(
						/* translators: %s = deposit label' */
							_x( '• Deposit: %s Per Item', 'deposit label', 'abp-rentalforge' ), wc_price( $deposit_value ) ) );
					}
					?></div><?php
				}
			}
			public static function item_price( $post_id, $rent_rule, $price_info ): void {
				?>
                <span class="price_label"><?php echo esc_html( ABPRF_Layout::rent_rules( $rent_rule ) ); ?></span>
                <span class="price_value">
                        <?php
	                        $price = array_key_exists( 'price', $price_info ) ? $price_info['price'] : '';
	                        $price = apply_filters( 'abprf_filter_price', $price, $rent_rule, $price_info );
	                        $price = ! empty( $price ) && $price > 0 ? ABPRF_Function::tax_with_price( $post_id, $price ) : 0;
	                        echo $price > 0 ? wp_kses_post( wc_price( $price ) ) : esc_html__( 'Free', 'abp-rentalforge' );
	                        echo esc_html( ABPRF_Layout::per_rent_rules( $rent_rule ) );
	                        if ( $rent_rule == 'multi_day' || $rent_rule == 'multi_month' ) {
		                        $price_multi = array_key_exists( 'price_multi', $price_info ) ? $price_info['price_multi'] : '';
		                        $price_multi = apply_filters( 'abprf_filter_price_multi', $price_multi, $rent_rule, $price_info );
		                        $price_multi = ! empty( $price_multi ) && $price_multi > 0 ? ABPRF_Function::tax_with_price( $post_id, $price_multi ) : 0;
		                        esc_html_e( ' & ', 'abp-rentalforge' );
		                        echo $price_multi > 0 ? wp_kses_post( wc_price( $price_multi ) ) : esc_html__( 'Free', 'abp-rentalforge' );
		                        echo $rent_rule == 'multi_day' ? esc_html( ABPRF_Layout::per_rent_rules( 'hourly' ) ) : esc_html( ABPRF_Layout::per_rent_rules( 'daily' ) );
	                        }
                        ?>
                    </span>
				<?php
			}
			public static function item_cost( $abprf_infos, $price_info, $total_price, $time_duration ): void {
				$rent_rule = array_key_exists( 'rent_rule', $abprf_infos ) ? $abprf_infos['rent_rule'] : '';
				$date_info = array_key_exists( 'date_info', $abprf_infos ) ? $abprf_infos['date_info'] : '';
				$dif_text  = ! empty( $date_info ) && array_key_exists( 'text', $date_info ) ? $date_info['text'] : '';
				?>
                <div class="calculated_cost">
					<?php if ( ! empty( $time_duration ) ) { ?>
                        <div class="cost_label"><?php echo esc_html__( 'Total for ', 'abp-rentalforge' ) . ' ' . esc_html( $dif_text ); ?></div>
                        <div class="cost_value">
							<?php echo $total_price > 0 ? wp_kses_post( wc_price( $total_price ) ) : esc_html__( 'Free ', 'abp-rentalforge' ); ?>
                        </div>
					<?php } else { ?>
                        <div class="cost_condition">
							<?php echo esc_html( ABPRF_Layout::item_condition( $rent_rule, $price_info ) ); ?>
                        </div>
					<?php } ?>
                </div>
				<?php
			}
			public static function item_select_property( $abprf_infos, $price_info, $total_price = 0 ): void {
				$post_id       = array_key_exists( 'post_id', $abprf_infos ) ? $abprf_infos['post_id'] : '';
				$property_id   = array_key_exists( 'property_id', $abprf_infos ) ? $abprf_infos['property_id'] : '';
				$name          = array_key_exists( 'property_name', $abprf_infos ) ? $abprf_infos['property_name'] : '';
				$deposit_info  = array_key_exists( 'deposit', $price_info ) ? $price_info['deposit'] : [];
				$deposit_type  = is_array( $deposit_info ) && array_key_exists( 'type', $deposit_info ) ? $deposit_info['type'] : '';
				$deposit_value = is_array( $deposit_info ) && array_key_exists( 'value', $deposit_info ) ? $deposit_info['value'] : '';
				$total_qty     = array_key_exists( 'qty', $price_info ) ? $price_info['qty'] : 0;
				$reserve_qty   = array_key_exists( 'reserve', $price_info ) ? $price_info['reserve'] : 0;
				$min_qty       = array_key_exists( 'min_qty', $price_info ) ? $price_info['min_qty'] : 1;
				$max_qty       = array_key_exists( 'max_qty', $price_info ) ? $price_info['max_qty'] : '';
				$sold_qty      = ABPRF_Query::get_sold_qty( $abprf_infos );
				//echo '<pre>';print_r( $sold_qty);					echo '</pre>';
				$available_qty = $total_qty - $reserve_qty - $sold_qty;
				$max_qty       = ! empty( $max_qty ) && $max_qty <= $available_qty ? $max_qty : $available_qty;
				$min_qty       = max( $min_qty, 1 );
				if ( $max_qty >= $min_qty ) {
					$collapse_id = '#' . $post_id . '_' . $property_id;
					?>
                    <div class="select_property">
                        <input type="hidden" name="property_id[]" value="<?php echo esc_attr( $property_id ); ?>"/>
                        <input type="hidden" name="deposit_type[]" value="<?php echo esc_attr( $deposit_type ); ?>"/>
                        <input type="hidden" name="deposit_value[]" value="<?php echo esc_attr( $deposit_value ); ?>"/>
                        <div class="custom_checkbox">
                            <input type="hidden" name="property_check[]" value="" data-id="<?php echo esc_attr( $collapse_id ); ?>"/>
                            <div class="checkbox_item _fa_center _fs_label" data-checked="1" data-open-icon="far fa-check-square" data-close-icon="far fa-square">
                                <h3 class="_abprf"><span data-icon class="_mar_r_xs far fa-square"></span></h3><?php echo esc_html__( 'Select ', 'abp-rentalforge' ) . ' ' . esc_html( $name ); ?>
                            </div>
                        </div>
						<?php
							if ( $max_qty > $min_qty ) {
								$input_info['name']        = 'property_qty[]';
								$input_info['price']       = $total_price;
								$input_info['available']   = $available_qty;
								$input_info['min_qty']     = $min_qty;
								$input_info['max_qty']     = $max_qty;
								$input_info['collapse_id'] = $collapse_id;
								ABPRF_Layout::quantity_input( $input_info );
							} else { ?>
                                <input type="hidden" name="property_qty[]" value="<?php echo esc_attr( $min_qty ); ?>" data-price="<?php echo esc_attr( $total_price ); ?>"/>
							<?php } ?>
                    </div>
				<?php } else {
					ABPRF_Layout::layout_warning_info_xs( 'property_not_available' );
				}
			}
			public static function create_client_form( $form, $name ): void {
				$type             = array_key_exists( 'type', $form ) ? $form['type'] : '';
				$required         = array_key_exists( 'required', $form ) && $form['required'] == 'on' ? 'required' : '';
				$label            = array_key_exists( 'label', $form ) ? $form['label'] : '';
				$d_value          = array_key_exists( 'd_value', $form ) ? $form['d_value'] : '';
				$validation_class = '';
				if ( $type == 'text' || $type == 'number' || $type == 'email' ) {
					$validation_class = $type == 'text' ? 'validation_name' : $validation_class;
					$validation_class = $type == 'number' ? 'validation_number' : $validation_class;
					?>
                    <label class="_input_item">
						<?php ABPRF_Layout::input_title( $label, $required ); ?>
                        <input type="<?php echo esc_attr( $type ); ?>" name="<?php echo esc_attr( $name ); ?>" value="<?php echo esc_attr( $d_value ); ?>" class="_form_control <?php echo esc_attr( $validation_class ); ?>" placeholder="<?php echo esc_attr( $label ); ?>" title="<?php echo esc_attr( $label ); ?>" <?php echo esc_attr( $required ); ?> />
                    </label>
					<?php
				}
				if ( $type == 'date' ) {
					ABPRF_Layout::input_date( $name, $d_value, $label, $required );
				}
				if ( $type == 'textarea' ) {
					ABPRF_Layout::textarea( $name, $d_value, $label, $required );
				}
				if ( $type == 'select' ) {
					$options = array_key_exists( 'option', $form ) ? $form['option'] : '';
					$options = $options ? explode( ',', $options ) : '';
					ABPRF_Layout::select( $name, $d_value, $label, $required, $options );
				}
				if ( $type == 'checkbox' ) {
					$options = array_key_exists( 'option', $form ) ? $form['option'] : '';
					$options = $options ? explode( ',', $options ) : '';
					ABPRF_Layout::checkbox( $name, $d_value, $label, $required, $options );
				}
				if ( $type == 'radio' ) {
					$options = array_key_exists( 'option', $form ) ? $form['option'] : '';
					$options = $options ? explode( ',', $options ) : '';
					ABPRF_Layout::radio( $name, $d_value, $label, $required, $options );
				}
			}
			//=============================//
			public static function ticket_info( $ticket_infos ): void {
				if ( ! empty( $ticket_infos ) && is_array( $ticket_infos ) ) { ?>
                    <ul class=" _abprf">
						<?php foreach ( $ticket_infos as $ticket_info ) {
							if ( ! empty( $ticket_info ) && sizeof( $ticket_info ) > 0 ) {
								$name  = $ticket_info['name'] ?? '';
								$qty   = $ticket_info['qty'] ?? 1;
								$price = $ticket_info['price'] ?? '';
								if ( ! empty( $name ) ) { ?>
                                    <li>
                                        <strong><?php echo esc_html( $name ); ?></strong>
										<?php echo esc_html( ' X ' . $qty . ' = ' ) . ' ' . ( ! empty( $price ) && $price > 0 ? wp_kses_post( wc_price( $price ) ) : esc_html__( 'FREE', 'abp-rentalforge' ) ); ?>
                                    </li>
								<?php }
							}
						} ?>
                    </ul>
				<?php }
			}
			public static function additional_info( $additional_infos ): void {
				if ( ! empty( $additional_infos ) && is_array( $additional_infos ) ) { ?>
                    <ul class=" _abprf">
						<?php foreach ( $additional_infos as $ex_info ) {
							if ( ! empty( $ex_info ) && sizeof( $ex_info ) > 0 ) {
								$name       = $ex_info['name'] ?? '';
								$qty        = $ex_info['qty'] ?? 1;
								$price      = $ex_info['price'] ?? '';
								$returnable = $ex_info['returnable'] ?? 'no';
								if ( ! empty( $name ) ) { ?>
                                    <li>
                                        <strong><?php echo esc_html( $name ); ?></strong>
										<?php echo esc_html( ' X ' . $qty . ' = ' ) . ' ' . ( ! empty( $price ) && $price > 0 ? wp_kses_post( wc_price( $price ) ) : esc_html__( 'FREE', 'abp-rentalforge' ) ); ?>
										<?php
											if ( $returnable == 'yes' ) {
												?> <span class="_color_required"> - <?php esc_html_e( 'Returnable', 'abp-rentalforge' ); ?></span><?php
											} ?>
                                    </li>
									<?php
								}
							}
						} ?>
                    </ul>
				<?php }
			}
			public static function client_info( $passenger_infos ): void {
				if ( ! empty( $passenger_infos ) && is_array( $passenger_infos ) ) { ?>
                    <ul class=" _abprf">
						<?php foreach ( $passenger_infos as $pas_form ) {
							if ( ! empty( $pas_form ) && sizeof( $pas_form ) > 0 ) {
								$label = $pas_form['label'] ?? '';
								$value = $pas_form['value'] ?? '';
								if ( ! empty( $label ) && ! empty( $value ) ) { ?>
                                    <li>
                                        <strong><?php echo esc_html( $label ); ?></strong> : <?php echo esc_html( $value ); ?>
                                    </li>
									<?php
								}
							}
						} ?>
                    </ul>
				<?php }
			}
			public static function billing_info( $booking_list ): void {
				if ( ! empty( $booking_list ) ) {
					$billing_name    = $booking_list['billing_name'] ?? '';
					$billing_email   = $booking_list['billing_email'] ?? '';
					$billing_phone   = $booking_list['billing_phone'] ?? '';
					$billing_address = $booking_list['billing_address'] ?? '';
					?>
                    <ul class=" _abprf">
						<?php if ( ! empty( $billing_name ) ) { ?>
                            <li><strong><?php esc_html_e( 'Name :', 'abp-rentalforge' ); ?></strong>&nbsp;<?php echo esc_html( $billing_name ); ?></li>
						<?php } ?>
						<?php if ( ! empty( $billing_email ) ) { ?>
                            <li><strong><?php esc_html_e( 'E-Mail :', 'abp-rentalforge' ); ?></strong>&nbsp;<?php echo esc_html( $billing_email ); ?></li>
						<?php } ?>
						<?php if ( ! empty( $billing_phone ) ) { ?>
                            <li><strong><?php esc_html_e( 'Phone :', 'abp-rentalforge' ); ?></strong>&nbsp;<?php echo esc_html( $billing_phone ); ?></li>
						<?php } ?>
						<?php if ( ! empty( $billing_address ) ) { ?>
                            <li><strong><?php esc_html_e( 'Address :', 'abp-rentalforge' ); ?></strong>&nbsp;<?php echo esc_html( $billing_address ); ?></li>
						<?php } ?>
                    </ul>
					<?php
				}
			}
			//=============================//
			public static function filter_post_list( $post_id = 0 ): void {
				$label        = ABPRF_Function::label();
				$all_post_ids = ABPRF_Query::get_post_id();
				$value        = $post_id > 0 ? $post_id : '';
				$brand_icon   = ABPRF_Function::icon();
				// echo '<pre>';print_r($configuration);echo '</pre>';
				?>
                <div class="_input_item abp_dropdown">
                    <label>
                        <span><?php ABPRF_Layout::image_icon( $brand_icon, '_mar_r_xxs' ); ?><?php echo esc_html( $label ); ?></span>
                        <input type="hidden" name="post_id" value="<?php echo esc_attr( $value ); ?>"/>
                        <input type="text" class="_form_control_w_full" name="" placeholder="<?php echo esc_attr( $label ); ?>" value="<?php echo esc_attr( get_the_title( $post_id ) ); ?>"/>
                    </label>
					<?php if ( sizeof( $all_post_ids ) > 0 ) { ?>
                        <div class="dropdown_list">
                            <ul class="_abprf ">
								<?php foreach ( $all_post_ids as $all_post_id ) {
									$sku      = ABPRF_Function::get_post_info( $all_post_id, 'post_sku' );
									$category = ABPRF_Function::get_post_info( $all_post_id, 'category' );
									$category = ! empty( $category ) ? get_term( $category )->name : '';
									$title    = get_the_title( $all_post_id );
									?>
                                    <li data-value="<?php echo esc_attr( $all_post_id ); ?>" data-text="<?php echo esc_attr( $title ); ?>">
										<?php ABPRF_Layout::image_icon( $brand_icon, '_mar_r_xs' ); ?>
                                        <span class="_fs_label"><?php echo esc_html( $title ); ?></span>
										<?php if ( ! empty( $category ) ) { ?>
                                            <sub class="_abprf_color_gray"> - <?php echo esc_html( $category ); ?></sub>
										<?php } ?>
										<?php if ( ! empty( $sku ) ) { ?>
                                            <sub class="_abprf_color_info"> - <?php echo esc_html( $sku ); ?></sub>
										<?php } ?>
                                    </li>
								<?php } ?>
                            </ul>
                        </div>
					<?php } ?>
                </div>
				<?php
			}
			public static function filter_booking_date(): void {
				$date_format = ABPRF_Function::date_format_php();
				$now         = date_i18n( $date_format, strtotime( current_time( 'Y-m-d' ) ) );
				?>
                <div class="_input_item">
                    <label class="_fd_column">
                        <span>📅 <?php esc_html_e( 'Booking Date', 'abp-rentalforge' ) ?></span>
                        <input type="hidden" name="start_time" value=""/>
                        <input type="text" value="" class="_form_control abp_datepicker" placeholder="<?php echo esc_attr( $now ); ?>" readonly/>
                        <span class="fas fa-times date_close_icon" title="<?php esc_attr_e( 'Clear Date', 'abp-rentalforge' ); ?>"></span>
                    </label>
                </div>
				<?php
			}
			public static function filter_order_date(): void {
				$date_format = ABPRF_Function::date_format_php();
				$now         = date_i18n( $date_format, strtotime( current_time( 'Y-m-d' ) ) );
				?>
                <div class="_input_item">
                    <label class="_fd_column">
                        <span>🗓️ <?php esc_html_e( 'Order Date', 'abp-rentalforge' ) ?></span>
                        <input type="hidden" name="order_date" value=""/>
                        <input type="text" value="" class="_form_control abp_datepicker" placeholder="<?php echo esc_attr( $now ); ?>" readonly/>
                        <span class="fas fa-times date_close_icon" title="<?php esc_attr_e( 'Clear Date', 'abp-rentalforge' ); ?>"></span>
                    </label>
                </div>
				<?php
			}
			public static function filter_booking_date_between(): void {
				$date_format = ABPRF_Function::date_format_php();
				$now         = date_i18n( $date_format, strtotime( current_time( 'Y-m-d' ) ) );
				?>
                <div class="_g_input_input_item_fd_column">
                    <label><span>⏰ <?php esc_html_e( 'Booking Date Between', 'abp-rentalforge' ); ?></span></label>
                    <div class="_f_equal">
                        <label>
                            <input type="hidden" name="booking_time_from" value=""/>
                            <input type="text" value="" class="_form_control abp_datepicker" placeholder="<?php echo esc_attr( $now ); ?>" readonly/>
                            <span class="fas fa-times date_close_icon" title="<?php esc_attr_e( 'Clear Date', 'abp-rentalforge' ); ?>"></span>
                        </label>
                        <label>
                            <input type="hidden" name="booking_time_to" value=""/>
                            <input type="text" value="" class="_form_control abp_datepicker" placeholder="<?php echo esc_attr( $now ); ?>" readonly/>
                            <span class="fas fa-times date_close_icon" title="<?php esc_attr_e( 'Clear Date', 'abp-rentalforge' ); ?>"></span>
                        </label>
                    </div>
                </div>
				<?php
			}
			public static function filter_order_date_between(): void {
				$date_format = ABPRF_Function::date_format_php();
				$now         = date_i18n( $date_format, strtotime( current_time( 'Y-m-d' ) ) );
				?>
                <div class="_g_input_input_item_fd_column" data-collapse="#view_more_filter_option">
                    <label><span>⏰ <?php esc_html_e( 'Order Date Between', 'abp-rentalforge' ); ?></span></label>
                    <div class="_f_equal">
                        <label>
                            <input type="hidden" name="order_date_from" value=""/>
                            <input type="text" value="" class="_form_control abp_datepicker" placeholder="<?php echo esc_attr( $now ); ?>" readonly/>
                            <span class="fas fa-times date_close_icon" title="<?php esc_attr_e( 'Clear Date', 'abp-rentalforge' ); ?>"></span>
                        </label>
                        <label>
                            <input type="hidden" name="order_date_to" value=""/>
                            <input type="text" value="" class="_form_control abp_datepicker" placeholder="<?php echo esc_attr( $now ); ?>" readonly/>
                            <span class="fas fa-times date_close_icon" title="<?php esc_attr_e( 'Clear Date', 'abp-rentalforge' ); ?>"></span>
                        </label>
                    </div>
                </div>
				<?php
			}
			public static function filter_user_id(): void {
				$all_users = get_users( array(
					'fields' => array( 'ID', 'display_name' ),
				) );
				?>
                <div class="_input_item abp_dropdown ">
                    <label class="_fd_column">
                        <span>👨‍💼  <?php esc_html_e( 'User Name', 'abp-rentalforge' ); ?></span>
                        <input type="hidden" name="user_id" value=""/>
                        <input type="text" class="_form_control_w_full" placeholder="<?php esc_attr_e( 'User Name', 'abp-rentalforge' ); ?>" value=""/>
                    </label>
					<?php if ( ! empty( $all_users ) ) { ?>
                        <div class="dropdown_list">
                            <ul class="_abprf ">
								<?php foreach ( $all_users as $user ) { ?>
                                    <li data-value="<?php echo esc_attr( $user->ID ); ?>" data-text="<?php echo esc_attr( $user->display_name ); ?>">
                                        <span class="_fs_label"><?php echo esc_html( $user->display_name ); ?></span>
                                    </li>
								<?php } ?>
                            </ul>
                        </div>
					<?php } ?>
                </div>
				<?php
			}
			public static function filter_order_id(): void {
				?>
                <div class="_input_item " data-collapse="#view_more_filter_option">
                    <label class="_fd_column">
                        <span>📦 <?php esc_html_e( 'Order ID', 'abp-rentalforge' ); ?></span>
                        <input type="number" class="_form_control_w_full validation_number" name="order_id" placeholder="<?php esc_attr_e( 'Order ID', 'abp-rentalforge' ); ?>" value=""/>
                    </label>
                </div>
				<?php
			}
			public static function filter_bill_name(): void {
				?>
                <div class="_input_item " data-collapse="#view_more_filter_option">
                    <label class="_fd_column">
                        <span>👤 <?php esc_html_e( 'Billing Name', 'abp-rentalforge' ); ?></span>
                        <input type="text" class="_form_control_w_full " name="billing_name" placeholder="<?php esc_attr_e( 'Billing Name', 'abp-rentalforge' ); ?>" value=""/>
                    </label>
                </div>
				<?php
			}
			public static function filter_bill_email(): void {
				?>
                <div class="_input_item " data-collapse="#view_more_filter_option">
                    <label class="_fd_column">
                        <span>✉️ <?php esc_html_e( 'Billing Email', 'abp-rentalforge' ); ?></span>
                        <input type="email" class="_form_control_w_full " name="billing_email" placeholder="<?php esc_attr_e( 'Billing Email', 'abp-rentalforge' ); ?>" value=""/>
                    </label>
                </div>
				<?php
			}
			public static function filter_bill_phone(): void {
				?>
                <div class="_input_item " data-collapse="#view_more_filter_option">
                    <label class="_fd_column">
                        <span>☎️ <?php esc_html_e( 'Billing phone', 'abp-rentalforge' ); ?></span>
                        <input type="text" class="_form_control_w_full " name="billing_phone" placeholder="<?php esc_attr_e( 'Billing phone', 'abp-rentalforge' ); ?>" value=""/>
                    </label>
                </div>
				<?php
			}
			public static function filter_location(): void {
				$all_locations = ABPRF_Locations;
				if ( ! empty( $all_locations ) ) {
					?>
                    <div class="_input_item abp_dropdown ">
                        <label class="_fd_column">
                            <span>📍  <?php esc_html_e( 'Location', 'abp-rentalforge' ); ?></span>
                            <input type="hidden" name="location" value=""/>
                            <input type="text" class="_form_control_w_full" placeholder="<?php esc_attr_e( 'Location', 'abp-rentalforge' ); ?>" value=""/>
                        </label>
                        <div class="dropdown_list">
                            <ul class="_abprf ">
								<?php foreach ( $all_locations as $key => $location ) {
									$name = is_array( $location ) && array_key_exists( 'name', $location ) ? $location['name'] : ''; ?>
                                    <li data-value="<?php echo esc_attr( $key ); ?>" data-text="<?php echo esc_attr( $name ); ?>">
                                        <span class="_fs_label"><?php echo esc_html( $name ); ?></span>
                                    </li>
								<?php } ?>
                            </ul>
                        </div>
                    </div>
					<?php
				}
			}
		}
		new ABPRF_Layout();
	}