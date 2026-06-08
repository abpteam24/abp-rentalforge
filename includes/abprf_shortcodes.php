<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	if ( ! class_exists( 'ABPRF_Shortcodes' ) ) {
		class ABPRF_Shortcodes {
			public function __construct() {
				add_shortcode( 'abprf-post', array( $this, 'post_list' ) );
				add_shortcode( 'abprf-property', array( $this, 'property_list' ) );
				add_shortcode( 'abprf-gallery', array( $this, 'gallery' ) );
			}

			public function post_list( $attribute ): bool|string {
				$defaults = $this->default_attribute();
				$params   = shortcode_atts( $defaults, $attribute );
				$post_id  = array_key_exists( 'post_id', $params ) && $params['post_id'] ? $params['post_id'] : '';
				//echo '<pre>';print_r($params);echo '</pre>';
				ob_start();
				if ( ! empty( $post_id ) ) {
					do_action( 'abprf_load_details_template', $post_id );
				} else {
					$params['all_post'] = ABPRF_Query::get_post_id( $params );
					$style              = array_key_exists( 'style', $params ) && $params['style'] ? $params['style'] : 'grid';
					$file               = ABPRF_Function::template_path( 'list/' . $style . '.php' );
					?>
                    <div class="abprf_area">
                        <div class="abprf_container rf_pagination">
							<?php
								do_action( 'abprf_post_filter', $params );
								if ( is_file( $file ) ) {
									include_once $file;
									do_action( 'abprf_' . $style . '_template', $params );
								} else {
									include_once ABPRF_Function::template_path( 'list/default.php' );
									do_action( 'abprf_default_template', $params );
								} ?>
                            <div class="rf_no_results _d_none">
								<?php ABPRF_Layout::layout_warning_info( 'not_match' ); ?>
                            </div>
                        </div>
                    </div>
					<?php
				}

				return ob_get_clean();
			}

			public function property_list( $attribute ): bool|string {
				$defaults = $this->default_attribute();
				$params   = shortcode_atts( $defaults, $attribute );
				$post_id  = array_key_exists( 'post_id', $params ) && $params['post_id'] ? $params['post_id'] : '';
				ob_start();
				if ( ! empty( $post_id ) ) {
					do_action( 'abprf_load_details_template', $post_id );
				} else {
					$params['all_property'] = ABPRF_Query::get_property( $params );
					$params['all_post']     = ABPRF_Query::get_post_id( $params );
					$style                  = array_key_exists( 'style', $params ) && $params['style'] ? $params['style'] : 'grid';
					$file                   = ABPRF_Function::template_path( 'list/property_' . $style . '.php' );
					?>
                    <div class="abprf_area">
                        <div class="abprf_container rf_pagination ">
							<?php
								do_action( 'abprf_post_filter', $params );
								if ( is_file( $file ) ) {
									include_once $file;
									do_action( 'abprf_property_' . $style . '_template', $params );
								} else {
									include_once ABPRF_Function::template_path( 'list/property_default.php' );
									do_action( 'abprf_property_default_template', $params );
								} ?>
                            <div class="rf_no_results _d_none">
								<?php ABPRF_Layout::layout_warning_info( 'not_match' ); ?>
                            </div>
                        </div>
                    </div>
					<?php
				}

				return ob_get_clean();
			}

			public function gallery( $attribute ): bool|string {
				$defaults = $this->default_attribute();
				$params   = shortcode_atts( $defaults, $attribute );
				$post_id  = array_key_exists( 'post_id', $params ) && $params['post_id'] ? $params['post_id'] : '';
				ob_start();
				?>
                <div class="abprf_area">
                    <div class="abprf_container">
						<?php
							if ( ! empty( $post_id ) ) {
								$img_infos = ABPRF_Function::get_post_info( $post_id, 'abprf_sliders', [] );
								do_action( 'abprf_slider', $img_infos, $params );
							} else {
								$post_ids  = ABPRF_Query::get_post_id( $params );
								$img_infos = [];
								if ( ! empty( $post_ids ) && sizeof( $post_ids ) > 0 ) {
									foreach ( $post_ids as $post_id ) {
										$info      = ABPRF_Function::get_post_info( $post_id, 'abprf_sliders', [] );
										$img_infos = array_merge( $img_infos, $info );
									}
									do_action( 'abprf_slider', $img_infos, $params );
								}
							}
						?>
                    </div>
                </div>
				<?php
				return ob_get_clean();
			}

			public function default_attribute(): array {
				return array(
					"post_id" => '',
					"cat_id" => '',
					"loc_id" => '',
					"brand_id" => '',
					"rent_rule" => '',
					"style" => 'grid',
					"show" => '',
					"column" => 3,
					'sort' => 'ASC',
					"pagination" => "yes",
					"pagination-style" => "live",
					'form' => 'inline',
				);
			}
		}
		new ABPRF_Shortcodes();
	}