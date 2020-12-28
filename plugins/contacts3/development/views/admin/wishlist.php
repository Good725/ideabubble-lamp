<?php $child = true; ?>

<?php if (Auth::instance()->has_access('contacts3_limited_family_access')): ?>
    <?php
    $child = false;

    if (isset($contacts[0])) {
        echo View::factory('frontend/snippets/family_members')
            ->set('family', $contacts[0]['family_id'])
            ->set('attributes', array('class' => 'wishlist-select_contact'));
    }
    ?>
<?php endif; ?>

<?php foreach ($contacts as $contact): ?>
    <?php
    $wishes = @$wishlist[$contact['id']];
    $contact_id = $contact['id'];
    ?>
    <?php if (count($wishes) > 0): ?>
        <div class="wishlist clearfix" data-contact_id="<?=$contact_id?>">
            <h2><?= $contact['first_name'] . ' ' . $contact['last_name'] ?>’s Wishlist</h2>

            <div class="table_scroll">
                <table class="table table-striped table-hover table-wishlist">
                    <thead>
                        <tr>
                            <th scope="col">Title</th>
                            <th scope="col">Schedule</th>
                            <th scope="col">Unit price</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php $total = 0; ?>
                        <?php foreach ($wishes as $wish): ?>
                            <?php $total += $wish['fee_amount']; ?>
                            <tr>
                                <td><h5><?= $wish['schedule'] ?></h5></td>
                                <td><?= $wish['start_date'] ?></td>
                                <td>&euro;<?= $wish['fee_amount'] ?></td>
                                <td>
                                        <div class="action-btn">
                                            <a class="btn" href="#"><span class="icon-ellipsis-h" aria-hidden="true"></span></a>
                                            <ul>
                                                <li><a href="/frontend/bookings/add_to_cart?course_id=<?= $wish['course_id'] ?>&add_to_cart_schedule_id=<?= $wish['schedule_id'] ?>&add_to_cart_timeslot_id=<?= @$wish['timeslot_id'] ?>&add_to_cart_contact_id=<?= $contact_id ?>">Add to Cart</a></li>
                                                <li><a class="wishlist_remove" data-contact_id="<?= $contact_id ?>" data-schedule_id="<?= $wish['schedule_id'] ?>">Remove from Wishlist</a></li>
                                            </ul>
                                        </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="total-wrap right">Total : <span class="total-price">&euro;<?= number_format($total, 2, '.', ',') ?></span></div>
        </div>
    <?php else: ?>
        <div class="wishlist clearfix" data-contact_id="<?= $contact_id ?>">
            <h2><?= trim($contact['first_name'] . ' ' . $contact['last_name']) ?>’s Wishlist is empty</h2>
        </div>
    <?php endif; ?>
<?php endforeach; ?>

<style>
    .table-wishlist {
        margin-top: 2em;
    }

    .table-wishlist tbody .action-btn ul li a {
        font-size: 13px;
    }

    .total-wrap{
        background: #f4f4f4;
        border: 1px solid #969696;
        border-radius: 5px;
        font-size: 24px;
        font-weight: bold;
        margin-top: 20px;
        padding: 15px 70px;
    }
    .total-wrap .total-price {
        padding-left: 50px;
    }

</style>

<script>
	$(document).ready(function(){
		$(document).on('click', '.wishlist_remove', function(){
			var button = this;
			var contact_id = $(this).data('contact_id');
			var schedule_id = $(this).data('schedule_id');

			$.post(
					'/admin/contacts3/wishlist_remove',
					{
						contact_id: contact_id,
						schedule_id: schedule_id
					},
					function (response) {
						window.location.reload();
					}
			);
		});

        $(document).on('change', '.wishlist-select_contact .family-member-checkbox', function()
        {
            var tab = document.querySelector('.wishlist[data-contact_id="' + $(this).data('contact_id') + '"]');

            if (this.checked) {
                tab.classList.remove('hidden');
            } else {
                tab.classList.add('hidden');
            }
        });

	});
</script>
