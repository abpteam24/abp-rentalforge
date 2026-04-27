<?php
		if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	if ( ! class_exists( 'ABPRF_Shortcodes' ) ) {
		class ABPRF_Shortcodes {
			public function __construct() {
				add_shortcode( 'abprf-equipment', array( $this, 'abptm_search' ) );
				add_shortcode( 'abprf-list', array( $this, 'abptm_list' ) );

			}

			public function abptm_search( $attribute ): bool|string {
				$defaults = $this->default_attribute();
				$params   = shortcode_atts( $defaults, $attribute );
				ob_start();
				$form_data = ABPRF_Function::get_form_data();
				//echo '<pre>';print_r($form_data);echo '</pre>';
				?>
                <div id="abprf_area" class="abprf_area">
                    <div class="abprf_container">
						<?php do_action( 'abprf_search_form', [], $params, $form_data ); ?>
                        <div class=" abprf_rental_result">
							<?php ABPRF_Layout::transport_list( $form_data ); ?>
                        </div>
                    </div>
                </div>
				<?php
				return ob_get_clean();
			}

			public function abptm_list( $attribute ): bool|string {
				$defaults = $this->default_attribute();
				$params   = shortcode_atts( $defaults, $attribute );
				$style    = array_key_exists( 'style', $params ) ? $params['style'] : 'grid';
				$file     = ABPRF_Function::template_path( 'list/' . $style . '.php' );
				ob_start();
				?>
                <div class="abprf_area">
                    <div class="abprf_container">
						<?php if ( is_file( $file ) ) {
							require $file;
						} else {
							require ABPRF_Function::template_path( 'list/grid.php' );
						} ?>
                    </div>
                </div>
				<?php
				return ob_get_clean();
			}

			public function default_attribute(): array {
				return array(
					"cat" => '',
					"from" => '',
					"to" => '',
					"org" => '',
					"style" => 'grid',
					"post" => '',
					'sort' => 'ASC',
					"pagination" => "yes",
					"pagination-style" => "live",
					"column" => 3,
					'form' => 'inline',
					'transport' => '',
					'return' => '',
				);
			}
		}
		new ABPRF_Shortcodes();
	}