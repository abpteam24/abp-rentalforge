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
					?>
					<div class="_section_xs abprf_orders">
						<h4 class="_abprf_color_theme"><span class="_mar_r_xxs">❓</span> <?php esc_html_e( 'FAQ', 'abprf-rental-forge' ); ?></h4>
						<div class="_divider_xs"></div>
					</div>
					<?php
			}
		}
		new ABPRF_FAQ();
	}