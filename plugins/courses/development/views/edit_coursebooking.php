<script>
window.coursebooking_default = <?=json_encode($booking)?>;
window.coursebooking_data = <?=json_encode($booking)?>;
</script>

<div class="row booking-top-area">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 booking-title">
		<h3><?=$booking ? __('Edit booking') . '<span>#' . $booking['id'] . '</span>' : __('Add new booking')?></h3>
		<ul>
			<?=$booking ? '<li><a>' . $booking['status'] . '</a></li>' : ''?>
			<?=$booking ? '<li><a href="/admin/contacts2/edit/' . $booking['payer']['id'] . '">' . $booking['payer']['first_name'] . ' ' . $booking['payer']['last_name'] . '</a></li>' : ''?>
		</ul>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-right booking-top-btn">
		<span class="booking-bal-area">Balance = <span class="balance"><?=number_format(@$booking['outstanding'], 2)?></span></span>
		<ul class="nav nav-pills booking-nav" role="tablist"> 
			<li role="presentation" class="dropdown"><a href="#" class="dropdown-toggle" id="drop4" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"> Action <span class="caret"></span> </a>
				 <ul class="dropdown-menu" id="menu1" aria-labelledby="drop4">
                 <?php
                     echo
                     (Auth::instance()->has_access('courses') && $booking['status'] != 'Cancelled' ? '<li><a class="cancel" data-outstanding="' . $booking['outstanding'] . '" data-booking_id="' . $booking['id'] . '" class="edit-link"><span class="icon-pencil"></span>' .  __('Cancel') . '</a></li>' : '') .
                     (Auth::instance()->has_access('courses') && $booking['status'] != 'Cancelled' ? '<li><a class="transfer" data-booking_id="' . $booking['id'] . '" class="edit-link"><span class="icon-pencil"></span>' .  __('Transfer Away') . '</a></li>' : '');
                 ?>
				</ul>
			</li>
		</ul>
	</div>
</div>
<div class="row booking-mid-area">
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 booking-table-title">
		<h3>Classes</h3>
	</div>
	<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-right booking-top-btn">
		<ul class="booking-filter-list nav nav-tabs">
			<li><a href="#booking_list_table" data-toggle="tab"><span class="flaticon-calendar" aria-hidden="true"></span> Calender</a></li>
			<li><a href="#booking_edit_table" data-toggle="tab"><span class="flaticon-list" aria-hidden="true"></span> List View</a></li>
		</ul>
	</div>
