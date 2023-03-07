function confirmRemove(){
    $('button.removeItem').prop('disabled', true);
    var removeButton = $(this);

	modalDialog(trans.get('need_confirm'), trans.get('sure_delete'),
        function () {
            showOverlay();
            var tr = removeButton.closest('tr');
            var orderId = tr.data('order');
            var salesLineId = tr.data('sales-line-id');

            $('html, body').animate({ scrollTop: $('tr[data-sales-line-id="' + salesLineId + '"]').offset().top - 5 }, 500);
            makeRemove(orderId, salesLineId);
        },
        { confirm: trans.get('delete'), cancel: trans.get('cancel') }, function(body) {
            $(body).closest('.confirmDialog').addClass('order-confirm-dialog');
        }
    );
}

function confirmSelectedRemove(){
    var items = $('table.basket').children('tbody').children('tr[data-role="product"]')
        .find('.productCheck input[type=checkbox]:visible:not(:disabled):checked');
    if (items.length < 1) {
        showError(trans.get('not_selected_products'));
        return true;
    }

    // пропустить строки без кнопок удаления
    items = items.filter(function() {
        return $(this).closest('tr').find('button.removeItem').length > 0;
    });
    if (items.length < 1) {
        showError(trans.get('operation_is_not_allowed_for_products'));
        return true;
    }
    $('button.removeItem').prop('disabled', true);

    modalDialog(trans.get('need_confirm'), trans.get('sure_delete'),
        function () {
            showOverlay();
            var tr, orderId;
            var salesLineId = [];
            items.each(function () {
                tr = $(this).closest('tr');
                orderId = tr.data('order');
                salesLineId.push(tr.data('sales-line-id'));
            });
            var strSalesLineIds = salesLineId.join(';');
            makeRemove(orderId, strSalesLineIds);
        },
        { confirm: trans.get('delete'), cancel: trans.get('cancel') }, function(body) {
            $(body).closest('.confirmDialog').addClass('order-confirm-dialog');
        }
    );
}

function makeRemove(orderId, salesLineId) {
    $.ajax({
        async : true,
        type: 'POST',
        dataType: 'json',
        url: $('.removeItem').eq(0).data('action'),
        data : {
            "orderId" : orderId,
            "salesLineId" : salesLineId
        },
        success: function (data) {
            if (data) {
                if (!data.result) {
                    hideOverlay();
                    showMessage(data.message, true);
                } else {
                    window.location.reload();
                }
            }
        }
    });
}

function confirmPrice(){
    $('button.confirmPrice').prop('disabled', true);
    var confirmButton = $(this);

    modalDialog(trans.get('need_confirm'), trans.get('sure_confirm_price'),
        function () {
            showOverlay();
            var tr = confirmButton.closest('tr');
            var orderId = tr.data('order');
            var salesLineId = tr.data('sales-line-id');

            $('html, body').animate({ scrollTop: $('tr[data-sales-line-id="' + salesLineId + '"]').offset().top - 5 }, 500);
            makeConfirm(orderId, salesLineId);
        },
        { confirm: trans.get('confirm_new_price'), cancel: trans.get('cancel') }, function(body) {
            $(body).closest('.confirmDialog').addClass('order-confirm-dialog');
        }
    );
}

function confirmSelectedPrice(){
    var items = $('table.basket').children('tbody').children('tr[data-role="product"]')
        .find('.productCheck input[type=checkbox]:visible:not(:disabled):checked');
    if (items.length < 1) {
        showError(trans.get('not_selected_products'));
        return true;
    }

    // пропустить строки без кнопок подтверждения
    items = items.filter(function() {
        return $(this).closest('tr').find('button.confirmPrice').length > 0;
    });
    if (items.length < 1) {
        showError(trans.get('operation_is_not_allowed_for_products'));
        return true;
    }
    $('button.confirmPrice').prop('disabled', true);

    modalDialog(trans.get('need_confirm'), trans.get('sure_confirm_price'),
        function () {
            showOverlay();
            var tr, orderId;
            var salesLineId = [];
            items.each(function () {
                tr = $(this).closest('tr');
                orderId = tr.data('order');
                salesLineId.push(tr.data('sales-line-id'));
            });
            var strSalesLineIds = salesLineId.join(';');
            makeConfirm(orderId, strSalesLineIds);
        },
        { confirm: trans.get('confirm_new_price'), cancel: trans.get('cancel') }, function(body) {
            $(body).closest('.confirmDialog').addClass('order-confirm-dialog');
        }
    );
}

