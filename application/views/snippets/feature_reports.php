<?php
/*
 * Stat counters that appear at the top of certain plugin screens.
 *
 * Classes can be renamed, after everything using them has been switched to using this view
 */
$attributes = isset($attributes) ? $attributes : [];
$attributes['class'] = isset($attributes['class']) ? 'timeoff-reports '.$attributes['class'] : 'timeoff-reports';
?>

<div <?= html::attributes($attributes) ?>>
    <?php foreach ($reports as $report): ?>
        <div class="timeoff-report">
            <div class="timeoff-report-top">
                <p class="timeoff-report-amount"><?= $report['amount'] ?></p>

                <p class="timeoff-report-text">
                    <span class="timeoff-report-title"><?= $report['text'] ?></span>
                </p>
            </div>

            <?php if (!empty($date_range)): ?>
                <div class="timeoff-report-bottom">
                    <div class="timeoff-report-period" style="font-size: .75rem;"><?= $date_range ?></div>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>