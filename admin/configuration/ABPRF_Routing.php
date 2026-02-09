<?php
	if (!defined('ABSPATH')) {
		exit; // Exit if accessed directly
	}
	if (!class_exists('ABPRF_Routing')) {
		class ABPRF_Routing {
			public function __construct() {
				add_action('abprf_post_content', [$this, 'routing_configuration']);
			}
			public function routing_configuration($abprf_infos): void {
				$abprf_configuration = array_key_exists('abprf_configuration', $abprf_infos) ? $abprf_infos['abprf_configuration'] : [];
				$post_title = array_key_exists('post_title', $abprf_infos) ? $abprf_infos['post_title'] : '';
				$transport_icon = isset($abprf_configuration['transport_icon']) && $abprf_configuration['transport_icon'] ? $abprf_configuration['transport_icon'] : 'fas fa-bus';
				$display_pickup = array_key_exists('display_pickup', $abprf_infos) ? $abprf_infos['display_pickup'] : 'off';
				$required_pickup = array_key_exists('required_pickup', $abprf_infos) ? $abprf_infos['required_pickup'] : 'off';
				$display_drop = array_key_exists('display_drop', $abprf_infos) ? $abprf_infos['display_drop'] : 'off';
				$required_drop = array_key_exists('required_drop', $abprf_infos) ? $abprf_infos['required_drop'] : 'off';
				$routing_infos = array_key_exists('routing_infos', $abprf_infos) ? $abprf_infos['routing_infos'] : [];
				$abprf_stops = ABPRF_LIB_Function::get_option('abprf_stops');
				$static_info['display_pickup'] = $display_pickup;
				$static_info['required_pickup'] = $required_pickup;
				$static_info['display_drop'] = $display_drop;
				$static_info['required_drop'] = $required_drop;
				?>
                <div class="tabsItem abptm_routing" data-tabs="#abptm_routing">
                    <h4 class="_abprf_color_theme"><span class="<?php echo esc_attr($transport_icon); ?> _mar_r_xs"></span> <?php echo esc_html($post_title . ' ' . __(' : ', 'abprf-rental-forge') . ' ' . __('Route Configuration', 'abprf-rental-forge')); ?></h4>
                    <div class="_divider_xs"></div>
                    <div class="_setting_item">
                        <div class="_fa_center">
							<?php ABPRF_LIB_Layout::switch_checkbox('display_pickup', $display_pickup); ?>
                            <span class="_fs_label_mar_lr_xs"><?php esc_html_e('Pickup Points', 'abprf-rental-forge'); ?></span>
                        </div>
                        <div class="_divider_xs"></div>
						<?php ABPRF_LIB_Layout::info_text('abptm_pickup'); ?>
                    </div>
                    <div data-collapse="#display_pickup" class="<?php echo esc_attr($display_pickup == 'on' ? 'rf_active' : ''); ?>">
                        <div class="_setting_item">
                            <div class="_fa_center">
								<?php ABPRF_LIB_Layout::switch_checkbox('required_pickup', $required_pickup); ?>
                                <span class="_fs_label_mar_lr_xs"><?php esc_html_e('Required Pickup Point ?', 'abprf-rental-forge'); ?></span>
                            </div>
                            <div class="_divider_xs"></div>
							<?php ABPRF_LIB_Layout::info_text('required_pickup'); ?>
                        </div>
                    </div>
                    <div class="_divider_xs"></div>
                    <div class="_setting_item">
                        <div class="_fa_center">
							<?php ABPRF_LIB_Layout::switch_checkbox('display_drop', $display_drop); ?>
                            <span class="_fs_label_mar_lr_xs"><?php esc_html_e('Drop-off Points', 'abprf-rental-forge'); ?></span>
                        </div>
                        <div class="_divider_xs"></div>
						<?php ABPRF_LIB_Layout::info_text('abptm_drop'); ?>
                    </div>
                    <div data-collapse="#display_drop" class="<?php echo esc_attr($display_drop == 'on' ? 'rf_active' : ''); ?>">
                        <div class="_setting_item">
                            <div class="_fa_center">
								<?php ABPRF_LIB_Layout::switch_checkbox('required_drop', $required_drop); ?>
                                <span class="_fs_label_mar_lr_xs"><?php esc_html_e('Required Drop-Off Point ?', 'abprf-rental-forge'); ?></span>
                            </div>
                            <div class="_divider_xs"></div>
							<?php ABPRF_LIB_Layout::info_text('required_drop'); ?>
                        </div>
                    </div>
                    <div class="abprf_configuration_content">
                        <table class="_abprf">
                            <thead>
                            <tr>
                                <th class="_w_50"></th>
                                <th class="_min_200"><span class="fas fa-route _mar_r_xs"></span><?php esc_html_e('Stops Name', 'abprf-rental-forge'); ?><sup class="_color_required">*</sup></th>
                                <th class="_min_100"><?php esc_html_e('Stops Type', 'abprf-rental-forge'); ?><sup class="_color_required">*</sup></th>
                                <th class="_min_100"><?php esc_html_e('Time', 'abprf-rental-forge'); ?><sup class="_color_required">*</sup></th>
                                <th class="_min_200 <?php echo esc_attr($display_pickup == 'on' ? 'rf_active' : ''); ?>" data-collapse="#display_pickup"><?php esc_html_e('Pickup Points', 'abprf-rental-forge'); ?></th>
                                <th class="_min_200 <?php echo esc_attr($display_drop == 'on' ? 'rf_active' : ''); ?>" data-collapse="#display_drop"><?php esc_html_e('Drop-off Points', 'abprf-rental-forge'); ?></th>
                                <th class="_w_75"><?php esc_html_e('Action', 'abprf-rental-forge'); ?></th>
                            </tr>
                            </thead>
                            <tbody class="abprf_insert_item abprf_sortable">
							<?php
								if ($routing_infos && is_array($routing_infos) && sizeof($routing_infos) > 0) {
									foreach ($routing_infos as $routing_info) {
										$static_info['id'] = uniqid();
										$this->stop_item($abprf_stops, $routing_info, $static_info);
									}
								}
							?>
                            </tbody>
                        </table>
                        <div class="_divider_xs"></div>
						<?php ABPRF_LIB_Layout::button_add(__('Add New stops', 'abprf-rental-forge'), 'abprf_add_item abptm_routing_add'); ?>
                        <div class="abprf_d_none abptm_routing_hidden">
                            <table class="_abprf">
                                <tbody class="abprf_hidden_item">
								<?php $static_info['id'] = uniqid();
									$this->stop_item($abprf_stops, [], $static_info); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
				<?php
			}
			public function stop_item($abprf_stops, $field = array(), $static_info = []): void {
				$field = $field ?: array();
				$stop = array_key_exists('stop', $field) ? $field['stop'] : '';
				$type = array_key_exists('type', $field) ? $field['type'] : '';
				$time = array_key_exists('time', $field) ? $field['time'] : '';
				$display_pickup = array_key_exists('display_pickup', $static_info) ? $static_info['display_pickup'] : '';
				$display_drop = array_key_exists('display_drop', $static_info) ? $static_info['display_drop'] : '';
				$id = array_key_exists('id', $static_info) ? $static_info['id'] : uniqid();
				?>
                <tr class="abprf_delete_area ">
                    <th class="_text_table_center "><span class="fas fa-arrow-down"></span></th>
                    <td>
                        <input type="hidden" name="route_hidden_id[]" value="<?php echo esc_attr($id); ?>"/>
                        <div class="dropdown_area">
                            <label>
                                <input type="hidden" name="abptm_stop[]" value="<?php echo esc_attr($stop); ?>"/>
                                <input type="text" class="_form_control_text_center validation_name abprf_allow" name="" placeholder="<?php esc_attr_e('Stops Name', 'abprf-rental-forge'); ?>" value="<?php echo esc_attr($stop); ?>"/>
                            </label>
							<?php ABPRF_LIB_Layout::input_dropdown($abprf_stops, 'fas fa-map-marker'); ?>
                        </div>
                    </td>
                    <th>
                        <label>
                            <select name="abptm_type[]" class='_form_control'>
                                <option selected disabled><?php esc_html_e('Please Select', 'abprf-rental-forge'); ?></option>
                                <option value="bp" <?php echo esc_attr($type == 'bp' ? 'selected' : ''); ?>><?php esc_html_e('Boarding', 'abprf-rental-forge'); ?></option>
                                <option value="dp" <?php echo esc_attr($type == 'dp' ? 'selected' : ''); ?>><?php esc_html_e('Dropping', 'abprf-rental-forge'); ?></option>
                                <option value="both" <?php echo esc_attr($type == 'both' ? 'selected' : ''); ?>><?php esc_html_e('Both', 'abprf-rental-forge'); ?></option>
                            </select>
                        </label>
                    </th>
                    <th>
                        <label>
                            <input type="time" class="_form_control" name="abptm_time[]" value="<?php echo esc_attr($time); ?>"/>
                        </label>
                    </th>
                    <td data-collapse="#display_pickup" class="<?php echo esc_attr($display_pickup == 'on' ? 'rf_active' : ''); ?>">
                        <div class="abprf_configuration_content abptm_pickup <?php echo esc_attr(($type == 'bp' || $type == 'both') ? '' : '_d_none'); ?>">
                            <div class="abprf_insert_item abprf_sortable">
								<?php
									$pickup_infos = array_key_exists('pickup_infos', $field) ? $field['pickup_infos'] : [];
									if (sizeof($pickup_infos) > 0) {
										foreach ($pickup_infos as $point) {
											if (is_array($point) && sizeof($point) > 0) {
												$this->pickup_item($id, $abprf_stops, $point);
											}
										}
									}
								?>
                            </div>
							<?php ABPRF_LIB_Layout::button_add(__('Add New Pickup Point', 'abprf-rental-forge'), '', '_btn_tp_xs'); ?>
                            <div class="abprf_d_none">
                                <div class="abprf_hidden_item">
									<?php $this->pickup_item($id, $abprf_stops); ?>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td data-collapse="#display_drop" class="<?php echo esc_attr($display_drop == 'on' ? 'rf_active' : ''); ?>">
                        <div class="abprf_configuration_content abptm_drop <?php echo esc_attr(($type == 'dp' || $type == 'both') ? '' : '_d_none'); ?>">
                            <div class="abprf_insert_item abprf_sortable">
								<?php
									$drop_infos = array_key_exists('drop_infos', $field) ? $field['drop_infos'] : [];
									if (sizeof($drop_infos) > 0) {
										foreach ($drop_infos as $point) {
											if (is_array($point) && sizeof($point) > 0) {
												$this->drop_item($id, $abprf_stops, $point);
											}
										}
									}
								?>
                            </div>
							<?php ABPRF_LIB_Layout::button_add(__('Add New Drop-off Point', 'abprf-rental-forge'), '', '_btn_tp_xs'); ?>
                            <div class="abprf_d_none">
                                <div class="abprf_hidden_item">
									<?php $this->drop_item($id, $abprf_stops); ?>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td><?php ABPRF_LIB_Layout::button_delete_sort(); ?></td>
                </tr>
				<?php
			}
			public function pickup_item($id, $abprf_stops, $point = []): void {
				$name = array_key_exists('name', $point) ? $point['name'] : '';
				$time = array_key_exists('time', $point) ? $point['time'] : '';
				?>
                <div class="abprf_delete_area ">
                    <div class="_fj_between">
                        <div class="dropdown_area">
                            <label>
                                <input type="hidden" name="pickup_name_<?php echo esc_attr($id); ?>[]" value="<?php echo esc_attr($name); ?>"/>
                                <input type="text" class="_form_control_text_center validation_name abprf_allow" name="" placeholder="<?php esc_attr_e('Pickup Point', 'abprf-rental-forge'); ?>" value="<?php echo esc_attr($name); ?>"/>
                            </label>
							<?php ABPRF_LIB_Layout::input_dropdown($abprf_stops, 'fas fa-map-marker'); ?>
                        </div>
                        <label>
                            <input type="time" class="_form_control" name="pickup_time_<?php echo esc_attr($id); ?>[]" value="<?php echo esc_attr($time); ?>"/>
                        </label>
						<?php ABPRF_LIB_Layout::button_delete_sort(); ?>
                    </div>
                    <div class="_divider_xs"></div>
                </div>
				<?php
			}
			public function drop_item($id, $abprf_stops, $point = []): void {
				$name = array_key_exists('name', $point) ? $point['name'] : '';
				$time = array_key_exists('time', $point) ? $point['time'] : '';
				?>
                <div class="abprf_delete_area ">
                    <div class="_fj_between">
                        <div class="dropdown_area">
                            <label>
                                <input type="hidden" name="drop_name_<?php echo esc_attr($id); ?>[]" value="<?php esc_attr($name); ?>"/>
                                <input type="text" class="_form_control_text_center validation_name abprf_allow" name="" placeholder="<?php esc_attr_e('Drop-off Points', 'abprf-rental-forge'); ?>" value="<?php echo esc_attr($name); ?>"/>
                            </label>
							<?php ABPRF_LIB_Layout::input_dropdown($abprf_stops, 'fas fa-map-marker'); ?>
                        </div>
                        <label>
                            <input type="time" class="_form_control" name="drop_time_<?php echo esc_attr($id) ?>[]" value="<?php echo esc_attr($time); ?>"/>
                        </label>
						<?php ABPRF_LIB_Layout::button_delete_sort(); ?>
                    </div>
                    <div class="_divider_xs"></div>
                </div>
				<?php
			}
		}
		new ABPRF_Routing();
	}