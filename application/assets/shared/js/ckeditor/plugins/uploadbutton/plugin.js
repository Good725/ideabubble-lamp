CKEDITOR.plugins.add('uploadbutton',
{
	icons: 'uploadbutton',
    init: function(editor)
    {
        if (document.getElementById('upload_files_modal'))
        {
            editor.ui.addButton('UploadButton',
            {
                label: 'Upload Images',
                command: 'uploadImage'
            });
        }

    }
});


