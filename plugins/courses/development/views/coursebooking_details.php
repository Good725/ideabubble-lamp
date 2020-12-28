<div class="col-sm-12 course_booking_details">

</div>

<div class="modal fade" id="confirm_cancel_booking">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h3>Warning!</h3>
            </div>

            <div class="modal-body form-horizontal">
                <h3>Do you wish to cancel booking?</h3>

                <div class="form-group outstanding">
                    <label class="col-sm-6 control-label">Clear Outstanding payment <span class="amount"></span>?</label>
                    <div class="col-sm-6">
                        <input type="checkbox" id="confirm_clear_outstanding" value="1" checked="checked" />
                    </div>
                </div>

                <div class="form-group note">
                    <label class="col-sm-6 control-label">Note</label>
                    <div class="col-sm-6"><textarea class="form-control" id="cancel_note"></textarea></div>
                </div>
            </div>

            <div class="modal-footer">
                <a href="#" class="btn" data-dismiss="modal">Abort</a>
                <a href="#" data-id="0" class="btn btn-danger" id="btn_cancel_booking_yes" data-booking_id="">Yes</a>
            </div>

        </div>
    </div>
</div>

<div class="modal fade" id="transfer_booking">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h3>Warning!</h3>
            </div>

            <div class="modal-body form-horizontal">
                <h3>Transfer booking?</h3>

                <div class="transfer_items">
                    <div class="transfer_item template">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">From</label>
                            <div class="col-sm-4">
                                <select class="transfer_from_bookingschedule_id form-control"></select>
                            </div>
                            <label class="col-sm-2 control-label">To</label>
                            <div class="col-sm-4">
                                <select class="transfer_to_schedule_id form-control"><option value="">-- select --</option></select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="fee_diff">Fee change: <span class="fee_diff_calculated"></span></div>
            </div>

            <div class="modal-footer">
                <a href="#" class="btn" data-dismiss="modal">Abort</a>
                <a href="#" data-id="0" class="btn btn-danger" id="btn_transfer_booking_yes" data-booking_id="">Transfer</a>
            </div>

        </div>
    </div>
</div>
