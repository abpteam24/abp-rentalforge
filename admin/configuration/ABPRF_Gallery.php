<?php
	if (!defined('ABSPATH')) {
		exit; // Exit if accessed directly
	}
	if (!class_exists('ABPRF_Gallery')) {
		class ABPRF_Gallery {
			public function __construct() {
				add_action('abprf_post_content', [$this, 'gallery_settings']);
			}
			public function gallery_settings($abprf_infos): void {
				$abprf_configuration = array_key_exists('abprf_configuration', $abprf_infos) ? $abprf_infos['abprf_configuration'] : [];
				$post_title = array_key_exists('post_title', $abprf_infos) ? $abprf_infos['post_title'] : '';
				$equipment_icon = isset($abprf_configuration['equipment_icon']) && $abprf_configuration['equipment_icon'] ? $abprf_configuration['equipment_icon'] : 'fas fa-hammer';
				$display_slider = array_key_exists('display_slider', $abprf_infos) ? $abprf_infos['display_slider'] : 'on';
				$abprf_sliders = array_key_exists('abprf_sliders', $abprf_infos) ? $abprf_infos['abprf_sliders'] : [];
				?>
                <div class="tab_item" data-tabs="#abprf_slider">
                    <h4 class="_abprf_color_theme"><span class="<?php echo esc_attr($equipment_icon); ?> _mar_r_xs"></span> <?php echo esc_html($post_title . ' ' . __(' : ', 'abprf-rental-forge') . ' ' . __('Slider Configuration', 'abprf-rental-forge')); ?></h4>
                    <div class="_divider_xs"></div>
                    <div class="_setting_item">
                        <div class="_fa_center">
							<?php ABPRF_Layout::switch_checkbox('display_slider', $display_slider); ?>
                            <span class="_fs_label_mar_lr_xs"><?php esc_html_e('Display Slider', 'abprf-rental-forge'); ?></span>
                        </div>
                        <div class="_divider_xs"></div>
						<?php ABPRF_Layout::info_text('display_slider'); ?>
                    </div>
                    <div data-collapse="#display_slider" class="<?php echo esc_attr($display_slider == 'on' ? 'rf_active' : ''); ?>">
						<?php do_action('abprf_add_image_multiple', 'abprf_sliders', $abprf_sliders); ?>
                    </div>
                </div>
				<?php
			}
		}
		new ABPRF_Gallery();
	}