function load_sortable_datepicker(parent, item) {
    if (parent.find('.abprf_insert_item_before').length > 0) {
        jQuery(item).insertBefore(parent.find('.abprf_insert_item_before').first()).promise().done(function () {
            parent.find('.abprf_sortable').sortable({
                handle: jQuery(this).find('.abprf_sortable_handle')
            });
            abprf_load_datepicker(parent);
        });
    } else {
        parent.find('.abprf_insert_item').first().append(item).promise().done(function () {
            parent.find('.abprf_sortable').sortable({
                handle: jQuery(this).find('.abprf_sortable_handle')
            });
            abprf_load_datepicker(parent);
        });
    }
    return true;
}
(function ($) {
    "use strict";
    $(document).ready(function () {
        //=========Color Picker==============//
        $('.abprf_color_picker').wpColorPicker();
        //=========Short able==============//
        $(document).find('.abprf_sortable').sortable({
            handle: $(this).find('.abprf_sortable_handle'),
            stop: function (event, ui) {
                ui.item.trigger('rf_trigger');
            }
        });
    });
    //=========upload image==============//
    $(document).on('click', '.abprf_add_image', function () {
        let parent = $(this);
        parent.find('.abprf_add_image_item').remove();
        wp.media.editor.send.attachment = function (props, attachment) {
            let attachment_id = attachment.id;
            let attachment_url = attachment.url;
            let html = '<div class="abprf_add_image_item" data-image-id="' + attachment_id + '"><span class="fas fa-times _circle_icon_xs abprf_remove_image"></span>';
            html += '<img class="_img_control" src="' + attachment_url + '" alt="' + attachment_id + '"/>';
            html += '</div>';
            parent.append(html);
            parent.find('input').val(attachment_id);
            parent.find('button').slideUp('fast');
        }
        wp.media.editor.open($(this));
        return false;
    });
    $(document).on('click', '.abprf_remove_image', function (e) {
        e.stopPropagation();
        let parent = $(this).closest('.abprf_add_image');
        $(this).closest('.abprf_add_image_item').remove();
        parent.find('input').val('');
        parent.find('button').slideDown('fast');
    });
    $(document).on('click', '.abprf_add_image_multi', function () {
        let parent = $(this).closest('.abprf_multiple_image_area');
        wp.media.editor.send.attachment = function (props, attachment) {
            let attachment_id = attachment.id;
            let attachment_url = attachment.url;
            let html = '<div class="abprf_multiple_image_item" data-image-id="' + attachment_id + '"><span class="fas fa-times _circle_icon_xs abprf_remove_image_multi"></span>';
            html += '<img class="_img_control" src="' + attachment_url + '" alt="' + attachment_id + '"/>';
            html += '</div>';
            parent.find('.abprf_multiple_image').append(html);
            let value = parent.find('.abprf_multiple_image_ids').val();
            value = value ? value + ',' + attachment_id : attachment_id;
            parent.find('.abprf_multiple_image_ids').val(value);
        }
        wp.media.editor.open($(this));
        return false;
    });
    $(document).on('click', '.abprf_remove_image_multi', function () {
        let parent = $(this).closest('.abprf_multiple_image_area');
        let current_parent = $(this).closest('.abprf_multiple_image_item');
        let img_id = current_parent.data('image-id');
        current_parent.remove();
        let all_img_ids = parent.find('.abprf_multiple_image_ids').val();
        all_img_ids = all_img_ids.replace(',' + img_id, '')
        all_img_ids = all_img_ids.replace(img_id + ',', '')
        all_img_ids = all_img_ids.replace(img_id, '')
        parent.find('.abprf_multiple_image_ids').val(all_img_ids);
    });
    //=========Remove Setting Item ==============//
    $(document).on('click', '.abprf_delete_item', function () {
        if (confirm('Are You Sure , Remove this Item ? \n\n 1. Ok : To Remove Item . \n 2. Cancel : To Cancel .')) {
            let parent = $(this).closest('.abprf_insert_item');
            $(this).closest('.abprf_delete_area ').slideUp(250).remove();
            parent.trigger('rf_trigger');
        }
    });
    //=========Add Setting Item==============//
    $(document).on('click', '.abprf_add_item', function () {
        let parent = $(this).closest('.abprf_configuration_content');
        let item = $(this).next($('.abprf_d_none')).find(' .abprf_hidden_item').html();
        if (!item || item === "undefined" || item === " ") {
            item = parent.find('.abprf_d_none').first().find('.abprf_hidden_item').html();
        }
        load_sortable_datepicker(parent, item);
        $(this).trigger('rf_trigger');
    });
}(jQuery));
//================= Single image=========================//
(function ($) {
    "use strict";
    // $(document).on('click', '.abprf_icon_image_selection_area button.abprf_add_icon', function () {
    //     let target_popup = $('.abprf_popup_icon');
    //     target_popup.find('.icon_item').click(function () {
    //         let parent = $('[data-active-popup]').closest('.abprf_icon_image_selection_area');
    //         let icon_class = $(this).data('icon-class');
    //         if (icon_class) {
    //             parent.find('input[type="hidden"]').val(icon_class);
    //             parent.find('.abprf_select_image_icon_content').slideUp('fast');
    //             parent.find('.abprf_image_item').slideUp('fast');
    //             parent.find('.abprf_item_icon').slideDown('fast');
    //             if ($(this).closest('.special_emoji').length > 0) {
    //                 parent.find('[data-add-icon]').removeAttr('class').html(icon_class);
    //             } else {
    //                 parent.find('[data-add-icon]').removeAttr('class').addClass(icon_class).html('');
    //             }
    //             target_popup.find('.icon_item').removeClass('rf_active');
    //             target_popup.find('.popup_close').trigger('click');
    //         }
    //     });
    //     target_popup.find('[data-icon-menu]').click(function () {
    //         if (!$(this).hasClass('rf_active')) {
    //             let target = $(this);
    //             let tabsTarget = target.data('icon-menu');
    //             target_popup.find('[data-icon-menu]').removeClass('rf_active');
    //             target.addClass('rf_active');
    //             target_popup.find('[data-icon-list]').each(function () {
    //                 let targetItem = $(this).data('icon-list');
    //                 if (tabsTarget === 'all_item' || targetItem === tabsTarget) {
    //                     $(this).slideDown(250);
    //                 } else {
    //                     $(this).slideUp(250);
    //                 }
    //             });
    //         }
    //         return false;
    //     });
    //     target_popup.find('.popup_close').click(function () {
    //         target_popup.find('[data-icon-menu="all_item"]').trigger('click');
    //         target_popup.find('.icon_item').removeClass('rf_active');
    //     });
    // });
    $(document).on('click', '.abprf_icon_image_selection_area .abprf_delete_icon', function () {
        let parent = $(this).closest('.abprf_icon_image_selection_area');
        parent.find('input[type="hidden"]').val('');
        parent.find('[data-add-icon]').removeAttr('class');
        parent.find('.abprf_item_icon').slideUp('fast');
        parent.find('.abprf_select_image_icon_content').slideDown('fast');
    });
    $(document).on('click', 'button.abprf_select_image', function () {
        let $this = $(this);
        let parent = $this.closest('.abprf_icon_image_selection_area');
        wp.media.editor.send.attachment = function (props, attachment) {
            let attachment_id = attachment.id;
            let attachment_url = attachment.url;
            parent.find('input[type="hidden"]').val(attachment_id);
            parent.find('.abprf_item_icon').slideUp('fast');
            parent.find('img').attr('src', attachment_url);
            parent.find('.abprf_image_item').slideDown('fast');
            parent.find('.abprf_select_image_icon_content').slideUp('fast');
        }
        wp.media.editor.open($this);
        return false;
    });
    $(document).on('click', '.abprf_icon_image_selection_area .abprf_delete_image', function () {
        let parent = $(this).closest('.abprf_icon_image_selection_area');
        parent.find('input[type="hidden"]').val('');
        parent.find('img').attr('src', '');
        parent.find('.abprf_image_item').slideUp('fast');
        parent.find('.abprf_select_image_icon_content').slideDown('fast');
    });
}(jQuery));
//=================select icon=========================//
(function ($) {
    'use strict';
    let abprf_target_popup = $('.abprf_popup_icon');
    let abprf_category_list = abprf_target_popup.find('.item_category_list');
    let abprf_search_field = abprf_category_list.find('input');
    let abprf_icon_title = abprf_target_popup.find('.item_icon_title');
    let abprf_icon_area = abprf_target_popup.find('.item_icon_area');
    let abprf_item_loader = abprf_target_popup.find('.item_loader');
    let search_result_icon = [];
    let total_icon = 0;
    let abprf_json_icon = [];
    $.getJSON(abprf_icons.url, function (data) {
        abprf_json_icon = data;
        load_icon_category_list();
    }).fail(function () {
        abprf_icon_area.html('Nothing Found !');
    });
    function check_emoji(str) {
        return !(/^fa[bsrld]\s/.test(str));
    }
    $(document).on('click', '.abprf_icon_image_selection_area button.abprf_add_icon', function () {
        let target_popup = $('.abprf_popup_icon');
        load_icon_list();
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
        target_popup.find('.icon_item').click(function () {
            let parent = $('[data-active-popup]').closest('.abprf_icon_image_selection_area');
            let icon_class = $(this).data('icon-class');
            if (icon_class) {
                parent.find('input[type="hidden"]').val(icon_class);
                parent.find('.abprf_select_image_icon_content').slideUp('fast');
                parent.find('.abprf_image_item').slideUp('fast');
                parent.find('.abprf_item_icon').slideDown('fast');
                if (check_emoji(icon_class)) {
                    parent.find('[data-add-icon]').removeAttr('class').html(icon_class);
                } else {
                    parent.find('[data-add-icon]').removeAttr('class').addClass(icon_class).html('');
                }
                target_popup.find('.icon_item').removeClass('rf_active');
                target_popup.find('.popup_close').trigger('click');
            }
        });
        target_popup.find('.popup_close').click(function () {
            abprf_search_field.val('').trigger('change');
            target_popup.find('.icon_item').removeClass('rf_active');
        });
    });
    // ─── get search icon array / initial array───────────
    function get_icon_array() {
        let pool = [];
        let search_value = abprf_search_field.val().toLowerCase().trim();
        if (search_value) {
            $.each(abprf_json_icon, function (i, group) {
                if (group.title.toLowerCase().includes(search_value)) {
                    $.each(group.icon, function (iconKey, iconLabel) {
                        pool.push({key: iconKey, label: iconLabel, cat: group.title});
                    });
                } else {
                    if (i !== 0) {
                        $.each(group.icon, function (iconKey, iconLabel) {
                            if (iconLabel.toLowerCase().includes(search_value) || iconKey.toLowerCase().includes(search_value)) {
                                pool.push({key: iconKey, label: iconLabel, cat: group.title});
                            }
                        });
                    }
                }
            });
        } else {
            let group = abprf_json_icon[0];
            if (!group) return [];
            $.each(group.icon, function (iconKey, iconLabel) {
                pool.push({key: iconKey, label: iconLabel, cat: group.title});
            });
        }
        return pool;
    }
    // ─── load input category ───────────
    function load_icon_category_list() {
        let category_list = $('<ul>').addClass('_abprf dropdown_input');
        $.each(abprf_json_icon, function (i, group) {
            if (i !== 0) {
                total_icon = total_icon + Object.keys(group.icon).length;
            }
            let category_li = $('<li>').attr('data-value', group.title);
            $('<span>').addClass('_mar_r_xxs').text(group.emoji).appendTo(category_li);
            $('<span>').attr('data-text', '').text(group.title).appendTo(category_li);
            $('<span>').text(' ( ' + Object.keys(group.icon).length + ' ) ').appendTo(category_li);
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
            if (check_emoji(item.key)) {
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
        search_value=search_value?search_value:'Selected Icon'
        abprf_icon_title.text(search_value +' : '+search_result_icon.length + ' / ' + total_icon + ' icons');
    }
})(jQuery);