<div>
    <table class="table" id="attendance-details-table">
        <thead>
            <tr>
                <th scope="col"><?= htmlspecialchars(__('Student')) ?></th>
                <th scope="col"><?= htmlspecialchars(__('Total')) ?></th>
                <th scope="col"><?= htmlspecialchars(__('No status')) ?></th>
                <?php foreach ($statuses as $status): ?>
                    <th scope="col"><?= htmlspecialchars(__($status)) ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>

        <tbody>
            <?php $totals = []; ?>

            <?php foreach ($students as $student): ?>
                <?php
                $total = $student->booking_items($filters)->count_all();
                if (!$total) {
                    continue;
                }
                ?>

                <tr>
                    <td><?= $student->get_full_name() ?></td>
                    <td>
                        <?php
                        $totals['all'] = !isset( $totals['all']) ? $total : $totals['all'] + $total;
                        echo $total;
                        ?>
                    </td>
                    <td>
                        <?php
                        $total = $student->booking_items($filters)->where('timeslot_status', 'is', null)->count_all();
                        $totals['no_status'] = !isset($totals['no_status']) ? $total : $totals['no_status'] + $total;
                        echo $total;
                        ?>
                    </td>
                    <?php foreach ($statuses as $status): ?>
                        <td>
                            <?php
                            $total = $student->booking_items($filters)->where('timeslot_status', '=', $status)->count_all();
                            $totals[$status] = !isset($totals[$status]) ? $total : $totals[$status] + $total;
                            echo $total;
                            ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>

        <tfoot>
            <tr>
                <th scope="row">Totals</th>
                <?php foreach ($totals as $total): ?>
                    <th scope="col"><?= $total ?></th>
                <?php endforeach; ?>
            </tr>
        </tfoot>
    </table>
</div>
<script>
    $('.attendance-mode-toggle').on('change', function() {
        var is_details_mode = ($('.attendance-mode-toggle:checked').val() == 'details');

        $('#attendance-details').toggleClass('hidden', !is_details_mode);
        $('#attendance-reports-wrapper').toggleClass('hidden', is_details_mode);
        $('#attendance-table-section').toggleClass('hidden', is_details_mode);
    });
</script>