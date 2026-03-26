(function ($) {
    "use strict";
    $(document).on("rf_trigger", "#abprf_search_area [name='_post_id']", function () {
        let $this = $(this);
        let parent = $this.closest('#abprf_search_area');
        let target = parent.find('.abptm_bp');
        $('body').find('.woocommerce-notices-wrapper').slideUp('fast');
        $.ajax({
            type: 'POST', url: abprf_ajax.ajax_url,
            data: {"action": "abptm_get_bp", 'form_data': $this.closest('form').serializeArray(), 'nonce': abprf_ajax.nonce},
            beforeSend: function () {
                abprf_spinner(parent);
            },
            success: function (data) {
                target.html(data).promise().done(function () {
                    abprf_spinner_remove(parent);
                    parent.find("[name='_bp_dummy']").val('').trigger('click');
                });
            },
            error: function (response) {
                console.log(response);
            }
        });
    });
    $(document).on("rf_trigger", "#abprf_search_area [name='_bp']", function () {
        let $this = $(this);
        let bp = $this.val();
        if (bp) {
            let parent = $this.closest('#abprf_search_area');
            let target = parent.find('.abptm_dp');
            $('body').find('.woocommerce-notices-wrapper').slideUp('fast');
            $.ajax({
                type: 'POST', url: abprf_ajax.ajax_url,
                data: {"action": "abptm_get_dp", 'form_data': $this.closest('form').serializeArray(), 'nonce': abprf_ajax.nonce},
                beforeSend: function () {
                    abprf_spinner(parent);
                },
                success: function (data) {
                    target.html(data).promise().done(function () {
                        load_bp_date(parent);
                    }).promise().done(function () {
                        abprf_spinner_remove(parent);
                        parent.find("[name='_dp_dummy']").val('').trigger('click');
                    });
                },
                error: function (response) {
                    console.log(response);
                }
            });
        }
    });
    $(document).on("rf_trigger", "#abprf_search_area [name='_dp']", function () {
        let $this = $(this);
        let parent = $this.closest('#abprf_search_area');
        let bp = parent.find("[name='_bp']").val();
        let dp = $this.val();
        if (bp && dp) {
            parent.find("#abptm_bp_date").focus();
        } else {
            $this.val('');
            parent.find("[name='_dp_dummy']").val('');
        }
    });
    function load_bp_date(parent) {
        let target = parent.find('.abptm_bp_date');
        $.ajax({
            type: 'POST', url: abprf_ajax.ajax_url,
            data: {'action': 'abptm_get_date', 'form_data': parent.find('form').serializeArray(), 'nonce': abprf_ajax.nonce},
            success: function (data) {
                target.html(data);
            },
            error: function (response) {
                console.log(response);
            }
        });
    }
    $(document).on("change", "#abprf_search_area [name='_j_date']", function () {
        let $this = $(this);
        let parent = $this.closest('#abprf_search_area');
        let target = parent.find('.abptm_return_date');
        let dp = parent.find("[name='_dp']").val();
        if (target.length > 0 && dp) {
            $.ajax({
                type: 'POST', url: abprf_ajax.ajax_url,
                data: {"action": "abptm_get_return_date", 'form_data': $this.closest('form').serializeArray(), 'nonce': abprf_ajax.nonce},
                beforeSend: function () {
                    abprf_spinner(parent);
                },
                success: function (data) {
                    target.html(data);
                    abprf_spinner_remove(parent);
                },
                error: function (response) {
                    console.log(response);
                }
            });
        }
    });
    $(document).on("click", "#abprf_area .abptm_goto_date", function (e) {
        e.preventDefault();
        let date = $(this).attr('data-go_date');
        if (date) {
            let parent = $(this).closest('#abprf_area');
            let key = $(this).closest('.abptm_return_trip_area').length > 0 ? '_r_date' : '_j_date';
            let target = parent.find("#abprf_search_area [name=" + key + "]");
            let target_picker = target.closest('label').find('.hasDatepicker');
            target.val(date).promise().done(function () {
                target_picker.datepicker("setDate", new Date(date));
                parent.find('.abprf_submit').trigger('click');
            });
        }
    });
    $(document).on("click", "#abprf_search_area .abprf_get_rental", function (e) {
        e.preventDefault();
        let parent = $(this).closest('#abprf_search_area');
        let bp = parent.find('input[name="_bp"]').val();
        let dp = parent.find('input[name="_dp"]').val();
        let bp_date = parent.find('input[name="_j_date"]').val();
        if (!bp || bp === '') {
            parent.find("[name='_bp_dummy']").trigger('click');
            return false;
        } else if (!dp || dp === '') {
            parent.find("[name='_dp_dummy']").trigger('click');
            return false;
        } else if (!bp_date || bp_date === '') {
            parent.find("#abptm_bp_date").focus();
            return false;
        } else {
            let target = parent.closest('#abprf_area').find('.abprf_rental_result');
            abprf_get_rental($(this), target, parent)
        }
    });
    function abprf_get_rental($this, target, parent) {
        $.ajax({
            type: "POST", url: abprf_ajax.ajax_url,
            data: {'action': 'abprf_get_rental', 'form_data': $this.closest('form').serializeArray(), 'nonce': abprf_ajax.nonce},
            beforeSend: function () {
                abprf_spinner(parent);
                abprf_spinner(target);
            },
            success: function (data) {
                target.html(data).promise().done(function () {
                    abprf_spinner_remove(parent);
                    abprf_spinner_remove(target);
                    abprf_load_slider();
                    abprf_load_bg_image();
                });
            },
            error: function (response) {
                console.log(response);
            },
        });
    }
    $(document).on("click", ".transportation_item .abprf_get_rental_details", function (e) {
        e.preventDefault();
        let parent = $(this).closest('.transportation_item');
        let target = parent.find('.abprf_rental_details');
        if (target.children().length > 0) {
            target.html('');
        } else {
            let bp = parent.find('input[name="bp"]').val();
            let dp = parent.find('input[name="dp"]').val();
            let bp_date = parent.find('input[name="j_date"]').val();
            let post_id = parent.find('[name="post_id"]').val();
            if (bp && dp && bp_date && post_id) {
                abprf_get_rental($(this), target, parent)
            }
        }
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
    $(document).on('click', 'div.abprf_registration_item .abprf_book_continue', function (e) {
        e.preventDefault();
        let current = $(this);
        let parent = current.closest('div.abprf_registration_item');
        if (get_price(parent) > 0) {
            if (submit_validation(current) < 1) {
                let checkout_system = parent.find("[name='checkout_system']").val();
                if (checkout_system === 'default') {
                    parent.find("[name='add-to-cart']").trigger('click');
                    parent.find("[name='add-admin-order']").trigger('click');
                } else {
                    $.ajax({
                        type: "POST", url: abprf_ajax.ajax_url,
                        data: {'action': 'abprf_book_continue', 'wc_link_id': parent.find('[name="add-to-cart"]').val(), 'form_data': current.closest('form').serializeArray(), 'nonce': abprf_ajax.nonce},
                        beforeSend: function () {
                            abprf_spinner(parent);
                        },
                        success: function (data) {
                            if (data) {
                                window.location.href = data;
                            } else {
                                alert(current.attr('data-msg'));
                                $('body').find(".abprf_get_rental").trigger('click');
                            }
                        },
                        error: function (response) {
                            console.log(response);
                        },
                    });
                }
            }
        } else {
            abprf_alert(current);
        }
    });
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
                let hidden_tr = parent.find('.abprf_d_none .abprf_delete_area ');
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

// (function ($) {
//     'use strict';
//     // ─── State ───────────────────────────────────────────────
//     let jsonData = [];   // raw JSON array (categories)
//     let activeCategory = 'all';
//     let searchQuery = '';
//     let searchTimer;
//     // ─── Init: JSON একবারই load হবে ─────────────────────────
//     $.getJSON(abprf_icons.url, function (data) {
//         jsonData = data;
//         //buildCategoryTabs();
//         renderIcons();
//     }).fail(function () {
//         $('#icon-grid').html('<p style="color:red;">Icon data load হয়নি।</p>');
//     });
//     $(document).on('click', '.abprf_icon_image_selection_area button.abprf_add_icon', function () {
//         let target_popup = $('.abprf_popup_icon');
//         //renderIcons(target_popup);
//         target_popup.find('.icon_item').click(function () {
//             let parent = $('[data-active-popup]').closest('.abprf_icon_image_selection_area');
//             let icon_class = $(this).data('icon-class');
//             if (icon_class) {
//                 parent.find('input[type="hidden"]').val(icon_class);
//                 parent.find('.abprf_select_image_icon_content').slideUp('fast');
//                 parent.find('.abprf_image_item').slideUp('fast');
//                 parent.find('.abprf_item_icon').slideDown('fast');
//                 if ($(this).closest('.special_emoji').length > 0) {
//                     parent.find('[data-add-icon]').removeAttr('class').html(icon_class);
//                 } else {
//                     parent.find('[data-add-icon]').removeAttr('class').addClass(icon_class).html('');
//                 }
//                 target_popup.find('.icon_item').removeClass('rf_active');
//                 target_popup.find('.popup_close').trigger('click');
//             }
//         });
//         target_popup.find('[data-icon-menu]').click(function () {
//             if (!$(this).hasClass('rf_active')) {
//                 let target = $(this);
//                 let tabsTarget = target.data('icon-menu');
//                 target_popup.find('[data-icon-menu]').removeClass('rf_active');
//                 target.addClass('rf_active');
//                 target_popup.find('[data-icon-list]').each(function () {
//                     let targetItem = $(this).data('icon-list');
//                     if (tabsTarget === 'all_item' || targetItem === tabsTarget) {
//                         $(this).slideDown(250);
//                     } else {
//                         $(this).slideUp(250);
//                     }
//                 });
//             }
//             return false;
//         });
//         target_popup.find('.popup_close').click(function () {
//             target_popup.find('[data-icon-menu="all_item"]').trigger('click');
//             target_popup.find('.icon_item').removeClass('rf_active');
//         });
//     });
//     // ─── Category tabs তৈরি ──────────────────────────────────
//     function buildCategoryTabs() {
//         var $tabs = $('#icon-category-tabs').empty();
//         // "সব" tab
//         $('<button>')
//             .addClass('icon-tab-btn active')
//             .attr('data-cat', 'all')
//             .text('সব')
//             .appendTo($tabs);
//         // প্রতিটি category থেকে tab
//         $.each(jsonData, function (i, group) {
//             $('<button>')
//                 .addClass('icon-tab-btn')
//                 .attr('data-cat', group.category)
//                 .text(group.title || group.category)
//                 .appendTo($tabs);
//         });
//     }
//     // ─── Icon render ─────────────────────────────────────────
//     function renderIcons() {
//         let target_popup = $('.abprf_popup_icon');
//         let item_icon_area = target_popup.find('.item_icon_area').empty();
//         let item_icon_title = target_popup.find('.item_icon_title').empty();
//         var q = searchQuery.toLowerCase().trim();
//         var total = 0;
//         $.each(jsonData, function (i, group) {
//             // Category filter
//             if (activeCategory !== 'all' && group.category !== activeCategory) {
//                 return; // continue
//             }
//             var icons = group.icon; // { "fas fa-home": "Home", ... }
//             var matched = [];
//             // icon object loop — key = class/emoji, value = label
//             $.each(icons, function (iconKey, iconLabel) {
//                 if (q === '') {
//                     matched.push({key: iconKey, label: iconLabel});
//                 } else {
//                     // search: label বা key-এ আছে কিনা
//                     if (
//                         iconLabel.toLowerCase().includes(q) ||
//                         iconKey.toLowerCase().includes(q)
//                     ) {
//                         matched.push({key: iconKey, label: iconLabel});
//                     }
//                 }
//             });
//             if (matched.length === 0) return; // এই group-এ কিছু নেই
//             // Icon items
//             $.each(matched, function (j, item) {
//                 let $item = $('<div>').addClass('icon_item').attr('title', item.label).attr('data-icon-class', item.key);
//                 // emoji নাকি FA class — detect করো
//                 var $preview;
//                 if (isEmoji(item.key)) {
//                     $preview = $('<span>').text(item.key);
//                 } else {
//                     $preview = $('<span>').addClass(item.key);
//                 }
//                 $item.append($preview);
//                 $item.append($('<i>').text(item.label));
//                 $item.appendTo(item_icon_area);
//                 total++;
//             });
//             if (total === 0) {
//                 item_icon_area.html('Nothing Found !');
//             } else {
//                 item_icon_title.append((q !== '' ? q : group.title) + ' : ' + total)
//             }
//         });
//         // Count দেখাও
//         $('#icon-count').text(total + ' টি icon');
//     }
//     function isEmoji(str) {
//         return !str.startsWith('fas ') && !str.startsWith('fab ') && !str.startsWith('far ') && !str.startsWith('fal ') && !str.startsWith('fad ');
//     }
//     // ─── Icon click — value select ────────────────────────────
//     $(document).on('click', '.icon-item', function () {
//         var key = $(this).data('icon-key');
//         var label = $(this).data('icon-label');
//         // hidden input-এ value set করো
//         $('#selected-icon-input').val(key);
//         // Preview দেখাও
//         if (isEmoji(key)) {
//             $('#icon-preview').html('<span style="font-size:32px">' + key + '</span>');
//         } else {
//             $('#icon-preview').html('<i class="' + key + '" style="font-size:32px"></i>');
//         }
//         $('#selected-icon-label').text(label);
//         // Selected highlight
//         $('.icon-item').removeClass('selected');
//         $(this).addClass('selected');
//     });
//     // ─── Tab click ────────────────────────────────────────────
//     $(document).on('click', '.icon-tab-btn', function () {
//         activeCategory = $(this).data('cat');
//         searchQuery = '';
//         $('#icon-search-input').val('');
//         $('.icon-tab-btn').removeClass('active');
//         $(this).addClass('active');
//         renderIcons();
//     });
//     // ─── Search (debounce 300ms) ──────────────────────────────
//     $(document).on('input', '#icon-search-input', function () {
//         clearTimeout(searchTimer);
//         var val = $(this).val();
//         searchTimer = setTimeout(function () {
//             searchQuery = val;
//             activeCategory = 'all'; // search করলে সব category দেখাও
//             $('.icon-tab-btn').removeClass('active');
//             $('[data-cat="all"]').addClass('active');
//             renderIcons();
//         }, 300);
//     });
// })(jQuery);