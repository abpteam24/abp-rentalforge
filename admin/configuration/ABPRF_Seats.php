<?php
	if (!defined('ABSPATH')) {
		exit; // Exit if accessed directly
	}
	if (!class_exists('ABPRF_Seats')) {
		class ABPRF_Seats {
			public function __construct() {
				add_action('abprf_post_content', [$this, 'tab_content']);
				//=============================//
				add_action('wp_ajax_abprf_create_sp', [$this, 'abprf_create_sp']);
				add_action('wp_ajax_nopriv_abprf_create_sp', [$this, 'abprf_create_sp']);
				//=============================//
				add_action('wp_ajax_get_abprf_ticketing', [$this, 'get_abprf_ticketing']);
				add_action('wp_ajax_nopriv_get_abprf_ticketing', [$this, 'get_abprf_ticketing']);
			}
			public function tab_content($abprf_infos): void {
				$abprf_configuration = array_key_exists('abprf_configuration', $abprf_infos) ? $abprf_infos['abprf_configuration'] : [];
				$post_title = array_key_exists('post_title', $abprf_infos) ? $abprf_infos['post_title'] : '';
				$transport_icon = isset($abprf_configuration['transport_icon']) && $abprf_configuration['transport_icon'] ? $abprf_configuration['transport_icon'] : 'fas fa-bus';
				$seat_type = array_key_exists('seat_type', $abprf_infos) ? $abprf_infos['seat_type'] : 'seat_plan';
				?>
                <div class="tabsItem abptm_seats" data-tabs="#abptm_seats">
                    <h4 class="_abprf_color_theme"><span class="<?php echo esc_attr($transport_icon); ?> _mar_r_xs"></span> <?php echo esc_html($post_title . ' ' . __(' : ', 'abprf-rental-forge') . ' ' . __('Ticket Configuration', 'abprf-rental-forge')); ?></h4>
                    <div class="_divider_xs"></div>
					<?php $this->select_seat_type($seat_type); ?>
                    <div class="abprf_ticketing">
						<?php if ($seat_type == 'seat_plan') {
							$this->seat_plan($abprf_infos);
						} else {
							$this->ticket_type($abprf_infos);
						} ?>
                    </div>
                </div>
				<?php
			}
			public function seat_plan($abprf_infos): void {
				$display_ud = array_key_exists('display_ud', $abprf_infos) ? $abprf_infos['display_ud'] : 'off';
				$this->select_ticket_type($abprf_infos); ?>
                <div class="abptm_seat_plan_configuration">
					<?php $this->show_upper_deck($display_ud); ?>
                    <div class="_abprf_panel ">
                        <div class="_panel_head">
                            <h4 class="_abprf_color_light"><?php esc_html_e('Seat Plan Configuration', 'abprf-rental-forge'); ?></h4>
                        </div>
                        <div class="_panel_body_no_gap">
                            <div class="_f_equal">
								<?php $this->sp_ld($abprf_infos); ?>
                                <div data-collapse="#display_ud" class="_ov_hidden_padding_xs_border_l <?php echo esc_attr($display_ud == 'on' ? 'rf_active' : ''); ?>">
									<?php $this->sp_ud($abprf_infos); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="sp_seat_configure _max_300_br_reflex_10">
                        <label class="_f_equal">
                            <span><?php esc_html_e('Seat Type', 'abprf-rental-forge'); ?></span>
                            <select class="_form_control " name="abptm_seat_item">
                                <option value="" disabled selected><?php esc_html_e('Please Select', 'abprf-rental-forge'); ?></option>
                                <option value="ticket"><?php esc_html_e('Ticket', 'abprf-rental-forge'); ?></option>
                                <option value="blank"><?php esc_html_e('Blank Space', 'abprf-rental-forge'); ?></option>
                                <option value="driver"><?php esc_html_e('Driver', 'abprf-rental-forge'); ?></option>
                                <option value="text"><?php esc_html_e('Others', 'abprf-rental-forge'); ?></option>
                            </select>
                        </label>
                        <div class="_divider_xs"></div>
                        <label class="_f_equal">
                            <span><?php esc_html_e('Text or Seat Name', 'abprf-rental-forge'); ?></span>
                            <input type="text" class="_form_control validation_name" name="abptm_seat_text" placeholder="<?php esc_attr_e('Ex: A', 'abprf-rental-forge'); ?>" value=""/>
                        </label>
                        <div class="_divider_xs"></div>
                        <label class="_f_equal">
                            <span><?php esc_html_e('Extend area', 'abprf-rental-forge'); ?></span>
                            <input type="number" data-min="1" data-max="" pattern="[0-9]*" step="1" class="_form_control validation_number" name="abptm_extend_area" placeholder="<?php esc_attr_e('Ex: 2', 'abprf-rental-forge'); ?>" value=""/>
                        </label>
                    </div>
                </div>
				<?php
			}
			public function sp_ld($abprf_infos): void {
				$rows = array_key_exists('ld_rows', $abprf_infos) ? $abprf_infos['ld_rows'] : 0;
				$columns = array_key_exists('ld_columns', $abprf_infos) ? $abprf_infos['ld_columns'] : 0;
				$sp_ld_infos = array_key_exists('ld_infos', $abprf_infos) ? $abprf_infos['ld_infos'] : [];
				?>
                <div class="abptm_sp_ld _ov_hidden_padding_xs">
                    <div class="_setting_item_all_center">
                        <div class="_fd_column  _max_300">
							<?php $this->select_row_column($rows, $columns); ?>
							<?php ABPRF_LIB_Layout::button_add(__('Create Seat Plan', 'abprf-rental-forge'), 'abprf_create_sp', '_btn_theme_full_width_br_no_mar_t_xxs'); ?>
                        </div>
                    </div>
                    <div class="_all_center abprf_configuration_content abptm_sp_settings">
						<?php $this->create_sp($rows, $columns, false, $sp_ld_infos); ?>
                    </div>
                </div>
				<?php
			}
			public function sp_ud($abprf_infos): void {
				$rows_ud = array_key_exists('ud_rows', $abprf_infos) ? $abprf_infos['ud_rows'] : 0;
				$columns_ud = array_key_exists('ud_columns', $abprf_infos) ? $abprf_infos['ud_columns'] : 0;
				$sp_ud_infos = array_key_exists('ud_infos', $abprf_infos) ? $abprf_infos['ud_infos'] : [];
				?>
                <div class="abptm_sp_ud">
                    <div class="_setting_item_all_center">
                        <div class="_fd_column  _max_300">
							<?php $this->select_row_column($rows_ud, $columns_ud, true); ?>
							<?php ABPRF_LIB_Layout::button_add(__('Create Seat Plan Upper Deck', 'abprf-rental-forge'), 'abprf_create_sp_ud', '_btn_theme_full_width_br_no_mar_t_xxs'); ?>
                        </div>
                    </div>
                    <div class="_all_center abprf_configuration_content abptm_sp_settings">
						<?php $this->create_sp($rows_ud, $columns_ud, true, $sp_ud_infos); ?>
                    </div>
                </div>
				<?php
			}
			public function select_seat_type($seat_type): void {
				?>
                <div class="_setting_item">
                    <label class="_f_equal_max_500_f_wrap">
                        <span class="_mar_r_xs"><?php esc_html_e('Seat Type', 'abprf-rental-forge'); ?></span>
                        <select class="_form_control " name="seat_type" required>
                            <option disabled selected> <?php esc_html_e('Please Select', 'abprf-rental-forge'); ?></option>
                            <option value="seat_plan" <?php echo esc_attr($seat_type == 'seat_plan' ? 'selected' : ''); ?>><?php esc_html_e('Seat Plan', 'abprf-rental-forge'); ?></option>
                            <option value="ticket_type" <?php echo esc_attr($seat_type == 'ticket_type' ? 'selected' : ''); ?>><?php esc_html_e('Ticket', 'abprf-rental-forge'); ?></option>
                        </select>
                    </label>
                    <div class="_divider_xs"></div>
					<?php ABPRF_LIB_Layout::info_text('seat_type'); ?>
                </div>
				<?php
			}
			public function select_ticket_type($abprf_infos): void {
				$ticket_type = array_key_exists('ticket_type', $abprf_infos) ? $abprf_infos['ticket_type'] : '';
				$display = array_key_exists('display_ticket_type', $abprf_infos) ? $abprf_infos['display_ticket_type'] : 'off';
				$ticket_type_array = $ticket_type ? explode(',', $ticket_type) : ['default'];
				$ticket_names = ABPRF_Function::get_ticket_type();
				?>
                <div class="_setting_item">
                    <div class="_fa_center">
                        <div class="_fa_center_max_250">
							<?php ABPRF_LIB_Layout::switch_checkbox('display_ticket_type', $display); ?>
                            <span class="_fs_label_mar_lr_xs"><?php esc_html_e('Multiple Ticket Type', 'abprf-rental-forge'); ?></span>
                        </div>
                        <div data-collapse="#display_ticket_type" class="<?php echo esc_attr($display == 'on' ? 'rf_active' : ''); ?>">
                            <div class="abprf_checkbox">
                                <input type="hidden" name="ticket_type" value="<?php echo esc_attr($ticket_type); ?>"/>
								<?php foreach ($ticket_names as $key => $ticket_name) { ?>
                                    <div class="checkbox_item _min_150">
                                        <button type="button" class="_btn_white_xs <?php echo esc_attr(in_array($key, $ticket_type_array) ? 'rf_active' : ''); ?>" data-checked="<?php echo esc_attr($key); ?>" data-open-icon="far fa-check-square" data-close-icon="far fa-square">
                                            <span data-icon class="_mar_r_xs <?php echo esc_attr(in_array($key, $ticket_type_array) ? 'far fa-check-square' : 'far fa-square'); ?>"></span><?php echo esc_html($ticket_name); ?>
                                        </button>
                                    </div>
								<?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="_divider_xs"></div>
					<?php ABPRF_LIB_Layout::info_text('display_ticket_type'); ?>
                </div>
				<?php
			}
			public function show_upper_deck($display): void {
				?>
                <div class="_setting_item">
                    <div class="_fa_center">
						<?php ABPRF_LIB_Layout::switch_checkbox('display_ud', $display); ?>
                        <span class="_fs_label_mar_lr_xs"><?php esc_html_e('Display Upper Deck', 'abprf-rental-forge'); ?></span>
                    </div>
                </div>
				<?php
			}
			public function select_row_column($rows = 0, $columns = 0, $ud = false): void {
				$name_row = $ud ? 'ud_rows' : 'ld_rows';
				$name_column = $ud ? 'ud_columns' : 'ld_columns';
				?>
                <div class="_all_center_fs_label">
                    <span class="_mar_r_xs"><?php esc_html_e('Rows', 'abprf-rental-forge'); ?></span>
                    <span class="fas fa-times"></span>
                    <span class="_mar_l_xs"><?php esc_html_e('Columns', 'abprf-rental-forge'); ?></span>
                </div>
                <div class="_group_content">
                    <label><input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="<?php echo esc_attr($name_row); ?>" placeholder="Ex: 10" value="<?php echo esc_attr($rows); ?>"/></label>
                    <label><input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="<?php echo esc_attr($name_column); ?>" placeholder="Ex: 10" value="<?php echo esc_attr($columns); ?>"/></label>
                </div>
				<?php
			}
			//=============================//
			public function create_sp($rows, $columns, $ud = false, $sp_infos = []): void {
				if ($rows > 0 && $columns > 0) {
					?>
                    <div class="_ov_auto">
                        <table class="_abprf">
                            <tbody class="abprf_insert_item abprf_sortable">
							<?php for ($i = 1; $i <= $rows; $i++) { ?>
								<?php $row_info = array_key_exists($i - 1, $sp_infos) ? $sp_infos[$i - 1] : []; ?>
								<?php $this->create_sp_row($i, $columns, $ud, $row_info); ?>
							<?php } ?>
                            </tbody>
                        </table>
                        <div class="_divider_xs"></div>
                        <div class="_all_center">
							<?php ABPRF_LIB_Layout::button_add(__('Add New Row', 'abprf-rental-forge')); ?>
                        </div>
                        <div class="abprf_d_none">
                            <table class="_abprf">
                                <tbody class="abprf_hidden_item">
								<?php $this->create_sp_row(0, $columns, $ud); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
					<?php
				}
			}
			public function create_sp_row($row, $columns, $ud, $row_info = []): void {
				$row_name = $ud ? 'abptm_sp_ud_row_' : 'abptm_sp_row_';
				$row_id = $row > 0 ? $row_name . $row : '';
				?>
                <tr class="abprf_delete_area ">
                    <input type="hidden" name="<?php echo esc_attr($ud ? 'abptm_sp_ud_row_id[]' : 'abptm_sp_row_id[]'); ?>" value="<?php echo esc_attr($row_id); ?>"/>
					<?php for ($j = 0; $j < $columns; $j++) {
						$seat_value = array_key_exists($j, $row_info) ? $row_info[$j] : '_@@__&&_1';
						$seat_type = explode('_@@_', $seat_value)[0];
						$text = explode('_@@_', $seat_value)[1];
						$text = explode('_&&_', $text)[0];
						$colspan = max((int)explode('_&&_', $seat_value)[1], 0);
						$class = $colspan < 1 ? '_d_none' : '';
						?>
                        <th colspan="<?php echo esc_attr($colspan); ?>" class="sp_item <?php echo esc_attr($class); ?>" data-colspan="<?php echo esc_attr($columns - $j); ?>" data-type="<?php echo esc_attr($seat_type); ?>">
                            <input type="hidden" name="<?php echo esc_attr($row_id . '[]'); ?>" value="<?php echo esc_attr($seat_value); ?>"/>
                            <span class="seat_text"><?php echo esc_html($text); ?></span>
                        </th>
					<?php } ?>
                    <td class=""><?php ABPRF_LIB_Layout::button_delete_sort(); ?></td>
                </tr>
				<?php
			}
			//=============================//
			public function ticket_type($abprf_infos): void {
				$ticket_infos = array_key_exists('abptm_ticket_info', $abprf_infos) ? $abprf_infos['abptm_ticket_info'] : [];
				?>
                <div class="abptm_ticket_configuration">
                    <div class="abprf_configuration_content">
                        <div class="_ov_auto">
                            <table class="_abprf">
                                <thead>
                                <tr>
                                    <th class="_w_125"><?php esc_html_e('Icon / Image', 'abprf-rental-forge'); ?></th>
                                    <th class="_min_200"><?php esc_html_e('Name', 'abprf-rental-forge'); ?><sup class="_color_required">*</sup></th>
                                    <th class="_min_100"><?php esc_html_e('Quantity', 'abprf-rental-forge'); ?><sup class="_color_required">*</sup></th>
                                    <th class="_min_100"><?php esc_html_e('Max qty/Order', 'abprf-rental-forge'); ?></th>
                                    <th class="_min_250"><?php esc_html_e('Description', 'abprf-rental-forge'); ?></th>
                                    <th class="_w_75"><?php esc_html_e('Action', 'abprf-rental-forge'); ?></th>
                                </tr>
                                </thead>
                                <tbody class="abprf_insert_item abprf_sortable">
								<?php
									if (sizeof($ticket_infos) > 0) {
										foreach ($ticket_infos as $key => $ticket_info) {
											$this->ticket_item($ticket_info, $key);
										}
									}
								?>
                                </tbody>
                            </table>
                        </div>
                        <div class="_divider"></div>
						<?php ABPRF_LIB_Layout::button_add(__('Add New Ticket Type', 'abprf-rental-forge')); ?>
                        <div class="abprf_d_none">
                            <table class="_abprf">
                                <tbody class="abprf_hidden_item">
								<?php $this->ticket_item(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
				<?php
			}
			public function ticket_item($field = array(), $key = ''): void {
				$key = $key ?: uniqid();
				$field = $field ?: array();
				$icon_image = array_key_exists('icon', $field) ? $field['icon'] : '';
				$name = array_key_exists('name', $field) ? $field['name'] : '';
				$qty = array_key_exists('qty', $field) ? $field['qty'] : '';
				$max_ty = array_key_exists('max_qty', $field) ? $field['max_qty'] : '';
				$description = array_key_exists('description', $field) ? $field['description'] : '';
				$icon = $image = "";
				if ($icon_image) {
					if (preg_match('/\s/', $icon_image)) {
						$icon = $icon_image;
					} else {
						$image = $icon_image;
					}
				}
				?>
                <tr class="abprf_delete_area ">
                    <td> <?php do_action('abprf_add_image_icon', 'abptm_ticket_icon[]', $icon, $image); ?>  </td>
                    <td>
                        <input type="hidden" name="ticket_type_hidden_id[]" value="<?php echo esc_attr($key); ?>"/>
                        <label>
                            <input type="text" class="_form_control validation_name" name="abptm_ticket_name[]" data-input-text="<?php echo esc_attr($key); ?>" placeholder="<?php esc_attr_e('EX: Adult', 'abprf-rental-forge'); ?>" value="<?php echo esc_attr($name); ?>"/>
                        </label>
                    </td>
                    <td>
                        <label>
                            <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="ticket_qty[]" placeholder="<?php esc_attr_e('EX: 15', 'abprf-rental-forge'); ?>" value="<?php echo esc_attr($qty); ?>"/>
                        </label>
                    </td>
                    <td>
                        <label>
                            <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="abptm_ticket_max_qty[]" placeholder="<?php esc_attr_e('EX: 15', 'abprf-rental-forge'); ?>" value="<?php echo esc_attr($max_ty); ?>"/>
                        </label>
                    </td>
                    <td>
                        <label>
                            <textarea class="_form_control" name="abptm_ticket_description[]" placeholder="<?php esc_attr_e('EX: Description', 'abprf-rental-forge'); ?>"><?php echo esc_html($description); ?></textarea>
                        </label>
                    </td>
                    <td><?php ABPRF_LIB_Layout::button_delete_sort(); ?></td>
                </tr>
				<?php
			}
			//=============================//
			public function abprf_create_sp(): void {
				if (is_admin() && isset($_POST['nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'abprf_admin_ajax_nonce')) {
					$row = isset($_POST['row']) ? sanitize_text_field(wp_unslash($_POST['row'])) : 0;
					$column = isset($_POST['column']) ? sanitize_text_field(wp_unslash($_POST['column'])) : 0;
					$ud = isset($_POST['ud']) ? sanitize_text_field(wp_unslash($_POST['ud'])) : 0;
					$ud = $ud > 0;
					$this->create_sp($row, $column, $ud);
				}
				wp_die();
			}
			public function get_abprf_ticketing(): void {
				if (is_admin() && isset($_POST['nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['nonce'])), 'abprf_admin_ajax_nonce')) {
					$seat_type = isset($_POST['seat_type']) ? sanitize_text_field(wp_unslash($_POST['seat_type'])) : '';
					$post_id = isset($_POST['post_id']) ? sanitize_text_field(wp_unslash($_POST['post_id'])) : '';
					if ($seat_type == 'seat_plan') {
						$abprf_infos['display_ud'] = ABPRF_LIB_Function::get_post_info($post_id, 'display_ud', 'off');
						$abprf_infos['ticket_type'] = ABPRF_LIB_Function::get_post_info($post_id, 'ticket_type');
						$abprf_infos['display_ticket_type'] = ABPRF_LIB_Function::get_post_info($post_id, 'display_ticket_type', 'off');
						$this->seat_plan($abprf_infos);
					} else {
						$abprf_infos['abptm_ticket_info'] = ABPRF_LIB_Function::get_post_info($post_id, 'abptm_ticket_info', []);
						$this->ticket_type($abprf_infos);
					}
				}
				wp_die();
			}
		}
		new ABPRF_Seats();
	}