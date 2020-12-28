<div class="modal fade" id="select-request" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Select request</h4>
            </div>
            <div class="modal-body">
                <table class="table table-striped dataTable-collapse">
                    <thead>
                    <tr>
                        <th scope="col">Start date</th>
                        <th scope="col">End date</th>
                        <th scope="col">Leave type</th>
                        <th scope="col">Duration</th>
                        <th scope="col">Status</th>
                        <th scope="col">Date approved</th>
                        <th scope="col">Actions</th>
                    </tr>
                    </thead>

                    <tbody>
                    <tr class="select-request-row hidden">
                        <td scope="col" ng-bind="vm.fmtDate(item.period[0])"></td>
                        <td scope="col" ng-bind="vm.fmtDate(item.period[1])"></td>
                        <td scope="col" ng-bind="item.type"></td>
                        <td scope="col" ng-bind="vm.fmtDuration(item.period[2])"></td>
                        <td scope="col" ng-bind="item.status"></td>
                        <td scope="col" ng-bind="vm.fmtDate(item.manager_updated_at)"></td>
                        <td scope="col"><a href="#" class="view">view</a></td>
                    </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>