jQuery(document).ready(function ($) {

    /**
     * Country select.
     */
    var $country_el = $('.opalestate-submission-form #opalestate_ppt_location, [name="location"],' +
            ' [name="opalestate_ofe_location"], [name="opalestate_agt_location"], [name="opalestate_user_location"]'),
        $state_el = $('.opalestate-submission-form #opalestate_ppt_state, [name="state"],' +
            ' [name="opalestate_ofe_state"], [name="opalestate_agt_state"], [name="opalestate_user_state"]'),
        $city_el = $('.opalestate-submission-form #opalestate_ppt_city, [name="city"],' +
            ' [name="opalestate_ofe_city"], [name="opalestate_agt_city"], [name="opalestate_user_city"]');

    $country_el.each(function () {
        if ($(this).val() != '' && $(this).val() != '-1') {
            opalestate_ajax_get_state_by_country($(this));
        }
    });

    $country_el.on('change', function () {
        opalestate_ajax_get_state_by_country($(this));
    });

    $state_el.on('change', function () {
        opalestate_ajax_get_city_by_state($(this));
    });

    function opalestate_ajax_get_state_by_country($el) {
        var country = $el.val();
        var is_search = 0;

        if ($el.closest('.opalestate-search-form').length !== 0) {
            is_search = 1;
        }

        var opalAjaxUrl = opalestate_get_ajax_url();

        $.ajax({
            type: 'POST',
            url: opalAjaxUrl,
            data: {
                'action': 'opalestate_ajax_get_state_by_country',
                'country': country,
                'is_search': is_search
            },
            success: function (data) {
                var old_selected = $state_el.val();
                var selected = is_search ? '-1' : '';
                if (old_selected != '' && old_selected != '-1') {
                    $.each($.parseJSON(data), function (key, value) {
                        if (old_selected == value.id) {
                            selected = value.id;
                        }
                    });
                }

                $state_el.empty();
                $state_el.select2({
                    data: $.parseJSON(data)
                });
                $state_el.val(selected).trigger('change');
            }
        });
    }

    function opalestate_ajax_get_city_by_state($el) {
        var state = $el.val();
        var is_search = 0;

        if ($el.closest('.opalestate-search-form').length !== 0) {
            is_search = 1;
        }

        var opalAjaxUrl = opalestate_get_ajax_url();

        $.ajax({
            type: 'POST',
            url: opalAjaxUrl,
            data: {
                'action': 'opalestate_ajax_get_city_by_state',
                'state': state,
                'is_search': is_search
            },
            success: function (data) {
                var old_selected = $city_el.val();
                var selected = is_search ? '-1' : '';
                if (old_selected != '' && old_selected != '-1') {
                    $.each($.parseJSON(data), function (key, value) {
                        if (old_selected == value.id) {
                            selected = value.id;
                        }
                    });
                }

                $city_el.empty();
                $city_el.select2({
                    data: $.parseJSON(data)
                });
                $city_el.val(selected).trigger('change');
            }
        });
    }

    function opalestate_get_ajax_url() {
        var opalAjaxUrl = '';
        if (typeof ajaxurl != 'undefined') {
            opalAjaxUrl = ajaxurl;
        } else {
            opalAjaxUrl = opalesateJS.ajaxurl;
        }

        return opalAjaxUrl;
    }
});
