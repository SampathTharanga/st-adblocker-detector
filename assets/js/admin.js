(function ($) {
    'use strict';

    function getTinyMCEContent(id) {
        try {
            var editor = window.tinyMCE && tinyMCE.get(id);
            var ta = $('textarea#' + id);
            if (ta.length && ta.is(':visible')) return ta.val();
            return editor ? editor.getContent() : ta.val();
        } catch (e) {
            return $('textarea#' + id).val();
        }
    }

    function collectFields() {
        var data = {
            content: getTinyMCEContent('st_adb_content'),
            _st_nonce: $('#_st_nonce').val()
        };
        $('.st-field').each(function () {
            var name = $(this).attr('name');
            if (!name) return;
            if ($(this).attr('type') === 'checkbox') {
                data[name] = $(this).is(':checked') ? 'true' : 'false';
            } else {
                data[name] = $(this).val();
            }
        });
        return data;
    }

    // Live opacity label
    $('input[name="bg_opacity"]').on('input', function () {
        $(this).next('o').text($(this).val());
    });

    // Save
    $(document).on('click', '#st_adb_save', function () {
        var $btn = $(this);
        var orig = $btn.html();
        $btn.html('<img src="' + st_adb.plugin_url + 'assets/img/load.gif" style="width:16px;vertical-align:middle;"> Saving...');

        var payload = collectFields();
        payload.action = 'st_adb_save';

        $.post(ajaxurl, payload, function (res) {
            $btn.html(orig);
            if (res.success) {
                showMsg(res.data, 'success');
            } else {
                showMsg(res.data || 'Error saving.', 'error');
            }
        }).fail(function () {
            $btn.html(orig);
            showMsg('Request failed.', 'error');
        });
    });

    // Reset
    $(document).on('click', '#st_adb_reset', function () {
        if (!confirm('Reset all settings to defaults?')) return;
        var $btn = $(this);
        var orig = $btn.html();
        $btn.html('<img src="' + st_adb.plugin_url + 'assets/img/load.gif" style="width:16px;vertical-align:middle;"> Resetting...');

        $.post(ajaxurl, {
            action: 'st_adb_reset',
            _st_nonce: $('#_st_nonce').val()
        }, function (res) {
            if (res.success) {
                showMsg(res.data, 'success');
                setTimeout(function () { window.location.reload(); }, 900);
            } else {
                $btn.html(orig);
                showMsg(res.data || 'Error.', 'error');
            }
        });
    });

    function showMsg(msg, type) {
        var $m = $('#st_adb_msg');
        $m.attr('class', 'st-msg st-msg-' + type).text(msg).show();
        setTimeout(function () { $m.fadeOut(); }, 3500);
    }

    // Color picker init
    $(document).ready(function () {
        if ($.fn.wpColorPicker) {
            $('.st-color-picker').wpColorPicker();
        }
    });

})(jQuery);
