<section class="inner-content" id="cdetails">
    <?php $course = Model_Schedules::get_front_one();
    $days = Model_Schedules::lec_get_days_for_booking($course['course_id']);
    if (is_array($course) AND count($course) > 0): ?>
        <article class="newsBox">
            <section class="course-detail-box">
                <header>
                    <h3><?=$course['title']?></h3>
                </header>
                <section class="course-detail-content">
                    <div class="title">TEACHING LEVEL</div>
                    <div class="content"><?=$course['level']?></div>
                    <div class="title"><label for="select_schedule">DATES &amp; TIMES</label></div>
                    <div class="content"><select id="select_schedule">
                            <?php
                            if (!empty($days) AND $days !== 0) {
                                echo $days;
                            } else {
                                if (is_array($course['start_date']) AND count($course['start_date']) > 0): ?>
                                    <?php foreach ($course['start_date'] as $date => $val): ?>
                                        <?php
                                        if ($val['number_booked'] < $val['max_capacity']) {
                                            $date_array = explode(' ', $val['start_date']);
                                            if (is_array($date_array) && count($date_array) === 2 && $date_array[1] != "00:00:00") {
                                                $start_date = date("H:ia l, jS F Y", strtotime($date_array[0] . ' ' . $date_array[1]));
                                            } else {
                                                $start_date = date("l, jS F Y", strtotime($date_array[0]));
                                            }
                                            $end_date_id = (strtotime($val['start_date']) < time()) ? " data-is_started='1' " : '';
                                            ?>
                                            <option data-id='<?= $val['id'] ?>' <?=$end_date_id?>
                                                    data-is_fee='<?= $val['is_fee_required'] ?>'
                                                    data-fee_amount='<?= $val['fee_amount'] ?>'
                                                    data-fee_per='<?= $val['fee_per'] ?>'
                                                    value="<?= $val['id'] ?>" <?php if ($val['id'] == $_GET['id']) {
                                                echo 'selected="selected"';
                                            }?>><?=$start_date?></option>
                                        <?
                                        } else {
                                            echo "<option><span style='text-decoration: line-through;'>" . date("l, jS F Y", strtotime($val['start_date'])) . "</span> Fully Booked</option>";
                                        }
                                        ?>
                                    <?php endforeach; ?>
                                <?php endif;
                            }?>
                        </select>
                    </div>
                    <div class="title">DOCUMENTS</div>
                    <div class="content"><?php if (isset($course['file_id'])) : ?><a
                            href="<?= Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder, $course['file_id'], 'docs') ?>"><?= $course['file_id'] ?></a><?php endif;?>
                    </div>
                </section>
                <div class="course-detail-content-price">
                    COURSE FEES
                    <span class="price"
                          id="dynamic_price"><?php if ($course['is_fee_required'] == '1') echo '€' . number_format($course['fee_amount'], 2); else echo "Free";?></span>
                </div>
            </section>

            <figure class="imgCourse"><img
                    src="<?= Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder, Model_Courses::get_course_image($course['id']), 'courses' . DIRECTORY_SEPARATOR . '_thumbs') ?>"
                    alt=""></figure>
        </article>
        <div class="content">
            <?=$course['summary']?>
            <?=$course['description'];?>
            <br/><a href="/courses/<?php echo IbHelpers::generate_friendly_url($course['category']) . '.html'; ?>">RETURN
                TO <?=strtoupper($course['category']);?> COURSES »</a>
        </div>
        <div class="book_now">
        <?php if ($course['book_button'] == "1") { ?>
            <a id="book_button" data-id='<?= $_GET['id'] ?>'
               href="<?php if ($course['is_fee_required'] == '1') : ?><?= URL::site() ?>checkout/<?php echo IbHelpers::generate_friendly_url($course['category']) . '/' . IbHelpers::generate_friendly_url($course['title']) . '.html'; ?><?php else : ?><?= URL::site() ?>booking-form/<?php echo IbHelpers::generate_friendly_url($course['category']) . '/' . IbHelpers::generate_friendly_url($course['title']) . '.html'; ?><?php endif; ?>"
               class="book_now_link"><img
                    src="<?= URL::site() ?>assets/default/images/book_course_button.png"/></a></div>
        <? } ?>
    <?php else: ?>
        <p>
            There is no information to display.<br><br>
        </p>
    <?php endif;?>

</section>