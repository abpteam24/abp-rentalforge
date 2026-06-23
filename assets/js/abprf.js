(function ($) {
    "use strict";
    let abprf_time_slot_infos = JSON.parse(abprf_infos.date_info);
    $(document).ready(function () {
        $('body').find('#abprf_search_area').each(function () {
            load_start_time($(this));
        })
    });
    $(document).on('rf_trigger', '#abprf_search_area [name="post_id"]', function () {
        let post_id = $(this).val();
        load_global_data(post_id, $(this));
    });
    $(document).on('click', 'div.abprf_area .pagination_item .select_post', function (e) {
        e.preventDefault();
        let post_id = $(this).attr('data-post_id');
        let $this = $(this).closest('div.abprf_area').find('#abprf_search_area [name="post_id"]');
        load_global_data(post_id, $this);
    });
    $(document).on("change", "#abprf_search_area [name='rent_start_date']", function (e) {
        e.preventDefault();
        let parent = $(this).closest("#abprf_search_area");
        let rent_rule = $.trim(parent.find('[name="rent_rule"]').val());
        load_start_time(parent);
        if (rent_rule === 'daily' || rent_rule === 'multi_day' || rent_rule === 'monthly' || rent_rule === 'multi_month') {
            load_end_date(parent);
        }
    });
    $(document).on("change", "#abprf_search_area [name='rent_end_date']", function (e) {
        e.preventDefault();
        let parent = $(this).closest("#abprf_search_area");
        let rent_rule = $.trim(parent.find('[name="rent_rule"]').val());
        if (rent_rule === 'multi_day') {
            let date = parent.find('[name="rent_end_date"]').val();
            let start_time = parent.find('[name="start_time"]').val();
            load_end_time(parent, date, start_time);
        }
    });
    $(document).on("change", "#abprf_search_area [name='start_time']", function (e) {
        e.preventDefault();
        let parent = $(this).closest("#abprf_search_area");
        let rent_rule = $.trim(parent.find('[name="rent_rule"]').val());
        if (rent_rule === 'hourly') {
            let date = parent.find('[name="rent_start_date"]').val();
            let start_time = parent.find('[name="start_time"]').val();
            load_end_time(parent, date, start_time);
        }
        if (rent_rule === 'multi_day') {
            let date = parent.find('[name="rent_end_date"]').val();
            let start_time = parent.find('[name="start_time"]').val();
            load_end_time(parent, date, start_time);
        }
    });
    $(document).on('submit', '#abprf_search_area form.abprf_property_form', function (e) {
        e.preventDefault();
        let parent = $(this).closest('.abprf_area');
        let form_area = $(this).closest('#abprf_search_area');
        let rent_rule = $.trim(form_area.find('[name="rent_rule"]').val());
        let post_id = form_area.find('[name="post_id"]').val();
        if (!post_id || post_id.trim() === "") {
            setTimeout(function () {
                abprf_toast_msg(abprf_infos.msg.select_post);
                form_area.find('[name="post_id"]').siblings('input').focus();
            }, 100);
            return;
        }
        if ($.trim(form_area.find('[name="rent_start_date"]').val()).length === 0) {
            setTimeout(function () {
                abprf_toast_msg(abprf_infos.msg.select_rent_start_date);
                form_area.find('#start_date').focus();
            }, 100);
            return;
        }
        if (rent_rule === 'hourly') {
            if ($.trim(form_area.find('[name="start_time"]').val()).length === 0) {
                abprf_toast_msg(abprf_infos.msg.select_rent_start_time);
                form_area.find('[name="start_time"]').show().focus();
                return;
            }
            if ($.trim(form_area.find('[name="end_time"]').val()).length === 0) {
                abprf_toast_msg(abprf_infos.msg.select_rent_end_time);
                form_area.find('[name="end_time"]').show().focus();
                return;
            }
        }
        if (rent_rule === 'daily') {
            if ($.trim(form_area.find('[name="rent_end_date"]').val()).length === 0) {
                setTimeout(function () {
                    abprf_toast_msg(abprf_infos.msg.select_rent_end_date);
                    form_area.find('#end_date').focus();
                }, 100);
                return;
            }
        }
        if (rent_rule === 'monthly') {
            if ($.trim(form_area.find('[name="rent_end_date"]').val()).length === 0) {
                setTimeout(function () {
                    abprf_toast_msg(abprf_infos.msg.select_rent_end_date);
                    form_area.find('[name="rent_end_date"]').show().focus();
                }, 100);
                return;
            }
        }
        let target = parent.find('.abprf_booking');
        let formData = new FormData(this);
        formData.append('action', 'abprf_load_property');
        formData.append('nonce', abprf_infos.nonce);
        $.ajax({
            type: 'POST', url: abprf_infos.ajax_url, contentType: false, processData: false, data: formData,
            beforeSend: function () {
                abprf_spinner(target);
                abprf_spinner(form_area);
                abprf_toast_msg(abprf_infos.msg.property_loading);
            },
            success: function (response) {
                abprf_spinner_remove(target);
                abprf_spinner_remove(form_area);
                if (response.data && response.data.hasOwnProperty('property_info')) {
                    target.find('.property_item_area').html(response.data.property_info);
                    target.find('.property_others').html(response.data.property_others);
                    form_area.find('.date_details').html(response.data.date_details).promise().done(function () {
                        abprf_load_datepicker(target);
                        abprf_load_image(target);
                    });
                    abprf_toast_msg(response.data.msg, 'success');
                } else {
                    abprf_toast_msg(response.data.msg, 'warn');
                }
            }
        });
    });
    function load_global_data(post_id, $this) {
        let text_val = $this.siblings('input').val() + ' ' + abprf_infos.msg.loading;
        let parent = $this.closest('.abprf_area');
        let target_form = parent.find('.global_form');
        let target_area = parent.find('.abprf_global_registration');
        $.ajax({
            type: 'POST', url: abprf_infos.ajax_url, data: {
                "action": "abprf_get_global_booking", 'post_id': post_id, 'nonce': abprf_infos.nonce
            }, beforeSend: function () {
                abprf_spinner(target_form);
                abprf_spinner(target_area);
                abprf_toast_msg(text_val);
            }, success: function (response) {
                abprf_spinner_remove(target_form);
                abprf_spinner_remove(target_area);
                if (response.data && response.data.hasOwnProperty('form') && response.data.hasOwnProperty('details')) {
                    target_form.html(response.data.form).promise().done(function () {
                        if (response.data.hasOwnProperty('start_date') && $('#start_date').length > 0) {
                            abprf_init_all_dynamic_datepickers('#start_date', response.data.start_date);
                        }
                        if (response.data.hasOwnProperty('end_date') && $('#end_date').length > 0) {
                            abprf_init_all_dynamic_datepickers('#end_date', response.data.end_date);
                        }
                    });
                    abprf_time_slot_infos = response.data.time_info;
                    target_area.html(response.data.details).promise().done(function () {
                        abprf_load_image(parent);
                    }).promise().done(function () {
                        load_start_time(target_form);
                    });
                    abprf_toast_msg(response.data.msg, 'success');
                } else {
                    abprf_toast_msg(response.data.msg, 'warn');
                }
            }
        });
    }
    function load_start_time(parent) {
        let rent_rule = $.trim(parent.find('[name="rent_rule"]').val());
        if (rent_rule === 'hourly' || rent_rule === 'multi_day') {
            let date = parent.find('[name="rent_start_date"]').val();
            let dateObj = new Date(date);
            let now = new Date(abprf_infos.now);
            let day_name = dateObj.toLocaleDateString('en-US', {weekday: 'long'}).toLowerCase();
            let selectedSlotString = "";
            if (abprf_time_slot_infos) {
                if (abprf_time_slot_infos[date]) {
                    selectedSlotString = abprf_time_slot_infos[date];
                } else if (abprf_time_slot_infos[day_name]) {
                    selectedSlotString = abprf_time_slot_infos[day_name];
                } else {
                    selectedSlotString = abprf_time_slot_infos['slot'];
                }
            }
            if (selectedSlotString) {
                let slots = selectedSlotString.split('##');
                let optionsHtml = '<option disabled selected>' + abprf_infos.msg.select_rent_start_time + '</option>';
                slots.forEach(slot => {
                    let parts = slot.split('--');
                    let val = parts[0];
                    let label = parts[1];
                    let inputDate = new Date(date + 'T' + val);
                    if (inputDate > now) {
                        optionsHtml += `<option value="${val}">${label}</option>`;
                    }
                });
                parent.find('[name="start_time"]').html(optionsHtml);
            }
        }
    }
    function load_end_date(parent) {
        let target = parent.find('.end_date');
        let date = parent.find('[name="rent_start_date"]').val();
        let post_id = parent.find('[name="post_id"]').val();
        let rent_rule = $.trim(parent.find('[name="rent_rule"]').val());
        let formData = new FormData();
        formData.append('post_id', post_id);
        formData.append('rent_start_date', date);
        formData.append('rent_rule', rent_rule);
        formData.append('action', 'abprf_load_end_date');
        formData.append('nonce', abprf_infos.nonce);
        $.ajax({
            type: 'POST', url: abprf_infos.ajax_url, contentType: false, processData: false, data: formData,
            beforeSend: function () {
                abprf_spinner(target);
                abprf_toast_msg(abprf_infos.msg.end_date_loading);
            },
            success: function (response) {
                abprf_spinner_remove(target);
                if (response.data && response.data.hasOwnProperty('html')) {
                    target.html(response.data.html).promise().done(function () {
                        if (response.data.hasOwnProperty('picker_config') && response.data.picker_config) {
                            abprf_init_all_dynamic_datepickers(response.data.selector, response.data.picker_config);
                        } else {
                            abprf_load_datepicker(target);
                        }
                    });
                    abprf_toast_msg(response.data.msg, 'success');
                } else {
                    abprf_toast_msg(response.data.msg, 'warn');
                }
            }
        });
    }
    function load_end_time(parent, date, start_time) {
        let current_date = parent.find('[name="rent_start_date"]').val();
        let dateObj = new Date(date);
        let day_name = dateObj.toLocaleDateString('en-US', {weekday: 'long'}).toLowerCase();
        let selectedSlotString = "";
        if (abprf_time_slot_infos) {
            if (abprf_time_slot_infos[date]) {
                selectedSlotString = abprf_time_slot_infos[date];
            } else if (abprf_time_slot_infos[day_name]) {
                selectedSlotString = abprf_time_slot_infos[day_name];
            } else {
                selectedSlotString = abprf_time_slot_infos['slot'];
            }
        }
        if (selectedSlotString) {
            let slots = selectedSlotString.split('##');
            let optionsHtml = '<option disabled selected>' + abprf_infos.msg.select_rent_end_time + '</option>';
            slots.forEach(slot => {
                let parts = slot.split('--');
                let val = parts[0];
                let label = parts[1];
                if (current_date === date) {
                    if (timeToMinutes(val) > timeToMinutes(start_time)) {
                        optionsHtml += `<option value="${val}">${label}</option>`;
                    }
                } else {
                    optionsHtml += `<option value="${val}">${label}</option>`;
                }
            });
            parent.find('[name="end_time"]').html(optionsHtml);
        }
    }
    function timeToMinutes(time) {
        let parts = time.split(':');
        return parseInt(parts[0]) * 60 + parseInt(parts[1]);
    }
}(jQuery));
(function ($) {
    "use strict";
    $(document).on("rf_trigger", "div.abprf_booking [name='property_check[]']", function (e) {
        e.preventDefault();
        let $this = $(this);
        let parent = $this.closest(".select_property");
        let data_id = $this.attr('data-id');
        let target = parent.find('[data-collapse="' + data_id + '"]');
        if (target.length > 0) {
            target.slideToggle('fast');
            $this.closest('.property_item').toggleClass('rf_active');
        }
        parent.find('[name="property_qty[]"]').trigger('change');
    });
    $(document).on('change', 'div.abprf_booking [name="property_qty[]"]', function () {
        let parent = $(this).closest('div.abprf_booking');
        all_management(parent);
    })
    $(document).on('change', 'div.abprf_booking .ex_price_calculate', function () {
        let parent = $(this).closest('div.abprf_booking');
        all_management(parent);
    });
    $(document).on('click', 'div.abprf_booking .abprf_book_continue', function (e) {
        e.preventDefault();
        let current = $(this);
        let parent = current.closest('div.abprf_booking');
        if (get_quantity(parent) > 0) {
            if (submit_validation(current) < 1) {
                parent.find("[name='add-to-cart']").trigger('click');
                parent.find("[name='add-admin-order']").trigger('click');
            }
        } else {
            abprf_alert(current);
        }
    });
    function all_management(parent) {
        let qty = get_quantity(parent);
        let price = 0;
        let total = 0;
        let ex_price = 0;
        let deposit_price = 0;
        if (qty > 0) {
            price = get_price(parent);
            ex_price = get_additional_price(parent);
            deposit_price = get_deposit_price(parent);
            total = price + ex_price + deposit_price;
            parent.find('.additional_service_area').slideDown('fast');
            parent.find('.client_info_area').slideDown('fast');
            parent.find('.total_continue_area').slideDown('fast');
        } else {
            parent.find('.client_info_area').slideUp('fast');
            parent.find('.additional_service_area').slideUp('fast');
            parent.find('.total_continue_area').slideUp('fast');
        }
        price = price > 0 ? abprf_wc_price_format(price) : abprf_infos.msg.free;
        ex_price = ex_price > 0 ? abprf_wc_price_format(ex_price) : abprf_infos.msg.free;
        total = total > 0 ? abprf_wc_price_format(total) : abprf_infos.msg.free;
        deposit_price = deposit_price > 0 ? abprf_wc_price_format(deposit_price) : abprf_infos.msg.free;
        parent.find('.item_total').html(price);
        parent.find('.additional_total').html(ex_price);
        parent.find('.deposit_total').html(deposit_price);
        parent.find('.abprf_total').html(total);
        // abprf_load_image();
    }
    function get_quantity(parent) {
        let qty = 0;
        parent.find('.select_property').each(function () {
            let current = $(this);
            let active_property = parseInt($.trim(current.find('[name="property_check[]"]').val()));
            if (active_property === 1) {
                qty = qty + parseInt($.trim(current.find('[name="property_qty[]"]').val()));
            }
        })
        return qty;
    }
    function get_price(parent) {
        let total = 0;
        parent.find('.select_property').each(function () {
            let current = $(this);
            let active_property = parseInt($.trim(current.find('[name="property_check[]"]').val()));
            if (active_property === 1) {
                let target = current.find('[name="property_qty[]"]');
                let price = parseFloat($.trim(target.attr('data-price')));
                price = price && price >= 0 ? price : 0;
                total = total + price * parseInt($.trim(target.val()));
            }
        })
        return total;
    }
    function get_additional_price(parent) {
        let total = 0
        parent.find('.ex_price_calculate').each(function () {
            let ex_qty = parseInt($(this).val());
            let ex_price = $(this).attr('data-price');
            ex_price = ex_price && ex_price >= 0 ? ex_price : 0;
            total = total + parseFloat(ex_price) * ex_qty;
        });
        return total;
    }
    function get_deposit_price(parent) {
        let total = 0;
        parent.find('.select_property').each(function () {
            let current = $(this);
            let active_property = parseInt($.trim(current.find('[name="property_check[]"]').val()));
            if (active_property === 1) {
                let target = current.find('[name="property_qty[]"]');
                let deposit_type = current.find('[name="deposit_type[]"]').val();
                let price = parseFloat($.trim(current.find('[name="deposit_value[]"]').val()));
                price = price && price >= 0 ? price : 0;
                if (deposit_type === 'fixed') {
                    total = total + price;
                } else if (deposit_type === 'percent') {
                    let price_current = parseFloat($.trim(target.attr('data-price'))) * parseInt($.trim(target.val()));
                    total = total + price * price_current / 100;
                } else {
                    total = total + price * parseInt($.trim(target.val()));
                }
            }
        })
        return total;
    }
    function submit_validation(current) {
        let exit = 0;
        current.closest('form').find("[required]").each(function () {
            let value = $(this).val();
            if (!value || value === ' ' || value === 'undefined' || value === '') {
                $(this).trigger('focus').addClass('abprf_required');
                exit++;
            }
        });
        return exit;
    }
}(jQuery));