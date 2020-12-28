<br/><br/>
<div class="order_details_header">
    <h1>Order Details</h1>
    <?php if (@is_array($course)): ?>
        <div class="order_details_header_top">
            <span style="margin-left:10px;width:490px;margin-top:25px;">Item</span><span
                style="float:right;">Price</span><span></span>
        </div>
        <div style="width:100%;float:left;">
            <section class="course-detail-box">
                <span class="course_purchase"><?= $course['title'] ?></span>
                <span class="course_price">€ <?= $course['fee_amount'] ?></span>
            </section>
        </div>
        <div class="order_details_header_bottom"></div>
        <input type="hidden" id='amount' name="amount" value="<?= $course['fee_amount'] ?>"/>
        <input type="hidden" id='title' name="title" value="<?= $course['title'] ?>"/>
        <input type="hidden" id='code' name="code" value="<?= md5(rand(1, 1000) . "&*%768" . rand(1, 2000)) ?>"/>
        <input type="hidden" id='schedule_id' name="schedule_id" value="<?= $_GET['id'] ?>"/>
        <input type="hidden" id='booking_id' name="booking_id" value=""/>

    <?php endif; ?>

</div>