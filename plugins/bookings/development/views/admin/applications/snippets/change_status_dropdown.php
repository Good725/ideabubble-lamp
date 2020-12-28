<button class="btn-link">
    <?= __('Change status') ?>
    <span class="icon-caret-right right"></span>
</button>

<ul class="dropdown-menu">
    <?php foreach ($status_groups as $status_group): ?>
        <li>
            <h3><?= htmlspecialchars($status_group['label']) ?></h3>

            <ul class="list-unstyled">
                <?php foreach ($status_group['statuses'] as $status): ?>
                    <?php if (true): // todo: permission check for each group to go here ?>
                        <li>
                            <label class="radio-bullet">
                                <input
                                    type="radio"
                                    class="dropdown-menu-radio application-status-radio"
                                    data-booking_id="<?= $application->booking_id ?>"
                                    data-status_group="<?= $status_group['name'] ?>"
                                    name="item[<?= $application->booking_id ?>]<?= $status_group['name'] ?>"
                                    value="<?= $status ?>"
                                    <?= (isset($application->{$status_group['name']}) && $application->{$status_group['name']} == $status)? ' checked="checked"' : '' ?> />
                                <span>- <?= htmlspecialchars($status) ?></span>
                            </label>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </li>
    <?php endforeach; ?>
</ul>