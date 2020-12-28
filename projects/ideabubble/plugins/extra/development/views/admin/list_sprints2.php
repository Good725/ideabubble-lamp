<div class="row">
    <div class="span12">
        <div class="page-header clearfix">
            <?=(isset($alert)) ? $alert : ''?>
            <h2 class="">Sprints</h2>
            <div class="pull-left"><a href="/admin/extra/sync_sprints2">Syncronize With Jira</a></div>
        </div>
    </div>
</div>

<style type="text/css">
    #date_from, #date_to, #date_from input, #date_from label, #date_to input, #date_to label { display: inline-block;}
</style>

<table class="table table-striped dataTable" id="sprints_table">
    <thead>
    <tr>
        <th scope="col">ID</th>
        <th scope="col">Customer</th>
        <th scope="col">Sprint</th>
        <th scope="col">Summary</th>
        <th scope="col">Budget</th>
        <th scope="col">Spent (h)</th>
        <th scope="col">Balance</th>
        <th scope="col">Progress</th>
        <th scope="col">Status</th>
        <th scope="col">Last Synced</th>
    </tr>
    </thead>

    <tbody>
    </tbody>
</table>
