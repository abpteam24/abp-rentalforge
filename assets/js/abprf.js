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
    function load_start_time(parent) {
        let post_id = parent.find('[name="post_id"]').val();
        let date = parent.find('[name="rent_start_date"]').val();
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
            let optionsHtml = '<option disabled selected>' + abprf_infos.msg.select_rent_start_time + '</option>';
            slots.forEach(slot => {
                let parts = slot.split('--');
                let val = parts[0];
                let label = parts[1];
                optionsHtml += `<option value="${val}">${label}</option>`;
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
        let target = parent.find('.property_item_area');
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
                form_area.find('.date_details').html(response.data.date_details);
                abprf_toast_msg(abprf_infos.msg.property_loading_success, 'success');
            }
        });
    });
}(jQuery));
(function ($) {
    "use strict";
    $(document).on('click', 'div.abprf_registration_item .ticket_type_list li', function () {
        let current = $(this);
        let target = current.closest('th').find('.seat_sale');
        let label = current.attr('data-label');
        let price = current.attr('data-price');
        let type = current.attr('data-type');
        let parent = current.closest('div.abprf_registration_item');
        target.attr('data-label', label).attr('data-price', price).attr('data-type', type).promise().done(function () {
            if (target.hasClass('selected')) {
                all_management(parent);
            } else {
                target.trigger('click');
            }
        });
    });
    $(document).on('change', 'div.abprf_registration_item .ex_price_calculate', function () {
        let parent = $(this).closest('div.abprf_registration_item');
        all_management(parent);
    });
    $(document).on('change', 'div.abprf_registration_item [name="equipment_qty[]"]', function () {
        let parent = $(this).closest('div.abprf_registration_item');
        all_management(parent);
    })
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
    function all_management(parent) {
        let qty = get_quantity(parent);
        let price = 0;
        let total = 0;
        ticket_management(parent, qty);
        traveller_management(parent, qty);
        additional_management(parent, qty);
        if (qty > 0) {
            price = get_price(parent);
            let ex_price = get_additional_price(parent);
            total = price + ex_price;
            parent.find('.transport_additional_service').slideDown('fast');
            parent.find('.transport_selection').slideDown('fast');
            parent.find('.abptm_pickup_drop').slideDown('fast');
        } else {
            parent.find('.transport_selection').slideUp('fast');
            parent.find('.transport_additional_service').slideUp('fast');
            parent.find('.abptm_pickup_drop').slideUp('fast');
        }
        parent.find('.abptm_sub_total').html(abprf_wc_price_format(price));
        parent.find('.abptm_total').html(abprf_wc_price_format(total));
        abprf_load_bg_image();
    }
    function get_price(parent) {
        let total = 0;
        if (parent.find('.abptm_seat_plan_area').length > 0) {
            parent.find('.seat_sale.selected').each(function () {
                total = total + parseFloat($(this).attr('data-price'));
            });
        } else {
            parent.find('[name="equipment_qty[]"]').each(function () {
                let qty = parseInt($(this).val());
                let price = parseFloat($(this).attr('data-price'));
                price = price && price >= 0 ? price : 0;
                total = total + price * qty;
            });
        }
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
    function get_quantity(parent) {
        let qty = 0;
        if (parent.find('.abptm_seat_plan_area').length > 0) {
            parent.find('.seat_sale.selected').each(function () {
                qty++;
            });
        } else {
            parent.find('[name="equipment_qty[]"]').each(function () {
                qty = qty + parseInt($(this).val());
            });
        }
        return qty;
    }
    function ticket_management(parent, total_qty) {
        if (parent.find('.abptm_seat_plan_area').length > 0) {
            let target_ld = parent.find('.seat_plan_ld');
            if (target_ld.length > 0) {
                let seats = '';
                let seats_type = '';
                target_ld.find('.seat_sale.selected').each(function () {
                    seats = seats ? seats + ',' + $(this).attr('data-name') : $(this).attr('data-name');
                    seats_type = seats_type ? seats_type + ',' + $(this).attr('data-type') : $(this).attr('data-type');
                }).promise().done(function () {
                    target_ld.find('[name="selected_ld"]').val(seats);
                    target_ld.find('[name="selected_ld_type"]').val(seats_type);
                });
            }
            let target_ud = parent.find('.seat_plan_ud');
            if (target_ud.length > 0) {
                let seats_dd = '';
                let seats_dd_type = '';
                target_ud.find('.seat_sale.selected').each(function () {
                    seats_dd = seats_dd ? seats_dd + ',' + $(this).attr('data-name') : $(this).attr('data-name');
                    seats_dd_type = seats_dd_type ? seats_dd_type + ',' + $(this).attr('data-type') : $(this).attr('data-type');
                }).promise().done(function () {
                    target_ud.find('[name="selected_ud"]').val(seats_dd);
                    target_ud.find('[name="selected_ud_type"]').val(seats_dd_type);
                });
            }
            seat_management(parent, total_qty)
        }
    }
    function seat_management(parent, total_qty) {
        if (parent.find('.abptm_seat_plan_area').length > 0) {
            let target = parent.find('.transport_selection .insert_item');
            if (total_qty > 0) {
                let item_length = target.find('tr').length;
                let hidden_tr = parent.find('.abprf_d_none .delete_area ');
                parent.find('.seat_sale.selected').each(function () {
                    let seat_name = $(this).attr('data-name');
                    let seat_type = $(this).attr('data-type');
                    if (target.find('[data-name="' + seat_name + '"]').length === 0) {
                        seat_selected($(this), hidden_tr, target);
                    } else {
                        if (target.find('[data-name="' + seat_name + '"]').length === 1 && target.find('[data-type="' + seat_type + '"]').length === 0) {
                            target.find('[data-name="' + seat_name + '"]').remove();
                            seat_selected($(this), hidden_tr, target);
                        }
                    }
                }).promise().done(function () {
                    item_length = target.find('tr').length;
                    if (item_length !== total_qty) {
                        target.find('tr').each(function () {
                            let seat_name = $(this).attr('data-name');
                            if (parent.find('.seat_sale.selected[data-name="' + seat_name + '"]').length === 0) {
                                $(this).remove();
                            }
                        });
                    }
                });
            } else {
                target.html('');
            }
        }
    }
    function seat_selected(current, hidden_tr, target) {
        let label = current.attr('data-label');
        let price = current.attr('data-price');
        let name = current.attr('data-name');
        let type = current.attr('data-type');
        let text = label ? name + '(' + label + ')' : name;
        hidden_tr.attr('data-type', type).attr('data-name', name).promise().done(function () {
            hidden_tr.find('.seat_name').html(text);
            hidden_tr.find('.seat_price').html(abprf_wc_price_format(price));
        }).promise().done(function () {
            target.append(hidden_tr.clone());
        });
    }
    function traveller_management(parent, qty) {
        let target = parent.find('.abprf_client_info');
        let single_attendee = parent.find('[name="display_single_form"]').val();
        if (single_attendee === 'on') {
            if (qty > 0) {
                target.slideDown(250).promise().done(function () {
                    abprf_load_datepicker(target);
                });
            } else {
                target.slideUp(250);
            }
        } else {
            if (target.length > 0 && qty > 0) {
                target.slideDown(250);
                let form_length = target.find('.attendee_item').length;
                if (form_length !== qty) {
                    let hidden_target = parent.find('.attendee_item_hidden');
                    if (parent.find('.abptm_seat_plan_area').length > 0) {
                        parent.find('.seat_sale.selected').each(function () {
                            let seat_name = $(this).attr('data-name');
                            if (target.find('[data-name="' + seat_name + '"]').length === 0) {
                                hidden_target.find('.attendee_item').attr('data-name', seat_name);
                                hidden_target.find('.attendee_seat_name').html(seat_name).promise().done(function () {
                                    target.append(hidden_target.html());
                                });
                            }
                        }).promise().done(function () {
                            abprf_load_datepicker(target);
                            form_length = target.find('.attendee_item').length;
                            if (form_length !== qty) {
                                target.find('.attendee_item').each(function () {
                                    let seat_name = $(this).attr('data-name');
                                    if (parent.find('.seat_sale.selected[data-name="' + seat_name + '"]').length === 0) {
                                        $(this).remove();
                                    }
                                });
                            }
                        });
                    } else {
                        if (form_length > qty) {
                            for (let i = form_length; i > qty; i--) {
                                target.find('.attendee_item:last-child').slideUp(250).remove();
                            }
                        } else {
                            for (let i = form_length; i < qty; i++) {
                                hidden_target.find('.attendee_seat_name').html(i + 1).promise().done(function () {
                                    target.append(hidden_target.html());
                                }).promise().done(function () {
                                    abprf_load_datepicker(target);
                                });
                            }
                        }
                    }
                }
            } else {
                target.html('').slideUp(250);
            }
        }
    }
    function additional_management(parent, qty) {
        let target = parent.find('.abprf_additional_info');
        let display_single_additional = parent.find('[name="display_single_additional"]').val();
        if (display_single_additional === 'off' && target.length > 0) {
            if (qty > 0) {
                target.slideDown(250);
                let form_length = target.find('.transport_additional_service').length;
                if (form_length !== qty) {
                    let hidden_target = parent.find('.additional_item_hidden');
                    if (parent.find('.abptm_seat_plan_area').length > 0) {
                        parent.find('.seat_sale.selected').each(function () {
                            let seat_name = $(this).attr('data-name');
                            if (target.find('[data-additional="' + seat_name + '"]').length === 0) {
                                hidden_target.find('.transport_additional_service').attr('data-additional', seat_name);
                                hidden_target.find('.additional_seat_name').html(seat_name).promise().done(function () {
                                    target.append(hidden_target.html());
                                });
                            }
                        }).promise().done(function () {
                            form_length = target.find('.transport_additional_service').length;
                            if (form_length !== qty) {
                                target.find('.transport_additional_service').each(function () {
                                    let seat_name = $(this).attr('data-additional');
                                    if (parent.find('.seat_sale.selected[data-name="' + seat_name + '"]').length === 0) {
                                        $(this).remove();
                                    }
                                });
                            }
                        });
                    } else {
                        if (form_length > qty) {
                            for (let i = form_length; i > qty; i--) {
                                target.find('.transport_additional_service:last-child').slideUp(250).remove();
                            }
                        } else {
                            for (let i = form_length; i < qty; i++) {
                                hidden_target.find('.additional_seat_name').html(i + 1).promise().done(function () {
                                    target.append(hidden_target.html());
                                });
                            }
                        }
                    }
                }
            } else {
                target.html('').slideUp(250);
            }
        }
    }
}(jQuery));
