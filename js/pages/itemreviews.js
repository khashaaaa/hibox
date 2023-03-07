var ItemReviews = Backbone.View.extend({
    el: 'body',
    events: {
        'submit form[name="answerToItemReview"]': 'answerItemReview',
        'click .grade span.positive, .grade span.negative': 'setRating',
        'click .addItemReview': 'showAddReviewForm',
        'click .item-review-page': 'nextReviews',
        'click #showAllReviews': 'showAllReviews',
        'submit #reviewForm': 'addReview',
    },

    initialize: function() {

        $('body').on('hide.bs.modal', '.order-confirm-dialog', function() {
            var reviewForm = $('#reviewForm');
            $('#add-item-review').html(reviewForm.show());
            reviewForm.find('textarea[name="text"]').val('');
            reviewForm.find('input[name="score"]').val('');
            reviewForm.find('.file-container').remove();
            $('.button-add-review, .addItemReview').removeAttr('disabled');
            $('.button-my-review, .myReviewButton').removeAttr('disabled');
            $('.ratyScoreReview').html('');
        });

        $('body').on('shown.bs.modal', '.order-confirm-dialog', function() {
            $('.ratyScoreReview').raty();
        });

        this.render();
    },

    render: function() {
        return this;
    },

    showAllReviews: function () {
        $('[attr="tab3"]').data('review-id', '').click();
    },

    nextReviews: function(e) {
        e.preventDefault();
        var action = this.$(e.target).data('action');
        var itemId = $('.js-reviews-tab').data('item-id');
        var from = this.$(e.currentTarget).attr("from");
        $('.js-reviews-container').html('<div class="spinner"></div>');
        $.ajax({
            url: action,
            data: {
                "itemid": itemId,
                "from": from,
            },
            success: function (data) {
                $('.js-reviews-container').html(data.reviews);
            }
        });
    },


    addReview: function(e) {
        showOverlay();
        e.preventDefault();
        var reviewForm = $('#reviewForm');
        var url = reviewForm.attr('action');
        var dialog = reviewForm.closest('.modal');

        var buttons = dialog.find('.btn');
        var orderLineId = reviewForm.find('input[name="orderLineId"]').val();
        var item = $('#item' + orderLineId);
        var params = reviewForm.serializeArray();

        /* fix double click */
        if (buttons.hasClass('disabled')) {
            return false;
        }
        buttons.addClass('disabled');

        $.post(url, params, function (data) {
            if (data.error) {
                showError(data);
            } else {
                item.find('.button-add-review').css('display', 'none');
                item.find('.button-my-review').data('review-id', data.id).css('display', 'inline-block');
                showMessage(trans.get('saved'));
                $('.modal').modal('hide');
                buttons.removeClass('disabled');
            }
            hideOverlay();
            if ($('li[tab="paymship"] a[attr="tab3"]')) {
                $('a[attr="tab3"]').trigger('click');
            }
            if ($('.nav-tabs a#reviews-tab.nav-link').length) {
                $('.js-tab-reviews').removeAttr('data-type'); // При переключении типа отзывов есть проверка этого атрибута чтобы не выполнялся ajax запрос если отзывы с нужным типом уже загружены. При добавлении нового отзыва удаляем этот атрибут чтобы подгрузить внутренние отзывы
                $('.nav-tabs a#reviews-tab.nav-link').trigger('click');
            }
        }, 'json');
    },


    showAddReviewForm: function(e) {
        var item = this.$(e.currentTarget).closest('.list-products__row-item');
        var orderId = item.data('order');
        var salesLineId = item.data('sales-line-id');
        var configId = item.data('config-id');
        var itemId = this.$(e.currentTarget).data('itemid');
        var reviewForm = $('#reviewForm');
        var self = this;

        reviewForm.find('input[name="itemId"]').val(itemId);
        reviewForm.find('input[name="configId"]').val(configId);
        reviewForm.find('input[name="orderId"]').val(orderId);
        reviewForm.find('input[name="orderLineId"]').val(salesLineId);
        $('.ratyScore').raty();

        modalDialog(trans.get('new_review'), reviewForm.show(),
            function () {
                var errorFilled = false;
                if (reviewForm.find('textarea[name="text"]').val().trim() === '')
                    errorFilled = true;
                if (reviewForm.find('input[name="score"]').length > 0 && reviewForm.find('input[name="score"]').val().trim() === '')
                    errorFilled = true;

                if (errorFilled) {
                    showError(trans.get('not_entered_required_data'));
                } else {
                    self.addReview(e);
                }
                // оставить окно открытым, закрытием окна управляет
                // обработчик submit
                return false;
            },
            {
                confirm: trans.get('save'),
                cancel: trans.get('cancel')
            },
            function (body) {
                $(body).closest('.confirmDialog').addClass('order-confirm-dialog');
                $('.button-add-review').attr('disabled', 'disabled');
            }
        );
    },


    answerItemReview: function (e) {
        e.preventDefault();

        if (this.$(e.target).find('textarea[name="reviewanswertext"]').val().trim() === '') {
            showError(trans.get('not_filled_required_field'));
            return false;
        }

        var btn = this.$(e.currentTarget).find('button[type="submit"]');
        btn.prop('disabled', true);

        var newAnswerElement = this.$(e.currentTarget).closest('li.collapse');
        var params = this.$(e.currentTarget).serializeArray();

        $.post(this.$(e.currentTarget).attr('action'), params, function(data){
            if (data.error) {
                btn.button('reset');
                btn.prop('disabled', false);
                showMessage(data, true);
                return false;
            }

            var addedAnswerElement = newAnswerElement.clone(); // новый блок ответа
            addedAnswerElement.removeAttr('id').removeClass('collapse').removeClass('in'); // удаление старых атрибутов
            addedAnswerElement.find('form[name="answerToItemReview"]').remove(); // удаление формы заполнения ответа

            var addedAnswerText = newAnswerElement.find('textarea[name="reviewanswertext"]').val(); // текст нового ответа
            addedAnswerElement.append(document.createTextNode(addedAnswerText)); // добавление текста с ответом в новый блок ответа

            var filesContainer = newAnswerElement.find('.files-container').clone(true);
            filesContainer.find('.actions').remove();
            filesContainer.find('.file-container.error').remove();
            filesContainer.find('.file-container .name').remove(); // очистка полей файлов

            addedAnswerElement.append(filesContainer); // добавление файлов

            addedAnswerElement.appendTo(newAnswerElement.closest('ul')); // добавление блока ответа в список

            newAnswerElement.collapse('toggle'); // закрытие окна создания отзыва
            newAnswerElement.find('textarea[name="reviewanswertext"]').val(''); // очистка поля с текстом отзыва
            newAnswerElement.find('.file-container').remove(); // очистка полей файлов

            btn.button('reset');
            btn.prop('disabled', false);
            showMessage(trans.get('saved'));
        }, 'json');
    },

    setRating: function (e) {
        var $btn = this.$(e.currentTarget);
        if (!$btn.hasClass('active')) {
            $btn.addClass('active').siblings('.active').removeClass('active');
        }
        var params = {
            itemreviewid: $btn.closest('.js-reviews-block').data('itemReviewId'),
            ispositive: !!$btn.hasClass('js-positive')
        };
        $.post('index.php?p=gradeItemReview', params, function (data) {
            if (!data.error) {
                var $ratingConteiner = $btn.closest('.js-review-rating');

                var predefinedPositiveGrades = $ratingConteiner.children('.js-positive').children('.js-review-rating-value').text();
                var isPositiveGradeChanged = data.positive !== predefinedPositiveGrades;
                var isPositiveGrade = data.positive && isPositiveGradeChanged;
                if (isPositiveGrade) {
                    $ratingConteiner
                        .find('.js-positive .js-review-rating-value')
                        .animate({opacity:'toggle'}, 200, function () {
                            $(this).text(data.positive).animate({opacity:'toggle'}, 200);
                        });
                }

                var predefinedNegativeGrades = $ratingConteiner.children('.js-negative').children('.js-review-rating-value').text();
                var isNegativeGradeChanged = data.negative !== predefinedNegativeGrades;
                var isNegativeGrade = data.negative && isNegativeGradeChanged;
                if (isNegativeGrade) {
                    $ratingConteiner
                        .find('.js-negative .js-review-rating-value')
                        .animate({opacity:'toggle'}, 200, function () {
                            $(this).text(data.negative).animate({opacity:'toggle'}, 200);
                        });
                }
            } else {
                $btn.removeClass('active');
                showMessage(data.message, true);
            }
        }, 'json');
    },

});

$(function () {
    var itemReviews = new ItemReviews(true);
});