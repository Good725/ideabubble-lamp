<div id="safety-precheck-details" class="hidden">
    <div>
        <table class="table" id="timeoff-details-table">
            <thead>
                <tr>
                    <th scope="col">Staff</th>
                    <th scope="col">Total</th>
                    <th scope="col">Passed</th>
                    <th scope="col">Failed</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td>Fred Flintstone</td>
                    <td>5</td>
                    <td>4</td>
                    <td>1</td>
                </tr>
                <tr>
                    <td>Barney Rubble</td>
                    <td>5</td>
                    <td>5</td>
                    <td>0</td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <th>Totals</th>
                    <th>10</th>
                    <th>9</th>
                    <th>1</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<script>
    $('.safety-precheck-mode-toggle').on('change', function() {
        var is_details_mode = ($('.safety-precheck-mode-toggle:checked').val() == 'details');
        
        $('#safety-precheck-details').toggleClass('hidden', !is_details_mode);
        $('#safety-precheck-reports-wrapper').toggleClass('hidden', is_details_mode);
        $('#safety-precheck-table-section').toggleClass('hidden', is_details_mode);
    });
</script>