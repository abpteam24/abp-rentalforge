let abprf_feature_data = JSON.parse(abprf_admin_data.feature_data);
let abprf_related_info = JSON.parse(abprf_admin_data.related_info);
function abprf_save_data(form_area, target, action) {
    let body = jQuery('body .rf_post_config');
    let formData = new FormData();
    form_area.find('input, select, textarea').each(function () {
        let name = jQuery(this).attr('name');
        let value = jQuery(this).val();
        if (name) {
            if (jQuery(this).attr('type') === 'checkbox' || jQuery(this).attr('type') === 'radio') {
                if (jQuery(this).is(':checked')) {
                    formData.append(name, value);
                }
            } else {
                formData.append(name, value);
            }
        }
    });
    if (action === 'abprf_save_property') {
        formData.append('filter_args', JSON.stringify(abprf_property_filter_arg(form_area)));
    }
    if (action === 'abprf_save_feature' || action === 'abprf_save_brand') {
        let property_id = form_area.find('input[name="property_id"]').val();
        formData.append('property_id', property_id);
        if (form_area.closest(".data_property").length > 0) {
            formData.append('property_add', 1);
        } else {
            formData.append('property_add', 0);
        }
    }
    if (body.find("[name='abprf_post_id']").length > 0) {
        formData.append('abprf_post_id', body.find("[name='abprf_post_id']").val());
    }
    formData.append('action', action);
    formData.append('nonce', abprf_admin_data.nonce);
    jQuery.ajax({
        type: 'POST', url: abprf_admin_data.ajax_url, contentType: false, processData: false, data: formData,
        beforeSend: function () {
            abprf_spinner(form_area);
            abprf_toast_msg(abprf_admin_data.msg.saving);
        },
        success: function (response) {
            abprf_spinner_remove(form_area);
            abprf_toast_msg(response.data.msg, 'success');
            if (target && target.length > 0) {
                target.html(response.data.html);
                abprf_load_image(target);
            }
            if (action === 'abprf_save_feature' && response.data && response.data.hasOwnProperty('feature_js')) {
                abprf_feature_data = response.data.feature_js;
                new ABPRF_FeatureSelection('div.abprf_admin .property_feature', abprf_feature_data);
            }
            if (action === 'abprf_save_feature' || action === 'abprf_save_brand') {
                form_area.find('.insertable_area').html('');
                form_area.find('.hide_on_load').slideUp('fast');
            } else {
                abprf_popup_close('#abprf_global_popup');
            }
        }
    });
}
function abprf_property_filter_arg($this) {
    let parent = $this.closest('.abprf_admin');
    let filter_args = {};
    if (parent.find("[name='abprf_post_id']").length > 0) {
        filter_args['post_id'] = parent.find("[name='abprf_post_id']").val();
    } else {
        if (parent.find("[name='select_property_hidden']").length > 0) {
            filter_args['post_id'] = parent.find("[name='select_property_hidden']").val();
        } else {
            filter_args['post_id'] = parent.find(".data_property [name='post_id']").val();
        }
    }
    if (parent.find(".properties_list [data-page].rf_active").length > 0) {
        filter_args['page_number'] = parent.find(".properties_list [data-page].rf_active").attr('data-page');
    } else {
        filter_args['page_number'] = 1;
    }
    if (parent.find("[name='page_item']").length > 0) {
        filter_args['page_item'] = parseInt(parent.find("[name='page_item']").val());
    }
    return filter_args;
}
//==========Post=================//
(function ($) {
    "use strict";
    $(document).on("click", "div.abprf_admin button.post_permanent_remove", function () {
        let post_id = $(this).attr('data-post_id');
        let parent = $(this).closest('.abprf_posts');
        $.ajax({
            type: 'POST', url: abprf_admin_data.ajax_url, data: {
                "action": "abprf_post_permanent_remove", 'post_id': post_id, 'nonce': abprf_admin_data.nonce
            }, beforeSend: function () {
                abprf_spinner(parent);
                abprf_toast_msg(abprf_admin_data.msg.post_deleting, 'error');
            }, success: function (response) {
                abprf_spinner_remove(parent);
                abprf_toast_msg(response.data.msg, 'warn');
                window.location.reload();
            }
        })
    });
    $(document).on("click", "div.abprf_admin button.post_move_trash", function () {
        let post_id = $(this).attr('data-post_id');
        let parent = $(this).closest('.abprf_posts');
        $.ajax({
            type: 'POST', url: abprf_admin_data.ajax_url, data: {
                "action": "abprf_post_move_trash", 'post_id': post_id, 'nonce': abprf_admin_data.nonce
            }, beforeSend: function () {
                abprf_spinner(parent);
                abprf_toast_msg(abprf_admin_data.msg.post_trashing, 'error');
            }, success: function (response) {
                abprf_spinner_remove(parent);
                abprf_toast_msg(response.data.msg, 'warn');
                window.location.reload();
            }
        })
    });
    $(document).on("click", "div.abprf_admin button.post_restore", function () {
        let post_id = $(this).attr('data-post_id');
        let parent = $(this).closest('.abprf_posts');
        $.ajax({
            type: 'POST', url: abprf_admin_data.ajax_url, data: {
                "action": "abprf_post_restore", 'post_id': post_id, 'nonce': abprf_admin_data.nonce
            }, beforeSend: function () {
                abprf_spinner(parent);
                abprf_toast_msg(abprf_admin_data.msg.post_restoring, 'info');
            }, success: function (response) {
                abprf_spinner_remove(parent);
                abprf_toast_msg(response.data.msg, 'success');
                window.location.reload();
            }
        })
    });
    $(document).on('click', 'div.abprf_admin .post_list .pagination_area button[data-page]', function () {
        let $this = $(this);
        if (!$this.hasClass('rf_active')) {
            let parent = $(this).closest('.abprf_posts');
            let filter_args = {};
            if (parent.find("[name='select_hidden_post_status']").length > 0) {
                filter_args['status'] = parent.find("[name='select_hidden_post_status']").val();
            }
            filter_args['page_number'] = parseInt($this.attr('data-page'));
            if (parent.find("[name='page_item']").length > 0) {
                filter_args['page_item'] = parseInt(parent.find("[name='page_item']").val());
            }
            abprf_load_post_list(parent, filter_args);
        }
    });
    function abprf_load_post_list(parent, filter_args) {
        let target = parent.find('.post_list');
        if (target.length > 0) {
            $.ajax({
                type: 'POST', url: abprf_admin_data.ajax_url, data: {
                    "action": "abprf_reload_post_list", "filter_args": filter_args, 'nonce': abprf_admin_data.nonce
                }, beforeSend: function () {
                    abprf_spinner(parent);
                    abprf_toast_msg(abprf_admin_data.msg.post_loading);
                }, success: function (response) {
                    target.html(response.data.html);
                    abprf_spinner_remove(parent);
                    abprf_toast_msg(response.data.html, 'success');
                }
            });
        } else {
            parent.find('.post_tab').trigger('click');
        }
    }
}(jQuery));
//==========Properties=================//
(function ($) {
    "use strict";
    $(document).on('click', 'div.abprf_admin button.save_property', function (e) {
        e.preventDefault();
        let $this = $(this);
        let target = $('div.abprf_admin .properties_list');
        let form_area = $this.closest('.data_property');
        abprf_save_data(form_area, target, 'abprf_save_property');
    });
    $(document).on("rf_trigger", "div.abprf_admin input[name='select_property_hidden']", function () {
        let parent = $(this).closest('.abprf_admin');
        let filter_args = abprf_property_filter_arg($(this));
        filter_args['post_id'] = $(this).val();
        filter_args['page_number'] = 1;
        abprf_load_property_list(parent, filter_args);
    });
    $(document).on('click', 'div.abprf_admin .properties_list .pagination_area button[data-page]', function () {
        let $this = $(this);
        if (!$this.hasClass('rf_active')) {
            let parent = $this.closest('.abprf_admin');
            let filter_args = abprf_property_filter_arg($this);
            filter_args['page_number'] = parseInt($this.attr('data-page'));
            abprf_load_property_list(parent, filter_args);
        }
    });
    $(document).on('click', 'div.abprf_admin button.delete_property', function () {
        if (confirm(abprf_admin_data.msg.confirm_delete + ' \n\n' + abprf_admin_data.msg.confirm_ok + ' \n ' + abprf_admin_data.msg.confirm_cancel)) {
            let parent = $(this).closest('.abprf_admin');
            let target = parent.find('.properties_list');
            let property_id = $(this).attr('data-property_id');
            property_id = (typeof property_id !== 'undefined' && property_id !== false) ? parseInt(property_id) : '';
            let filter_args = abprf_property_filter_arg($(this));
            $.ajax({
                type: 'POST', url: abprf_admin_data.ajax_url, data: {
                    "action": "abprf_property_delete", 'property_id': property_id, 'filter_args': filter_args, 'nonce': abprf_admin_data.nonce
                }, beforeSend: function () {
                    abprf_spinner(target);
                    abprf_toast_msg(abprf_admin_data.msg.deleting, 'error');
                }, success: function (response) {
                    abprf_toast_msg(response.data.msg);
                    target.html(response.data.html);
                }
            });
        }
    });
    function abprf_load_property_list(parent, filter_args) {
        let target = parent.find('.properties_list');
        if (target.length > 0) {
            $.ajax({
                type: 'POST', url: abprf_admin_data.ajax_url, data: {
                    "action": "abprf_reload_property_list", "filter_args": filter_args, 'nonce': abprf_admin_data.nonce
                }, beforeSend: function () {
                    abprf_spinner(target);
                    abprf_toast_msg(abprf_admin_data.msg.property_loading);
                }, success: function (response) {
                    target.html(response.data.html);
                    abprf_toast_msg(response.data.msg, 'success');
                }
            });
        } else {
            parent.find('.properties_tab').trigger('click');
        }
    }
}(jQuery));
//==========Orders=================//
(function ($) {
    "use strict";
    $(document).on('submit', 'div.abprf_admin form.load_order_list', function (e) {
        e.preventDefault();
        let parent = $(this).closest('.abprf_orders');
        let target = parent.find('.order_list');
        let formData = new FormData(this);
        if (parent.find('[data-page].rf_active').length > 0) {
            formData.append('page_number', parseInt(parent.find('[data-page].rf_active').attr('data-page')));
        }
        formData.append('page_item', parseInt(parent.find("[name='page_item']").val()));
        formData.append('status', parent.find('.order_status_menu [data-status].rf_active').attr('data-status'));
        formData.append('action', 'abprf_load_order_list');
        formData.append('nonce', abprf_admin_data.nonce);
        $.ajax({
            type: 'POST', url: abprf_admin_data.ajax_url, contentType: false, processData: false, data: formData,
            beforeSend: function () {
                abprf_spinner(parent);
                abprf_toast_msg(abprf_admin_data.msg.order_loading);
            },
            success: function (response) {
                abprf_spinner_remove(parent);
                target.html(response.data.html);
                abprf_toast_msg(response.data.msg, 'success');
                abprf_load_more();
            }
        });
    });
    $(document).on('click', 'div.abprf_admin .order_status_menu button[data-status]', function () {
        let $this = $(this);
        if (!$this.hasClass('rf_active')) {
            $this.closest('.order_status_menu').find('[data-status].rf_active').removeClass('rf_active').promise().done(function () {
                $this.addClass('rf_active').promise().done(function () {
                    $this.closest('.abprf_orders').find('form.load_order_list').submit();
                });
            });
        }
    });
    $(document).on('click', 'div.abprf_admin button.abprf_item_cancel', function () {
        let $this = $(this);
        let parent = $(this).closest('.abprf_orders');
        let item_id = $this.attr('data-item_id');
        if (confirm(abprf_admin_data.msg.confirm_delete + ' \n\n' + abprf_admin_data.msg.confirm_ok + ' \n ' + abprf_admin_data.msg.confirm_cancel)) {
            $.ajax({
                type: 'POST', url: abprf_admin_data.ajax_url, data: {
                    "action": "abprf_item_cancel", 'item_id': item_id, 'nonce': abprf_admin_data.nonce
                }, beforeSend: function () {
                    abprf_spinner(parent);
                    abprf_toast_msg(abprf_admin_data.msg.deleting, 'error');
                }, success: function (response) {
                    abprf_spinner_remove(parent);
                    abprf_toast_msg(response.data.msg);
                    $this.closest('.abprf_orders').find('form.load_order_list').submit();
                }
            });
        }
    });
    $(document).on('click', 'div.abprf_admin .order_list .pagination_area button[data-page]', function () {
        let $this = $(this);
        if (!$this.hasClass('rf_active')) {
            let parent = $(this).closest('.order_list');
            parent.find('[data-page].rf_active').removeClass('rf_active').promise().done(function () {
                $this.addClass('rf_active').promise().done(function () {
                    $this.closest('.abprf_orders').find('form.load_order_list').submit();
                });
            });
        }
    });
}(jQuery));
//==========Date/Additional/Client Form/Faq/Status configuration=================//
(function ($) {
    "use strict";
    $(document).on("rf_trigger", "div.abprf_admin [data-popup='#abprf_global_popup'] .popup_close", function () {
        $(this).closest('.abprf_popup').find('.popup_body').html('');
    });
    $(document).on("rf_trigger", "div.abprf_admin [data-target-popup='#abprf_global_popup']", function () {
        let tax_id = $(this).attr('data-id');
        let type = $(this).attr('data-type');
        let action = '';
        if (type === 'category') {
            action = 'abprf_add_category';
        } else if (type === 'location') {
            action = 'abprf_add_location';
        } else if (type === 'property') {
            action = 'abprf_add_property';
        }
        if (action) {
            tax_id = (typeof tax_id !== 'undefined' && tax_id !== false) ? parseInt(tax_id) : '';
            let target_id = $(this).attr('data-active-popup', '').data('target-popup');
            let parent = $('body').find('[data-popup="' + target_id + '"]').find('.popup_area');
            let target = parent.find('.popup_body');
            let property_copy = +$(this).hasClass('property_copy');
            let post_id = $('body').find("[name='abprf_post_id']").val();
            $.ajax({
                type: 'POST', url: abprf_admin_data.ajax_url, data: {
                    "action": action, 'tax_id': tax_id, 'property_copy': property_copy, 'post_id': post_id, 'nonce': abprf_admin_data.nonce
                }, beforeSend: function () {
                    abprf_spinner(parent);
                    abprf_toast_msg(abprf_admin_data.msg.loading);
                }, success: function (response) {
                    abprf_spinner_remove(parent);
                    target.html(response.data.html).promise().done(function () {
                        abprf_toast_msg(response.data.msg, 'success');
                        abprf_load_more();
                        abprf_load_datepicker(target);
                        if (type === 'property') {
                            new ABPRF_FeatureSelection('div.abprf_admin .property_feature', abprf_feature_data);
                        }
                    });
                }
            })
        }
    });
    //==========Date configuration=================//
    $(document).on('submit', 'div.abprf_admin form.save_dates', function (e) {
        e.preventDefault();
        let parent = $(this).closest('.abprf_admin');
        let target = parent.find('.dashboard_content');
        let formData = new FormData(this);
        formData.append('action', 'abprf_save_dates');
        formData.append('nonce', abprf_admin_data.nonce);
        $.ajax({
            type: 'POST', url: abprf_admin_data.ajax_url, contentType: false, processData: false, data: formData,
            beforeSend: function () {
                abprf_spinner(target);
                abprf_toast_msg(abprf_admin_data.msg.saving);
            },
            success: function (response) {
                abprf_toast_msg(response.data.msg, 'success');
                window.location.reload();
            }
        });
    });
//==========Additional configuration=================//
    $(document).on('submit', 'div.abprf_admin form.save_additional_service', function (e) {
        e.preventDefault();
        let parent = $(this).closest('.abprf_admin');
        let target = parent.find('.dashboard_content');
        let formData = new FormData(this);
        formData.append('action', 'abprf_save_additional_service');
        formData.append('nonce', abprf_admin_data.nonce);
        $.ajax({
            type: 'POST', url: abprf_admin_data.ajax_url, contentType: false, processData: false, data: formData,
            beforeSend: function () {
                abprf_spinner(target);
                abprf_toast_msg(abprf_admin_data.msg.saving);
            },
            success: function (response) {
                abprf_toast_msg(response.data.msg, 'success');
                window.location.reload();
            }
        });
    });
    $(document).on('click', 'div.abprf_admin button.import_additional', function () {
        let parent = $(this).closest('.additional_configuration');
        let target = parent.find('.additional_content');
        $.ajax({
            type: 'POST', url: abprf_admin_data.ajax_url, data: {
                "action": "abprf_import_additional", 'nonce': abprf_admin_data.nonce
            }, beforeSend: function () {
                abprf_spinner(target);
                abprf_toast_msg(abprf_admin_data.msg.importing);
            }, success: function (response) {
                target.html(response.data.html).promise().done(function () {
                    target.find('.sortable_area').sortable({
                        handle: jQuery(this).find('.sortable_handle')
                    });
                    abprf_toast_msg(response.data.msg, 'success');
                });
            }
        });
    });
//==========Client Form configuration=================//
    $(document).on('submit', 'div.abprf_admin form.save_client_form', function (e) {
        e.preventDefault();
        let parent = $(this).closest('.abprf_admin');
        let target = parent.find('.dashboard_content');
        let formData = new FormData(this);
        formData.append('action', 'abprf_save_client_form');
        formData.append('nonce', abprf_admin_data.nonce);
        $.ajax({
            type: 'POST', url: abprf_admin_data.ajax_url, contentType: false, processData: false, data: formData,
            beforeSend: function () {
                abprf_spinner(target);
                abprf_toast_msg(abprf_admin_data.msg.saving);
            },
            success: function (response) {
                abprf_toast_msg(response.data.msg, 'success');
                window.location.reload();
            }
        });
    });
    $(document).on('click', 'div.abprf_admin button.import_global_form', function () {
        let parent = $(this).closest('.abprf_client_form');
        let target = parent.find('.client_form_content');
        $.ajax({
            type: 'POST', url: abprf_admin_data.ajax_url, data: {
                "action": "abprf_import_global_form", 'nonce': abprf_admin_data.nonce
            }, beforeSend: function () {
                abprf_spinner(target);
                abprf_toast_msg(abprf_admin_data.msg.importing);
            }, success: function (response) {
                target.html(response.data.html).promise().done(function () {
                    target.find('.sortable_area').sortable({
                        handle: jQuery(this).find('.sortable_handle')
                    });
                    abprf_toast_msg(response.data.msg, 'success');
                });
            }
        });
    });
    //==========Taxonomy configuration=================//
    $(document).on('click', 'div.abprf_admin button.save_category', function (e) {
        e.preventDefault();
        let $this = $(this);
        let body = jQuery('body .rf_post_config');
        let target = (body.find("[name='abprf_post_id']").length > 0) ? body.find('.category_selection') : $('div.abprf_admin .category_list');
        let form_area = $this.closest('.popup_body');
        abprf_save_data(form_area, target, 'abprf_save_category');
    });
    $(document).on('click', 'div.abprf_admin button.delete_category', function (e) {
        e.preventDefault();
        let parent = $(this).closest('.category_list');
        let cat_id = parseInt($(this).attr('data-cat_id'));
        $.ajax({
            type: 'POST', url: abprf_admin_data.ajax_url, data: {
                "action": "abprf_delete_category", "cat_id": cat_id, 'nonce': abprf_admin_data.nonce
            },
            beforeSend: function () {
                abprf_spinner(parent);
                abprf_toast_msg(abprf_admin_data.msg.deleting, 'error');
            },
            success: function (response) {
                abprf_spinner_remove(parent);
                abprf_toast_msg(response.data.msg);
                parent.html(response.data.html);
            }
        });
    });
    $(document).on('click', 'div.abprf_admin button.save_location', function (e) {
        e.preventDefault();
        let $this = $(this);
        let body = jQuery('body .rf_post_config');
        let target = (body.find("[name='abprf_post_id']").length > 0) ? body.find('.location_selection') : $('div.abprf_admin .location_list');
        let form_area = $this.closest('.popup_body');
        abprf_save_data(form_area, target, 'abprf_save_location');
    });
    $(document).on('click', 'div.abprf_admin button.delete_location', function (e) {
        e.preventDefault();
        let parent = $(this).closest('.location_list');
        let loc_id = parseInt($(this).attr('data-loc_id'));
        $.ajax({
            type: 'POST', url: abprf_admin_data.ajax_url, data: {
                "action": "abprf_delete_location", "loc_id": loc_id, 'nonce': abprf_admin_data.nonce
            },
            beforeSend: function () {
                abprf_spinner(parent);
                abprf_toast_msg(abprf_admin_data.msg.deleting, 'error');
            },
            success: function (response) {
                abprf_spinner_remove(parent);
                abprf_toast_msg(response.data.msg);
                parent.html(response.data.html);
            }
        });
    });
    $(document).on('click', 'div.abprf_admin button.save_brand', function (e) {
        e.preventDefault();
        let $this = $(this);
        let form_area = $this.closest('.form_area');
        let target = (form_area.closest(".data_property").length > 0) ? form_area.closest(".data_property").find('.brand_selection') : $('div.abprf_admin .brand_list');
        abprf_save_data(form_area, target, 'abprf_save_brand');
    });
    $(document).on('click', 'div.abprf_admin button.delete_brand', function (e) {
        e.preventDefault();
        let parent = $(this).closest('.brand_list');
        let brand_id = parseInt($(this).attr('data-id'));
        $.ajax({
            type: 'POST', url: abprf_admin_data.ajax_url, data: {
                "action": "abprf_delete_brand", "brand_id": brand_id, 'nonce': abprf_admin_data.nonce
            },
            beforeSend: function () {
                abprf_spinner(parent);
                abprf_toast_msg(abprf_admin_data.msg.deleting, 'error');
            },
            success: function (response) {
                abprf_spinner_remove(parent);
                abprf_toast_msg(response.data.msg);
                parent.html(response.data.html);
            }
        });
    });
    $(document).on('click', 'div.abprf_admin button.edit_brand', function (e) {
        e.preventDefault();
        let target = $(this);
        let parent = target.closest('.brand_area');
        let id = $(this).attr('data-id');
        $.ajax({
            type: 'POST', url: abprf_admin_data.ajax_url, data: {
                "action": "abprf_edit_brand", "id": id, 'nonce': abprf_admin_data.nonce
            },
            beforeSend: function () {
                abprf_spinner(parent);
                abprf_toast_msg(abprf_admin_data.msg.loading, 'error');
            },
            success: function (response) {
                abprf_spinner_remove(parent);
                abprf_toast_msg(response.data.msg);
                parent.find('.insertable_area').html(response.data.html);
                parent.find('.hide_on_load').slideDown();
            }
        });
    });
    $(document).on('click', 'div.abprf_admin button.save_feature', function (e) {
        e.preventDefault();
        let $this = $(this);
        let form_area = $this.closest('.form_area');
        let target = (form_area.closest(".data_property").length > 0) ? form_area.closest(".data_property").find('.feature_selection') : $('div.abprf_admin .feature_list');
        abprf_save_data(form_area, target, 'abprf_save_feature');
    });
    $(document).on('click', 'div.abprf_admin button.delete_feature', function (e) {
        e.preventDefault();
        let target = $(this);
        let parent = target.closest('.feature_area');
        let fec_id = $(this).attr('data-fec_id');
        $.ajax({
            type: 'POST', url: abprf_admin_data.ajax_url, data: {
                "action": "abprf_delete_feature", "fec_id": fec_id, 'nonce': abprf_admin_data.nonce
            },
            beforeSend: function () {
                abprf_spinner(parent);
                abprf_toast_msg(abprf_admin_data.msg.deleting, 'error');
            },
            success: function (response) {
                abprf_spinner_remove(parent);
                abprf_toast_msg(response.data.msg);
                parent.find('.feature_list').html(response.data.html);
                parent.find('.hide_on_load').slideUp('fast');
            }
        });
    });
    $(document).on('click', 'div.abprf_admin button.edit_feature', function (e) {
        e.preventDefault();
        let target = $(this);
        let parent = target.closest('.feature_area');
        let fec_id = $(this).attr('data-id');
        $.ajax({
            type: 'POST', url: abprf_admin_data.ajax_url, data: {
                "action": "abprf_edit_feature", "fec_id": fec_id, 'nonce': abprf_admin_data.nonce
            },
            beforeSend: function () {
                abprf_spinner(parent);
                abprf_toast_msg(abprf_admin_data.msg.loading, 'error');
            },
            success: function (response) {
                abprf_spinner_remove(parent);
                abprf_toast_msg(response.data.msg);
                parent.find('.insertable_area').html(response.data.html);
                parent.find('.hide_on_load').slideDown();
            }
        });
    });
    //==========Faq configuration=================//
    $(document).on('submit', 'div.abprf_admin form.save_faq', function (e) {
        e.preventDefault();
        let parent = $(this).closest('.abprf_admin');
        let target = parent.find('.dashboard_content');
        let formData = new FormData(this);
        formData.append('action', 'abprf_save_faqs');
        formData.append('nonce', abprf_admin_data.nonce);
        $.ajax({
            type: 'POST', url: abprf_admin_data.ajax_url, contentType: false, processData: false, data: formData,
            beforeSend: function () {
                abprf_spinner(target);
                abprf_toast_msg(abprf_admin_data.msg.saving);
            },
            success: function (response) {
                abprf_spinner_remove(target);
                abprf_toast_msg(response.data.msg, 'success');
                window.location.reload();
            }
        });
    });
    $(document).on('click', 'div.abprf_admin button.import_faq', function () {
        let parent = $(this).closest('.faq_configuration');
        let target = parent.find('.faq_content');
        $.ajax({
            type: 'POST', url: abprf_admin_data.ajax_url, data: {
                "action": "abprf_import_faq", 'nonce': abprf_admin_data.nonce
            }, beforeSend: function () {
                abprf_spinner(target);
                abprf_toast_msg(abprf_admin_data.msg.importing);
            }, success: function (data) {
                target.html(data).promise().done(function () {
                    target.find('.sortable_area').sortable({
                        handle: jQuery(this).find('.sortable_handle')
                    });
                    target.find('.insertable_area .edit_area').each(function () {
                        abprf_wp_editor_init($(this));
                    });
                    abprf_toast_msg(abprf_admin_data.msg.imported, 'success');
                });
            }
        });
    });
    $(document).on('submit', 'div.abprf_admin form.save_tc', function (e) {
        e.preventDefault();
        let parent = $(this).closest('.abprf_admin');
        let target = parent.find('.dashboard_content');
        let formData = new FormData(this);
        formData.append('action', 'abprf_save_tc');
        formData.append('nonce', abprf_admin_data.nonce);
        $.ajax({
            type: 'POST', url: abprf_admin_data.ajax_url, contentType: false, processData: false, data: formData,
            beforeSend: function () {
                abprf_spinner(target);
                abprf_toast_msg(abprf_admin_data.msg.saving);
            },
            success: function (response) {
                abprf_spinner_remove(target);
                abprf_toast_msg(response.data.msg, 'success');
                window.location.reload();
            }
        });
    });
    $(document).on('click', 'div.abprf_admin button.import_tc', function () {
        let parent = $(this).closest('.tc_configuration');
        let target = parent.find('.tc_content');
        $.ajax({
            type: 'POST', url: abprf_admin_data.ajax_url, data: {
                "action": "abprf_import_tc", 'nonce': abprf_admin_data.nonce
            }, beforeSend: function () {
                abprf_spinner(target);
                abprf_toast_msg(abprf_admin_data.msg.importing);
            }, success: function (response) {
                target.html(response.data.html).promise().done(function () {
                    target.find('.edit_area').each(function () {
                        abprf_wp_editor_init($(this));
                    });
                    abprf_toast_msg(response.data.msg, 'success');
                });
            }
        });
    });
    //==========WooCommerce configuration=================//
    $(document).on('click', 'div.abprf_admin button.install_and_active_wc', function () {
        let parent = $(this).closest('.abprf_status');
        $.ajax({
            type: 'POST', url: abprf_admin_data.ajax_url, data: {
                "action": "abprf_install_and_active_wc", 'nonce': abprf_admin_data.nonce
            }, beforeSend: function () {
                abprf_spinner(parent);
                abprf_toast_msg(abprf_admin_data.msg.wc_install);
            }, success: function () {
                abprf_toast_msg(abprf_admin_data.msg.wc_installed_success, 'success');
                window.location.reload();
            }
        });
    });
    $(document).on('click', 'div.abprf_admin button.active_wc', function () {
        let parent = $(this).closest('.abprf_status');
        $.ajax({
            type: 'POST', url: abprf_admin_data.ajax_url, data: {
                "action": "abprf_active_wc", 'nonce': abprf_admin_data.nonce
            }, beforeSend: function () {
                abprf_spinner(parent);
                abprf_toast_msg(abprf_admin_data.msg.wc_installing);
            }, success: function () {
                abprf_toast_msg(abprf_admin_data.msg.wc_installed, 'success');
                window.location.reload();
            }
        });
    });
    //==========page create=================//
    $(document).on('click', 'div.abprf_admin button.create_page', function () {
        let type = $(this).attr('data-page_type');
        let parent = $(this).closest('.abprf_status');
        $.ajax({
            type: 'POST', url: abprf_admin_data.ajax_url, data: {
                "action": "abprf_create_page", 'nonce': abprf_admin_data.nonce, 'type': type
            }, beforeSend: function () {
                abprf_spinner(parent);
                abprf_toast_msg(abprf_admin_data.msg.create_post_page);
            }, success: function (response) {
                abprf_toast_msg(response.data.msg, response.data.info_type);
                window.location.reload();
            }
        });
    });
    //==========Dummy data configuration=================//
    $(document).on('click', 'div.abprf_admin button.import_dummy', function () {
        let parent = $(this).closest('.abprf_status');
        $.ajax({
            type: 'POST', url: abprf_admin_data.ajax_url, data: {
                "action": "abprf_import_dummy", 'nonce': abprf_admin_data.nonce
            }, beforeSend: function () {
                abprf_spinner(parent);
                abprf_toast_msg(abprf_admin_data.msg.importing);
            }, success: function (response) {
                abprf_toast_msg(response.data.msg, 'success');
                window.location.reload();
            }
        });
    });
}(jQuery));
//==========================Global=======================//
function abprf_load_sortable_datepicker(parent, item) {
    if (parent.find('.insertable_area_before').length > 0) {
        jQuery(item).insertBefore(parent.find('.insertable_area_before').first()).promise().done(function () {
            parent.find('.sortable_area').sortable({
                handle: jQuery(this).find('.sortable_handle')
            });
            abprf_load_datepicker(parent);
        });
    } else {
        parent.find('.insertable_area').first().append(item).promise().done(function () {
            parent.find('.sortable_area').sortable({
                handle: jQuery(this).find('.sortable_handle')
            });
            abprf_load_datepicker(parent);
        });
    }
    return true;
}
function abprf_wp_editor_init(target) {
    let textArea = target.find('textarea.wp-editor-area');
    if (textArea.length > 0) {
        let uniqueId = 'editor_' + Math.random().toString(36).substring(2, 11);
        if (target.find('.wp-editor-wrap').length > 0) {
            target.find('.wp-editor-wrap').replaceWith(textArea);
        }
        textArea.attr('id', uniqueId).show();
        setTimeout(function () {
            if (typeof wp !== 'undefined' && wp.editor) {
                wp.editor.remove(uniqueId);
                wp.editor.initialize(uniqueId, {
                    tinymce: {
                        wpautop: true,
                        cleanup: false,
                        verify_html: false,
                        entity_encoding: 'raw',
                        forced_root_block: false,
                        valid_elements: '*[*]',
                        setup: function (editor) {
                            editor.on('change', function () {
                                editor.save();
                            });
                        }
                    },
                    quicktags: true,
                    mediaButtons: true
                });
            }
        }, 100);
    }
}
(function ($) {
    "use strict";
    $(document).ready(function () {
        //=========Color Picker==============//
        $('.abprf_color_picker').wpColorPicker();
        $(document).on('click', function (e) {
            if (!$(e.target).closest('.abprf_color_picker').length) {
                $('.wp-picker-container.wp-picker-active').find('.wp-color-result').trigger('click');
            }
        });
        //=========Short able==============//
        $(document).find('div.abprf_area .sortable_area').sortable({
            handle: $(this).find('.sortable_handle'),
            stop: function (event, ui) {
                ui.item.trigger('rf_trigger');
            }
        });
    });
    //=========select  image / image==============//
    $(document).on('click', 'div.abprf_admin .add_image', function () {
        let parent = $(this);
        parent.find('.add_image_item').remove();
        wp.media.editor.send.attachment = function (props, attachment) {
            let attachment_id = attachment.id;
            let attachment_url = attachment.url;
            let html = '<div class="add_image_item" data-image-id="' + attachment_id + '"><span class="fas fa-times _circle_icon_xs remove_image"></span>';
            html += '<img class="_img_control" src="' + attachment_url + '" alt="' + attachment_id + '"/>';
            html += '</div>';
            parent.append(html);
            parent.find('input').val(attachment_id);
            parent.find('button').slideUp('fast');
        }
        wp.media.editor.open($(this));
        return false;
    });
    $(document).on('click', 'div.abprf_admin .remove_image', function (e) {
        e.stopPropagation();
        let parent = $(this).closest('.add_image');
        $(this).closest('.add_image_item').remove();
        parent.find('input').val('');
        parent.find('button').slideDown('fast');
    });
    $(document).on('click', 'div.abprf_admin .add_image_multi', function () {
        let parent = $(this).closest('.multiple_image_area');
        wp.media.editor.send.attachment = function (props, attachment) {
            let attachment_id = attachment.id;
            let attachment_url = attachment.url;
            let html = '<div class="multiple_image_item" data-image-id="' + attachment_id + '"><span class="fas fa-times _circle_icon_xs remove_image_multi"></span>';
            html += '<img class="_img_control" src="' + attachment_url + '" alt="' + attachment_id + '"/>';
            html += '</div>';
            parent.find('.multiple_image').append(html);
            let value = parent.find('.multiple_image_ids').val();
            value = value ? value + ',' + attachment_id : attachment_id;
            parent.find('.multiple_image_ids').val(value);
        }
        wp.media.editor.open($(this));
        return false;
    });
    $(document).on('click', 'div.abprf_admin .remove_image_multi', function () {
        let parent = $(this).closest('.multiple_image_area');
        let current_parent = $(this).closest('.multiple_image_item');
        let img_id = current_parent.data('image-id');
        current_parent.remove();
        let all_img_ids = parent.find('.multiple_image_ids').val();
        all_img_ids = all_img_ids.replace(',' + img_id, '')
        all_img_ids = all_img_ids.replace(img_id + ',', '')
        all_img_ids = all_img_ids.replace(img_id, '')
        parent.find('.multiple_image_ids').val(all_img_ids);
    });
    $(document).on('click', 'div.abprf_admin .icon_image_selection_area .icon_delete', function () {
        let parent = $(this).closest('.icon_image_selection_area');
        parent.find('input[type="hidden"]').val('');
        parent.find('[data-add-icon]').removeAttr('class');
        parent.find('.icon_item').slideUp('fast');
        parent.find('.image_icon_select_area').slideDown('fast');
    });
    $(document).on('click', 'div.abprf_admin button.image_select', function () {
        let $this = $(this);
        let parent = $this.closest('.icon_image_selection_area');
        wp.media.editor.send.attachment = function (props, attachment) {
            let attachment_id = attachment.id;
            let attachment_url = attachment.url;
            parent.find('input[type="hidden"]').val(attachment_id);
            parent.find('.icon_item').slideUp('fast');
            parent.find('img').attr('src', attachment_url);
            parent.find('.image_item').slideDown('fast');
            parent.find('.image_icon_select_area').slideUp('fast');
        }
        wp.media.editor.open($this);
        return false;
    });
    $(document).on('click', 'div.abprf_admin .icon_image_selection_area .image_delete', function () {
        let parent = $(this).closest('.icon_image_selection_area');
        parent.find('input[type="hidden"]').val('');
        parent.find('img').attr('src', '');
        parent.find('.image_item').slideUp('fast');
        parent.find('.image_icon_select_area').slideDown('fast');
    });
    //========= ==============//
    $(document).on('click', 'div.abprf_admin .delete_hook', function () {
        if (confirm(abprf_admin_data.msg.confirm_delete + ' \n\n' + abprf_admin_data.msg.confirm_ok + ' \n ' + abprf_admin_data.msg.confirm_cancel)) {
            $(this).closest('.delete_area ').slideUp(250).remove();
            abprf_toast_msg(abprf_admin_data.msg.delete_success);
        }
    });
    $(document).on('click', 'div.abprf_admin .add_new_hook', function () {
        let parent = $(this).closest('.configuration_content');
        let hidden_target = $(this).next($('.abprf_d_none')).find(' .hidden_content');
        let item = hidden_target.html();
        if (!item || item === "undefined" || item === " ") {
            item = parent.find('.abprf_d_none').first().find('.hidden_content').html();
        }
        if (abprf_load_sortable_datepicker(parent, item)) {
            let target = parent.find('.insertable_area .delete_area').last();
            abprf_wp_editor_init(target);
            target.find('.edit_area').slideDown('fast');
        }
        parent.find('.hide_on_load').slideDown(300);
        $(this).trigger('rf_trigger');
    });
    $(document).on('click', 'div.abprf_admin .edit_hook', function () {
        $(this).closest('.delete_area').toggleClass('active').find('.edit_area').slideToggle('fast');
        //$(this).closest('.delete_area').find('.edit_area').slideToggle('fast');
    });
    $(document).on('keyup change', 'div.abprf_admin [data-pass]', function () {
        let input_value = $(this).val();
        let input_id = $(this).attr('data-pass');
        $(this).closest('.delete_area').find("[data-paste='" + input_id + "']").each(function () {
            $(this).html(input_value);
        });
    });
}(jQuery));
//=================select icon=========================//
(function ($) {
    'use strict';
    let abprf_target_popup = $(document).find('div.abprf_admin .popup_icon');
    let abprf_category_list = abprf_target_popup.find('.dropdown_list');
    let abprf_search_field = abprf_target_popup.find('.abp_dropdown .abp_icon_search');
    let abprf_icon_title = abprf_target_popup.find('.item_icon_title');
    let abprf_icon_area = abprf_target_popup.find('.item_icon_area');
    let abprf_item_loader = abprf_target_popup.find('.item_loader');
    let search_result_icon = [];
    let total_icon = 0;
    let abprf_json_icon = [];
    $.getJSON(abprf_admin_data.icon_url, function (data) {
        abprf_json_icon = data;
        load_icon_category_list();
    }).fail(function () {
        abprf_icon_area.html('Nothing Found !');
    });
    $(document).on('click', 'div.abprf_admin .icon_image_selection_area button.icon_add', function () {
        load_icon_list();
    });
    $(document).on('rf_trigger', 'div.abprf_admin .abp_dropdown .abp_icon_search_hidden', function () {
        let search_value = $(this).val().toLowerCase().trim();
        if (search_value === '' || search_value.length > 2) {
            load_icon_list();
        }
    });
    abprf_search_field.keyup(function () {
        let search_value = $(this).val().toLowerCase().trim();
        if (search_value === '' || search_value.length > 2) {
            load_icon_list();
        }
    });
    abprf_search_field.change(function () {
        let search_value = $(this).val().toLowerCase().trim();
        if (search_value === '' || search_value.length > 2) {
            load_icon_list();
        }
    });
    abprf_target_popup.find('.popup_close').click(function () {
        abprf_search_field.val('').trigger('change');
        abprf_target_popup.find('.icon_item').removeClass('rf_active');
    });
    abprf_target_popup.on('click', '.icon_item', function () {
        let parent = $('[data-active-popup]').closest('.icon_image_selection_area');
        let icon_class = $(this).data('icon-class');
        if (icon_class) {
            parent.find('input[type="hidden"]').val(icon_class);
            parent.find('.image_icon_select_area').slideUp('fast');
            parent.find('.image_item').slideUp('fast');
            parent.find('.icon_item').slideDown('fast');
            if (abprf_emoji_check(icon_class)) {
                parent.find('[data-add-icon]').removeAttr('class').html(icon_class);
            } else {
                parent.find('[data-add-icon]').removeAttr('class').addClass(icon_class).html('');
            }
            abprf_target_popup.find('.icon_item').removeClass('rf_active');
            abprf_target_popup.find('.popup_close').trigger('click');
        }
    });
    // ─── get search icon array / initial array───────────
    function get_icon_array() {
        let pool = [];
        let search_value = abprf_search_field.val().toLowerCase().trim();
        if (search_value) {
            $.each(abprf_json_icon, function (i, group) {
                if (group.category.toLowerCase().includes(search_value)) {
                    $.each(group.icons, function (iconKey, iconLabel) {
                        let match = iconLabel.match(/#(.*?)#/);
                        let finalLabel = match ? match[1] : iconLabel;
                        pool.push({key: iconKey, label: finalLabel});
                    });
                    return pool;
                } else {
                    if (i !== 0) {
                        $.each(group.icons, function (iconKey, iconLabel) {
                            if (iconLabel.toLowerCase().includes(search_value)) {
                                let match = iconLabel.match(/#(.*?)#/);
                                let finalLabel = match ? match[1] : iconLabel;
                                pool.push({key: iconKey, label: finalLabel});
                            }
                        });
                    }
                }
            });
        } else {
            let group = abprf_json_icon[0];
            if (!group) return [];
            $.each(group.icons, function (iconKey, iconLabel) {
                pool.push({key: iconKey, label: iconLabel});
            });
        }
        return pool;
    }
    // ─── load input category ───────────
    function load_icon_category_list() {
        let category_list = $('<ul>').addClass('_abprf');
        $.each(abprf_json_icon, function (i, group) {
            let current_count = Object.keys(group.icons).length;
            if (i !== 0) {
                total_icon += current_count;
            }
            let text = group.category;
            let category_li = $('<li>').attr('data-value', text).attr('data-text', text);
            $('<span>').addClass('_mar_r_xxs').text(group.emoji).appendTo(category_li);
            $('<span>').text(text).appendTo(category_li);
            $('<span>').text('( ' + current_count + ' )').appendTo(category_li);
            category_li.appendTo(category_list);
        });
        category_list.appendTo(abprf_category_list);
        abprf_spinner(abprf_item_loader);
    }
    function load_icon_list() {
        abprf_icon_area.empty();
        search_result_icon = get_icon_array();
        if (search_result_icon.length === 0) {
            abprf_icon_area.html('Nothing Found !');
            updateCount();
            return;
        }
        $.each(search_result_icon, function (i, item) {
            let $item = $('<div>').addClass('icon_item').attr('title', item.label).attr('data-icon-class', item.key);
            let $preview;
            if (abprf_emoji_check(item.key)) {
                $preview = $('<span>').text(item.key);
            } else {
                $preview = $('<span>').addClass(item.key);
            }
            $item.append($preview);
            $item.append($('<i>').text(item.label));
            $item.appendTo(abprf_icon_area);
        });
        updateCount();
    }
    function updateCount() {
        let search_value = abprf_search_field.val();
        search_value = search_value ? search_value : 'Selected Icon'
        abprf_icon_title.text(search_value + ' : ' + search_result_icon.length + ' / ' + total_icon + ' icons');
    }
})(jQuery);
function abprf_emoji_check(str) {
    return !(/^fa[bsrld]\s/.test(str));
}
jQuery(document).ready(function ($) {
    // abprf_related_selection_init();
    $('#publish, .editor-post-publish-button').on('click', function (e) {
        let title = $('#title').val() || $('.editor-post-title__input').val();
        if (!title || title.trim().length === 0) {
            alert('Title empty! Please enter a title before updating.');
            e.preventDefault();
            return false;
        }
    });
});
//=========== Feature selection start=================//
class ABPRF_FeatureSelection {
    constructor(parentSelector, dataSource) {
        this.parent = document.querySelector(parentSelector);
        if (!this.parent) return;
        this.dataSource = dataSource;
        this.hiddenInput = this.parent.querySelector('input[type="hidden"]');
        this.selectedList = this.parent.querySelector('.selected_list');
        this.init();
    }
    init() {
        this.searchEl = this.parent.querySelector('.item_search');
        this.featureListEl = this.parent.querySelector('.selection_list');
        this.loadPreSelected();
        this.bindEvents();
        this.render();
    }
    bindEvents() {
        if (this.searchEl) {
            ['focusin', 'click'].forEach(eventType => {
                this.searchEl.addEventListener(eventType, (e) => {
                    e.stopPropagation();
                    this.featureListEl?.classList.add('active');
                });
            });
            this.searchEl.addEventListener('input', () => this.render());
        }
        document.addEventListener('click', (e) => {
            if (!e.target.closest(this.parent.className.split(' ').map(c => '.' + c).join(''))) {
                this.featureListEl?.classList.remove('active');
            }
        });
    }
    loadPreSelected() {
        if (!this.hiddenInput || !this.selectedList) return;
        let hiddenVal = this.hiddenInput.value;
        let preIds = hiddenVal ? hiddenVal.split(',').map(s => s.trim()).filter(Boolean) : [];
        if (preIds.length > 0) {
            this.selectedList.innerHTML = '';
            preIds.forEach(id => {
                let f = this.dataSource.find(x => String(x.id) === String(id));
                if (!f) return;
                this.appendSelectedItem(f);
            });
        }
    }
    getSelectedIds() {
        let ids = [];
        this.parent.querySelectorAll('.selected_item').forEach(el => {
            let id = el.getAttribute('data-id');
            if (id) ids.push(id);
        });
        return ids;
    }
    render() {
        if (!this.featureListEl) return;
        let q = this.searchEl ? this.searchEl.value.toLowerCase() : '';
        let selectedIds = this.getSelectedIds();
        let available = this.dataSource.filter(f => {
            return selectedIds.indexOf(String(f.id)) === -1 && f.label.toLowerCase().indexOf(q) !== -1;
        });
        if (available.length === 0) {
            this.featureListEl.innerHTML = `<div class="item_empty">${abprf_admin_data.msg.no_item}</div>`;
            return;
        }
        this.featureListEl.innerHTML = available.map(f => {
            let icon_text = abprf_emoji_check(f.icon) ? `<span class="_mar_r_xxs">${f.icon}</span>` : `<span class="${f.icon} _mar_r_xxs"></span>`;
            let label = f.value ? f.label + '-' + f.value : f.label;
            return `
                <div class="selection_item" data-id="${f.id}">
                    <div>${icon_text}${label}</div>
                    <span class="fa-solid fa-plus fs-add"></span>
                </div>
            `;
        }).join('');
        this.featureListEl.querySelectorAll('.selection_item').forEach(item => {
            item.addEventListener('click', () => {
                this.selectItem(item.getAttribute('data-id'));
            });
        });
    }
    selectItem(id) {
        let f = this.dataSource.find(x => String(x.id) === String(id));
        if (!f) return;
        let placeholder = this.selectedList.querySelector('.item_empty');
        if (placeholder) placeholder.remove();
        this.appendSelectedItem(f);
        this.updateHiddenField();
        this.render();
        setTimeout(() => {
            this.featureListEl?.classList.add('active');
        }, 0);
    }
    appendSelectedItem(f) {
        let div = document.createElement('div');
        div.className = 'selected_item';
        div.setAttribute('data-id', f.id);
        let icon_text = abprf_emoji_check(f.icon) ? `<span class="_mar_r_xxs">${f.icon}</span>` : `<i class="${f.icon} _mar_r_xxs"></i>`;
        let label = f.value ? f.label + '-' + f.value : f.label;
        div.innerHTML = `
            <div class="_fa_center">${icon_text}${label}</div>
            <span class="item_remove">❌</span>
        `;
        div.querySelector('.item_remove').addEventListener('click', (e) => {
            e.stopPropagation();
            this.removeItem(f.id);
        });
        this.selectedList.appendChild(div);
    }
    removeItem(id) {
        let item = this.selectedList.querySelector(`.selected_item[data-id="${id}"]`);
        if (item) item.remove();
        if (!this.selectedList.querySelector('.selected_item')) {
            this.selectedList.innerHTML = `<div class="item_empty">${abprf_admin_data.msg.no_item_selected}</div>`;
        }
        this.updateHiddenField();
        this.render();
    }
    updateHiddenField() {
        if (this.hiddenInput) {
            this.hiddenInput.value = this.getSelectedIds().join(',');
        }
    }
}
document.addEventListener('DOMContentLoaded', function () {
    //=========== Related post  selection=================//
    new ABPRF_FeatureSelection('div.abprf_admin .related_item', abprf_related_info);
});
//=========== Feature selection end=================//


