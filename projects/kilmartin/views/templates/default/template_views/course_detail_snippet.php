<?php if (isset($_GET['id']) && (int)$_GET['id'] > 0) {
    $course = Model_Courses::get_detailed_info((int)$_GET['id']);
}
?>
<?php
$schedule_id = '';
if (isset($_GET['schedule_id']) && (int)$_GET['schedule_id'] > 0) {
    $schedule_id = $_GET['schedule_id'];
}
?>
<?php if(isset($course) AND $course):
$course_image = (!empty($course['image'])) ? Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder, $course['image'], 'courses' . DIRECTORY_SEPARATOR . '_thumbs') : Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder, "404imagekes.png", 'courses' . DIRECTORY_SEPARATOR . '_thumbs');

//check if book now button is to be shown on settings toggle value
$product_enquiry = FALSE;
if (Settings::instance()->get('product_enquiry') == 1) {
    $product_enquiry = TRUE;
}
?>
<section id="course_details_page" class="content-section inner-content">

    <? require_once 'left_sidebar_filter.php';?>

    <section class="revision-block">

        <div class="contentBlock">
            <h1><strong><?= $course['title'] ." - (ID:#".$course['id']?>)</strong></h1>

            <div class="clear">
                <div class="buttons">
                    <?php
                    if (count($course['schedules']) >= 1) {
                        echo '<button class="button sky" data-title="' . urlencode($course['title']) . '" id="enquire-course" data-id="0"><span><span>ENQUIRE NOW »</span></span></button>';
                        if (!$product_enquiry):
                            echo '<button class="button blue" data-title="' . urlencode($course['title']) . '" id="book-course" data-id="0"><span><span>BOOK NOW »</span></span></button>';
                        endif;
                    }?>
                </div>
                <div class="desc">
                    <div class="clear">
                        <div class="lt">
                            <?= $course['year'] ?> <?= $course['level'] ?><br>
                            <?= $course['category'] ?><br/>
                            <?= $course['type'] ?> &nbsp;<br>

                            <form method="post" action="#" id="selectcform">
                                <label class="selectbox">
                                    <?php
                                    if(count($item['schedules']) == 0 AND ($item['category'] == 'Grinds' OR $item['category'] == 'Grinds/Tutorials')):?>
                                        Please contact the office for schedules regarding this course.
                                    <?php else:?>
                                    <select class="styled validate[required]" name="schedule" id="schedule_selector" title="Please Select Schedule">
                                        <?php if (isset($course['schedules']) && is_array($course['schedules']) && count($course['schedules']) > 0): ?>
                                            <option value="" <?= $schedule_id == '' ? ' selected="selected"' : '' ; ?>>SELECT SCHEDULE</option>
                                            <?php foreach ($course['schedules'] as $schedule_i => $schedule): ?>
                                                <?php $selected = $schedule['id']==$schedule_id ? ' selected="selected"' : '' ; ?>
                                                <?php if ($schedule['location'] != null) { ?>
                                                <option value="<?= $schedule['id'] ?>" data-event_id="<?= $schedule['event_id'] ?>" <?= $selected ?>>
                                                <?php
                                                    echo $schedule['location'] ?>, <?= date("D d M H:i ", strtotime($schedule['start_date'])).((isset($schedule['end_date'])) ? ' - '.date("H:i", strtotime($schedule['end_date'])) : '').', ('.date('jS F Y',strtotime($schedule['start_date'])),')';
                                                ?>
                                                </option>
                                                <?php } ?>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <option value="" data-event_id="">No schedules defined</option>
                                        <?php endif; ?>
                                    </select>
                                    <?php endif; ?>
                                </label>
                            </form>
                            <div id="trainer_name"></div>
                            <div class="price_wrapper" style="visibility: hidden;">Price:
                                <span class="price"></span>
                            </div>

                        </div>
                        <br class="spacer">
                        <h4>Course Summary</h4>

                        <p>
                            <?= $course['summary'] ?>
                        </p>
                        <h4>Course Description</h4>

                        <p>
                            <?= $course['description'] ?>
                        </p>

                    </div>

                </div>
            </div>

    </section>
    <section class="revision-block">
        <div class="whiteBox">
            <h4>Schedule Description:</h4>

            <p id="schedule-description">

            </p>
        </div>
        <div class="contentBlock">
            <dl>
                <dt id="schedule_date"></dt>
                <dd id="schedule_duration"></dd>
            </dl>
            <dl id="frequency_change">
                <dt>Repeat:</dt>
                <dt id="frequency_time"></dt>
            </dl>
            <dl>
                <dt id="schedule_frequency"></dt>
                <? /* KES-295 <dd id="schedule_time"></dd> */ ?>
            </dl>
            <dl>
                <dt id="schedule_start_time"></dt>
                <? /* KES-295 <dd id="schedule_days"></dd> */ ?>
            </dl>
            <dl>
                <dt id="schedule_location"></dt>
                <dd id="schedule_trainer"></dd>
            </dl>
        </div>
    </section>
    <?php else: ?>
    <p>Course not found! Please select course from <a href="/course-list.html">Course listing</a>, or use a search
       bar to find course for You.
    </p>
    <?php endif; ?>
</section>
<script>

    $('#trainer_name').hide();

    $("#schedule_selector").live("change", function () {
        var id = $(this).val();
        var event_id = this.selectedIndex != -1 ? $(this.options[this.selectedIndex]).data("event_id") : "";
        var price_wrapper = $('.price_wrapper');

        $("#enquire-course, #book-course").prop("disabled", true);
        if (id.length > 0) {
            $.post('/frontend/courses/get_schedule_price_by_id', {sid: id, event_id: event_id}, function (data) {
                $(price_wrapper).find('.price').html(data.price);
                price_wrapper[0].style.visibility = 'visible';
                $("#enquire-course, #book-course").prop("disabled", false);
            });
        }
        else {
            price_wrapper.style.visibility = 'hidden';
            $('#trainer_name').hide();
            $('#trainer_name').html('');
            $(price_wrapper).find('.price').html('');
        }
    });
</script>
