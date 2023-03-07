$(function(){

    $('div.ratyScore').raty({
        readOnly: true
    });

    $(".item-review-page").on("click", function (event) {
        event.preventDefault();
        $('#paymship').html('<div class="spinner"></div>');
        $.ajax({
            url: '/?p=itemcomments&itemid=' + $('#paymship').attr('item_id') + '&from=' + $(this).attr("from"),
            success: function(data) {
                $('#paymship').html(data.reviews);
            }
        });
    });

    $('body').on('submit', 'form[name="answerToItemReview"]', function(e){
        e.preventDefault();
        e.stopImmediatePropagation();
        
        if ($(this).find('textarea[name="reviewanswertext"]').val().trim() === '') {
            showError(trans.get('not_filled_required_field'));
            return false;
        }

        var btn = $(this).find('button[type="submit"]').button('loading');

        var newAnswerElement = $(this).closest('li.collapse');
        var params = $(this).serializeArray();

        $.post($(this).attr('action'), params, function(data){
            if (data.error) {
                btn.button('reset');
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
            showMessage(trans.get('saved'));
        }, 'json');
    });

    $('.grade span.positive, .grade span.negative').on('click', function () {
        var thisButton = $(this);
        if (!thisButton.hasClass('active')) {

            thisButton.addClass('active').siblings('span.active').removeClass('active');

            var itemReviewId = thisButton.closest('div.item-review-block').data('item-review-id');
            var isPositive = !!thisButton.hasClass('positive');
            var params = {
                itemreviewid: itemReviewId,
                ispositive: isPositive
            };
            $.post('index.php?p=gradeItemReview', params, function(data){
                if (!data.error) {
                    var parentContainer = thisButton.closest('span.grade');
                    if (data.positive && data.positive != parentContainer.children('span.positive').children('span').text()) {
                        parentContainer.children('span.positive').children('span')
                            .animate({opacity:'toggle'}, 200, function () {
                                $(this).text(data.positive).animate({opacity:'toggle'}, 200);
                            });
                    }
                    if (data.negative && data.negative != parentContainer.children('span.negative').children('span').text()) {
                        parentContainer.children('span.negative').children('span')
                            .animate({opacity:'toggle'}, 200, function () {
                                $(this).text(data.negative).animate({opacity:'toggle'}, 200);
                            });
                    }
                } else {
                    thisButton.removeClass('active');
                    showMessage(data.message, true);
                }
            }, 'json');
        }
    });

    $('.collapseAnswer').on('click', function (e) {
        var scroll = $(this).closest('ul.paymshipReviews').length;

        if (scroll) {
            var offsetTop = $(this).offset().top;
            $('html, body').animate({ scrollTop: offsetTop }, 500);
        }
    });
});