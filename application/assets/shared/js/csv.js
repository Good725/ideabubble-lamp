$(document).ready(function(){
   $("#table").on('change',function(){
       $.post('/admin/settings/get_columns_from_table',{table:$("#table").val()},function(data){
           data = $.parseJSON(data);

           var html = '';

           $.each(data.columns,function(){
                html = html +'<tr><td><b>' +
                    this.column_name +
                    '</b></td><td>' +
                    '<input type="text" data-column_name="'+this.column_name+'"/>' +
                    '</td></tr>'
           });

           $("#columns_table tbody").html(html);
       });
   });

    $(".save").on('click',function(){
        var json = generate_json_for_csv();
        $("#columns").val(json);
        $("#manage_csv_form").submit();
    });

    var data = $.parseJSON($("#columns").val());
    var table = data[0];
    $("#table").val(table.table);
    $("#table").change();
    data.splice(0,1);
    setTimeout(function(){
        $.each(data,function(){
            $("#columns_table tbody tr input[data-column_name='"+this.table_column+"']").val(this.csv_column);
        });
    },1500);
});

function generate_json_for_csv()
{
    var inputs = [];
    inputs.push({table:$("#table").val()});
    $("#columns_table tbody tr input").each(function(){
        inputs.push({table_column:$(this).data('column_name'),csv_column:$(this).val()});
    });
    return JSON.stringify(inputs);
}