<?php
$is_manager = isset($is_manager) ? $is_manager : FALSE;
$is_admin = isset($is_admin) ? $is_admin : FALSE;
?>

<link rel="stylesheet" href="<?= URL::get_engine_plugin_assets_base('cardbuilder') ?>css/frontend/styles.css" type="text/css"/>

<div>
    <label for="filter_by_status">Filter Status</label>
    <select id="filter_by_status">
        <option value="">Select Status</option>
        <option value="pending">Pending Approval</option>
        <option value="approved">Approved</option>
        <option value="printed">Printed</option>
    </select>
</div>

<div id="orders_table_wrapper" class="orders_table_wrapper">
    <table id="orders_table" class="orders_table">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <?php if ($is_manager): ?>
                    <th scope="col">Order</th>
                <?php endif; ?>
                <th scope="col" style="width: 7.5em;">Created</th>
                <th scope="col" style="width: 7.5em;">Modified</th>
                <th scope="col">Status</th>
                <th scope="col">Employee Name</th>
                <th scope="col">Title</th>
                <th scope="col">Department</th>
                <th scope="col">View</th>
                <?php if ($is_admin): ?>
                    <th scope="col">Delete</th>
                <?php endif; ?>
                <?php if ($is_manager): ?>
                    <th scope="col">
                        <label class="approve_checkboxes_all_wrapper"><input id="approve_checkboxes_all" type="checkbox"/> All</label>
                    </th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cards as $card): ?>
                <?php // span class="hidden" at the start of cells contains sorting-friendly versions of the cell content ?>

                <?php $status = ($card['printed'] == 1) ? 'printed' : (($card['approved'] == 1) ? 'approved' : 'pending'); ?>
                <tr data-id="<?= $card['id'] ?>" data-status="<?= $status ?>" class="card_<?= $status ?>">
                    <td><span class="hidden"><?= str_pad($card['id'], 5, '0', STR_PAD_LEFT) ?></span><?= $card['id'] ?>
                    </td>
                    <?php if ($is_manager): ?>
                        <td>
                            <span class="hidden"><?= str_pad($card['order_id'], 5, '0', STR_PAD_LEFT) ?></span><?= $card['order_id'] ?>
                        </td>
                    <?php endif; ?>
                    <td>
                        <span class="hidden"><?= date('Y-M-d', strtotime($card['date_created'])) ?></span><?= date('d M Y', strtotime($card['date_created'])) ?>
                    </td>
                    <td>
                        <span class="hidden"><?= date('Y-M-d', strtotime($card['date_modified'])) ?></span><?= date('d M Y', strtotime($card['date_modified'])) ?>
                    </td>
                    <td><?= ($status == 'printed') ? 'Printed' : (($status == 'approved') ? 'Approved' : 'Pending'); ?></td>
                    <td><?= $card['employee_name'] ?></td>
                    <td><?= $card['title'] ?></td>
                    <td><?= $card['department'] ?></td>
                    <td><a href="/card-builder.html/<?= $card['id'] ?>">View</a></td>
                    <?php if ($is_admin): ?>
                        <td><span class="delete_icon" data-id="<?= $card['id'] ?>"></span></td>
                    <?php endif; ?>
                    <?php if ($is_manager): ?>
                        <td>
                            <span class="hidden checked_value"></span><label><input class="approve_checkbox" type="checkbox" data-id="<?= $card['id'] ?>"<?= $card['approved'] == 1 ? ' disabled="disabled"' : ''; ?> /></label>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php if ($is_manager): ?>
    <button id="approve_order_button" class="primary_button approve_order_button" type="button">Approve Order</button>
<?php endif; ?>
<div class="accessible-hide">
    <?php
    $card = $blank_card;
    include 'card_builder.php';
    ?>
</div>

<?php if ($is_manager): ?>
    <!-- Modal boxes -->
    <div id="approval_failed_modal" class="cb-modal-overlay">
        <div class="cb-modal">
            <div class="cb-modal-head">
                <div class="cb-modal-dismiss">&times;</div>
                <h3>Error</h3>
            </div>
            <div class="cb-modal-body">
                <p>You have not selected any cards.</p>
            </div>
            <div class="cb-modal-foot">
                <button type="button" class="cb-modal-dismiss default_button">OK</button>
            </div>
        </div>
    </div>

    <div id="order_processing_modal" class="cb-modal-overlay">
        <div class="cb-modal">
            <div class="cb-modal-head">
                <h3>Processing order</h3>
            </div>
            <div class="cb-modal-body">
                <p>Please wait while your order is processed.</p>
            </div>
            <div class="cb-modal-foot"></div>
        </div>
    </div>


    <div id="order_placed_modal" class="cb-modal-overlay">
        <div class="cb-modal">
            <div class="cb-modal-head">
                <div class="cb-modal-dismiss">&times;</div>
                <h3>Order placed</h3>
            </div>
            <div class="cb-modal-body">
                <p>Order successfully placed.</p>
            </div>
            <div class="cb-modal-foot">
                <button type="button" class="cb-modal-dismiss primary_button">OK</button>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if ($is_admin): ?>
    <div id="delete_card_modal" class="cb-modal-overlay">
        <div class="cb-modal">
            <div class="cb-modal-head">
                <div class="cb-modal-dismiss">&times;</div>
                <h3>Confirm Deletion</h3>
            </div>
            <div class="cb-modal-body">
                <p>Are you sure you want to delete card #<span id="confirm_delete_sign_id"></span>?</p>
            </div>
            <div class="cb-modal-foot">
                <a href="#" class="danger_button" id="delete_card_button">Delete</a>
                <button type="button" class="cb-modal-dismiss default_button">Cancel</button>
            </div>
        </div>
    </div>
<?php endif; ?>
<img id="cb-logo" src="/assets/14/images/regeneron_ireland-logo.svg" style="display: none;"/><?php // to be genericised  ?>
<script src="<?= URL::get_engine_plugin_assets_base('cardbuilder') ?>js/frontend/order_listing.js"></script>
<script src="<?= URL::get_engine_plugin_assets_base('cardbuilder') ?>js/frontend/cardbuilder.js"></script>
