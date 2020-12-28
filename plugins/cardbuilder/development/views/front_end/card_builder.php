<?php $logged_in_user = Auth::instance()->get_user(); ?>
<?= isset($alerts) ? $alerts : ''?>
<?php if ( ! isset($message) OR $message == ''): ?>
    <div class="card_builder_wrapper">
        <header>
            <h1>Order a Business Card</h1>
        </header>

        <form id="card_builder_form" action="/frontend/cardbuilder/save_card" method="post">
            <input id="cb-id" type="hidden" name="id" value="<?= $card['id'] ?>" />
            <input id="cb-redirect" type="hidden" name="redirect" />
            <input type="hidden" name="location" value="card-builder.html" />

            <div class="card_builder_canvas_wrapper">
                <h2 class="subtitle"><?= ($card['id'] != '') ? 'Editing card #'.$card['id'] : 'Create a new card' ?></h2>
                <canvas id="card_builder_canvas" class="card_builder_canvas" width="600">Your Browser does not support this feature.</canvas>
            </div>

            <div id="card_builder_controls" class="card_builder_controls">
                <h2>Enter Card Details</h2>
                <fieldset>
                    <dl>
                        <dt><label for="cb-employee_name">Employee Name</label></dt>
                        <dd><input class="update_card" id="cb-employee_name" name="employee_name" value="<?= $card['employee_name'] ?>" type="text" /></dd>
						<dt><label for="cb-post_nominal_letters">Post-Nominal Initials</label></dt>
						<dd><input class="update_card" id="cb-post_nominal_letters" name="post_nominal_letters" value="<?= $card['post_nominal_letters'] ?>" type="text" /></dd>
                        <dt><label for="cb-title">Title</label></dt>
                        <dd><input class="update_card" id="cb-title" name="title" value="<?= $card['title'] ?>" type="text" /></dd>
                        <dt><label for="cb-department">Department</label></dt>
                        <dd><input class="update_card" id="cb-department" name="department" value="<?= $card['department'] ?>" type="text" /></dd>
                        <dt><label for="cb-telephone">Telephone</label></dt>
                        <dd><input class="update_card" id="cb-telephone" name="telephone" value="<?= $card['telephone'] ?>" type="text" /></dd>
                        <dt><label for="cb-fax">Fax</label></dt>
                        <dd><input class="update_card" id="cb-fax" name="fax" value="<?= $card['fax'] ?>" type="text" /></dd>
                        <dt><label for="cb-mobile">Mobile</label></dt>
                        <dd><input class="update_card" id="cb-mobile" name="mobile" value="<?= $card['mobile'] ?>" type="text" /></dd>
                        <dt><label for="cb-email">Email</label></dt>
                        <dd><input class="update_card" id="cb-email" name="email" value="<?= $card['email'] ?>" type="email" /></dd>
                        <dt><label for="cb-office">Office</label></dt>
                        <dd>
                            <select class="update_card" id="cb-office" name="office_id">
                                <option value="">Select Office</option>
                                <option value="1"<?= ($card['office_id'] == 1) ? ' selected="selecetd"' : '' ?>
                                    data-name="limerick"
									data-country="ie"
                                    data-address="Regeneron Ireland
                                                  Raheen Business Park
                                                  Limerick
                                                  Ireland"
                                    >Limerick</option>
								<option value="2"<?= ($card['office_id'] == 2) ? ' selected="selecetd"' : '' ?>
										data-name="dublin"
										data-country="ie"
										data-address="Regeneron Ireland
                                                  Europa House
                                                  Block 9 Harcourt Centre
                                                  Harcourt Street
                                                  Dublin 2
                                                  Ireland"
									>Dublin</option>
                            </select>
                        </dd>
                    </dl>
                </fieldset>
                <button id="place_order_button"          class="primary_button order_card_button" type="submit" data-redirect="save">         <?= ($card['id'] == '') ? 'Order' : 'Update' ?> Card</button>
                <button id="place_order_and_exit_button" class="primary_button order_card_button" type="submit" data-redirect="save_and_exit"><?= ($card['id'] == '') ? 'Order' : 'Update' ?> Card and Exit</button>
            </div>
        </form>

	</div>
	<img id="cb-logo" src="/assets/14/images/regeneron_ireland-logo.svg" style="display: none;" /><?php // to be genericised  ?>

<?php else: ?>
    <p><?= $message ?></p>
<?php endif; ?>
<script src="<?= URL::get_engine_plugin_assets_base('cardbuilder') ?>js/frontend/cardbuilder.js"></script>
