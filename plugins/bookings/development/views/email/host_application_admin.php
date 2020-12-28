<?php
$dl_styles = 'clear: both; margin: 0;';
$dt_styles = 'float: left; font-weight: bold; width: 12em; margin: 0 1em .5em 0;';
$dd_styles = 'float: left; margin: 0 0 .5em 0;';
?>

<div style="font-family: sans-serif;">
    <h2>Host application submitted</h2>

    <?php foreach ($data as $label => $value): ?>
        <dl style="<?= $dl_styles ?>">
            <dt style="<?= $dt_styles ?>"><?= str_replace('_', ' ', htmlspecialchars(ucfirst($label))) ?>:</dt>

            <dd style="<?= $dd_styles ?>">
                <?php if (is_object($value) || is_array($value)): ?>
                    <?php foreach ($value as $label2 => $value2): ?>
                        <dl style="<?= $dl_styles ?>">
                            <?php if (!is_numeric($label2)): ?>
                                <dt style="<?= $dt_styles ?>"><?= str_replace('_', ' ', htmlspecialchars(ucfirst($label2))) ?>:</dt>
                            <?php endif; ?>

                            <dd style="<?= $dd_styles ?>">
                                <?php if (is_object($value2) || is_array($value2)): ?>
                                    <?php foreach ($value2 as $label3 => $value3): ?>
                                        <dl style="<?= $dl_styles ?>">
                                            <?php if (!is_numeric($label3)): ?>
                                                <dt style="<?= $dt_styles ?>"><?= str_replace('_', ' ', htmlspecialchars(ucfirst($label3))) ?>:</dt>
                                            <?php endif; ?>
                                            <dd style="<?= $dd_styles ?>"><?= is_object($value3) || is_array($value3) ? htmlentities(json_encode($value3)) : nl2br(htmlspecialchars($value3)) ?></dd>
                                        </dl>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <?= nl2br(htmlspecialchars($value2)) ?>
                                <?php endif; ?>
                            </dd>
                        </dl>
                    <?php endforeach; ?>
                <?php else: ?>
                    <?= nl2br(htmlspecialchars($value)) ?>
                <?php endif; ?>
            </dd>
        </dl>
    <?php endforeach; ?>
</div>