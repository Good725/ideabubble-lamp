$(document).on("ready", function(){
	var shortcut_list = [];
	var current_sequence = [];
	
	function keysequence_loader(response)
	{
		for(var i in response){
			var shortcut = response[i];
			shortcut.keysequence = shortcut.keysequence.split(',');
			shortcut_list.push(shortcut);
		}
		
		$(document).on("keydown", keydown_handler);
		$(document).on("keyup", keyup_handler);
	}

	function handle_shortcut(shortcut)
	{
		current_sequence = [];
		location.href = shortcut.url;
	}

	function display_shortcuts()
	{
		var table = '<table class="table table-striped modal-dialog modal-sm" style="width:400px; background-color:#fff;"><thead><tr><th>Name</th><th>Url</th><th>Key Sequence</th></tr></thead>';
		table += '<tbody>';
		for(var i in shortcut_list){
			table += "<tr>";
			table += "<td>" + shortcut_list[i].name + "</td>";
			table += "<td>" + shortcut_list[i].url + "</td>";
			table += "<td>" + shortcut_list[i].keysequence.join(",") + "</td>";
			table += "</tr>";
		}
		table += '</tbody>';
		table += '</table>';
		var div = document.createElement("div");
		div.className = "modal fade";
		div.innerHTML = table;
		document.body.appendChild(div);
		$(div).modal('show').on('hidden.bs.modal', function(){
			$(div).remove();
		});
	}

	function check_sequence()
	{
		if(current_sequence == "SHIFT+?"){
			display_shortcuts();
			return true;
		}
		var match_found = false;
		for(var i in shortcut_list){
			var shortcut = shortcut_list[i];
			var sequence_matched = true;
			for(var k = 0 ; k < current_sequence.length && k < shortcut.keysequence.length; ++k){
				if(shortcut.keysequence[k] != current_sequence[k]){
					sequence_matched = false;
					break;
				}
			}
			if(sequence_matched){
				 if(shortcut.keysequence.length == current_sequence.length){
					handle_shortcut(shortcut);
					break;
				 } else {
					 match_found = true; // partial
				 }
			}
		}
		if(!match_found){
			current_sequence = [];
			return false;
		} else {
			return true;
		}
	}

	function keydown_handler(e)
	{
		if(e.target.tagName == "INPUT" || e.target.tagName == "TEXTAREA"){
			return;
		}
		if(e.key == "Control" || e.key == "Alt" || e.key == "Shift"){
			return;
		}
		
		var key = "";
		var ekey = e.key ? e.key : String.fromCharCode(e.which);
		
		if(e.ctrlKey){
			key += "CTRL+";
		}
		if(e.altKey){
			key += "ALT+";
		}
		if(e.shiftKey){
			key += "SHIFT+";
		}
		if(e.metaKey){
			key += "META+";
		}
		key += ekey.toUpperCase();
	}

	function keyup_handler(e)
	{
		if(e.target.tagName == "INPUT" || e.target.tagName == "TEXTAREA"){
			return;
		}
		if(e.key == "Control" || e.key == "Alt" || e.key == "Shift"){
			return;
		}
		
		var key = "";
		var ekey = e.key ? e.key : String.fromCharCode(e.which);
				
		if(e.ctrlKey){
			key += "CTRL+";
		}
		if(e.altKey){
			key += "ALT+";
		}
		if(e.shiftKey){
			key += "SHIFT+";
		}
		if(e.metaKey){
			key += "META+";
		}
		key += ekey.toUpperCase();
		
		current_sequence.push(key);
		
		if(check_sequence()){
			e.preventDefault();
		}
	}


	var prev_xhr_hide = window.disableScreenDiv.hide;
	window.disableScreenDiv.hide = false;
	$.get("/admin/profile/keyboardshortcuts_load?" + Math.random(), keysequence_loader);
	window.disableScreenDiv.hide = prev_xhr_hide;
} );
