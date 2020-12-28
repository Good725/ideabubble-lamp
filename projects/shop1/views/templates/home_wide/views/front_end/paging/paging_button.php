<?php $page_before = ($current_page > 2) ? 2 : $current_page; ?>
<div class="paging-numbers">
	<?php for ($page_num = $current_page - $page_before; $page_num < $current_page + $page_after; $page_num++): ?>
		<button type="submit" name="page" value="<?= $page_num ?>" class="paging <?= $page_num == $current_page ? 'active' : ''; ?>"><?= $page_num + 1 ?></button>
	<?php endfor; ?>
</div>

<?php if ($current_page > 0 OR $page_after > 1): ?>
	<div class="paging-chronology">
		<?php if ($current_page > 0): ?>
			<button type="submit" name="page" value="<?= $current_page - 1; ?>" class="paging paging-prev right">Prev</button>
		<?php endif; ?>

		<?php if ($page_after > 1): ?>
			<button type="submit" name="page" value="<?= $current_page + 1; ?>" class="paging paging-next right">Next</button>
		<?php endif; ?>
	</div>
<?php endif; ?>