<?php
$total = 0;
foreach ($statuses as $status) {
    $total += $status['amount'];
}
?>

<?php if ($total > 0): ?>
<div class="progressbar-wrapper">
    <div class="progressbar">
        <?php foreach ($statuses as $status): ?>
            <div
                class="progressbar-item"
                data-status="<?= $status['name'] ?>"
                title="<?= $status['name'] ?>"
                style="<?= !empty($status['color']) ? 'background-color: '.$status['color'].'; ' : '' ?>width: <?= $status['amount'] / $total * 100 ?>%"
            ></div>
        <?php endforeach; ?>
    </div>

    <div class="progressbar">
        <?php foreach ($statuses as $status): ?>
            <div
                class="progressbar-item-label"
                data-status="<?= $status['name'] ?>"
                style="<?= !empty($status['color']) ? 'color: '.$status['color'].'; ' : '' ?>width: <?= $status['amount'] / $total * 100 ?>%"
                ><?php
                echo $status['amount'] ? $status['amount'] : '';
                echo (isset($total_hours)) ? "h/{$total_hours}h" : "";
                ?></div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>