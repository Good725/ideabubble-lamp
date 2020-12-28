/*****
 *
 * SLIDER PLUGIN, PLEASE DO NOT ADD ANY OTHER JS CODE
 *
 *****/
    
//@NAMESPACE ideaslider     
    
//this var block the next/prev function while the current next/prev is running
var ideaslider_stop = false;
//This variable should be the real width of the image, if something is wrong, please check the CSS rules.
ideaslider_image_width = 1904;
//Set the time interval fot the auto movement
var time_interval = 8000;
jQuery(document).ready(function(){
    //Set first elements in the banner slider
    $('#background-header li').first().addClass('current');
    $('.slider-inner').first().addClass('current_div');

    //Set position
    var window_size = $(window).width();
    $('#background-header').width(window_size);

    var right_size = Math.floor((window_size - ideaslider_image_width) / 2);
    $('#background-header .current img').css('right',right_size +'px');

    $(window).resize(function(){
        update_size();
    });

    //Slider, next
    $('#btn_panel .bx-next').click(function(event){
        event.preventDefault();
        if(ideaslider_stop == true)
            return false;
        slider_next();
    })
    $('#btn_panel .bx-prev').click(function(event){
        event.preventDefault();
        if(ideaslider_stop == true)
            return false;
        slider_prev();
    })

    window.setInterval("slider_next()",time_interval);

});
function update_size(){
    var window_size = $(window).width();
    $('#background-header').width(window_size);

    var right_size = Math.floor((window_size - ideaslider_image_width) / 2);
    $('#background-header .current img').css('right',right_size +'px');
}
function slider_next(){
    //How its works. Set a class 'current' in the current element in a LIST of items (UL or DIV), apply the effect and change the current

    //Do not allow this function if is already doing a movement
    if(ideaslider_stop == true){
        return false;
    }

    //Prevent multiple calls of the same function, only one movement is allow
    ideaslider_stop = true;
    var window_size = $(window).width();
    var right_size = Math.floor(((window_size - ideaslider_image_width) / 2));

    //This moves the current image to the left 
    $('#background-header .current img').animate({ right: '+=' + (($(window).width() / 2) + $('#background-header .current img').width()) + 'px'}, 700, function(){$(this).parent().removeClass('current')});

    //If is last, then start from the begining
    if($('#background-header .current').is("li:last-child")){
        $('#background-header li').first().find('img').css({'display':'block', 'right': -ideaslider_image_width + 'px' }); //Show
        $('#background-header li').first().addClass('current'); //Set
        $('#background-header li').first().find('img').animate({ right: '+=' + (window_size - right_size)  + 'px' }, 700, function(){ //Move
            $('#background-header .current').next().find('img').css({'display':'block', 'right': -ideaslider_image_width + 'px' }); //Show
            ideaslider_stop = false;
        });
    }
    else{
        $('#background-header .current').next().find('img').css({'display':'block', 'right': -ideaslider_image_width + 'px' });
        $('#background-header .current').next().find('img').animate({ right: '+=' + (window_size - right_size)  + 'px' }, 700, function(){
            $(this).parent().addClass('current');
            $('#background-header .current').next().find('img').css({'display':'block', 'right': -ideaslider_image_width + 'px' });
            ideaslider_stop = false;
        });
    }

    //Fade out/in the DIV, all div inside the "header-slider-page"
    speed = 200;
    //if the speed is slower than image slider, then move the ideaslider_stop = false to the this function
    $('#header-slider-page .current_div').fadeOut(speed,
        function(){
            if($('#header-slider-page .current_div').is("div:last-child")){
                $('.slider-inner').first().fadeIn(speed,function(){
                    $('#header-slider-page .current_div').removeClass('current_div');
                    $('.slider-inner').first().addClass('current_div');
                });
            }
            else{
                $('#header-slider-page .current_div').next().fadeIn(speed,function(){
                    $('#header-slider-page .current_div').next().addClass('current_div');
                    $(this).prev().removeClass('current_div');

                });
            }
        }
    )
}

function slider_prev(){

    //Do not allow this function if is already doing a movement
    if(ideaslider_stop == true){
        return false;
    }
    //Prevent multiple calls of the same function, only one movement is allow
    ideaslider_stop = true;
    var window_size = $(window).width();
    var right_size = Math.floor(((window_size - ideaslider_image_width) / 2));
    var right_position = ideaslider_image_width + right_size * 2;

    $('#background-header .current img').animate({ right: '-=' + (($(window).width() / 2) + $('#background-header .current img').width()) + 'px'}, 700, function(){$(this).parent().removeClass('current')});

    if($('#background-header .current').is("li:first-child")){
        $('#background-header li').last().find('img').css({'display':'block', 'right': right_position + 'px' });
        $('#background-header li').last().addClass('current');
        $('#background-header li').last().find('img').animate({ right: '-=' + (window_size - right_size)  + 'px' }, 700, function(){
            $('#background-header .current').prev().find('img').css({'display':'block', 'right': right_position + 'px' });
            ideaslider_stop = false;
        });
    }
    else{
        $('#background-header .current').prev().find('img').css({'display':'block', 'right': right_position + 'px' });
        $('#background-header .current').prev().find('img').animate({ right: '-=' + (window_size - right_size)  + 'px' }, 700, function(){
            $(this).parent().addClass('current');
            $('#background-header .current').prev().find('img').css({'display':'block', 'right': right_position + 'px' });
            ideaslider_stop = false;
        });
    }
    speed = 200;
    //if the speed is slower than image slider, then move the ideaslider_stop = false to the this function
    $('#header-slider-page .current_div').fadeOut(speed,
        function(){
            if($('#header-slider-page .current_div').is("div:first-child")){
                $('.slider-inner').last().fadeIn(speed, function(){
                    $('#header-slider-page .current_div').removeClass('current_div');
                    $('.slider-inner').last().addClass('current_div');
                    
                });
            }
            else{
                $('#header-slider-page .current_div').prev().fadeIn(speed, function(){
                    $('#header-slider-page .current_div').prev().addClass('current_div');
                    $(this).next().removeClass('current_div');

                });
            }
        }
    )
}