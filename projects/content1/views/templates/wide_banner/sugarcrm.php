<?php include 'template_views/html_document_header.php' ?>
<body id="Page-financial-services" class="default_layout">
    <div id="wrapper">
        <p class="topshadow"></p>
        <div id="page">
            <div id="container">
                <?php include 'template_views/header.php' ?>

                <!-- banner -->
                <div id="banner"><?= trim(Model_PageBanner::render_frontend_banners($page_data['banner_photo'])) ?></div>
                <!-- /banner -->

                <!-- main area -->
                <div id="iner-main">
                    <div class="ct"><?= trim($page_data['content']) ?>
                        <?php
                        // Request Invoice Id
                        if(isset($_REQUEST['sid'])){$invoice_id = $_REQUEST['sid'];}else $invoice_id ="";
                        if(isset($_REQUEST['iid'])){$iid = $_REQUEST['iid'];}else $iid ="";

                        if (empty($invoice_id) || empty($iid)) {
                            echo('Bad data request - Please note no invoice or request id was sent to this page for processing. Please re check your address.');
                            //http_response_code(400);
                            //die();
                        }
                        ?>
                        <div>
                            <div id="form" class="form-container active" style="display: none;">
                                <form class="checkout" id="sugarcrm_payment_form">
                                    <input type="hidden" id="account_id" name="account_id" value="">
                                    <input type="hidden" id="invoice_id" name="invoice_id" value="<?php echo $invoice_id; ?>">
                                    <input type="hidden" id="iid" name="iid" value="<?php echo $iid; ?>">
                                    <input type="hidden" id="total" name="amount" value="">
                                    <div class="checkout-header">
                                        <h1 class="checkout-title" id="invoice_name">
                                            <span class="checkout-price" id="amount">0</span>
                                        </h1>
                                    </div>
                                    <p>
										<label class="accessible-hide" for="card_type">Card type</label>
                                        <select class="checkout-input checkout-type validate[required]" name="card_type" id="card_type" autofocus>
                                            <option value="">Please Enter the Card Type</option>
                                            <option value="laser">Laser</option>
                                            <option value="visa_credit">Visa Credit</option>
                                            <option value="visa_debit_dom">Visa Debit Domestic including Visa Business Debit &amp; Electron</option>
                                            <option value="visa_debit_int">Visa Debit International including Visa Business Debit &amp; Electron</option>
                                            <option value="mc_credit">MasterCard Credit</option>
                                            <option value="mc_debit_dom">MasterCard Debit Domestic including MasterCard Business Debit</option>
                                            <option value="mc_debit_int">MasterCard Debit International including MasterCard Business Debit</option>
                                            <option value="mc_bus">MasterCard Business, Corporate and Purchasing</option>
                                        </select>
                                    </p>
                                    <p>
										<label class="accessible-hide" for="name">Full name</label>
                                        <input type="text" class="checkout-input checkout-name validate[required]" placeholder="Full Name" maxlength="200" id="name" name="name" />

										<label class="accessible-hide" for="cvv">CCV</label>
                                        <input type="text" class="checkout-input checkout-cvc validate[required,custom[onlyNumber],minSize[3],maxSize[3]]" placeholder="CVV" autocomplete="off" id="cvv" name="cvc" />
                                    </p>
                                    <p>
										<label class="accessible-hide" for="number">Card number</label>
                                        <input type="text" class="checkout-input checkout-card validate[required,custom[onlyNumber],minSize[12]]" placeholder="Card Number" autocomplete="off" id="number" name="number">

										<label class="accessible-hide" for="expiry">Expiration date (MMYY)</label>
                                        <input type="text" class="checkout-input checkout-exp validate[required,custom[onlyNumber],minSize[4],maxSize[4]]" placeholder="MMYY" id="expiry" name="expiry" />
                                    </p>
                                    <p>
                                        <input type="button" class="checkout-btn" id="submit" value="Submit">
                                    </p>
                                </form>
                            </div>
                            <div id="card" class="card-wrapper"  style="display: none;"></div>
                            <br />
                            <div class="alert" id="return"></div>
                        </div>
                    </div>
                    <div class="sidert">
                        <div id="content_panels" class="content_panels"><?= Model_Panels::get_panels_feed('content_right'); ?></div>
                    </div>
                </div>
                <!-- /main area -->

                <?php include 'template_views/footer.php' ?>
            </div>
        </div>
        <p class="botshadow"></p>
    </div>
    <script type="text/javascript" src="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/js/fdcPayment.js"></script>
</body>
<?php include 'template_views/html_document_footer.php' ?>

