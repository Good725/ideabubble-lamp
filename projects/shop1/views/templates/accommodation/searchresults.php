<?php include 'template_views/header.php' ?>
<?php
$result_format = (isset($_GET['result_format']) AND in_array($_GET['result_format'], array('listing', 'map', 'image'))) ? $_GET['result_format'] : 'listing';

parse_str($_SERVER['QUERY_STRING'], $result_array);
unset($result_array['result_format']);
unset($result_array['page']);
$pagination_url = '//'.$_SERVER['HTTP_HOST'].'/search-results.html?'.$_SERVER['QUERY_STRING'];
$_SERVER['QUERY_STRING'] = http_build_query($result_array);
$no_format_url = '//'.$_SERVER['HTTP_HOST'].'/search-results.html?'.$_SERVER['QUERY_STRING'];
$pagination_count = @ceil($search_results['count'] / 10);

?>
<div class="search-results-info" id="search-results-info">
	<ul class="property-tabs">
		<li><a href="<?= $no_format_url.'&result_format=listing#search-criteria' ?>"><?= __('Listings') ?></a></li>
		<li><a href="<?= $no_format_url.'&result_format=map#search-criteria'     ?>"><?= __('Maps')     ?></a></li>
		<li><a href="<?= $no_format_url.'&result_format=image#search-criteria'   ?>"><?= __('Images')   ?></a></li>
	</ul>

	<?php $number_found = (isset($search_results['results'])) ? $search_results['count'] : 0 ?>
	<div><?= $number_found ?> properties found</div>
</div>

<?php if (isset($search_results['results']) AND count($search_results['results']) > 0): ?>
	<div class="space-between-cols search-results-wrapper search-result-format-<?= $result_format ?>">
		<?php
		// If the format is image or map, loop through each result and print out the relevant view
		if (in_array($result_format, array('listing', 'image'))):?>
            <?php
            $linkParams  = array();
            if (isset($_GET['check_in'])) {
                $linkParams['check_in'] = $_GET['check_in'];
            }
            if (isset($_GET['check_out'])) {
                $linkParams['check_out'] = $_GET['check_out'];
            }
            if (isset($_GET['guests'])) {
                $linkParams['guests'] = $_GET['guests'];
            }
            if (isset($_GET['bedrooms'])) {
                $linkParams['bedrooms'] = $_GET['bedrooms'];
            }
			foreach ($search_results['results'] as $property_data)
			{
				$link =' /property-details.html/'.$property_data->url . '?' . http_build_query($linkParams);
				include 'template_views/searchresults_'.$result_format.'.php';
			}
            if ($pagination_count > 1): ?>
            <div class="pagination">
                <span class="pagination-index"><span>Page <?= $search_results['page' ]?> of <?= $pagination_count ?></span></span>
                <ul class="pagination-pages">
					<li class="pagination-prev">
						<a <?= ($search_results['page'] != 1) ? 'href="'.$pagination_url.'&page='.($search_results['page'] - 1).'#search-criteria"' : 'href="#" class="disabled"' ?>>
							<?=__('Previous')?>
						</a>
					</li>
                    <?php for($i = 1; $i <= $pagination_count; $i++): ?>
                        <li><a href="<?= $pagination_url.'&page='.$i ?>#search-criteria"><?= $i ?></a></li>
                    <?php endfor; ?>
					<li class="pagination-next">
						<a <?= ($search_results['page'] + 1 != $i) ? 'href="'.$pagination_url.'&page='.($search_results['page'] + 1).'#search-criteria"' : 'href="#" class="disabled"' ?>>
							<?=__('Next')?>
						</a>
					</li>
                </ul>
            </div>
            <?php endif;

		// If the format is map, loop through each search result to build an array of data for the map pointers
		// Then print the map
		elseif ($result_format == 'map'):

			$needles = array();
			$info_windows = array();
			foreach ($search_results['results'] as $property_data)
			{
                $link =' /property-details.html/'.$property_data->url;
				if ($property_data->latitude AND $property_data->longitude)
				{
					$needles[] = array($property_data->name, $property_data->latitude , $property_data->longitude);
					$info_windows[] = array('<div class="map-window"><div class="related-property">'.View::factory('property_thumbnail')->set('property_item', $property_data)->set('description', TRUE)->set('link',$link).'</div></div>');
				}
			}
			include 'template_views/searchresults_map.php';

            endif;
		?>
	</div>
<?php endif; ?>

<?php include 'template_views/footer.php' ?>