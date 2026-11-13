<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
add_action('abprf_faq_template', function ($abprf_infos = [], $type = '') {
	if (ABPRF_Function::on_off('faq')) {
		$faq_infos = [];
		if (!empty($abprf_infos)) {
			$display = $abprf_infos['display_faq'] ?? 'on';
			$active_global_faq = $abprf_infos['active_global_faq'] ?? 'on';
			if ($display === 'on') {
				$faq_infos = ($active_global_faq === 'on')
					? ABPRF_Function::get_option('abprf_faqs')
					: ($abprf_infos['abprf_faqs'] ?? []);
			}
		} elseif ($type === 'global') {
			$faq_infos = ABPRF_Function::get_option('abprf_faqs');
		}
		if (empty($faq_infos) || !is_array($faq_infos)) {
			return;
		}
		?>
        <div class="faq_area _section">
            <h4 class="abp_gap_xs">❓<?php esc_html_e('Frequently Asked Questions', 'abp-rentalforge'); ?>                    </h4>
            <div class="_divider_xs"></div>
            <div class=" faq_list">
				<?php
				foreach ($faq_infos as $faq) {
					$title = $faq['title'] ?? '';
					$description = $faq['des'] ?? '';
					if (!empty($title) && !empty($description)) {
						?>
                        <div class="faq_item">
                            <div class="faq_question faq_target">
                                <h6 class="abp"><?php echo esc_html($title); ?></h6>
                                <span class="faq_icon"></span>
                            </div>
                            <div class="faq_answer">
                                <div class="faq_answer_content">
									<?php echo wp_kses_post(ABPRF_Function::format_content($description)); ?>
                                </div>
                            </div>
                        </div>
					<?php }
				} ?>
            </div>
        </div>
		<?php
	}
}, 10, 2);