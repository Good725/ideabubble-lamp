<?php
if ($ticket == null) {
?>
    <p>No ticket found!</p>
<?php
} else {
    //echo '<pre>' . print_r($ticket, true) . '</pre>';
?>
<div>
    <table cellspacing="5">
        <tbody>
            <tr>
                <td><img src="/qrcode.png?url=<?=urlencode($url)?>&size=3" /></td>
                <td valign="top">
                    Buyer:<?=$ticket['buyer']?><br />
                    Event:<?=$ticket['event']?><br />
                    Date:<?=$ticket['starts']?><br />
                    Ticket:<?=$ticket['ticket'] . '(' . $ticket['type'] . ')'?><br />
                    <?php if ($allowToEnterStatus) { ?>
                        <form method="post">
                            <label>Checked:</label><input type="checkbox" name="checked" value="1" <?=$ticket['checked'] ? 'checked="checked"' : ''?> />
                            <label>Note:</label><input type="text" name="checked_note" value="<?=html::chars($ticket['checked_note'])?>" />
                            <?php if ($ticket['checked_by']) { ?>
                                <label>Checked By:</label><input type="text" readonly="readonly" value="<?=$ticket['checker']?>" />
                            <?php } else { ?>
                                <button type="submit" name="action" value="update">Update</button>
                            <?php } ?>
                        </form>
                    <?php } ?>
                </td>
            </tr>
        </tbody>
    </table>
</div>
<?php

}
?>
