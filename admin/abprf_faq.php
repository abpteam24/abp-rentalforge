<?php
	if ( ! defined( 'ABSPATH' ) ) {
		die;
	} // Cannot access pages directly.
	if ( ! class_exists( 'ABPRF_FAQ' ) ) {
		class ABPRF_FAQ {
			public function __construct() {
				add_action( 'abprf_load_faq', array( $this, 'load_faq' ) );
			}

			public function load_faq() {
				$faqs = ABPRF_Function::get_option( 'abprf_faqs');
					?>
                <form class="_section_xs abprf_save_additional_service" method="post" action="">
                    <h4 class="_abprf_color_theme"><span class="_mar_r_xxs">❓</span> <?php esc_html_e( 'FAQ', 'abprf-rental-forge' ); ?></h4>
					<?php ABPRF_Layout::info_text( 'abprf_faqs' ); ?>
                    <div class="_divider_xs"></div>
					<?php $this->faq( $faqs ); ?>
                    <div class="_divider_xs"></div>
                    <button type="submit" class="_btn_theme"><span class="_mar_r_xxs">💾</span><?php esc_html_e( 'Save FAQs Configuration', 'abprf-rental-forge' ); ?></button>
                </form>
					<?php
			}
            public function faq($faqs) {

            }
		}
		new ABPRF_FAQ();
	}