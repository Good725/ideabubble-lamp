<!-- Footer -->
<div id="footer" class="left">
    <div class="row_1">
        <div class="lt">
            <dl id="contact_details">
                <?php
                $address1 = Settings::instance()->get('addres_line_1');
                $address2 = Settings::instance()->get('addres_line_2');
                $address3 = Settings::instance()->get('addres_line_3');
                $telephone = Settings::instance()->get('telephone');
                $fax = Settings::instance()->get('fax');
                $mobile = Settings::instance()->get('mobile');
                $email = Settings::instance()->get('email');
                ?>
                <?php if ($address1 != ""): ?>
                    <dt class="address">Address</dt>
                    <dd class="address">
                        <address>
                            <span class="line"><?=$address1; ?></span>
                            <?=($address2 != '') ? '<span class="line">' . $address2 . '</span>' : ''; ?>
                            <?=($address3 != '') ? '<span class="line">' . $address3 . '</span>' : ''; ?>
                        </address>
                    </dd>
                <?php endif; ?>
                <?php if ($telephone != ""): ?>
                    <dt>Phone</dt>
                    <dd><?=$telephone ?></dd>
                <?php endif; ?>
                <?php if ($fax != ""): ?>
                    <dt>Fax</dt>
                    <dd><?= Settings::instance()->get('fax'); ?></dd>
                <?php endif; ?>
                <?php if ($mobile != ""): ?>
                    <dt>Mobile</dt>
                    <dd><?= Settings::instance()->get('mobile'); ?></dd>
                <?php endif; ?>
                <?php if ($email != ""): ?>
                    <dt>E-mail</dt>
                    <dd>
                        <a href="mailto:<?= Settings::instance()->get('email'); ?>"><?= Settings::instance()->get('email'); ?></a>
                    </dd>
                <?php endif; ?>
            </dl>
        </div>
        <div class="rt">
            <h1><span class="red"><?= Settings::instance()->get('company_title'); ?></h1>
            <ul class="horizontallist">
                <li><?= Settings::instance()->get('company_slogan'); ?></li>
            </ul>
        </div>
    </div>

    <div class="row_2">
        <div class="left"><?php menuhelper::add_menu_editable_heading('footer', "footer_menu"); ?></div>
        <div class="powered_by right"><?= (Settings::instance()->get('cms_copyright') == '') ? 'Powered by <a href="https://ideabubble.ie">Idea Bubble</a> <a href="https://ideabubble.ie/website-cms-solutions.html">CMS</a>' : Settings::instance()->get('cms_copyright'); ?></div>
    </div>
    <div class="row_3">
        <img id="footer_img1" src="<?= URL::get_skin_urlpath(TRUE) ?>images/footer-img01.jpg" alt="Payment"
             title="Payment"/>
        <img id="footer_img2" src="<?= URL::get_skin_urlpath(TRUE) ?>images/footer-img02.jpg" alt="Payment"
             title="Payment"/>
        <img id="footer_img3" src="<?= URL::get_skin_urlpath(TRUE) ?>images/footer-img03.jpg" alt="Payment"
             title="Payment"/>
        <img id="footer_img4" src="<?= URL::get_skin_urlpath(TRUE) ?>images/footer-img04.jpg" alt="Payment"
             title="Payment"/>

        <div class="left left-100">
            <img id="footer_img8" src="<?= URL::get_skin_urlpath(TRUE) ?>images/payment_methods.png" alt="Payment"
                 title="Payment"/>
        </div>
    </div>
</div>
<!-- /Footer -->
