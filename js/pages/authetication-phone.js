let AuthenticationPhoneView = Backbone.View.extend({
    "el": "body",
    "events": {
        "click .js-authentication-change": "authenticationMethodChange",
        "submit .js-confirmation-form": "confirmation",
    },

    initialize: function() {
        if (this.$el.find('.js-authentication-method-chosen').length) {
            this.$el
                .find('.js-authentication-email.js-authentication-change')
                .click();
        }
        this.render();
    },

    render: function() {
        return this;
    },

    authenticationMethodChange: function (e) {
        let method = this.$(e.currentTarget).data('method'),
            $methodChosen = this.$('.js-authentication-method-chosen'),
            $currentMethod = this.$('.js-authentication-' + method),
            $methods = this.$('.js-authentication-method, .js-authentication-change'),
            $inputs = $methods.find('.js-authentication-method-input');

        $methods.hide();
        $inputs.removeAttr('required');
        $inputs.val('');

        $currentMethod.show().find('.js-authentication-method-input').attr('required', 'required');
        $methodChosen.val(method);
    },

    confirmation: function (e) {
        e.preventDefault();

        let $form = this.$el.find(e.currentTarget),
            $btn = $form.find('.js-confirmation-form-btn'),
            $err = this.$el.find('.js-confirmation-error');

        $btn.addClass('disabled').attr('disabled', 'disabled');
        $.post(
            $form.attr('action'),
            $form.serializeArray(),
            function (response) {
                $err.hide();
                $btn.removeClass('disabled').removeAttr('disabled');

                if (response.error) {
                    $err.show();
                    $err.find('.js-confirmation-error-message').html(response.message);
                } else if (response.redirect) {
                    window.location.href = response.redirect;
                }
            }
        );
    },
});

$(function () {
    let authenticationPhoneView = new AuthenticationPhoneView();
});