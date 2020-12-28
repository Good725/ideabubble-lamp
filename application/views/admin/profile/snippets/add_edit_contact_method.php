<?php
isset($notification) ? extract($notification) : NULL;
isset($use_family_notifications) ? NULL : $use_family_notifications = FALSE;
switch($type_stub)
{
    case 'email':    $icon = 'envelope'; break;
    case 'mobile':   $icon = 'mobile';   break;
    case 'landline': $icon = 'phone';    break;
    case 'web':      $icon = 'globe';    break;
    case 'skype':    $icon = 'skype';    break;
    case 'facebook': $icon = 'facebook'; break;
    case 'twitter':  $icon = 'twitter';  break;
    default:         $icon = '';
}

$attributes = array('class' => 'contactdetail_value');
if ($type_stub == 'email') {
    $attributes['class'] .= ' validate[custom[email]]';
}
if ($use_family_notifications) {
    $attributes['readonly'] = 'readonly';
}
$args = array(
    'icon'            => '<span class="icon-'.$icon.'"></span>',
    'icon_attributes' => array('data-stub' => $type_stub, 'title' => $type_text),
    'right_icon'      => '<button type="button" class="remove-contactdetail-button"'.($use_family_notifications ? ' readonly="readonly"' : '').'><span class="icon-remove"></span></button>'
);
?>

<div class="form-group no-gutters contactdetail_wrapper" data-stub="<?= $type_stub ?>">
    <input name="contactdetail_id[]" type="hidden" value="<?= isset($notification['id']) ? $notification['id'] : 'new' ?>">
    <input name="contactdetail_type_id[]" type="hidden" value="<?= isset($notification['type_id']) ? $notification['type_id'] : '';?>">
    <?= Form::ib_input(null, 'contactdetail_value[]', $value, $attributes, $args); ?>
</div>

<?php
unset($type_stub);
unset($type_text);
unset($value);
unset($type_id);
?>