<?php
	if (!defined('ABSPATH')) {
		exit; // Exit if accessed directly
	}
	$form_data = $form_data ?? [];
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$abprf_infos = $abprf_infos ?? [];
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$post_id = array_key_exists('post_id', $form_data) ? $form_data['post_id'] : 0;
	$bp = array_key_exists('bp', $form_data) ? $form_data['bp'] : '';
	$dp = array_key_exists('dp', $form_data) ? $form_data['dp'] : '';
	if ($post_id > 0 && $bp && $dp) {
		$pickups = array_key_exists('pickup_infos', $form_data) ? $form_data['pickup_infos'] : '';
		$drops = array_key_exists('drop_infos', $form_data) ? $form_data['drop_infos'] : '';
		$required_pickup = array_key_exists('required_pickup', $abprf_infos) ? $abprf_infos['required_pickup'] : 'off';
		$required_drop = array_key_exists('required_drop', $abprf_infos) ? $abprf_infos['required_drop'] : 'off';
		$required_pickup = $required_pickup == 'on' ? 'required' : '';
		$required_drop = $required_drop == 'on' ? 'required' : '';
		if (sizeof($pickups) > 0 || sizeof($drops) > 0) {
			?>
            <div class="abptm_pickup_drop">
				<?php if (sizeof($pickups) > 0) { ?>
                    <label class="_f_equal">
                        <span>
                            <?php esc_html_e('Pickup Point', 'abprf-rental-forge');
	                            if ($required_pickup) { ?>
                                    <sup class="_color_required">*</sup>
	                            <?php } ?>
                        </span>
                        <select name="pickup_point" class="_form_control" title="<?php esc_attr_e('Please Select Pickup Point', 'abprf-rental-forge'); ?>" <?php echo esc_attr($required_pickup); ?>>
                            <option value="" disabled selected><?php esc_html_e('Please Select Pickup Point', 'abprf-rental-forge'); ?></option>
							<?php foreach ($pickups as $pickup) {
								$name = array_key_exists('name', $pickup) ? $pickup['name'] : '';
								$time = array_key_exists('time', $pickup) ? $pickup['time'] : '';
								$value = $name . ' ' . ($time ? ' (' . ABPRF_Function::date_format($time, 'time') . ')' : '');
								?>
                                <option value="<?php echo esc_attr($value); ?>" <?php echo esc_attr(strtolower($name) == strtolower($bp) ? 'selected' : ''); ?>><?php echo esc_html($value); ?></option>
							<?php } ?>
                        </select>
                    </label>
				<?php } ?>
				<?php if (sizeof($drops) > 0) { ?>
                    <label class="_f_equal_mar_t_xs">
                        <span>
                            <?php esc_html_e('Drop-off Point', 'abprf-rental-forge');
	                            if ($required_drop) { ?>
                                    <sup class="_color_required">*</sup>
	                            <?php } ?>
                        </span>
                        <select name="drop_point" class="_form_control" title="<?php esc_attr_e('Please Select Drop-off Point', 'abprf-rental-forge'); ?>" <?php echo esc_attr($required_drop); ?>>
                            <option value="" disabled selected><?php esc_html_e('Please Select Drop-off Point', 'abprf-rental-forge'); ?></option>
							<?php foreach ($drops as $drop) {
								$name = array_key_exists('name', $drop) ? $drop['name'] : '';
								$time = array_key_exists('time', $drop) ? $drop['time'] : '';
								$value = $name . ' ' . ($time ? ' (' . ABPRF_Function::date_format($time, 'time') . ')' : '');
								?>
                                <option value="<?php echo esc_attr($value); ?>" <?php echo esc_attr(strtolower($name) == strtolower($dp) ? 'selected' : ''); ?>><?php echo esc_html($value); ?></option>
							<?php } ?>
                        </select>
                    </label>
				<?php } ?>
            </div>
			<?php
		}
	}
