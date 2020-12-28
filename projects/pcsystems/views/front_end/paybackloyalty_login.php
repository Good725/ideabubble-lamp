<?php
echo IbHelpers::get_messages();

$session = Session::instance();

if(isset($_SESSION['pl_user']['memberid']) AND !empty($_SESSION['pl_user']['memberid'])): ?>
    <form id="pl_member_login_form" action="/frontend/paybackloyalty/user_login" method="POST">
        <h2>Rewards Club - Members Login Area</h2>
        <h3>You are already loggedin</3>
    </form>
<?php else:?>
    <form id="pl_member_login_form" action="/frontend/paybackloyalty/user_login" method="POST">
        <h2>Rewards Club - Members Login Area</h2>
        <fieldset>
            <legend>Members Login Area</legend>
            <div>
                <div class="text_label">Account ID</div>
                <div class="text_input">
                    <input type="text" name="pl_username" id="pl_username" class="validate[required] text-input"><span class="req_symb">*</span>
                </div>
            </div>
            <div>
                <div class="text_label">Password</div>
                <div class="text_input">
                    <input type="password" name="pl_password" id="pl_password" class="validate[required] text-input"><span class="req_symb">*</span>
                </div>
            </div>
            <div>
                <div class="text_label"><a href="mailto:info@garretts.ie">Forgot Password</a></div>
            </div>
            <input type="hidden" name="referer" value="<?=@$_SERVER['HTTP_REFERER']?>">
            <button type="button" id="pl_login_frm_btn" onclick="if($('#pl_member_login_form').validationEngine('validate')){$('#pl_member_login_form').submit()}">Login »</button>
        </fieldset>
    </form>

    <script type="text/javascript" src="<?=URL::site()?>assets/default/js/jquery.validationEngine2.js"></script>
    <script type="text/javascript" src="<?=URL::site()?>assets/default/js/jquery.validationEngine2-en.js"></script>
<?php endif;?>


