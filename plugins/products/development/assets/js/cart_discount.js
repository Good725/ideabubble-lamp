//
// INITIAL
//

$(document).ready(function() {
	$('#format_type').change(function() {
        var discount_types = $('#discount_types').val();
        var format_type = $('#format_type').val();
		if(format_type != '' && format_type != null && format_type != undefined && discount_types != '' && discount_types != null && discount_types != undefined){
			$.ajax({
				type: "POST",
				url: "/admin/products/ajax_show_discount_type_report/",
				data: "discount_types="+discount_types+"&format_type="+format_type,
				success: function(data){
					$('#report_result').html('');
					$('#report_result').html(data);
				}
			});
		}else{
			alert('Please first select type of discount and then format type.');
			return false;
		}
    });

  /* reset the format type fld*/
	$('#discount_types').change(function() {
		$('#format_type').val('');
	});	
  
});
   
