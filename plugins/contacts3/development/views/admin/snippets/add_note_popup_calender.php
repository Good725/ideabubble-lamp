<div class="modal fade" id="add_note_popup">
    <div class="modal-dialog">
        <form class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Add Notes</h4>
            </div>

            <div class="modal-body">
                <div class="form-row">
                    <label class="col-sm-3 control-label" for="calendar-add_note-is_attending">Attending</label>

                    <div class="col-sm-3">
                        <select class="form-input is_attending" id="calendar-add_note-is_attending">
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <label class="col-sm-3 control-label" for="calendar-add_note-note">Note</label>

                    <div class="col-sm-9">
                        <textarea class="form-input note" id="calendar-add_note-note"></textarea>
                    </div>
                </div>
            </div>

            <div class="modal-footer form-actions">
                <button type="button" class="btn btn-primary" id="add_note_popup-submit">Submit</button>
                <button type="button" class="btn-cancel" data-dismiss="modal">Cancel</button>
            </div>
        </form>
    </div>
</div>