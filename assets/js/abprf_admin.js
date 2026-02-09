//==========configuration=================//
(function ($) {
    "use strict";
    //==========WooCommerce configuration=================//
    $(document).on('click', 'button.abprf_install_and_active_wc', function () {
        let parent = $(this).closest('.abprf_tools');
        $.ajax({
            type: 'POST', url: abprf_admin_ajax.ajax_url, data: {
                "action": "abprf_install_and_active_wc", 'nonce': abprf_admin_ajax.nonce
            }, beforeSend: function () {
                abprf_spinner(parent);
            }, success: function () {
                window.location.href = window.location.href.replace('admin.php?page=tools_info', 'edit.php?post_type=abprf_post&page=tools_info');
            }
        });
    });
    $(document).on('click', 'button.rf_active_wc', function () {
        let parent = $(this).closest('.abprf_tools');
        $.ajax({
            type: 'POST', url: abprf_admin_ajax.ajax_url, data: {
                "action": "rf_active_wc", 'nonce': abprf_admin_ajax.nonce
            }, beforeSend: function () {
                abprf_spinner(parent);
            }, success: function () {
                window.location.href = window.location.href.replace('admin.php?page=tools_info', 'edit.php?post_type=abprf_post&page=tools_info');
            }
        });
    });
    //==========page create=================//
    $(document).on('click', 'button.abprf_create_transport_search_page', function () {
        let parent = $(this).closest('.abprf_tools');
        $.ajax({
            type: 'POST', url: abprf_admin_ajax.ajax_url, data: {
                "action": "abprf_create_transport_search_page", 'nonce': abprf_admin_ajax.nonce
            }, beforeSend: function () {
                abprf_spinner(parent);
            }, success: function () {
                window.location.reload();
            }
        });
    });
    $(document).on('click', 'button.abprf_create_search_result_page', function () {
        let parent = $(this).closest('.abprf_tools');
        $.ajax({
            type: 'POST', url: abprf_admin_ajax.ajax_url, data: {
                "action": "abprf_create_search_result_page", 'nonce': abprf_admin_ajax.nonce
            }, beforeSend: function () {
                abprf_spinner(parent);
            }, success: function () {
                window.location.reload();
            }
        });
    });
    //==========Dummy data configuration=================//
    $(document).on('click', 'button.abprf_import_bus', function () {
        let parent = $(this).closest('.abprf_tools');
        $.ajax({
            type: 'POST', url: abprf_admin_ajax.ajax_url, data: {
                "action": "abprf_import_bus", 'nonce': abprf_admin_ajax.nonce
            }, beforeSend: function () {
                abprf_spinner(parent);
            }, success: function () {
                window.location.reload();
            }
        });
    });
    //==========Additional service =================//
    $(document).on('click', 'button.abprf_import_additional_service', function () {
        let parent = $(this).closest('.additional_configuration');
        let target = parent.find('.abprf_additional_content');
        $.ajax({
            type: 'POST', url: abprf_admin_ajax.ajax_url, data: {
                "action": "abprf_import_additional_service", 'nonce': abprf_admin_ajax.nonce
            }, beforeSend: function () {
                abprf_spinner(target);
            }, success: function (data) {
                target.html(data);
            }
        });
    });
}(jQuery));
