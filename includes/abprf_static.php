<?php
    if (!defined('ABSPATH')) {
        exit; // Exit if accessed directly
    }
    if (!class_exists('ABPRF_Static')) {
        class ABPRF_Static {
            public static function array_info($key) {
                $current_date = current_time('Y-m-d H:i');
                $des = array(
                    'sub_title' => __('Note: Add a Sub-title to enable the Post sub-tile. Leave this blank if you dont want to show any Sub-title information for this Post.', 'abp-rentalforge'),
                    'rent_continue' => __('Note: This switch indicate property rent close/continue . You can  rent close/continue  by this switch. By default rent will be  continue', 'abp-rentalforge'),
                    'post_sku' => __('Note: Here you can add an SKU for this post. You can also show or hide it on the frontend by turning the switch On or Off.', 'abp-rentalforge'),
                    'abprf_template' => __('Note: Here You can change your details page template.', 'abp-rentalforge'),
                    'display_category' => __('Note : This switch indicate Post/Property Category . You can also show or hide it on the frontend by turning the switch On or Off.', 'abp-rentalforge'),
                    'display_location' => __('Note : Enable or disable store locations on the frontend using this switch. If multiple locations are selected, property stock will be managed separately for each location.', 'abp-rentalforge'),
                    'cat_name' => __('Note: Please enter a category name — the field cannot be empty. ', 'abp-rentalforge'),
                    'cat_slug' => __('Note: Category slug is optional — leave it blank to auto-generate from the name. ', 'abp-rentalforge'),
                    'cat_des' => __('Note: Category description is optional — you can add details to better explain this category. ', 'abp-rentalforge'),
                    'loc_name' => __('Note: Please enter a Location name — the field cannot be empty. ', 'abp-rentalforge'),
                    'loc_slug' => __('Note: Location slug is optional — leave it blank to auto-generate from the name. ', 'abp-rentalforge'),
                    'loc_des' => __('Note: Location Address is optional — you can add details to better explain this Location Full  Address. ', 'abp-rentalforge'),
                    'loc_geo' => __('Note: Add the GPS coordinates of this location (optional). These are used for distance-based and polygon-based "Near Me" searches on the frontend. ', 'abp-rentalforge'),
                    'loc_radius' => __('Note: Set a service radius in kilometers (optional). When a visitor searches near a point, locations whose coordinates fall inside this distance are matched. ', 'abp-rentalforge'),
                    'loc_polygon' => __('Note: Define a custom service area (geo-fence) for this location (optional). Enter one "lat,lng" coordinate pair per line, at least 3 points. Points inside the polygon match this location for nearby searches. ', 'abp-rentalforge'),
                    'feature_name' => __('Note: Please enter a Feature Label — the field cannot be empty. ', 'abp-rentalforge'),
                    'feature_slug' => __('Note: Feature slug is optional — leave it blank to auto-generate from the name. ', 'abp-rentalforge'),
                    'feature_des' => __('Note: Please enter a Feature Value  — the field cannot be empty. ', 'abp-rentalforge'),
                    //=============================//
                    'date_format' => __('Note:  If you want to change the Date  Format, simply choose a different format. The default date is: ', 'abp-rentalforge') . ' ' . date_i18n('D j M , Y', strtotime($current_date)),
                    'time_format' => __('Note : If you want to change the Time Format, simply choose a different format. The default Time Format is: ', 'abp-rentalforge') . ' ' . date_i18n(get_option('time_format'), strtotime($current_date)),
                    'sale_close_before' => __('Note: Enter the time in minutes to close  rent before current time. If not specified, it will default to 0 (e.g. 1 hour equals 60 minutes).', 'abp-rentalforge'),
                    'sale_close_after' => __('Note: Enter the time in minutes to close  rent after current time. If not specified, it will default to 0 (e.g. 1 hour equals 60 minutes).', 'abp-rentalforge'),
                    'advance_date_number' => __('Note: Kindly provide the number of days in advance for booking. By default, the advance booking period is set to 28 days.(optional) ', 'abp-rentalforge'),
                    'active_global_dates' => __('Note: Keep this switch ON to apply the global date settings.Switch it OFF if you want to set special date rules for this property.Date configuration options will open when turned OFF. ', 'abp-rentalforge'),
                    'date_type' => __('Note: Please Select your property operational date type. Default operational date will be Periodic', 'abp-rentalforge'),
                    'specific_dates' => __('Note: Please add your property operational Specific Date lists and Operation time length(optional). If operation time empty that means it will be default operation time.', 'abp-rentalforge'),
                    'operation_time' => __('Note: Please add your property rent  Operation time length(optional). If operation time empty that means it will be 24 hours(optional)', 'abp-rentalforge'),
                    'periodic_start_date' => __('Note: Please add your property rent Launching Date otherwise it will be start today ', 'abp-rentalforge'),
                    'periodic_end_date' => __('Note: Please add your property rent Terminate  Date otherwise it will be Continuously running periodically', 'abp-rentalforge'),
                    'periodic_after' => __('Note: Please add your periodically after days. if  your property rent operation day everyday this will be one(1).(optional)', 'abp-rentalforge'),
                    'date_rule' => __('Note: Enable this checkbox to configure special on/off date and time settings. This option is optional. If you set a date/time in the special “On” date, that date will remain active even if it falls within an “Off” date range or on weekends.', 'abp-rentalforge'),
                    'special_on_dates' => __('Note: If you add any date and time in Special On Dates, it will always remain active—even if that date falls within an off date range or on weekends.', 'abp-rentalforge'),
                    'weekend' => __('Note: Please select your weekend.Default all days open(optional)', 'abp-rentalforge'),
                    'day_wise_time' => __('Note: Day-wise operation time will apply only if the date does not fall within any Special On Date range. If the time field is left empty in Special On Dates, then the day-wise operation time will be applied for that date.', 'abp-rentalforge'),
                    'specific_off_dates' => __('Note: please add your specific Operation off dates.(optional)', 'abp-rentalforge'),
                    'off_date_range' => __('Note: If you have off days between two dates which can add here.(optional)', 'abp-rentalforge'),
                    'abprf_dates' => __('Note: Set a global date configuration for your property rentals that can be reused across all posts, with options to import and customize anytime.', 'abp-rentalforge'),
                    //=============================//
                    'post_id' => __('Note: You must select the Post under which this property belongs here. Selecting a Post is required — the data will not be saved if no Post is selected.', 'abp-rentalforge'),
                    'name' => __('Note: You must enter the property name in the field above. This field is required — the data will not be saved if the property name is not provided.', 'abp-rentalforge'),
                    'icon' => __('Note: Here You can set an image, icon, or emoji for each property directly', 'abp-rentalforge'),
                    'post_icon' => __('Note: Set a custom icon or emoji for this post. The selected icon/emoji will be displayed alongside the post title wherever the title appears across the website, helping it stand out and improve visual recognition.', 'abp-rentalforge'),
                    'qty_reserve_min_max' => __('Note: Set the total stock quantity available for rent. This field is required to save the property. You can also set reserve, minimum, and maximum quantity limits for customer bookings. Reserve quantity keeps specific items unavailable, minimum quantity defaults to 1, and maximum quantity will follow the available stock if left empty.', 'abp-rentalforge'),
                    'hourly_min_max' => __('Note: Enter the hourly rental rate to enable hourly booking for this property. You can also set minimum and maximum rental hours for customers. The default minimum is 1 hour, while the maximum will follow available time slots if left empty.', 'abp-rentalforge'),
                    'daily_min_max' => __('Note: Enter the daily rental rate to enable daily booking for this property.  You can also set minimum and maximum rental days for customers. The minimum defaults to 1 day if left empty, while the maximum depends on available booking dates. If no daily rate is provided, daily rental will remain disabled.', 'abp-rentalforge'),
                    'monthly_min_max' => __('Note: Enter the monthly rental rate to enable monthly booking for this property. This rate will apply only for the Monthly rent rule. You can also set minimum and maximum rental months. The default minimum is 1 month, while the maximum depends on available booking months.', 'abp-rentalforge'),
                    'deposit_type' => __('Note: There are three(3) types of deposit options: Fixed Amount (a set deposit regardless of quantity), Percentage of Total Price (calculated based on the total rental cost), and Fixed Amount per Quantity (applied for each item rented).', 'abp-rentalforge'),
                    'brand' => __('Note: Add a brand name to enable the property sub-tile. Leave this blank if you dont want to show any brand information for this item.', 'abp-rentalforge'),
                    'post_description' => __('Note: Add short description about this Post . Leave this blank if you dont want to show any Post description for this item.', 'abp-rentalforge'),
                    'related_item' => __('Note: Select related items to display on the details page. Leave this option empty or disabled if you do not want to show related items.', 'abp-rentalforge'),
                    'description' => __('Note: Add short description about this property. Leave this blank if you dont want to show any property description for this item.', 'abp-rentalforge'),
                    'price_rule' => __('Note: Select the Property Date & Time Rule that matches this post. If an incorrect rule is chosen, the system will automatically update and apply the post Date & Time Rule to the property upon publishing.', 'abp-rentalforge'),
                    'property_feature' => __('Note: If you want to add feature for this property, you can add Here. These feature will be show with this properties . You may leave this section empty if you do not want to show frontend. ', 'abp-rentalforge'),
                    'abprf_sliders' => __('Note: If you want to add an image gallery for this property, you can upload images below. These images will be merged with all properties under the same category. You may leave this section empty if you do not want to add images. ', 'abp-rentalforge'),
                    'time_slot_length' => __('Note: You can define the time slot interval for frontend time selection here. This controls how frequently time options will appear for users. By default, it is set to 60 minutes, meaning time slots will be available in 1-hour intervals.', 'abp-rentalforge'),
                    'day_time_start_end' => __('Note: You can define the start and end time of a rental day here. By default, a rental day runs from 10:00 AM to 10:00 AM the next day. The first time applies to the start of the first day, and the second time applies to the end of the following day. The total duration between these times must not exceed 24 hours.', 'abp-rentalforge'),
                    'hour_threshold' => __('Note: You can define how many hours will be counted as one full day here. By default, it is set to 24 hours. Adjust this value to control when a booking duration should be considered as a full day.', 'abp-rentalforge'),
                    'cut_off_date' => __('Note: You can set the cutoff date for allowing bookings in the next month. By default, users can make bookings for the current month up to the 10th. After this date, next month’s rental slots will become available for booking.', 'abp-rentalforge'),
                    'day_threshold' => __('Note: You can define how many days will be counted as one full month here. By default, it is set to 30 days. Adjust this value to control when a booking duration should be considered as a full month.', 'abp-rentalforge'),
                    'rent_rule' => __('Note: The items displayed are filtered by your selected Rent Time Rules. Properties not matching these rules are still available via the main Property List.', 'abp-rentalforge'),
                    //=============================//
                    '_tax_class' => __('Note: If you want to add any new tax class , Please go to WooCommerce ->configuration->Tax Area', 'abp-rentalforge'),
                    'enable_tax_msg' => __('Note: Your Woo-commerce Tax setting already disable. If you want to enable tax please enable woo-commerce tax.', 'abp-rentalforge'),
                    //=============================//
                    'display_additional_services' => __('Note: If you want sale/rent additional product/equipment with regular property then active this button and add additional service. Additional item not depends on  operation time.', 'abp-rentalforge'),
                    'additional_services' => __('Note: Add extra services for products/equipment with your property—import or set per Post (also usable globally); stock applies per Post, empty quantity = unlimited, empty max qty = no limit, empty/Zero price = free.', 'abp-rentalforge'),
                    'active_global_additional' => __('Note: Keep this switch ON to apply the global additional settings.Switch it OFF if you want to set special additional rules for this property.additional configuration options will open when turned OFF. ', 'abp-rentalforge'),
                    //=============================//
                    'client_form_option' => __('Use comma( , ) to separate option.', 'abp-rentalforge'),
                    'display_client_form' => __('Note: If you want to get Client information then active this button and add form/import global form or use global form as a client form', 'abp-rentalforge'),
                    'active_global_form' => __('Note: Keep this switch ON to apply the global Client Form settings.Switch it OFF if you want to set special  Client Form rules for this property. Client Form configuration options will open when turned OFF. ', 'abp-rentalforge'),
                    'global_client_forms' => __('Note: This is a flexibility global form system. Once you design the structure here, it serves as a global form. You can effortlessly import this form into any property or use this setting at any property,', 'abp-rentalforge'),
                    //=============================//
                    'abprf_tc' => __('You can set all rental-related Term & Condition here and use them globally across all properties. You can also import these Term & Condition into any individual property and customize them as needed.', 'abp-rentalforge'),
                    'tc_item' => __('Use the editor to customize and design your Terms & Conditions as you prefer. The content and formatting you create here will be displayed the same way on the frontend.', 'abp-rentalforge'),
                    'display_tc' => __('Use this switch to control whether the Term & Condition is displayed on the frontend. Turn the switch ON to show the Term & Condition, and OFF to hide it. By default, this option is set to ON.', 'abp-rentalforge'),
                    'active_global_tc' => __('Enable this switch to apply the global Term & Condition to this post. If you want to add custom Term & Condition specifically for this post, turn the switch OFF and add your custom Term & Condition below.You can also use the Import button to bring in global Term & Condition, which you can then edit or delete based on your needs.', 'abp-rentalforge'),
                    //=============================//
                    'abprf_faqs' => __('You can set all rental-related FAQs here and use them globally across all properties. You can also import these FAQs into any individual property and customize them as needed.', 'abp-rentalforge'),
                    'faq_item' => __('Both the Title and Description fields are required. If either field is left empty, this FAQ item will not be displayed on the frontend.', 'abp-rentalforge'),
                    'display_faq' => __('Use this switch to control whether the FAQ is displayed on the frontend. Turn the switch ON to show the FAQ, and OFF to hide it. By default, this option is set to ON.', 'abp-rentalforge'),
                    'active_global_faq' => __('Enable this switch to apply the global FAQ to this post. If you want to add custom FAQs specifically for this post, turn the switch OFF and add your custom FAQs below.You can also use the Import button to bring in global FAQs, which you can then edit or delete based on your needs.', 'abp-rentalforge'),
                    //=============================//
                    'search_get_wrong_data_info' => __('Somethings went Wrong ! Please Try again', 'abp-rentalforge'),
                    'sale_close_msg' => __('This Property rent close shortly. please try another Property.', 'abp-rentalforge'),
                    'not_date' => __('No Dates Found !', 'abp-rentalforge'),
                    'not_match' => __('No Results Found !', 'abp-rentalforge'),
                    'not_found' => __('No Post Found !', 'abp-rentalforge'),
                    'not_post_found' => __('No Post Found !', 'abp-rentalforge'),
                    'not_property_found' => __('No Property Found !', 'abp-rentalforge'),
                    'no_category' => __('No Category Found ! Please add Category to use Category feature', 'abp-rentalforge'),
                    'no_brand' => __('No Brand Found ! Please add Brand to use Brand feature', 'abp-rentalforge'),
                    'no_location' => __('No Location Found ! Please add Location to use Location feature', 'abp-rentalforge'),
                    'no_feature' => __('No Feature Found ! Please add Feature to use Feature', 'abp-rentalforge'),
                    'property_not_available' => __('The property is not available for the selected date and time. Please choose a different schedule.', 'abp-rentalforge'),
                    //=============================//
                    'must_wc' => __('RentalForge is entirely dependent on the WooCommerce plugin. Please install and activate the WooCommerce plugin otherwise the plugin will not work. Installing this tool may take some time', 'abp-rentalforge'),
                    //=============================//
                    'sign_up_msg' => __('Please Login your account to Download/View ticket !', 'abp-rentalforge'),
                    'no_permit_msg' => __('You are not permitted to Download/View this ticket !', 'abp-rentalforge'),
                    'wrong_msg_id' => __('We see, this id are not valid !', 'abp-rentalforge'),
                    'no_property_found' => __('Property not found or  rent close shortly', 'abp-rentalforge'),
                    'no_order_found' => __('Sorry ! We can not find any Order in your criteria.', 'abp-rentalforge'),
                    //''          => __( '', 'abp-rentalforge' ),
                );
                $des = apply_filters('abprf_info_array_filter', $des);
                return $des[$key] ?? '';
            }
            public static function svg($key): void {
                $des = [
                    'user_group_1' => '<svg  viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"  d="M17 20c0-1.657-2.239-3-5-3s-5 1.343-5 3m14-3c0-1.23-1.234-2.287-3-2.75M3 17c0-1.23 1.234-2.287 3-2.75m12-4.014a3 3 0 1 0-4-4.472m-8 4.472a3 3 0 0 1 4-4.472M12 14a3 3 0 1 1 0-6a3 3 0 0 1 0 6Z"/></svg>',
                    'user_group_2' => '<svg  viewBox="0 0 24 24"> <path fill="currentColor" fill-rule="evenodd" d="M12 6a3.5 3.5 0 1 0 0 7a3.5 3.5 0 0 0 0-7m-1.5 8a4 4 0 0 0-4 4c0 1.1.9 2 2 2h7a2 2 0 0 0 2-2a4 4 0 0 0-4-4zm6.8-3.1a5.5 5.5 0 0 0-2.8-6.3c.6-.4 1.3-.6 2-.6a3.5 3.5 0 0 1 .8 6.9m2.2 7.1h.5a2 2 0 0 0 2-2a4 4 0 0 0-4-4h-1.1l-.5.8c1.9 1 3.1 3 3.1 5.2M4 7.5a3.5 3.5 0 0 1 5.5-2.9A5.5 5.5 0 0 0 6.7 11A3.5 3.5 0 0 1 4 7.5M7.1 12H6a4 4 0 0 0-4 4c0 1.1.9 2 2 2h.5a6 6 0 0 1 3-5.2z" clip-rule="evenodd"/></svg>',
                    'plus' => '<svg viewBox="0 0 16 16"><path fill="currentColor" d="M14 7H9V2H7v5H2v2h5v5h2V9h5V7z"/></svg>',
                    'minus_1' => '<svg viewBox="0 0 16 16"><path fill="currentColor" d="M2 7h12v2H2V7z"/></svg>',
                    'minus_2' => '<svg viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.707 10.295a2.41 2.41 0 0 0 0 3.41l7.588 7.588a2.41 2.41 0 0 0 3.41 0l7.588-7.588a2.41 2.41 0 0 0 0-3.41l-7.588-7.588a2.41 2.41 0 0 0-3.41 0zM8.5 12h7"/></svg>',
                    'minus_3' => '<svg viewBox="0 0 32 32"><path fill="currentColor" d="M16 3C8.832 3 3 8.832 3 16s5.832 13 13 13s13-5.832 13-13S23.168 3 16 3zm0 2c6.087 0 11 4.913 11 11s-4.913 11-11 11S5 22.087 5 16S9.913 5 16 5zm-6 10v2h12v-2H10z"/></svg>',
                    'minus_4' => '<svg viewBox="0 0 24 24"><path fill="currentColor" d="M299 213H0v-42h299v42z"/></svg>',
                    'save' => '<svg  viewBox="0 0 100 100.016"><path fill="#23475F" d="M88.555 0H83v.016a2 2 0 0 1-2 2H19a2 2 0 0 1-2-2V0H4a4 4 0 0 0-4 4v92.016a4 4 0 0 0 4 4h92a4 4 0 0 0 4-4V11.525C100.049 11.436 88.564.071 88.555 0z"/><path fill="#1C3C50" d="M81.04 53.016H18.96a2 2 0 0 0-2 2v45h66.08v-45c0-1.106-.895-2-2-2zm-61.957-10h61.834a2 2 0 0 0 2-2V.555A1.993 1.993 0 0 1 81 2.015H19c-.916 0-1.681-.62-1.917-1.46v40.46a2 2 0 0 0 2 2.001z"/><path fill="#EBF0F1" d="M22 55.985h56a2 2 0 0 1 2 2v37.031a2 2 0 0 1-2 2H22c-1.104 0-2-.396-2-1.5V57.985a2 2 0 0 1 2-2z"/><path fill="#BCC4C8" d="M25 77.016h50v1H25v-1zm0 10h50v1H25v-1z"/><path fill="#1C3C50" d="M7 84.016h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2zm83 0h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2h-3a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2z"/><path fill="#BCC4C8" d="M37 1.989v36.026a2 2 0 0 0 2 2h39a2 2 0 0 0 2-2V1.989c0 .007-42.982.007-43 0zm37 29.027a2 2 0 0 1-2 2h-6a2 2 0 0 1-2-2V10.989a2 2 0 0 1 2-2h6a2 2 0 0 1 2 2v20.027z"/><path fill="#FF9D00" d="M78 55.985H22a2 2 0 0 0-2 2v10.031h60V57.985a2 2 0 0 0-2-2z"/></svg>',
                    'edit' => '<svg  viewBox="0 0 16 16"><path fill="currentColor" d="M15.49 7.3h-1.16v6.35H1.67V3.28H8V2H1.67A1.21 1.21 0 0 0 .5 3.28v10.37a1.21 1.21 0 0 0 1.17 1.25h12.66a1.21 1.21 0 0 0 1.17-1.25z"/><path fill="currentColor" d="M10.56 2.87L6.22 7.22l-.44.44l-.08.08l-1.52 3.16a1.08 1.08 0 0 0 1.45 1.45l3.14-1.53l.53-.53l.43-.43l4.34-4.36l.45-.44l.25-.25a2.18 2.18 0 0 0 0-3.08a2.17 2.17 0 0 0-1.53-.63a2.19 2.19 0 0 0-1.54.63l-.7.69l-.45.44zM5.51 11l1.18-2.43l1.25 1.26zm2-3.36l3.9-3.91l1.3 1.31L8.85 9zm5.68-5.31a.91.91 0 0 1 .65.27a.93.93 0 0 1 0 1.31l-.25.24l-1.3-1.3l.25-.25a.88.88 0 0 1 .69-.25z"/></svg>',
                    'drag' => '<svg  viewBox="0 0 16 16"><path fill="currentColor" d="m15.46 7l-3.2-2.19l-.71 1l2.29 1.57H8.62V2.16l1.57 2.29l1-.71L9 .54a1.25 1.25 0 0 0-2 0l-2.22 3.2l1 .71l1.59-2.29v5.22H2.16l2.29-1.57l-.71-1L.54 7a1.25 1.25 0 0 0 0 2l3.2 2.19l.71-1l-2.29-1.57h5.21v5.22l-1.56-2.29l-1 .71L7 15.46a1.25 1.25 0 0 0 2.06 0l2.19-3.2l-1-.71l-1.63 2.29V8.62h5.22l-2.29 1.57l.71 1L15.46 9a1.25 1.25 0 0 0 0-2z"/></svg>',
                    'order' => '<svg  viewBox="0 0 16 16"> <path fill="none" stroke="currentColor" stroke-linejoin="round" d="M5 11.5h4M5 9h6M5 6.5h6m-5.5-4h-2v12h9v-12h-2m-5-1h5l-.625 2h-3.75z"/></svg>',
                    'seat' => '<svg viewBox="0 0 24 24"><path fill="currentColor" d="M4 21v-6h16v6h-2v-4H6v4H4Zm-1-7v-3h3v3H3Zm4 0V3h10v11H7Zm11 0v-3h3v3h-3Z"/></svg>',
                    'globe' => '<svg viewBox="0 0 100 100"><path id="gisGlobe0" fill="currentColor" fill-opacity="1" fill-rule="nonzero" stroke="none" stroke-dasharray="none" stroke-dashoffset="188.976" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="4" stroke-opacity="1" stroke-width="5" d="M52.5 5.682v20.187h17.676c-.988-2.823-2.13-5.429-3.408-7.75c-3.966-7.2-9-11.541-14.268-12.437zm-5 .197c-4.93 1.223-9.61 5.462-13.342 12.24c-1.278 2.321-2.42 4.927-3.408 7.75H47.5V5.88zM35.98 7.232C25.985 10.5 17.545 17.163 12.01 25.87h13.455c1.187-3.695 2.633-7.112 4.312-10.162c1.793-3.255 3.88-6.123 6.203-8.475zm29.41.463c2.145 2.263 4.082 4.967 5.758 8.012c1.68 3.05 3.123 6.467 4.307 10.162H87.99c-5.28-8.306-13.202-14.761-22.6-18.174zM9.257 30.87A44.79 44.79 0 0 0 5.072 47.5h16.79c.194-5.872.957-11.469 2.202-16.63H9.256zm19.974 0c-1.32 5.077-2.15 10.696-2.363 16.631H47.5V30.87H29.23zm23.27 0V47.5H74.06c-.212-5.935-1.043-11.554-2.364-16.63H52.5zm24.355 0c1.243 5.163 2.004 10.76 2.198 16.631h15.875a44.79 44.79 0 0 0-4.184-16.63h-13.89zM5.072 52.5a44.79 44.79 0 0 0 4.184 16.63h14.572c-1.174-5.176-1.865-10.774-1.994-16.63H5.072zm21.762 0c.14 5.915.901 11.53 2.146 16.63H47.5V52.5H26.834zm25.666 0v16.63h19.445c1.245-5.1 2.006-10.715 2.147-16.63H52.5zm26.576 0c-.129 5.855-.815 11.453-1.986 16.63h13.654a44.79 44.79 0 0 0 4.184-16.63H79.076zM12.01 74.13c5.285 8.313 13.214 14.772 22.62 18.183c-1.785-2.05-3.415-4.407-4.853-7.018c-1.83-3.325-3.389-7.08-4.63-11.164H12.01zm18.394 0c1.062 3.216 2.326 6.159 3.754 8.753c3.5 6.355 7.834 10.475 12.424 11.974c.306.023.61.054.918.07V74.132H30.404zm22.096 0v20.798a45.48 45.48 0 0 0 2.127-.162c4.485-1.575 8.713-5.658 12.14-11.883c1.429-2.594 2.693-5.537 3.754-8.752H52.5zm23.275 0c-1.239 4.085-2.796 7.84-4.627 11.165c-1.311 2.382-2.782 4.556-4.386 6.476a45.06 45.06 0 0 0 21.228-17.64H75.775z" color="currentColor" color-interpolation="sRGB" color-rendering="auto" display="inline" opacity="1" vector-effect="none" visibility="visible"/></svg>',
                    'setting' => '<svg viewBox="0 0 24 24"><g fill="none" fill-rule="evenodd"><path d="M24 0v24H0V0h24ZM12.594 23.258l-.012.002l-.071.035l-.02.004l-.014-.004l-.071-.036c-.01-.003-.019 0-.024.006l-.004.01l-.017.428l.005.02l.01.013l.104.074l.015.004l.012-.004l.104-.074l.012-.016l.004-.017l-.017-.427c-.002-.01-.009-.017-.016-.018Zm.264-.113l-.014.002l-.184.093l-.01.01l-.003.011l.018.43l.005.012l.008.008l.201.092c.012.004.023 0 .029-.008l.004-.014l-.034-.614c-.003-.012-.01-.02-.02-.022Zm-.715.002a.023.023 0 0 0-.027.006l-.006.014l-.034.614c0 .012.007.02.017.024l.015-.002l.201-.093l.01-.008l.003-.011l.018-.43l-.003-.012l-.01-.01l-.184-.092Z"/><path fill="currentColor" d="M16 15c1.306 0 2.418.835 2.83 2H20a1 1 0 1 1 0 2h-1.17a3.001 3.001 0 0 1-5.66 0H4a1 1 0 1 1 0-2h9.17A3.001 3.001 0 0 1 16 15Zm0 2a1 1 0 1 0 0 2a1 1 0 0 0 0-2ZM8 9a3 3 0 0 1 2.762 1.828l.067.172H20a1 1 0 0 1 .117 1.993L20 13h-9.17a3.001 3.001 0 0 1-5.592.172L5.17 13H4a1 1 0 0 1-.117-1.993L4 11h1.17A3.001 3.001 0 0 1 8 9Zm0 2a1 1 0 1 0 0 2a1 1 0 0 0 0-2Zm8-8c1.306 0 2.418.835 2.83 2H20a1 1 0 1 1 0 2h-1.17a3.001 3.001 0 0 1-5.66 0H4a1 1 0 0 1 0-2h9.17A3.001 3.001 0 0 1 16 3Zm0 2a1 1 0 1 0 0 2a1 1 0 0 0 0-2Z"/></g></svg>',
                    'status' => '<svg  viewBox="0 0 24 24"><path fill="currentColor" fill-rule="evenodd" d="M16.001 5.2q-.262.385-.445.82a7 7 0 0 0-.157.405c-.165.461-.27.881-.373 1.303l-.005.019c-.132.533-.264 1.067-.525 1.687a5 5 0 0 1-.48.88l-.006.006l-.002.004c2.325.684 4.925-.224 5.868-2.452c.58-1.368.257-3.08-1.052-3.672c-1.054-.476-2.088-.086-2.822 1M3.184 12.023q.147-.447.38-.854q.111-.194.224-.37c.264-.408.532-.742.8-1.076l.026-.031c.335-.42.672-.84.998-1.414q.234-.407.386-.854l.006-.019l.018-.058c1.897 1.55 2.71 4.266 1.52 6.362c-.729 1.289-2.258 2.021-3.487 1.268c-.99-.605-1.29-1.702-.871-2.954M21 17.251c-2.51 0-4.544-2.108-4.544-4.708h-2.654C13.802 16.662 17.025 20 21 20zm-8.663-4.708c0 4.119-3.223 7.457-7.198 7.457v-2.75c2.51 0 4.544-2.107 4.544-4.707zm0 0l-.002-8.324H9.68l.002 8.324z" clip-rule="evenodd"/></svg>',
                    'category' => '<svg  viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="12" rx="2"/>  <path d="M3 9h18"/>  <path d="M7 16v2"/>  <path d="M17 16v2"/><circle cx="7" cy="18.5" r="0.5" fill="currentColor" stroke="none"/>  <circle cx="17" cy="18.5" r="0.5" fill="currentColor" stroke="none"/></svg>',
                    'category_1' => '<svg  viewBox="0 0 24 24" fill="none">  <rect x="4" y="7" width="16" height="12" rx="2" stroke="currentColor" stroke-width="2"/>  <line x1="4" y1="11" x2="20" y2="11" stroke="currentColor" stroke-width="2"/>  <line x1="8" y1="4" x2="8" y2="9" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>  <line x1="16" y1="4" x2="16" y2="9" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>  <circle cx="8.5" cy="15" r="1.3" fill="currentColor"/></svg>',
                    'brand_1' => '<svg  viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l7 3v6c0 5-3 8.5-7 11-4-2.5-7-6-7-11V5z"/>  <path d="M9 12l2 2 4-4"/></svg>',
                    'brand_2' => '<svg  viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="3" width="16" height="18" rx="1"/><line x1="8" y1="7" x2="8" y2="7.01"/><line x1="12" y1="7" x2="12" y2="7.01"/><line x1="16" y1="7" x2="16" y2="7.01"/><line x1="8" y1="11" x2="8" y2="11.01"/><line x1="12" y1="11" x2="12" y2="11.01"/><line x1="16" y1="11" x2="16" y2="11.01"/><path d="M9 21v-4h6v4"/></svg>',
                    'organizer_1' => '<svg  viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="3.5"/>  <path d="M5 20c0-3.5 3-6 7-6s7 2.5 7 6"/>  <path d="M17.5 4.5l1 1 2-2"/></svg>',
                    'organizer_2' => '<svg  viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="3" width="16" height="18" rx="2"/><path d="M8 8h2"/><path d="M8 12h2"/><path d="M8 16h2"/><path d="M14 8l1.5 1.5L18 7"/><path d="M14 12l1.5 1.5L18 11"/><path d="M14 16l1.5 1.5L18 15"/></svg>',
                    'date_1' => '<svg viewBox="0 0 48 48">    <path fill="#CFD8DC" d="M5 38V14h38v24c0 2.2-1.8 4-4 4H9c-2.2 0-4-1.8-4-4z"/>    <path fill="#F44336" d="M43 10v6H5v-6c0-2.2 1.8-4 4-4h30c2.2 0 4 1.8 4 4z"/>    <g fill="#B71C1C">        <circle cx="33" cy="10" r="3"/>        <circle cx="15" cy="10" r="3"/>    </g>    <path fill="#B0BEC5" d="M33 3c-1.1 0-2 .9-2 2v5c0 1.1.9 2 2 2s2-.9 2-2V5c0-1.1-.9-2-2-2zM15 3c-1.1 0-2 .9-2 2v5c0 1.1.9 2 2 2s2-.9 2-2V5c0-1.1-.9-2-2-2z"/>    <path fill="#90A4AE" d="M13 20h4v4h-4zm6 0h4v4h-4zm6 0h4v4h-4zm6 0h4v4h-4zm-18 6h4v4h-4zm6 0h4v4h-4zm6 0h4v4h-4zm6 0h4v4h-4zm-18 6h4v4h-4zm6 0h4v4h-4zm6 0h4v4h-4zm6 0h4v4h-4z"/></svg>',
                    'date_2' => '<svg  viewBox="0 0 64 64">    <path fill="#ba9372" d="M58.1 19.6c-9.8-6-25-14.8-27.1-14.8c-2.1 0-16.7 8.9-26 14.8l-.5-.9C6.9 17.2 28 3.8 31 3.8s25.1 13.4 27.6 15l-.5.8"/>    <path fill="#93a2aa" d="M62 56.8c0 1.6-1.2 3-2.7 3H6.8c-1.5 0-2.7-1.3-2.7-3V21.1c0-1.6 1.2-3 2.7-3h52.5c1.5 0 2.7 1.3 2.7 3v35.7"/>    <path fill="#ed4c5c" d="M60 21.1c0-1.6-1.2-3-2.7-3H4.7c-1.5 0-2.7 1.3-2.7 3v9.5h58v-9.5z"/>    <path fill="#d9e3e8" d="M2 30.6v26.2c0 1.6 1.2 3 2.7 3h52.5c1.5 0 2.7-1.3 2.7-3V30.6H2z"/> <path fill="#93a2aa" d="M4.5 33h6v2.2h-6zm7.8 0h6v2.2h-6zm7.9 0h6v2.2h-6zm7.8 0h6v2.2h-6zm7.8 0h6v2.2h-6zm7.9 0h6v2.2h-6zm7.8 0h6v2.2h-6zM28 37.4h6v2.2h-6zm7.8 0h6v2.2h-6zm7.9 0h6v2.2h-6zm7.8 0h6v2.2h-6zm-47 4.4h6V44h-6zm7.8 0h6V44h-6zm7.9 0h6V44h-6zm7.8 0h6V44h-6zm7.8 0h6V44h-6zm7.9 0h6V44h-6zm7.8 0h6V44h-6zm-47 4.5h6v2.2h-6zm7.8 0h6v2.2h-6zm7.9 0h6v2.2h-6zm7.8 0h6v2.2h-6zm7.8 0h6v2.2h-6zm7.9 0h6v2.2h-6zm7.8 0h6v2.2h-6zm-47 4.4h6v2.2h-6zm7.8 0h6v2.2h-6zm7.9 0h6v2.2h-6zm7.8 0h6v2.2h-6zm7.8 0h6v2.2h-6zm7.9 0h6v2.2h-6zm7.8 0h6v2.2h-6zm-47 4.4h6v2.2h-6zm7.8 0h6v2.2h-6zm7.9 0h6v2.2h-6zm7.8 0h6v2.2h-6zm7.8 0h6v2.2h-6zm7.9 0h6v2.2h-6z"/><ellipse cx="31.2" cy="6.2" fill="#333" rx="1.8" ry="1.9"/><ellipse cx="31" cy="6.2" fill="#93a2aa" rx="1.8" ry="1.9"/>    <path fill="#fff" d="M19.5 25.5v.2c0 .6.1 1 .2 1.3c.1.2.3.4.7.4c.4 0 .6-.1.7-.4c.1-.2.1-.4.1-.8v-5.5h1.6v5.4c0 .7-.1 1.2-.3 1.6c-.4.7-1 1-2 1s-1.6-.3-2-.8c-.3-.5-.5-1.3-.5-2.2v-.2h1.5m5-4.8h1.6v4.8c0 .5.1.9.2 1.2c.2.4.6.7 1.3.7c.6 0 1.1-.2 1.3-.7c.1-.3.1-.7.1-1.2v-4.8h1.6v4.8c0 .8-.1 1.5-.4 1.9c-.5.8-1.4 1.3-2.7 1.3c-1.3 0-2.2-.4-2.7-1.3c-.3-.5-.4-1.1-.4-1.9c.1 0 .1-4.8.1-4.8m7.7 0h1.6v6.4h3.8v1.4h-5.4v-7.8m10 0H44l-2.6 4.9v2.9h-1.6v-2.9l-2.7-4.9H39l1.6 3.4l1.6-3.4"/></svg>',
                    'clone_1' => '<svg viewBox="0 0 1792 1792"><path fill="currentColor" d="M1664 1632V544q0-13-9.5-22.5T1632 512H544q-13 0-22.5 9.5T512 544v1088q0 13 9.5 22.5t22.5 9.5h1088q13 0 22.5-9.5t9.5-22.5zm128-1088v1088q0 66-47 113t-113 47H544q-66 0-113-47t-47-113V544q0-66 47-113t113-47h1088q66 0 113 47t47 113zm-384-384v160h-128V160q0-13-9.5-22.5T1248 128H160q-13 0-22.5 9.5T128 160v1088q0 13 9.5 22.5t22.5 9.5h160v128H160q-66 0-113-47T0 1248V160Q0 94 47 47T160 0h1088q66 0 113 47t47 113z"/></svg>',
                    'clone_2' => '<svg viewBox="0 0 36 36"><path fill="currentColor" d="M24 10V6a2 2 0 0 0-2-2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h4V12a2 2 0 0 1 2-2Z" class="clr-i-solid clr-i-solid-path-1"/>    <path fill="currentColor" d="M30 12H14a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V14a2 2 0 0 0-2-2Zm-2 11h-5v5h-2v-5h-5v-2h5v-5h2v5h5Z" class="clr-i-solid clr-i-solid-path-2"/>    <path fill="none" d="M0 0h36v36H0z"/></svg>',
                    'close_1' => '<svg viewBox="0 0 304 384"><path fill="currentColor" d="M299 73L179 192l120 119l-30 30l-120-119L30 341L0 311l119-119L0 73l30-30l119 119L269 43z"/></svg>',
                    'close_2' => '<svg viewBox="0 0 24 24"><path fill="currentColor" d="M19 3H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2m-3.4 14L12 13.4L8.4 17L7 15.6l3.6-3.6L7 8.4L8.4 7l3.6 3.6L15.6 7L17 8.4L13.4 12l3.6 3.6l-1.4 1.4Z"/></svg>',
                    'view_1' => '<svg viewBox="0 0 24 24" stroke="currentColor"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M2.25.75h19.5s1.5 0 1.5 1.5v19.5s0 1.5-1.5 1.5H2.25s-1.5 0-1.5-1.5V2.25s0-1.5 1.5-1.5"/><path d="M4.267 10.722a1.825 1.825 0 0 0 0 2.544C5.818 14.821 8.591 17.25 12 17.25c3.41 0 6.183-2.428 7.735-3.983a1.825 1.825 0 0 0 0-2.544C18.182 9.168 15.406 6.739 12 6.739s-6.18 2.427-7.733 3.983"/><path d="M9.75 11.991a2.25 2.25 0 1 0 4.5 0a2.25 2.25 0 0 0-4.5 0"/></g></svg>',
                    'view_2' => '<svg viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-width="2" d="M12 21c-5 0-11-5-11-9s6-9 11-9s11 5 11 9s-6 9-11 9Zm0-14a5 5 0 1 0 0 10a5 5 0 0 0 0-10Z"/></svg>',
                    'view_3' => '<svg viewBox="0 0 24 24"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"><path d="M5 12s2.545-5 7-5c4.454 0 7 5 7 5s-2.546 5-7 5c-4.455 0-7-5-7-5z"/><path d="M12 13a1 1 0 1 0 0-2a1 1 0 0 0 0 2zm9 4v2a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-2M21 7V5a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v2"/></g></svg>',
                    'pdf_1' => '<svg  viewBox="0 0 48 48"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M7.45 5.5a2 2 0 0 0-1.95 2v33.1a2 2 0 0 0 2 2h33.1a2 2 0 0 0 2-2V7.45a2 2 0 0 0-2-1.95Z"/><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" d="M20.09 30V18h2a6 6 0 0 1 6 6h0a6 6 0 0 1-6 6Zm12.39-11.96h5.98m-5.98 5.98h3.9m-3.9-5.98V30M9.54 30V18h4a4 4 0 0 1 0 8h-4"/></svg>',
                    'pdf_2' => '<svg  viewBox="0 0 24 24" fill="none">  <path d="M6 2h9l5 5v13a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z" fill="#e53e3e"/>  <path d="M15 2v5h5" fill="#ffffff" opacity="0.4"/>  <rect x="4" y="14" width="16" height="6" rx="1" fill="#c53030"/>  <text x="12" y="18.5" text-anchor="middle" font-size="6" font-weight="700" fill="#ffffff" font-family="Arial, sans-serif">PDF</text></svg>',
                    'pdf_3' => '<svg viewBox="0 0 24 24" fill="none">  <circle cx="12" cy="12" r="11" fill="#e53e3e"/>  <path d="M8 7h6l3 3v7a1 1 0 0 1-1 1H8a1 1 0 0 1-1-1V8a1 1 0 0 1 1-1z" fill="#ffffff"/>  <text x="12" y="15.5" text-anchor="middle" font-size="5" font-weight="700" fill="#e53e3e" font-family="Arial, sans-serif">PDF</text></svg>',
                    'pdf_4' => '<svg  viewBox="0 0 32 32" fill="none">  <path d="M7 2h13l7 7v19a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z" fill="currentColor"/>  <path d="M20 2v7h7" fill="#f87171"/>  <rect x="8" y="12" width="10" height="1.5" rx="0.75" fill="#ffffff" opacity="0.6"/>  <rect x="8" y="16" width="14" height="1.5" rx="0.75" fill="#ffffff" opacity="0.6"/>  <rect x="4" y="21" width="20" height="8" rx="1.5" fill="#991b1b"/>  <text x="14" y="27" text-anchor="middle" font-size="8" font-weight="700" fill="#ffffff" font-family="Arial, sans-serif">PDF</text></svg>',
                    'property' => '<svg viewBox="0 0 120 125" fill="none" stroke="currentColor" stroke-width="4" stroke-linejoin="round" stroke-linecap="round"><path d="M5 55 L 60 15 L 115 55"/><rect x="18" y="50" width="84" height="70" rx="2"/><rect x="50" y="80" width="24" height="40"/> <rect x="30" y="62" width="16" height="16"/> <rect x="74" y="62" width="16" height="16"/></svg>',
                    'check_1' => '<svg  viewBox="0 0 16 16"><path fill="currentColor" d="m5.063 2.616l-1.47.002l-.147.005a1.977 1.977 0 0 0-1.828 1.971l-.002 1.469l-1.038 1.04l-.115.126A1.977 1.977 0 0 0 .58 9.898l1.037 1.037l.002 1.471l.005.148a1.977 1.977 0 0 0 1.971 1.828l1.468-.001l1.04 1.04l.126.116a1.977 1.977 0 0 0 2.67-.116l1.036-1.04l1.471.001l.148-.005a1.977 1.977 0 0 0 1.83-1.971l-.002-1.472l1.04-1.036l.115-.126a1.977 1.977 0 0 0-.115-2.67l-1.04-1.04l.001-1.467l-.005-.148a1.977 1.977 0 0 0-1.971-1.83l-1.471-.001l-1.037-1.037a1.976 1.976 0 0 0-2.796 0zm3.127-.33l1.331 1.331h1.885c.539 0 .976.438.976.977v1.883l1.332 1.333c.381.38.381 1 0 1.38l-1.333 1.331l.001 1.885a.976.976 0 0 1-.976.976H9.52l-1.33 1.332a.976.976 0 0 1-1.381 0l-1.333-1.333l-1.883.001a.976.976 0 0 1-.976-.976l-.001-1.885l-1.331-1.33a.976.976 0 0 1 0-1.381l1.331-1.333V4.594c0-.539.438-.976.977-.976l1.883-.001L6.81 2.286a.976.976 0 0 1 1.38 0m2.664 3.86a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708 0l-2-2a.5.5 0 1 1 .708-.708L6.5 9.793l3.646-3.647a.5.5 0 0 1 .708 0"/></svg>',
                    'check_2' => '<svg   viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m4 12l4.95 4.95L19.557 6.343"/></svg>',
                    'check_3' => '<svg  viewBox="0 0 16 16"><path fill="currentColor" d="M14 1H2c-.55 0-1 .45-1 1v12c0 .55.45 1 1 1h12c.55 0 1-.45 1-1V2c0-.55-.45-1-1-1m-1.56 5.44l-5 5c-.12.12-.28.18-.44.18s-.32-.06-.44-.18l-2.5-2.5c-.24-.24-.24-.64 0-.88s.64-.24.88 0L7 10.12l4.56-4.56c.24-.24.64-.24.88 0c.25.24.25.64 0 .88"/></svg>',
                    'check_4' => '<svg  viewBox="0 0 12 12">    <g fill="none" stroke="currentColor">        <rect width="11" height="11" x=".5" y=".5" rx=".5" ry=".5"/>        <path stroke-linecap="round" stroke-linejoin="round" d="m3 6.5l2 2l4-4"/>    </g></svg>',
                    'box_1' => '<svg  viewBox="0 0 24 24">    <path fill="currentColor" d="M5 21q-.825 0-1.413-.588T3 19V5q0-.825.588-1.413T5 3h14q.825 0 1.413.588T21 5v14q0 .825-.588 1.413T19 21H5Zm0-2h14V5H5v14Z"/></svg>',
                    'box_2' => '<svg viewBox="0 0 24 24">    <path fill="currentColor" d="M4 20V4h16v16H4Zm1-1h14V5H5v14Z"/></svg>',
                    'csv_1' => '<svg  viewBox="0 0 32 32" fill="none">  <path d="M7 2h13l7 7v19a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z" fill="currentColor"/>  <path d="M20 2v7h7" fill="#4ade80"/>  <rect x="8" y="12" width="10" height="1.5" rx="0.75" fill="#ffffff" opacity="0.6"/>  <rect x="8" y="16" width="14" height="1.5" rx="0.75" fill="#ffffff" opacity="0.6"/>  <rect x="4" y="21" width="20" height="8" rx="1.5" fill="#166534"/>  <text x="14" y="27" text-anchor="middle" font-size="8" font-weight="700" fill="#ffffff" font-family="Arial, sans-serif">CSV</text></svg>',
                    'csv_2' => '<svg  viewBox="0 0 24 24" fill="none" >  <circle cx="12" cy="12" r="11" fill="currentColor"/>  <path d="M8 7h6l3 3v7a1 1 0 0 1-1 1H8a1 1 0 0 1-1-1V8a1 1 0 0 1 1-1z" fill="currentColor"/>  <text x="12" y="15.5" text-anchor="middle" font-size="5" font-weight="700" fill="#38a169" font-family="Arial, sans-serif">CSV</text></svg>',
                    'csv_3' => '<svg  viewBox="0 0 24 24" fill="none">  <path d="M6 2h9l5 5v13a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z" fill="currentColor"/>  <path d="M15 2v5h5" fill="#ffffff" opacity="0.4"/>  <rect x="4" y="14" width="16" height="6" rx="1" fill="#2f855a"/>  <text x="12" y="18.5" text-anchor="middle" font-size="6" font-weight="700" fill="#ffffff" font-family="Arial, sans-serif">CSV</text></svg>',
                    '' => '',
                ];
                $allowed_svg_tags = [
                    'svg' => ['class' => true, 'aria-hidden' => true, 'aria-labelledby' => true, 'role' => true, 'xmlns' => true, 'width' => true, 'height' => true, 'viewbox' => true, 'fill' => true, 'stroke' => true, 'stroke-width' => true,], 'g' => ['fill' => true,], 'title' => ['title' => true,], 'path' => ['d' => true, 'fill' => true, 'stroke' => true, 'stroke-width' => true, 'stroke-linecap' => true, 'stroke-linejoin' => true,], 'rect' => ['x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true, 'ry' => true, 'fill' => true, 'stroke' => true,],
                    'circle' => ['cx' => true, 'cy' => true, 'r' => true, 'fill' => true, 'stroke' => true,],
                    'line' => ['x1' => true, 'y1' => true, 'x2' => true, 'y2' => true, 'stroke' => true, 'stroke-width' => true,],
                    'polyline' => ['points' => true, 'fill' => true, 'stroke' => true,],
                ];
                $icons = $des[$key] ?? '';
                echo !empty($icons) ? wp_kses($icons, $allowed_svg_tags) : '';
            }
            public static function static_form($key = ''): array {
                $form['pass_name'] = ['type' => 'text', 'required' => 'on', 'label' => __('First Name', 'abp-rentalforge')];
                $form['pass_name_2'] = ['type' => 'text', 'required' => 'on', 'label' => __('Last Name', 'abp-rentalforge')];
                $form['pass_email'] = ['type' => 'email', 'required' => 'on', 'label' => __('E-Mail', 'abp-rentalforge')];
                $form['pass_phone'] = ['type' => 'text', 'required' => 'on', 'label' => __('Phone', 'abp-rentalforge')];
                $form['pass_gender'] = ['type' => 'select', 'required' => 'off', 'label' => __('Gender', 'abp-rentalforge'), 'option' => 'male,female'];
                $form['pass_date'] = ['type' => 'date', 'required' => 'off', 'label' => __('Date of Birth', 'abp-rentalforge')];
                $form['pass_address'] = ['type' => 'textarea', 'required' => 'off', 'label' => __('Address', 'abp-rentalforge')];
                if (!is_string($key) && !is_int($key)) {
                    return $form;
                }
                if ($key === '') {
                    return $form;
                }
                return is_array($form[$key] ?? null) ? $form[$key] : [];
            }
            public static function static_additional(): array {
                return [
                    'additional_service_1' => ['icon' => 'fas fa-helmet-un', 'name' => __('Helmet', 'abp-rentalforge'), 'qty' => 50, 'max_qty' => 1, 'price' => 0, 'returnable' => 'yes', 'description' => __('1x Safety Helmet per order. Keep your head protected at no extra cost. Your safety is our priority!', 'abp-rentalforge'),],
                    'additional_service_2' => ['icon' => 'fas fa-suitcase', 'name' => __('Storage', 'abp-rentalforge'), 'qty' => 30, 'max_qty' => 3, 'price' => 2.99, 'returnable' => 'no', 'description' => __('Optional baggage support is available as a paid service to help carry your essentials with ease.', 'abp-rentalforge'),],
                    'additional_service_3' => ['icon' => 'fas fa-user-tie', 'name' => __('Tie', 'abp-rentalforge'), 'qty' => 100, 'price' => 1.00, 'returnable' => 'no', 'description' => __('Multiple color available', 'abp-rentalforge'),],
                    'additional_service_4' => ['icon' => 'fas fa-shoe-prints', 'name' => __('Shoes', 'abp-rentalforge'), 'qty' => 100, 'price' => 1.00, 'returnable' => 'yes', 'description' => __('Multiple Size available', 'abp-rentalforge'),]
                ];
            }
            public static function static_feature(): array {
                return [
                    'fec_id_1' => ['icon' => '⛽', 'label' => __('Fuel Type', 'abp-rentalforge'), 'value' => __('Electric', 'abp-rentalforge')],
                    'fec_id_2' => ['icon' => '⛽', 'label' => __('Fuel Type', 'abp-rentalforge'), 'value' => __(' Hybrid', 'abp-rentalforge')],
                    'fec_id_3' => ['icon' => '⛽', 'label' => __('Fuel Type', 'abp-rentalforge'), 'value' => __('Diesel', 'abp-rentalforge')],
                    'fec_id_4' => ['icon' => '⛽', 'label' => __('Fuel Type', 'abp-rentalforge'), 'value' => __('Petrol', 'abp-rentalforge')],
                    'fec_id_5' => ['icon' => 'fas fa-cog', 'label' => __('Transmission', 'abp-rentalforge'), 'value' => __('Automatic', 'abp-rentalforge')],
                    'fec_id_6' => ['icon' => 'fas fa-tachometer-alt', 'label' => __('Mileage', 'abp-rentalforge'), 'value' => __('15 km/L', 'abp-rentalforge')],
                    'fec_id_7' => ['icon' => 'fas fa-tachometer-alt', 'label' => __('Mileage', 'abp-rentalforge'), 'value' => __('40 km/L', 'abp-rentalforge')],
                    'fec_id_8' => ['icon' => 'fas fa-tachometer-alt', 'label' => __('Mileage', 'abp-rentalforge'), 'value' => __('10 km/L', 'abp-rentalforge')],
                    'fec_id_9' => ['icon' => '⚡', 'label' => __('Performance', 'abp-rentalforge'), 'value' => __('High efficiency & smooth driving systems', 'abp-rentalforge')],
                    'fec_id_10' => ['icon' => '🛡️', 'label' => __('Safety', 'abp-rentalforge'), 'value' => __('ABS, Airbags, ADAS systems', 'abp-rentalforge')],
                    'fec_id_11' => ['icon' => '🧭', 'label' => __('Navigation', 'abp-rentalforge'), 'value' => __('Smart GPS & route optimization', 'abp-rentalforge')],
                    'fec_id_12' => ['icon' => 'fas fa-users', 'label' => __('Capacity', 'abp-rentalforge'), 'value' => __('4 Persons', 'abp-rentalforge')],
                    'fec_id_13' => ['icon' => 'fas fa-users', 'label' => __('Capacity', 'abp-rentalforge'), 'value' => __('5 Seater', 'abp-rentalforge')],
                    'fec_id_14' => ['icon' => 'fas fa-suitcase', 'label' => __('Boot Space', 'abp-rentalforge'), 'value' => __('400L', 'abp-rentalforge')],
                    'fec_id_15' => ['icon' => '📸', 'label' => __('Sensor Quality', 'abp-rentalforge'), 'value' => __('Full-frame / APS-C high resolution sensors', 'abp-rentalforge')],
                    'fec_id_16' => ['icon' => '🎥', 'label' => __('Resolution', 'abp-rentalforge'), 'value' => __('4K / 8K cinematic recording', 'abp-rentalforge')],
                    'fec_id_17' => ['icon' => '🎥', 'label' => __('Resolution', 'abp-rentalforge'), 'value' => __('24 MP', 'abp-rentalforge')],
                    'fec_id_18' => ['icon' => '🎥', 'label' => __('Resolution', 'abp-rentalforge'), 'value' => __('4K UHD', 'abp-rentalforge')],
                    'fec_id_19' => ['icon' => '🔍', 'label' => __('Lens System', 'abp-rentalforge'), 'value' => __('Interchangeable professional lenses', 'abp-rentalforge')],
                    'fec_id_20' => ['icon' => '⚙️', 'label' => __('Autofocus', 'abp-rentalforge'), 'value' => __('Fast AI-based tracking focus system', 'abp-rentalforge')],
                    'fec_id_21' => ['icon' => '🔋', 'label' => __('Battery Life', 'abp-rentalforge'), 'value' => __('Long-lasting rechargeable battery system', 'abp-rentalforge')],
                    'fec_id_22' => ['icon' => 'fas fa-search-plus', 'label' => __('Zoom', 'abp-rentalforge'), 'value' => __('30x Optical', 'abp-rentalforge')],
                    'fec_id_23' => ['icon' => 'fas fa-battery-full', 'label' => __('Battery Life', 'abp-rentalforge'), 'value' => __('500 Shots', 'abp-rentalforge')],
                    'fec_id_24' => ['icon' => 'fas fa-wifi', 'label' => __('Connectivity', 'abp-rentalforge'), 'value' => __('Built-in Wi-Fi/NFC', 'abp-rentalforge')],
                    'fec_id_25' => ['icon' => '🔊', 'label' => __('Sound', 'abp-rentalforge'), 'value' => __('High-power bass boosted speakers', 'abp-rentalforge')],
                    'fec_id_26' => ['icon' => '🔊', 'label' => __('Sound', 'abp-rentalforge'), 'value' => __('1000W', 'abp-rentalforge')],
                    'fec_id_27' => ['icon' => '🎚️', 'label' => __('DJ Control', 'abp-rentalforge'), 'value' => __('Professional mixers & controllers support', 'abp-rentalforge')],
                    'fec_id_28' => ['icon' => '💡', 'label' => __('Lighting', 'abp-rentalforge'), 'value' => __('RGB LED & stage lighting systems', 'abp-rentalforge')],
                    'fec_id_29' => ['icon' => '💡', 'label' => __('Lighting', 'abp-rentalforge'), 'value' => __('LED RGB', 'abp-rentalforge')],
                    'fec_id_30' => ['icon' => '📡', 'label' => __('Connectivity', 'abp-rentalforge'), 'value' => __('Bluetooth / USB / AUX / Wireless sync', 'abp-rentalforge')],
                    'fec_id_31' => ['icon' => '🔌', 'label' => __('Power Efficiency', 'abp-rentalforge'), 'value' => __('Energy-efficient high-output systems', 'abp-rentalforge')],
                    'fec_id_32' => ['icon' => 'fas fa-battery-full', 'label' => __('Battery Life', 'abp-rentalforge'), 'value' => __('Up to 12 Hours', 'abp-rentalforge')],
                    'fec_id_33' => ['icon' => 'fas fa-microphone', 'label' => __('Input', 'abp-rentalforge'), 'value' => __('Wireless Mic Support', 'abp-rentalforge')],
                    'fec_id_34' => ['icon' => 'fas fa-water', 'label' => __('Durability', 'abp-rentalforge'), 'value' => __('Splash Proof (IPX4)', 'abp-rentalforge')],
                    'fec_id_35' => ['icon' => '🚁', 'label' => __('Flight Stability', 'abp-rentalforge'), 'value' => __('GPS-based stable hovering system', 'abp-rentalforge')],
                    'fec_id_36' => ['icon' => '🧠', 'label' => __('AI Features', 'abp-rentalforge'), 'value' => __('Smart tracking & auto flight modes', 'abp-rentalforge')],
                    'fec_id_37' => ['icon' => '🔋', 'label' => __('Battery Range', 'abp-rentalforge'), 'value' => __('Extended flight time with fast charging', 'abp-rentalforge')],
                    'fec_id_38' => ['icon' => '📡', 'label' => __('Range', 'abp-rentalforge'), 'value' => __('15KM', 'abp-rentalforge')],
                    'fec_id_39' => ['icon' => 'fas fa-clock', 'label' => __('Flight Time', 'abp-rentalforge'), 'value' => __('30 Minutes', 'abp-rentalforge')],
                    'fec_id_40' => ['icon' => '📡', 'label' => __('Range', 'abp-rentalforge'), 'value' => __('5 km', 'abp-rentalforge')],
                    'fec_id_41' => ['icon' => 'fas fa-camera', 'label' => __('Camera', 'abp-rentalforge'), 'value' => __('4K / 12MP', 'abp-rentalforge')],
                    'fec_id_42' => ['icon' => 'fas fa-wind', 'label' => __('Wind Resistance', 'abp-rentalforge'), 'value' => __('Level 5', 'abp-rentalforge')],
                    'fec_id_43' => ['icon' => 'fas fa-weight-hanging', 'label' => __('Max Payload', 'abp-rentalforge'), 'value' => __('500g', 'abp-rentalforge')],
                    'fec_id_44' => ['icon' => 'fas fa-clock', 'label' => __('Flight Time', 'abp-rentalforge'), 'value' => __('30-40 Mins', 'abp-rentalforge')],
                    'fec_id_45' => ['icon' => 'fas fa-tachometer-alt', 'label' => __('Max Speed', 'abp-rentalforge'), 'value' => __('70 KM/H', 'abp-rentalforge')],
                    'fec_id_46' => ['icon' => 'fas fa-eye', 'label' => __('Obstacle Avoidance', 'abp-rentalforge'), 'value' => __('Omnidirectional', 'abp-rentalforge')],
                    'fec_id_47' => ['icon' => 'fas fa-weight-hanging', 'label' => __('Takeoff Weight', 'abp-rentalforge'), 'value' => __('249g', 'abp-rentalforge')],
                    'fec_id_48' => ['icon' => '🏝️', 'label' => __('Location', 'abp-rentalforge'), 'value' => __('Beachfront / Mountain / Island premium spots', 'abp-rentalforge')],
                    'fec_id_49' => ['icon' => '🏨', 'label' => __('Luxury Level', 'abp-rentalforge'), 'value' => __('5-star ultra luxury hospitality service', 'abp-rentalforge')],
                    'fec_id_50' => ['icon' => '🍽️', 'label' => __('Dining', 'abp-rentalforge'), 'value' => __('International gourmet food experience', 'abp-rentalforge')],
                    'fec_id_51' => ['icon' => '🧖', 'label' => __('Facilities', 'abp-rentalforge'), 'value' => __('Spa, pool, gym, wellness center', 'abp-rentalforge')],
                    'fec_id_52' => ['icon' => '🌅', 'label' => __('Experience', 'abp-rentalforge'), 'value' => __('Scenic views & premium relaxation environment', 'abp-rentalforge')],
                    'fec_id_53' => ['icon' => 'fas fa-bed', 'label' => __('Room Type', 'abp-rentalforge'), 'value' => __('Deluxe Suite', 'abp-rentalforge')],
                    'fec_id_54' => ['icon' => 'fas fa-swimming-pool', 'label' => __('Pool', 'abp-rentalforge'), 'value' => __('Infinity Pool', 'abp-rentalforge')],
                    'fec_id_55' => ['icon' => 'fas fa-utensils', 'label' => __('Meals', 'abp-rentalforge'), 'value' => __('All Inclusive', 'abp-rentalforge')],
                    'fec_id_56' => ['icon' => 'fas fa-wifi', 'label' => __('WiFi', 'abp-rentalforge'), 'value' => __('Free / High Speed', 'abp-rentalforge')],
                    'fec_id_57' => ['icon' => 'fas fa-concierge-bell', 'label' => __('Service', 'abp-rentalforge'), 'value' => __('24/7 Concierge', 'abp-rentalforge')],
                    'fec_id_58' => ['icon' => 'fas fa-swimming-pool', 'label' => __('Pool', 'abp-rentalforge'), 'value' => __('Private Infinity Pool', 'abp-rentalforge')],
                    'fec_id_59' => ['icon' => 'fas fa-utensils', 'label' => __('Dining', 'abp-rentalforge'), 'value' => __('Fine Dining Included', 'abp-rentalforge')],
                    'fec_id_60' => ['icon' => 'fas fa-spa', 'label' => __('Wellness', 'abp-rentalforge'), 'value' => __('Luxury Spa & Sauna', 'abp-rentalforge')],
                    'fec_id_61' => ['icon' => 'fas fa-mountain', 'label' => __('View', 'abp-rentalforge'), 'value' => __('Ocean Front', 'abp-rentalforge')],
                    'fec_id_62' => ['icon' => '🧳', 'label' => __('Durability', 'abp-rentalforge'), 'value' => __('Shock-resistant strong build quality', 'abp-rentalforge')],
                    'fec_id_63' => ['icon' => '🎒', 'label' => __('Portability', 'abp-rentalforge'), 'value' => __('Lightweight and easy carry design', 'abp-rentalforge')],
                    'fec_id_64' => ['icon' => '🔒', 'label' => __('Security', 'abp-rentalforge'), 'value' => __('TSA lock & anti-theft protection', 'abp-rentalforge')],
                    'fec_id_65' => ['icon' => '💼', 'label' => __('Storage Capacity', 'abp-rentalforge'), 'value' => __('Expandable smart packing space', 'abp-rentalforge')],
                    'fec_id_66' => ['icon' => '⚙️', 'label' => __('Build Material', 'abp-rentalforge'), 'value' => __('Polycarbonate / premium fabric construction', 'abp-rentalforge')],
                    'fec_id_67' => ['icon' => 'fas fa-weight', 'label' => __('Weight', 'abp-rentalforge'), 'value' => __('2.5 kg', 'abp-rentalforge')],
                    'fec_id_68' => ['icon' => 'fas fa-box', 'label' => __('Storage Capacity', 'abp-rentalforge'), 'value' => __('60 Liters', 'abp-rentalforge')],
                    'fec_id_69' => ['icon' => 'fas fa-tint-slash', 'label' => __('Waterproof', 'abp-rentalforge'), 'value' => __('IPX6 Rated', 'abp-rentalforge')],
                    'fec_id_70' => ['icon' => 'fas fa-expand-arrows-alt', 'label' => __('Dimensions', 'abp-rentalforge'), 'value' => __('70×35×25 cm', 'abp-rentalforge')],
                    'fec_id_71' => ['icon' => 'fas fa-lock', 'label' => __('Security Lock', 'abp-rentalforge'), 'value' => __('TSA Approved', 'abp-rentalforge')],
                    'fec_id_72' => ['icon' => 'fas fa-feather', 'label' => __('Weight', 'abp-rentalforge'), 'value' => __('Ultra Lightweight', 'abp-rentalforge')],
                    'fec_id_73' => ['icon' => 'fas fa-shield-virus', 'label' => __('Material', 'abp-rentalforge'), 'value' => __('Waterproof Carbon Fiber', 'abp-rentalforge')],
                    'fec_id_74' => ['icon' => 'fas fa-lock', 'label' => __('Security', 'abp-rentalforge'), 'value' => __('TSA Approved Lock', 'abp-rentalforge')],
                    'fec_id_75' => ['icon' => 'fas fa-expand', 'label' => __('Space', 'abp-rentalforge'), 'value' => __('Expandable Capacity', 'abp-rentalforge')],
                    'fec_id_76' => ['icon' => 'fas fa-compass', 'label' => __('Utility', 'abp-rentalforge'), 'value' => __('Smart Tracking GPS', 'abp-rentalforge')],
                    'fec_id_77' => ['icon' => '🏅', 'label' => __('Performance', 'abp-rentalforge'), 'value' => __('Professional-grade sports efficiency', 'abp-rentalforge')],
                    'fec_id_78' => ['icon' => '⚽', 'label' => __('Multi-sport Use', 'abp-rentalforge'), 'value' => __('Suitable for multiple sports categories', 'abp-rentalforge')],
                    'fec_id_79' => ['icon' => '🛡️', 'label' => __('Safety', 'abp-rentalforge'), 'value' => __('Injury protection & ergonomic design', 'abp-rentalforge')],
                    'fec_id_80' => ['icon' => '🏋️', 'label' => __('Strength Build', 'abp-rentalforge'), 'value' => __('High durability & impact resistance', 'abp-rentalforge')],
                    'fec_id_81' => ['icon' => '🎯', 'label' => __('Precision', 'abp-rentalforge'), 'value' => __('Accurate control & performance tuning', 'abp-rentalforge')],
                    'fec_id_82' => ['icon' => 'fas fa-dumbbell', 'label' => __('Equipment Type', 'abp-rentalforge'), 'value' => __('Multi-Use', 'abp-rentalforge')],
                    'fec_id_83' => ['icon' => 'fas fa-weight-hanging', 'label' => __('Max Load', 'abp-rentalforge'), 'value' => __('150 kg', 'abp-rentalforge')],
                    'fec_id_84' => ['icon' => 'fas fa-ruler', 'label' => __('Size', 'abp-rentalforge'), 'value' => __('Adjustable', 'abp-rentalforge')],
                    'fec_id_85' => ['icon' => 'fas fa-tools', 'label' => __('Material', 'abp-rentalforge'), 'value' => __('Steel Alloy', 'abp-rentalforge')],
                    'fec_id_86' => ['icon' => '🏅', 'label' => __('Skill Level', 'abp-rentalforge'), 'value' => __('All Levels', 'abp-rentalforge')],
                    'fec_id_87' => ['icon' => 'fas fa-running', 'label' => __('Performance', 'abp-rentalforge'), 'value' => __('Pro-Grade Grip', 'abp-rentalforge')],
                    'fec_id_88' => ['icon' => 'fas fa-heartbeat', 'label' => __('Tracking', 'abp-rentalforge'), 'value' => __('Built-in Heart Sensors', 'abp-rentalforge')],
                    'fec_id_89' => ['icon' => 'fas fa-dumbbell', 'label' => __('Strength', 'abp-rentalforge'), 'value' => __('Heavy-Duty Build', 'abp-rentalforge')],
                    'fec_id_90' => ['icon' => 'fas fa-wind', 'label' => __('Aerodynamics', 'abp-rentalforge'), 'value' => __('High Speed Optimized', 'abp-rentalforge')],
                    'fec_id_91' => ['icon' => 'fas fa-check-circle', 'label' => __('Certification', 'abp-rentalforge'), 'value' => __('FIFA/Olympic Standard', 'abp-rentalforge')],
                ];
            }
            public static function static_category(): array {
                return [
                    'transport' => ['name' => __('Transport', 'abp-rentalforge')],
                    'camera' => ['name' => __('Camera', 'abp-rentalforge')],
                    'party' => ['name' => __('Party Equipment', 'abp-rentalforge')],
                    'drone' => ['name' => __('Drone', 'abp-rentalforge')],
                    'resort' => ['name' => __('Resort', 'abp-rentalforge')],
                    'travel' => ['name' => __('Travel Equipment', 'abp-rentalforge')],
                    'sport' => ['name' => __('Sport Equipment', 'abp-rentalforge')],
                ];
            }
            public static function static_location(): array {
                return [
                    0 => ['name' => __('Washington, D.C', 'abp-rentalforge')],
                    1 => ['name' => __('New York City', 'abp-rentalforge')],
                    2 => ['name' => __('California', 'abp-rentalforge')],
                    3 => ['name' => __('Los Angeles', 'abp-rentalforge')],
                    4 => ['name' => __('Chicago', 'abp-rentalforge')],
                    5 => ['name' => __('San Francisco', 'abp-rentalforge')],
                ];
            }
            public static function static_brand(): array {
                return [
                    0 => ['name' => __('Mercedes-Benz', 'abp-rentalforge')],
                    1 => ['name' => __('Toyota Motor', 'abp-rentalforge')],
                    2 => ['name' => __('Tesla', 'abp-rentalforge')],
                    3 => ['name' => __('BMW', 'abp-rentalforge')],
                    4 => ['name' => __('Canon', 'abp-rentalforge')],
                    5 => ['name' => __('Nikon', 'abp-rentalforge')],
                    6 => ['name' => __('Sony', 'abp-rentalforge')],
                    7 => ['name' => __('Fujifilm', 'abp-rentalforge')],
                    8 => ['name' => __('Pioneer DJ', 'abp-rentalforge')],
                    9 => ['name' => __('Yamaha', 'abp-rentalforge')],
                    10 => ['name' => __('DJI', 'abp-rentalforge')],
                    11 => ['name' => __('Autel Robotics', 'abp-rentalforge')],
                    12 => ['name' => __('Marriott International', 'abp-rentalforge')],
                    13 => ['name' => __('Hilton Worldwide', 'abp-rentalforge')],
                    14 => ['name' => __('American Tourister', 'abp-rentalforge')],
                    15 => ['name' => __('Samsonite International', 'abp-rentalforge')],
                    16 => ['name' => __('Nike', 'abp-rentalforge')],
                    17 => ['name' => __('Adidas', 'abp-rentalforge')],
                    18 => ['name' => __('Puma', 'abp-rentalforge')],
                ];
            }
            public static function static_faq(): array {
                return [
                    1 => [
                        'title' => __('How do I book a rental?', 'abp-rentalforge'),
                        'des' => __('You can book a rental by selecting your preferred property or equipment, rental duration, available dates, and required options. Complete the checkout process to confirm your rental.', 'abp-rentalforge'),
                    ],
                    2 => [
                        'title' => __('Can I cancel my rental booking?', 'abp-rentalforge'),
                        'des' => __('Yes, you can cancel your rental according to the cancellation policy configured by the rental provider. Refund eligibility may vary depending on the booking conditions.', 'abp-rentalforge'),
                    ],
                    3 => [
                        'title' => __('Can I change my rental dates?', 'abp-rentalforge'),
                        'des' => __('Rental dates can be changed if the provider allows date modifications and the selected property or equipment is available for the new rental period.', 'abp-rentalforge'),
                    ],
                    4 => [
                        'title' => __('What rental durations are available?', 'abp-rentalforge'),
                        'des' => __('Available rental durations depend on the rental item. Depending on the configuration, you may be able to rent an item hourly, daily, monthly, or for a custom rental period.', 'abp-rentalforge'),
                    ],
                    5 => [
                        'title' => __('Will I receive a rental confirmation?', 'abp-rentalforge'),
                        'des' => __('Yes, a rental confirmation will be provided after a successful booking and payment. Your rental details can also be reviewed from your account dashboard.', 'abp-rentalforge'),
                    ],
                    6 => [
                        'title' => __('What payment methods are supported?', 'abp-rentalforge'),
                        'des' => __('Available payment methods depend on the payment gateways configured by the website administrator or rental provider.', 'abp-rentalforge'),
                    ],
                    7 => [
                        'title' => __('Is a security deposit required?', 'abp-rentalforge'),
                        'des' => __('A security deposit may be required depending on the rental item and the provider’s configuration. Any applicable deposit amount will be shown during the booking process.', 'abp-rentalforge'),
                    ],
                    8 => [
                        'title' => __('What happens if the rental item is unavailable?', 'abp-rentalforge'),
                        'des' => __('Rental items are subject to availability for the selected dates and duration. If an item is unavailable, you may need to select another date, location, or available rental item.', 'abp-rentalforge'),
                    ],
                    9 => [
                        'title' => __('What happens if the rental item is damaged?', 'abp-rentalforge'),
                        'des' => __('Customers are responsible for taking reasonable care of rented items. Damage or loss may result in additional charges according to the rental provider’s damage and liability policy.', 'abp-rentalforge'),
                    ],
                    10 => [
                        'title' => __('Who should I contact for rental assistance?', 'abp-rentalforge'),
                        'des' => __('If you need help with your rental, booking, payment, pickup, return, or other rental-related issues, please contact the rental provider using the contact information provided on the website.', 'abp-rentalforge'),
                    ],
                ];
            }
            public static function static_tc(): false|string {
                ob_start(); ?>
                <h6>1. <?php esc_html_e('Acceptance of Terms', 'abp-rentalforge'); ?></h6>
                <?php esc_html_e('By using our rental service, you agree to comply with these terms and conditions. If you do not agree with any part of these terms, please do not use this service.', 'abp-rentalforge'); ?>
                <h6>2. <?php esc_html_e('Rental Booking', 'abp-rentalforge'); ?></h6>
                <?php esc_html_e('A rental booking is considered confirmed only after successful completion of the booking process and payment, where applicable. Customers are responsible for providing accurate information during the booking process.', 'abp-rentalforge'); ?>
                <h6>3. <?php esc_html_e('Customer Information', 'abp-rentalforge'); ?></h6>
                <?php esc_html_e('Customers must provide accurate and valid information, including their name, contact details, billing information, and any other information required to complete the rental. Incorrect or misleading information may result in booking cancellation.', 'abp-rentalforge'); ?>
                <h6>4. <?php esc_html_e('Rental Availability', 'abp-rentalforge'); ?></h6>
                <?php esc_html_e('All rental bookings are subject to availability. Availability may vary depending on the selected rental item, location, rental period, quantity, and other booking conditions.', 'abp-rentalforge'); ?>
                <h6>5. <?php esc_html_e('Rental Period', 'abp-rentalforge'); ?></h6>
                <?php esc_html_e('The rental period begins and ends according to the dates and times specified during the booking process. Customers are responsible for returning or completing the rental within the agreed rental period.', 'abp-rentalforge'); ?>
                <h6>6. <?php esc_html_e('Pickup and Return', 'abp-rentalforge'); ?></h6>
                <?php esc_html_e('Customers must follow the pickup and return instructions provided by the rental provider. Late pickup or return may result in additional charges according to the provider’s rental policy.', 'abp-rentalforge'); ?>
                <h6>7. <?php esc_html_e('Cancellation and Refund Policy', 'abp-rentalforge'); ?></h6>
                <?php esc_html_e('Cancellation and refund eligibility may vary depending on the rental provider’s policy. Applicable processing fees, service charges, or other non-refundable amounts may be deducted from the refund.', 'abp-rentalforge'); ?>
                <h6>8. <?php esc_html_e('Rental Charges and Payments', 'abp-rentalforge'); ?></h6>
                <?php esc_html_e('Customers are responsible for paying all applicable rental charges, taxes, fees, deposits, and other costs displayed during the booking process. The final amount may depend on the rental duration, quantity, location, and selected options.', 'abp-rentalforge'); ?>
                <h6>9. <?php esc_html_e('Security Deposit', 'abp-rentalforge'); ?></h6>
                <?php esc_html_e('A security deposit may be required for certain rental items. The deposit may be refunded after the rental period if the item is returned according to the agreed conditions and no applicable damage, loss, or additional charges are identified.', 'abp-rentalforge'); ?>
                <h6>10. <?php esc_html_e('Customer Responsibility', 'abp-rentalforge'); ?></h6>
                <?php esc_html_e('Customers are responsible for using rented properties or equipment properly and in accordance with the instructions provided by the rental provider. Customers must take reasonable care of all rented items during the rental period.', 'abp-rentalforge'); ?>
                <h6>11. <?php esc_html_e('Damage or Loss', 'abp-rentalforge'); ?></h6>
                <?php esc_html_e('Customers may be responsible for costs associated with damage, loss, theft, or misuse of rented items during the rental period. Any applicable charges will be handled according to the rental provider’s policies.', 'abp-rentalforge'); ?>
                <h6>12. <?php esc_html_e('Property and Equipment Usage', 'abp-rentalforge'); ?></h6>
                <?php esc_html_e('Rented properties and equipment must only be used for their intended purpose and in accordance with applicable rules, safety requirements, and instructions provided by the rental provider.', 'abp-rentalforge'); ?>
                <h6>13. <?php esc_html_e('Late Return', 'abp-rentalforge'); ?></h6>
                <?php esc_html_e('Returning a rental item after the agreed rental period may result in additional rental charges or late fees. The applicable charges will depend on the rental provider’s configuration and policies.', 'abp-rentalforge'); ?>
                <h6>14. <?php esc_html_e('Availability and Substitution', 'abp-rentalforge'); ?></h6>
                <?php esc_html_e('In exceptional circumstances, the rental provider may be unable to provide the originally selected item or property. Where necessary, the provider may offer an alternative item or arrangement subject to availability.', 'abp-rentalforge'); ?>
                <h6>15. <?php esc_html_e('Limitation of Liability', 'abp-rentalforge'); ?></h6>
                <?php esc_html_e('The rental provider shall not be held responsible for delays, service interruptions, equipment failure, property issues, natural events, or other circumstances beyond reasonable control.', 'abp-rentalforge'); ?>
                <h6>16. <?php esc_html_e('Privacy Policy', 'abp-rentalforge'); ?></h6>
                <?php esc_html_e('Personal information collected during the rental process will be used to process bookings, provide rental services, communicate with customers, and improve the service in accordance with the applicable privacy policy.', 'abp-rentalforge'); ?>
                <h6>17. <?php esc_html_e('Changes to Terms', 'abp-rentalforge'); ?></h6>
                <?php esc_html_e('These terms and conditions may be updated from time to time without prior notice. Continued use of the rental service after changes are published indicates acceptance of the revised terms and conditions.', 'abp-rentalforge'); ?>
                <h6>18. <?php esc_html_e('Contact Information', 'abp-rentalforge'); ?></h6>
                <?php esc_html_e('If you have any questions regarding these terms and conditions, rental bookings, payments, cancellations, or other rental-related matters, please contact the rental provider directly.', 'abp-rentalforge'); ?>
                <?php return ob_get_clean();
            }
        }
    }