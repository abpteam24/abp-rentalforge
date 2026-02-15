<?php
	if (!defined('ABSPATH')) {
		exit; // Exit if accessed directly
	}
	if (!class_exists('ABPRF_Layout')) {
		class ABPRF_Layout {
			public function __construct() {
				add_action( 'abprf_load_date_picker', [ $this, 'load_date_picker' ], 10, 2 );
				//==============================//
				add_action('abprf_add_icon', array($this, 'load_icon'), 10, 2);
				add_action('abprf_add_image', array($this, 'add_single_image'), 10, 2);
				add_action('abprf_add_image_multiple', array($this, 'abprf_add_image_multi'), 10, 2);
				add_action('abprf_add_image_icon', array($this, 'selection_icon_image'), 10, 3);
				//==============================//
				add_action('abprf_slider', array($this, 'full_slider'));
				add_action('abprf_slider_only', array($this, 'slider_only'), 10, 2);
				//==============================//
            }
			public function load_date_picker( $selector, $dates ): void {
				$start_date  = current( $dates );
				$start_year  = gmdate( 'Y', strtotime( $start_date ) );
				$start_month = ( gmdate( 'n', strtotime( $start_date ) ) - 1 );
				$start_day   = gmdate( 'j', strtotime( $start_date ) );
				$end_date    = end( $dates );
				$end_year    = gmdate( 'Y', strtotime( $end_date ) );
				$end_month   = ( gmdate( 'n', strtotime( $end_date ) ) - 1 );
				$end_day     = gmdate( 'j', strtotime( $end_date ) );
				$all_date    = [];
				foreach ( $dates as $date ) {
					$all_date[] = '"' . gmdate( 'j-n-Y', strtotime( $date ) ) . '"';
				}
				?>
                <script>
                    jQuery(document).ready(function () {
                        jQuery("<?php echo esc_attr( $selector ); ?>").datepicker({
                            dateFormat: abprf_var.date_picker_format,
                            autoSize: true, changeMonth: true, changeYear: true,
                            minDate: new Date(<?php echo esc_attr( $start_year ); ?>, <?php echo esc_attr( $start_month ); ?>, <?php echo esc_attr( $start_day ); ?>),
                            maxDate: new Date(<?php echo esc_attr( $end_year ); ?>, <?php echo esc_attr( $end_month ); ?>, <?php echo esc_attr( $end_day ); ?>),
                            beforeShowDay: available_check,
                            onSelect: function (dateString, data) {
                                let date = data.selectedYear + '-' + ('0' + (parseInt(data.selectedMonth) + 1)).slice(-2) + '-' + ('0' + parseInt(data.selectedDay)).slice(-2);
                                jQuery(this).closest('label').find('input[type="hidden"]').val(date).trigger('change');
                            }
                        });
                        function available_check(date) {
                            let availableDates = [<?php echo wp_kses_post( implode( ',', $all_date ) ); ?>];
                            let dmy = date.getDate() + "-" + (date.getMonth() + 1) + "-" + date.getFullYear();
                            if (jQuery.inArray(dmy, availableDates) !== -1) {
                                return [true, "", "<?php esc_attr_e( 'Available', 'abprf-rental-forge' ); ?>"];
                            } else {
                                return [false, "", "<?php esc_attr_e( 'Unavailable', 'abprf-rental-forge' ); ?>"];
                            }
                        }
                    });
                </script>
				<?php
			}
			public static function info_text($key): void {
				$data = ABPRF_Static_Array::array_info($key);
				if ($data) {
					echo '<span class="info_text"><i class="fas fa-info-circle _color_theme_mar_r_xxs"></i>' . esc_html($data) . '</span>';
				}
			}
			public static function layout_warning_info($key): void {
				$data = ABPRF_Static_Array::array_info($key);
				if ($data) {
					echo '<div class="_section_bg_warning_mar_zero"><h4 class="_abprf_text_center_color_white">' . esc_html($data) . '</h4></div>';
				}
			}
			public static function switch_checkbox($name, $value = ''): void {
				$value = in_array($value, ['on', 'off', ''], true) ? $value : '';
				?>
                <div class="_br <?php echo esc_attr($value === 'on' ? 'rf_active' : ''); ?>" data-switch data-collapse-target="#<?php echo esc_attr($name); ?>">
                    <input type="hidden" name="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr($value); ?>">
                </div>
				<?php
			}
			public static function button_add($button_text, $class = '', $button_class = '', $icon_class = '', $change_input_name = ''): void {
				$class = $class ?: 'abprf_add_item';
				$button_class = $button_class ?: '_btn_default';
				$icon_class = $icon_class ?: 'fas fa-plus';
				?>
                <button class="<?php echo esc_attr($button_class . ' ' . $class); ?>" type="button">
                    <span class="_mar_r_xs <?php echo esc_attr($icon_class); ?>"></span><span data-input-change="<?php echo esc_attr($change_input_name); ?>"><?php echo esc_html($button_text); ?></span>
                </button>
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
			public static function button_delete($class = 'abprf_delete_item'): void {
				?>
                <button class="_btn_danger_xs <?php echo esc_attr($class); ?>" type="button" title="<?php esc_attr_e('Delete This Item', 'abprf-rental-forge'); ?>">
                    <span class="fas fa-times"></span>
                </button>
				<?php
			}
			public static function button_sort(): void {
				?>
                <div class="_btn_warning_xs abprf_sortable_handle" type="button" title="<?php esc_attr_e('Move This Item', 'abprf-rental-forge'); ?>">
                    <span class="fas fa-arrows-alt"></span>
                </div>
				<?php
			}
			//=============================//
			public static function input_dropdown($infos, $icon = ''): void {
				if (is_array($infos) && sizeof($infos) > 0) {
					asort($infos);
					?>
                    <ul class="_abprf dropdown_input">
						<?php foreach ($infos as $info) { ?>
                            <li data-value="<?php echo esc_attr($info); ?>"><span class="<?php echo esc_attr($icon); ?> _mar_r_xxs"></span><span data-text><?php echo esc_html($info); ?></span></li>
						<?php } ?>
                    </ul>
					<?php
				}
			}
			//=============================//
			public static function popup_button($target_popup_id, $text): void {
				?>
                <button type="button" class="_btn_default_bg_blue" data-target-popup="<?php echo esc_attr($target_popup_id); ?>"><span class="fas fa-plus-square"></span> <?php echo esc_html($text); ?></button>
				<?php
			}
			public static function popup_button_xs($target_popup_id, $text): void {
				?>
                <button type="button" class="_btn_default_xs_bg_blue" data-target-popup="<?php echo esc_attr($target_popup_id); ?>"><span class="fas fa-plus-square"></span> <?php echo esc_html($text); ?></button>
				<?php
			}
			//=============================//
			public static function bg_image($post_id = '', $image_id = '', $url = '', $class = ''): void {
				$thumbnail = ($post_id > 0 || $image_id) ? ABPRF_LIB_Function::get_image_url($post_id, $image_id) : $url;
				$post_url = $post_id > 0 ? get_the_permalink($post_id) : '';
				if ($thumbnail) {
					?>
                    <div class="abprf_bg_img  <?php echo esc_attr($class); ?>" data-href="<?php echo esc_url($post_url); ?>" data-placeholder>
                        <div data-bg-image="<?php echo esc_url($thumbnail); ?>"></div>
                    </div>
					<?php
				}
			}
			//=============================//
			public static function load_more_text($text = '', $length = 150): void {
				$text_length = strlen($text);
				if ($text && $text_length > $length) {
					?>
                    <div class="abprf_load_more">
                        <span data-read-close><?php echo esc_html(substr($text, 0, $length)); ?> ....</span>
                        <span data-read-open class="_d_none"><?php echo esc_html($text); ?></span>
                        <div data-read data-open-text="<?php esc_attr_e('Load More', 'abprf-rental-forge'); ?>" data-close-text="<?php esc_attr_e('Less More', 'abprf-rental-forge'); ?>">
                            <span data-text><?php esc_html_e('Load More', 'abprf-rental-forge'); ?></span>
                        </div>
                    </div>
					<?php
				} else {
					?>
                    <span><?php echo esc_html($text); ?></span>
					<?php
				}
			}
			//=============================//
			public static function quantity_input($input_info = []): void {
				$name = array_key_exists('name', $input_info) ? $input_info['name'] : '';
				$price = array_key_exists('price', $input_info) ? $input_info['price'] : 0;
				$available = array_key_exists('available', $input_info) ? $input_info['available'] : 1;
				$default_qty = array_key_exists('d_qty', $input_info) ? $input_info['d_qty'] : 0;
				$min_qty = array_key_exists('min_qty', $input_info) ? $input_info['min_qty'] : 0;
				$max_qty = array_key_exists('max_qty', $input_info) ? $input_info['max_qty'] : '';
				$class = array_key_exists('class', $input_info) ? $input_info['class'] : '';
				$min_qty = max($default_qty, $min_qty);
				if ($name && $available > $min_qty) {
					?>
                    <div class="_group_content qty_input">
                        <div class="qty_decrease _ag_content"><span class="fas fa-minus"></span></div>
                        <label>
                            <input type="text" class="_form_control  validation_number <?php echo esc_attr($class); ?>"
                                   name="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr(max(0, $default_qty)); ?>"
                                   data-price="<?php echo esc_attr($price); ?>" data-min="<?php echo esc_attr($min_qty); ?>"
                                   data-max="<?php echo esc_attr($max_qty > 0 ? $max_qty : $available); ?>"
                            />
                        </label>
                        <div class="qty_increase _ag_content"><span class="fas fa-plus"></span></div>
                    </div>
					<?php
				}
			}
			//=============================//
			public static function on(): bool|string {
				ob_start();
				?>
                <strong class="_abprf_color_theme"> <?php esc_html_e('ON', 'abprf-rental-forge'); ?></strong>
				<?php
				return ob_get_clean();
			}
			public static function off(): bool|string {
				ob_start();
				?>
                <strong class="_abprf_color_theme"> <?php esc_html_e('OFF', 'abprf-rental-forge'); ?></strong>
				<?php
				return ob_get_clean();
			}
			//=============================//
			public static function input_title($label = '', $required = ''): void {
				if ($label) { ?>
                    <span class="_mar_b_xxs">
							<?php echo esc_html($label); ?>
						<?php if ($required) { ?>
                            <sup class="_color_required">*</sup>
						<?php } ?>
						</span>
					<?php
				}
			}
			public static function input_date($name, $date = '', $label = '', $required = ''): void {
				$date_format = ABPRF_LIB_Function::date_picker_format();
				$now = date_i18n($date_format, strtotime(current_time('Y-m-d')));
				$hidden_date = $date ? gmdate('Y-m-d', strtotime($date)) : '';
				$visible_date = $date ? date_i18n($date_format, strtotime($date)) : '';
				?>
                <label class="_input_item">
					<?php self::input_title($label, $required); ?>
                    <input type="hidden" name="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr($hidden_date); ?>" <?php echo esc_attr($required); ?>/>
                    <input type="text" name="" class="_form_control abprf_datepicker" value="<?php echo esc_attr($visible_date); ?>" placeholder="<?php echo esc_attr($now); ?>" readonly/>
                    <span class="fas fa-times date_close_icon" title="<?php esc_attr_e('Clear Date', 'abprf-rental-forge'); ?>"></span>
                </label>
				<?php
			}
			public static function input_time($name, $time = '', $label = '', $required = ''): void {
				?>
                <label class="_input_item">
					<?php self::input_title($label, $required); ?>
                    <input type="time" class="_form_control" name="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr($time); ?>" <?php echo esc_attr($required); ?>/>
                    <span class="fas fa-times time_close_icon" title="<?php esc_attr_e('Clear Time', 'abprf-rental-forge'); ?>"></span>
                </label>
				<?php
			}
			public static function textarea($name, $value = '', $label = '', $required = ''): void {
				?>
                <label class="abprf_textarea _input_item">
					<?php self::input_title($label, $required); ?>
                    <textarea name="<?php echo esc_attr($name); ?>" rows="3" class="_form_control" placeholder="<?php echo esc_attr($label); ?>" title="<?php echo esc_attr($label); ?>"  <?php echo esc_attr($required); ?>><?php echo esc_textarea($value); ?></textarea>
                </label>
				<?php
			}
			public static function select($name, $value = '', $label = '', $required = '', $options = []): void {
				if (is_array($options) && sizeof($options) > 0) {
					?>
                    <label class="_input_item">
						<?php self::input_title($label, $required); ?>
                        <select name="<?php echo esc_attr($name); ?>" class="_form_control" title="<?php echo esc_attr($label); ?>" <?php echo esc_attr($required); ?>>
                            <option value="" disabled selected><?php echo esc_html__('Please Select', 'abprf-rental-forge') . ' ' . esc_html($label); ?></option>
							<?php foreach ($options as $option) { ?>
                                <option value="<?php echo esc_attr($option); ?>" <?php echo esc_attr($option == $value ? 'selected' : ''); ?>><?php echo esc_html($option); ?></option>
							<?php } ?>
                        </select>
                    </label>
					<?php
				}
			}
			public static function checkbox($name, $value = '', $label = '', $required = '', $options = []): void {
				if (is_array($options) && sizeof($options) > 0) {
					?>
                    <div class="abprf_checkbox _input_item">
                        <span class="_fs_label"> <?php self::input_title($label, $required); ?></span>
                        <input type="hidden" class="_form_control" name="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr($value); ?>"/>
                        <div class="_f_wrap">
							<?php foreach ($options as $option) { ?>
                                <div class="checkbox_item">
                                    <button type="button" class="_btn_white_xs <?php echo esc_attr($option == $value ? 'rf_active' : ''); ?>" data-checked="<?php echo esc_attr($option); ?>" data-open-icon="far fa-check-square" data-close-icon="far fa-square">
                                        <span data-icon class="_mar_r_xs <?php echo esc_attr($option == $value ? 'far fa-check-square' : 'far fa-square'); ?>"></span><?php echo esc_html($option); ?>
                                    </button>
                                </div>
							<?php } ?>
                        </div>
                    </div>
					<?php
				}
			}
			public static function radio($name, $value = '', $label = '', $required = '', $options = []): void {
				if (is_array($options) && sizeof($options) > 0) {
					?>
                    <div class="abprf_radio _input_item">
                        <span class="_fs_label"> <?php self::input_title($label, $required); ?></span>
                        <input type="hidden" class="_form_control" name="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr($value); ?>"/>
                        <div class="_f_wrap">
							<?php foreach ($options as $option) { ?>
                                <div class="radio_item">
                                    <button type="button" class="_btn_white_xs <?php echo esc_attr($option == $value ? 'rf_active' : ''); ?>" data-radio="<?php echo esc_attr($option); ?>" data-open-icon="far fa-check-circle" data-close-icon="far fa-circle">
                                        <span data-icon class="_mar_r_xs <?php echo esc_attr($option == $value ? 'far fa-check-circle' : 'far fa-circle'); ?>"></span><?php echo esc_html($option); ?>
                                    </button>
                                </div>
							<?php } ?>
                        </div>
                    </div>
					<?php
				}
			}
			//=================================//
			public function load_icon($name, $value = ''): void {
				$button_active_class = $value ? '_d_none' : '';
				?>
                <div class="abprf_icon_image_selection_area">
                    <input type="hidden" name="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr($value); ?>"/>
                    <div class="abprf_item_icon">
                        <div class="_all_center"><span class="<?php echo esc_attr($value); ?>" data-add-icon></span></div>
                        <span class="fas fa-times close_icon abprf_delete_icon" title="<?php esc_html_e('Remove Icon', 'abprf-rental-forge'); ?>"></span>
                    </div>
                    <div class="abprf_select_image_icon_content <?php echo esc_attr($button_active_class); ?>">
                        <button class="_btn_info_xs abprf_add_icon" type="button" data-target-popup="#abprf_popup_icon"><span class="fas fa-icons _fs_h6"></span></button>
                    </div>
                </div>
				<?php
				add_action('admin_footer', array($this, 'icon_popup'));
			}
			public function icon_popup(): void {
				if (!defined('ABPTM_ICON')) {
					?>
                    <div class="abprf_popup_icon abprf_popup abprf_area" data-popup="#abprf_popup_icon">
                        <div class="popup_main_area _full_width">
                            <div class="popup_head _all_center">
                                <h2 class="_abprf_mar_r"><?php esc_html_e('Select Icon', 'abprf-rental-forge'); ?></h2>
                                <span class="popup_close"><i class="fas fa-times"></i></span>
                            </div>
                            <div class="popup_body">
								<?php
									$icons = ABPRF_Static_Array::fontawesome_array();
									if (sizeof($icons) > 0) {
										$total_icon = 0;
										foreach ($icons as $icon) {
											$total_icon += sizeof($icon['icon']);
										}
										?>
                                        <div class="_d_flex">
                                            <ul class="_abprf popup_icon_menu">
                                                <li class="rf_active" data-icon-menu="all_item" data-icon-title="all_item"><?php esc_html_e('All Icon', 'abprf-rental-forge'); ?>&nbsp;( <strong class="_abprf"><?php echo esc_html($total_icon); ?></strong> )</li>
												<?php foreach ($icons as $key => $icon) { ?>
                                                    <li data-icon-menu="<?php echo esc_attr($key); ?>"><?php echo esc_html($icon['title']); ?> &nbsp;(<strong class="_abprf"><?php echo esc_html(sizeof($icon['icon'])); ?></strong>)</li>
												<?php } ?>
                                            </ul>
                                            <div class="popup_all_icon">
												<?php foreach ($icons as $key => $icon) { ?>
                                                    <div class="popupTabItem" data-icon-list="<?php echo esc_attr($key); ?>" data-icon-title="<?php echo esc_attr($icon['title']); ?>">
                                                        <h5 class="_abprf_color_theme_mar_t_xs"><?php echo esc_html($icon['title']); ?> &nbsp;(<strong class="_abprf"><?php echo esc_html(sizeof($icon['icon'])); ?></strong>) </h5>
                                                        <div class="_divider_xs"></div>
                                                        <div class="item_icon_area">
															<?php foreach ($icon['icon'] as $icon => $item) { ?>
                                                                <div class="iconItem _all_center_fd_column" data-icon-class="<?php echo esc_attr($icon); ?>" data-icon-name="<?php echo esc_attr($item); ?>" title="<?php echo esc_attr($item); ?>">
                                                                    <span class="<?php echo esc_attr($icon); ?>"></span>
                                                                    <i><?php echo esc_html($item); ?></i>
                                                                </div>
															<?php } ?>
                                                        </div>
                                                    </div>
												<?php } ?>
                                            </div>
                                        </div>
									<?php } ?>
                            </div>
                        </div>
                    </div>
					<?php
					define('ABPTM_ICON', true);
				}
			}
			//============= Image================//
			public function add_single_image($name, $image_id = ''): void {
				?>
                <div class="abprf_add_image">
                    <input type="hidden" name="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr($image_id); ?>"/>
					<?php if ($image_id) { ?>
                        <div class="abprf_add_image_item" data-image-id="<?php echo esc_attr($image_id); ?>'">
                            <span class="fas fa-times _circle_icon_xs abprf_remove_image"></span>
                            <img class="_img_control" src="<?php echo esc_url(wp_get_attachment_image_url($image_id, 'medium')); ?>" alt="<?php echo esc_attr($image_id); ?>"/>
                        </div>
					<?php } ?>
                    <button type="button" class="_btn_default_xs_bg_color_1_full_width <?php echo esc_attr($image_id ? '_d_none' : ''); ?>">
                        <span class="fas fa-image _mar_r_xs"></span><?php esc_html_e('Image', 'abprf-rental-forge'); ?>
                    </button>
                </div>
				<?php
			}
			public function abprf_add_image_multi($name, $images): void {
				$images = is_array($images) ? ABPRF_LIB_Function::array_to_string($images) : $images;
				?>
                <div class="abprf_multiple_image_area">
                    <input type="hidden" class="abprf_multiple_image_ids" name="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr($images); ?>"/>
                    <div class="abprf_multiple_image">
						<?php
							$all_images = explode(',', $images);
							if ($images && sizeof($all_images) > 0) {
								foreach ($all_images as $image) {
									$img_url = ABPRF_LIB_Function::get_image_url('', $image, 'medium') ?: ABPRF_BLANK_IMG_URL;
									?>
                                    <div class="abprf_multiple_image_item" data-image-id="<?php echo esc_attr($image); ?>">
                                        <span class="fas fa-times _circle_icon_xs abprf_remove_image_multi"></span>
                                        <img class="_img_control" src="<?php echo esc_attr($img_url); ?>" alt="<?php echo esc_attr($image); ?>"/>
                                    </div>
									<?php
								}
							}
						?>
                    </div>
                    <div class="_divider_xs"></div>
					<?php ABPRF_Layout::button_add(__('Add  Image', 'abprf-rental-forge'), 'abprf_add_image_multi'); ?>
                </div>
				<?php
			}
			//=============================//
			public function selection_icon_image($name, $icon = '', $image = ''): void {
				$icon_class = $icon ? '' : '_d_none';
				$image_class = $image ? '' : '_d_none';
				$value = $image ?: $icon;
				$button_active_class = $icon || $image ? '_d_none' : '';
				?>
                <div class="abprf_icon_image_selection_area _fd_column">
                    <input type="hidden" name="<?php echo esc_attr($name); ?>" value="<?php echo esc_attr($value); ?>"/>
                    <div class="abprf_item_icon <?php echo esc_attr($icon_class); ?>">
                        <div class="_all_center"><span class="<?php echo esc_attr($icon); ?>" data-add-icon></span></div>
                        <span class="fas fa-times close_icon abprf_delete_icon" title="<?php esc_html_e('Remove Icon', 'abprf-rental-forge'); ?>"></span>
                    </div>
                    <div class="abprf_image_item <?php echo esc_attr($image_class); ?>">
                        <img class="_img_control" src="<?php echo esc_url(ABPRF_LIB_Function::get_image_url('', $image, 'medium')); ?>" alt="image">
                        <span class="fas fa-times close_icon abprf_delete_image" title="<?php esc_html_e('Remove Image', 'abprf-rental-forge'); ?>"></span>
                    </div>
                    <div class="abprf_select_image_icon_content <?php echo esc_attr($button_active_class); ?>">
                        <div class="_group_content_f_equal_full_width">
                            <button class="_btn_info_xs abprf_select_image" type="button"><span class="fas fa-image _fs_h6"></span></button>
                            <button class="_btn_info_xs abprf_add_icon" type="button" data-target-popup="#abprf_popup_icon"><span class="fas fa-icons _fs_h6"></span></button>
                        </div>
                    </div>
                </div>
				<?php
				add_action('admin_footer', array($this, 'icon_popup'));
			}
			//==============================//
			public function full_slider($abprf_infos): void {
				$post_id = array_key_exists('post_id', $abprf_infos) ? $abprf_infos['post_id'] : 0;
				$display_slider = array_key_exists('display_slider', $abprf_infos) ? $abprf_infos['display_slider'] : 'on';
				$abprf_slider = ABPRF_LIB_Function::get_option('abprf_slider');
				$image_ids = array_unique(ABPRF_LIB_Function::get_post_info($post_id, 'abprf_sliders', array()));
				//echo '<pre>';print_r($image_ids);echo '</pre>';
				if (sizeof($image_ids) > 0 && $display_slider == 'on') {
					if ( sizeof($image_ids) > 1) {
						$this->slider($abprf_slider, $post_id, $image_ids);
					} else {
						$thumb_id = $image_ids[0];
						$thumb_id=$thumb_id?:get_post_thumbnail_id($post_id);
						ABPRF_Layout::bg_image('', $thumb_id, ABPRF_BLANK_IMG_URL, 'abprf_slider');
					}
				} else {
					$thumb_id = get_post_thumbnail_id($post_id);
					ABPRF_Layout::bg_image( '', $thumb_id,ABPRF_BLANK_IMG_URL, 'abprf_slider');
				}
			}
			public function slider_only($abprf_infos, $class = ''): void {
				$post_id = array_key_exists('post_id', $abprf_infos) ? $abprf_infos['post_id'] : 0;
				$display_slider = array_key_exists('display_slider', $abprf_infos) ? $abprf_infos['display_slider'] : 'on';
				$abprf_slider = ABPRF_LIB_Function::get_option('abprf_slider');
				$image_ids = array_unique(ABPRF_LIB_Function::get_post_info($post_id, 'abprf_sliders', array()));
				if (sizeof($image_ids) > 0 && $display_slider == 'on') {
					if ( sizeof($image_ids) > 1) { ?>
                        <div class="abprf_slider abprf_cover <?php echo esc_attr($class); ?>">
							<?php $this->slider_all_item($abprf_slider, $image_ids); ?>
                        </div>
					<?php } else {
						$thumb_id = $image_ids[0];
						$thumb_id=$thumb_id?:get_post_thumbnail_id($post_id);
						ABPRF_Layout::bg_image('', $thumb_id, ABPRF_BLANK_IMG_URL);
					}
				} else {
					$thumb_id = get_post_thumbnail_id($post_id);
					ABPRF_Layout::bg_image( '', $thumb_id,ABPRF_BLANK_IMG_URL);
				}
			}
			public function slider($abprf_slider, $post_id, $image_ids): void {
				if (is_array($image_ids) && sizeof($image_ids) > 0) {
					$showcase_position = isset($abprf_slider['showcase_position']) && $abprf_slider['showcase_position'] ? $abprf_slider['showcase_position'] : 'right';
					$slider_style = isset($abprf_slider['slider_style']) && $abprf_slider['slider_style'] ? $abprf_slider['slider_style'] : 'style_1';
					$slider_indicator = isset($abprf_slider['indicator_visible']) && $abprf_slider['indicator_visible'] ? $abprf_slider['indicator_visible'] : 'on';
					$icon = isset($abprf_slider['indicator_type']) && $abprf_slider['indicator_type'] ? $abprf_slider['indicator_type'] : 'icon';
					$column_class = $showcase_position == 'top' || $showcase_position == 'bottom' ? 'area_column' : '';
					?>
                    <div class="abprf_slider abprf_cover _fd_column">
                        <div class="_d_flex _full_width  <?php echo esc_attr($column_class); ?>">
							<?php
								if ($showcase_position == 'top' || $showcase_position == 'left') {
									$this->slider_showcase($abprf_slider, $image_ids);
								}
								$this->slider_all_item($abprf_slider, $image_ids);
								if ($showcase_position == 'bottom' || $showcase_position == 'right') {
									$this->slider_showcase($abprf_slider, $image_ids);
								}
								if ($slider_style == 'style_2') {
									?>
                                    <div class="abTopLeft">
                                        <button type="button" class="_btn_default_bg_white_color_default" data-target-popup="abprf_slider" data-slide-index="1">
											<?php echo esc_html__('View All', 'abprf-rental-forge') . ' ' . esc_html(sizeof($image_ids)) . ' ' . esc_html__('Images', 'abprf-rental-forge'); ?>
                                        </button>
                                    </div>
									<?php
								}
							?>
                        </div>
						<?php
							if ($slider_indicator == 'on' && $icon == 'image') {
								$this->image_indicator($image_ids);
							}
							$this->slider_popup($abprf_slider, $post_id, $image_ids); ?>
                    </div>
					<?php
				}
			}
			public function slider_all_item($abprf_slider, $image_ids, $popup_slider_icon = ''): void {
				if (is_array($image_ids) && sizeof($image_ids) > 0) {
					$icon = isset($abprf_slider['indicator_type']) && $abprf_slider['indicator_type'] ? $abprf_slider['indicator_type'] : 'icon';
					?>
                    <div class="slider_item_area">
						<?php $count = 1;
							foreach ($image_ids as $id) {
								$image_url = ABPRF_LIB_Function::get_image_url('', $id); ?>
                                <div class="slider_item" data-slide-index="<?php echo esc_attr($count); ?>" <?php if ($popup_slider_icon == 'on') { ?> data-target-popup="abprf_slider" <?php } ?> data-placeholder>
                                    <div data-bg-image="<?php echo esc_url($image_url); ?>"></div>
                                </div>
								<?php
								$count++;
							}
							if (($icon == 'icon' || $popup_slider_icon == 'on') && sizeof($image_ids) > 1) {
								$slider_indicator = isset($abprf_slider['indicator_visible']) && $abprf_slider['indicator_visible'] ? $abprf_slider['indicator_visible'] : 'on';
								if ($slider_indicator == 'on' || $popup_slider_icon == 'on') {
									?>
                                    <div class="icon_direction prev_item">
                                        <span class="fas fa-chevron-left"></span>
                                    </div>
                                    <div class="icon_direction next_item">
                                        <span class="fas fa-chevron-right"></span>
                                    </div>
									<?php
								}
							}
						?>
                    </div>
					<?php
				}
			}
			public function slider_showcase($abprf_slider, $image_ids): void {
				$showcase = isset($abprf_slider['showcase_visible']) && $abprf_slider['showcase_visible'] ? $abprf_slider['showcase_visible'] : 'on';
				$showcase_position = isset($abprf_slider['showcase_position']) && $abprf_slider['showcase_position'] ? $abprf_slider['showcase_position'] : 'right';
				$slider_style = isset($abprf_slider['slider_style']) && $abprf_slider['slider_style'] ? $abprf_slider['slider_style'] : 'style_1';
				if ($showcase == 'on' && is_array($image_ids) && sizeof($image_ids) > 0) {
					?>
                    <div class="slider_img_list <?php echo esc_attr($showcase_position . ' ' . $slider_style); ?>">
						<?php
							if ($slider_style == 'style_1') {
								$this->slider_showcase_style_1($image_ids);
							} else {
								$this->slider_showcase_style_2($image_ids);
							}
						?>
                    </div>
					<?php
				}
			}
			public function slider_showcase_style_1($image_ids): void {
				$count = 1;
				foreach ($image_ids as $id) {
					$image_url = ABPRF_LIB_Function::get_image_url('', $id);
					if ($count < 4) {
						?>
                        <div class="slider_img_list_item" data-slide-target="<?php echo esc_attr($count); ?>" data-placeholder>
                            <div data-bg-image="<?php echo esc_url($image_url); ?>"></div>
                        </div>
						<?php
					}
					if ($count == 4) {
						?>
                        <div class="slider_img_list_item" data-target-popup="abprf_slider" data-placeholder>
                            <div data-bg-image="<?php echo esc_url($image_url); ?>"></div>
                            <div class="slider_more_item">
                                <span class="fas fa-plus"></span>
								<?php echo esc_html(sizeof($image_ids) - 4); ?>
                                <span class="far fa-image"></span>
                            </div>
                        </div>
						<?php
					}
					$count++;
				}
			}
			public function slider_showcase_style_2($image_ids): void {
				$count = 1;
				foreach ($image_ids as $id) {
					$image_url = ABPRF_LIB_Function::get_image_url('', $id);
					if ($count > 1 && $count < 5) {
						?>
                        <div class="slider_img_list_item" data-target-popup="abprf_slider" data-slide-index="<?php echo esc_attr($count); ?>" data-placeholder>
                            <div data-bg-image="<?php echo esc_url($image_url); ?>"></div>
                        </div>
						<?php
					}
					$count++;
				}
			}
			public function image_indicator($image_ids): void {
				if (is_array($image_ids) && sizeof($image_ids) > 0) {
					?>
                    <div class="slide_direction">
						<?php
							$count = 1;
							foreach ($image_ids as $id) {
								$image_url = ABPRF_LIB_Function::get_image_url('', $id, array(150, 100));
								?>
                                <div class="slider_direction_item" data-slide-target="<?php echo esc_attr($count); ?>">
                                    <div data-bg-image="<?php echo esc_url($image_url); ?>"></div>
                                </div>
								<?php
								$count++;
							}
						?>
                    </div>
					<?php
				}
			}
			public function slider_popup($abprf_slider, $post_id, $image_ids): void {
				if (is_array($image_ids) && sizeof($image_ids) > 0) {
					$active_popup = isset($abprf_slider['visible_popup']) && $abprf_slider['visible_popup'] ? $abprf_slider['visible_popup'] : 'on';
					$popup_icon_indicator = isset($abprf_slider['popup_icon_indicator']) && $abprf_slider['popup_icon_indicator'] ? $abprf_slider['popup_icon_indicator'] : 'on';
					$indicator = isset($abprf_slider['popup_image_indicator']) && $abprf_slider['popup_image_indicator'] ? $abprf_slider['popup_image_indicator'] : 'on';
					if ($active_popup == 'on') {
						?>
                        <div class="slider_popup" data-popup="abprf_slider">
                            <div class="abprf_slider">
                                <div class="popup_head">
                                    <h2 class="_abprf"><?php echo esc_html(get_the_title($post_id)); ?></h2>
                                    <span class="popup_close _circle"><i class="fas fa-times"></i></span>
                                </div>
                                <div class="popup_body">
									<?php $this->slider_all_item($abprf_slider, $image_ids, $popup_icon_indicator); ?>
                                </div>
                                <div class="popup_foot">
									<?php if ($indicator == 'on') {
										$this->image_indicator($image_ids);
									} ?>
                                </div>
                            </div>
                        </div>
						<?php
					}
				}
			}
			//==============================//
			public static function boarding_from($route_bp, $transport_bp = ''): void {
				?>
                <label>
                    <span><i class="fas fa-map-marker-alt _mar_r_xxs"></i><?php esc_html_e('From', 'abprf-rental-forge'); ?><sup class="_color_required">*</sup></span>
                    <input type="hidden" name="_bp" value="<?php echo esc_attr($transport_bp); ?>" data-alert="<?php esc_attr_e('Please Select from below list.', 'abprf-rental-forge'); ?>"/>
                    <input type="text" class="_form_control_full_width " name="_bp_dummy" placeholder="<?php esc_attr_e('Boarding', 'abprf-rental-forge'); ?>" value="<?php echo esc_attr($transport_bp); ?>" autocomplete="off" required/>
                </label>
				<?php ABPRF_Layout::input_dropdown($route_bp, 'fas fa-map-marker-alt');
			}
			public static function dropping_from($route_dp, $transport_dp = ''): void {
				?>
                <label>
                    <span><i class="fas fa-map-marker-alt _mar_r_xxs"></i><?php esc_html_e('To', 'abprf-rental-forge'); ?><sup class="_color_required">*</sup></span>
                    <input type="hidden" name="_dp" value="<?php echo esc_attr($transport_dp); ?>" data-alert="<?php esc_attr_e('Please Select from below list.', 'abprf-rental-forge'); ?>"/>
                    <input type="text" class="_form_control_full_width " name="_dp_dummy" placeholder="<?php esc_attr_e('Dropping', 'abprf-rental-forge'); ?>" value="<?php echo esc_attr($transport_dp); ?>" autocomplete="off" required/>
                </label>
				<?php ABPRF_Layout::input_dropdown($route_dp, 'fas fa-map-marker-alt'); ?>
				<?php
			}
			public static function departure_date($post_id = '', $bp = '', $date = ''): void {
				$date_format = ABPRF_LIB_Function::date_picker_format();
				$now = date_i18n($date_format, strtotime(current_time('Y-m-d')));
				$hidden_date = $date ? gmdate('Y-m-d', strtotime($date)) : '';
				$visible_date = $date ? date_i18n($date_format, strtotime($date)) : '';
				?>
                <label>
                    <span><i class="fas fa-calendar-check _mar_r_xxs"></i><?php esc_html_e('Journey Date', 'abprf-rental-forge'); ?><sup class="_color_required">*</sup></span>
                    <input type="hidden" name="_j_date" value="<?php echo esc_attr($hidden_date); ?>" required/>
                    <input id="abptm_bp_date" type="text" value="<?php echo esc_attr($visible_date); ?>" class="_form_control" placeholder="<?php echo esc_attr($now); ?>" data-alert="<?php esc_attr_e('Please Select Journey Route', 'abprf-rental-forge'); ?>" readonly required/>
                    <span class="fas fa-times date_close_icon" title="<?php esc_attr_e('Clear Date', 'abprf-rental-forge'); ?>"></span>
                </label>
				<?php
				if ($bp || $post_id) {
					$all_dates = ABPRF_Function::get_dates($post_id, $bp);
					do_action('abprf_load_date_picker', '#abptm_bp_date', $all_dates);
				}
			}
			public static function return_date($bp, $dp, $bp_date, $return_date = ''): void {
				$date_format = ABPRF_LIB_Function::date_picker_format();
				$now = date_i18n($date_format, strtotime(current_time('Y-m-d')));
				$hidden_date = $return_date ? gmdate('Y-m-d', strtotime($return_date)) : '';
				$visible_date = $return_date ? date_i18n($date_format, strtotime($return_date)) : '';
				?>
                <label>
                    <span><i class="fas fa-calendar-check _mar_r_xxs"></i><?php esc_html_e('Return Date', 'abprf-rental-forge'); ?></span>
                    <input type="hidden" name="_r_date" value="<?php echo esc_attr($hidden_date); ?>" required/>
                    <input id="abptm_return_date" type="text" value="<?php echo esc_attr($visible_date); ?>" class="_form_control" placeholder="<?php echo esc_attr($now); ?>" readonly/>
                    <span class="fas fa-times date_close_icon" title="<?php esc_attr_e('Clear Date', 'abprf-rental-forge'); ?>"></span>
                </label>
				<?php
				if ($dp) {
					$all_dates = ABPRF_Function::get_dates(0, $dp, $bp);
					if (sizeof($all_dates) > 0) {
						$bp_date = strtotime($bp_date);
						$date_list = [];
						foreach ($all_dates as $date) {
							if (strtotime($date) >= $bp_date) {
								$date_list[] = $date;
							}
						}
						do_action('abprf_load_date_picker', '#abptm_return_date', $date_list);
					}
				}
			}
			public static function transport_list($form_data): void {
				$_post_id = array_key_exists('_post_id', $form_data) ? $form_data['_post_id'] : 0;
				$post_id = array_key_exists('post_id', $form_data) ? $form_data['post_id'] : 0;
				$_bp = array_key_exists('_bp', $form_data) ? $form_data['_bp'] : '';
				$_dp = array_key_exists('_dp', $form_data) ? $form_data['_dp'] : '';
				$_j_date = array_key_exists('_j_date', $form_data) ? $form_data['_j_date'] : '';
				$_r_date = array_key_exists('_r_date', $form_data) ? $form_data['_r_date'] : '';
				if ($_bp && $_dp && ($_j_date || $_r_date)) {
					if ($_post_id > 0 || $post_id > 0) {
						if ($_post_id > 0) {
							$form_data['bp'] = $_bp;
							$form_data['dp'] = $_dp;
							$form_data['j_date'] = $_j_date;
						}
						$form_data['post_id'] = max($_post_id, $post_id);
						do_action('abptm_registration', $form_data);
					} else {
						$form_data['bp'] = $_bp;
						$form_data['dp'] = $_dp;
						$form_data['j_date'] = $_j_date;
						self::transport_search($form_data);
						if ($_r_date) {
							$form_data['bp'] = $_dp;
							$form_data['dp'] = $_bp;
							$form_data['j_date'] = $_r_date;
							?>
                            <div class="abptm_return_trip_area _mar_t_40">
                                <div class="_divider"></div>
                                <h3 class="_abprf_color_navy_blue_text_center"><span class="fas fa-hand-point-down _mar_r_xs"></span><?php esc_html_e('Return Trips', 'abprf-rental-forge'); ?></h3>
                                <div class="_divider"></div>
								<?php self::transport_search($form_data); ?>
                            </div>
						<?php }
					}
				}
			}
			public static function transport_search($form_data): void {
				$bp = array_key_exists('bp', $form_data) ? $form_data['bp'] : '';
				$dp = array_key_exists('dp', $form_data) ? $form_data['dp'] : '';
				$j_date = array_key_exists('j_date', $form_data) ? $form_data['j_date'] : '';
				$transport_items = ABPRF_Function::get_transport_list_details($bp, $dp, $j_date);
				do_action('abptm_next_prev_day', $form_data);
				if (sizeof($transport_items) > 0) {
					foreach ($transport_items as $transport_item) {
						do_action('abptm_search_list', $form_data, $transport_item);
					}
				} else {
					ABPRF_Layout::layout_warning_info('no_transport_found');
				}
			}
			public static function hidden_search_form($form_data): void {
				?>
                <input type="hidden" name="_post_id" value="<?php echo esc_attr(array_key_exists('_post_id', $form_data) ? $form_data['_post_id'] : ''); ?>"/>
                <input type="hidden" name="_bp" value="<?php echo esc_attr(array_key_exists('_bp', $form_data) ? $form_data['_bp'] : ''); ?>"/>
                <input type="hidden" name="_dp" value="<?php echo esc_attr(array_key_exists('_dp', $form_data) ? $form_data['_dp'] : ''); ?>"/>
                <input type="hidden" name="_j_date" value="<?php echo esc_attr(array_key_exists('_j_date', $form_data) ? $form_data['_j_date'] : ''); ?>"/>
                <input type="hidden" name="_r_date" value="<?php echo esc_attr(array_key_exists('_r_date', $form_data) ? $form_data['_r_date'] : ''); ?>"/>
				<?php
			}
			//=============================//
			public static function get_seat_plan($abprf_infos, $bp, $dp, $bp_date, $sp_infos, $sold_seats, $ud = false): void {
				$post_id = array_key_exists('post_id', $abprf_infos) ? $abprf_infos['post_id'] : 0;
				$display_ticket_type = array_key_exists('display_ticket_type', $abprf_infos) ? $abprf_infos['display_ticket_type'] : 'off';
				$ticket_type = array_key_exists('ticket_type', $abprf_infos) ? $abprf_infos['ticket_type'] : '';
				$ticket_type_array = $ticket_type ? explode(',', $ticket_type) : ['default'];
				$ticket_names_array = ABPRF_Function::get_ticket_type();
				$ticket_type_key = 'price';
				$adult_price = '';
				$child_price = '';
				$infant_price = '';
				if ($display_ticket_type == 'on' && sizeof($ticket_type_array) > 0) {
					$adult_price = in_array('adult', $ticket_type_array) ? ABPRF_Function:: get_price($post_id, $bp, $dp, 'adult', $ud) : '';
					$adult_price = $adult_price ? ABPRF_LIB_Function::get_wc_raw_price($post_id, $adult_price) : '';
					$child_price = in_array('child', $ticket_type_array) ? ABPRF_Function:: get_price($post_id, $bp, $dp, 'child', $ud) : '';
					$child_price = $child_price ? ABPRF_LIB_Function::get_wc_raw_price($post_id, $child_price) : '';
					$infant_price = in_array('infant', $ticket_type_array) ? ABPRF_Function:: get_price($post_id, $bp, $dp, 'infant', $ud) : '';
					$infant_price = $infant_price ? ABPRF_LIB_Function::get_wc_raw_price($post_id, $infant_price) : '';
					$ticket_type_key = $infant_price ? 'infant' : 'child';
					$ticket_type_key = $adult_price ? 'adult' : $ticket_type_key;
				}
				$d_price = $d_price ?? ABPRF_Function:: get_price($post_id, $bp, $dp, $ticket_type_key, true);
				?>
                <table class="_abprf">
					<?php foreach ($sp_infos as $sp_ud_info) {
						if (is_array($sp_ud_info) && sizeof($sp_ud_info) > 0) { ?>
                            <tr>
								<?php foreach ($sp_ud_info as $sp_ld) {
									$seat_type = explode('_@@_', $sp_ld)[0];
									$text = explode('_@@_', $sp_ld)[1];
									$text = explode('_&&_', $text)[0];
									$colspan = max((int)explode('_&&_', $sp_ld)[1], 0);
									if ($colspan > 0) { ?>
                                        <th colspan="<?php echo esc_attr($colspan); ?>">
											<?php if ($seat_type == 'ticket') {
												if (in_array($text, $sold_seats)) {
													$seat_class = 'seat_sold';
													$seat_title = __('Sold', 'abprf-rental-forge') . ' :  ' . $text;
												} elseif (ABPRF_Function::already_in_cart($post_id, $bp, $dp, $bp_date, $text) > 0) {
													$seat_class = 'seat_cart';
													$seat_title = __('Already in Cart', 'abprf-rental-forge') . ' :  ' . $text;
												} else {
													$seat_class = 'seat_sale';
													$seat_title = __('On Sale ', 'abprf-rental-forge') . ' :  ' . $text;
												}
												?>
                                                <div class="seat_item <?php echo esc_attr($seat_class); ?>" title="<?php echo esc_attr($seat_title); ?>"
                                                     data-name="<?php echo esc_attr($text); ?>" data-price="<?php echo esc_attr($d_price); ?>" data-type="<?php echo esc_attr($ticket_type_key); ?>"
													<?php if ($seat_class == 'seat_sale' && $display_ticket_type == 'on' && sizeof($ticket_type_array) > 0) { ?>
                                                        data-label="<?php echo esc_attr($ticket_names_array['adult']); ?>"
													<?php } ?>
                                                >
													<?php if (in_array($text, $sold_seats)) { ?>
                                                        <span class="fas fa-times"></span>
													<?php } else {
														echo esc_html($text);
													} ?>
                                                </div>
												<?php if ($seat_class == 'seat_sale' && $display_ticket_type == 'on' && sizeof($ticket_type_array) > 0) { ?>
                                                    <div class="_transition ticket_type_list">
                                                        <ul class="_abprf_list">
															<?php if ($adult_price) { ?>
                                                                <li data-price="<?php echo esc_attr($adult_price); ?>" data-type="adult" data-label="<?php echo esc_attr($ticket_names_array['adult']); ?>"><?php echo esc_html($ticket_names_array['adult'] . esc_html__(' : ', 'abprf-rental-forge')); ?><strong class="_abprf"><?php echo $adult_price > 0 ? wp_kses_post(wc_price($adult_price)) : esc_html__('Free', 'abprf-rental-forge'); ?></strong></li>
															<?php }
																if ($child_price) { ?>
                                                                    <li data-price="<?php echo esc_attr($child_price); ?>" data-type="child" data-label="<?php echo esc_attr($ticket_names_array['child']); ?>"><?php echo esc_html($ticket_names_array['child']) . esc_html__(' : ', 'abprf-rental-forge'); ?><strong class="_abprf"><?php echo $child_price > 0 ? wp_kses_post(wc_price($child_price)) : esc_html__('Free', 'abprf-rental-forge'); ?></strong></li>
																<?php }
																if ($infant_price) { ?>
                                                                    <li data-price="<?php echo esc_attr($infant_price); ?>" data-type="infant" data-label="<?php echo esc_attr($ticket_names_array['infant']); ?>"><?php echo esc_html($ticket_names_array['infant']) . esc_html__(' : ', 'abprf-rental-forge'); ?><strong class="_abprf"><?php echo $infant_price > 0 ? wp_kses_post(wc_price($infant_price)) : esc_html__('Free', 'abprf-rental-forge'); ?></strong></li>
																<?php } ?>
                                                        </ul>
                                                    </div>
													<?php
												}
											} else if ($seat_type == 'driver') { ?>
                                                <div class="abprf_bg_img">
                                                    <div data-bg-image="<?php echo esc_url(ABPRF_URL . '/assets/images/suspension.png'); ?>"></div>
                                                </div>
											<?php } else if ($seat_type == 'text') {
												echo esc_html($text);
											} else {
												echo esc_html('');
											} ?>
                                        </th>
									<?php }
								} ?>
                            </tr>
						<?php } ?>
					<?php } ?>
                </table>
				<?php
			}
			//=============================//
			public static function filter_transport($post_id = 0): void {
				$abprf_configuration = ABPRF_LIB_Function::get_option('abprf_configuration');
				$label = isset($abprf_configuration['label']) && $abprf_configuration['label'] ? $abprf_configuration['label'] : __('RentalForge', 'abprf-rental-forge');
				$equipment_ids = ABPRF_Query::get_equipment_id();
				$value = $post_id > 0 ? $post_id : '';
				$display_category = $post_id > 0 ? ABPRF_LIB_Function::get_post_info($post_id, 'display_category', 'on') : '';
				$category = $post_id > 0 ? ABPRF_LIB_Function::get_post_info($post_id, 'category') : '';
				$post_title = $post_id > 0 ? (get_the_title($post_id) . ' ' . ($category && $display_category == 'on' ? ' -  ' . $category : '')) : '';
				$equipment_icon = isset($abprf_configuration['equipment_icon']) && $abprf_configuration['equipment_icon'] ? $abprf_configuration['equipment_icon'] : 'fas fa-hammer';
				?>
                <div class="_input_item dropdown_area">
                    <label>
                        <span><i class="<?php echo esc_attr($equipment_icon); ?> _mar_r_xs"></i><?php esc_html_e('Rental', 'abprf-rental-forge'); ?></span>
                        <input type="hidden" name="_post_id" value="<?php echo esc_attr($value); ?>"/>
                        <input type="text" class="_form_control_full_width" name="" placeholder="<?php echo esc_attr($label); ?>" value="<?php echo esc_attr($post_title); ?>"/>
                    </label>
					<?php if (sizeof($equipment_ids) > 0) { ?>
                        <ul class="_abprf dropdown_input">
							<?php foreach ($equipment_ids as $equipment_id) {
								$display_id = ABPRF_LIB_Function::get_post_info($equipment_id, 'display_equipment_id', 'on');
								$id = ABPRF_LIB_Function::get_post_info($equipment_id, 'equipment_id');
								$display_category = ABPRF_LIB_Function::get_post_info($equipment_id, 'display_category', 'on');
								$category = ABPRF_LIB_Function::get_post_info($equipment_id, 'category');
								?>
                                <li data-value="<?php echo esc_attr(get_the_title($equipment_id) . ' ' . $id . ' ' . $category); ?>">
                                    <span class="<?php echo esc_attr($equipment_icon); ?>"></span>
                                    <span data-id="<?php echo esc_attr($equipment_id); ?>" data-text><?php echo esc_html(get_the_title($equipment_id) . ' ' . ($category && $display_category == 'on' ? ' -  ' . $category : '')); ?></span>
									<?php if ($id && $display_id == 'on') { ?>
                                        <span class="_abprf_color_gray"><?php echo esc_html(' - ' . $id); ?></span>
									<?php } ?>
                                </li>
							<?php } ?>
                        </ul>
					<?php } ?>
                </div>
				<?php
			}
		}
		new ABPRF_Layout();
	}