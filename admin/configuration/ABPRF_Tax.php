<?php
	if (!defined('ABSPATH')) {
		exit; // Exit if accessed directly
	}
	if (!class_exists('ABPRF_Tax')) {
		class ABPRF_Tax {
			public function __construct() {
				add_action('abprf_post_content', [$this, 'tab_content']);
			}
			public function tab_content($abprf_infos): void {
				$abprf_configuration = array_key_exists('abprf_configuration', $abprf_infos) ? $abprf_infos['abprf_configuration'] : [];
				$post_title = array_key_exists('post_title', $abprf_infos) ? $abprf_infos['post_title'] : '';
				$equipment_icon = isset($abprf_configuration['equipment_icon']) && $abprf_configuration['equipment_icon'] ? $abprf_configuration['equipment_icon'] : 'fas fa-hammer';
				?>
                <div class="tab_item" data-tabs="#abprf_tax">
                    <h4 class="_abprf_color_theme"><span class="<?php echo esc_attr($equipment_icon); ?> _mar_r_xs"></span> <?php echo esc_html($post_title . ' ' . __(' : ', 'abprf-rental-forge') . ' ' . __('Tax Configuration', 'abprf-rental-forge')); ?></h4>
                    <div class="_divider_xs"></div>
					<?php if (get_option('woocommerce_calc_taxes') == 'yes') { ?>
						<?php $this->tax_status($abprf_infos); ?>
                        <div class="_divider"></div>
						<?php $this->tax_class($abprf_infos); ?>
					<?php } else { ?>
						<?php ABPRF_LIB_Layout::layout_warning_info('enable_tax_msg'); ?>
					<?php } ?>
                </div>
				<?php
			}
			public function tax_status($abprf_infos): void {
				$tax_status = array_key_exists('_tax_status', $abprf_infos) ? $abprf_infos['_tax_status'] : '';
				?>
                <div class="_setting_item">
                    <label>
                        <span class="_mar_r_xs_w_300"><?php esc_html_e('Tax Status', 'abprf-rental-forge'); ?></span>
                        <select class="_form_control" name="_tax_status">
                            <option disabled selected><?php esc_html_e('Please Select', 'abprf-rental-forge'); ?></option>
                            <option value="taxable" <?php echo esc_attr($tax_status == 'taxable' ? 'selected' : ''); ?>><?php esc_html_e('Taxable', 'abprf-rental-forge'); ?></option>
                            <option value="shipping" <?php echo esc_attr($tax_status == 'shipping' ? 'selected' : ''); ?>><?php esc_html_e('Shipping only', 'abprf-rental-forge'); ?></option>
                            <option value="none" <?php echo esc_attr($tax_status == 'none' ? 'selected' : ''); ?>><?php esc_html_e('None', 'abprf-rental-forge'); ?></option>
                        </select>
                    </label>
                </div>
				<?php
			}
			public function tax_class($abprf_infos): void {
				$tax_classes = WC_Tax::get_tax_rate_classes();
				$tax_class = array_key_exists('_tax_class', $abprf_infos) ? $abprf_infos['_tax_class'] : '';
				?>
                <div class="_setting_item">
                    <label>
                        <span class="_mar_r_xs_w_300"><?php esc_html_e('Tax Class', 'abprf-rental-forge'); ?></span>
                        <select class="_form_control" name="_tax_class">
                            <option disabled selected><?php esc_html_e('Please Select', 'abprf-rental-forge'); ?></option>
                            <option value="standard" <?php echo esc_attr($tax_class == 'standard' ? 'selected' : ''); ?>><?php esc_html_e('Standard', 'abprf-rental-forge'); ?></option>
							<?php if (sizeof($tax_classes) > 0) { ?>
								<?php foreach ($tax_classes as $class) { ?>
                                    <option value="<?php echo esc_attr($class->slug); ?>" <?php echo esc_attr($tax_class == $class->slug ? 'selected' : ''); ?>> <?php echo esc_html($class->name); ?> </option>
								<?php } ?>
							<?php } ?>
                        </select>
                    </label>
                    <div class="_divider_xs"></div>
					<?php ABPRF_LIB_Layout::info_text('_tax_class'); ?>
                </div>
				<?php
			}
		}
		new ABPRF_Tax();
	}