<div id="timesheet-details-modal" class="modal fade">
    <div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-body">
            <h3 class="submit timesheet hidden"><?=  __('Submit timesheets')  ?></h3>
            <h3 class="approve timesheet hidden"><?= __('Approve timesheets') ?></h3>
            <h3 class="reject timesheet hidden"><?=  __('Reject timesheets')  ?></h3>
            
            <div class="timesheets-period-submit form-horizontal">
                <div class="row no-gutters">
                    <div ng-class="modal.data.mode == 'submit' ? 'col-xs-4' : 'col-xs-8'">
                        <strong><?= __('Team member') ?></strong>
                    </div>
                    
                    <div class="col-xs-4" ng-show="modal.data.mode == 'submit'">
                        <strong><?= __('Reviewer') ?></strong>
                    </div>
                    
                    <div class="col-xs-4 text-right">
                        <strong><?= __('Worked / required') ?></strong>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="col-xs-12">
                        <div class="row no-gutters timesheets-submit-work_required">
                            <div ng-bind="timesheet.staff.name" ng-class="modal.data.mode == 'submit' ? 'col-xs-4' : 'col-xs-8'">
                            </div>

                            <div class="col-xs-4">
                                <?= Form::ib_select(null, 'reviewer_id', html::optionsFromArray($reviewers, null), null, array(
                                        'class' => 'ib-modal-combobox')
                                ); ?>
                            </div>

                            <div class="col-xs-4 text-right">
                                <span class="logged-time"></span> / <span class="required-time"></span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="col-xs-12">
                        <label for="timeshetes-approve-comment"><?= __('Comment') ?></label>
                        <?= Form::ib_textarea(null, 'comment', null, array(
                                'id' => 'timesheets-comment',
                                'placeholder' => __('Write a comment'),
                        )) ?>
                    </div>
                </div>
                
                <div class="row gutters timesheets-submit-footer">
                    <div class="col-sm-6 text-left">
                        <a class="view-timesheet"><?= __('View timesheet') ?></a>
                    </div>
                    <div class="col-sm-6 text-right">
                        <button type="button" class="btn btn-primary action">
                            <span class="submit hidden"><?=  __('Submit timesheets')  ?></span>
                            <span class="approve hidden"><?= __('Approve timesheets') ?></span>
                            <span class="reject hidden"><?=  __('Reject timesheets')  ?></span>
                        </button>
                        
                        <button type="button" class="btn-cancel" data-dismiss="modal"><?= __('Cancel') ?></button>
                    </div>
                </div>
                
                <div class="row no-gutters margin-top-15 hidden" id="timesheet-details-list">
                    <div class="col-xs-12">
                        <div class="form-row">
                            <div class="dataTables_length hidden">
                                <label>
                                    Show
                                    <select class="pagination-limit">
                                        <option ng-value="10">10</option>
                                        <option ng-value="20">20</option>
                                        <option ng-value="50">50</option>
                                    </select>
                                    entries
                                </label>
                            </div>
                            <div class="dataTables_filter hidden">
                                <label>
                                    Search: <input type="text" class="search" id="timesheet-details-list-table-search">
                                </label>
                            </div>
                            <table class="table table-striped dataTable-collapse" id="timesheet-details-list-table">
                                <thead>
                                <th ib-th="period_start_date" scope="col">Date</th>
                                <th ib-th="type" scope="col">Log type</th>
                                <th ib-th="item" scope="col">Schedule / item</th>
                                <th ib-th="description" scope="col">Description</th>
                                <th ib-th="duration" scope="col">Hours</th>
                                </thead>
                                <tbody>
                                <tr class="hidden">
                                    <td class="date"></td>
                                    <td class="type"></td>
                                    <td class="title"></td>
                                    <td class="description"></td>
                                    <td class="period"></td>
                                </tr>
                                </tbody>
                            </table>
                            <div class="no_records hidden">
                                <p>There are no records to display.</p>
                            </div>
                            <div class="pagination hidden">
                                <div class="form-row gutters vertically_center">
                                    <div class="col-sm-6">
                                        <p>Showing <span class="from"></span> to <span class="to"></span> of <span class="total"></span> entries</p>
                                    </div>

                                    <div class="col-sm-6 text-right">
                                        <div class="paging_bootstrap pagination">
                                            <ul>
                                                <li class="prev">
                                                    <button type="button">← Previous</button>
                                                </li>

                                                <li class="page hidden">
                                                    <button type="button" class="hidden"></button>
                                                </li>

                                                <li class="next">
                                                    <button type="button">Next →</button>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        
        </div>
    </div>
    </div>
</div>
