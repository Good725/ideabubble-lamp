<?= (isset($alert)) ? $alert : '' ?>
<?php
	if(isset($alert)){
	?>
		<script>
			remove_popbox();
		</script>
	<?php
	}
?>
<div id="messaging_send_one">
	<form name="messaging_send_one_form" method="post">
	<?php if($send_result === false){ ?>
	<p>Message creation failed!</p>
	<?php } ?>
	<?php if(is_numeric($send_result)){ ?>
	<p>Message has been created successfully.</p>
	<?php } ?>
    <table id="messaging_send_one_table" class="table table-striped">
		<tr>
			<th>Driver:</th>
			<td>
				<select name="provider">
				<option value="">Select Driver</option>
				<?php foreach($messaging_drivers as $driver => $providers){ ?>
					<optgroup label="<?=ucfirst($driver)?>">
					<?php foreach($providers as $provider){ ?>
						<option value="<?=$driver . '.' . strtolower($provider)?>" data-has-message-subject="<?=$provider->has_message_subject();?>" data-max-message-size="<?=$provider->max_message_size();?>"><?=$provider?></option>
					<?php } ?>
					</optgroup>
				<?php } ?>
				</select>
			</td>
		</tr>
		<tr>
			<th>To:</th>
			<td>
				<table cellspacing="5" cellpadding="0">
				<tbody></tbody>
				<tfoot>
					<tr><th colspan="2"><button id="add_to" type="button">add recipient</button></th></tr>
				</tfoot>
				</table>
			</td>
		</tr>
		<tr><th>Subject:</th><td><input type="text" name="subject" /></td></tr>
		<tr>
			<th valign="top">Enter Message or select a system news page:</th>
			<td>
				<textarea name="message"></textarea>
				<select name="page_id">
					<option value=""></option>
					<?php foreach($news_pages as $news_page){ ?>
					<option value="<?=$news_page['id']?>"><?=$news_page['title']?></option>
					<?php } ?>
				</select>
			</td>
		</tr>
		<tr><th colspan="2"><button type="submit" name="send">Send</button></th></tr>
    </table>
	</form>
	<script>
	var recipient_provider_ids = <?=json_encode($recipient_provider_ids);?>;
	function add_recipient()
	{
		var tbody = $("#messaging_send_one_table table tbody")[0];
		var tr = document.createElement("tr");
		var td = null;
		var select = null;
		var input = null;
		
		td = document.createElement("td");
		select = document.createElement("select");
		select.name = "target_type[]";
		select.setAttribute('class', 'form-control');
		var options_html = '<option value=""></option>';
		for(var i in recipient_provider_ids){
			options_html += '<option value="' + recipient_provider_ids[i] + '">' + recipient_provider_ids[i] + '</option>';
		}
		options_html += '<option value="EMAIL">Email</option><option value="PHONE">Phone</option>';
		select.innerHTML = options_html;
		td.appendChild(select);
		tr.appendChild(td);
		
		td = document.createElement("td");
		input = document.createElement("input");
		input.type = "text";
		input.name = "target[]";
		td.appendChild(input);
		tr.appendChild(td);
		
		tbody.appendChild(tr);
		
		$(input).autocomplete({
			source: function(data, callback){
				if(select.value == "EMAIL" || select.value == "PHONE"){
					callback([]);
				} else {
					var driver = $('[name=provider]').val().split('.')[0];
					data.type = select.value;
					data.driver = driver;
					$.get("/admin/messaging/to_autocomplete",
							data,
							function(response){
								callback(response);
								$(".ui-helper-hidden-accessible").css("display", "none");
								$(".ui-autocomplete").css("max-height", "300px").css("overflow","auto");
							});
				}
			}
		});
	}
	$("#messaging_send_one_table #add_to").on("click", function(){
		add_recipient();
	});
	$("#messaging_send_one_table [name=provider]").on("change", function(){
		//alert(this.selectedIndex);
		var has_subject = ($(this.options[this.selectedIndex]).attr("data-has-message-subject"));
		$("#messaging_send_one_table").find("[name=subject]").prop("disabled", ! has_subject);
	});
	$("[name=messaging_send_one_form]").on("submit", function(){
		var max_size = parseInt($(this.provider.options[this.provider.selectedIndex]).attr("data-max-message-size"));
		if(this.message.value.length > max_size){
			alert("Maximum message size is " + max_size + "\nPlease shorten your message");
			return false;
		}
		return true;
	});
	</script>
</div>
