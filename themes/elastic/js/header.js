var HeaderView = Backbone.View.extend({
    el: '.js-header',
    events: {
        'click .header-delivery .user-preference ul li': 'changeUserPreference',
        'click .header-delivery .select-language li': 'changeLanguage',
        'click .header-search .form-select.select-img .dropdown-menu li': 'changeProvider',
        'submit .search-form': 'showOverlay'
    },

    initialize: function() {
        this.render();
    },

    render: function() {
        this.$(document).ready(function () {
            $('.menu__links').addClass('slow_hide');
            return this;
        });
    },

    changeUserPreference: function (e) {
        this.showOverlay();
        var block = this.$(e.currentTarget).closest('.user-preference');
        var action = block.data('action');
        var data = {};
        data[block.data('preference')] = this.$(e.currentTarget).data('value');

        $.post(
            action,
            data,
            function () {
                window.location.reload();
            }
        );
    },

    changeLanguage: function (e) {
        this.showOverlay();
        var language = $(e.currentTarget);
        window.location.href = language.data('href');
    },

    changeProvider: function (e) {
        var provider = $(e.currentTarget);
        var alias = provider.data('alias');
        var searchForm = this.$('.search-form');

        if (provider.data('image-search')) {
            this.$('#photo-search').css('display', 'inline-block');
        } else {
            this.$('#photo-search').css('display', 'none');
        }

        searchForm.find('input[name="Provider"]').val(alias);

        if (searchForm.find('#box-search-form_search').val() || (provider.data('image-search') && searchForm.find('input[name="imageId"]').val())) {
            searchForm.submit();
        } else {
            provider.closest('.form-select.select-img').find('#choose_provider ul li').html(provider.html());
        }
    },

    showOverlay: function () {
        $('#overlay-no-preloader .message').html('');
        $('#overlay-no-preloader').show();
    },
});

$(function () {
    var headerView = new HeaderView();
});