</div>
<div class="tab-content">
    <form class="course-booking-form" method="post" action="/admin/courses/save_booking">
        <input type="hidden" name="action" value="" />
        <input type="hidden" name="booking[id]" value="<?=@$booking['id']?>" />
        <input type="hidden" name="booking[status]" value="<?=@$booking['status'] ? $booking['status'] : 'Processing'?>" />
	<div class="tab-pane active" role="tabpanel" id="booking_edit_table">
		<table class="table table-striped" aria-describedby="booking_edit_table_info">
			<thead>
				<tr role="row">
					<th class="sorting" rowspan="1" colspan="1" style="width: 108px;">Courses</th>
					<th class="sorting" rowspan="1" colspan="1" style="width: 183px;">Schedule</th>
					<th class="sorting" rowspan="1" colspan="1" style="width: 86px;">Day</th>
					<th class="sorting" rowspan="1" colspan="1" style="width: 100px;">Date</th>
					<th class="sorting" rowspan="1" colspan="1" style="width: 99px;">Time</th>
					<th class="sorting" rowspan="1" colspan="1" style="width: 84px;">Attend</th>
					<th rowspan="1" colspan="1" style="width: 432px;">Notes</th>
				</tr>
			</thead> 
			<tbody role="alert" aria-live="polite" aria-relevant="all">
            <?php
            if (is_array(@$booking['has_schedules']))
            foreach ($booking['has_schedules'] as $i => $has_schedule) {
                foreach ($has_schedule['has_timeslots'] as $j => $has_timeslot) {
            ?>
                <tr role="row" data-has_timeslot_id="<?=$has_timeslot['timeslot_id']?>">
                    <td rowspan="1" colspan="1" style="width: 108px;">
                        <input type="hidden" name="booking[has_schedules][<?=$i?>][has_timeslots][<?=$j?>][id]" value="<?=@$has_timeslot['id']?>" />
                        <input type="hidden" name="booking[has_schedules][<?=$i?>][has_timeslots][<?=$j?>][timeslot_id]" value="<?=@$has_timeslot['timeslot_id']?>" />
                        <?=$has_timeslot['course']?>
                    </td>
                    <td rowspan="1" colspan="1" style="width: 183px;"><?=$has_timeslot['schedule']?></td>
                    <td rowspan="1" colspan="1" style="width: 86px;"><?=date::format('D', $has_timeslot['datetime_start'])?></td>
                    <td rowspan="1" colspan="1" style="width: 100px;"><?=date::format('M d', $has_timeslot['datetime_start'])?></td>
                    <td rowspan="1" colspan="1" style="width: 99px;"><?=date::format('H:i', $has_timeslot['datetime_start']) . ' - ' . date::format('H:i', $has_timeslot['datetime_end'])?></td>
                    <td rowspan="1" colspan="1" style="width: 84px;">
                        <input type="hidden" name="booking[has_schedules][<?=$i?>][has_timeslots][<?=$j?>][attend]" value="0" />
                        <input type="checkbox" name="booking[has_schedules][<?=$i?>][has_timeslots][<?=$j?>][attend]" <?=$has_timeslot['attend'] != "0" ? 'checked="checked"' : ''?> value="1" />Yes
                    </td>
                    <td rowspan="1" colspan="1" style="width: 432px;">
                        <input type="text" name="booking[has_schedules][<?=$i?>][has_timeslots][<?=$j?>][note]" value="<?=html::chars($has_timeslot['note'])?>" />
                    </td>
                </tr>
            <?php
                }
            }
            ?>
			</tbody>
		</table>
		<div class="row booking-paging-area">
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 booking-pagination">
                <span>Showing 1 to 3 of 3 entries</span>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 text-right booking-top-btn">
                <ul class="booking-prev-next-btn">
                    <li><a href="#">Previous</a></li>
                    <li><a href="#">Next</a></li>
                </ul>
            </div>
        </div>

        <div class="row booking-mid-area">
                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 booking-table-title">
                    <h3>Courses</h3>
                </div>
        </div>
        <table class="table table-striped" id="booking_payment_table" aria-describedby="booking_payment_table_info">
            <thead>
                <tr role="row">
                    <th rowspan="1" colspan="1" style="width: 39px;"></th>
                    <th class="sorting" rowspan="1" colspan="1" style="width: 168px;">Courses</th>
                    <th class="sorting" rowspan="1" colspan="1" style="width: 232px;">Schedule</th>
                    <th class="sorting" rowspan="1" colspan="1" style="width: 68px;">Type</th>
                    <th class="sorting" rowspan="1" colspan="1" style="width: 88px;">Classes</th>
                    <th class="sorting" rowspan="1" colspan="1" style="width: 127px;">Start Date</th>
                    <th class="sorting" rowspan="1" colspan="1" style="width: 124px;">Next Payment</th>
                    <th class="sorting" rowspan="1" colspan="1" style="width: 137px;">Due</th>
                </tr>
            </thead>
            <tbody role="alert" aria-live="polite" aria-relevant="all">
                <?php
                if (is_array(@$booking['has_schedules']))
                foreach ($booking['has_schedules'] as $i => $has_schedule) {
                ?>
                <tr class="<?=$i % 2 == 0 ? 'even' : 'odd'?>">
                    <td rowspan="1" colspan="1" style="width: 39px;">
                        <input type="hidden" name="booking[has_schedules][<?=$i?>][id]" value="<?=@$has_schedule['id']?>" />
                        <input type="hidden" name="booking[has_schedules][<?=$i?>][schedule_id]" value="<?=@$has_schedule['schedule_id']?>" />
                        <input type="hidden" name="booking[has_schedules][<?=$i?>][status]" value="<?=@$has_schedule['status'] ? $has_schedule['status'] : 'Processing'?>" />
                        <input type="checkbox" name="booking[has_schedules][<?=$i?>][deleted]" value="1" />
                    </td>
                    <td rowspan="1" colspan="1" style="width: 168px;"><?=$has_schedule['course']?></td>
                    <td rowspan="1" colspan="1" style="width: 232px;"><?=$has_schedule['name']?></td>
                    <td rowspan="1" colspan="1" style="width: 68px;"><?=$has_schedule['payment_type'] == 1 ? 'PAYG' : 'PrePAY'?></td>
                    <td rowspan="1" colspan="1" style="width: 88px;"><?=count($has_schedule['has_timeslots'])?></td>
                    <td rowspan="1" colspan="1" style="width: 127px;"><?=date('d/m/Y', strtotime($has_schedule['start_date']))?></td>
                    <td rowspan="1" colspan="1" style="width: 124px;"><?=date('d/m/Y', strtotime($has_schedule['end_date']))?></td>
                    <td rowspan="1" colspan="1" style="width: 137px;">&euro; <?=$has_schedule['due']?></td>
                </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
        <div class="row booking-paging-area">

            <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12 pull-right">
                <ul class="booking-total">
                    <li>Sub Total <span>&euro; <?=$booking['all_fee']?></span></li>
                    <?php if (count($booking['has_discounts']) > 0) { ?>
                    <?php foreach ($booking['has_discounts'] as $discount) { ?>
                    <li><?=$discount['title']?> <span>-&euro; <?=$discount['amount']?></span></li>
                    <?php } ?>
                    <li>Total <span>&euro; <?=$booking['all_total']?></span></li>
                    <?php } ?>
                    <li>Due Now <span>&euro; <?=$booking['outstanding']?></span></li>
                </ul>
            </div>
        </div>

        <div class="row booking-bot-btn-area">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <ul class="booking-btns">
                    <?php if (!in_array(@$booking['status'], array('Cancelled'))) {?>
                    <li><a data-action="save">Save</a></li>
                    <?php } ?>

                    <?php if (!Auth::instance()->has_access('courses_limited_access')) { ?>

                    <?php if (in_array(@$booking['status'], array('Processing', 'Pending'))) {?>
                        <li><a data-action="confirm">Confirm</a></li>
                        <li><a data-action="cancel">Cancel</a></li>
                    <?php } ?>
                    <?php if (!in_array(@$booking['status'], array('Confirmed', 'Processing', 'Pending', 'Cancelled'))) {?>
                    <li><a data-action="pay">Book &amp; Pay</a></li>
                    <li><a data-action="bill">Book &amp; Bill</a></li>
                    <li><a>Cancel</a></li>
                    <?php } ?>

                    <?php } ?>

                    <?php
                    if (in_array(@$booking['status'], array('Confirmed')) && $booking['outstanding'] > 0) {
                        foreach ($booking['has_transactions'] as $has_transaction) {
                            if ($has_transaction['outstanding'] > 0) {
                    ?>
                        <li><a class="make-payment" data-transaction_id="<?=$has_transaction['id']?>">Make Payment</a></li>
                    <?php
                            }
                        }
                    }
                    ?>
                </ul>
            </div>
        </div>
	</div>

	<div class="tab-pane hidden" role="tabpanel" id="booking_list_table">
		<div class="calendar-panel">
			Calendar code here
		</div>

		<div class="checkout-panel">
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
				<h3>Check out</h3>
				<p>Select your payment method</p>
				<ul class="pay-ul">
					<li><a href="#"></a></li>
					<li><a href="#"></a></li>
				</ul>
				<h4>Credit Card Payment Detials</h4>
				<form>
					<ul>
						<li>
							<label>Card Type</label>
							<select>
								<option></option>
								<option></option>
								<option></option>
							</select>
						</li>
						<li>
							<label>Card No</label>
							<input type="text" value="" />
						</li>
						<li>
							<label>CVV No</label>
							<input type="text" value="" />
						</li>
						<li>
							<label>Expiry</label>
							<select class="month">
								<option>MM</option>
								<option>MM</option>
								<option>MM</option>
							</select>
							<select class="year">
								<option>YYYY</option>
								<option>YYYY</option>
								<option>YYYY</option>
							</select>
						</li>
					</ul>
				</form>
				<p class="desc">We use a secure certificare for all our payments <br /> and Realex our payment partner provide all <br/> secure  connections for your transaction.</p>
				
				<ul class="checkbox-ul"> 
					<li><input type="checkbox" id="test1" value="" /> <label for="test1">I would like to sign up to the newsletter </label></li>
					<li><input type="checkbox" id="test2" value="" /> <label for="test2">I accept the <a href="javascript:void(0);">Terms and Conditions</a></label></li>
				</ul>
				<ul class="checkout-links-ul">
					<li><a href="">Pay now</a></li>
					<li><a href="">Cancel</a></li>
				</ul>
			</div>
		</div>
	</div>
    </form>
</div>





