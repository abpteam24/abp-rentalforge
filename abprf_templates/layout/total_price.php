<?php
	if (!defined('ABSPATH')) {
		exit; // Exit if accessed directly
	}
	$post_id = $post_id ?? 0;
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$wc_link = ABPRF_Function::get_post_info($post_id, 'link_wc_id', 0);
	if ($post_id > 0 && $wc_link > 0) {
		?>
        <div class="total_continue">
            <h5 class="_abprf">
				<?php esc_html_e('Total Price : ', 'abprf-rental-forge'); ?>&nbsp;
                <span class="abptm_total _color_theme"></span>
            </h5>
			<?php if (is_admin() && str_contains(wp_get_referer(), 'add_order')) { ?>
                <input type="submit" class="_d_none" name="add-admin-order" value="<?php echo esc_attr($wc_link); ?>"/>
			<?php } else { ?>
                <input type="submit" class="_d_none" name="add-to-cart" value="<?php echo esc_attr($wc_link); ?>"/>
			<?php } ?>
            <button class="_btn_light_theme abprf_book_continue" type="button" data-alert="<?php esc_attr_e('No Ticket Selected ! Please Select Ticket', 'abprf-rental-forge'); ?>" data-msg="<?php esc_attr_e('Added to Cart Successfully', 'abprf-rental-forge'); ?>">
				<?php esc_html_e('Continue', 'abprf-rental-forge'); ?>
                <span class="fas fa-angle-double-right _mar_l_xs"></span>
            </button>
        </div>
		<?php
	}
?>
