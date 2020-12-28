<header id="header">
    <div class="content">
        <h1 class="logo"><a href="<?=URL::site();?>" title="Logo">KILMARTIN Educational Services</a></h1>
		<div class="right">
			<a href="/call-me-back.html" class="header-callback">Call Me Back</a>
			<address>
				<strong>LIMERICK</strong> <?=Settings::instance()->get('addres_line_1');?><br>
				<strong>ENNIS</strong> <?=Settings::instance()->get('addres_line_2');?><br>
				<span>TEL:</span> <?=Settings::instance()->get('telephone');?><span> EMAIL:</span> <a href="mailto:<?=Settings::instance()->get('email');?>"><?=Settings::instance()->get('email');?></a>
			</address>
			<?php $localisation_content_active = Settings::instance()->get('localisation_content_active') == '1'; ?>
			<?php if ($localisation_content_active): ?>
				<?php
				$localisation_languages = Model_Localisation::languages_list();
				$generic_uri = substr(Request::$current->uri(), strlen(I18n::$lang) + 1);
				?>
				<?php foreach($localisation_languages as $localisation_language): ?>
					<link rel="alternate" hreflang="<?= $localisation_language['code'] ?>" href="<?= URL::site() . $localisation_language['code'] . '/' . $generic_uri ?>" />
				<?php endforeach; ?>
				<label for="locale-changer"></label>
				<select id="locale-changer" onchange="location.href=this.value;" style="width:auto;">
					<?php foreach($localisation_languages as $localisation_language){ ?>
						<option<?=$localisation_language['code'] == I18n::$lang ? ' selected="selected"' : ''?> value="<?=URL::site() . $localisation_language['code'] . '/' . $generic_uri?>"><?=$localisation_language['title']?></option>
					<?php } ?>
				</select>
			<?php endif; ?>
		</div>
    </div>
</header>