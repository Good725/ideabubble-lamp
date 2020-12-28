<?= (isset($alert)) ? $alert : '' ?>

<?php if (isset($default_dashboard) AND $default_dashboard AND Settings::instance()->get('dashboard_date_filter') == 1): ?>
	<div class="widget-date-range-wrapper">
		<div class="widget-date-range" id="widget-date-range">
			<span class="icon-calendar"></span>
			<span id="widget-date-range-display-from"><?= date('F j, Y', strtotime('-1 year')) ?></span> &ndash;
			<span id="widget-date-range-display-to"><?= date('F j, Y') ?></span>
			<a href="#" class="expand-dropout icon-chevron-right"></a>
			<ul class="dropout" id="widget-date-range-options">
				<li><a href="#" data-range="day" data-minus="1">Past Day</a></li>
				<li><a href="#" data-range="week" data-minus="7">Past 7 Days</a></li>
				<li><a href="#" data-range="month" data-minus="30">Past 30 Days</a></li>
				<li><a href="#" data-range="year" data-minus="365">Past 365 Days</a></li>
				<li><a href="#" data-range="custom">Custom Range</a></li>
				<li class="widget-date-range-actions">
					<div id="widget-date-range-custom" style="display:none;">
						<div>
							<label for="widget-date-range-input-from">From</label>
							<input id="widget-date-range-input-from" class="datepicker" type="text" />
						</div>
						<div>
							<label for="widget-date-range-input-to">To</label>
							<input id="widget-date-range-input-to" class="datepicker" type="text" />
						</div>
					</div>

					<button type="button" id="widget-date-range-apply"  class="btn btn-primary">Apply</button>
					<button type="button" id="widget-date-range-cancel" class="btn">Cancel</button>
				</li>
			</ul>
		</div>
	</div>
<?php endif; ?>

<?php if ($report_widgets != ''): ?>
	<div id="displayWidgets"><?= $report_widgets ?></div>
<?php endif; ?>

<div class="row dashboard">
    <?= $widgets ?>
</div>

