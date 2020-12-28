<html>
<body>
<?
$languages = array(
    '43' => 'English'
);
$titles = array(
    '0' => 'n/a',
    '1' => 'Mr',
    '2' => 'Mrs',
    '3' => 'Miss',
    '4' => 'Ms',
    '5' => 'Dr',
    '6' => 'Fr',
    '7' => 'Prof',
);
$countries = array(
    '105' => 'Ireland'
);
?>

<p>A new member has been registered to the "Payback Loyalty" system on <?=url::site();?></p>

<p>Customer details</p><?=$post_data['pl_f_name']?>


Title: <?=$titles[$post_data['pl_title_id']].' (PL value: '.$post_data['pl_title_id'].')'?><br />
First Name: <?=$post_data['pl_f_name']?><br />
Last Name: <?=$post_data['pl_s_name']?><br />
Date of Birth: <?=$post_data['pl_dob']?><br />
Address line 1: <?=$post_data['pl_addr1']?><br />
Address line 2: <?=$post_data['pl_addr2']?><br />
Address line 3: <?=$post_data['pl_addr3']?><br />
Address line 4: <?=$post_data['pl_addr4']?><br />
Country: <?=$countries[$post_data['pl_country_id']].' (PL value: '.$post_data['pl_country_id'].')'?><br />
Email: <?=$post_data['pl_email']?><br />
Phone: <?=$post_data['pl_mobile']?><br />

</body>
<h5>This email was sent <?=date('F j,  Y,  g:i a')?> from: <?=url::site()?> "Rewards Club" online system</h5>
</html>