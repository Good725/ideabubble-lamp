<div class="row no-gutters">
    <?php for ($i = 0; $i < 3; $i++): ?>
        <div class="col-sm-4 px-2">
            <div class="mini-widget-wrapper">
                <div class="mini-widget clearfix">
                    <div class="col-sm-12">
                        <div class="chart-info-widget-single_value">
                            <div class="text-center">
                                <h3>Example</h3>
                                <span style="font-size: 2em;">500</span>
                                <hr>
                                <a href="#">Link</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endfor; ?>
</div>

<div class="row gutters">
    <div class="col-sm-12">
        <?php
        $reports = ['Website Traffic', 'Top Web Pages', 'Top Referrals'];
        foreach ($reports as $report_name) {
            $report_id = DB::select('id')->from('plugin_reports_reports')->where('name', '=', $report_name)->where('delete', '=', 0)->execute()->get('id', 0);
            $report = new Model_Reports($report_id);
            $report->get(true);
            $report->get_widget(true);
            echo $report->render_widget();
        }
        ?>
    </div>
</div>

<h3 class="numbered-header">Stat counters</h3>

<?php
$reports = [
    ['amount' =>   '100', 'text' => 'Lorem ipsum'],
    ['amount' => '8,000', 'text' => 'Contacts'],
    ['amount' =>   '500', 'text' => 'Days'],
    ['amount' =>     '1', 'text' => 'Login']
];
echo View::factory('snippets/feature_reports')->set('reports', $reports)->set('date_range', date('Y'));
?>