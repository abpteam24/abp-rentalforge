<?php
	if (!defined('ABSPATH')) {
		exit; // Exit if accessed directly
	}
	if (!class_exists('ABPRF_Hooks')) {
		class ABPRF_Hooks {
			public function __construct() {
				add_action('abprf_title', [$this, 'title']);
				add_action('abprf_sub_title', [$this, 'sub_title']);
				//=============================//
				add_action('abprf_category', [$this, 'category'], 10, 2);
				add_action('abptm_capacity', [$this, 'capacity'], 10, 2);
				add_action('abptm_route_direction', [$this, 'route_direction']);
				add_action('abptm_the_content', [$this, 'the_content']);
				add_action('abprf_search_form', [$this, 'search_form'], 10, 3);
				add_action('abptm_registration', [$this, 'registration']);
				add_action('abptm_details_info', [$this, 'details_info'], 10, 2);
				add_action('abptm_selection_item', [$this, 'selection_item']);
				add_action('abptm_ticket', [$this, 'ticket'], 10, 2);
				add_action('abptm_seat_plan', [$this, 'seat_plan'], 10, 2);
				add_action('abprf_additional', [$this, 'additional_services'], 10, 2);
				add_action('abptm_pickup_drop', [$this, 'pickup_drop'], 10, 2);
				add_action('abprf_total_price', [$this, 'total_price']);
				add_action('abptm_search_list', [$this, 'search_list'], 10, 2);
				add_action('abptm_next_prev_day', [$this, 'next_prev_day']);
				add_action('abprf_hidden_form', [$this, 'hidden_form'], 10, 2);
				add_action('abptm_display_cart_item', [$this, 'display_cart_item']);
				add_action('abprf_pagination', array($this, 'pagination'));
			}
			public function title($abprf_infos = []): void { include ABPRF_Function::template_path('layout/title.php'); }
			public function sub_title($abprf_infos = []): void { include ABPRF_Function::template_path('layout/sub_title.php'); }
			//=============================//
			public function category($abprf_infos = [], $ribbon = false): void { include ABPRF_Function::template_path('layout/category.php'); }
			public function capacity($abprf_infos = [], $ribbon = false): void { include ABPRF_Function::template_path('layout/capacity.php'); }
			public function route_direction($abprf_infos = []): void { include ABPRF_Function::template_path('layout/route_direction.php'); }
			public function the_content($abprf_infos = []): void { include ABPRF_Function::template_path('layout/the_content.php'); }
			public function search_form($abprf_infos = [], $params = [], $form_data = []): void { include ABPRF_Function::template_path('layout/search_form.php'); }
			public function registration($form_data = []): void { include ABPRF_Function::template_path('layout/registration.php'); }
			public function details_info($abprf_infos = [], $form_data = []): void { include ABPRF_Function::template_path('layout/details_info.php'); }
			public function selection_item(): void { include ABPRF_Function::template_path('layout/selection_item.php'); }
			public function ticket($abprf_infos = [], $form_data = []): void { include ABPRF_Function::template_path('layout/ticket.php'); }
			public function seat_plan($abprf_infos = [], $form_data = []): void { include ABPRF_Function::template_path('layout/seat_plan.php'); }
			public function additional_services($abprf_infos = [], $form_data = []): void { include ABPRF_Function::template_path('layout/additional_services.php'); }
			public function pickup_drop($abprf_infos, $form_data): void { include ABPRF_Function::template_path('layout/pickup_drop.php'); }
			public function total_price($post_id): void { include ABPRF_Function::template_path('layout/total_price.php'); }
			public function search_list($form_data = [], $transport_item = []): void { include ABPRF_Function::template_path('layout/transport_list.php'); }
			public function next_prev_day($form_data = []): void { include ABPRF_Function::template_path('layout/next_prev_day.php'); }
			public function hidden_form($abprf_infos = [], $form_data = []): void { include ABPRF_Function::template_path('layout/hidden_form.php'); }
			public function display_cart_item($cart_item = []): void { include ABPRF_Function::template_path('layout/display_cart_item.php'); }
			public function pagination($args): void { include ABPRF_Function::template_path('layout/pagination.php'); }
		}
		new ABPRF_Hooks();
	}