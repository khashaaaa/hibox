/* tabs */
$('ul.tabs__caption').on('click', 'li:not(.active)', function() {
    $(this)
      .addClass('active').siblings().removeClass('active')
      .closest('div.tabs').find('div.tabs__content').removeClass('active').eq($(this).index()).addClass('active');
});

$('ul.tabs__caption-inside').on('click', 'li:not(.active)', function() {
    $(this)
      .addClass('active').siblings().removeClass('active')
      .closest('div.tabs-inside').find('div.tabs__content-inside').removeClass('active').eq($(this).index()).addClass('active');
});


$(".link-category").on("click", function(){
    $(this).nextAll(".header-category-list").toggle();
});

$(".header-search .search-icon").on("click", function(){
    $(this).next('form').toggleClass('active');
    $(".form-overlay").show();
});
$(".form-overlay").on("click", function(){
    $(this).hide();
    $('.header-search form').removeClass('active');
});

$('body').on('click', '.number__minus', function (e) {
    var $btn = $(e.currentTarget);

    if ($btn.hasClass('disabled_button')) {
        return false;
    }

    var $input = $btn.parent().find('input');
    var min = $input.attr('min');
    var count = parseInt($input.val()) - 1;

    min = (typeof min !== 'undefined') ? min : 1;
    count = count < min ? min : count;

    $input.val(count);
    $input.change();
});

$('body').on('click', '.number__plus', function (e) {
    var $btn = $(e.currentTarget);
    var $input = $btn.parent().find('input');

    if ($btn.hasClass('disabled_button')) {
        return false;
    }

    $input.val(parseInt($input.val()) + 1);
    $input.change();
});

$(window).scroll(function(){
    if ( $(this).scrollTop() > 0 ) {
        $('header').addClass('header-fixed');
    } else {
        $('header').removeClass('header-fixed');
    }
})

$('.number-input').bind("change keyup input click", function() {
  if (this.value.match(/[^0-9]/g)) {
  this.value = this.value.replace(/[^0-9]/g, '');
  }
});

$(".row-dropdown-arrow").on("click", function(){
    $(this).toggleClass('active');
    $(this).parents('div').toggleClass('active');
    $('.table-sizes-tr-hidden').toggleClass('vissible');
});

$(document).on('click', '.js-dropdown-arrow', function(e){
    var $el = $(e.currentTarget);
    var $wrapper = $el.closest('.js-dropdown');

    $el.toggleClass('active');
    $wrapper.find('.js-dropdown-hidden-el').toggleClass('visible').toggleClass('hidden');
});

$(".menu-aside").on("click", function(){
    $('.accordion-menu-page').slideToggle();
});

$(".icon-filter").on("click", function(){
    $('#modal-filter').modal('show')
});
$(".modal-filter .close").on("click", function(){
    $('#modal-filter').modal('hide')
});

var navbar =  $('.sticky-top-mobile');  // navigation block
var wrapper = $('.content');        // may be: navbar.parent();

$(window).scroll(function(){
    var nsc = $(document).scrollTop();
    var bp1 = wrapper.offset().top;
    var bp2 = bp1 + wrapper.outerHeight()-$(window).height();
    
    if (nsc>bp1) {  navbar.css('position','fixed'); }
    //else { navbar.css('position','static'); }
    if (nsc>bp2) { navbar.addClass('show') }
    else { navbar.removeClass('show') }
});

$(".add-favorite").on("click", function(){
    $(this).addClass('active');
});

// Смена картинки товара при наведении
$(document).on('mouseover', '.other-photo', function () {
    var el = $(this).closest(".item-product-inside").find('img');
    el.attr('src', $(this).attr('img-src'));
    return false;
});