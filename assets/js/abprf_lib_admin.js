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
(function ($) {
    "use strict";
    //=================select icon=========================//
    $(document).on('click', '.abprf_icon_image_selection_area button.abprf_add_icon', function () {
        let target_popup = $('.abprf_popup_icon');
        target_popup.find('.iconItem').click(function () {
            let parent = $('[data-active-popup]').closest('.abprf_icon_image_selection_area');
            let icon_class = $(this).data('icon-class');
            if (icon_class) {
                parent.find('input[type="hidden"]').val(icon_class);
                parent.find('.abprf_select_image_icon_content').slideUp('fast');
                parent.find('.abprf_image_item').slideUp('fast');
                parent.find('.abprf_item_icon').slideDown('fast');
                parent.find('[data-add-icon]').removeAttr('class').addClass(icon_class);
                target_popup.find('.iconItem').removeClass('rf_active');
                target_popup.find('.popup_close').trigger('click');
            }
        });
        target_popup.find('[data-icon-menu]').click(function () {
            if (!$(this).hasClass('rf_active')) {
                let target = $(this);
                let tabsTarget = target.data('icon-menu');
                target_popup.find('[data-icon-menu]').removeClass('rf_active');
                target.addClass('rf_active');
                target_popup.find('[data-icon-list]').each(function () {
                    let targetItem = $(this).data('icon-list');
                    if (tabsTarget === 'all_item' || targetItem === tabsTarget) {
                        $(this).slideDown(250);
                    } else {
                        $(this).slideUp(250);
                    }
                });
            }
            return false;
        });
        target_popup.find('.popup_close').click(function () {
            target_popup.find('[data-icon-menu="all_item"]').trigger('click');
            target_popup.find('.iconItem').removeClass('rf_active');
        });
    });
    $(document).on('click', '.abprf_icon_image_selection_area .abprf_delete_icon', function () {
        let parent = $(this).closest('.abprf_icon_image_selection_area');
        parent.find('input[type="hidden"]').val('');
        parent.find('[data-add-icon]').removeAttr('class');
        parent.find('.abprf_item_icon').slideUp('fast');
        parent.find('.abprf_select_image_icon_content').slideDown('fast');
    });
    //=================select Single image=========================//
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