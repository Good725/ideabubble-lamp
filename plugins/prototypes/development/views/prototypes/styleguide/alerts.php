<h3 class="numbered-header">Notifications</h3>
<?php
foreach ($contexts as $key => $label) {
    if (!in_array($key, ['primary', 'default'])) {
        echo IbHelpers::alert('<strong>'.$label.'</strong> This is a message.', $key.' popup_box alert-stay', false);
    }
}
?>

<h3 class="numbered-header">Validation errors</h3>
<div class="formError" style="position: static; opacity: 0.87; width: 9em;">
    <div class="formErrorContent">* Validation error</div>
    <div class="formErrorArrow">
        <div class="line10"><!-- --></div><div class="line9"><!-- --></div><div class="line8"><!-- --></div><div class="line7"><!-- --></div><div class="line6"><!-- --></div><div class="line5"><!-- --></div><div class="line4"><!-- --></div><div class="line3"><!-- --></div><div class="line2"><!-- --></div><div class="line1"><!-- --></div>
    </div>
</div>

<h3 class="numbered-header">Cookie consent</h3>

<div class="cc_banner cc_container cc_container--open" style="position: static;">
    <a href="#null" data-cc-event="click:dismiss" target="_blank" class="cc_btn cc_btn_accept_all">Accept</a>
    <p class="cc_message">This website uses cookies. <a data-cc-if="options.link" target="_self" class="cc_more_info" href="styles">See our cookie notice.</a></p>
    <a class="cc_logo" target="_blank" href="http://silktide.com/cookieconsent">Cookie Consent plugin for the EU cookie law</a>
</div>

<h3 class="numbered-header">Browser sniffer</h3>

<iframe src="/admin/prototypes/cms/empty#test-bu" height="120" width="100%" class="border-0"></iframe>