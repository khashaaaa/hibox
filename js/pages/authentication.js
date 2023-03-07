let AuthenticationView = Backbone.View.extend({
    "el": "body",
    "events": {
        "submit .js-authentication-login-form": "login",
        "submit .js-authentication-registration-form": "registration",
        "submit .js-authentication-recovery-form": "recovery",
        "click .js-open-modal-login": "openModalLogin",
        "click .js-open-modal-registration": "openModalRegistration",
        "click .js-open-modal-recovery": "openModalRecovery",
    },

    initialize: function() {
        this.render();
    },

    render: function() {
        return this;
    },

    login: function (e) {
        let form = $(e.target),
            btn = form.find('.js-submit'),
            responseWrapper = form.find('.js-response');

        btn.button('loading');
        responseWrapper.html('');

        $.post(form.attr('action'), form.serialize(), function (data) {
                if (data.error) {
                    responseWrapper.html('<div class="alert alert-danger" role="alert">' + data.message + '</div>');
                    if (typeof captchaRefresh == 'function') {
                        captchaRefresh();
                    }
                } else if (data.redirect) {
                    document.location = data.redirect;
                }
            }, 'json'
        ).complete(function () {
            btn.button('reset');
        });

        return false;
    },
    registration: function (e) {
        let self = this,
            $form = self.$el.find(e.target),
            $btn = $form.find('.js-submit'),
            $responseWrapper = $form.find('.js-response');

        $btn.button('loading');
        $responseWrapper.html('');

        $.post(
            $form.attr('action'),
            $form.serialize(),
            function (data) {
                if (data.error) {
                    $responseWrapper.html('<div class="alert alert-danger" role="alert">' + data.message + '</div>');
                    if (typeof captchaRefresh === 'function') {
                        captchaRefresh();
                    }
                } else if (data.isPhoneVerificationUsed) {
                    self.openConfirmationModal();
                } else if (data.redirect) {
                    document.location = data.redirect;
                }
            }, 'json'
        ).complete(function () {
            $btn.button('reset');
        });

        return false;
    },
    recovery: function (e) {
        let form = $(e.target);
        let btn = form.find('.js-submit');
        let responseWrapper = form.find('.js-response');
        btn.button('loading');
        responseWrapper.html('');

        $.post(form.attr('action'), form.serialize(), function (data) {
                if (data.error) {
                    responseWrapper.html('<div class="alert alert-danger" role="alert">' + data.message + '</div>');
                } else if (data.message) {
                    responseWrapper.html('<div class="alert alert-success" role="alert">' + data.message + '</div>');
                    btn.data('original-text', trans.get('recover_repeat')).html(trans.get('recover_repeat'));
                } else if (data.redirect) {
                    document.location = data.redirect;
                }
            }, 'json'
        ).complete(function () {
            btn.button('reset');
        });

        return false;
    },
    openConfirmationModal: function () {
        $('.js-modal-registration').modal('hide');
        $('.js-modal-recovery').modal('hide');
        $('.js-modal-login').modal('hide');

        $('.js-modal-confirmation').modal('show');
    },
    openModalLogin: function (e) {
        e.preventDefault();

        $('.js-modal-registration').modal('hide');
        $('.js-modal-recovery').modal('hide');
        $('.js-modal-confirmation').modal('hide');

        $('.js-modal-login').modal('show');
    },
    openModalRegistration: function (e) {
        e.preventDefault();

        $('.js-modal-login').modal('hide');
        $('.js-modal-recovery').modal('hide');
        $('.js-modal-confirmation').modal('hide');

        $('.js-modal-registration').modal('show');
    },
    openModalRecovery: function (e) {
        e.preventDefault();

        $('.js-modal-login').modal('hide');
        $('.js-modal-registration').modal('hide');
        $('.js-modal-confirmation').modal('hide');

        $('.js-modal-recovery').modal('show');
    },
});

$(function () {
    let authenticationView = new AuthenticationView();
});