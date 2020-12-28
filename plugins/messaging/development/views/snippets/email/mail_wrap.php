<div class="mail-wrap"> 
	<div class="top-head">
		<div class="left">
			<ul>
				<li><a href="#"><i class="fa icon-reply" aria-hidden="true"></i></a></li>
				<li><a href="#"><i class="fa icon-reply-all" aria-hidden="true"></i></a></li>
				<li><a href="#"><i class="fa icon-share" aria-hidden="true"></i></a></li>
				<li><a href="javascript:void(0)" class="add-btn" rel="check-link"><i class="fa icon-link" aria-hidden="true"></i></a></li>
			</ul>
		</div>
		<div class="right">
			<ul>
				<li><a href="javascript:void(0)"  class="detail-btn" rel="detailed-status"><i class="fa icon-file-text" aria-hidden="true"></i></a></li>
				<li><a href="javascript:void(0)" class="detail-btn" rel="attachments"><i class="fa icon-paperclip" aria-hidden="true"></i></a></li>
				<li><a href="javascript:void(0)" class="basic_close"><i class="fa icon-times" aria-hidden="true"></i></a></li>
			</ul>
		</div>
	</div>
	<div class="user-info">
		<div class="left-section">
			<figure class="imgbox">
				<img src="<?php echo URL::get_engine_plugin_assets_base('messaging');?>/images/user-img.jpg">
			</figure>
			<i class="fa icon-envelope" aria-hidden="true"></i>
		</div>
		<div class="right-section">
			<ul>
				<li><label>From:</label>maja@ideabubble.ie</li>
				<li class="time">08:00</li>
				<li><label>To:</label>michael@ideabubble.ie</li>
				<li><label>CC:</label>michael@ideabubble.ie <a href="javascript:void(0)" class="pullBtn"><i class="fa icon-angle-down" aria-hidden="true"></i></a>
					<ul class="toggle-box more--mail ">
						<li><label>CC:</label>michael@ideabubble.ie</li>
						<li><label>CC:</label>michael@ideabubble.ie</li>
						<li><label>CC:</label>michael@ideabubble.ie</li>
						<li><label>CC:</label>michael@ideabubble.ie</li>
					</ul>
				</li>
			</ul>
			<h5><label>Subject:</label>New Policy</h5>                                   
		</div>
	</div>
	<div id="check-link" class="add-files-wrap read--links">
		<div class="link-wrap">
			<div class="grid">Select</div>
			<div class="grid">
                <?= Form::ib_select(NULL, NULL, array('contacts' => __('Contacts'))); ?>
			</div>
			<div class="grid">    
				<a href="#" class="btn btn-lg btn-primary-outline d-block p-2">Add</a>
			</div>    
		</div>    
	</div>
	<div class="descbody">
		These are the assumptions we have made to make the quoting process easier for you to perform. You will need to confirm that these are correct before you can proceed with cover.<br/> <br/>
		You:<br/>
		<ul>
			<li>Are resident(s) in Ireland</li>
			<li>Have not had more than 2 accidents or losses in the past 5 years in connection with any craft you have used or owned, and the total amount claimed or lost did not exceeded €1,000,</li>
			<li>And all other drivers are experienced in handling this type of vessel.</li> 
		</ul>
		<br/>
		<br/>
		<br/>
		<div class="bottom">
			Yachtsman Euromarine<br/>
			College Road<br/>
			Clane,<br/>
			Co. Kildare<br/>
			Ireland: + 353 (0) 45 982668<br/>
			N Ireland: + 44 (0) 28 90995822<br/>
			Spain: + 34 966260484<br/>
			<a href="#">www.yachtsman.ie</a><br/>
			---------
		</div>                           
	</div>
</div>
