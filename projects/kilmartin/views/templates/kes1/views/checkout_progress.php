<div class="checkout-progress">
    <?php
    if (!isset($progress_links)) {
        // These should be defined in the controller. Remove everything these defaults after everything has been updated

        $last_search_parameters = Session::instance()->get('last_search_params');
        $list_query = (!empty($last_search_parameters) && is_array($last_search_parameters)) ? '?'.http_build_query($last_search_parameters) : '';

        $details_query = '';
        if (!empty($checkout_progress_event)) {
            $details_query .= '/'.preg_replace('/[^a-z0-9]+/i', '-', strtolower($checkout_progress_event['course']));
            $details_query .= '?id='.$checkout_progress_event['course_id'];

            if (!empty($checkout_progress_event['schedule_id'])) {
                $details_query .= '&schedule_id='.$checkout_progress_event['schedule_id'];
            }
        }

        $progress_links = array(
            'home'     => array('title' => __('Home'),      'link' => '/'),
            'results'  => array('title' => __('Results'),   'link' => '/course-list.html'.$list_query),
            'details'  => array('title' => __('Details'),   'link' => '/course-detail'.$details_query),
            'checkout' => array('title' => __('Checkout'),  'link' => false),
            'thankyou' => array('title' => __('Thank you'), 'link' => false)
        );

        if (!empty($current_step) && isset($progress_links[$current_step])) {
            $progress_links[$current_step]['active'] = true;
        }
    }
    ?>
    <ul>
        <?php $reached_current_step = false; ?>

        <?php foreach ($progress_links as $stage): ?>
            <?php $reached_current_step = ($reached_current_step || !empty($stage['active'])) ?>

            <li<?= (!empty($stage['active'])) ? ' class="curr"' : '' ?>>
                <a href="<?= (!$reached_current_step && $stage['link']) ? $stage['link'] : '#' ?>">
                    <p><?= $stage['title'] ?></p>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>