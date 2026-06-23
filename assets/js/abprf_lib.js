//=============================================================================Load initial=================//
(function ($) {
    "use strict";
    $(document).ready(function () {
        abprf_load_datepicker();
        abprf_init_all_dynamic_datepickers();
        abprf_load_tabs();
        $('body').find('div.abprf_area [data-image-href]').each(function () {
            abprf_spinner($(this));
        });
        abprf_load_image();
        if ($('.toast_msg_area').length === 0) {
            $('div.abprf_area').first().append('<div class="toast_msg_area"></div>');
        }
        let toast_notices = $('body div.abprf_area ').find('.toast_notice');
        if (toast_notices.length > 0) {
            toast_notices.each(function (index) {
                let current_notice = $(this);
                let type = current_notice.attr('data-type') || 'info';
                let msg = current_notice.html();
                setTimeout(function() {
                    abprf_toast_msg(msg, type);
                }, index * 600);
            });
        }
    });
    //======================================================================Outer Close==========//
    $(document).click(function (e) {
        let target = $(e.target);
        if (target.closest('.abp_dropdown').length === 0) {
            $('body').find('.dropdown_list').slideUp(250);
        }
    });
}(jQuery));
//=============================================================================Change icon and text=================//
function abprf_data_change($this) {
    abprf_load_image();
    abprf_class_change($this);
    abprf_icon_change($this);
    abprf_text_change($this);
    abprf_input_value_change($this);
}
function abprf_icon_change(currentTarget) {
    let openIcon = currentTarget.data('open-icon');
    let closeIcon = currentTarget.data('close-icon');
    if (openIcon || closeIcon) {
        if (currentTarget.hasClass('rf_active')) {
            currentTarget.find('[data-icon]').removeClass(closeIcon).addClass(openIcon);
        } else {
            currentTarget.find('[data-icon]').removeClass(openIcon).addClass(closeIcon);
        }
        // currentTarget.find('[data-icon]').toggleClass(closeIcon).toggleClass(openIcon);
    }
}
function abprf_text_change(currentTarget) {
    let openText = currentTarget.data('open-text');
    openText = openText ? openText.toString() : '';
    let closeText = currentTarget.data('close-text');
    closeText = closeText ? closeText : '';
    if (openText || closeText) {
        let text = currentTarget.find('[data-text]').html();
        text = text ? text.toString() : ''
        if (text !== openText) {
            currentTarget.find('[data-text]').html(openText);
        } else {
            currentTarget.find('[data-text]').html(closeText);
        }
    }
}
function abprf_class_change(currentTarget) {
    let clsName = currentTarget.data('add-class');
    if (clsName) {
        if (currentTarget.find('[data-class]').length > 0) {
            currentTarget.find('[data-class]').toggleClass(clsName);
        } else {
            currentTarget.toggleClass(clsName);
        }
    }
}
function abprf_input_value_change(currentTarget) {
    currentTarget.find('[data-value]').each(function () {
        let value = jQuery(this).val();
        if (value) {
            jQuery(this).val('');
        } else {
            jQuery(this).val(jQuery(this).data('value'));
        }
        jQuery(this).trigger('change');
    });
}
(function ($) {
    "use strict";
    $(document).on('click', '.abprf_area [data-all-change]', function () {
        abprf_data_change($(this));
    });
    $(document).on('click', '.abprf_area [data-icon-change]', function () {
        abprf_icon_change($(this));
    });
    $(document).on('click', '.abprf_area [data-text-change]', function () {
        abprf_text_change($(this));
    });
    $(document).on('click', '.abprf_area [data-class-change]', function () {
        abprf_class_change($(this));
    });
    $(document).on('click', '.abprf_area [data-value-change]', function () {
        abprf_input_value_change($(this));
    });
    $(document).on('keyup change', '.abprf_area [data-input-text]', function () {
        let input_value = $(this).val();
        let input_id = $(this).attr('data-input-text');
        $(".abprf_area [data-input-change='" + input_id + "']").each(function () {
            $(this).html(input_value);
        });
    });
    $(document).on('keyup change', '.abprf_area [data-target-same-input]', function () {
        let input_value = $(this).val();
        let input_id = $(this).data('target-same-input');
        $(".abprf_area [data-same-input='" + input_id + "']").each(function () {
            $(this).val(input_value);
        });
    });
    $(document).on('click', '.abprf_area .date_close_icon', function (e) {
        e.preventDefault();
        let parent = $(this).closest('label');
        parent.find('input[type="text"]').datepicker("setDate", '');
        parent.find('input[type="hidden"]').val('').trigger('change');
    });
    $(document).on('click', '.abprf_area .time_close_icon', function (e) {
        e.preventDefault();
        let parent = $(this).closest('label');
        parent.find('input[type="time"]').val('').trigger('rf_trigger');
    });
}(jQuery));
document.body.addEventListener('click', function (event) {
    const targetDiv = event.target.closest('div.abprf_area [data-href]');
    if (targetDiv) {
        const url = targetDiv.getAttribute('data-href');
        const target = targetDiv.getAttribute('data-blank');
        if (url) {
            window.open(url, target ? target : '_self');
        }
    }
});
//==============================================================================Collapse & Tabs & Modal / Popup=================//
function abprf_load_tabs() {
    jQuery('div.abprf_area .abprf_tabs').each(function () {
        let tab_lists = jQuery(this).find('.tab_lists:first');
        let activeTab = tab_lists.find('[data-tabs-target].rf_active');
        let targetTab = activeTab.length > 0 ? activeTab : tab_lists.find('[data-tabs-target]').first();
        targetTab.trigger('click');
    });
}
function abprf_target_close(close_id, $this) {
    if ($this.closest('.rf_close_area').length > 0) {
        $this.closest('.rf_close_area').find('[data-close="' + close_id + '"]').slideUp(250);
    } else {
        jQuery('body').find('[data-close="' + close_id + '"]').slideUp(250);
    }
    return true;
}
function abprf_target_open(close_id, $this) {
    if ($this.closest('.rf_close_area').length > 0) {
        $this.closest('.rf_close_area').find('[data-close="' + close_id + '"]').slideDown(250);
    } else {
        jQuery('body').find('[data-close="' + close_id + '"]').slideDown(250);
    }
    return true;
}
function abprf_popup_close(target_id = '') {
    if (target_id) {
        jQuery('body').find('[data-popup="' + target_id + '"]').find('.popup_close').trigger('click');
    } else {
        jQuery('body').find('.popup_close').trigger('click');
    }
}
(function ($) {
    "use strict";
    $(document).on('click', 'div.abprf_area [data-tabs-target]', function () {
        if (!$(this).hasClass('rf_active')) {
            let tabsTarget = $(this).data('tabs-target');
            let parent = $(this).closest('.abprf_tabs');
            parent.height(parent.height());
            let tab_lists = $(this).closest('.tab_lists');
            let tab_content = parent.find('.tab_content:first');
            tab_lists.find('[data-tabs-target].rf_active').each(function () {
                $(this).removeClass('rf_active').promise().done(function () {
                    abprf_data_change($(this))
                });
            });
            $(this).addClass('rf_active').promise().done(function () {
                abprf_data_change($(this))
            });
            tab_content.children('[data-tabs="' + tabsTarget + '"]').slideDown(350);
            tab_content.children('[data-tabs].rf_active').slideUp(350).removeClass('rf_active').promise().done(function () {
                tab_content.children('[data-tabs="' + tabsTarget + '"]').addClass('rf_active').promise().done(function () {
                    abprf_load_image();
                    parent.height('auto');
                });
            });
        }
        abprf_load_more();
    });
    //================//
    $(document).on('click', 'div.abprf_area [data-target-popup]', function () {
        let $this = $(this);
        let target = $this.attr('data-active-popup', '').data('target-popup');
        $('body').addClass('_stop_scroll').find('[data-popup="' + target + '"]').addClass('in').promise().done(function () {
            abprf_load_image();
            $this.trigger('rf_trigger');
            return true;
        });
    });
    $(document).on('click', 'div.abprf_popup  .popup_close', function () {
        let $this = $(this);
        $this.closest('[data-popup]').removeClass('in');
        $('body').removeClass('_stop_scroll').find('[data-active-popup]').removeAttr('data-active-popup');
        $this.trigger('rf_trigger');
        return true;
    });
    //================//
    $(document).on('click', 'div.abprf_area [data-collapse-target]', function () {
        let currentTarget = $(this);
        let target_id = currentTarget.attr('data-collapse-target');
        let close_id = currentTarget.attr('data-close-target');
        let target = $('[data-collapse="' + target_id + '"]');
        if (target_close(close_id, target_id) && collapse_close_inside(currentTarget) && target_collapse(target, currentTarget)) {
            abprf_data_change(currentTarget);
        }
    });
    $(document).on('change', '.abprf_area select[data-collapse-target]', function () {
        let currentTarget = $(this);
        let value = currentTarget.val();
        currentTarget.find('option').each(function () {
            if ($(this).attr('data-option-target-multi')) {
                let target_ids = $(this).data('option-target-multi');
                target_ids = target_ids.toString().split(" ");
                target_ids.forEach(function (target_id) {
                    let target = get_collapse_target(currentTarget, target_id);
                    target.slideUp(350).removeClass('rf_active');
                });
            } else {
                let target = get_collapse_target($(this));
                target.slideUp('fast').removeClass('rf_active');
            }
        }).promise().done(function () {
            currentTarget.find('option').each(function () {
                let current_value = $(this).val();
                if (current_value === value) {
                    if ($(this).attr('data-option-target-multi')) {
                        let target_ids = $(this).data('option-target-multi');
                        target_ids = target_ids.toString().split(" ");
                        target_ids.forEach(function (target_id) {
                            let target = get_collapse_target(currentTarget, target_id);
                            target.slideDown(350).removeClass('rf_active');
                        });
                    } else {
                        let target = get_collapse_target($(this));
                        target.slideDown(350).removeClass('rf_active');
                    }
                }
            });
        });
    });
    function get_collapse_target(current, id = '') {
        let target_id = id !== '' ? id : current.attr('data-option-target');
        if (current.closest('.data_single_collapse').length > 0) {
            return current.closest('.data_single_collapse').find('[data-collapse="' + target_id + '"]');
        } else {
            return $('[data-collapse="' + target_id + '"]');
        }
    }
    function target_close(close_id, target_id) {
        $('body').find('[data-close="' + close_id + '"]:not([data-collapse="' + target_id + '"])').slideUp(250);
        return true;
    }
    function target_collapse(target, $this) {
        if ($this.is('[type="radio"]')) {
            target.slideDown(250);
        } else {
            target.each(function () {
                $(this).stop(true, true).slideToggle(250, function () {
                    $(this).toggleClass('rf_active');
                });
            });
        }
        return true;
    }
    function collapse_close_inside(currentTarget) {
        let parent_target_close = currentTarget.data('collapse-close-inside');
        if (parent_target_close) {
            $(parent_target_close).find('[data-collapse]').each(function () {
                if ($(this).hasClass('rf_active')) {
                    let collapse_id = $(this).data('collapse');
                    let target_collapse = $('[data-collapse-target="' + collapse_id + '"]');
                    if (collapse_id !== currentTarget.data('collapse-target')) {
                        $(this).slideUp(250).removeClass('rf_active');
                        let clsName = target_collapse.data('add-class');
                        if (clsName) {
                            target_collapse.removeClass(clsName);
                        }
                        abprf_text_change(target_collapse);
                        abprf_icon_change(target_collapse);
                    }
                }
            })
        }
        return true;
    }
}(jQuery));
//==============================================================================Form section ==============//
(function ($) {
    "use strict";
    //==============================================================================Qty inc dec================//
    $(document).on("click", "div.abprf_area .qty_decrease ,div.abprf_area .qty_increase", function () {
        let current = $(this);
        let target = current.closest('.qty_input').find('input');
        let currentValue = parseInt(target.val());
        let value = current.hasClass('qty_increase') ? (currentValue + 1) : ((currentValue - 1) > 0 ? (currentValue - 1) : 0);
        let min = parseInt(target.attr('data-min'));
        let max = parseInt(target.attr('data-max'));
        target.parents('.qty_input').find('.qty_increase , .qty_decrease').removeClass('_disabled');
        if (value < min || isNaN(value) || value === 0) {
            value = min;
            target.parents('.qty_input').find('.qty_decrease').addClass('_disabled');
        }
        if (value > max) {
            value = max;
            target.parents('.qty_input').find('.qty_increase').addClass('_disabled');
        }
        target.val(value).trigger('change').trigger('input');
    });
    //=======================================================Group checkbox ==============//
    $(document).on('click', 'div.abprf_area .custom_checkbox [data-checked]', function () {
        let $this = $(this);
        $this.toggleClass('rf_active').promise().done(function () {
            let parent = $(this).closest('.custom_checkbox');
            let value = '';
            let separator = ',';
            parent.find(' [data-checked]').each(function () {
                if ($(this).hasClass('rf_active')) {
                    let currentValue = $(this).attr('data-checked');
                    value = value + (value ? separator : '') + currentValue;
                }
            }).promise().done(function () {
                abprf_data_change($this);
                parent.find('input[type="hidden"]').val(value).trigger('rf_trigger');
            });
        });
    });
    //======================================================= radio========================//
    $(document).on('click', 'div.abprf_area  .custom_radio [data-radio]', function () {
        let parent = $(this).closest('.custom_radio');
        let $this = $(this);
        if (!$this.hasClass('rf_active')) {
            let value = $this.attr('data-radio');
            parent.find('.rf_active[data-radio]').each(function () {
                if ($(this).attr('data-close-target')) {
                    let close_id = $(this).attr('data-close-target');
                    abprf_target_close(close_id, $this);
                }
                $(this).removeClass('rf_active');
                abprf_data_change($(this));
            }).promise().done(function () {
                if ($this.attr('data-close-target')) {
                    let close_id = $this.attr('data-close-target');
                    abprf_target_open(close_id, $this);
                }
                $this.addClass('rf_active');
                abprf_data_change($this);
                parent.find('input[type="hidden"]').val(value).trigger('rf_trigger');
            });
        }
    });
    //=======================================================Switch button ==============//
    $(document).on('click', 'div.abprf_area  [data-switch]', function () {
        if ($(this).hasClass('rf_active')) {
            $(this).removeClass('rf_active').find('input[type="hidden"]').val('off').trigger('rf_trigger');
        } else {
            $(this).addClass('rf_active').find('input[type="hidden"]').val('on').trigger('rf_trigger');
        }
    });
    //=======================================================validation ==============//
    $(document).on('keyup change', 'div.abprf_area .validation_number', function () {
        let value = $(this).val();
        value = parseInt(value.replace(/\D/g, ''));
        if ($(this).attr('data-min') || $(this).attr('data-max')) {
            let min = parseInt($(this).attr('data-min'));
            let max = parseInt($(this).attr('data-max'));
            if ((min && value < min) || isNaN(value)) {
                value = min;
            }
            if (max && value > max) {
                value = max;
            }
        }
        $(this).val(value);
        return true;
    });
    $(document).on('keyup change', 'div.abprf_area .validation_price', function () {
        let n = $(this).val();
        $(this).val(n.replace(/[^\d.]/g, ''));
        return true;
    });
    $(document).on('keyup change', 'div.abprf_area .validation_id', function () {
        let n = $(this).val();
        $(this).val(n.replace(/[^\d_a-zA-Z]/g, ''));
        return true;
    });
    $(document).on('keyup change', 'div.abprf_area .validation_name', function () {
        let n = $(this).val();
        $(this).val(n.replace(/[@%'":;&_]/g, ''));
        return true;
    });
    $(document).on('keyup change', 'div.abprf_area [required]', function () {
        abprf_required($(this));
    });
    function abprf_required(input) {
        if (input.val() !== '') {
            input.removeClass('abprf_required');
            return true;
        } else {
            input.addClass('abprf_required');
            return false;
        }
    }
    //==============================================================================custom select ================//
    $(document).on("click", "div.abprf_area .abp_dropdown .dropdown_list li", function (e) {
        e.preventDefault();
        let current = $(this);
        let parent = $(this).closest('.abp_dropdown');
        let value = current.attr('data-value');
        let text = current.attr('data-text');
        parent.find('.dropdown_list').slideUp(250);
        parent.find('input[type="text"]').val(text);
        parent.find('input[type="hidden"]').val(value).trigger('rf_trigger');
    });
    $(document).on({
        keyup: function () {
            let input = $(this).val().toLowerCase();
            $(this).closest('.abp_dropdown').find('.dropdown_list li').each(function () {
                $(this).toggle($(this).attr('data-text').toLowerCase().indexOf(input) > -1);
            });
            $(this).closest('.abp_dropdown').find('.dropdown_list').slideDown(200);
        }, click: function () {
            let $this = $(this);
            let input = '';
            let target = $(this).closest('.abp_dropdown').find('.dropdown_list ');
            if (target.is(':visible')) {
                $('body').find('.abp_dropdown .dropdown_list').slideUp(250);
                let parent = $this.closest('.abp_dropdown');
                input = parent.find('input[type="text"]').val().toLowerCase();
            } else {
                $('body').find('.abp_dropdown .dropdown_list').slideUp(250);
                target.slideDown(250);
            }
            target.find('li').each(function () {
                let data = $(this).attr('data-text').toLowerCase();
                if (!input || input === data) {
                    $(this).slideDown('fast');
                }
            });
        }, blur: function (e) {
            let target = $(e.relatedTarget);
            let $this = $(this);
            let parent = $this.closest('.abp_dropdown');
            setTimeout(function () {
                if (target.closest('.abp_dropdown').length === 0) {
                    $('body').find('.dropdown_list').slideUp(250);
                    if ($this.hasClass('abprf_allow')) {
                        parent.find('input[type="hidden"]').val($this.val());
                        parent.find('input[type="text"]').val($this.val());
                    } else {
                        if (target.closest('.abp_dropdown').length === 0) {
                            let current_val = parent.find('input[type="text"]').val().toLowerCase();
                            let input = parent.find('input[type="hidden"]').val();
                            let exit = 0;
                            $this.closest('.abp_dropdown').find('.dropdown_list li').each(function () {
                                let data_value = $(this).attr('data-value');
                                let data_text = $(this).attr('data-text').toLowerCase();
                                if (input === data_value && current_val === data_text) {
                                    exit = 1;
                                }
                            }).promise().done(function () {
                                if (exit < 1) {
                                    parent.find('input[type="text"]').val('');
                                    parent.find('input[type="hidden"]').val('');
                                    parent.find('input[type="hidden"]').trigger('rf_trigger');
                                }
                            });
                        }
                    }
                }
            }, 200);
        }
    }, 'div.abprf_area .abp_dropdown input[type="text"]');
}(jQuery));
//================================================================================Filter and pagination=================//
function abprf_filter(parent) {
    abprf_spinner(parent);
    let cat_id = parent.find('[name="cat_id"]').val();
    let loc_id = parent.find('[name="loc_id"]').val();
    cat_id = cat_id ? cat_id.trim() : '';
    loc_id = loc_id ? loc_id.trim() : '';
    parent.find('.pagination_item').each(function () {
        let item = jQuery(this);
        let itemCat = item.data('cat_id') ? String(item.data('cat_id')).trim() : '';
        let itemLocRaw = item.data('loc_id') ? String(item.data('loc_id')).trim() : '';
        let itemLocArray = itemLocRaw.split(',').map(id => id.trim());
        let isCatMatch = cat_id === '' || itemCat === cat_id;
        let isLocMatch = loc_id === '' || itemLocArray.includes(loc_id);
        if (isCatMatch && isLocMatch) {
            item.removeClass('rf_off').addClass('rf_on rf_close');
        } else {
            item.addClass('rf_off').removeClass('rf_on rf_close');
        }
    }).promise().done(function () {
        let btn = parent.find('.live_pagination');
        btn.attr('data-load-more', 0);
        abprf_live_pagination(parent);
    });
}
function abprf_live_pagination(parent) {
    let btn = parent.find('.live_pagination');
    let pagination_page = parseInt(btn.attr('data-load-more')) || 0;
    let page_item = parseInt(parent.find('input[name="page_item"]').val()) || 10;
    let show_until = (pagination_page + 1) * page_item;
    let visible_count = 0;
    let is_filter_active = parent.find('.pagination_item.rf_on, .pagination_item.rf_off').length > 0;
    let total_filtered_match = parent.find('.pagination_item.rf_on').length;
    if (is_filter_active && total_filtered_match === 0) {
        parent.find('.rf_no_results').fadeIn();
    } else {
        parent.find('.rf_no_results').hide();
    }
    parent.find('.pagination_item').each(function () {
        let item = jQuery(this);
        if (!is_filter_active) {
            if (visible_count < show_until) {
                item.removeClass('rf_close');
            } else {
                item.addClass('rf_close');
            }
            visible_count++;
        } else {
            if (item.hasClass('rf_on')) {
                if (visible_count < show_until) {
                    item.removeClass('rf_close');
                } else {
                    item.addClass('rf_close');
                }
                visible_count++;
            } else {
                item.addClass('rf_close');
            }
        }
    });
    abprf_pagination_item(parent);
    if (typeof abprf_load_image === "function") {
        abprf_load_image();
    }
}
function abprf_pagination_item(parent) {
    let is_filter_active = parent.find('.pagination_item.rf_on, .pagination_item.rf_off').length > 0;
    let hidden_items;
    let total_items;
    if (!is_filter_active) {
        total_items = parent.find('.pagination_item').length;
        hidden_items = parent.find('.pagination_item.rf_close').length;
    } else {
        total_items = parent.find('.pagination_item.rf_on').length;
        hidden_items = parent.find('.pagination_item.rf_on.rf_close').length;
    }
    if (total_items === 0 || hidden_items === 0) {
        parent.find('.live_pagination').attr('disabled', 'disabled').hide();
    } else {
        parent.find('.live_pagination').removeAttr('disabled').show();
    }
    abprf_spinner_remove(parent);
}
(function ($) {
    "use strict";
    $(document).on('change', 'div.abprf_area .rf_pagination [name="cat_id"]', function () {
        let parent = $(this).closest('div.rf_pagination');
        abprf_filter(parent);
    });
    $(document).on('rf_trigger', 'div.abprf_area .rf_pagination [name="cat_id"]', function () {
        let parent = $(this).closest('div.rf_pagination');
        abprf_filter(parent);
    });
    $(document).on('change', 'div.abprf_area .rf_pagination [name="loc_id"]', function () {
        let parent = $(this).closest('div.rf_pagination');
        abprf_filter(parent);
    });
    $(document).on('rf_trigger', 'div.abprf_area .rf_pagination [name="loc_id"]', function () {
        let parent = $(this).closest('div.rf_pagination');
        abprf_filter(parent);
    });
    $(document).on('click', 'div.abprf_area  .grid_view', function () {
        let parent = $(this).closest('div.abprf_area');
        let container = parent.find('.abprf_lists');
        if (container) {
            container.addClass('view-switching');
            setTimeout(function () {
                container.removeClass('abprf_lists').addClass('abprf_grid');
                container.removeClass('view-switching');
            }, 150);
        }
        parent.find('.list_view').removeClass('rf_active');
        $(this).addClass('rf_active');
        abprf_load_more();
    });
    $(document).on('click', 'div.abprf_area  .list_view', function () {
        let parent = $(this).closest('div.abprf_area');
        let container = parent.find('.abprf_grid');
        if (container) {
            container.addClass('view-switching');
            setTimeout(function () {
                container.removeClass('abprf_grid').addClass('abprf_lists');
                container.removeClass('view-switching');
                abprf_load_more();
            }, 150);
        }
        parent.find('.grid_view').removeClass('rf_active');
        $(this).addClass('rf_active');
    });
    $(document).on('click', 'div.abprf_area .rf_pagination .live_pagination', function () {
        let parent = $(this).closest('div.rf_pagination');
        abprf_spinner(parent);
        let pagination_page = parseInt($(this).attr('data-load-more')) + 1;
        $(this).attr('data-load-more', pagination_page);
        abprf_live_pagination(parent);
    });
}(jQuery));
//================================================================================Load Bg Image=================//
function abprf_load_image(body = jQuery('body .abprf_area')) {
    body.find('[data-image-href]:visible').each(function () {
        let target = jQuery(this);
        let bg_url = target.data('image-href');
        target.attr('data-image-href', '');
        if (!bg_url || bg_url.width === 0 || bg_url.width === 'undefined') {
            bg_url = abprf_var.blank_image;
        }
        if (bg_url) {
            target.find('img').attr('src', bg_url).promise().done(function () {
                abprf_spinner_remove(target);
            });
        }
    });
    return true;
}
//=================================================================================Date picker & Sticky & Price Format & Page Scroll==============//
function abprf_load_datepicker(parent = jQuery('.abprf_area')) {
    parent.find(".abp_datepicker.hasDatepicker").each(function () {
        jQuery(this).removeClass('hasDatepicker').attr('id', '').removeData('datepicker').unbind();
    }).promise().done(function () {
        parent.find(".abp_datepicker").datepicker({
            dateFormat: abprf_var.date_format, autoSize: true, changeMonth: true, changeYear: true, //showButtonPanel: true,
            onSelect: function (dateString, data) {
                let date = data.selectedYear + '-' + ('0' + (parseInt(data.selectedMonth) + 1)).slice(-2) + '-' + ('0' + parseInt(data.selectedDay)).slice(-2);
                jQuery(this).closest('label').find('input[type="hidden"]').val(date).trigger('change');
            }
        });
    });
}
function abprf_init_all_dynamic_datepickers(newSelector = null, newConfig = null) {
    window.abprf_picker_data = window.abprf_picker_data || {};
    if (newSelector && newConfig) {
        window.abprf_picker_data[newSelector] = newConfig;
    }
    if (jQuery.isEmptyObject(window.abprf_picker_data)) {
        return;
    }
    let currentDateFormat = (typeof abprf_var !== 'undefined' && abprf_var.date_format) ? abprf_var.date_format : 'yy-mm-dd';
    jQuery.each(window.abprf_picker_data, function (selector, config) {
        let $el = jQuery(selector);
        if ($el.length) {
            if ($el.hasClass('hasDatepicker')) {
                $el.datepicker("destroy").removeClass('hasDatepicker').removeAttr('id').unbind();
            }
            $el.datepicker({
                dateFormat: currentDateFormat,
                autoSize: true,
                changeMonth: true,
                changeYear: true,
                minDate: new Date(config.minYear, config.minMonth, config.minDay),
                maxDate: new Date(config.maxYear, config.maxMonth, config.maxDay),
                beforeShowDay: function (date) {
                    var dmy = date.getDate() + "-" + (date.getMonth() + 1) + "-" + date.getFullYear();
                    if (jQuery.inArray(dmy, config.activeDates) !== -1) {
                        return [true, "enabled-date", config.txtAvail];
                    } else {
                        return [false, "disabled-date", config.txtUnavail];
                    }
                },
                onSelect: function (dateString, data) {
                    let date = data.selectedYear + '-' + ('0' + (parseInt(data.selectedMonth) + 1)).slice(-2) + '-' + ('0' + parseInt(data.selectedDay)).slice(-2);
                    jQuery(this).closest('label').find('input[type="hidden"]').val(date).trigger('change');
                }
            });
        }
    });
}
function abprf_alert($this, attr = 'alert') {
    alert($this.data(attr));
}
function abprf_page_scroll(target) {
    jQuery('html, body').animate({
        scrollTop: target.offset().top -= 150
    }, 1000);
}
function abprf_toast_msg(msg, type = 'info') {
    const icons = {success: '✅', error: '❌', warn: '⚠️', info: 'ℹ️'};
    const el = jQuery(`<div class="toast_msg_box ${type}"><span>${icons[type] || 'ℹ️'}</span><span>${msg}</span></div>`);
    jQuery('div.abprf_area .toast_msg_area').append(el).hide().fadeIn(200);
    setTimeout(() => el.fadeOut(300, () => el.remove()), 3400);
}
function abprf_wc_price_format(price) {
    if (typeof price === 'string') {
        price = Number(price);
    }
    price = price.toFixed(abprf_var.decimal_num);
    let total_part = price.toString().split(".");
    total_part[0] = total_part[0].replace(/\B(?=(\d{3})+(?!\d))/g, abprf_var.thousands_separator);
    price = total_part.join(abprf_var.currency_decimal);
    let price_text = '';
    if (abprf_var.currency_position === 'right') {
        price_text = price + abprf_var.currency_symbol;
    } else if (abprf_var.currency_position === 'right_space') {
        price_text = price + '&nbsp;' + abprf_var.currency_symbol;
    } else if (abprf_var.currency_position === 'left') {
        price_text = abprf_var.currency_symbol + price;
    } else {
        price_text = abprf_var.currency_symbol + '&nbsp;' + price;
    }
    if (abprf_var.currency_suffix) {
        price_text = price + '&nbsp;' + abprf_var.currency_suffix;
    }
    return price_text;
}
//======================================================================================Loader==============//
function abprf_spinner(target) {
    if (target.find('.abprf_spinner').length < 1) {
        target.addClass('_p_relative').append('<div class="abprf_spinner"></div>');
    }
}
function abprf_spinner_remove(target = jQuery('body')) {
    target.removeClass('_p_relative').find('.abprf_spinner').remove();
}
//=============================================================================Slider=================//
(function ($) {
    "use strict";
    $(document).on('click', '.abprf_slider [data-target-popup]', function () {
        let target = $(this).data('target-popup');
        $('body').addClass('_stop_scroll').find('[data-popup="' + target + '"]').addClass('in').promise().done(function () {
            abprf_load_image();
        });
    });
    $(document).on('click', '.abprf_slider .popup_close', function () {
        $(this).closest('[data-popup]').removeClass('in');
        $('body').removeClass('_stop_scroll');
    });
}(jQuery));
class ABPRFSlider {
    constructor(root) {
        this.root = root;
        this.sliderItems = Array.from(root.querySelectorAll('.slider_item'));
        this.sliderShow = root.querySelector('.slider_show');
        this.slideResize = root.querySelector('.slide_resize');
        this.imageIndicator = root.querySelector('.image_indicator');
        this.progressFill = root.querySelector('.progress_fill');
        this.slideCurrentNum = root.querySelector('.slide_current_num');
        this.prevItem = root.querySelector('.prev_item');
        this.nextItem = root.querySelector('.next_item');
        this.AUTOPLAY_MS = parseInt(root.dataset.autoplay) || 4500;
        this.current = 0;
        this.autoTimer = null;
        this.progressStart = null;
        this.progressRaf = null;
        this.touchStart = 0;
        this._init();
    }
    _init() {
        this.sliderItems.forEach(slide => {
            const url = slide.dataset.img;
            const img = slide.querySelector('img');
            const shimmer = slide.querySelector('.slider_loading');
            if (url && img) {
                img.src = url;
                img.onload = () => shimmer && shimmer.classList.add('hidden');
                img.onerror = () => {
                    if (shimmer) shimmer.style.background = '#1A0A2E';
                };
            }
        });
        if (this.imageIndicator) {
            this.sliderItems.forEach((slide, i) => {
                const th = document.createElement('div');
                th.className = 'thumb_item' + (i === 0 ? ' active' : '');
                th.innerHTML = `<img src="${slide.dataset.img}" alt="Thumbnail" loading="lazy" />`;
                th.addEventListener('click', () => this.navigate(i));
                this.imageIndicator.appendChild(th);
            });
        }
        this.prevItem?.addEventListener('click', () => this.navigate(this.current - 1));
        this.nextItem?.addEventListener('click', () => this.navigate(this.current + 1));
        this.sliderShow?.addEventListener('mouseenter', () => this.stopAutoplay());
        this.sliderShow?.addEventListener('mouseleave', () => this.startAutoplay());
        this.sliderShow?.addEventListener('touchstart', e => {
            this.touchStart = e.touches[0].clientX;
        }, {passive: true});
        this.sliderShow?.addEventListener('touchend', e => {
            const dx = e.changedTouches[0].clientX - this.touchStart;
            if (Math.abs(dx) > 40) this.navigate(dx < 0 ? this.current + 1 : this.current - 1);
        });
        if (this.sliderShow) {
            this.sliderShow.setAttribute('tabindex', '0');
            this.sliderShow.addEventListener('keydown', e => {
                if (e.key === 'ArrowLeft') this.navigate(this.current - 1);
                if (e.key === 'ArrowRight') this.navigate(this.current + 1);
            });
        }
        this.sliderItems[0]?.classList.add('active');
        this.setResizer(this.sliderItems[0]?.dataset.img);
        this.startAutoplay();
    }
    navigate(next) {
        const total = this.sliderItems.length;
        next = ((next % total) + total) % total;
        if (next === this.current) return;
        const prevIndex = this.current;
        this.sliderItems[prevIndex].classList.add('active_slide');
        this.sliderItems[prevIndex].classList.remove('active');
        this.current = next;
        this.sliderItems[this.current].classList.remove('active_slide');
        this.sliderItems[this.current].classList.add('active');
        this.setResizer(this.sliderItems[this.current].dataset.img);
        setTimeout(() => this.sliderItems[prevIndex].classList.remove('active_slide'), 750);
        this.updateUI();
        this.resetAutoplay();
    }
    setResizer(url) {
        if (this.slideResize && url) this.slideResize.src = url;
    }
    updateUI() {
        if (this.imageIndicator) {
            this.imageIndicator.querySelectorAll('.thumb_item')
                .forEach((t, i) => t.classList.toggle('active', i === this.current));
        }
        if (this.slideCurrentNum) {
            this.slideCurrentNum.textContent = String(this.current + 1);
        }
    }
    startProgress() {
        cancelAnimationFrame(this.progressRaf);
        if (this.progressFill) {
            this.progressFill.style.transition = 'none';
            this.progressFill.style.width = '0%';
        }
        this.progressStart = performance.now();
        const tick = now => {
            const pct = Math.min(((now - this.progressStart) / this.AUTOPLAY_MS) * 100, 100);
            if (this.progressFill) this.progressFill.style.width = pct + '%';
            if (pct < 100) this.progressRaf = requestAnimationFrame(tick);
        };
        this.progressRaf = requestAnimationFrame(tick);
    }
    startAutoplay() {
        this.stopAutoplay();
        this.startProgress();
        this.autoTimer = setTimeout(() => {
            this.navigate(this.current + 1);
            this.startAutoplay();
        }, this.AUTOPLAY_MS);
    }
    stopAutoplay() {
        clearTimeout(this.autoTimer);
        cancelAnimationFrame(this.progressRaf);
        this.autoTimer = this.progressRaf = null;
    }
    resetAutoplay() {
        this.stopAutoplay();
        this.startAutoplay();
    }
}
document.addEventListener('DOMContentLoaded', () => {
    abprf_load_more()
    document.querySelectorAll('div.abprf_area [data-rf-slider]').forEach(el => new ABPRFSlider(el));
    const rf_gallery_item = document.querySelectorAll('div.abprf_area .gallery_item');
    rf_gallery_item.forEach(item => {
        const url = item.dataset.img;
        const img = item.querySelector('img');
        if (!url || !img) return;
        img.src = url;
        img.onload = () => {
            img.classList.add('loaded');
            item.classList.add('img-loaded');
        };
        img.onerror = () => {
            item.classList.add('img-loaded');
        };
    });
    const rf_faq_item = document.querySelectorAll('div.abprf_area .faq_item');
    rf_faq_item.forEach(item => {
        const questionBtn = item.querySelector('.faq_target');
        const answerWrapper = item.querySelector('.faq_answer');
        if (!questionBtn || !answerWrapper) {
            return;
        }
        questionBtn.addEventListener('click', () => {
            const isActive = item.classList.contains('active');
            rf_faq_item.forEach(innerItem => {
                innerItem.classList.remove('active');
                innerItem.querySelector('.faq_answer').style.maxHeight = '0';
            });
            if (!isActive) {
                item.classList.add('active');
                answerWrapper.style.maxHeight = answerWrapper.scrollHeight + "px";
            } else {
                item.classList.remove('active');
                answerWrapper.style.maxHeight = '0';
            }
        });
    });
});
function abprf_load_more() {
    const containers = document.querySelectorAll('div.abprf_area .load_more');
    containers.forEach(container => {
        const textContent = container.querySelector('.load_more_content');
        const toggleBtn = container.querySelector('.load_more_action');
        if (!textContent || !toggleBtn) return;
        const textMore = toggleBtn.getAttribute('data-more') || '... Load More';
        const textLess = toggleBtn.getAttribute('data-less') || ' ....Show Less';
        if (textContent.scrollHeight <= textContent.clientHeight) {
            toggleBtn.style.display = 'none';
        } else {
            toggleBtn.style.display = 'inline';
        }
        toggleBtn.replaceWith(toggleBtn.cloneNode(true));
        const newToggleBtn = container.querySelector('.load_more_action');
        newToggleBtn.addEventListener('click', function () {
            textContent.classList.toggle('expanded');
            if (textContent.classList.contains('expanded')) {
                newToggleBtn.textContent = textLess;
            } else {
                newToggleBtn.textContent = textMore;
            }
        });
    });
}
//
// (function ($) {
//     "use strict";
//
// }(jQuery));

