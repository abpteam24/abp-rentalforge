<?php
	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly
	}
	if ( ! class_exists( 'ABPRF_General' ) ) {
		class ABPRF_General {
			public function __construct() {
				add_action( 'abprf_post_content', [ $this, 'general_configuration' ] );
			}

			public function general_configuration( $abprf_infos ): void {
				$abprf_configuration = array_key_exists( 'abprf_configuration', $abprf_infos ) ? $abprf_infos['abprf_configuration'] : [];
				$post_title          = array_key_exists( 'post_title', $abprf_infos ) ? $abprf_infos['post_title'] : '';
				$equipment_icon      = isset( $abprf_configuration['equipment_icon'] ) && $abprf_configuration['equipment_icon'] ? $abprf_configuration['equipment_icon'] : 'fas fa-hammer';
				?>
                <div class="tab_item" data-tabs="#abprf_general">
                    <h4 class="_abprf_color_theme"><span class="<?php echo esc_attr( $equipment_icon ); ?> _mar_r_xs"></span> <?php echo esc_html( $post_title . ' ' . __( ' : ', 'abprf-rental-forge' ) . ' ' . __( 'General Configuration', 'abprf-rental-forge' ) ); ?></h4>
                    <div class="_divider_xs"></div>
					<?php $this->sub_title( $abprf_infos ); ?>
					<?php $this->sale_close( $abprf_infos ); ?>
					<?php $this->unique_id( $abprf_infos ); ?>
					<?php $this->category( $abprf_infos ); ?>
					<?php $this->template( $abprf_infos ); ?>
					<?php $this->select_advance_date_number( $abprf_infos ); ?>
                </div>
				<?php
			}
			public function sub_title( $abprf_infos ): void {
				$display= array_key_exists( 'display_sub_title', $abprf_infos ) ? $abprf_infos['display_sub_title'] : 'on';
				$sub_title         = array_key_exists( 'sub_title', $abprf_infos ) ? $abprf_infos['sub_title'] : '';
				?>
                <div class="_setting_item">
                    <div class="_f_equal_max_500_f_wrap">
                        <div class="_fa_center">
							<?php ABPRF_Layout::switch_checkbox( 'display_sub_title', $display ); ?>
                            <span class="_fs_label_mar_lr_xs"><?php esc_html_e( 'Sub Title', 'abprf-rental-forge' ); ?></span>
                        </div>
                        <div data-collapse="#display_sub_title" class="<?php echo esc_attr( $display == 'on' ? 'rf_active' : '' ); ?>">
                            <label>
                                <input class="_form_control" name="sub_title" value="<?php echo esc_attr( $sub_title ); ?>" placeholder="<?php esc_attr_e( 'Equipment Sub Title', 'abprf-rental-forge' ); ?>"/>
                            </label>
                        </div>
                    </div>
                    <div class="_divider_xs"></div>
					<?php ABPRF_Layout::info_text( 'display_sub_title' ); ?>
                </div>
				<?php
			}
			public function sale_close( $abprf_infos ): void {
				$sale_continue = array_key_exists( 'sale_continue', $abprf_infos ) ? $abprf_infos['sale_continue'] : 'on';
				?>
                <div class="_setting_item">
                    <div class="_fa_center">
						<?php ABPRF_Layout::switch_checkbox( 'sale_continue', $sale_continue ); ?>
                        <span class="_fs_label_mar_lr_xs"><?php esc_html_e( 'Sale continue?', 'abprf-rental-forge' ); ?></span>
                    </div>
                    <div class="_divider_xs"></div>
					<?php ABPRF_Layout::info_text( 'sale_continue' ); ?>
                </div>
				<?php
			}

			public function unique_id( $abprf_infos ): void {
				$display_equipment_id = array_key_exists( 'display_equipment_id', $abprf_infos ) ? $abprf_infos['display_equipment_id'] : 'on';
				$equipment_id         = array_key_exists( 'equipment_id', $abprf_infos ) ? $abprf_infos['equipment_id'] : '';
				?>
                <div class="_setting_item">
                    <div class="_f_equal_max_500_f_wrap">
                        <div class="_fa_center">
							<?php ABPRF_Layout::switch_checkbox( 'display_equipment_id', $display_equipment_id ); ?>
                            <span class="_fs_label_mar_lr_xs"><?php esc_html_e( 'Equipment Groups ID', 'abprf-rental-forge' ); ?></span>
                        </div>
                        <div data-collapse="#display_equipment_id" class="<?php echo esc_attr( $display_equipment_id == 'on' ? 'rf_active' : '' ); ?>">
                            <label>
                                <input type="text" class="_form_control validation_id" name="equipment_id" value="<?php echo esc_attr( $equipment_id ); ?>" placeholder="<?php esc_attr_e( 'Ex : XYZ_123', 'abprf-rental-forge' ); ?>"/>
                            </label>
                        </div>
                    </div>
                    <div class="_divider_xs"></div>
					<?php ABPRF_Layout::info_text( 'display_equipment_id' ); ?>
                </div>
				<?php
			}

			public function category( $abprf_infos ): void {
				$abprf_configuration = array_key_exists( 'abprf_configuration', $abprf_infos ) ? $abprf_infos['abprf_configuration'] : [];
				$category_label      = isset( $abprf_configuration['category_label'] ) && $abprf_configuration['category_label'] ? $abprf_configuration['category_label'] : __( 'Category', 'abprf-rental-forge' );
				$equipment_category  = array_key_exists( 'category', $abprf_infos ) ? $abprf_infos['category'] : '';
				$display_category    = array_key_exists( 'display_category', $abprf_infos ) ? $abprf_infos['display_category'] : 'on';
				$all_categories      = ABPRF_Function::get_all_term_data( 'abprf_category' );
				?>
                <div class="_setting_item">
                    <div class="_f_equal_max_500_f_wrap">
                        <div class="_fa_center">
							<?php ABPRF_Layout::switch_checkbox( 'display_category', $display_category ); ?>
                            <span class="_fs_label_mar_lr_xs"><?php echo esc_html( $category_label ); ?></span>
                        </div>
                        <div data-collapse="#display_category" class="<?php echo esc_attr( $display_category == 'on' ? 'rf_active' : '' ); ?>">
							<?php if ( sizeof( $all_categories ) > 0 ) { ?>
                                <label>
                                    <select class="_form_control" name="category">
                                        <option disabled selected><?php esc_html_e( 'Please Select', 'abprf-rental-forge' ); ?></option>
										<?php foreach ( $all_categories as $category ) { ?>
                                            <option value="<?php echo esc_attr( $category ); ?>" <?php echo esc_attr( $equipment_category == $category ? 'selected' : '' ); ?>><?php echo esc_html( $category ); ?></option>
										<?php } ?>
                                    </select>
                                </label>
							<?php } else {
								$category_url = admin_url() . 'edit-tags.php?taxonomy=abprf_category&post_type=abprf_post';
								?>
                                <div class="_btn_warning_xs">
                                    <span><?php esc_html_e( 'Your have no Category. To Create category', 'abprf-rental-forge' ); ?>&nbsp; <a class="_abprf" href="<?php echo esc_url( $category_url ); ?>"><?php esc_html_e( 'Click Here', 'abprf-rental-forge' ); ?></a></span>
                                </div>
							<?php } ?>
                        </div>
                    </div>
                    <div class="_divider_xs"></div>
					<?php ABPRF_Layout::info_text( 'display_category' ); ?>
                </div>
				<?php
			}

			public function template( $abprf_infos ): void {
				$abprf_template = array_key_exists( 'abprf_template', $abprf_infos ) ? $abprf_infos['abprf_template'] : 'default';
				?>
                <div class="_setting_item">
                    <label class="_f_equal_max_500_f_wrap">
                        <span class="_mar_r_xs"><?php esc_html_e( 'Template', 'abprf-rental-forge' ); ?></span>
                        <select class="_form_control " name="abprf_template" data-collapse-target required>
                            <option disabled selected><?php esc_html_e( 'Please Select', 'abprf-rental-forge' ); ?></option>
                            <option value="default" <?php echo esc_attr( $abprf_template == 'default' ? 'selected' : '' ); ?>><?php esc_html_e( 'Default Template', 'abprf-rental-forge' ); ?></option>
                            <option value="light" <?php echo esc_attr( $abprf_template == 'light' ? 'selected' : '' ); ?>><?php esc_html_e( 'Light Template', 'abprf-rental-forge' ); ?></option>
                        </select>
                    </label>
                    <div class="_divider_xs"></div>
					<?php ABPRF_Layout::info_text( 'abprf_template' ); ?>
                </div>
				<?php
			}

			public function select_advance_date_number( $abprf_infos ): void {
				$advance_date_number = array_key_exists( 'advance_date_number', $abprf_infos ) ? $abprf_infos['advance_date_number'] : '15';
				?>
                <div class="_setting_item">
                    <label class="_f_equal_max_500_f_wrap">
                        <span class="_mar_r_xs"><?php esc_html_e( 'Number of advance booking date', 'abprf-rental-forge' ); ?></span>
                        <input type="number" pattern="[0-9]*" step="1" class="_form_control validation_number" name="advance_date_number" placeholder="Ex: 15" value="<?php echo esc_attr( $advance_date_number ); ?>"/>
                    </label>
                    <div class="_divider_xs"></div>
					<?php ABPRF_Layout::info_text( 'advance_date_number' ); ?>
                </div>
				<?php
			}
		}
		new ABPRF_General();
	}