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
                    <form class="form-horizontal validate-on-submit tab-pane<?= ($mode == 'login' ? ' active' : '')?>" id="login-tab-login" name="login_form" method="post" action="/admin/login/set_phone<?= $redirect ? '?redirect='.urlencode($redirect) : '' ?>">
                        <input type="hidden" name="user_id" value="<?=$user_id?>" />
                        <input type="hidden" name="redirect" value="<?= $redirect ?>" />

                        <div class="modal-body">
                            <fieldset>
                                <div class="form-group">
                                    <h3 class="col-sm-12"><?=__('Two-Factor Authentication')?></h3>
                                    <p>Your account has two-factor authentication active. Please enter your mobile number to receive your authentication code. </p>
                                    <br/>
                                    <div class="col-sm-12" style="text-align: center">

                                        <?php
                                        $country_attributes = array(
                        'class'    => 'mobile-international_code',
                        'readonly' => false,
                         'disabled' => false,
                         'id'       => 'mobile-international_code');
                                        $country_code_selected = !empty($user['country_dial_code_mobile']) ? $user['country_dial_code_mobile'] : '353';
                                        $options = Model_Country::get_dial_code_as_options($country_code_selected);
                                        $country_code = Model_Country::get_country_code_by_country_dial_code($country_code_selected);
                                        $mobile_codes_array = Model_Country::get_phone_codes_country_code($country_code);
                                        $mobile_codes = array('' => '');
                                        foreach($mobile_codes_array as $mobile_code) {
                                            $mobile_codes[$mobile_code['dial_code']] = $mobile_code['dial_code'];
                                        }
                                        $code_attributes = array(
                                            'class'    => 'mobile-code',
                                            'readonly' => false,
                                            'disabled' => false,
                                            'id'       => 'dial_code_mobile',
                                        );
                                        $code_selected = isset($user['dial_code_mobile']) ? $user['dial_code_mobile'] : null;
                                        ?>
                                        <div class="col-sm-4" style="padding-left: 0; padding-right: 2px;">
                                            <?= Form::ib_select(__('Country'), 'country_dial_code_mobile', $options, $country_code_selected,  $country_attributes)?>
                                        </div>
                                        <div class="col-sm-4 dial_code" style="padding-left: 0; padding-right: 2px;">
                                            <?= !empty($mobile_codes_array) ?
                                                Form::ib_select(__('Code'), 'dial_code_mobile', $mobile_codes, $code_selected, $code_attributes) :
                                                Form::ib_input(__('Code'), 'dial_code_mobile', $code_selected, array('id' => 'dial_code_mobile', 'maxlength' => 5))?>
                                        </div>
                                        <div class="col-sm-4" style="padding-left: 0; padding-right: 2px;">
                                            <?= Form::ib_input(__('Mobile'), 'mobile', $user['mobile'], array('id' => 'edit_profile_phone', 'maxlength' => 10)); ?>
                                        </div>
                                    </div>
                                        <script>
                                            $(document).on('change', '#mobile-international_code', function(){
                                                var country_code = $(this).val();
                                                if (country_code) {
                                                    $.ajax({
                                                        url:'/admin/login/ajax_get_dial_codes',
                                                        data:{
                                                            country_code : country_code
                                                        },
                                                        type: 'POST',
                                                        dataType:'json'
                                                    }).done(function(data){
                                                        if (data.length == 0) {
                                                            $('#dial_code_mobile').closest('.form-select').remove();
                                                            $('#dial_code_mobile').closest('.form-input').remove();
                                                            var input =
                                                                '   <label class="form-input form-input--text form-input--pseudo form-input--active">' +
                                                                '        <span class="form-input--pseudo-label label--mandatory">Code</span>' +
                                                                '        <input type="text" id="dial_code_mobile" name="dial_code_mobile" value="" class="mobile-code validate[required]" placeholder="Code: *">' +
                                                                '    </label>';
                                                            $('.col-sm-4.dial_code').append(input);
                                                        } else {
                                                            if (!$('#dial_code_mobile').is("select")) {
                                                                $('#dial_code_mobile').closest('.form-select').remove();
                                                                $('#dial_code_mobile').closest('.form-input').remove();                    var select = '<label class="form-select">' +
                                                                    '        <span class="form-input form-input--select form-input--pseudo form-input--active">\n' +
                                                                    '            <span class="form-input--pseudo-label">Code</span>' +
                                                                    '               <select id="dial_code_mobile" name="dial_code_mobile" class="mobile-code" readonly="">\n' +
                                                                    '</select>       ' +
                                                                    '        </span>' +
                                                                    '    </label>';
                                                                $('.col-sm-4.dial_code').append(select);
                                                            }
                                                            $('#dial_code_mobile').find('option').remove();
                                                            $('#dial_code_mobile').append('<option value=""></option>');
                                                            $.each(data, function(key, code){
                                                                var option = '<option value="' + code.dial_code+'">'+code.dial_code+'</option>';
                                                                $('#dial_code_mobile').append(option);
                                                            });
                                                        }
                                                    });
                                                }
                                            });
                                        </script>
                                    </div>
                                <div class="form-group login-buttons">
                                    <div class="col-sm-12">
                                        <input type="submit" class="button btn btn-lg btn--full btn-primary" id="login_button" name="login" value="<?=__('Send Code')?>" />
                                    </div>
                                </div>
                            </fieldset>
                        </div>
                    </form>
                </div>

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
