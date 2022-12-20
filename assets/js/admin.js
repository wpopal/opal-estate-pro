jQuery(document).ready(function ($) {

    /// apply select2 style 
    $('select.cmb2_select').select2();

    function load_select2_member(id, action) {
        $(id).select2({
            width: '100%',
            ajax: {
                url: ajaxurl + "?action=" + action,
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    return {
                        q: params.term, // search term
                        page: params.page
                    };
                },
                processResults: function (data, params) {
                    params.page = params.page || 1;

                    return {
                        results: data.items,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
                cache: true
            },
            placeholder: 'Search for a repository',
            escapeMarkup: function (markup) {
                return markup;
            }, // let our custom formatter work
            minimumInputLength: 1,
            templateResult: formatRepo,
            templateSelection: formatRepoSelection
        });
    }

    load_select2_member('#opalestate_ppt_agent', 'opalestate_search_agents');
    load_select2_member('#opalestate_ppt_agency', 'opalestate_search_agencies');
    load_select2_member('#p-assignment #post_author_override', 'opalestate_search_property_users');
    load_select2_member('.opalestate-customer-search', 'opalestate_search_property_users');

    function formatRepo(repo) {
        if (repo.loading) {
            return repo.text;
        }
        var markup = "<div class='select2-result-repository clearfix'>" +
            "<div class='select2-result-repository__avatar'><img width=\"50\" src='" + repo.avatar_url + "' /></div>" +
            "<div class='select2-result-repository__meta'>" +
            "<div class='select2-result-repository__title'>" + repo.full_name + "</div>";
        markup += "</div></div>";
        return markup;
    }

    function formatRepoSelection(repo) {
        return repo.full_name || repo.text;
    }

    // Ajax user search
    $('.opalestate-ajax-user-search').on('keyup', function () {
        var user_search = $(this).val();
        var exclude = '';

        if ($(this).data('exclude')) {
            exclude = $(this).data('exclude');
        }

        $('.opalestate-ajax').show();
        data = {
            action: 'opalestate_search_users',
            user_name: user_search,
            exclude: exclude
        };

        document.body.style.cursor = 'wait';

        $.ajax({
            type: "POST",
            data: data,
            dataType: "json",
            url: ajaxurl,
            success: function (search_response) {
                $('.opalestate-ajax').hide();
                $('.opalestate_user_search_results').removeClass('hidden');
                $('.opalestate_user_search_results span').html('');
                $(search_response.results).appendTo('.opalestate_user_search_results span');
                document.body.style.cursor = 'default';
            }
        });
    });

    $('body').on('click.opalestateSelectUser', '.opalestate_user_search_results span a', function (e) {
        e.preventDefault();
        var login = $(this).data('login');
        $('.opalestate-ajax-user-search').val(login);
        $('.opalestate_user_search_results').addClass('hidden');
        $('.opalestate_user_search_results span').html('');
    });

    $('body').on('click.opalestateCancelUserSearch', '.opalestate_user_search_results a.opalestate-ajax-user-cancel', function (e) {
        e.preventDefault();
        $('.opalestate-ajax-user-search').val('');
        $('.opalestate_user_search_results').addClass('hidden');
        $('.opalestate_user_search_results span').html('');
    });

    /**
     *
     */
    function open_media(field) {

        var media = wp.media({
            title: 'Choose an image',
            button: {
                text: 'Select'
            },
            multiple: false
        });

        media.open();

        media.on('select', function () {
            var selection = media.state().get('selection');

            var attachment = selection.first().toJSON();

            //var attach = wp.media.attachment( cmb.attach_id );
            //  attach.fetch();
            //  selection.set( attach ? [ attach ] : [] );
            $('input', field).val(attachment.id);

            if ($(field.data('related')).length > 0) {
                $(field.data('related')).attr('src', attachment.url);
            } else if ($('img', field).length > 0) {
                $('img', field).attr('src', attachment.url);
            }
        })
            .on('open', function () {

            });

    }

    $('.media-view-upload-button').click(function () {
        var field = $(this).parent();
        open_media(field);
    });

    $('.media-view-remove-button').click(function () {
        var field = $(this).parent();
        $('input', field).val('');
        $('img', field).attr('src', $('img', field).data('placeholder'));
    });
    /**
     *
     */
});
