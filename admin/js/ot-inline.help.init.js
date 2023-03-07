
$(document).ready(function () {
    $('.ot_inline_help').clickover({
        'placement': function (popup, el) {
            var offset = $(el).offset();

            var popupClone = $(popup).clone();
            popupClone.attr('popupClone');
            $(el).parent().append(popupClone);
            var popupHeight = popupClone.height();
            var popupWidth = popupClone.width();
            popupClone.remove();

            if (($(window).width() - offset.left) > popupWidth) {
                //return "right";
            }

            var htmlElementsShift = $('.ot_sub_nav').outerHeight() + $('.navbar').outerHeight();
            var top = offset.top - $(window).scrollTop() + htmlElementsShift;
            var spaceBelow = $(window).height() - top;
            if (spaceBelow < top) {
                return "top";
            }

            return spaceBelow > popupHeight ? "bottom" : "top";
        }
    });
});