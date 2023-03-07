var CostPage = Backbone.View.extend({
    "el": $(".pricing-cost")[0],
    "events": {
        "click .collapseCustom": "collapseCustom",
        "click #save-available_discount_mode": "saveDiscountMode"
    },
    render: function()
    {
        return this;
    },
    initialize: function()
    {
        var self = this;
        this.render();

        $('#apply_button').click(this.getBillingPeriodInfo);
    },
    collapseCustom: function (e) {
        e.preventDefault();
        var target = this.$(e.target);
        if (! $('.ot_show_collapse').attr('opened')) {
            $('.ot_show_collapse').show();//.css('height','auto');
            $('.ot_show_collapse').attr('opened','1');
        } else {
            $('.ot_show_collapse').hide();//.css('height','0px');
            $('.ot_show_collapse').removeAttr('opened');
        }

        return false;
    },
    saveDiscountMode: function(){
        var button = $('#save-available_discount_mode');
        $(button).addClass('disabled').find('.icon-ok').removeClass('icon-ok').addClass('icon-refresh');

        var availableDiscountMode = $('.available_discount_mode').val();
        $.post(
            '?cmd=pricing&do=saveCost',
            {'name': 'discountmode', 'value': availableDiscountMode},
            function (data) {
                if (! data.error) {
                    showMessage(trans.get('Notify_success'));
                } else {
                    showError(data);
                }
                $(button).find('.icon-refresh').removeClass('icon-refresh').addClass('icon-ok');
                $(button).removeClass('disabled');
            }, 'json'
        );

        return false;
    },
});

$(function(){
    new CostPage();
});
