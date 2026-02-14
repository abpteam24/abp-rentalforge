<?php
	if (!defined('ABSPATH')) {
		exit; // Exit if accessed directly
	}
	if (!class_exists('ABPRF_LIB_Layout')) {
		class ABPRF_LIB_Layout {
			public function __construct() {
				add_action('abprf_add_icon', array($this, 'load_icon'), 10, 2);
				add_action('abprf_add_image', array($this, 'add_single_image'), 10, 2);
				add_action('abprf_add_image_multiple', array($this, 'abprf_add_image_multi'), 10, 2);
				add_action('abprf_add_image_icon', array($this, 'selection_icon_image'), 10, 3);
				//==============================//
				add_action('abprf_slider', array($this, 'full_slider'));
				add_action('abprf_slider_only', array($this, 'slider_only'), 10, 2);
				//==============================//
			}
			public static function info_text($key): void {
				$data = ABPRF_Static_Array::array_info($key);
				if ($data) {
					echo '<span class="info_text"><i class="fas fa-info-circle _color_theme"></i>' . esc_html($data) . '</span>';
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
					<?php ABPRF_LIB_Layout::button_add(__('Add  Image', 'abprf-rental-forge'), 'abprf_add_image_multi'); ?>
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
			public function full_slider($post_id = ''): void {
				$abprf_slider = ABPRF_LIB_Function::get_option('abprf_slider');
				$type = isset($abprf_slider['slider_type']) && $abprf_slider['slider_type'] ? $abprf_slider['slider_type'] : 'on';
				$post_id = $post_id > 0 ? $post_id : get_the_id();
				$image_ids = $this->get_slider_ids($post_id);
				if (is_array($image_ids) && sizeof($image_ids) > 0) {
					if ($type == 'on' && sizeof($image_ids) > 1) {
						$this->slider($abprf_slider, $post_id, $image_ids);
					} else {
						ABPRF_LIB_Layout::bg_image('', $image_ids[0], ABPRF_BLANK_IMG_URL, 'abprf_slider');
					}
				} else {
					ABPRF_LIB_Layout::bg_image($post_id, '', ABPRF_BLANK_IMG_URL, 'abprf_slider');
				}
			}
			public function slider_only($post_id, $class = ''): void {
				$abprf_slider = ABPRF_LIB_Function::get_option('abprf_slider');
				$type = isset($abprf_slider['slider_type']) && $abprf_slider['slider_type'] ? $abprf_slider['slider_type'] : 'on';
				$post_id = $post_id > 0 ? $post_id : get_the_id();
				$image_ids = $this->get_slider_ids($post_id);
				if (is_array($image_ids) && sizeof($image_ids) > 0) {
					if ($type == 'on' && sizeof($image_ids) > 1) { ?>
                        <div class="abprf_slider abprf_cover <?php echo esc_attr($class); ?>">
							<?php $this->slider_all_item($abprf_slider, $image_ids); ?>
                        </div>
					<?php } else {
						ABPRF_LIB_Layout::bg_image('', current($image_ids), ABPRF_BLANK_IMG_URL);
					}
				} else {
					ABPRF_LIB_Layout::bg_image($post_id, '', ABPRF_BLANK_IMG_URL);
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
			//=============================//
			public function get_slider_ids($post_id) {
				$thumb_id = get_post_thumbnail_id($post_id);
				$image_ids = ABPRF_LIB_Function::get_post_info($post_id, 'abprf_sliders', array());
				if ($thumb_id) {
					array_unshift($image_ids, $thumb_id);
				}
				return array_unique($image_ids);
			}
			//==============================//
		}
		new ABPRF_LIB_Layout();
	}