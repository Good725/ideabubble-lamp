<?
/*
 * This was not meant to be committed. Delete when sure it's not being used.
*/
?>
<style>
    #drag_and_drop_area{border:5px dashed #CCC;height:190px;position:relative;}
    #drag_and_drop_message{color:#999;display:block;margin-left:-200px;position:absolute;top:70px;left:50%;text-align:center;width:400px;}
    .multiple_upload_button{display:inline;}
    .multiple_upload_button input{height:0;width:0;}
</style>
<div class="dialaog">
    <div id="drag_and_drop_area">
        <div id="drag_and_drop_message">
            <p>[ Drag and drop images here ]<br />OR</p>
            <div class="btn multiple_upload_button">
                <span class="upload_label">Choose Files</span>
                <input type="file" multiple="multiple" />
            </div>
        </div>
    </div>
</div>
<div id="temp_uploaded_list" action="/admin/media/ajax_upload_media_item/">

</div>
<?php if (@$selectionDialog) { ?>
<a href="/admin/media/dialog">media selection</a>
<?php } ?>

<script type="text/javascript">
    $('.multiple_upload_button').find('.upload_label').click(function()
    {
        $(this).parent('.multiple_upload_button').find('input[type="file"]').click();
    });

    var holder2 = document.getElementById('drag_and_drop_area');
    var tests = {
        filereader: typeof FileReader != 'undefined',
        dnd: 'draggable' in document.createElement('span'),
        formdata: !!window.FormData,
        progress: "upload" in new XMLHttpRequest
    };
    var support = {
        filereader: document.getElementById('filereader'),
        formdata: document.getElementById('formdata')
    };
    var acceptedTypes = {
        'image/png': true,
        'image/jpeg': true,
        'image/gif': true,
        'image/svg+xml': true,
        'image/ico': true,
        'image/vnd.microsoft.icon': true
    };

    function previewfile2(file, width, height)
    {
        if(typeof(width)==='undefined') width = 450;
        if(typeof(height)==='undefined') height = 300;

        if (file.type == 'file')
        {
            file = file.files[0];
        }

        if (tests.filereader === true && acceptedTypes[file.type] === true)
        {
            var reader = new FileReader();
            reader.onload = function (event)
            {
                var image = new Image();
                image.src = event.target.result;
                $(image).css('max-width', '450px'); // a fake resize

                $('#temp_uploaded_list').prepend(image);

                $.ajax({
                    url: "/admin/media/ajax_upload_media_item",
                    type: "POST",
                    contentType:false,
                    processData: false,
                    cache: false,
                    data: {
                        'image': image.src
                    }
                });
            };

            reader.readAsDataURL(file);

        }
        else
        {
            holder2.innerHTML += '<p>Uploaded ' + file.name + ' ' + (file.size ? (file.size/1024|0) + 'K' : '');
        }
    }

    function readfiles2(files)
    {
        var formData = tests.formdata ? new FormData() : null;
        for (var i = 0; i < files.length; i++)
        {
            if (tests.formdata) formData.append('file', files[i]);
            previewfile2(files[i]);
            console.log(files[i]);
        }
    }

    if (tests.dnd)
    {
        holder2.ondragover = function() { this.className = 'hover'; return false; };
        holder2.ondragend = function()  { this.className = ''; return false; };
        holder2.ondrop = function(e)
        {
            this.className = '';
            e.preventDefault();
            readfiles2(e.dataTransfer.files);


        }
    }
    else
    {
        fileupload.className = 'hidden';
        fileupload.querySelector('input').onchange = function () {
            readfiles2(this.files);
        };
    }

</script>