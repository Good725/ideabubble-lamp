/**
 * Created by dale on 01/05/2014.
 */
$(document).ready(function(){
    var redirects = [];
    var addEditRedirects = $("#add_edit_redirects");
    $("#add").click(function(){
        var block =
            '<div class="form-group redirect_group">' +
                '<label class="col-sm-1">From:</label>' +
				'<div class="col-sm-3"><input class="form-control from" type="text" value="" name="newRedirect[from][]"/></div> ' +
                '<label class="col-sm-1">To:</label>' +
				'<div class="col-sm-3"><input class="form-control to" type="text" value="" name="newRedirect[to][]"/></div> ' +
                '<label class="col-sm-1">Type:</label>' +
				'<div class="col-sm-2">' +
                    '<select class="form-control type" name="newRedirect[type][]">' +
                        '<option value="301">301</option>' +
                        '<option value="302">302</option>' +
                    '</select>' +
                '</div> ' +
                '<a href="#" class="delete">&times;</a>' +
            '</div>';
        $("#edit_redirects_list").append(block);
    });

    $("#save").click(function(){
        $("#add_edit_redirects .redirect_group").each(function(){
            var result = $(this).children(".from").val()+","+$(this).children(".to").val()+","+$(this).children(".type").val()+"|";
            redirects.push(result);
        });
        $("#values").val(redirects);
        addEditRedirects.submit();
    });

	$('#edit_redirects_list').on("click", ".redirect_group .delete", function(){
        var deleteId = $(this).data('id');
        if(deleteId){
            var deleteInput = document.createElement('input');
            deleteInput.type = 'hidden';
            deleteInput.value = deleteId;
            deleteInput.name = 'deleteRedirect[]';
            addEditRedirects.append(deleteInput);
        }

        $(this).parent().remove();
    });
});
