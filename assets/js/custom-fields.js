jQuery(document).ready(function ($) {
    $.ajax({
        type: 'post',
        dataType: 'json',
        url: ajaxurl,
        data: {action: 'opalestate_setting_custom_fields'},
        success: function (response) {
            var arr_setting_fields = response.data;

            for (var i = 0; i < arr_setting_fields.length; i++) {
                $('#' + arr_setting_fields[i]).addClass('search-control');
                $('input[name="' + arr_setting_fields[i] + '_search_type"]').addClass('search-type-ctrl');
            }

            $('.search-type-ctrl').each(function (index, value) {
                if ($(this).prop('checked')) {
                    var val = $(this).val();
                    var name = $(this).attr('name');
                    if (val === 'select') {
                        var res = name.replace(/_search_type/g, '');
                        var res = res.replace(/_/g, '-');

                        $('.cmb2-id-' + res + '-options-value').show();
                        $('.cmb2-id-' + res + '-min-range').hide();
                        $('.cmb2-id-' + res + '-max-range').hide();
                        $('.cmb2-id-' + res + '-unit-thousand').hide();
                        $('.cmb2-id-' + res + '-default-text').hide();
                    }

                    if (val === 'text') {
                        var res = name.replace(/_search_type/g, '');
                        var res = res.replace(/_/g, '-');

                        $('.cmb2-id-' + res + '-default-text').show();
                        $('.cmb2-id-' + res + '-min-range').hide();
                        $('.cmb2-id-' + res + '-max-range').hide();
                        $('.cmb2-id-' + res + '-unit-thousand').hide();
                        $('.cmb2-id-' + res + '-options-value').hide();
                    }

                    if (val === 'range') {
                        var name = $(this).attr('name');
                        var res = name.replace(/_search_type/g, '');
                        var res = res.replace(/_/g, '-');
                        $('.cmb2-id-' + res + '-options-value').hide();
                        $('.cmb2-id-' + res + '-min-range').show();
                        $('.cmb2-id-' + res + '-max-range').show();
                        $('.cmb2-id-' + res + '-unit-thousand').show();
                        $('.cmb2-id-' + res + '-default-text').hide();
                    }
                }
            });

            $('.search-type-ctrl').on('change', function () {
                var val = $(this).val();
                var name = $(this).attr('name');
                var res = name.replace(/_search_type/g, '');
                var res = res.replace(/_/g, '-');

                if (val == 'range') {
                    $('.cmb2-id-' + res + '-options-value').hide();
                    $('.cmb2-id-' + res + '-min-range').show();
                    $('.cmb2-id-' + res + '-max-range').show();
                    $('.cmb2-id-' + res + '-unit-thousand').show();
                    $('.cmb2-id-' + res + '-default-text').hide();
                } else if (val == 'text') {
                    $('.cmb2-id-' + res + '-default-text').show();
                    $('.cmb2-id-' + res + '-options-value').hide();
                    $('.cmb2-id-' + res + '-min-range').hide();
                    $('.cmb2-id-' + res + '-max-range').hide();
                    $('.cmb2-id-' + res + '-unit-thousand').hide();
                } else {
                    $('.cmb2-id-' + res + '-options-value').show();
                    $('.cmb2-id-' + res + '-min-range').hide();
                    $('.cmb2-id-' + res + '-max-range').hide();
                    $('.cmb2-id-' + res + '-unit-thousand').hide();
                    $('.cmb2-id-' + res + '-default-text').hide();
                }
            });
        }
    });
});
