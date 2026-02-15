<?php
	if (!defined('ABSPATH')) {
		exit; // Exit if accessed directly
	}
	$form_data = $form_data ?? [];
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$post_id = array_key_exists('post_id', $form_data) ? $form_data['post_id'] : 0;
	$abprf_infos = $abprf_infos ?? [];
	$display_additional_services = array_key_exists('display_additional_services', $abprf_infos) ? $abprf_infos['display_additional_services'] : 'on';
	$additional_services = array_key_exists('additional_services', $abprf_infos) ? $abprf_infos['additional_services'] : [];
	if ($display_additional_services == 'on' && sizeof($additional_services) > 0) {
		$origin_time = array_key_exists('origin_time', $form_data) ? $form_data['origin_time'] : '';
		$additional_sold =array_key_exists('booking_info',$abprf_infos) && array_key_exists('additional',$abprf_infos['booking_info'])?$abprf_infos['booking_info']['additional']:[];
		$ex_count = 0;
		?>
        <div class="transport_additional_service" data-additional>
            <div class="_p_relative additional_service">
                <h5 class="_abprf_title"> <?php esc_html_e('Additional services ( Optional ) : ', 'abprf-rental-forge'); ?>&nbsp;<span class="_color_theme additional_seat_name"></span></h5>
				<?php foreach ($additional_services as $id => $additional_service) {
					$icon_image = array_key_exists('icon', $additional_service) ? $additional_service['icon'] : '';
					$ex_name = array_key_exists('name', $additional_service) ? $additional_service['name'] : '';
					$ex_price = array_key_exists('price', $additional_service) ? $additional_service['price'] : '';
					$row_ex_price = ABPRF_LIB_Function::get_wc_raw_price($post_id, $ex_price);
					$ex_qty = array_key_exists('qty', $additional_service) ? $additional_service['qty'] : '';
					$ex_max_qty = array_key_exists('max_qty', $additional_service) ? $additional_service['max_qty'] : '';
					$ex_sold = array_key_exists($id, $additional_sold) ? $additional_sold[$id] : 0;
					$ex_available = $ex_qty - $ex_sold;
					$ex_description = array_key_exists('description', $additional_service) ? $additional_service['description'] : '';
					$icon = $image = "";
					if ($icon_image) {
						if (preg_match('/\s/', $icon_image)) {
							$icon = $icon_image;
						} else {
							$image = $icon_image;
						}
					}
					if ($ex_count > 0) { ?>
                        <div class="_divider_xs"></div>
					<?php }
					$ex_count++; ?>
                    <div class="service_item _d_flex">
						<?php if ($image) { ?>
                            <div class="_w_100"><?php ABPRF_Layout::bg_image('', $image); ?></div>
						<?php } ?>
                        <div class="_fd_column_full_width">
                            <div class="_fj_between">
                                <h6 class="_abprf_fa_center">
									<?php if ($icon) { ?>
                                        <span class="<?php echo esc_attr($icon); ?> _mar_r_xs"></span>
									<?php }
										echo esc_html($ex_name); ?>
                                </h6>
								<?php if ($ex_available > 0) { ?>
                                    <input type="hidden" name="<?php echo esc_attr('name_'.$id.'[]'); ?>" value="<?php echo esc_attr($ex_name); ?>"/>
									<?php
									$input_info = ['name' => 'qty_' . $id . '[]', 'price' => $row_ex_price, 'available' => $ex_available, 'd_qty' => 0, 'min_qty' => 0, 'max_qty' => $ex_max_qty, 'class' => 'ex_price_calculate',];
									ABPRF_Layout::quantity_input($input_info);
								} else { ?>
                                    <span class="_color_warning"> <?php esc_html_e('Not Available !', 'abprf-rental-forge'); ?></span>
								<?php } ?>
                            </div>
                            <h5 class="_abprf_color_theme">
								<?php
									if ($ex_price > 0) {
										echo wp_kses_post(wc_price($ex_price));
									} else {
										esc_html_e('Free', 'abprf-rental-forge');
									}
								?>
                            </h5>
                            <p class="_abprf"><?php echo esc_html($ex_description); ?></p>
                        </div>
                    </div>
				<?php } ?>
            </div>
        </div>
		<?php
	}
