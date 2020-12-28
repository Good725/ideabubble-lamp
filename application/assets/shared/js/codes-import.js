$(document).ready(function(){

  $('#import').click(function() {
      
      
    if($('input[name=file]').val()!='' && $('select[name=group_id]').val()!='empty' && $('select[name=role_id]').val()!='empty') {  
      
    var location = window.location.href;  
      
    $('#add_codes_modal').modal('hide');

    var fd = new FormData(document.forms.namedItem("formdata"));
                                               
    $.ajax({
      url: "/admin/settings/import_activation_codes",
      type: "POST",
      data: fd,
      processData: false,
      contentType: false,
      complete : function(r) {
        alert(r.responseText);
        console.log(r);
        window.location = location;
        
      }
      
    });
    console.log(fd);
    
    } else {
        
        alert('The form is invalid.');
        
    }
    
    return false;
  
  });
  
  
  $('.del-confirm').click(function(){

        var answer = confirm("Delete this code?");
        
        if (answer) {
                return true;
        } else {
                return false;
        }
        
    });
  
  
});
