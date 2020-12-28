<div id="messaging-<?= isset($message_type) ? $message_type.'-' : '' ?>schedule" class="tabs-pills-content" style="display: none;">
	<div class="padding10">
		<div class="full-grid margin-bottom-15"> 
			<h2 class="theme-heading left">Schedule</h2>
			<div class="btn-group btn-group-slide left" data-toggle="buttons">
				<label class="btn btn-plain  active">
					<input type="radio" checked="checked" value="1" name="publish">Yes
				</label>
				<label class="btn btn-plain ">
					<input type="radio" value="0" name="publish">No
				</label>
			</div>
		</div>   
		<div class="full-grid margin-bottom-15">   
			<h2 class="theme-heading">Interval</h2>
			<ul class="set-interval">
				<li>
					Minutes
					<label class="icon-wrap">
						<input value="02:00am" type="text">
						<i class="fa icon-clock-o" aria-hidden="true"></i>
					</label>
				</li>
				<li>
					Hours
					<label class="icon-wrap">
						<input value="02:00am" type="text">
						<i class="fa icon-clock-o" aria-hidden="true"></i>
					</label>
				</li>
				<li>
					Dates
					<label class="icon-wrap">
						<input value="02:00am" type="text">
						<i class="fa icon-calendar" aria-hidden="true"></i>
					</label>
				</li>
				<li>
					Months
					<label class="icon-wrap">
						<input value="02:00am" type="text">
					   <i class="fa icon-calendar" aria-hidden="true"></i>
					</label>
				</li>
			</ul>     
			

		</div>
		<div class="sub-title">
		<h3>Days in Week</h3>
		</div>
		<ul class="week-name">
			<li><a href="#" class="disable">S</a></li>
			<li><a href="#">M</a></li>
			<li><a href="#">T</a></li>
			<li><a class="selected" href="#">W</a></li>
			<li><a href="#" class="selected">T</a></li>
			<li><a href="#">F</a></li>
			<li><a href="#" class="disable">S</a></li>
		</ul>
		<div class="bottom-btn">
			<input class="btn btn-lg btn-outline-primary" type="button" value="Save as template"><br/>
			<button class="btn btn-lg btn-outline-primary">
				Save as Draft <i class="fa icon-angle-down" aria-hidden="true"></i>
			</button>
			<button class="btn btn-lg btn-outline-primary">
				Send <i class="fa icon-angle-down" aria-hidden="true"></i>
			</button>
            <button type="button" class="btn btn-lg btn-cancel">Cancel</button>
		</div>
	</div>
</div>
