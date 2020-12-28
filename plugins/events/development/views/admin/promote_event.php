<?= isset($alert) ? $alert : '' ?>
<?php
	if(isset($alert)){
	?>
		<script>
			remove_popbox();
		</script>
	<?php
	}
?>
<form id="event-edit" class="form-horizontal event-edit" method="post" action="/admin/events/save_event/<?= $event['id'] ?>">
    <input type="hidden" name="id" value="<?= $event['id'] ?>" />

    <section class="form-section">
        <h2><?= __('Promote your event') ?></h2>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="edit-event-social-link-selector"><?= __('Get Social') ?></label>
            <div class="col-sm-3">
                <select class="form-control" id="edit-event-social-link-selector">
                    <option value="Twitter"><?= __('Twitter') ?></option>
                    <option value="Facebook"><?= __('Facebook') ?></option>
                    <option value="LinkedIn"><?= __('LinkedIn') ?></option>
                </select>
            </div>
            <div class="col-sm-2">
                <button type="button" class="btn btn-default" id="edit-event-social-link-btn"><?= __('Add') ?></button>
            </div>
        </div>

        <div id="edit-event-social-links">
            <div class="form-group edit-event-social-link" id="edit-event-social-link-template" style="display: none;">
                <div class="col-sm-offset-2 col-sm-4">
                    <input type="text" class="form-control" placeholder="<?= __('Enter URL') ?>" />
                </div>
                <div class="col-sm-3">
                    <button type="button" class="btn-link edit-event-social-link-delete"><?= __('delete') ?></button>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="edit-event-sender_email"><?= __('Sender Email') ?></label>
            <div class="col-sm-5">
                <input type="text" class="form-control" id="edit-event-sender_email" name="sender_email" value="event@uticket.ie" />
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <div class="panel panel-default">
                    <button type="button" class="btn-link panel-heading" role="button" data-toggle="collapse" data-target="#edit-event-email-panel" tabindex="0"><?= __('View email details') ?></button>
                    <div class="panel-body collapse" id="edit-event-email-panel">

                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="edit-event-email_message"><?= __('Email Message') ?></label>
                            <div class="col-sm-6">
                                <select class="form-control" id="edit-event-email_message" name="email_message">
                                    <option value="">New Ticket Sale</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="edit-event-email_sender"><?= __('From') ?></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="edit-event-email_sender" name="email_sender" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="edit-event-email_subject"><?= __('Subject') ?></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="edit-event-email_subject" name="email_subject" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="edit-event-email_body"><?= __('Body') ?></label>
                            <div class="col-sm-9">
                                <textarea class="form-control ckeditor" id="edit-event-email_body" name="email_body">&lt;p&gt;Dear {TICKET_USER_NAME},&lt;/p&gt;
