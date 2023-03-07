$(function () {
    $('.slider-product-item').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        fade: false,
        arrows:true,
        dots:true,
        centerMode: false,
        slide:'.slide',
    });

    $(".my-rating-done").rateYo({
        fullStar: true,
        starWidth: "14px",
        normalFill: "#b2b2b2",
        ratedFill: "#fc8215",
        spacing: "0px"
    });
});