var BannersPage = Backbone.View.extend({
    "el": $("#banners-wrapper")[0],

    "events": {
        "click .ot_show_deletion_dialog": "confirmBannerDelete",
        "click .saveBanner": "saveBanner",
        "click .saveBannerSort": "saveBannerSort"
    },

    initialize: function() {
        if ($('#uploaded_logo').length) {
            $('span.fileinput-button.disabled').removeClass('disabled');
            $('#uploaded_logo').prop('disabled', false).css('cursor', 'pointer');
        }
        if ($('#banners-wrapper ol#banners-sortable').length) {
            var banners = document.getElementById('banners-sortable');
            var bannersSortable = new Sortable.create(banners, {
                handle: '.sortable_handler',
                animation: 150
            });
        }
        initializeTinyMCE('#bannerName, #bannerContent');
    },

    confirmBannerDelete: function(e) {
        e.preventDefault();
        var target;
        if ($(e.target).hasClass('icon-remove')) {
            target = $(e.target).parent();
        } else {
            target = $(e.target);
        }
        var bannerId = $(target).attr('banner-id');
        var bannerName = $(target).attr('banner-name');
        var action = $(target).attr('action');
        var msg = _.template(trans.get('delete_warning'), {item: escapeData(bannerName)});
        modalDialog(trans.get('Confirm_needed'), msg, function(){
            $.post(
                action,
                { bannerId : bannerId },
                function (data) {
                    if (! data.error) {
                        showMessage(trans.get('Banner_is_deleted'));
                        $('.banner_sort li[data-id="' + bannerId + '"]').remove();
                    } else {
                        $button.button('reset');
                        showError(data.message);
                    }
                }, 'json'
            );
        });

    },

    saveBanner: function (e) {
        e.preventDefault();
        if (($('#existing_logo').val() == '' || $('#existing_logo').val() == undefined) && ($('#uploaded_logo').val() == '' || $('#uploaded_logo').val() == undefined) ) {
            showError(trans.get('contents::You_must_upload_the_image'));
            return false;
        }

        var target = this.$(e.target);
        var $form = target.closest('form');
        var $button = target.button('loading');
        var action = target.closest('form').attr('action');
        var name = tinyMCE.editors[0].getContent();
        var content = tinyMCE.editors[1].getContent();
        $('#bannerName', $form).val(name);
        $('#bannerContent', $form).val(content);
        $form.ajaxSubmit({
            url     :   action,
            type    :   'POST',
            dataType:   'json',
            success :   function(data) {
                if (! data.error) {
                    showMessage(trans.get('Banner_is_saved'));
                    window.location.href = target.data('link');
                } else {
                    $button.button('reset');
                    showError(data.message);
                }
             }
        });

        return false;
    },

    saveBannerSort: function (e) {
        e.preventDefault();
        var target = this.$(e.target);
        var action = $(target).attr('action');
        var params = new Array();
        if (! target.hasClass('disabled')) {
            var $button = target.button('loading');
            $.each($('.banner_sort > li'), function( index, value ) {
                params.push($(value).attr('data-id'));
            });
            $.post(
                action,
                { sort : params },
                function (data) {
                    if (! data.error) {
                        showMessage(trans.get('Items_order_saved'));
                        $button.button('reset');
                        target.addClass('disabled');
                    } else {
                        $button.button('reset');
                        showError(data.message);
                    }
                }, 'json'
            );
        }
        return false;
    }
});

$(function(){
    var bannerPage = new BannersPage();
});
