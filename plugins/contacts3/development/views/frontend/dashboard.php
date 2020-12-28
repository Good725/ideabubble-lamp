<h1>Home Dashboard</h1>

<?php
$reports = array();
if (Auth::instance()->has_access('contacts3_frontend_bookings')) {
    $reports[] = array('title' => 'Total Booked Courses', 'total' => $booked_courses, 'link' => '/bookings.html', 'link_text' => 'Go to Bookings');
}
if (Auth::instance()->has_access('contacts3_limited_view')) {
    $reports[] = array('title' => 'Total myTime entry', 'total' => count($mytime_entries), 'link' => '/timetables.html', 'link_text' => 'Go to myTime');
} else {
    $reports[] = array('title' => 'Total profiles created', 'total' => $contacts_count, 'link' => '/admin/profile/edit?section=contact', 'link_text' => 'Go to Profile');
}
$reports[] = array('title' => 'Total attended courses', 'total' => $attendance['attended'], 'link' => '/admin/contacts3/attendance', 'link_text' => 'Go to Attendance');
?>

<div id="dashboard_report_widgets">
    <?php foreach ($reports as $report): ?>
        <div class="mini-widget-wrapper">
            <div class="mini-widget clearfix" style="background-color: #EBEBEB; color:rgb(255, 255, 255);">
                <div class="col-sm-12">
                    <div class="chart-info-widget-single_value">
                        <div class="text-center">
                            <h3><?= $report['title'] ?></h3>
                            <span style="font-size: 2em;"><?= $report['total'] ?></span>
                            <hr>
                            <a href="<?= $report['link'] ?>"><?= $report['link_text'] ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php
$plugins = array(
    'accounts'    => array('url' => '/accounts.html',    'icon' => 'flaticon-settings',       'text' => 'Accounts',     'permission' => 'contacts3_frontend_accounts'),
    'attendance'  => array('url' => '/admin/contacts3/attendance', 'icon' => 'flaticon-pencil-square', 'text' => 'Attendance',  'permission' => 'contacts3_frontend_attendance'),
    'bookings'    => array('url' => '/bookings.html',    'icon' => 'flaticon-receipt',        'text' => 'Bookings',     'permission' => 'contacts3_frontend_bookings'),
    'exams'       => array('url' => '/admin/assessments',      'icon' => 'flaticon-test',           'text' => 'Exams',        'permission' => 'assessments_list'),
    'myschedules' => array('url' => '/admin/courses/schedules', 'icon' => 'flaticon-heart',   'text' => 'My Schedules', 'permission' => 'courses_schedule_edit_limited'),
    'homework'    => array('url' => '/admin/homework',   'icon' => 'flaticon-homework',       'text' => 'Homework',     'permission' => 'contacts3_frontend_homeworks'),
    'timesheets'  => array('url' => '/admin/timesheets', 'icon' => 'flaticon-time',           'text' => 'Timesheets',   'permission' => 'contacts3_frontend_timesheets'),
    'timetables'  => array('url' => '/timetables.html',  'icon' => 'flaticon-calendar-dates', 'text' => 'Timetables',   'permission' => 'contacts3_frontend_timetables'),
    'wishlist'    => array('url' => '/wishlist.html',    'icon' => 'flaticon-heart',          'text' => 'Wishlist',     'permission' => 'contacts3_frontend_wishlist'),
);
?>

<div class="dashboard_icons dashboard_icons_active">
    <?php foreach ($plugins as $plugin): ?>
        <?php if (Auth::instance()->has_access($plugin['permission'])): ?>
            <div class="dashboard_plugin popinit styled_plugin" data-trigger="hover" rel="popover">
                <a href="<?= $plugin['url'] ?>">
                    <div class="icon-background">
                        <span class="<?= $plugin['icon'] ?>"></span>
                    </div>
                    <div class="dashboard_plugin_name"><?= $plugin['text'] ?></div>
                </a>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
</div>
