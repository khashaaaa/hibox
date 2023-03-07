let CalculatorView = Backbone.View.extend({
    el: '.js-content-main',
    events: {
        'submit .js-calculator-form': 'getDeliveriesTable',
    },

    initialize: function() {
        this.render();
    },

    render: function() {
        return this;
    },

    getDeliveriesTable: function (e) {
        e.preventDefault();
        let self = this;
        let form = self.$(e.currentTarget);
        let btn = form.find('.js-submit');
        let pageUrl = form.data('page-url');
        let ajaxUrl = form.data('ajax-url');

        btn.button('loading');
        self.$('.js-deliveries-table').html('');

        $.post(ajaxUrl, form.serialize(), function (data) {
                if (data.error) {
                    showError(data);
                } else {
                    self.$('.js-deliveries-table').html(data.html);
                }
            }, 'json'
        ).always(function () {
            btn.button('reset');
        });

        let separator = pageUrl.indexOf('?') === -1 ? '?' : '&';
        let newPageUrl = pageUrl + separator + form.serialize();
        window.history.replaceState('', '', newPageUrl);
    }
});

$(function () {
    let calculatorView = new CalculatorView();
});