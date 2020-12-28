<!-- Documents Actions Menu -->
<div class="btn-group pull-right">
	<a class="btn dropdown-toggle" data-toggle="dropdown" href="#">Actions<span class="caret"></span></a>
	<ul id="document_actions_menu" class="dropdown-menu">
		<li><a class="upload_document" href="#">Upload Document</a></li>
		<?php if ($level == 'contact'): ?>
			<li><a class="generate_documents" href="#">Generate Documents</a></li>
		<?php endif; ?>
	</ul>
</div>