<?php if( @$GLOBALS['dashboard_has_google_map'] ){ ?>
<!-- moved from widget_active_policies_map.php, google map does not work when the functions are loaded again after ajax html. the functions below has to be loaded only once -->
<script src="<?= URL::overload_asset('js/markerclusterer.js')?>"></script>
<script src="http://www.google.com/jsapi"></script>
<script type="text/javascript">
    google.load('maps', '3', {
        other_params: 'sensor=false'
    });
	if(!window.policies_map_initialized){
	    google.setOnLoadCallback(initialize);
	} else {
		$(document).ready(initialize);
	}

    var styles = [
        [
            { url: '../images/people35.png', height: 35, width: 35, anchor: [16, 0], textColor: '#ff00ff', textSize: 10 },
            { url: '../images/people45.png', height: 45, width: 45, anchor: [24, 0], textColor: '#ff0000', textSize: 11 },
            { url: '../images/people55.png', height: 55, width: 55, anchor: [32, 0], textColor: '#ffffff', textSize: 12 }
        ],
        [
            { url: '../images/conv30.png', height: 27, width: 30, anchor: [3, 0], textColor: '#ff00ff', textSize: 10 },
            { url: '../images/conv40.png', height: 36, width: 40, anchor: [6, 0], textColor: '#ff0000', textSize: 11 },
            { url: '../images/conv50.png', width: 50, height: 45, anchor: [8, 0], textSize: 12 }
        ],
        [
            { url: '../images/heart30.png', height: 26, width: 30, anchor: [4, 0], textColor: '#ff00ff', textSize: 10 },
            { url: '../images/heart40.png', height: 35, width: 40, anchor: [8, 0], textColor: '#ff0000', textSize: 11 },
            { url: '../images/heart50.png', width: 50, height: 44, anchor: [12, 0], textSize: 12 }
        ]
    ];

    var markerClusterer = null;
    var map = null;
    var imageUrl = 'http://chart.apis.google.com/chart?cht=mm&chs=24x32&chco=FFFFFF,008CFF,000000&ext=.png';

    function refreshMap()
	{
		if ( ! refreshing_map)
		{
			refreshing_map = true;
			if (markerClusterer) {
				markerClusterer.clearMarkers();
			}

            var filters = $('#map_filter').serialize();
            // If there is a visible date range, add the "from" and "to" dates to the filters
            if ($('.widget-date-range').css('visibility') == 'visible') {
                var date_from = document.getElementById('widget-date-range-input-from').value;
                var date_to   = document.getElementById('widget-date-range-input-to').value;
                filters += '&date_from='+date_from+'&date_to='+date_to;
            }

			$.ajax({
				url      : '/admin/insurance/ajax_get_map_coordinates',
				data     : filters,
				type     : 'post',
				dataType : 'json'
			}).done(function(results)
			{
				data = (results == null) ? [] : results;
				var markers = [];

				var markerImage = new google.maps.MarkerImage(imageUrl, new google.maps.Size(24, 32));
				var infowindow  = new google.maps.InfoWindow({content: ''});

				for (var i = 0; i < data.length; i++) {
					var latLng = new google.maps.LatLng(
						data[i].latitude,
						data[i].longitude);
					var marker = new google.maps.Marker({
						position: latLng,
						draggable: false,
						icon: markerImage,
						description: data[i].policynumber
					});

					marker_popover(marker, map, infowindow, '<div style="height:30px;width:150px;font-size:12px;">Policy number: ' + data[i].policynumber+'</div>');

					markers.push(marker);
				}

				markerClusterer = new MarkerClusterer(map, markers, { zoomOnClick: false });
				cluster_popover(markerClusterer, map, infowindow);
				refreshing_map = false;
			});
		}

    }

    function initialize() {
		map = new google.maps.Map(document.getElementById('map'), {
			zoom: 5,
			center: new google.maps.LatLng(52.439046, -3.867188),
			mapTypeId: google.maps.MapTypeId.ROADMAP
		});
		refreshing_map = false;
		refreshMap();
		window.policies_map_initialized = true;
    }

	function marker_popover(marker, map, infowindow, html)
	{
		google.maps.event.addListener(marker, 'click', function()
		{
			infowindow.setContent(html);
			infowindow.open(map, marker);
		});
	}

	function cluster_popover(markerCluster, map, infowindow)
	{
		google.maps.event.addListener(markerCluster, 'clusterclick', function(cluster)
		{
			var info = new google.maps.MVCObject;
			info.set('position', cluster.center_);
			var markers = cluster.getMarkers();
			var content = '<div style="max-height:100px;width:220px;overflow-x:auto;"><h3>Policy Numbers</h3><p style="font-family:monospace;">';
			for (var i = 0; i < markers.length; i++)
			{
				content += markers[i].description + ', ';
			}
			content = content.replace(/[,\s]+$/, '') + '</p></div>';
			infowindow.close();
			infowindow.setContent(content);
			infowindow.open(map, info);
			google.maps.event.addListener(map, 'zoom_changed', function() { infowindow.close() });

		});
	}

	function clearClusters(e) {
        e.preventDefault();
        e.stopPropagation();
        markerClusterer.clearMarkers();
    }

</script>
<?php } ?>