function makeConfirm(orderId, salesLineId) {
    $.ajax({
        async : true,
        type: 'POST',
        dataType: 'json',
        url: $('.confirmPrice').eq(0).data('action'),
        data : {
            "orderId" : orderId,
            "salesLineId" : salesLineId
        },
        success: function (data) {
            if (data) {
                if (!data.result) {
                    hideOverlay();
                    showMessage(data.message, true);
                } else {
                    window.location.reload();
                }
            }
        }
    });
}

function cancelOrder(orderId){
    modalDialog(trans.get('need_confirm'), trans.get('sure_cancel'),
        function (body) {
            $.ajax({
                async : true,
                type: 'POST',
                dataType: 'json',
                url: $('#cancelOrder').data('action'),
                data : {
                    "orderId" : orderId
                },
                success: function (data) {
                    if (data && data.result) {
                    	location.reload();
                    }
                }
            }); 
        },
        { confirm: trans.get('cancel_order'), cancel: trans.get('cancel') }, function(body) {
            $(body).closest('.confirmDialog').addClass('order-confirm-dialog');
        }
    );
}

function closeLineOrder(currentTarget){
    var tr = $(currentTarget).closest('tr');
    var orderId = tr.data('order');
    var salesLineId = tr.data('sales-line-id');

    showOverlay();
    $.post('index.php?p=closeLineOrder', {'orderId':orderId, 'salesLineId':salesLineId}, function(data){
        hideOverlay();
        if (data.error) {
            showError(data);
            return false;
        }
        $('#confirmReceiptGoods-'+salesLineId).css('display', 'none');
        tr.find('.addItemReview').css('display', 'block');
        showMessage(trans.get('saved'));
    });
}

function getReview(currentTarget){
    showOverlay();
    $('.myReviewButton').attr('disabled', 'disabled');
    var reviewId = $(currentTarget).data('review-id');
    var url = $(currentTarget).attr('href');

    $.get(url, {'reviewId':reviewId}, function(data){
        if (data.error) {
            $('.myReviewButton').removeAttr('disabled');
            showError(data);
            return false;
        }
        var reviewBlock = $('#myReview');
        reviewBlock.html(data.review).show();

        modalDialog(trans.get('your_review'), reviewBlock, function (body) {},
            { confirm: false, cancel: trans.get('cancel') }, function(body) {
                $(body).closest('.confirmDialog').addClass('order-confirm-dialog');
            }
        );
    }, 'json').complete(function() {
        hideOverlay();
    });
    $('body').on('hide.bs.modal', '.order-confirm-dialog', function() {
        var reviewForm = $('#myReview');
        $('.modal-body').html(reviewForm.show());
        reviewForm.find('textarea[name="text"]').val('');
        reviewForm.find('input[name="score"]').val('');
        reviewForm.find('.file-container').remove();
        $('.button-add-review').removeAttr('disabled');
        $('.button-my-review').removeAttr('disabled');
    });
}

