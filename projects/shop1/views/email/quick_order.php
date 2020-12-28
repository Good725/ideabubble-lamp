<?php $form_identifier = '';?>

Name: <?=              isset($form['name'])              ? $form['name']                     : '' ;?><br/>
Address: <?=           isset($form['address'])           ? nl2br($form['address'])           : '' ; ?><br/>
Phone: <?=             isset($form['telephone'])         ? $form['telephone']                : '' ?><br/>
Budget: <?=            isset($form['budget'])            ? $form['budget']                   : '' ;?><br/>
Email: <?=             isset($form['email'])             ? $form['email']                    : '' ;?><br/>
Delivery Time: <?=     isset($form['delivery_time'])     ? $form['delivery_time']            : '' ;?><br />
Event Type: <?=        isset($form['event_type'])        ? $form['event_type']               : '' ;?><br/>
Recipient Name: <?=    isset($form['recipient_name'])    ? $form['recipient_name']           : '' ;?><br/>
Recipient Address: <?= isset($form['recipient_address']) ? nl2br($form['recipient_address']) : '' ;?><br/>
Recipient Phone: <?=   isset($form['recipient_phone'])   ? $form['recipient_phone']          : '' ;?><br/>
Receipient Email: <?=  isset($form['recipient_email'])   ? $form['recipient_email']          : '' ;?><br/>
Comments: <?=          isset($form['comments'])          ? nl2br($form['comments'])          : ''; ?><br/>