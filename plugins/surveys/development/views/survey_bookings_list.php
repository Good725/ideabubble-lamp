<?= (isset($alert)) ? $alert : '' ?>
<?php
if (isset($alert)) {
    ?>
    <script>
        remove_popbox();
    </script>
    <?php
}
?>
<?php if(count($surveys) > 0): ?>
    <div class="title-left">
        <h2>Assessments</h2>
    </div>
<table class="table table-striped" id="survey_booking_list_table">
    <thead>
    <tr>
        <th>Author</th>
        <th>Date</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    <?php
    foreach($surveys as $survey):
        $survey_author = ($survey['survey_author'] != NULL) ? Model_Users::get_user($survey['survey_author'])['name'] : 'N/A'; ?>
    <tr>
        <td><a href='<?= '#' ?>'>
                <?= $survey_author ?></a>
        </td>
        <td><a href='<?= '#' ?>'>
                <?= IbHelpers::relative_time_with_tooltip(date("Y-m-d H:i:s",substr($survey['endtime'], 0, 10))) ?></a></td>
    </tr>
    <?php endforeach; ?>
    </tbody>

</table>
 <?php else: ?>
    <div class="title-left">
        <h2>No assessments exist for booking</h2>
    </div>
<?php endif; ?>
