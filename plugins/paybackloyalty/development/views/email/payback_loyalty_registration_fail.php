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

<p>There was a Problem with the Registration of a new Member to "Payback Loyalty" system of the "<?=url::site();?> - Rewards Club" website.</p>
<p>Please find Registration Details and the arisen problem during this Registration bellow:</p>

<h2>Payback Loyalty System - Error Message:</h2>
<p><?=$request_err_msg?></p>

<h3>Registration Form - Customer details</h3>
<p>
	Name: <?=$post_data['pl_f_name'].' '.$post_data['pl_s_name']?><br />
	Phone: <?=$post_data['pl_mobile']?><br />
	Email: <?=$post_data['pl_email']?><br />
<? if(isset($post_data['pl_card_number']) && $post_data['pl_card_number'] != 0){?>
	"Payback Loyalty" card: <?=$post_data['pl_card_number']?><br />
<? }else if(isset($request_result['cardno'])){?>
	"Payback Loyalty" card: <?=$request_result['cardno']?><br />
<?}else{?>
	"Payback Loyalty" card: NOT Present<br />
<? }?>
<? if(isset($request_result) && !is_null($request_result)){?>
	Payback Loyalty MemberID (Account ID): <?=(!empty($request_result['memberid']))? $request_result['memberid'] : 'NOT Present'?><br />
	Payback Loyalty Username: <?=(!empty($request_result['username']))? $request_result['username'] : 'NOT Present'?><br />
	Payback Loyalty Temporary Password: <?=(!empty($request_result['password']))? $request_result['password'] : 'NOT Present'?><br />
<?}?>
	Language: <?=$languages[$post_data['pl_language_id']].' (PL value: '.$post_data['pl_language_id'].')' ?><br />
	Gender: <?=($post_data['pl_gender_id'] == 1)? 'Male' : 'Female' ?><br />
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
	Contact for info (by email): <?=(isset($post_data['pl_contact_for_info']) AND $post_data['pl_contact_for_info'] == 1)? 'No' : 'Yes'?><br />
	Contact by SMS: <?=(isset($post_data['pl_contact_by_sms']) AND $post_data['pl_contact_by_sms'] == 1)? 'No' : 'Yes'?><br />
	Contact for Research: <?=(isset($post_data['pl_contact_for_research']) AND $post_data['pl_contact_for_research'] == 1)? 'Yes' : 'No'?><br />
	Contact by Partners: <?=(isset($post_data['pl_contact_by_partners']) AND $post_data['pl_contact_by_partners'] == 1)? 'Yes' : 'No'?><br />
</p>

</body>
<h5>This email was sent <?=date('F j,  Y,  g:i a')?> from: <?=url::site()?> "Rewards Club" online system</h5>
</html>