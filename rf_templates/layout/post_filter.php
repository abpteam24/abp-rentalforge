<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	add_action( 'abprf_post_filter_template', function ( $params ) {
		$style      = array_key_exists( 'style', $params ) && $params['style'] ? $params['style'] : 'grid';
		$post_ids   = array_key_exists( 'all_post', $params ) && $params['all_post'] ? $params['all_post'] : [];
		$cat_id     = array_key_exists( 'cat_id', $params ) && ! empty( $params['cat_id'] ) ? $params['cat_id'] : null;
		$loc_id     = array_key_exists( 'loc_id', $params ) && ! empty( $params['loc_id'] ) ? $params['loc_id'] : null;
		//$rent_rule  = array_key_exists( 'rent_rule', $params ) && ! empty( $filters['rent_rule'] ) ? $params['rent_rule'] : null;
		$categories = [];
		if ( empty( $cat_id ) ) {
			if ( ! empty( $post_ids ) && sizeof( $post_ids ) > 0 ) {
				foreach ( $post_ids as $post_id ) {
                    $category=ABPRF_Function::get_post_info( $post_id, 'abprf_category' );
                    if(!empty($category)) {
	                    $categories[] = $category;
                        }
				}
				$categories = array_unique( $categories );
			}
		}
		$locations = [];
		if ( empty( $loc_id ) ) {
			if ( ! empty( $post_ids ) && sizeof( $post_ids ) > 0 ) {
				foreach ( $post_ids as $post_id ) {
					$location_array = ABPRF_Function::get_post_info( $post_id, 'abprf_location' );
					if ( ! empty( $location_array ) ) {
						$location_array = explode( ',', $location_array );
						$locations      = array_merge( $locations, $location_array );
					}
				}
				$locations = array_unique( $locations );
			}
		}
	//	$rent_rules = [];
//		if ( empty( $rent_rule ) ) {
//			if ( ! empty( $post_ids ) && sizeof( $post_ids ) > 0 ) {
//				foreach ( $post_ids as $post_id ) {
//					$rent_rules[] = ABPRF_Function::get_post_info( $post_id, 'rent_rule' );
//				}
//				$rent_rules = array_unique( $rent_rules );
//			}
//		}
		//echo '<pre>';print_r( $categories);					echo '</pre>';
		if ( sizeof( $categories ) > 1 || sizeof( $locations ) > 1 || $style == 'grid' || $style == 'list') {
			?>
            <div class="post_top_filter">
				<?php if ( sizeof( $categories ) > 1 ) {
					if ( sizeof( $categories ) > 4 && is_array( ABPRF_Category ) ) {
						?>
                        <label>
                            <select class="_form_control" name="cat_id">
                                <option value="" selected><?php echo esc_html__( 'All ', 'abprf-rental-forge' ) . ' ' . esc_html(ABPRF_Function::category_label()); ?></option>
								<?php foreach ( $categories as $cat_id ) {
									$category = array_key_exists( $cat_id, ABPRF_Category ) ? ABPRF_Category[ $cat_id ] : [];
									$name     = ! empty( $category ) && array_key_exists( 'name', $category ) ? $category['name'] : '';
									if ( ! empty( $name ) ) {
										?>
                                        <option value="<?php echo esc_attr( $cat_id ); ?>"><?php echo esc_html( array_key_exists( $cat_id, ABPRF_Category ) ? ABPRF_Category[ $cat_id ]['name'] : '' ); ?></option>
									<?php }
								} ?>
                            </select>
                        </label>
						<?php
					} else {
						?>
                        <div class="custom_radio _group_content">
                            <input type="hidden" name="cat_id" value=""/>
                            <div class="radio_item">
                                <button type="button" class="_btn_info_xs_fs_h6 rf_active" data-radio="" data-open-icon="fa-check-circle" data-close-icon="fa-circle">
                                    <span data-icon class="_mar_r_xs far fa-check-circle"></span><?php echo esc_html__( 'All ', 'abprf-rental-forge' ) . ' ' .esc_html( ABPRF_Function::category_label()); ?>
                                </button>
                            </div>
							<?php foreach ( $categories as $cat_id ) {
								$category = is_array( ABPRF_Category ) && array_key_exists( $cat_id, ABPRF_Category ) ? ABPRF_Category[ $cat_id ] : [];
								$name     = ! empty( $category ) && array_key_exists( 'name', $category ) ? $category['name'] : '';
								if ( ! empty( $name ) ) {
									?>
                                    <div class="radio_item">
                                        <button type="button" class="_btn_info_xs_fs_h6 " data-radio="<?php echo esc_attr( $cat_id ); ?>" data-open-icon="far fa-check-circle" data-close-icon="far fa-circle">
                                            <span data-icon class="_mar_r_xxs far fa-circle"></span><?php echo esc_html( $name ); ?>
                                        </button>
                                    </div>
								<?php }
							} ?>
                        </div>
						<?php
					}
				}
					if ( sizeof( $locations ) > 1 ) {
						if ( sizeof( $locations ) > 4 && is_array( ABPRF_Locations ) ) {
							?>
                            <label>
                                <select class="_form_control" name="loc_id">
                                    <option value="" selected><?php esc_html_e( 'All Location', 'abprf-rental-forge' ); ?></option>
									<?php foreach ( $locations as $loc_id ) {
										$location = array_key_exists( $loc_id, ABPRF_Locations ) ? ABPRF_Locations[ $loc_id ] : [];
										$name     = ! empty( $location ) && array_key_exists( 'name', $location ) ? $location['name'] : '';
										if ( ! empty( $name ) ) {
											?>
                                            <option value="<?php echo esc_attr( $loc_id ); ?>"><?php echo esc_html( array_key_exists( $loc_id, ABPRF_Locations ) ? ABPRF_Locations[ $loc_id ]['name'] : '' ); ?></option>
										<?php }
									} ?>
                                </select>
                            </label>
							<?php
						} else {
							?>
                            <div class="custom_radio _group_content">
                                <input type="hidden" name="loc_id" value=""/>
                                <div class="radio_item">
                                    <button type="button" class="_btn_info_xs_fs_h6 rf_active" data-radio="" data-open-icon="fa-check-circle" data-close-icon="fa-circle">
                                        <span data-icon class="_mar_r_xs far fa-check-circle"></span><?php esc_html_e( 'All Location', 'abprf-rental-forge' ); ?>
                                    </button>
                                </div>
								<?php foreach ( $locations as $loc_id ) {
									$location = is_array( ABPRF_Locations ) && array_key_exists( $loc_id, ABPRF_Locations ) ? ABPRF_Locations[ $loc_id ] : [];
									$name     = ! empty( $location ) && array_key_exists( 'name', $location ) ? $location['name'] : '';
									if ( ! empty( $name ) ) {
										?>
                                        <div class="radio_item">
                                            <button type="button" class="_btn_info_xs_fs_h6 " data-radio="<?php echo esc_attr( $loc_id ); ?>" data-open-icon="far fa-check-circle" data-close-icon="far fa-circle">
                                                <span data-icon class="_mar_r_xxs far fa-circle"></span><?php echo esc_html( $name ); ?>
                                            </button>
                                        </div>
									<?php }
								} ?>
                            </div>
							<?php
						}
					}
				?>

				<?php if ( $style == 'grid' || $style == 'list' ) { ?>
                    <div class="_group_content">
                        <button type="button" class="_btn_info_xs_fs_h6 grid_view <?php echo esc_attr( $style == 'grid' ? 'rf_active' : '' ); ?>"><span class="fas fa-table-cells"></span></button>
                        <button type="button" class="_btn_info_xs_fs_h6 list_view <?php echo esc_attr( $style == 'list' ? 'rf_active' : '' ); ?>"><span class="fas fa-list"></span></button>
                    </div>
				<?php } ?>
            </div>
			<?php
		}
	}, 10, 2 );