function getPackageTracking(e) {
    e.preventDefault();

    var self = this,
        $btn = $(e.currentTarget),
        packageId = $btn.data('packageId'),
        action = $btn.data('action');

    $.post(action,{'packageId': packageId}, function (data) {
        if (data.error) {
            showError(data.message);
        } else {
            if (data.externalTrackingUrl) {
                $btn.removeClass('js-package-tracking')
                    .attr('href', data.externalTrackingUrl)
                    .attr('target', '_blank')
                    .off('click', self);
                window.open(data.externalTrackingUrl, "_blank");
            } else {
                var tracking = trans.get('no_tracking');

                if (data.packageTrackingCheckpoints) {
                    var $table = $('.js-package-tracking-table').clone().show(),
                        $lines = $table.find('.js-package-tracking-table-lines'),
                        $lineSkeleton = $lines.find('.js-package-tracking-table-line');

                    $.each(data.packageTrackingCheckpoints, function (i, checkpoint) {
                        var $line = $lineSkeleton.clone().show();
                        $line.find('.js-package-tracking-table-line-time').html(checkpoint.time);
                        $line.find('.js-package-tracking-table-line-status').html(checkpoint.status);
                        $line.find('.js-package-tracking-table-line-location').html(checkpoint.location);
                        $lines.append($line);
                    });

                    tracking = $table;
                }

                modalDialog(trans.get('tracking'), tracking, null, {'cancel': trans.get('close'), 'confirm': false});
            }
        }
    });
}

function showOverlay(){
    var h = $('#overlay').parent().height();
    var w = $('#overlay').parent().width();
    $('#overlay').css({
        height: h+'px',
        width: w+'px',
        display: 'block'
    });
}
function hideOverlay(){
    $('#overlay').hide();
}

$(document).ready(function(){
    $('.checkAll')
        .on('click', function () {
            var toogleButton = $(this);
            $(this)
                .closest('table').children('tbody').children('tr[data-role="product"]')
                .find('.productCheck input[type=checkbox]:visible:not(:disabled)')
                .prop('checked', toogleButton.is(':checked'));
        });

    var availableCheckboxes = $('table.basket').children('tbody').children('tr[data-role="product"]')
        .find('.productCheck input[type=checkbox]:visible:not(:disabled)');
    var confirmButtons = $('table.basket').children('tbody').children('tr[data-role="product"]')
        .children('td.row-action').children('button.confirmPrice ');
    /* Выбор чекбоксов по умолчанию */
    if (availableCheckboxes.length === confirmButtons.length) {
        $('.checkAll').click();
    } else {
        confirmButtons.closest('tr').find('.productCheck input[type=checkbox]').prop('checked', true);
    }

    $('button.confirmPrice').on('click', confirmPrice);
    $('button.removeItem').on('click', confirmRemove);

    $('body').on('hidden.bs.modal', '.order-confirm-dialog', function() {
        $('button.confirmPrice:disabled').prop('disabled', false);
        $('button.removeItem:disabled').prop('disabled', false);
    });



    $(".pictures").colorbox({photo:true});
    
    if ($('tr.needConfirm').length > 0) {
    	$('html, body').animate({ scrollTop: $('tr.needConfirm:first').offset().top }, 500);
    	$().toastmessage('showToast', {'text': trans.get('need_confirm'), 'sticky' : true, 'type': 'error'});
    }
    
    if ($('.message .new').length > 0) {
    	$('html, body').animate({ scrollTop: $('.message .new:first').offset().top }, 500);
    }
    
    $('.add-comment-btn').click(function(e){
    	var form = $(e.currentTarget).closest('.message-form');
    	var msg = $('.message-text', form).val();
    	var orderId =  $(form).data('orderid');
    	var ticketId =  $(form).data('ticketid');

    	
    	if (msg.length == 0) {
    		return false;
    	}
    	
    	$.ajax({
            async : true,
            type: 'POST',
            dataType: 'json',
            url: $('.add-comment-btn').eq(0).data('action'),
            data : {
                "orderId" : orderId,
                "ticketId": ticketId,
                "message": msg
            },
            success: function (data) {
                if (data && data.result) {
                	$('.message-text', form).val('');
                	$(form).after(
		                    '<div class="message"><strong><i class="icon-reply muted"></i>&nbsp;' + data.user + '</strong>&nbsp;'+
		                    '<span class="message-time">' + data.creationDate + '</span>'+
		                    '<span class="message-text">' + msg + '</span></div>'                          
                	);
                }
            }
        }); 
    });

    $('.js-package-tracking').on('click', getPackageTracking);
});