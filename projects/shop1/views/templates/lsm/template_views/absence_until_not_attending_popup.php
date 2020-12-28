<div id="popup" class="sectionOverlay" style="display:block;">
  <div class="overlayer"></div>
  <div class="screenTable">
    <div class="screenCell">
      <div class="sectioninner">
        	<div class="popup-title">Wll attend until
        		<a class="basic_close"><i class="fa fa-times" aria-hidden="true"></i></a>
        	</div>	
       		
	        <div class="popup-content">
	        	<form>
		        	<div class="one-half-view">
		        		<div class="colm">
		        			<div class="form-wrap">
		        				<label class="form-label">End Date</label>
		        				<label class="icon-wrap">
			        				<input type="text"  value="Mon 26, 2016">
			        				<i class="fa fa-calendar" aria-hidden="true"></i>
		        				</label>
		        			</div>
		        			<div class="form-wrap">
		        				<label class="form-label">End Time</label>
		        				<label class="icon-wrap">
		        					<input type="text"  value="2:00pm">
		        					<i class="fa fa-clock-o" aria-hidden="true"></i>
		        				</label>
		        			</div>

		        		</div>
		        		<div class="colm">
		        			<ul>
		        				<li>
		        					<div class="check-wrap small-size">
										<input id="check1" type="checkbox">
										<label for="check1">This class only</label>
									</div>
		        				</li>
		        				<li>
		        					<div class="check-wrap small-size">
										<input id="check2" type="checkbox">
										<label for="check2">All classes for this student</label>
									</div>
		        				</li>
		        				<li>
		        					<div class="check-wrap small-size">
										<input id="check3" type="checkbox">
										<label for="check3">All classes for this family.</label>
									</div>
		        				</li>

		        			</ul>

		        		</div>
		        	</div>
		        	<div class="colm">
			        	<div class="green-subheading black">	
							<h3>Please confirm attendance for these 7 Classes</h3>
						</div>
						<div class="slider-wrapper">
								<ul>
									<li>
										<a href="#"><span>Mon 12 Dec</span>
											<span class="sub-name">Biology</span>
											8 pm <i class="fa gray-circle fa-exclamation" aria-hidden="true"></i></a></li>
									<li>
										<a href="#">
											<span>Mon 12 Dec</span>
											<span class="sub-name">Accounting</span>
											10 pm <i class="fa gray-circle fa-exclamation" aria-hidden="true"></i></a></li>
									<li>
										<a href="#"><span>Tue 13 Dec</span>
											<span class="sub-name">Business</span>
											6 pm <i class="fa gray-circle fa-exclamation" aria-hidden="true"></i>
										</a>
									</li>
									<li>
										<a href="#">
											<span>Wed 13 Dec</span>
											<span class="sub-name">Business</span> 
											8 pm <i class="fa gray-circle fa-exclamation" aria-hidden="true"></i></a>
									</li>
									<li>
										<a href="#"><span>Mon 19 Dec</span>
										<span class="sub-name">Maths</span>  
											10 pm <i class="fa gray-circle fa-exclamation" aria-hidden="true"></i></a>
									</li>
								
								</ul>
								<div class="slider_action">
									<a href="#" class="prv_arrow"><i class="fa fa-angle-left" aria-hidden="true"></i></a>
									<a href="#" class="next_arrow"><i class="fa fa-angle-right" aria-hidden="true"></i></a>
								</div>

						</div>
					</div>	
					<div class="colm">
						<label class="form-label">Note</label>
						<textarea></textarea>
					</div>
					<div class="center-align-btn">
						<input value="Confirm" class="tempBtn light--blue" type="submit">
						<a href="#" class="cancel">Cancel</a>
					</div>
				</form>	
	        </div>

      </div>
    </div>
  </div>
</div>

<script type="text/javascript">
	$('.screenCell .basic_close').click(function(){
		$(".sectioninner").removeClass("zoomIn");
		$('.sectionOverlay').css('display','none');
		return false;
	});
	
	$('.popup-btn').click(function(){
		$(".sectioninner").addClass("zoomIn");
		$('#popup').css('display','block');
		return false;
	});
</script>
