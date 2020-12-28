<div class="row">
    <div class="span12">
        <div class="page-header clearfix">
            <?=(isset($alert)) ? $alert : ''?>
            <h2 class="">Services</h2>
            <div class="pull-left"><a href='/admin/extra/add_service'>Add Service</a></div>
            <div class="pull-right"><button type="button" id="refresh_data" class="btn" title="Update domain data, including IP addresses">Refresh</button></div>
        </div>
    </div>
</div>

<style type="text/css">
    #date_from, #date_to, #date_from input, #date_from label, #date_to input, #date_to label { display: inline-block;}
</style>

<form class="form-horizontal" id="form_sort_expiry" name="form_sort_expiry" action="/admin/extra/date_service" method="post">
    <div id="date_from">
        <label for="date_from">Expiry from</label>
        <input name="date_from" type="text" id="date_from" class="datepicker" value="<?= @$date_from ?>" size="20" />
    </div>
    <div id="date_to">
        <label for="date_to">Expiry to</label>
        <input name="date_to" type="text" id="date_to" class="datepicker" value="<?= @$date_to ?>" size="20" />
    </div>
    <input type="submit" id="btn_sort" class="btn" value="Filter" />
</form>

<table class="table table-striped dataTable" id="categories_table">
    <thead>
        <tr>
            <th scope="col">ID</th>
            <th scope="col">Service</th>
            <th scope="col">Company</th>
            <th scope="col">Domain</th>
            <th scope="col">Expiry</th>
            <th scope="col">Type</th>
            <th scope="col">IP Address</th>
            <th scope="col">Years Paid</th>
            <th scope="col"><?= date('Y') - 1 ?></th>
            <th scope="col"><?= date('Y') ?></th>
            <th scope="col"> Last Modified</th>
            <th scope="col"> Actions</th>
        </tr>
    </thead>

    <tbody>
        <?php foreach($services as $service): ?>
            <?php
            if (!is_numeric(strpos($service['years_paid'], strval(date('Y') - 1))))
                $service['prev_year_paid'] = 'No';
            else
                $service['prev_year_paid'] = 'Yes';

            if (!is_numeric(strpos($service['years_paid'], strval(date('Y')))))
                $service['curr_year_paid'] = 'No';
            else
                $service['curr_year_paid'] = 'Yes';

            if (strtotime($service['date_end']) > 0)
                $expiry_date = date('Y/m/d', strtotime($service['date_end']));
            else
                $expiry_date = NULL;
            ?>

            <?php $editEventScript = 'onclick="location.href=\'' . URL::Site('admin/extra/edit_service/' . $service['id']) . '\'"' ?>
            <tr id="service_<?= $service['id'] ?>" data-service-id="<?= $service['id'] ?>" >
                <td <?= $editEventScript ?>><?= $service['id'] ?></td>
                <td <?= $editEventScript ?>><?= $service['service_type'] ?></td>
                <td <?= $editEventScript ?>><?= $service['company_title'] ?></td>
                <td <?= $editEventScript ?>><?= $service['url'] ?></td>
                <td <?= $editEventScript ?>><?= $expiry_date ?></td>
                <td <?= $editEventScript ?>><?= $service['domain_type'] ?></td>
                <td <?= $editEventScript ?> class="ip_address"><?= $service['ip_address'] ?></td>
                <td <?= $editEventScript ?>><?= str_replace('|', ' ', $service['years_paid']) ?></td>
                <td <?= $editEventScript ?>><?= $service['prev_year_paid'] ?></td>
                <td <?= $editEventScript ?>><?= $service['curr_year_paid'] ?></td>
                <td <?= $editEventScript ?>><?= $service['date_modified'] ?></td>
                <td>
                    <a class="send_reminder"><?= __('Send reminder') ?></a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<div class="modal fade" tabindex="-1" role="dialog" id="send-reminder-modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">Send reminder</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" name="serviceId" id="service-id">
                <div class="form-group">
                    <label for="recipient-name" class="form-control-label">Recipient</label>
                    <input type="text" class="form-control" id="recipient-email">
                </div>
                <div class="form-group">
                    <label for="message-text" class="form-control-label">Message</label>
                    <textarea class="form-control" id="email-text" rows="15"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="send-reminder-button">Send</button>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/4.4.3/tinymce.min.js"></script>
<script type="text/javascript">
    $('#refresh_data').on('click', function(ev){
        $.ajax({
            url      : '/admin/extra/ajax_refresh_services/',
            dataType : 'json',
            async    : false
        }).done(function(result){
                for (var i = 0; i < result.length; i++)
                {
                    var id = result[i]['id'];
                    var ip = result[i]['ip_address'];
                    $('#service_'+id).find('td.ip_address').html(ip);
                }
            });
    });

    $('.send_reminder').on('click', function (ev) {
        var serviceId = $(this).closest('tr').data('service-id');

        $.ajax({
            url      : '/admin/extra/ajax_get_reminder_text',
            type     : 'post',
            dataType : 'json',
            data     : { serviceId: serviceId }
        }).done(function(result){
            $('#service-id').val(result.service_id);
            $('#recipient-email').val(result.to);

            tinymce.init({
                selector: '#email-text',
                height: 500,
                plugins: [
                    'advlist autolink lists link image charmap print preview anchor',
                    'searchreplace visualblocks code fullscreen',
                    'insertdatetime media table contextmenu paste code'
                ],
                toolbar: 'insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image',
            });

            setTimeout(function () {
                tinymce.get('email-text').setContent(result.email_text, {format: 'raw'});
            }, 800);

            $('#send-reminder-modal').modal();
        });
    });

    $('#send-reminder-button').on('click', function (ev) {
        $.ajax({
            url      : '/admin/extra/ajax_send_reminder',
            type     : 'post',
            dataType : 'json',
            data     : {
                serviceId : $('#service-id').val(),
                to        : $('#recipient-email').val(),
                message   : tinymce.get('email-text').getContent({format: 'raw'})
            }
        }).done(function(result){
            $('#send-reminder-modal').modal('hide');
        });
    });
</script>

