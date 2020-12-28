$(document).ready(function(){

    //Change publish status, AJAX request
    $(".publish").on("click", function(event) {
        var click_item = $(this);
        //Get the id from the id attribute
        var str = $(this).attr('id');
        var n=str.split("publish_");
        const $table = $(this).parents('table');

        $.get('pages/publish/' + n[1], function(data) {
            data = JSON.parse(data);

            // Display notice
            $('body').add_alert(data.message, (data.success ? 'success' : 'danger')+' popup_box');

            // If this were a server-side table, this line of JS to refresh the table would suffice
            // $table.dataTable().fnDraw();

            // For now, directly update the HTML
            const publish = $(click_item).find('.icon-remove').length;

            $(click_item).html(publish
                ? '<span class="hidden">0</span><span class="icon-ok"></span>'
                : '<span class="hidden">1</span><span class="icon-remove"></span>'
            );
        });
    });
});
