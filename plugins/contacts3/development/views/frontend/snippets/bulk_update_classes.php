<p>Please confirm <strong><?= ($attending == 'will_attend' OR $attending == 1) ? 'attendance' : 'absence' ?></strong> for these <?= count($booked_classes) ?> Classes</p>

<div class="slider-wrapper">
    <ul>
        <?php if ($booked_classes) foreach ($booked_classes as $class): ?>
            <li>
                <input type="hidden" name="classes_ids[]" value="<?= $class['booking_item_id'] ?>"/>
                <a href="#"><span><?= date('D d M', strtotime($class['datetime_start'])) ?></span>
                    <span class="sub-name"><?= $class['course'] ?></span>
                    <?= date('h a', strtotime($class['datetime_start'])) ?>
                    <?php if($attending): ?>
                        <span class="icon_circled icon-exclamation" aria-hidden="true"></span>
                    <?php else: ?>
                        <span class="icon-check" aria-hidden="true"></span>
                    <?php endif; ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>

    <div class="slider_action">
        <a href="#" class="prev_arrow"><span class="icon-angle-left" aria-hidden="true"></span></a>
        <a href="#" class="next_arrow"><span class="icon-angle-right" aria-hidden="true"></span></a>
    </div>
</div>

<div class="note-block">
    <label class="form-label">Note</label>
    <textarea class="form-input" name="note"></textarea>
</div>

<div class="form-actions">
    <button type="submit" value="Confirm" class="btn btn-primary continue" id="confirm_bulk_update" data-count="<?= count($booked_classes) ?>"<?= count($booked_classes) ? '' : ' disabled="disabled"' ?>>Confirm</button>
    <button type="button" class="btn-cancel" data-dismiss="modal">Cancel</button>
</div>