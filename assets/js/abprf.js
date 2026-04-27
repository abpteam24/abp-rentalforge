(function ($) {
    "use strict";
    let abprf_date_infos = JSON.parse(abprf_infos.date_info);
    $(document).ready(function () {
        $('body').find('#abprf_search_area').each(function () {
            load_start_time($(this));
        })
    });
    $(document).on("change", "#abprf_search_area [name='rent_start_date']", function (e) {
        e.preventDefault();
        let parent = $(this).closest("#abprf_search_area");
        let rent_rule = $.trim(parent.find('[name="rent_rule"]').val());
        if (rent_rule === 'hourly') {
            load_start_time(parent);
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
    });
    $(document).on('submit', '#abprf_search_area form.abprf_property_form', function (e) {
        e.preventDefault();
        let parent = $(this).closest('#abprf_area');
        let form_area = $(this).closest('#abprf_search_area');
        let rent_rule = $.trim(form_area.find('[name="rent_rule"]').val());
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
        let target = parent.find('.property_registration');
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
                target.html(response.data.property_info);
                form_area.find('.date_details').html(response.data.date_details).promise().done(function (){
                    abprf_load_datepicker(target);
                });
                abprf_toast_msg(abprf_infos.msg.property_loading_success, 'success');
            }
        });
    });
    function load_start_time(parent) {
        let post_id = parent.find('[name="post_id"]').val();
        let date = parent.find('[name="rent_start_date"]').val();
        let dateObj = new Date(date);
        let now = new Date(abprf_infos.now);
        let day_name = dateObj.toLocaleDateString('en-US', {weekday: 'long'}).toLowerCase();
        let selectedSlotString = "";
        if (abprf_date_infos[post_id]) {
            let date_info = abprf_date_infos[post_id];
            if (date_info[date]) {
                selectedSlotString = date_info[date];
            } else if (date_info[day_name]) {
                selectedSlotString = date_info[day_name];
            } else {
                selectedSlotString = date_info['slot'];
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
    function load_end_time(parent, date, start_time) {
        let post_id = parent.find('[name="post_id"]').val();
        let current_date = parent.find('[name="rent_start_date"]').val();
        let dateObj = new Date(date);
        let day_name = dateObj.toLocaleDateString('en-US', {weekday: 'long'}).toLowerCase();
        let selectedSlotString = "";
        if (abprf_date_infos[post_id]) {
            let date_info = abprf_date_infos[post_id];
            if (date_info[date]) {
                selectedSlotString = date_info[date];
            } else if (date_info[day_name]) {
                selectedSlotString = date_info[day_name];
            } else {
                selectedSlotString = date_info['slot'];
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
    $(document).on("rf_trigger", "div.abprf_registration_item [name='property_check[]']", function (e) {
        e.preventDefault();
        let $this = $(this);
        let parent = $this.closest(".select_property");
        let data_id = $this.attr('data-id');
        let target = parent.find('[data-collapse="' + data_id + '"]');
        if (target.length > 0) {
            target.slideToggle('fast');
        }
        parent.find('[name="property_qty[]"]').trigger('change');
    });
    $(document).on('change', 'div.abprf_registration_item [name="property_qty[]"]', function () {
        let parent = $(this).closest('div.abprf_registration_item');
        all_management(parent);
    })
    $(document).on('change', 'div.abprf_registration_item .ex_price_calculate', function () {
        let parent = $(this).closest('div.abprf_registration_item');
        all_management(parent);
    });
    $(document).on('click', 'div.abprf_registration_item .abprf_book_continue', function (e) {
        e.preventDefault();
        let current = $(this);
        let parent = current.closest('div.abprf_registration_item');
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
        // abprf_load_bg_image();
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

