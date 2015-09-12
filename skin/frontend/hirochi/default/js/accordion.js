jQuery.noConflict();
jQuery(document).ready(function($){
    $(".box ul").hide();
    $(".box p").click(function(){
    $(this).next("ul").slideToggle();
        $(this).children("span").toggleClass("open");
    });
    $(".nav ul").hide();
    $(".nav p").click(function(){
    $(this).next("ul").slideToggle();
        $(this).children("span").toggleClass("open");
    });
});