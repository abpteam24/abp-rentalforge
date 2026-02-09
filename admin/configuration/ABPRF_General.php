<?php
	if (!defined('ABSPATH')) {
		exit; // Exit if accessed directly
	}
	if (!class_exists('ABPRF_General')) {
		class ABPRF_General {
			public function __construct() {
				add_action('abprf_post_content', [$this, 'general_configuration']);
			}
			public function general_configuration($abprf_infos): void {
				$abprf_configuration = array_key_exists('abprf_configuration', $abprf_infos) ? $abprf_infos['abprf_configuration'] : [];
				$post_title = array_key_exists('post_title', $abprf_infos) ? $abprf_infos['post_title'] : '';
				$transport_icon = isset($abprf_configuration['transport_icon']) && $abprf_configuration['transport_icon'] ? $abprf_configuration['transport_icon'] : 'fas fa-bus';
				$sale_continue = array_key_exists('sale_continue', $abprf_infos) ? $abprf_infos['sale_continue'] : 'on';
				$display_transport_id = array_key_exists('display_transport_id', $abprf_infos) ? $abprf_infos['display_transport_id'] : 'on';
				$transport_id = array_key_exists('transport_id', $abprf_infos) ? $abprf_infos['transport_id'] : '';
				$abptm_template = array_key_exists('abptm_template', $abprf_infos) ? $abprf_infos['abptm_template'] : 'default';
				?>
                <div class="tabsItem" data-tabs="#abptm_general">
                    <h4 class="_abprf_color_theme"><span class="<?php echo esc_attr($transport_icon); ?> _mar_r_xs"></span> <?php echo esc_html($post_title . ' ' . __(' : ', 'abprf-rental-forge') . ' ' . __('General Configuration', 'abprf-rental-forge')); ?></h4>
                    <div class="_divider_xs"></div>
					<?php $this->sale_close($sale_continue); ?>
					<?php $this->transportation_id($display_transport_id, $transport_id); ?>
					<?php $this->category($abprf_infos); ?>
					<?php $this->organizer($abprf_infos); ?>
					<?php $this->template($abptm_template); ?>
                </div>
				<?php
			}
			public function sale_close($sale_continue): void {
				?>
                <div class="_setting_item">
                    <div class="_fa_center">
						<?php ABPRF_LIB_Layout::switch_checkbox('sale_continue', $sale_continue); ?>
                        <span class="_fs_label_mar_lr_xs"><?php esc_html_e('Sale continue?', 'abprf-rental-forge'); ?></span>
                    </div>
                    <div class="_divider_xs"></div>
					<?php ABPRF_LIB_Layout::info_text('sale_continue'); ?>
                </div>
				<?php
			}
			public function transportation_id($display_transport_id, $transport_id): void {
				?>
                <div class="_setting_item">
                    <div class="_f_equal_max_500_f_wrap">
                        <div class="_fa_center">
							<?php ABPRF_LIB_Layout::switch_checkbox('display_transport_id', $display_transport_id); ?>
                            <span class="_fs_label_mar_lr_xs"><?php esc_html_e('Transport ID', 'abprf-rental-forge'); ?></span>
                        </div>
                        <div data-collapse="#display_transport_id" class="<?php echo esc_attr($display_transport_id == 'on' ? 'rf_active' : ''); ?>">
                            <label>
                                <input class="_form_control validation_id" name="transport_id" value="<?php echo esc_attr($transport_id); ?>" placeholder="<?php esc_attr_e('Ex : XYZ_123', 'abprf-rental-forge'); ?>"/>
                            </label>
                        </div>
                    </div>
                    <div class="_divider_xs"></div>
					<?php ABPRF_LIB_Layout::info_text('display_transport_id'); ?>
                </div>
				<?php
			}
			public function category($abprf_infos): void {
				$abprf_configuration = array_key_exists('abprf_configuration', $abprf_infos) ? $abprf_infos['abprf_configuration'] : [];
				$category_label = isset($abprf_configuration['category_label']) && $abprf_configuration['category_label'] ? $abprf_configuration['category_label'] : __('Category', 'abprf-rental-forge');
				$transport_category = array_key_exists('category', $abprf_infos) ? $abprf_infos['category'] : '';
				$display_category = array_key_exists('display_category', $abprf_infos) ? $abprf_infos['display_category'] : 'on';
				$all_categories = ABPRF_LIB_Function::get_all_term_data('abprf_category');
				?>
                <div class="_setting_item">
                    <div class="_f_equal_max_500_f_wrap">
                        <div class="_fa_center">
							<?php ABPRF_LIB_Layout::switch_checkbox('display_category', $display_category); ?>
                            <span class="_fs_label_mar_lr_xs"><?php echo esc_html($category_label); ?></span>
                        </div>
                        <div data-collapse="#display_category" class="<?php echo esc_attr($display_category == 'on' ? 'rf_active' : ''); ?>">
							<?php if (sizeof($all_categories) > 0) { ?>
                                <label>
                                    <select class="_form_control" name="category">
                                        <option disabled selected><?php esc_html_e('Please Select', 'abprf-rental-forge'); ?></option>
										<?php foreach ($all_categories as $category) { ?>
                                            <option value="<?php echo esc_attr($category); ?>" <?php echo esc_attr($transport_category == $category ? 'selected' : ''); ?>><?php echo esc_html($category); ?></option>
										<?php } ?>
                                    </select>
                                </label>
							<?php } ?>
                        </div>
                    </div>
                    <div class="_divider_xs"></div>
					<?php ABPRF_LIB_Layout::info_text('display_category'); ?>
                </div>
				<?php
			}
			public function organizer($abprf_infos): void {
				$abprf_configuration = array_key_exists('abprf_configuration', $abprf_infos) ? $abprf_infos['abprf_configuration'] : [];
				$organizer_label = isset($abprf_configuration['organizer_label']) && $abprf_configuration['organizer_label'] ? $abprf_configuration['organizer_label'] : __('Organizer', 'abprf-rental-forge');
				$transport_organizer = array_key_exists('organizer', $abprf_infos) ? $abprf_infos['organizer'] : '';
				$display_organizer = array_key_exists('display_organizer', $abprf_infos) ? $abprf_infos['display_organizer'] : 'off';
				$all_organizer = ABPRF_LIB_Function::get_all_term_data('abprf_organizer');
				?>
                <div class="_setting_item">
                    <div class="_f_equal_max_500_f_wrap">
                        <div class="_fa_center">
							<?php ABPRF_LIB_Layout::switch_checkbox('display_organizer', $display_organizer); ?>
                            <span class="_fs_label_mar_lr_xs"><?php echo esc_html($organizer_label); ?></span>
                        </div>
                        <div data-collapse="#display_organizer" class="<?php echo esc_attr($display_organizer == 'on' ? 'rf_active' : ''); ?>">
							<?php if (sizeof($all_organizer) > 0) { ?>
                                <label>
                                    <select class="_form_control" name="organizer">
                                        <option disabled selected><?php esc_html_e('Please Select', 'abprf-rental-forge'); ?></option>
										<?php foreach ($all_organizer as $organizer) { ?>
                                            <option value="<?php echo esc_attr($organizer); ?>" <?php echo esc_attr($transport_organizer == $organizer ? 'selected' : ''); ?>><?php echo esc_html($organizer); ?></option>
										<?php } ?>
                                    </select>
                                </label>
							<?php } ?>
                        </div>
                    </div>
                    <div class="_divider_xs"></div>
					<?php ABPRF_LIB_Layout::info_text('display_organizer'); ?>
                </div>
				<?php
			}
			public function template($abptm_template): void {
				?>
                <div class="_setting_item">
                    <label class="_f_equal_max_500_f_wrap">
                        <span class="_mar_r_xs"><?php esc_html_e('Template', 'abprf-rental-forge'); ?></span>
                        <select class="_form_control " name="abptm_template" data-collapse-target required>
                            <option disabled selected><?php esc_html_e('Please Select', 'abprf-rental-forge'); ?></option>
                            <option value="default" <?php echo esc_attr($abptm_template == 'default' ? 'selected' : ''); ?>><?php esc_html_e('Default Template', 'abprf-rental-forge'); ?></option>
                            <option value="light" <?php echo esc_attr($abptm_template == 'light' ? 'selected' : ''); ?>><?php esc_html_e('Light Template', 'abprf-rental-forge'); ?></option>
                        </select>
                    </label>
                    <div class="_divider_xs"></div>
					<?php ABPRF_LIB_Layout::info_text('abptm_template'); ?>
                </div>
				<?php
			}
		}
		new ABPRF_General();
	}