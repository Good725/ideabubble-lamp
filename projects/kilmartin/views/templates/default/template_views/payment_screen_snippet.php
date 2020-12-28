<?php
$session = Session::instance();
$_bookings = $session->get('bookings');
$form_identifier = 'payment_';
?>

<form action="#" method="post" id="payment_form">
    <section class="content-section inner-content">
        <div class="title">Order Details</div>
        <!-- cart-list -->
        <section class="revision-block">
            <?php
            $ids = "";

            if (isset($_bookings) AND is_array($_bookings) AND count($_bookings) > 0) {
                ?>
                <h1><strong>Items</strong></h1>
                <?php
                foreach ($_bookings['cart'] as $_elem => $_key) {
                    if (!isset($_key['amount']) && is_array($_key)) {
                        $ids .= $_key['bid'] . "|";
                        ?>
                        <div class="order-row">
                            <span class="left"><?= $_key['title'] ?> (<?= $_key['schedule_d'] ?>)</span>

                            <div class="priceBox">
                                <span class="price">Price: €<?= $_key['price'] ?></span>
                                <a href="/frontend/courses/clear_cart" data-id="<?= $_key['bid'] ?>" class="remove-btn">Remove</a>
                            </div>
                        </div>
                    <?php
                    }
                    //end if
                }
                //end foreach
                if (Settings::instance()->get('admin_fee_toggle') === 'TRUE') {
                    echo '<div class="order-row">
                            <span class="left">Online Booking Fee</span>

                            <div class="priceBox">
                                <span class="price">Price: €' . Settings::instance()->get('admin_fee_price') . '</span>
                            </div>
                        </div>';
                }
                ?>
                <br class="spacer">
                <div class="divider2"><span></span></div>
                <div class="total-price"><strong>Total:</strong> €<?= $_bookings['cart']['amount'] ?></div>
                <span id='red_text'>*Fees are non transferable and non refundable </span>
            <?php
            } else {
                echo '<h1><strong>Cart is empty</strong></h1>';
            }
            ?>
        </section>
        <!-- /cart-list -->

        <?php
        if (isset($_bookings) AND is_array($_bookings) AND count($_bookings) > 0) {
            ?>
            <!-- payment-details -->
            <section class="revision-block">
                <h1><strong>Credit Card Payment Details</strong></h1>

                <div class="formBlock">
                    <section class="col1">
                        <input type="hidden" id="title" name="title" value="KES Booking"/>
                        <input type="hidden" id="amount" name="amount" value="<?= $_bookings['cart']['amount'] ?>"/>
                        <input type="hidden" id="ids" name="ids" value="<?= $ids ?>"/>
                        <input type="hidden" id="form_identifier" name="form_identifier"
                               value="<?= $form_identifier ?>"/>
                        <label id="ccType_p"><span>CARD TYPE</span></label>

                        <div class="selectbox">
                            <select class="styled" name="ccType" id="ccType">
                                <option value="">Please select</option>
                               <option value="visa">Visa</option>
                               <option value="mc">Mastercard</option>
                            </select>
                        </div>
                        <br class="spacer">
                        <label id="ccName_p"><span>CARD NAME</span></label>
                        <input name="ccName" id="ccName" type="text"
                               onkeyup="$('#<?= $form_identifier ?>form_name').val(this.value);"/>
                        <input name="<?= $form_identifier ?>form_name" id="<?= $form_identifier ?>form_name"
                               type="hidden" value=""/>
                        <br class="spacer">
                        <label id="ccAddress1_p"><span>ADDRESS 1</span></label>
                        <input name="ccAddress1" id="ccAddress1" class="validate[required]" type="text"/>
                        <br class="spacer">
                        <label id="ccAddress2_p"><span>ADDRESS 2</span></label>
                        <input name="ccAddress2" id="ccAddress2" class="validate[required]" type="text"/>
                        <br class="spacer">
                        <label id="ccNum_p"><span>CARD NO</span></label>
                        <input name="ccNum" id="ccNum" type="text"/>
                        <br class="spacer">
                        <label id="ccv_p"><span>CCV No.</span></label>
                        <input name="ccv" id="ccv" type="text"/>
                        <br class="spacer">
                        <label><span>EXPIRY</span></label>

                        <div class="month-yr">
                            <label id="ccExpMM_p" class="selectbox">
                                <select name="ccExpMM" id="ccExpMM" class="styled">
                                    <option value="">MM</option>
                                    <?php
                                    for ($i = 1; $i <= 12; $i++) {
                                        $j = str_pad($i, 2, "0", STR_PAD_LEFT);
                                        echo '<option value="' . $j . '">' . $j . '</option>';
                                    }
                                    ?>
                                </select>
                            </label>
                            <label id="ccExpYY_p" class="selectbox">
                                <select name="ccExpYY" id="ccExpYY" class="styled">
                                    <option value="">YYYY</option>
                                    <?php
                                    for ($i = date('y'); $i <= (date('y') + 10); $i++) {
                                        $j = str_pad($i, 2, "0", STR_PAD_LEFT);
                                        echo '<option value="' . $j . '">20' . $j . '</option>';
                                    }
                                    ?>
                                </select>
                            </label>
                        </div>
                        <br class="spacer">
                        <label id="<?= $form_identifier ?>email_p"
                               for="<?= $form_identifier ?>form_email_address"><span>EMAIL</span></label>
                        <input name="<?= $form_identifier ?>form_email_address"
                               id="<?= $form_identifier ?>form_email_address" class="validate[required,custom[email]]"
                               type="text"/>
                        <br class="spacer">
                        <label id="<?= $form_identifier ?>tel_p" for=""><span>PHONE</span></label>
                        <input name="<?= $form_identifier ?>form_tel" id="<?= $form_identifier ?>form_tel"
                               class="validate[required]" type="text"/>
                        <br class="spacer">
                    </section>

                    <section class="col4">
                        <label>COMMENTS</label>

                        <div class="fields-col">
                            <span class="txtarea"><textarea name="comments" id="comments"></textarea></span>

                            <p>
                                <input type="checkbox" id="signupCheckbox" name="signupCheckbox" value="1"
                                       class="styled" data-id="signupCheckbox_span"/>
                                I would like to sign up to the newsletter
                            </p>

                            <p id="contact_p">
                                <input type="checkbox" id="contact" name="contact" value="1" class="styled"
                                       data-id="contact_span"/>
                                I agree that KES can contact me in relation to student absence or disciplinary issues.
                            </p>

                            <p id="accept_p">
                                <input type="checkbox" id="accept" name="accept" value="1" class="styled"
                                       data-id="accept_span"/>
                                I accept the <a href="/terms-and-conditions.html">term and conditions</a>
                            </p>
                        </div>
                    </section>
                </div>
            </section>
            <!-- /payment-details -->
            <div class="right">
                <button class="button blue" id="submit-checkout"><span><span>CONFIRM BOOKING »</span></span></button>
            </div>
        <?php
        }
        ?>
    </section>
</form>