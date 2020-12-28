// LettersMarket Clipboard Button
// LMCButton
// http://www.lettersmarket.com

function isNotEmpty(str) {
 return !((str == undefined) || (str == ''));
}

function ShowLMCButton(cliptext, capt, js, furl)
{
    setTimeout(function()
    {
        var table_html = $("#autotimetables_table_preview_wrapper table").html();
        cliptext = "<table class=\"autotimetable\">"+table_html+"</table>";
        var params = 'txt=' + encodeURIComponent(cliptext);
        if (!isNotEmpty(furl)) { furl = "lmcbutton.swf"; }
        if (isNotEmpty(capt)) { params += '&capt=' + capt; }
        if (isNotEmpty(js)) { params += '&js=' + js; }

        $('#container_copy').html('<object width="52" height="28">'+' <param name="movie" value="' + furl + '">'+' <PARAM NAME=FlashVars VALUE="' + params + '">'+' <embed src="' + furl + '" flashvars="' + params + '"  width="40" height="20"></embed>'+'</object>');
        //alert('file: ' + furl + ' Params: ' + params); // debug
    }, 5000);
}

