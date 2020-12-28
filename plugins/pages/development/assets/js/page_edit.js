$(document).ready(function () {
    $('#menu_group').on('change', function () {
        var change = $(this);
        if (change.val() == "none") {
            $('#menu_addition').hide();
        }
        else {
            $.post("/admin/pages/getSubMenus", { category: $('#menu_group').val()}, function (data) {
                $('#submenu_group').html(data);
            });
            if ($("#menu_addition").is(":hidden")) {
                $('#menu_addition').show();
            }
        }
    });

    CKEDITOR.replace(
        'footer_editor',
        {
            // CUSTOM Toolbar
            // More Info @ http://docs.cksource.com/CKEditor_3.x/Developers_Guide/Toolbar
            /*toolbar :
             [
             ['Source', '-', 'NewPage'],
             ['Font', 'FontSize'],
             ['Bold', 'Italic', 'Underline', '-', 'RemoveFormat'],
             ['TextColor','BGColor'],
             ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'],
             ['NumberedList', 'BulletedList', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'],
             ['Image', 'Table'],
             ['Link', 'Unlink', 'Anchor'],
             ['Maximize', 'ShowBlocks']
             ],*/
            width: '100%'
        }
    );

    $("#frm_page_edit #btn_delete").click(function () {
        $('#confirm_delete').modal();
    })
    $("#frm_page_edit #btn_delete_yes").click(function () {
        location.href = "/admin/pages/delete_pag/" + $("#val_pages_id").val();
    })
    $("#frm_page_edit #btn_save_exit").click(function () {
        $('#bannerStaticDiv[style="display: none;"]').html('');
        $('#bannerDynamicDiv[style="display: none;"]').html('');
        if ($.trim($('#inp_name').val()) == '') {
            alert('Please insert a page name');
            return false;
        }
        document.getElementById('frm_page_edit').submit();
    })
    $("#frm_page_edit #btn_save_seo_all").click(function () {
        $("#action").val('save_seo_all');
		document.getElementById('frm_page_edit').submit();
    })
    $("#frm_page_edit #btn_save").click(function () {
        if ($.trim($('#inp_name').val()) == '') {
            alert('Please insert a page name');
            return false;
        }
        $('#bannerStaticDiv[style="display: none;"]').html('');
        $('#bannerDynamicDiv[style="display: none;"]').html('');
		document.getElementById('frm_page_edit').submit();
    })
    $("#frm_page_edit #btn_save_new").click(function () {
        $('#bannerStaticDiv[style="display: none;"]').html('');
        $('#bannerDynamicDiv[style="display: none;"]').html('');
		document.getElementById('frm_page_edit').submit();
    })
    $("#frm_page_edit #btn_send").click(function(e) {
        e.preventDefault();
        $('.send-message-form').off('submit');
        $('.send-message-form').on('submit', messaging_submit_handler);
        $("#send-email-page-title").html("Page: " + $("[name=page_name]").val());
        $("[name='email[page_id]']").val($("[name=pages_id]").val());
        $("#send-email-subject").val($("[name=title]").val());
        $("#send-email-message").css("display", "none");
        $('#send-message-modal-email').modal();
        return false;
    });

    $(document).scroll(function (e) {
        actionBarScroller();
    });

    $(document).resize(function () {
        actionBarScroller();
    });

    var page_url = $('#page_url').val(); // URL of the page being edited (or the main page)
    $.get(page_url).then(function(content)
    {
        // get the URLs of relevant stylesheets on the page
        var links = content.match(/http.*\/assets\/([0-9]+|default)\/css\/styles\.css/g);

        // Ajax to get colours from the CSS files
        $.ajax({
            url: '/admin/pages/ajax_parse_css_colors',
            type: 'POST',
            data: {links: links}
        }).done(function(results)
            {
                // Put these colours in the CKEditor colour palette
                CKEDITOR.replace('page_editor',{
                    colorButton_colors: color_string = results.replace(/#|"|\[|\]/g, ''),
                    width: '956px',
                    height: '400px'
                });
            });
    });

	if ($('#layout_id').find(':selected').html() == 'campaign')
	{
		$('#ib_select_folder').val('campaign');
	}

	$('#layout_id').on('change', function()
	{
		if ($(this).find(':selected').html() == 'campaign')
		{
			$('#ib_select_folder').val('campaign');
		}
	});

    actionBarScroller();
});

var $ActionMenu = null;
function actionBarScroller() {
    if ($ActionMenu == null) {
        $ActionMenu = $('#ActionMenu');
    }
    var viewportHeight = window.innerHeight ? window.innerHeight : $(window).height();
    var fromTop = $(window).scrollTop();
    var howFar = viewportHeight + fromTop;
    var pos = $('.floating-nav-marker').position().top;
    stuff = 'AP:' + pos + ' PB:' + howFar;
    var offset = $("#frm_page_edit .tabbable").offset();
    if (pos > howFar) {
        if (!$ActionMenu.hasClass("floatingMenu")) {
            $ActionMenu.addClass("floatingMenu");
            $ActionMenu.css("left", offset.left + "px");
            $ActionMenu.removeClass("fixedMenu");
        }
    } else {
        if (!$ActionMenu.hasClass("fixedMenu")) {
            $ActionMenu.addClass("fixedMenu");
            $ActionMenu.css("left", "");
            $ActionMenu.removeClass("floatingMenu");
        }
    }
}
function isNumberKey(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode
    if (charCode > 31 && (charCode < 48 || charCode > 57))
        return false;

    return true;
}