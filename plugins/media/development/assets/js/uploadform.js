$(function(){
    var image = $('#dropbox img');
    image.width = 100;
    image.height = 100;
			
    var reader = new FileReader();
		
                
    var dropbox = $('#dropbox'),
    message = $('.message', dropbox);
	
    dropbox.filedrop({
        // The name of the $_FILES entry:
        paramname:'pic',
		
        maxfiles: 5,
        maxfilesize: 10,
        url: 'mediauploader/postfile',
		
        uploadFinished:function(i,file,response){
            $.data(file).addClass('done');
            $('#filename').val(file.name);
            showMessage(response.status);

        // response is the JSON object that post_file.php returns
        },
		
        error: function(err, file) {
            switch(err) {
                case 'BrowserNotSupported':
                    showMessage('Your browser does not support HTML5 file uploads!');
                    break;
                case 'TooManyFiles':
                    alert('Too many files! Please select 5 at most! (configurable)');
                    break;
                case 'FileTooLarge':
                    alert(file.name+' is too large! Please upload files up to 10mb (configurable).');
                    break;
                default:
                    break;
            }
        },
		
        // Called before each upload is started
        beforeEach: function(file){
            if(!file.type.match(/^image\//)){
                alert('Only images are allowed!');
				
                // Returning false will cause the
                // file to be rejected
                return false;
            }
            createImage(file);
        },
		
        uploadStarted:function(i, file, len){
            showMessage('Uploading...');
            //createImage(file);
        },
		
        progressUpdated: function(i, file, progress) {
            $.data(file).find('.progress').width(progress);
        }
    	 
    });
	

	

    function createImage(file){

        // if Jcrop is applied to previous image, it will not work correctly, therefore needed to be destroyed first before applying again
        if (jcrop_api) {
            jcrop_api.destroy();
        }
        

        var  preview = $('.preview');
        var image = $('#dropbox img');
			
        var reader = new FileReader();
		
        image.show();
        image.width = 100;
        image.height = 100;
        
        reader.onload = function(e){
			
            // e.target.result holds the DataURL which
            // can be used as a source of the image:
			
            image.attr('src',e.target.result);
            image.show();
            
            $('#cropbox').Jcrop({
                    aspectRatio: 1,
                    onSelect: updateCoords
                    }
                    ,function(){
                    jcrop_api = this;
            });
            
            
        };
		
        // Reading the file as a DataURL. When finished,
        // this will trigger the onload function above:
        reader.readAsDataURL(file);
		
        //message.hide();
		
        // Associating a preview container
        // with the file, using jQuery's $.data():
        $.data(file,preview);
    }

    function showMessage(msg){
        message.html(msg);
    }

});
