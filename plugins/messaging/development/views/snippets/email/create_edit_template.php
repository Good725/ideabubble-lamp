<div id="template" class="content-box create--template">
	<div class="mail-title">
		<h2>Create or edit Template</h2>
		<a href="javascript:void(0)" class="close-btn"><i class="fa icon-times" aria-hidden="true"></i></a>
	</div>
	<div class="padding10 temp-form">
		<div class="form-wrap table-box">
			<div class="label-name grid">Templates: </div>
			<div class="grid">
                <?= Form::ib_select(__('Choose existing Template to edit'), NULL); ?>
			</div>
		</div>
		<div class="form-wrap table-box">
			<div class="grid label-name ">Template Name: </div>
			<div class="grid">
				<input type="text" value="This will be used as a referance in code">
			</div>
		</div>
		<div class="form-wrap table-box">
			<div class="label-name grid">Category: </div>
			<div class="grid">
                <?= Form::ib_select(NULL, NULL, array('contacts' => __('Contacts'))); ?>
			</div>    
		</div>
		<div class="form-wrap">
			<textarea>Description:</textarea>
		</div>
		<div class="center-btn">
			<input type="submit" value="Save" class="btn btn-lg btn-primary">
            <button type="button" class="btn btn-lg btn-cancel">Cancel</button>
		</div>
	</div>
</div>
