<?php
$action = '/frontend/messaging/receive_callback?driver=sms-bongo';
?>
<form action="<?=$action?>" method="post" target="test">
    <legend>test <?=$action?></legend>
    <label>org</label><input type="text" name="org" /><br />
    <label>dest</label><input type="text" name="dest" /><br />
    <label>message</label><textarea name="message"></textarea><br />
    <button type="submit">test</button>
</form>

<iframe id="test" name="test"></iframe>