&lt;p&gt;Please find {NO_OF_TICKETS}{TICKET_TYPE} ticket(s) for {EVENT_NAME} attached.&lt;/p&gt;
&lt;p&gt;We are looking forward to seeing you there,&lt;/p&gt;
&lt;p&gt;King regards,&lt;br /&gt;
{EVENT_NAME} Team&lt;/p&gt;</textarea>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-2 control-label"><?= __('Discounts') ?></div>
            <div class="col-sm-10">
                <div class="panel panel-default">
                    <button type="button" class="btn-link panel-heading" role="button" data-toggle="collapse" data-target="#edit-event-discounts-panel" tabindex="0"><?= __('Add a discount?') ?></button>
                    <div class="panel-body collapse" id="edit-event-discounts-panel">
                        <h3><?= __('Discount Codes') ?></h3>

                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="edit-event-discount_code"><?= __('Code') ?></label>
                            <div class="col-sm-6">
                                <input type="text" class="form-control" id="edit-event-discount_code" name="discount_code" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="edit-event-discount_type"><?= __('Type') ?></label>
                            <div class="col-sm-4">
                                <select class="form-control" id="edit-event-discount_type" name="discount_type_id">
                                    <option value="">Fixed amount</option>
                                    <option value="">Percentage amount</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="edit-event-discount_value"><?= __('Value') ?></label>
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <div class="input-group-addon">&euro;</div>
                                    <input type="text" class="form-control" id="edit-event-discount_value" name="discount_value" />
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="edit-event-discounts_available_for"><?= __('Available for') ?></label>
                            <div class="col-sm-4">
                                <select class="form-control" id="edit-event-discounts_available_for" name="discounts_available_for">
                                    <option>All ticket types</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="edit-event-discounts_available"><?= __('Discounts available') ?></label>
                            <div class="col-sm-4">
                                <input type="number" class="form-control" id="edit-event-discounts_available" name="discounts_available" />
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="edit-event-discount_expiration_date"><?= __('Expiry Date') ?></label>
                            <div class="col-sm-4">
                                <div class="input-group">
                                    <input type="text" class="form-control datepicker" id="edit-event-discount_expiration_date" name="discount_expiration_date" />
                                    <div class="input-group-addon"><span class="icon-calendar"></span></div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-6">
                                <button type="button" class="btn btn-default"><?= __('Save') ?></button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="well">
            <button type="button" class="btn btn-default back-button"><?= __('Back') ?></button>
            <button type="submit" class="btn btn-primary"><?= __('Publish Event') ?></button>
            <a href="/admin/events" class="btn btn-default"><?= __('Cancel') ?></a>
        </div>
    </section>


</form>

<div class="modal fade" id="image-upload-dialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?= __('Upload images') ?></h4>
            </div>
            <div class="modal-body"><?= View::factory('multiple_upload') ?></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="new-contact-dialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
<div class="modal-dialog" role="contact">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel"><?= __('Add new contact') ?></h4>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label class="col-sm-3 control-label" for="add-new-contact-first_name"><?= __('First Name') ?></label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" id="add-new-contact-first_name" placeholder="<?= __('First Name') ?>" />
                </div>
            </div>
            <br clear="both" />
            <div class="form-group">
                <label class="col-sm-3 control-label" for="add-new-contact-last_name"><?= __('Last Name') ?></label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" id="add-new-contact-last_name" placeholder="<?= __('Last Name') ?>" />
                </div>
            </div>
            <br clear="both" />
            <div class="form-group">
                <label class="col-sm-3 control-label" for="add-new-contact-last_name"><?= __('Email') ?></label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" id="add-new-contact-email" placeholder="<?= __('Email') ?>" />
                </div>
            </div>
            <br clear="both" />
            <div class="form-group">
                <label class="col-sm-3 control-label" for="add-new-contact-phone"><?= __('Phone') ?></label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" id="add-new-contact-phone" placeholder="<?= __('Phone') ?>" />
                </div>
            </div>
            <br clear="both" />
            <div class="form-group">
                <label class="col-sm-3 control-label" for="add-new-contact-mobile"><?= __('Mobile') ?></label>
                <div class="col-sm-4">
                    <input type="text" class="form-control" id="add-new-contact-mobile" placeholder="<?= __('Mobile') ?>" />
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default add" data-dismiss="modal">Add</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        </div>
    </div>
</div>
</div>

