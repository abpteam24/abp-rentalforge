<?php
	if (!defined('ABSPATH')) {
		exit; // Exit if accessed directly
	}
	if (!class_exists('ABPRF_Layout')) {
		class ABPRF_Layout {
			public function __construct() { }
			public static function boarding_from($route_bp, $transport_bp = ''): void {
				?>
                <label>
                    <span><i class="fas fa-map-marker-alt _mar_r_xxs"></i><?php esc_html_e('From', 'abprf-rental-forge'); ?><sup class="_color_required">*</sup></span>
                    <input type="hidden" name="_bp" value="<?php echo esc_attr($transport_bp); ?>" data-alert="<?php esc_attr_e('Please Select from below list.', 'abprf-rental-forge'); ?>"/>
                    <input type="text" class="_form_control_full_width " name="_bp_dummy" placeholder="<?php esc_attr_e('Boarding', 'abprf-rental-forge'); ?>" value="<?php echo esc_attr($transport_bp); ?>" autocomplete="off" required/>
                </label>
				<?php ABPRF_LIB_Layout::input_dropdown($route_bp, 'fas fa-map-marker-alt');
			}
			public static function dropping_from($route_dp, $transport_dp = ''): void {
				?>
                <label>
                    <span><i class="fas fa-map-marker-alt _mar_r_xxs"></i><?php esc_html_e('To', 'abprf-rental-forge'); ?><sup class="_color_required">*</sup></span>
                    <input type="hidden" name="_dp" value="<?php echo esc_attr($transport_dp); ?>" data-alert="<?php esc_attr_e('Please Select from below list.', 'abprf-rental-forge'); ?>"/>
                    <input type="text" class="_form_control_full_width " name="_dp_dummy" placeholder="<?php esc_attr_e('Dropping', 'abprf-rental-forge'); ?>" value="<?php echo esc_attr($transport_dp); ?>" autocomplete="off" required/>
                </label>
				<?php ABPRF_LIB_Layout::input_dropdown($route_dp, 'fas fa-map-marker-alt'); ?>
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
					do_action('abptm_load_date_picker', '#abptm_bp_date', $all_dates);
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
						do_action('abptm_load_date_picker', '#abptm_return_date', $date_list);
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
					ABPRF_LIB_Layout::layout_warning_info('no_transport_found');
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
				$label = isset($abprf_configuration['label']) && $abprf_configuration['label'] ? $abprf_configuration['label'] : __('Transportation', 'abprf-rental-forge');
				$transport_ids = ABPRF_Query::get_transport_id();
				$value = $post_id > 0 ? $post_id : '';
				$display_category = $post_id > 0 ? ABPRF_LIB_Function::get_post_info($post_id, 'display_category', 'on') : '';
				$category = $post_id > 0 ? ABPRF_LIB_Function::get_post_info($post_id, 'category') : '';
				$post_title = $post_id > 0 ? (get_the_title($post_id) . ' ' . ($category && $display_category == 'on' ? ' -  ' . $category : '')) : '';
				$transport_icon = isset($abprf_configuration['transport_icon']) && $abprf_configuration['transport_icon'] ? $abprf_configuration['transport_icon'] : 'fas fa-bus';
				?>
                <div class="_input_item dropdown_area">
                    <label>
                        <span><i class="<?php echo esc_attr($transport_icon); ?> _mar_r_xs"></i><?php esc_html_e('Rental', 'abprf-rental-forge'); ?></span>
                        <input type="hidden" name="_post_id" value="<?php echo esc_attr($value); ?>"/>
                        <input type="text" class="_form_control_full_width" name="" placeholder="<?php echo esc_attr($label); ?>" value="<?php echo esc_attr($post_title); ?>"/>
                    </label>
					<?php if (sizeof($transport_ids) > 0) { ?>
                        <ul class="_abprf dropdown_input">
							<?php foreach ($transport_ids as $transport_id) {
								$display_id = ABPRF_LIB_Function::get_post_info($transport_id, 'display_transport_id', 'on');
								$id = ABPRF_LIB_Function::get_post_info($transport_id, 'transport_id');
								$display_category = ABPRF_LIB_Function::get_post_info($transport_id, 'display_category', 'on');
								$category = ABPRF_LIB_Function::get_post_info($transport_id, 'category');
								?>
                                <li data-value="<?php echo esc_attr(get_the_title($transport_id) . ' ' . $id . ' ' . $category); ?>">
                                    <span class="<?php echo esc_attr($transport_icon); ?>"></span>
                                    <span data-id="<?php echo esc_attr($transport_id); ?>" data-text><?php echo esc_html(get_the_title($transport_id) . ' ' . ($category && $display_category == 'on' ? ' -  ' . $category : '')); ?></span>
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