<?php if (Settings::instance()->get('dashboard_date_filter') == 1): ?>
	<script>
		$('#widget-date-range-options').find('[data-range]').on('click', function(ev)
		{
			ev.preventDefault();
			var from_input    = document.getElementById('widget-date-range-input-from');
			var to_input      = document.getElementById('widget-date-range-input-to');
			var custom_fields = document.getElementById('widget-date-range-custom');
			var date          = new Date();
			custom_fields.style.display = 'none';

			// Remove highlight from previously selected item
			$(this).parents('ul').find('.dropout-highlight').removeClass('dropout-highlight');

			switch (this.getAttribute('data-range'))
			{
				// Make input boxes available for custom entry
				case 'custom':
					custom_fields.style.display = 'block';
					break;

				// Put dates from chosen range into the input boxes
				default:
					var from_date = new Date();
					var   to_date = new Date();
					from_date.setDate(from_date.getDate() - this.getAttribute('data-minus'));
					from_input.value = from_date.getDate()+'-'+(from_date.getMonth()+1)+'-'+from_date.getFullYear();
					to_input.value =   to_date.getDate()+'-'+(  to_date.getMonth()+1)+'-'+  to_date.getFullYear();
					break;
			}

			// Display dates in the main box using the format "January 1, 2001"
			var to_date_parts   =   to_input.value.split('-');
			var from_date_parts = from_input.value.split('-');
			if (to_date_parts.length == 3 && from_date_parts.length == 3)
			{
				document.getElementById('widget-date-range-display-from').innerHTML = get_month_name(from_date_parts[1]-1)+' '+from_date_parts[0]+', '+from_date_parts[2];
				document.getElementById('widget-date-range-display-to'  ).innerHTML = get_month_name(  to_date_parts[1]-1)+' '+  to_date_parts[0]+', '+  to_date_parts[2];
			}

			// Highlight the selected option
			$(this).addClass('dropout-highlight');
		});

		$('#widget-date-range-cancel').on('click', function()
		{
			document.getElementById('widget-date-range').querySelector('.expand-dropout.expanded').click();
		});
		$('#widget-date-range-apply').on('click', function()
		{
			var date_from = document.getElementById('widget-date-range-input-from').value;
			var date_to   = document.getElementById('widget-date-range-input-to').value;

            // If a map is being reloaded, keep a copy of its filters
            var $old_map_filter = $('#map_filter').detach();

			window.old_map_div = document.getElementById("map");
			if(window.old_map_div){
				window.old_map_div.parentNode.removeChild(window.old_map_div);
			}
			$('#displayWidgets').load(
                '/admin/reports/ajax_render_dashboard_reports?dashboard-from='+date_from+'&dashboard-to='+date_to,
                function() {
                    // Restore the previous map filters and refresh the map
                    $('#map_filter').replaceWith($old_map_filter);
                    if (typeof window.refreshMap == 'function') {
                        window.refreshMap();
                    }
                }
            );
			update_displayed_date();
			document.getElementById('widget-date-range').querySelector('.expand-dropout.expanded').click();
		});

		// Display dates in the main box using the format "January 1, 2001"
		function update_displayed_date()
		{
			var from_date_parts = document.getElementById('widget-date-range-input-from').value.split('-');
			var to_date_parts   = document.getElementById('widget-date-range-input-to'  ).value.split('-');

			if (to_date_parts.length == 3 && from_date_parts.length == 3)
			{
				document.getElementById('widget-date-range-display-from').innerHTML = get_month_name(from_date_parts[1]-1)+' '+from_date_parts[0].replace(/^[0]+/g,"")+', '+from_date_parts[2];
				document.getElementById('widget-date-range-display-to'  ).innerHTML = get_month_name(  to_date_parts[1]-1)+' '+  to_date_parts[0].replace(/^[0]+/g,"")+', '+  to_date_parts[2];
			}
		}

		function get_month_name(number)
		{
			switch(number)
			{
				case 0:  return 'January';   break;
				case 1:  return 'February';  break;
				case 2:  return 'March';     break;
				case 3:  return 'April';     break;
				case 4:  return 'May';       break;
				case 5:  return 'June';      break;
				case 6:  return 'July';      break;
				case 7:  return 'August';    break;
				case 8:  return 'September'; break;
				case 9:  return 'October';   break;
				case 10: return 'November';  break;
				case 11: return 'December';  break;
				default: return '';          break;
			}
		}
	</script>
<?php endif; ?>
