<?php
$location = (isset($_GET['location']) AND strlen($_GET['location']) > 0) ? $_GET['location'] : FALSE;
$product_enquiry = (Settings::instance()->get('product_enquiry') == 1);

$pagination_count = @ceil($courses['total_count'] / 10);

?>
<?php if (isset($courses['data']) AND ! is_null($courses['data']) AND !empty($courses['data'])): ?>

    <?php if ($pagination_count > 1): ?>
        <div class="filter_pagination">
            <a class="prev" href="#" onclick="filter_offset('prev')">prev</a>
            <a class="next" href="#" onclick="filter_offset('next')">next</a>
            <ul>
                <?php for($i = 1; $i <= $pagination_count; $i++): ?>
                    <li><a href="#" data-page="<?= $i ?>" <?= ($i == @$courses['page']) ? ' class="current"' : ''; ?>onclick="filter_offset(<?= ($i-1)*10 ?>)"><?= $i ?></a></li>
                <?php endfor; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (isset($courses['results_found'])): ?>
        <p><?= $courses['results_found'] ?></p>
    <?php endif; ?>

    <?php for ($i = 0; $i < count($courses['data']) AND $i < 10; $i++): ?>
        <?php $item = $courses['data'][$i]; ?>
        <section class="revision-block course-line">

            <div class="contentBlock">
                <h1 data-id="<?= $item['id'] ?>"><strong><?= $item['title'] ." - (ID:#".$item['id']?>) </strong>
                    <span class="label-container">
                        <?=(!empty($item['year'])) ? '<span class="label-year">'.$item['year'].'</span>' : '';?>
                        <?=(!empty($item['category'])) ? '<span class="label-category">'.$item['category'].'</span>' : '';?>
                        <?=(!empty($item['level'])) ? '<span class="label-level">'.$item['level'].'</span>' : '';?>
                    </span>
                </h1>

                <div class="clear">
                    <div class="desc">
                        <div class="clear">
                            <div class="lt">
                                <?= $item['year'] ?> <?= $item['level'] ?><br>
                                <?= $item['type'] ?>
                            </div>
                            <div class=" rt">
                                <?php
                                if(count($item['schedules']) == 0 AND ($item['category'] == 'Grinds' OR $item['category'] == 'Grinds/Tutorials')):?>
                                    Please contact the office for schedules regarding this course.
                                <?php else:?>
                                <form action="#" method="post" id="select_schedule<?= $item['id'] ?>">

                                    <label class="selectbox">
                                        <select name="start_date" id="start_date_<?= $item['id'] ?>" class="validate[required] start_date" data-id="<?= $item['id'] ?>"
                                            <? if (count($item['schedules']) < 1): ?>
                                            style="visibility: hidden;"
                                        <? endif; ?>
                                            >
                                            <?php if (is_array($item['schedules']) AND count($item['schedules']) > 0): ?>
                                                <option value="">TIME &amp; DATE</option>
                                                <?php foreach ($item['schedules'] as $schedule):
                                                    $trainer = ($schedule['trainer_name']=='') ? 'Not Assigned Yet' : $schedule['trainer_name']
                                                    ?>
                                                    <option value="<?= $schedule['id'] ?>" data-event_id="<?= @$schedule['event_id'] ?>" data-id="<?= @$schedule['id'] ?>">
                                                        <?= date('D', strtotime($schedule['start_date']))
                                                        .' - '.$schedule['location']
                                                        .' - Teacher: '. $trainer
                                                        .' - '.(@$schedule['timeslots'][0] ? date('H:i', strtotime($schedule['timeslots'][0]['datetime_start'])) : date('H:i D d M', strtotime($schedule['start_date'])))
                                                        .' - end date:'.date('D d M y', strtotime($schedule['end_date']))
                                                        ; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <option value="">No dates and times defined</option>
                                            <?php endif; ?>
                                        </select>
                                    </label>
                                </form>
                                <?php endif;?>
                            </div>
                        </div>
                        <br class="spacer"/>

                        <div class="price_wrapper" style="visibility: hidden;">Price: <span class="price"></span>
                        </div>

                        <div class="nogap">
                            <?= $item['summary'] ?>
                        </div>
                    </div>
                    <div class="buttons">
                        <? $url_name = str_replace('%2F', '', urlencode($item['title'])) ?>
                        <a href="/course-detail/<?= $url_name ?>.html/?id=<?= $item['id']; ?>">
                            <button type="button" class="button grn course-detail" data-title="<?= $url_name ?>"
                                           data-id="<?= $item['id'] ?>"><span><span>VIEW COURSE »</span></span></button>
                        </a>
                        <?php
                        if (is_array($item['schedules']) AND count($item['schedules']) > 0) {
                            echo '<button type="button" class="button sky course-enquire" data-schedule="0"
                                        data-title="' . $url_name . '" data-id="' . $item['id'] . '">
                                            <span><span>ENQUIRE NOW »</span></span>
                                        </button>';
                            if (!$product_enquiry){
                                echo '
                                    <button type="button" class="button blue course-book" data-schedule="0"
                                    data-title="' . $url_name . '" data-id="' . $item['id'] . '"
                                    style="visibility: hidden;">
                                        <span><span>BOOK NOW »</span></span>
                                    </button>';
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </section>
    <?php endfor; ?>

    <?php if ($pagination_count > 1): ?>
        <div class="filter_pagination">
            <a class="prev" href="#" onclick="filter_offset('prev')">prev</a>
            <a class="next" href="#" onclick="filter_offset('next')">next</a>
            <ul>
                <?php for($i = 1; $i <= $pagination_count; $i++): ?>
                    <li><a href="#" data-page="<?= $i ?>" <?= ($i == @$courses['page']) ? ' class="current"' : ''; ?>onclick="filter_offset(<?= ($i-1)*10 ?>)"><?= $i ?></a></li>
                <?php endfor; ?>
            </ul>
        </div>
    <?php endif; ?>

<?php else: ?>
    <div class="message">
        <p>No results found.</p>
    </div>
<?php endif; ?>