<div class="clearfix">
    <?php
    echo View::factory('snippets/btn_dropdown')
        ->set('type', 'main_actions')
        ->set('id', 'questionnaire-responses-actions')
        ->set('group_attributes', ['data-action_for_table' => '#questionnaire-responses-table'])
        ->set('options', [
            [
                'type' => 'link',
                'title' => 'Download',
                'attributes' => ['href' => '/admin/surveys/download_csv/'.$questionnaire->id]
            ]
        ])
        ->render();
    ?>

    <table class="table dataTable" data-fixed_filter="1" id="questionnaire-responses-table">
        <thead>
            <tr>
                <th scope="col">Name</th>
                <th scope="col">Completed</th>
                <?php /* <th scope="col">Score</th> */ ?>
                <th scope="col">Updated</th>
            </tr>
        </thead>

        <tbody>
            <?php foreach ($questionnaire->responses->order_by('endtime', 'desc')->order_by('starttime', 'desc')->find_all() as $response): ?>
                <tr>
                    <td><?= htmlentities($response->author->get_full_name()) ?></td>
                    <td><?= htmlentities($response->get_completion()) ?></td>
                    <?php /* <td><?= htmlentities($response->get_score()) ?></td> */ ?>
                    <td><?= htmlentities(IbHelpers::formatted_time(
                            date('Y-m-d H:i:s', $response->endtime ? $response->endtime : $response->starttime)
                        )) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>