/**
 * @author ideabubble
 */

function actionBarScroller()
{
    var nav = $('.floating-nav-marker');
    if(nav.length) {
        var viewportHeight = window.innerHeight ? window.innerHeight : $(window).height();
        var howFar = viewportHeight + $(window).scrollTop();
        var pos = $('.floating-nav-marker').offset().top;
        var $menu = $('#ActionMenu');
        if (pos > howFar) {
            $menu.addClass("floatingMenu").removeClass("fixedMenu").css('width', $menu.parent().outerWidth());
        }
        else {
            $menu.addClass("fixedMenu").removeClass("floatingMenu").css('width', '');
        }
    }
}

$(document).ready(actionBarScroller);
$(document).scroll(actionBarScroller);
$(window).resize(actionBarScroller);