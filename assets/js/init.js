jQuery(document).ready(function ($) {
    jQuery(".thumbnail .thumbnails").jCarouselLite({
        vertical: true,
        visible: 4,
    });

    $(".thumbnail .thumbnails img").hover(function () {
        $(this).parents('div.thumbnail').find('img.attachment-shop_catalog').attr("src", $(this).parent().attr("data-src"));
    });
    $(".thumbnail").hover(function () {
        $(this).find('.thumbnails').css( "visibility", "visible");
    });
        $(".thumbnail").mouseleave(function () {
        $(this).find('.thumbnails').css("visibility", "hidden");
    });
});


