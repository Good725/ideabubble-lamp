<?= isset($alert) ? $alert : '' ?>
<?php
	if(isset($alert)){
	?>
		<script>
			remove_popbox();
		</script>
	<?php
	}
?>
<div class="table_scroll">
	<table class="table table-striped dataTable border-hover">
		<thead>
			<tr>
				<th class="sorting_asc">Teacher</th>
				<th class="sorting">Schedule</th>
				<th class="sorting">Course</th>
				<th class="sorting">Description</th>
				<th class="sorting">Date</th>
				<th class="sorting">Actions</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>Julie Kilmartin</td>
				<td>Junior Certificate 1</td>
				<td>Irich</td>
				<td>Text of short description about homework</td>
				<td>11-11-2016</td>
				<td>
					<div class="action-btn">
						<a href="#">...</a>
						<ul>
							<li><a href="#">View</a></li>
							<li><a href="#">Delete</a></li>
						</ul>
					</div>
				</td>
			</tr>
			<tr>
				<td>John Smith</td>
				<td>Junior Certificate 1</td>
				<td>Maths</td>
				<td>Text of short description about homework</td>							
				<td>15-11-2016</td>
				<td>
					<div class="action-btn">
						<a href="#">...</a>
						<ul>
							<li><a href="#">View</a></li>
							<li><a href="#">Delete</a></li>
						</ul>
					</div>
				</td>
			</tr>
			<tr>
				<td>Peter Samsonite</td>
				<td>Junior Certificate 1</td>
				<td>French</td>
				<td>Text of short description about homework</td>							
				<td>11-12-2016</td>
				<td>
					<div class="action-btn">
						<a href="#">...</a>
						<ul>
							<li><a href="#">View</a></li>
							<li><a href="#">Delete</a></li>
						</ul>
					</div>
				</td>
			</tr>
		</tbody>
	</table>
		
</div>
<script type="text/javascript">
$(document).ready(function(){
 	$('.action-btn a').click(function () {
        $(this).toggleClass('open');
        $(this).siblings('.action-btn ul').slideToggle();
        return false;
    });
});
</script>