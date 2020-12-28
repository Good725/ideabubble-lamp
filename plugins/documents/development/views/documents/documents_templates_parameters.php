<fieldset id="outstanding_transactions" class="toggleable-block">
    <div class="form-group">
        <label class="col-sm-5 control-label" for="outstanding_transaction">Outstanding Transaction:</label>

        <div class="col-sm-7">
            <select id="outstanding_transaction" class="form-control">
                <option value="">Select an Outstanding Transaction</option>
                <?php foreach ($outstandings AS $key => $transac): ?>
                    <option value="<?= $transac['id']; ?>">Transaction#<?= $transac['id']; ?> - Type: <?=$transac['type']; ?> - Booking#<?=$transac['booking_id']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
</fieldset>

<fieldset id="payg_transactions" class="toggleable-block">
    <div class="form-group">
        <label class="col-sm-5 control-label" for="payg_transaction">PAYG Bookings:</label>

        <div class="col-sm-7">
            <select id="payg_transaction" class="form-control">
                <option value="">Select a PAYG Booking</option>
                <?php foreach ($payg AS $item): ?>
                    <option value="<?= $item['id'];?>">Booking#<?= $item['booking_id']; ?> - PAYG - Transaction#<?= $item['id']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
</fieldset>

<fieldset id="cancel_payg_transactions" class="toggleable-block">
    <div class="form-group">
        <label class="col-sm-5 control-label" for="cancel_payg_transaction">Cancelled PAYG Bookings:</label>

        <div class="col-sm-7">
            <select id="cancel_payg_transaction" class="form-control">
                <option value="">-- Select a PAYG Booking --</option>
                <?php foreach ($cancelled_payg AS $item): ?>
                    <option value="<?= $item['id'];?>">Booking#<?= $item['booking_id']; ?> - Cancelled PAYG - Transaction#<?= $item['id']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
</fieldset>

<fieldset id="modal_payments_made" class="toggleable-block">
    <div class="form-group">
        <label class="col-sm-5 control-label" for="payment_made">Payments completed:</label>
        
        <div class="col-sm-7">
            <select class="form-control" id="payment_made">
                <option value="">-- Select a Payment for your receipt --</option>
                <?php foreach ($payments AS $item): ?>
                    <option value="<?= $item['id'];?>"><?=$item['payment_type'] ;?>Payment#<?= $item['id']; ?> - on Transaction: <?= $item['transaction']; ?></option>
                <?php endforeach; ?>
            </select>            
        </div>
    </div>
</fieldset>

<fieldset id="document-templates-param-all_bookings-wrapper" class="toggleable-block">
    <div class="form-group">
        <label class="col-sm-5 control-label" for="document-templates-param-all_booking">Bookings:</label>

        <div class="col-sm-7">
            <?php
            $options = [];
            foreach ($bookings as $booking) {
                $options[$booking['booking_id']] = 'Booking#' . $booking['booking_id'] . ' - Schedules: ' . strip_tags($booking['schedule_title']);
            }
            echo Form::ib_select(null, null, $options, null, ['id' => 'document-templates-param-all_booking'], ['please_select' => true]);
            ?>
        </div>
    </div>
</fieldset>

<fieldset id="modal_confirmed_bookings" class="toggleable-block">
    <div class="form-group">
        <label class="col-sm-5 control-label" for="confirmed_booking">Confirmed Bookings:</label>

        <div class="col-sm-7">
            <select class="form-control" id="confirmed_booking">
                <option value="">-- Select a confirmed Booking --</option>
                <?php foreach ($confirmed_bookings AS $item): ?>
                    <option value="<?= $item['booking_id'];?>">Booking#<?= $item['booking_id']; ?> - Schedules: <?= $item['schedule_title']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
</fieldset>

<fieldset id="modal_cancelled_bookings" class="toggleable-block">
    <div class="form-group">
        <label class="col-sm-5 control-label" for="cancelled_booking">Cancelled Bookings:</label>

        <div class="col-sm-7">
            <select class="form-control" id="cancelled_booking">
                <option value="">-- Select a cancelled Booking --</option>
                <?php foreach ($cancelled_bookings AS $item): ?>
                    <option value="<?= $item['transaction_id'];?>">Transaction #<?= $item['transaction_id'] ?>, Booking#<?= $item['booking_id']; ?>, Schedule: <?= $item['schedule_title']; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
</fieldset>

<fieldset class="toggleable-block" id="document-templates-param-report_card_wrapper">
    <div class="form-group">
        <label class="col-sm-5 control-label" for="document-template-param-academic_year"><?= htmlspecialchars(__('Academic year')) ?>*</label>

        <div class="col-sm-7">
            <?= Form::ib_select(null, null, $academic_years->as_options(), null, ['id' => 'document-template-param-academic_year']); ?>
        </div>
    </div>
    <div class="form-group document-template-param-todo_categories-wrapper">
        <label class="col-sm-5 control-label" for="document-template-param-todo_categories"><?= htmlspecialchars(__('Categories')) ?>*</label>

        <div class="col-sm-7">
            <?php
            $args = [
                'multiselect_options' => [
                    'enableCaseInsensitiveFiltering' => true,
                    'enableClickableOptGroups' => true,
                    'enableFiltering' => true,
                    'includeSelectAllOption' => true,
                    'maxHeight' => 460,
                    'numberDisplayed' => 1,
                    'selectAllText' => __('ALL')
                ]
            ];
            $todo_categories    = ORM::factory('todo_item')->todo_category->find_all_undeleted()->as_array('id', 'title');
            echo Form::ib_select(__('Select todo categories'), 'document-template-param-todo_categories[]', $todo_categories,
                null, array('class' => 'multiple_select document-template-param-todo_categories', 'multiple' => 'multiple'), $args);?>
        </div>
    </div>
</fieldset>


<fieldset class="toggleable-block" id="document-templates-param-exam-wrapper">
    <div class="form-group">
        <label class="col-sm-5 control-label" for="document-template-param-exam"><?= htmlspecialchars(__('Exam')) ?>*</label>

        <div class="col-sm-7">
            <?= Form::ib_select(null, null, $exams->as_options(), null, ['id' => 'document-template-param-exam']); ?>
        </div>
    </div>
</fieldset>

<fieldset class="toggleable-block" id="document-templates-param-tutor-wrapper">
    <div class="form-group">
        <label class="col-sm-5 control-label" for="document-template-param-tutor"><?= htmlspecialchars(__('Tutor')) ?></label>

        <div class="col-sm-7">
            <?= Form::ib_select(null, null, $tutors->as_options(), null, ['id' => 'document-template-param-tutor']); ?>
        </div>
    </div>
</fieldset>

<fieldset class="toggleable-block" id="courses-list">
    <div class="form-group">
        <label class="col-sm-5 control-label" for="document-template-param-tutor"><?= htmlspecialchars(__('Courses')) ?></label>

        <div class="col-sm-7">
            <?= Form::ib_select(null, null, $courses->as_options(), null, ['id' => 'course_id']); ?>
        </div>
    </div>
</fieldset>