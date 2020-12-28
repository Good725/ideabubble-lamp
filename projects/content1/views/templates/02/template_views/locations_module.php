<div class="maparea_wrapper">
    <div class="maparea" id="part1">
        <div class="maparea-cont" id="maparea-cont">
            <a href="#"><img src="<?=URL::get_skin_urlpath(TRUE)?>images/flag_ireland.gif" alt="" /></a>
            <a href="#"><img src="<?=URL::get_skin_urlpath(TRUE)?>images/flag_united_kingdom.gif" alt="" /></a>
            <a href="#"><img src="<?=URL::get_skin_urlpath(TRUE)?>images/flag_serbia.gif" alt="" /></a>
            <span>Our Locations</span>
            <a href="#"><span class="dPart2 maparea-arrow"></span></a>
        </div>
    </div>

    <div id="locationbox" style="display: none;">
        <div class="dropdown locationDropdown">
            <div class="clear">
                <div class="dropdown-cont">
                    <div class="lft"><a href="contact-us.html"><img src="<?=URL::get_skin_urlpath(TRUE)?>images/flag_ireland.gif" alt="" width="19" height="14" /></a></div>
                    <div class="rht">
                        <h6>Ailesbury Hair Clinic - Dublin</h6>
                        <p>Phone: +353 1 2690933 6 Merrion Court, Ailesbury Road, <br>Dublin, Ireland</p>
                    </div>
                </div>
                <div class="dropdown-cont">
                    <div class="lft"><a href="contact-us.html"><img src="<?=URL::get_skin_urlpath(TRUE)?>images/flag_ireland.gif" alt="" width="19" height="14" /></a></div>
                    <div class="rht">
                        <h6>Ailesbury Hair Clinic - Cork</h6>
                        <p>Phone: +353 1 2690933 Village House, East Village Douglas, <br>Cork, Ireland</p>
                    </div>
                </div>
            </div>
            <div class="clear">
                <div class="dropdown-cont">
                    <div class="lft"><a href="contact-us.html"><img src="<?=URL::get_skin_urlpath(TRUE)?>images/flag_united_kingdom.gif" alt="" width="19" height="14" /></a></div>
                    <div class="rht">
                        <h6>Ailesbury Hair Clinic - London</h6>
                        <p>Phone: +44 (0)20 7411 9021<br>Berkeley Square<br>London W1J 6BD, UK</p>
                    </div>
                </div>
                <div class="dropdown-cont">
                    <div class="lft"><a href="contact-us.html"><img src="<?=URL::get_skin_urlpath(TRUE)?>images/flag_turkey.png" alt="" width="18" height="12" /></a></div>
                    <div class="rht">
                        <h6>Ailesbury Hair Clinic - Istanbul</h6>
                        <p>Innovia Hair Restoration Clinic&nbsp;<br>Nispetiye Street . No:35 Apt.C Etiler İSTANBUL</p>
                    </div>
                </div>
                <div class="dropdown-cont">
                    <div class="lft"><a href="contact-us.html"><img src="<?=URL::get_skin_urlpath(TRUE)?>images/flag_united_arab_emirates.png" alt="" width="19" height="14" /></a></div>
                    <div class="rht">
                        <h6>Ailesbury Hair Clinic - Dubai</h6>
                        <p><span>Jumeriah Beach Road, Villa 359, <br>Jumeriah 2<br>Dubai, United Arab Emirates<br /><strong>Call: +971 4 3428383</strong></span></p>
                    </div>
                </div>
                <div class="dropdown-cont">
                    <div class="lft"><a href="contact-us.html"><img src="<?=URL::get_skin_urlpath(TRUE)?>images/flag_united_arab_emirates.png" alt="" width="19" height="14" /></a></div>
                    <div class="rht">
                        <h6>Ailesbury Hair Clinic - Dubai</h6>
                        <p>Dubai Healthcare City, Al Faris Building,&nbsp;<br>2nd Floor, Office 202, Dubai, United Arab Emirates<br><strong>Call: +971 4 296 8005</strong></p>
                    </div>
                </div>
            </div>
            <div id="closebox" class="closebox"><a class="closebox" href="#">CLOSE X</a></div>
        </div>
    </div>
</div>

<div class="pharea" onclick="document.location.href='contact-us.html'">
    <p class="phone_number">Dublin: +353 1 269 0933</p>
    <p class="phone_number">UK: +44 20 7411 9021</p>
</div>

<script type="text/javascript">
    $('#maparea-cont').click(function(){
        if (!$('#locationbox').is(':visible'))
            $('#locationbox').show('slow');
        else
            $('#locationbox').hide();
    });

    $('body').click(function(e){
        if (!$(e.target).hasClass('locationDropdown') && !$(e.target).hasClass('dPart2')) {
            var parents = $(e.target).parents('.locationDropdown');
            if (parents.length == 0 || $(e.target).hasClass('closebox'))
                $('#locationbox').hide();
        }
    });
</script>