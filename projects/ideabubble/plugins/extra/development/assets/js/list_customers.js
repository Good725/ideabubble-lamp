/**
 * Created with JetBrains PhpStorm.
 * User: dale
 * Date: 14/02/2014
 * Time: 15:06
 * To change this template use File | Settings | File Templates.
 */
$(document).ready(function(){
    $(".delete").click(function(){
        var customer = $(this).data('id');
        $.post('/admin/extra/delete_customer',{id: customer},'json').done(function(){location.reload(true);});
    });
});