<div class="col-sm-12">
    <label>Import DB From</label><input type="text" id="url" value="<?=Settings::instance()->get('engine_db_transfer_from_url')?>" />
    <button type="button" class="btn btn-primary" id="import-btn">Start Import</button>
</div>
<script>
$(document).on("ready", function(){
   $("#import-btn").on("click", function(){
       $.get(
           "/frontend/assets/cron_mysqltransfer?url=" + encodeURIComponent($("#url").val()),
           {

           },
           function (response) {
               alert(response);
           }
       );
   });
});
</script>