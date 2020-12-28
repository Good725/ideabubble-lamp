<?php
$mm          = 1;
$inch        = $mm * 25.4;
$pt          = $inch / 72;

$paddingTop  = 46   * $mm;
$paddingLeft = 20.5 * $mm;
$cutLineW    = 12.5 * $mm;
$cutLineH    = 14.5 * $mm;
$cutLineT    =  0.1 * $mm; // thickness
$card_width  =  3.5 * $inch;
$card_height =  2   * $inch;
$gap         =  3   * $mm;
?>
<style>
	*{margin:0;padding:0;}
	body{
		font-family:helveticaneuelight,HelveticaNeue-Light, 'Helvetica Neue', Helvetica, chelvetica, Arial, sans-serif;
	}
	.card{
		float: left;
		color: #000;
		font-family:helveticaneuelight,HelveticaNeue-Light, 'Helvetica Neue', Helvetica, chelvetica, Arial, sans-serif;
		line-height: <?= 10.25 * $pt ?>mm;
		width:<?= 3.5 * $inch - 8 * $mm ?>mm;
		height:<?= 2 * $inch - 8 * $mm ?>mm;
		padding: <?= 4 * $mm ?>mm;
	}

	.card_line {
		font-weight: 300;
		font-size: <?= 8.25 * $pt ?>mm;
	}

	.card_contact_name {
		font-family:helveticaneuemedium, 'Helvetica Neue', Helvetica, chelvetica, Arial, sans-serif;
		font-weight: 500;
		font-size: <?= 9 * $pt ?>mm;
		color: #5268c2;
	}

	.card_post_nominal_letters {
		font-size: <?= 6.5 * $pt ?>mm;
		font-style: italic;
	}

	.card_line_label {
		float: left;
		width: <?= 12 * $mm ?>mm;
	}

	.card_line_value {
		float: left;
	}

	.card_logo img {
		width: <?= 33 * $mm?>mm;
	}

	.card_address{
		position: absolute;
		width: 40mm;
	}

	.card_address_col_0{left: <?= $paddingLeft + 56 ?>mm;}
	.card_address_col_1{left: <?= $paddingLeft + 56 + $card_width ?>mm;	}

	<?php for ($r = 0; $r < 4; $r++): ?>
	.card_address_row_<?= $r ?> {top: <?= $paddingTop + ($card_height * $r) + (4 * $mm) ?>mm;}
	<?php endfor; ?>

	.card_contact_details {
		margin-top: <?= 11 * $mm ?>mm;
	}
	.card_contact_details .card_line{
		clear:left;
	}
	.cards{width: 210mm;}
	.cards,#page{width: 210mm;}
	.cut_line{position:absolute;height:<?= $cutLineT ?>mm;width:<?= $cutLineT ?>mm;}

	.cl-vertical{border-left:<?= $cutLineT ?>mm solid #000;height:<?= $cutLineH ?>mm;}
	.cl-horizontal{border-bottom:<?= $cutLineT ?>mm solid #000;width:<?= $cutLineW ?>mm}

	.vtop-1{top:<?= $paddingTop - $gap - $cutLineH ?>mm;}
	.vtop-2{top:<?= $paddingTop + $card_height * 4 + $gap ?>mm;}
	.vleft-1{left:<?= $paddingLeft                   ?>mm;}
	.vleft-2{left:<?= $paddingLeft + $card_width     ?>mm;}
	.vleft-3{left:<?= $paddingLeft + $card_width * 2 ?>mm;}

	.hleft-1{left:<?= $paddingLeft - $gap - $cutLineW ?>mm;}
	.hleft-2{left:<?= $paddingLeft + $card_width * 2 + $gap ?>mm;}
	.htop-1{top:<?= $paddingTop                    ?>mm;}
	.htop-2{top:<?= $paddingTop + $card_height     ?>mm;}
	.htop-3{top:<?= $paddingTop + $card_height * 2 ?>mm;}
	.htop-4{top:<?= $paddingTop + $card_height * 3 ?>mm;}
	.htop-5{top:<?= $paddingTop + $card_height * 4 ?>mm;}


</style>
<?php
switch (count($cards))
{
	case 1:  $repeat = 8; break; // display same image eight times
	case 2:  $repeat = 4; break; // display both images four times
	case 3:  $repeat = 2; break; // display all three images twice
	case 4:  $repeat = 2; break; // display all four images twice
	default: $repeat = 0; break; // shouldn't be any other amount
}
?>
<div id="page" style="padding-left:<?= $paddingLeft ?>mm;padding-top:<?= $paddingTop ?>mm;position:relative;">
	<div class="cards">
		<?php $i = 0; ?>
		<?php foreach ($cards as $card): ?>
			<?php for ($j = 0; $j < $repeat; $j++, $i++): ?>
				<div class="card row_<?= (int) ($i / 2) ?> col_<?= ($i % 2) ?>" id="card_<?= $i ?>">
					<div class="card_logo">
						<img src="/assets/14/images/regeneron_ireland-logo.svg" />
					</div>

					<?php
					$lines = array();
					$lines[] = 'www.regeneron.com';
					if (trim($card['email'])                != '') $lines[] = '<div class="card_line_label">Email:</div> <div class="card_line_value">'.$card['email'].'</div>';
					if (trim($card['fax'])                  != '') $lines[] = '<div class="card_line_label">Fax:</div>   <div class="card_line_value">'.$card['fax']  .'</div>';
					if (trim($card['telephone'])            != '') $lines[] = '<div class="card_line_label">Phone:</div> <div class="card_line_value">'.$card['telephone'].'</div>';
					if (trim($card['department'])           != '') $lines[] = $card['department'];
					if (trim($card['title'])                != '') $lines[] = $card['title'];
					if (trim($card['post_nominal_letters']) != '') $lines[] = '<div class="card_post_nominal_letters">'.$card['post_nominal_letters'].'</div>';
					if (trim($card['employee_name'])        != '') $lines[] = '<div class="card_contact_name">'.$card['employee_name'].'</div>';
					?>

					<div class="card_contact_details">
						<?php for ($line = 6; $line >= 0; $line--): ?>
							<div class="card_line"><?= isset($lines[$line]) ? $lines[$line] : '&nbsp;' ?></div>
						<?php endfor; ?>
					</div>
				</div>
			<?php endfor; ?>
		<?php endforeach; ?>
	</div>
</div>

<?php $i = 0; ?>
<?php foreach ($cards as $card): ?>
	<?php for ($j = 0; $j < $repeat; $j++, $i++): ?>
		<div class="card_address card_address_row_<?= (int) ($i / 2) ?> card_address_col_<?= ($i % 2) ?>">
			<?php if ($card['office_id'] == 1): ?>
				<div class="card_line">Regeneron Ireland</div>
				<div class="card_line">Raheen Business Park</div>
				<div class="card_line">Limerick</div>
				<div class="card_line">Ireland</div>
			<?php elseif ($card['office_id'] == 2): ?>
				<div class="card_line">Regeneron Ireland</div>
				<div class="card_line">Europa House</div>
				<div class="card_line">Block 9 Harcourt Centre</div>
				<div class="card_line">Harcourt Street</div>
				<div class="card_line">Dublin 2</div>
				<div class="card_line">Ireland</div>
			<?php endif; ?>
		</div>
	<?php endfor; ?>
<?php endforeach; ?>
<div class="cut_line cl-vertical   vtop-1 vleft-1"></div>
<div class="cut_line cl-vertical   vtop-1 vleft-2"></div>
<div class="cut_line cl-vertical   vtop-1 vleft-3"></div>
<div class="cut_line cl-vertical   vtop-2 vleft-1"></div>
<div class="cut_line cl-vertical   vtop-2 vleft-2"></div>
<div class="cut_line cl-vertical   vtop-2 vleft-3"></div>

<div class="cut_line cl-horizontal htop-1 hleft-1"></div>
<div class="cut_line cl-horizontal htop-1 hleft-2"></div>
<div class="cut_line cl-horizontal htop-2 hleft-1"></div>
<div class="cut_line cl-horizontal htop-2 hleft-2"></div>
<div class="cut_line cl-horizontal htop-3 hleft-1"></div>
<div class="cut_line cl-horizontal htop-3 hleft-2"></div>
<div class="cut_line cl-horizontal htop-4 hleft-1"></div>
<div class="cut_line cl-horizontal htop-4 hleft-2"></div>
<div class="cut_line cl-horizontal htop-5 hleft-1"></div>
<div class="cut_line cl-horizontal htop-5 hleft-2"></div>



