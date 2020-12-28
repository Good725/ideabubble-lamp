<?php if (isset($alert)): ?>
    <?= $alert ?>
    <script>remove_popbox();</script>
<?php endif; ?>

<?php if ($access_all_homework): ?>
    <table class="table table-striped table-hover table-condensed" id="homework_table">
        <thead>
            <tr>
                <th scope="col">Teacher</th>
                <th scope="col">Schedule</th>
                <th scope="col">Course</th>
                <th scope="col">Date</th>
                <th scope="col">Actions</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <div class="modal fade" tabindex="-1" role="dialog" id="delete-homework-modal">
        <div class="modal-dialog" role="document">
            <form class="modal-content" action="/admin/homework/delete" method="post">
                <input type="hidden" name="id" id="delete-homework-id"/>

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Confirm deletion</h4>
                </div>

                <div class="modal-body">
                    <p>Are you sure you wish to delete this homework item?</p>
                </div>

                <div class="modal-footer form-actions">
                    <button type="submit" class="btn btn-danger">Delete</button>
                    <button type="button" class="btn-cancel" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

<?php if ($access_family_homework): ?>
    <?php
    if (Auth::instance()->has_access('contacts3_limited_family_access')) {
        echo View::factory('frontend/snippets/family_members')
            ->set('family', isset($contacts[0]) ? $contacts[0]['object']->get_family() : '')
            ->set('attributes', array('id' => 'homework-select_contact'));
    }
    ?>

    <?php foreach ($contacts as $contact): ?>
        <?php if (count($family_homework[$contact['id']]) > 0): ?>
            <div class="padd-bottom-20 clearfix homework-list" data-contact_id="<?=$contact['id']?>">
                <h3 class="text-success"><?=$contact['first_name'] . ' ' . $contact['last_name']?>'s Homework</h3>

                <div class="table_scroll" style="min-height: 300px;">
                    <table class="table table-striped table-hover dataTable">
                        <thead>
                            <tr>
                                <th scope="col">Teacher</th>
                                <th scope="col">Schedule</th>
                                <th scope="col">Course</th>
                                <th scope="col">Date</th>
                                <th scope="col">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($family_homework[$contact['id']] as $homework): ?>
                                <tr>
                                    <td><?= $homework['trainer1'] ?></td>
                                    <td><?= $homework['schedule'] ?></td>
                                    <td><?= $homework['course'] ?></td>

                                    <td><?= IbHelpers::relative_time_with_tooltip($homework['datetime_start']) ?></td>
                                    <td>
                                        <div class="text-center">
                                            <div class="action-btn left">
                                                <a><span class="icon-ellipsis-h" aria-hidden="true"></span></a>
                                                <ul>
                                                    <li><a href="/admin/homework/view/<?= $homework['id'] ?>">View</a></li>
                                                    <?php foreach ($homework['files'] as $file) { ?>
                                                        <li><a href="/frontend/contacts3/download_homework?file_id=<?=$file['file_id']?>">download</a></li>
                                                    <?php } ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                </div>
            </div>
        <?php elseif ($contact['object']->has_role('student')): ?>
            <div class="homework-list" data-contact_id="<?=$contact['id']?>">
                <h3 class="text-success"><?=$contact['first_name'] . ' ' . $contact['last_name']?> does not have any homework</h3>
            </div>
        <?php else: ?>
            <div class="homework-list" data-contact_id="<?=$contact['id']?>">
                <h3><?=$contact['first_name'] . ' ' . $contact['last_name']?> is not a student.</h3>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>

    <script>
        $(document).ready(function(){
            $('#homework-select_contact').on('change', '.family-member-checkbox', function() {
                var contact_id     = $(this).data('contact_id');
                var $homework_list = $('.homework-list[data-contact_id="'+contact_id+'"]');

                if (this.checked) {
                    $homework_list.removeClass('hidden');
                } else {
                    $homework_list.addClass('hidden');
                }
            });
        })
    </script>
<?php endif; ?>