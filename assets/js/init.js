jQuery(document).ready(function ($) {
    $(".products .product").hover(function () {
        console.log($(this));
        $(this).find('.previewslider').css("display", "block");
    });
    $(".products .product").mouseleave(function () {
        $(this).find('.previewslider').css("display", "none");
    });    
    $(".products .product .previewslider img").hover(function () {
        $(this).parents('.product').find('a img').first().attr("src", $(this).parent().attr("data-href"));
    });
});


