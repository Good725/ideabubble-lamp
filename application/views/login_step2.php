<?php if (Request::current()->directory() != 'admin'): ?>
    <link rel="stylesheet" href="<?= URL::overload_asset('css/login-signup.css') ?>" />
<?php endif; ?>
<script src="https://www.google.com/recaptcha/api.js" async defer></script>
<?php
$mode = 'login';
?>

<div class="login-form-container login-form">
    <div class="modal show">
        <div class="modal-dialog">
            <div class="modal-content login">
                <div class="modal-header">
                    <img class="client-logo" src="<?= Ibhelpers::get_login_logo() ?>" alt="" />
                </div>

                <?= (isset($alert)) ? $alert : '' ?>

                <div class="tab-content">
                    <form class="form-horizontal validate-on-submit tab-pane<?= ($mode == 'login' ? ' active' : '')?>" id="login-tab-login" name="login_form" method="post" action="/admin/login/step2<?= $redirect ? '?redirect='.urlencode($redirect) : '' ?>">
                        <input type="hidden" name="user_id" id="user_id" value="<?=$user_id?>" />
                        <input type="hidden" name="redirect" value="<?= html::chars($redirect) ?>" />

                        <div class="modal-body">
                            <fieldset>
                                <div class="form-group">
                                    <h3 class="col-sm-12"><?=__('Two-Factor Authentication')?></h3>
                                    <div class="col-sm-12" style="text-align: center">
                                        <?php for ($i = 0 ; $i < 6 ; ++$i) { ?>
                                            <?php
                                            $attributes = array(
                                                'autofocus' => 'autofocus',
                                                'class'     => 'validate[required]',
                                                'id'        => 'login-email',
                                                'maxlength' => 2,
                                                'style' => 'max-width:15%'
                                            );
                                            ?>
                                            <input type="text" name="digit[<?=$i?>]" maxlength="1" inputmode="numeric" pattern="[0-9]*" tabindex="<?=$i?>" onkeypress='is_digit_only(event)'
                                                   class="code-digit validate[required] form-control"
                                                   style="max-width: 40px; width: 40px; display: inline-block;  margin: 0px 2px; clear: none; float: none;" />
                                        <?php } ?>
                                        <input type="hidden" name="code" value="" />
                                        <div id="auth_code_status_text">
                                        <?php if(!$wrong_code):?>
                                            <p>A message with a verification code has been sent to
                                                <?=str_repeat('*', strlen($user['country_dial_code_mobile'].$user['dial_code'].$user['mobile']) - 2) . substr($user['mobile'], -2)?>.
                                                Enter the code to continue.</p>
                                        <?php endif?>
                                        </div>

                                    </div>

                                </div>

                                <div class="form-group login-buttons">
                                    <div class="col-sm-12">
                                        <input type="submit" class="button btn btn-lg btn--full btn-primary" id="login_button" name="login" value="<?=__('Log in')?>" />
                                    </div>
                                </div>
                            </fieldset>
                            <div class="form-group clearfix">
                                <div class="col-sm-12 layout-login-alternative_option">
                                    <div class="col-sm-6">
                                        <a href="#" class="resend" id="resend_verification_code">Re-send verification code</a>
                                    </div>
                                    <div class="col-sm-6">
                                        <a href="/contact-us" class="contact" id="contact_us">Did not receive the code?</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
                <script>
                    // Add an alert to a message area.
                    // e.g. $('#selector').add_alert('Save successful', 'success');
                    (function($)
                    {
                        $.fn.add_alert = function(message, type)
                        {
                            var $alert = $(
                                '<div class="alert'+((type) ? ' alert-'+type : '')+' popup_box">' +
                                '<a href="#" class="close" data-dismiss="alert">&times;</a> ' + message +
                                '</div>');
                            $(this).append($alert);
                        };
                    })(jQuery);
                    $(document).on("ready", function(){
                        $(".code-digit").on("change", function(){
                            var code = "";
                            $(".code-digit").each(function(){
                                code += this.value;
                            });
                            $("input[name=code]").val(code);
                        });
                        $(".code-digit").keyup(function () {
                            if (this.value.length == this.maxLength) {
                                $(this).next('.code-digit').focus();
                            }
                        });
                        $('#resend_verification_code').click(function(e){
                            var user_id = $('#user_id').val();
                            $.ajax(
                                {
                                    url: '/admin/login/ajax_resend_auth_code',
                                    ContentType: 'application/json',
                                    data : {
                                        user_id : user_id
                                    },
                                    type: "POST",
                                    success: function (response) {
                                        if (response.length == 0) {
                                             $('body').add_alert('Error. Unable to to re-send the authentication code', 'danger popup_box');
                                        } else {
                                            $('body').add_alert('Authentication code was re-sent', 'success popup_box');
                                            $('#auth_code_status_text').html('');
                                            $('#auth_code_status_text').html(response.text);
                                        }
                                    }
                                });
                        })
                    });
                    function is_digit_only(evt) {
                        var theEvent = evt || window.event;

                        // Handle paste
                        if (theEvent.type === 'paste') {
                            key = event.clipboardData.getData('text/plain');
                        } else {
                            // Handle key press
                            var key = theEvent.keyCode || theEvent.which;
                            key = String.fromCharCode(key);
                        }
                        var regex = /[0-9]|\./;
                        if( !regex.test(key) ) {
                            theEvent.returnValue = false;
                            if(theEvent.preventDefault) theEvent.preventDefault();
                        }
                    }
                </script>
                <div class="modal-footer">
                    <?php if (false): // future of this section is uncertain ?>
                        <div class="poweredby">
                            <p><?= Settings::instance()->get('cms_copyright') ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
