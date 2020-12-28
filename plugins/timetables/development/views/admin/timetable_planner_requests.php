<style>
    .timetable-planner-requests {
        padding-left: 2em;
    }

    .timetable-planner-requests .form-checkbox {
        position: relative;
        left: -1.75rem;
    }

    .timetable-planner-requests .form-checkbox-helper {
        font-size: .75rem;
    }

    .timetable-planner-requests td:first-child {
        position: relative;
    }

    .timetable-planner-requests td:first-child .form-checkbox {
        position: absolute;
        margin-left: -1px;
    }
</style>
<div class="timetable-planner-requests hidden" id="timetable-planner-requests">
    <div class="form-group">
        <div class="row gutters">
            <div class="col-sm-6">
                <?= Form::ib_checkbox('<span class="slot_count">1</span> slot requires resolution', null, null) ?>
            </div>

            <!--
            <div class="col-sm-6 text-right">
                <button type="button" class="btn btn-primary">Resolve (1)</button>
                <button type="button" class="btn btn-cancel">Remove (1)</button>
            </div>
            -->
        </div>

        <table class="table dataTable dataTable-collapse" id="timetable-planner-requests-requires_resolution">
            <thead>
                <tr>
                    <th scope="col">Staff member</th>
                    <th scope="col">Schedule</th>
                    <th scope="col">Day</th>
                    <th scope="col">Date</th>
                    <th scope="col">Time</th>
                    <th scope="col">Location</th>
                    <th scope="col">Status</th>
                    <th scope="col">Hrs</th>
                    <th scope="col">Total&nbsp;h</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>

            <tbody>
                
            </tbody>
        </table>
    </div>

    <div class="form-group">
        <table class="table dataTable dataTable-collapse" id="timetable-planner-requests-other">
            <thead>
                <tr>
                    <th scope="col">Staff member</th>
                    <th scope="col">Schedule</th>
                    <th scope="col">Day</th>
                    <th scope="col">Date</th>
                    <th scope="col">Time</th>
                    <th scope="col">Location</th>
                    <th scope="col">Status</th>
                    <th scope="col">Hrs</th>
                    <th scope="col">Total&nbsp;h</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>

            <tbody>

            </tbody>
        </table>
    </div>
</div>


<div id="slot_confirm_remove" class="modal fade confirm_remove_modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3>Confirm Deletion</h3>
            </div>
            <div class="modal-body">
                <p>Are you sure you wish to remove this timeslot?</p>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn" data-dismiss="modal" data-content="Exit the form without saving changes">Cancel</a>
                <a class="btn btn-danger remove" data-action="delete" data-content="Delete the current slot.">Remove</a>
            </div>
        </div>
    </div>
</div>

<div id="slot_confirm_approve" class="modal fade confirm_approve_modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h3>Confirm Approval</h3>
            </div>
            <div class="modal-body">
                <p>Are you sure you wish to approva this timeslot?</p>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn" data-dismiss="modal" data-content="Exit the form without saving changes">Cancel</a>
                <a class="btn btn-primary approva" data-action="approve" data-content="Approve the current slot.">approve</a>
            </div>
        </div>
    </div>
</div>
