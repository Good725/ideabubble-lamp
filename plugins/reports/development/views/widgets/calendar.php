<?php if (count($events) > 0): ?>
    <div style="height: 258px;overflow-y: auto;">
        <ul>
            <?php foreach ($events as $event): ?>
                <li class="list-group-item clearfix">
                    <div class="calendar-date"><?= $event['Date']  ?></div>
                    <div>
                        <div class="calendar-event-name"><?= $event['Title'] ?></div>
                        <?php if ($event['Link']): ?>
                            <a class="calendar-link" href="<?= $event['Link'] ?>"><?= !empty($event['Read more text']) ? $event['Read more text'] : __('Read More') ?></a>
                        <?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php else: ?>
    <div class="flex-center">
        <h3 style="color: #000;"><?= __('No data available') ?></h3>
    </div>
<?php endif; ?>