<style>
    input[type="text"].ib_text_title_input {
        width: 100%;
    }
	.event-edit .control-label {
		text-align: left;
		text-transform: uppercase;
	}

    .form-section {
        display: none;
    }
    .form-section.active {
        display: block;
    }

    .form-section .panel-heading {
        background: none;
        width: 100%;
        text-align: left;
        border-width: 0 0 1px;
    }
	.modal-heading .form-group,
	.panel-heading .form-group {
		margin-bottom: 0;
	}

	/*
    .form-section .panel-heading:after {
        float: right;
        content: 'show';
    }

    .form-section .panel-heading[aria-expanded="true"]:after {
        float: right;
        content: 'hide';
    }
    */

    .form-section .well {
        clear: both;
    }
    .toggle.btn {
        min-height: 32px;
    }
    .toggle-group .btn {
        padding-top: .5em;
    }
    .edit-event-images-table {
        width: 100%;
    }
    .edit-event-images-table td {
        padding-bottom: .5em;
    }
    .text-left {
        text-align: left ! important;
    }

	.btn-event-inverse {
		background: none;
		border: 2px solid #31ceb4;
		color: #31ceb4;
	}
	.btn-event,
	.btn-event-inverse:hover,
	.btn-event-inverse:focus {
		background: #31ceb4;
		border: 2px solid #31ceb4;
		box-shadow: none;
		color: #fff;
	}
	.btn-event:hover,
	.btn-event:focus {
		color: #fff;
	}

	.event-edit .btn-lg {
		font-size: 1.7em;
	}

	.edit-event-ticket-table {
		margin: 1em 0;
		width: 100%;
	}

	.edit-event-ticket-table .form-control {
		width: auto;
	}

	.edit-event-ticket-table th,
	.edit-event-ticket-table td {
		padding: .3em;
	}
	.edit-event-ticket-table td:last-child {
		white-space: nowrap;
	}

</style>
<script>
    window.eventEditData = <?=json_encode($event)?>;

    // "Continue" and "Back" buttons to toggle the visible portion of the form
    $('.continue-button').on('click', function()
    {
        var $current_section = $(this).parents('.form-section');
        var $next_section    = $current_section.find('\+ .form-section');
        $current_section.hide();
        $next_section.show()[0].scrollIntoView();
    });
    $('.back-button').on('click', function()
    {
        var $current_section  = $(this).parents('.form-section');
        var $previous_section =  $current_section.prev();
        $current_section.hide();
        $previous_section.show()[0].scrollIntoView();
    });

    // Adding social media links
    $('#edit-event-social-link-btn').on('click', function()
    {
        var social_media = $('#edit-event-social-link-selector').val();
        var count = $('.edit-event-social-link').length - 1;

        var $new_block = $('#edit-event-social-link-template').clone().removeAttr('id');
        $new_block.find('input')
            .attr('placeholder', 'Enter '+social_media+' URL')
            .attr('name', 'event[social]['+count+']['+(social_media.toLowerCase())+']')
        ;

        $('#edit-event-social-links').append($new_block);
        $new_block.show();
    });

    // Removing social media links
    $('#edit-event-social-links').on('click', '.edit-event-social-link-delete', function()
    {
        $(this).parents('.edit-event-social-link').remove();
    });

    // Dragging sortability
    $('.sortable-tbody').sortable({cancel: 'a, button, :input, label'});

    // CKEditor
    $(document).ready(function() {
        CKEDITOR.replace('edit-event-email_body', {
            toolbar :
                [
                    ['Source'],
                    ['Bold', 'Italic', 'Underline', 'Strike', '-', 'RemoveFormat']
                ]
        });

    });

    $('[name="pre_sale"]').on('change', function()
    {
        var pre_sale = ($('[name="pre_sale"]:checked').val() == 1);
        var $fields  = $('#pre_sale-url-fields');
        pre_sale ? $fields.show() : $fields.hide();
    });

    // Sync the URL fields
    $('#edit-event-web_address_url').on('keyup change', function()
    {
        document.getElementById('edit-event-pre_sale_url').value = this.value;
    });
    $('#edit-event-pre_sale_url').on('keyup change', function()
    {
        document.getElementById('edit-event-web_address_url').value = this.value;
    });

	$('#edit-event-tickets-list').sortable();

	$('#edit-event-ticket-buttons').find('button').on('click', function()
	{
		var type = this.getAttribute('data-type');
		var $clone = $('#create-ticket-templates-'+type).clone();
		$('#edit-event-tickets-list').append($clone);
		$('#edit-event-ticket-table').find('thead').removeClass('hidden');

	});


</script>
