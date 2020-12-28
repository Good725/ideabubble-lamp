<?php if (isset($id)): ?>
	<?php // Create a PDF using the raster image ?>
	<img src="/tmp/product_<?= $id; ?>.png" />
<?php elseif (isset($layers_obj)): ?>
	<?php // Create a PDF using the layers information ?>
	<style>
		html, body, div, img { margin: 0; padding: 0;}
		body { background-color: <?= $background_color ?>}
		.layer { position: absolute; }
	</style>
	<?php $in = 1 /300 ?>

	<?php foreach ($layers_obj as $layer): ?>
		<?php if ($layer->type == 'image'): ?>
			<div class="layer" style="
				left:   <?= $layer->x      / 300 ?>in;
				top:    <?= $layer->y      / 300 ?>in;
				width:  <?= $layer->width  * $layer->scale / 300 ?>in;
				height: <?= $layer->height * $layer->scale / 300 ?>in;
			">
				<img src="<?= $layer->src ?>" class="layer" style="
					width:  <?= $layer->width  * $layer->scale / 300 ?>in;
					height: <?= $layer->height * $layer->scale / 300 ?>in;
				" />
			</div>
		<?php elseif ($layer->type == 'text'): ?>
			<div class="layer" style="
				width:            <?= ($layer->width  - 2 * $layer->padding - 2 * $layer->border_width) / 300 ?>in;
				height:           <?= ($layer->height - 2 * $layer->padding - 2 * $layer->border_width) / 300 ?>in;
				left:             <?= $layer->x      / 300     ?>in;
				top:              <?= $layer->y      / 300     ?>in;
				background-color: <?= $layer->background_color ?>;
				border:           <?= $layer->border_width / 300 ?>in solid <?= $layer->border_color ?>;
				<?php if ($layer->rounded): ?>
					border-radius:    <?= $layer->padding / 300    ?>in;
				<?php endif; ?>
				text-align:       <?= $layer->text_align       ?>;
				padding:          <?= $layer->padding / 300    ?>in;
				<?php foreach ((array) $layer->text_styles as $property => $value): ?>
					<?= $property ?>: <?= $value ?>;
				<?php endforeach; ?>
			"><?= nl2br($layer->text) ?></div>
		<?php endif; ?>
	<?php endforeach; ?>
<?php endif; ?>