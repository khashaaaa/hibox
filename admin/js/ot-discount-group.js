var DiscountGroupPage = Backbone.View.extend({
    "el": $("#discount-group-wrapper")[0],
    "events": {
        "click .ot_show_deletion_discont_user_dialog_modal": "confirmUserDelete",
        "click .ot_show_deletion_dialog_to_group_modal": "confirmDeleteGroup",
        "click #save-discount": "saveDiscount",
    },
    saveDiscount: function (e) {
        e.preventDefault();

        var target = this.$(e.target);
        var $button = target.button('loading');
        var action = target.closest('form').attr('action');

        if (! isValidForm()) {
            $button.button('reset');
            return false;
        }

        $.post(
            action,
            target.closest('form').serializeArray(),
            function (data) {
                if (! data.error) {
                    showMessage(trans.get('Discount_is_saved'));
                    window.location.href = target.data('link');
                } else {
                    $button.button('reset');
                    showError(data.message);
                }
            }, 'json'
        );
        return false;
    },
    confirmUserDelete: function(e) {
        e.preventDefault();
        var target;
        if ($(e.target).hasClass('icon-remove')) {
            target = $(e.target).parent();
        } else {
            target = $(e.target);
        }

        var itemName = $(target).attr('user-name');
        var userId = $(target).attr('user-id');
        var groupId = $(target).attr('user-group');
        var action = $(target).attr('action');
        var noDelete = $(target).attr('noDelete');
        var isAutomateSetted = $(target).attr('is-automate-setted');
        var content = '';

        content = renderTemplate('discount-dialog-select', {
                categories: allCategories,
                noDelete: noDelete,
                groupId: groupId,
                msg: _.template(trans.get('delete_warning'), {item: itemName}),
                item: itemName
        });

        modalDialog(trans.get('Confirm_needed'), content, function(){
            newGroupId = $("#toCategory").val();
            $.post(
                action,
                {
                    userId     : userId,
                    groupId    : groupId,
                    isAutomateSetted: isAutomateSetted,
                    newGroupId : newGroupId
                },
                function (data) {
                    if (! data.error) {
                        if (newGroupId) {
                            showMessage(trans.get('User_is_replaced'));
                        } else {
                            showMessage(trans.get('User_is_deleted'));
                        }
                        location.reload();
                    } else {
                        showError(data.message);
                    }
                }, 'json'
            );
        });

    },
    confirmDeleteGroup: function(e) {
        e.preventDefault();
        var target;
        if ($(e.target).hasClass('icon-remove-sign')) {
            target = $(e.target).parent();
        } else {
            target = $(e.target);
        }
        var groupId = $(target).attr('group-id');
        var groupName = $(target).attr('group-name');
        var action = $(target).attr('action');

        var msg = _.template(trans.get('delete_warning'), {item: escapeData(groupName)});

        modalDialog(trans.get('Confirm_needed'), msg, function(){
            $.post(
                action,
                {
                    groupId      : groupId
                },
                function (data) {
                    if (! data.error) {
                        showMessage(trans.get('Discount_is_deleted'));
                        location.reload();
                    } else {
                        showError(data.message);
                    }
                }, 'json'
            );
        });
    }
});

$(function(){
    new DiscountGroupPage();

    $('.ot-typehead-discountusers').each(function() {
        var that = this;
        var usersIds = [];
        $(this).popover({
            html: true,
            content: renderInlineEditableElement('popover-typeahead', {id: 'add-discount-user'})
        }).
            click(function() {
                var addUserTextEl = $('#add-discount-user');
                addUserTextEl.typeahead ({
                    source: function (query, process) {
                        if (! addUserTextEl.hasClass('searching')) {
                            addUserTextEl.addClass('searching');
                            return $.get('?cmd=discount&do=getUsersForDiscount&username='+query, {}, function (data) {
                                addUserTextEl.removeClass('searching');
                                usersIds = data.full;
                                return process(data.options);
                            }, 'json');
                        }
                    }
                });
                $('#add-discount-user-submit').click(function() {
                    var userId = 0;
                    $('#add-referral-user-form').hide();
                    $('#add-referral-user-loader').show();
                    $.each(usersIds, function(key, val) {
                        if (val[0] == $('#add-discount-user').val()) {
                            userId = val[1];
                        }
                    });

                    $.post(
                        $(that).attr('data-url'),
                        {
                            userId      : userId,
                            login       : $('#add-discount-user').val()
                        },
                        function (data) {
                            if (! data.error) {
                                if (! data.confirm) {
                                    $('#add-discount-user-form').show();
                                    $('#add-discount-user-loader').hide();
                                    $(that).popover('hide');
                                    showMessage(trans.get('User_added_to_discount'));

                                    if ($('.ot-typehead-discountusers').attr('do') == "refresh") {
                                        location.reload();
                                    }
                                } else {
                                    modalDialog(trans.get('Confirm_needed'), data.message, function(){
                                        $.post(
                                            '?cmd=discount&do=deleteOrReplaceUserDiscount',
                                            {
                                                userId     : data.userId,
                                                groupId    : data.groupId,
                                                newGroupId : data.newGroupId,
                                                isAutomateSetted : data.isAutomateSetted
                                            },
                                            function (data) {
                                                if (! data.error) {
                                                    $(that).popover('hide');
                                                    showMessage(trans.get('User_is_replaced'));
                                                    if ($('.ot-typehead-discountusers').attr('do') == "refresh") {
                                                        location.reload();
                                                    }
                                                } else {
                                                    showError(data.message);
                                                }
                                            }, 'json'
                                        );
                                    });
                                }
                            } else {
                                showError(data.message);
                            }
                       }, 'json'
                    );
                    return false;
                });
                $('#add-discount-user-cancel').click(function(){
                    $(that).popover('hide');
                });
            });
    });
});

function isValidForm() {
    var discountName = $('[data-check = Name]').val();
    var discount = parseFloat($('[data-check = Discount]').val());
    var minPrice = parseFloat($('[data-check = PurchaseVolume]').val());

    if (discountName == '') {
        showError(trans.get('Must_be_enter_discount_name'));
        return false;
    }

    if (discount == 0 && $('[data-check = Discount]').val().indexOf('-', 0) != -1 ) {
        showError(trans.get('Discount_value_is_wrong'));
        return false;
    }

    if (discount > 99) {
        showError(trans.get('Discount_value_must_not_be_greater_than_hundred'));
        return false;
    }

    if (minPrice < 0) {
        showError(trans.get('Minprice_must_be_greater_than_zero'));
        return false;
    }

    if (isNaN(discount)) {
        showError(trans.get('Discount_value_is_wrong'));
        return false;
    }
    
    if (isNaN(minPrice)) {
        showError(trans.get('Minprice_must_be_greater_than_zero'));
        return false;
    }    

    return true;
}