<div class="form-row no-gutters">
    <div class="col-xs-12 timeoff-reports" id="<?= $id ?>">
        <?php foreach ($reports as $report): ?>
            <div class="timeoff-report">
                <div class="timeoff-report-top">
                    <p class="timeoff-report-amount"><?= $report['amount'] ?></p>
                    <p class="timeoff-report-text">
                        <span class="timeoff-report-title"><?= $report['title']  ?></span>
                    </p>
                </div>

                <div class="timeoff-report-bottom">
                    <div class="timeoff-report-period"><?= $report['period'] ?></div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>