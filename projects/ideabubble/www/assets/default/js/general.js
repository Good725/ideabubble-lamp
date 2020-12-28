$(document).ready(function(){
    /**
     * Main menu dropdown
     */
    $('#main-menu .main_menu').on({
        mouseenter: function(event){
            $(this).children('ul').fadeIn(100);
        },
        mouseleave: function(event){
            $(this).children('ul').fadeOut(100);
        }
    },'li');

	$('#gallery').bxSlider();
});

function hide_notice(){
    var cookie_name = 'ibPolicyNotice';
    var expire = new Date();
    expire.setDate(expire.getDate() + 365);
    var cookie_value = cookie_name + "=policyCookie; expires="+expire.toUTCString();
    document.cookie=cookie_value;
    $("#display_message").remove();
}
