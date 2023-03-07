var UsersProfile = new Backbone.Collection();
var UsersProfilePage = Backbone.View.extend({
    "el": ".users-profile-wrapper",
    "events": {
        "click #generatePassword span, #generatePassword i": "generatePassword",
        "click #cancelGeneratePassword": "cancelGeneratePassword",
        "click #saveNewPassword": "saveNewPassword",
        "click #enrollMoneyBtn, #withdrawMoneyBtn": "updateAccount",
        "click #toggleEnrollForm": "toggleEnrollForm",
        "click #toggleWithdrawForm": "toggleWithdrawForm",
        "click a[href=#customer-account-tab]" : "showAccountInfoTab",
        "click a[href=#customer-orders-tab]" : "showOrdersListTab",
        "click #showUserLogBtn" : "showUserLog",
        "click #hideUserLogBtn" : "hideUserLog"
    },
    render: function()
    {
        return this;
    },
    initialize: function()
    {
        this.userId = $.deparam.querystring().id;
        this.userLogin = $('#userLogin').val();
        this.$(this.$('#toggleEnrollForm').data('target')).on('hidden', this.onEnrollFormHidden);
        this.$(this.$('#toggleEnrollForm').data('target')).on('shown', this.onEnrollFormShown);
        this.$(this.$('#toggleWithdrawForm').data('target')).on('hidden', this.onWithdrawFormHidden);
        this.$(this.$('#toggleWithdrawForm').data('target')).on('shown', this.onWithdrawFormShown);
        this.render();
    },
    generatePassword: function (ev) {
        var password = '';
        for (var i = 0; i < 16; i++) {
            password += "abcdefhjmnpqrstuvwxyz23456789ABCDEFGHJKLMNPQRSTUVWYXZ".charAt(Math.floor(Math.random()*53));
        }
        this.$('#generatePassword input').val(password);
    },
    cancelGeneratePassword: function () {
        this.$('#generatePassword input').val('');
    },
    showOrdersListTab: function(ev){
        var targetDiv = $($(ev.currentTarget).attr('href'));
        if (targetDiv.hasClass('profile-orders-loaded')) {
            return;
        }
        $.post(
            '?cmd=users&do=getUserOrders',
            {'userId': User.id, 'page': pageParams.number},
            function (data) {
                if (data.error) {
                    showError(data);
                } else {
                    if (data.html) {
                        targetDiv.find('tbody').empty().html(data.html);
                        targetDiv.addClass('profile-orders-loaded');
                        targetDiv.find('.pagination-orders').html(data.pagination);
                    }
                }
            },
            'json'
        );
    },
    showAccountInfoTab: function(ev){
        var targetDiv = $($(ev.currentTarget).attr('href'));
        if (targetDiv.hasClass('profile-account-loaded')) {
            return;
        }
        var self = this;
        $.post('?cmd=users&do=getAccountInfo', {'userId' : User.id}, function (data) {
            if (data.error) {
                showError(data);
            } else {
                if (data.html) {
                    targetDiv.find('#ajax-account-content').empty().html(data.html);
                    targetDiv.addClass('profile-account-loaded');
                }
            }
            self.initialize();
        }, 'json');
    },
    saveNewPassword: function (ev) {
        var form = self.$(ev.target).parents('form:first');
        form.find('button').attr('disabled', 'disabled');
        $.post('?cmd=users&do=savePassword&id=' + this.userId,
            {"Password" : this.$('#generatePassword input').val(), "Id": this.userId},
            function (data) {
                form.find('button').removeAttr('disabled');
                if (! data.error) {
                    form.find('#cancelGeneratePassword').trigger('click');
                    showMessage(data.message);
                    //$('.user-pass-recover-feedback-message').clone().slideDown();
                } else {
                    showError(data.message);
                }
            }, 'json'
        );
    },
    updateAccount: function (ev) {
        var self = this;
        var form = self.$(ev.target).parents('form:first');
        form.find('button').attr('disabled', 'disabled');
        $.post('?cmd=users&do=updateAccount&id=' + self.userId + '&login=' + self.userLogin,
            form.serialize(),
            function (data) {
                if (! data.error && data.userAccount) {
                    self.$('#accountAvailableAmount').text(data.userAccount.AvailableCust + ' ' + data.userAccount.CurrencySignCust);
                    form.find(':input').not(':button, :submit, :reset, :hidden').val('');
                    self.$('button[data-target*="' + form.parent().attr('class').split(' ')[0] + '"]').trigger('click');
                    if (data.moneyHistory) {
                        $.get('templates/users/profile/account/historyItem.html', function (tpl) {
                            var newHistoryItem = _.template(tpl, {transaction: _.first(data.moneyHistory.TransList)});
                            self.$('#accountOperationsHistory').css('visibility', 'visible').find('tbody').prepend(newHistoryItem);
                            self.$('#noAccountOperationsFound').remove();
                        });
                    }
                    showMessage('undefined' !== typeof data.message ? data.message : trans.get('Notify_success'));
                } else {
                    showError('undefined' !== typeof data.message ? data.message : trans.get('Notify_error'));
                }
                form.find('button').removeAttr('disabled');
            }, 'json'
        );
    },
    toggleEnrollForm: function (ev) {
        this.$(this.$('#toggleWithdrawForm').data('target')).css('height', '0px').removeClass('in');
        this.$('#toggleWithdrawForm').removeClass('active');
    },
    toggleWithdrawForm: function (ev) {
        this.$(this.$('#toggleEnrollForm').data('target')).css('height', '0px').removeClass('in');
        this.$('#toggleEnrollForm').removeClass('active');
    },
    onEnrollFormHidden: function(ev) {
        $('#toggleEnrollForm').removeClass('active');
    },
    onWithdrawFormHidden: function(ev) {
        $('#toggleWithdrawForm').removeClass('active');
    },
    onEnrollFormShown: function(ev) {},
    onWithdrawFormShown: function(ev) {},


    /**
     * Выводит лог действий оператора на странице пользователя
     */
    showUserLog: function () {

        $('#showUserLogBtn').addClass('hidden');
        $('#hideUserLogBtn').removeClass('hidden');
        $('#userLogWrapper').removeClass('hidden');

        var options = {
            "ajax": '?cmd=reports&do=userLog&id=' + this.userId,
            columnDefs: [
                {type: 'de_datetimesec', targets: 0}
            ],
            retrieve: true
        };

        if(currentAdminLang != 'en') {
            options.language = {
                url: 'js/vendor/DataTables/js/i18n/' + currentAdminLang + '.lang.json'
            }
        }

        $('#userLog').dataTable(options);

        destination = $('#userLogWrapper').offset().top;
        $('body').animate({scrollTop: destination}, 1100);

        return false;
    },

    hideUserLog: function () {

        $('#showUserLogBtn').removeClass('hidden');
        $('#hideUserLogBtn').addClass('hidden');
        $('#userLogWrapper').addClass('hidden');

        return false;
    }
});

$(function(){
    var UF = new UsersProfilePage();
});
