<html>
<body>
<h1>Dear <?=$data['pl_f_name'].' '.$data['pl_s_name']?>,</h1>


<p>Your registration to: "<?=url::site()?> - Reward Club" online system has been successfully processed.</p>

<p>
    Please Note that you can login to your "Reward Club" account using the following link:
    <a href="<?=url::site()?>'login.html">Login to your "<?=url::site()?> - Rewards Club" account now</a><br />
    and your login details, provided below.
</p>

<p>Your Login Details</p>
<p>
    <strong>MemberId:</strong> <?=$data['registered_member_details']['memberid']?>
</p>

<p>
    <strong>Username:</strong> <?=$data['registered_member_details']['username']?><br />
    Please note: that this is the number of your "Reward Club" card
</p>

<p>
    <strong>Password:</strong> <?=$data['registered_member_details']['password']?><br />
    Please note: that this is a randomly generated password. If you require you can change this with a more suitable for you password,
    using the provided in our <a href="<?=url::site()?>members_area.html"></a> "<?=url::site()?> - Reward Club" online service
    system functionality.
</p>

<p>Welcome to our "Reward Club".</p>
<p>Kind regards,</p>
<p><?=url::site()?></p>

</body>
<h5>This email was sent <?=date('F j,  Y,  g:i a')?> from: <?=url::site()?> "Reward Club" online system</h5>
</html>