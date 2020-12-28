<?php
isset($notification) ? extract($notification) : NULL;
switch ($type_stub) {
    case 'email':    $icon = 'envelope'; break;
    case 'mobile':   $icon = 'mobile';   break;
    case 'landline': $icon = 'phone';    break;
    case 'web':      $icon = 'globe';    break;
    case 'skype':    $icon = 'skype';    break;
    case 'facebook': $icon = 'facebook'; break;
    case 'twitter':  $icon = 'twitter';  break;
    default:         $icon = '';
}
?>
<div class="contactdetail_wrapper mb-3<?=$type_stub == 'mobile' || $type_stub == 'landline' ? ' phone_contact_data' : ''?>" data-type="<?= $type_stub ?>" >
    <input name="contactdetail_id[<?=$notification['id']?>]" type="hidden" value="<?= isset($notification['id']) ? $notification['id'] : 'new' ?>">
    <input name="contactdetail_type_id[<?=$notification['id']?>]" type="hidden" value="<?= isset($notification['type_id']) ? $notification['type_id'] : '';?>">

    <?php
    $attributes = [
        'class' => 'contactdetail_value'.(($type_stub == 'email') ? ' validate[custom[email]]' : ''),
        'id'=> $type_stub == 'email' ? 'contact_email': ''];
    $args = [
        'icon' => '<span class="icon-'. $icon .'" title="'.$type_stub.'""></span>',
        'right_icon' => '<button class="btn btn-link remove-contactdetail-button" type="button"><span class="icon-remove"></span></button>'
    ];
    if ($type_stub == 'mobile') {
                     $country_attributes = array(
                        'class'    => 'mobile-international_code validate[required]',
                        'readonly' => false,
                         'disabled' => false,
                         'id'       => 'mobile-international_code',
                         'style'=> 'height: 3em!important; padding-left: 1em; padding-right: 0;');
                     $country_code_selected = !empty($notification['country_dial_code']) ? $notification['country_dial_code'] : '353';
                     $options = Model_Country::get_dial_code_as_options($country_code_selected, null, true);
                     $country_code = Model_Country::get_country_code_by_country_dial_code($country_code_selected);
                     $mobile_codes_array = Model_Country::get_phone_codes_country_code($country_code);
                     $mobile_codes = array('' => '');
                     foreach($mobile_codes_array as $mobile_code) {
                         $mobile_codes[$mobile_code['dial_code']] = $mobile_code['dial_code'];
                     }
                    $code_attributes = array(
                        'class'    => 'mobile-code validate[required]',
                        'readonly' => false,
                        'disabled' => false,
                        'id'       => 'dial_code_mobile',
                        'style'=> 'height: 3em!important; padding-left: 1em; padding-right: 0;'
                );
                $code_selected = isset($notification['dial_code']) ? $notification['dial_code'] : null;
                     ?>
        <input type="hidden" id="notification_type_<?=$notification['id']?>" class="notification_type" name="contactdetail_value[<?=$notification['id']?>][notification_id]" value="<?=$notification['notification_id']?>">
        <div class="col-sm-3" style="padding-left: 0; padding-right:0; margin-bottom: 3px; font-size: 12px; height: 3em!important;">
        <?= Form::ib_select(__('Country'), 'contactdetail_value['.$notification['id'].'][country_dial_code_mobile]', $options, $country_code_selected,  $country_attributes)?>
    </div>
        <div class="col-sm-3 dial_code" style="padding-left: 0; padding-right:0; margin-bottom: 3px; font-size: 12px; height: 3em!important;">
            <?= !empty($mobile_codes_array) ?
                Form::ib_select(__('Code'), 'contactdetail_value['.$notification['id'].'][dial_code_mobile]', $mobile_codes, $code_selected, $code_attributes,  array('group_class' => 'area_code')) :
                Form::ib_input(__('Code'), 'contactdetail_value['.$notification['id'].'][dial_code_mobile]', $code_selected, $code_attributes, array('group_class' => 'area_code'))?>
        </div>
        <div class="col-sm-4" style="padding-left: 0; padding-right:0; margin-bottom: 3px; font-size: 12px; height: 3em!important;">
            <?= Form::ib_input(__('Mobile'), 'contactdetail_value['.$notification['id'].'][mobile]', $notification['value'], array('id' => 'edit_profile_phone', 'class' => 'validate[required]', 'style'=> 'height: 3em!important; padding-left: 1em; padding-right: 0;')); ?>
        </div>
        <div class="col-sm-1" style="padding-left: 0; margin-bottom: 10px; margin-top: 0;">
            <button class="btn btn-link remove-contactdetail-button" type="button" style="height: 2.9em; width: 100%; padding-right: 25px;padding-left: 15px;background-color: white;border-radius: 0;border-color: #ccc; color: var(--primary);">
                <span class="icon-remove"></span>
            </button>
        </div>
    <?php } elseif($type_stub == 'landline') {

        $country_attributes = array(
            'class'    => 'landline-international_code validate[required]',
            'readonly' => false,
            'disabled' => false,
            'id'       => 'landline-international_code',
            'style'    => 'height: 3em!important; padding-left: 1em; padding-right: 0;');
        $country_code_selected = !empty($notification['country_dial_code']) ? $notification['country_dial_code'] : '353';
        $options = Model_Country::get_dial_code_as_options($country_code_selected, null, true);
        $country_code = Model_Country::get_country_code_by_country_dial_code($country_code_selected);
        $codes_array = Model_Country::get_phone_codes_country_code($country_code, 2, 'landline');
        $landline_codes = array('' => '');
        foreach($codes_array as $landline_code) {
            $landline_codes[$landline_code['dial_code']] = $landline_code['dial_code'];
        }
        $code_attributes = array(
            'class'    => 'landline-code validate[required]',
            'readonly' => false,
            'disabled' => false,
            'id'       => 'dial_code_landline',
            'style'    => 'height: 3em!important;'
        );
        $code_selected = isset($notification['dial_code']) ? $notification['dial_code'] : null;
        ?>
        <input type="hidden" id="notification_type_<?=$notification['id']?>" class="notification_type" name="contactdetail_value[<?=$notification['id']?>][notification_id]" value="<?=$notification['notification_id']?>">
        <div class="col-sm-3" style="padding-left: 0; padding-right:0; margin-bottom: 3px; font-size: 12px; height: 3em!important;">
            <?= Form::ib_select(__('Country'), 'contactdetail_value['.$notification['id'].'][country_dial_code_landline]', $options, $country_code_selected,  $country_attributes)?>
        </div>
        <div class="col-sm-3 dial_code" style="padding-left: 0; padding-right:0; margin-bottom: 3px; font-size: 12px; height: 3em!important;">
            <?= Form::ib_input(__('Code'), 'contactdetail_value['.$notification['id'].'][dial_code_landline]', $code_selected, array('id' => 'dial_code_mobile', 'class' => 'validate[required]', 'style'=> 'height: 3em!important;'))?>
        </div>
        <div class="col-sm-4" style="padding-left: 0; padding-right:0; margin-bottom: 3px; font-size: 12px; height: 3em!important;">
            <?= Form::ib_input(__('Phone'), 'contactdetail_value['.$notification['id'].'][phone]', $notification['value'], array('id' => 'edit_profile_phone', 'class' => 'validate[required]', 'style'=> 'height: 3em!important;')); ?>
        </div>
        <div class="col-sm-1" style="padding-left: 0; margin-bottom: 10px; margin-top: 0;">
            <button class="btn btn-link remove-contactdetail-button"
                    type="button" style="height: 2.9em; width: 100%; padding-right: 25px;padding-left: 15px;background-color: white;border-radius: 0;border-color: #ccc; color: var(--primary);">
                    <span class="icon-remove"></span>
            </button>
        </div>
    <?php } else {
        echo '<input type="hidden" id="notification_type'.$notification['id'].'" name="contactdetail_value[' . $notification['id'] . '][notification_id]" value="'.$notification['notification_id'].'">';
        echo Form::ib_input(null, 'contactdetail_value['.$notification['id'].'][value]', $notification['value'], $attributes, $args);
    }
    ?>
</div>

<?php
unset($type_stub);
unset($type_text);
unset($value);
unset($type_id);
?>