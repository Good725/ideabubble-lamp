<?php require_once Kohana::find_file('template_views', 'header') ?>
<div class="row row--search_results">
	<?=(isset($alert)) ? $alert : ''?>
	<div class="columns small-12">
		<h2 class="search_results-heading">Search Events</h2>
	</div>

	<div>
		<div class="columns small-12 medium-4 large-3">
			<div class="widget">
				<div class="widget-body">
					<form method="get">
						<div class="form-group">
							<label class="text-primary">Search</label>
							<div class="row">
								<label class="columns small-6 medium-12">
									<input type="text" name="term" id="search-filter-term" class="form_field" placeholder="<?= __('Find your event') ?>" value="<?= htmlentities(@$_REQUEST['term']) ?>" />
								</label>
							</div>
						</div>

						<div class="form-group">
							<label for="search-filter-category" class="text-primary">Category</label>
							<div class="select">
								<select class="form_field" id="search-filter-category" name="category_id">
									<option value=""><?=__('All')?></option>
									<?=html::optionsFromRows('value', 'label', $categories, @$_REQUEST['category_id']);?>
								</select>
							</div>
						</div>

						<div class="form-group">
							<label class="text-primary">Date</label>
							<div class="row">
								<label class="columns small-6 medium-12">
									<span class="show-for-sr"><?= __('Date from') ?></span>
									<input type="text" class="form_field" id="search-filter-date_after" name="date_after" value="<?= trim((isset($_REQUEST['date_after'])) ? html::chars(@$_REQUEST['date_after']) : html::chars(@$_REQUEST['date'])) ?>" placeholder="<?= __('From') ?>"/ >
								</label>
								<label class="columns small-6 medium-12">
									<span class="show-for-sr"><?= __('Date to') ?></span>
									<input type="text" class="form_field" id="search-filter-date_before" name="date_before" value="<?= trim(html::chars(@$_REQUEST['date_before'])) ?>" placeholder="<?= __('To') ?>" />
								</label>
							</div>
						</div>

						<div class="well">
							<button type="submit" class="button secondary button--full"><?=__('Filter')?></button>
						</div>
					</form>
				</div>
			</div>

		</div>

		<div class="columns small-12 medium-8 large-9">
			<?php if ( ! empty ($search_results)): ?>
				<?php
				foreach ($search_results as $result)
				{
					switch ($result['type'])
					{
						case 'event':
							$event = $result['data'];
							include 'snippets/event_widget.php';
							unset($event);
							break;

						case 'organiser':
							$organiser = $result['data'];
							include 'snippets/organiser_widget.php';
							unset($organiser);
							break;

						case 'venue':
							$venue = $result['data'];
							include 'snippets/venue_widget.php';
							unset($venue);
							break;

						default:
							include 'snippets/search_result.php';
							break;
					}
				}
				?>
			<?php else: ?>
				<h4>Sorry our search did not return any results for &quot;<?= (isset($_GET['term'])) ? htmlentities($_GET['term']) : ''; ?>&quot;</h4>
			<?php endif; ?>
		</div>
	</div>
</div>

<script>
	$(function() {
		$('#search-filter-date_after').datetimepicker({
			format:'d/m/Y',
			onShow:function() {
				this.setOptions({
					minDate: 0,
					maxDate: $('#search-filter-date_before').val()?$('#search-filter-date_before').val():false,
					closeOnDateSelect: true
				})
			},
			timepicker:false
		});
		$('#search-filter-date_before').datetimepicker({
		  format:'d/m/Y',
		  onShow:function() {
			  this.setOptions({
				  minDate: $('#search-filter-date_after').val()?$('#search-filter-date_after').val():false,
				  closeOnDateSelect: true
			  })
		  },
		  timepicker:false
	  });
	});
</script>

<?php require_once Kohana::find_file('template_views', 'footer') ?>
