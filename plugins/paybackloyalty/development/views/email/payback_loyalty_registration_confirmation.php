<html>
<body>
<h1>Dear <?=$post_data['pl_f_name'].' '.$post_data['pl_s_name']?>,</h1>

<p>Your registration to: "<?=url::site();?> - Rewards Club" online system has been successfully processed.</p>

<p>
    Please Note that you can login to your "Rewards Club" account using the following link:
    <a href="<?=url::site()?>login.html">Login</a><br />
    and your login details, provided below.
</p>

<p>Your Login Details</p>
<p>
    <strong>MemberId:</strong> <?=$request_result['memberid']?>
</p>

<p>
    <strong>Username:</strong> <?=$request_result['username']?><br />
    Please note: that this is the number of your "Reward Club" card
</p>

<p>
    <strong>Password:</strong> <?=$request_result['password']?><br />
    Please note: that this is a randomly generated password. If you require you can change this with a more suitable for you password,
    using the provided in our <a href="<?=url::site()?>members-area.html" >member area</a>
    system functionality.
</p>

<p>Welcome to our "Reward Club".</p>
<p>Kind regards,</p>
<p><?=url::site()?></p>

</body>
<h5>This email was sent <?=date('F j,  Y,  g:i a')?> from: <?=url::site()?> "Rewards Club" online system</h5>
</html>