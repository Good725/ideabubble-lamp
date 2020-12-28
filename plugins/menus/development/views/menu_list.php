<div class="row">
	<div class="span12">
		<?php
		if(isset($alert)){
            echo $alert;
		?>
			<script>
				remove_popbox();
			</script>
		<?php
		}
	?>
	</div>
</div>
<style>
    .menu_form tr td:first-child{text-align:right;}
    .menu_form tbody td:first-child {
        white-space: nowrap;
    }
    .menu_form .icon-arrow-right i,
    .menu_form .icon-arrow-right input{
        display:inline-block;
        margin-left:5%;
        width:75%;
    }

    .menu_form select {
        max-width: 150px;
    }
</style>
<div id='new_menu_div'>
	<h3>New Menu Item</h3>

	<form name="new_menu" method="POST" action="/admin/menus/save_new_menu"> <!-- Menu new item -->
		<table class="table table-bordered">
			<tr>
				<th scope="col">MENU TEXT</th>
				<th scope="col">PAGE</th>
				<th scope="col">ANCHOR / EXTERNAL URL</th>
				<th scope="col">MENU ORDER</th>
				<th scope="col">SUBMENU OF</th>
                <th scope="col">OPTIONS</th>
				<th scope="col">PUBLISH</th>
				<th scope="col">ADD</th>
			</tr>
			<tr>
				<td>
					<input type="text" name="title" class="form-control" />
                    <input type="hidden" name="menu_image" class="form-control menu_image"/>
                    <input type="hidden" name="html_attributes" class="form-control menu_html_attributes" />
				</td>
				<td><?=$pages_dropdown?></td>
				<td><input class="form-control" type="text" name="link_url" value=""/> </td>
				<td><input class="form-control" type="text" name="menu_order" value="1"></td>
				<td id='new_menu_submenu'></td>
				<td>
                    <input type="checkbox" value="1" name="open_in_window" title="Open link in new window?"/>
                    <a href="#" class="icon-plus" data-toggle="modal" data-target="#image_modal" title="Add an Image"></a>
				</td>
                <td>
					<select class="form-control" name="publish">
						<option value="1" selected="selected">Yes</option>
						<option value="0">No</option>
					</select>
				</td>
				<td>
					<input class="btn" type="submit" value="Add"/>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<a href="" class="icon-plus" id="add_menu_group"> Add to new menu group</a>
				</td>
				<td colspan="2" id="new_group" style="border-right: 0;" ><input type="hidden" name="category"/></td>
				<td colspan="4" style="border-left: 0;"></td>
			</tr>
		</table>
	</form>
</div>
<form name="menus" method="POST" action="/admin/menus/save_menus/" class="menu_form">
	<?php

	//Fill the tabs, first the "labels" later the "content of the labels", Dificult to understand, maybe it can be improve.
	$x = 1;
	foreach ($menus as $menu)
	{
		if ($x === 1)
		{
			$menu_prev = $menu['category'];
			echo '
                <ul class="nav nav-tabs" id="menu_list_tabs">
                <li class="active"><a href="#tab1" data-toggle="tab" >'.$menu['category'].'</a></li>
            ';
			$x++;
		}
		else
		{
			if ($menu_prev == $menu['category'])
				continue;
			else
				$menu_prev = $menu['category'];

			echo '<li><a href="#tab'.$x.'" data-toggle="tab" >'.$menu['category'].'</a></li>'.PHP_EOL;
			$x++;
		}

	}


	if ($x > 1)
	{
		// Diferent layer for print the data.
		$y = 1;
		$active = 'active';
		$menu_prev = $menu['category'];
		$table_header = '<tr>
				<th scope="col">MENU TEXT</th>
				<th scope="col">PAGE</th>
				<th scope="col">ANCHOR / EXTERNAL URL</th>
				<th scope="col">MENU ORDER</th>
				<th scope="col">SUBMENU OF</th>
				<th scope="col">OPTIONS</th>
				<th scope="col">PUBLISH</th>
				<th scope="col">DELETE</th>
			</tr>';
		echo '</ul>
          <div class="tab-content">'.PHP_EOL;

		foreach ($menus as $menu)
		{

			// First dropdown selector, save in a variable, print later.
			$options = '
<select name="link_tag[]" class="form-control">
<option value="0">URL</option>
<option value="-1"'.(($menu['link_tag'] == '-1') ? ' selected="selected"' : '').'>Home Page</option>
<option value="separator" disabled="disabled">------------------</option>';


			// Make selector for pages link
			foreach ($pages as $page)
			{
				if ($page['id'] == $menu['link_tag'])
				{
					$options .= '<option selected="selected" value="'.$page['id'].'" >'.$page['name_tag'].'</option>';
				}
				else
				{
					$options .= '<option value="'.$page['id'].'" >'.$page['name_tag'].'</option>';
				}
			}
			$options .= '</select>';

			//check the 'open in a window' checkboxes
			if (isset($menu['menus_target']) && $menu['menus_target'] == '_blank')
			{
				$check = 'checked="checked"';
			}
			else
			{
				$check = '';
			}


			if ($menu['publish'] == '1')
				$icon = '<i class="icon-ok"></i>';
			else
				$icon = '<i class="icon-remove"></i>';

			$spaces = '';
			$i = (int) $menu['level'];
			//echo var_dump($menu);
			while ($i > 0)
			{
				$spaces .= ' <i class="icon-arrow-right" /> ';
				$i--;
			}

			//if the categoy is the same, print in the same DIV, oterwhise print in new DIV.
			if ($y === 1)
			{
				$menu_prev = $menu['category'];
				echo '<div class="tab-pane '.$active.'" id="tab'.$y.'"><table class="table table-striped">';
				//Table header
				echo $table_header;
				//menu lines
				echo '
<tr data-id="'.$menu['id'].'" data-parent="'.$menu['parent_id'].'">
    <td>
        <input type="hidden" name="id[]" value="'.$menu['id'].'" />
        <input type="hidden" name="category[]" value="'.$menu['category'].'" />'.$spaces.'
        <input type="hidden" name="menu_image[]" value="'.$menu['image_id'].'" class="menu_image" />
        <input type="hidden" name="html_attributes[]" value="'.$menu['image_id'].'" class="menu_html_attributes" />
        <input type="text"   name="title[]" value="'.$menu['title'].'" class="form-control" />
</td>
    <td>'.$options.'</td><td><input class="form-control" type="text" name="link_url[]" value="'.$menu['link_url'].'"> </td>
    <td>'.$spaces.'<input type="text" name="menu_order[]" class="form-control" value="'.$menu['menu_order'].'"></td>
    <td>'.$menu['submenu'].'</td>
    <td>
        <input type="checkbox" '.$check.' value="'.$menu['id'].'" name="open_in_window[]" title="Open link in new window?" />
        <a href="#" title="Add an Image" class="icon-plus modal_image" data-toggle="modal" data-menu_id="'.$menu['id'].'" data-target="#image_modal"></a>
    </td>
    <td id="publish_'.$menu['id'].'" class="publish">'.$icon.'</td>
    <td id="remove_'.$menu['id'].'" class="remove"><i class="icon-remove"></i></td>
</tr>'.PHP_EOL;

				$y++;
				$active = '';
			}
			else
			{
				if ($menu_prev == $menu['category'])
				{
					echo '
<tr data-id="'.$menu['id'].'" data-parent="'.$menu['parent_id'].'">
    <td>
        <input type="hidden" name="id[]" value="'.$menu['id'].'" />
        <input type="hidden" name="menu_image[]" value="'.($menu['image_id'] ?: '').'" class="menu_image" />
        <input type="hidden" name="html_attributes[]" value="'.htmlspecialchars($menu['html_attributes']).'" class="menu_html_attributes" />
        <input type="hidden" name="category[]" value="'.$menu['category'].'" />'.$spaces.'
        <input type="text"   name="title[]" value="'.$menu['title'].'" class="form-control" />
</td>
    <td>'.$options.'</td>
    <td><input class="form-control" type="text" name="link_url[]" value="'.$menu['link_url'].'"></td>
    <td>'.$spaces.'<input type="text" name="menu_order[]" class="form-control" value="'.$menu['menu_order'].'"></td>
    <td>'.$menu['submenu'].'</td>
    <td>
        <input type="checkbox" '.$check.' value="'.$menu['id'].'" name="open_in_window[]" title="Open link in new window?" />
        <a href="#" title="More properties" class="icon-plus modal_image" data-toggle="modal" data-menu_id="'.$menu['id'].'" data-target="#image_modal"></a>
    </td>
    <td id="publish_'.$menu['id'].'" class="publish">'.$icon.'</td>
    <td id="remove_'.$menu['id'].'" class="remove"><span class="icon-remove"></span></td>
</tr>'.PHP_EOL;
				}
				else
				{
					$menu_prev = $menu['category'];
					echo '</table></div>';
					echo '<div class="tab-pane '.$active.'" id="tab'.$y.'"><table class="table table-striped">';
					//Table header
					echo $table_header;
					//menu lines
					echo '
<tr data-id="'.$menu['id'].'" data-parent="'.$menu['parent_id'].'">
    <td>
        <input type="hidden" name="menu_image[]" value="'.$menu['image_id'].'" class="menu_image" />
        <input type="hidden" name="html_attributes[]" value="'.htmlspecialchars($menu['html_attributes']).'" class="menu_html_attributes" />
        <input type="hidden" name="id[]"         value="'.$menu['id'].'" />
        <input type="hidden" name="category[]"   value="'.$menu['category'].'" />'.$spaces.'
        <input type="text"   name="title[]"      value="'.$menu['title'].'" class="form-control" />
    </td>
    <td>'.$options.'</td><td><input class="form-control" type="text" name="link_url[]" value="'.$menu['link_url'].'"></td>
    <td>'.$spaces.'<input type="text" name="menu_order[]" class="form-control" value="'.$menu['menu_order'].'"></td>
    <td>'.$menu['submenu'].'</td>
    <td>
        <input type="checkbox" '.$check.' value="'.$menu['id'].'" name="open_in_window[]" title="Open link in new window?" />
        <a href="#" title="More properties" class="icon-plus modal_image" data-toggle="modal" data-menu_id="'.$menu['id'].'" data-target="#image_modal"></a>
    </td>
    <td id="publish_'.$menu['id'].'" class="publish">'.$icon.'</td>
    <td id="remove_'.$menu['id'].'" class="remove"><span class="icon-remove"></span></td>
</tr>'.PHP_EOL;
					$y++;
					$active = '';
				}
			}
		}
		echo '</table></div></div>';
	}


	if ($x > 1)
	{
		?>
		<div id="ActionMenu" class="floatingMenu form-actions">
			<input class="btn btn-primary" type="submit" value="Save"/>
			<a class="btn-cancel" href="">Reset</a>
		</div>
		<div class="floating-nav-marker"></div>
	<?
	}

	?>

	<!-- Confirm window-->
	<div class="modal fade" id="confirm_delete">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">×</button>
					<h3>Are you sure you wish to delete this menu?</h3>
				</div>

				<div class="modal-footer">
					<a href="#" class="btn" data-dismiss="modal">Cancel</a>
					<a href="#" class="btn btn-danger" id="btn_delete_yes">Delete</a>
				</div>
			</div>
		</div>
	</div>
</form>

<?php ob_start(); ?>
    <input type="hidden" id="modal_row_id" value=""/>

    <div class="form-group">
        <div class="col-sm-3">
            <label for="menu_image">Image</label>
        </div>

        <div class="col-sm-6">
            <label class="form-select">
                <select name="menu_image" class="form-input" id="menu_image">
                    <option value=""> --- Please select ---</option>
                    <?php foreach ($available_images AS $key => $image): ?>
                        <option
                            value="<?= $image['id']; ?>"
                            data-link="<?= Model_Media::get_image_path($image['filename'], $image['location'].'/_thumbs_cms'); ?>"
                            ><?= $image['filename'] ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
        </div>

        <div class="col-sm-3">
            <img src="" id="image_preview" style="display:none;"/>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-3">
            <label for="menu-edit-html_attributes">HTML Attributes</label>
        </div>

        <div class="col-sm-9">
            <?php
            $attributes = [
                'class' => 'popinit',
                'data-trigger' => 'hover',
                'data-content' => 'Enter additional attributes in raw HTML e.g. <code>class="example" title="Title text"</code>',
                'data-html' => 'true',
                'rel' => 'popover',
                'id' => 'menu-edit-html_attributes',
            ];
            echo Form::ib_textarea(null, 'html_attributes', null, $attributes);
            ?>
        </div>
    </div>

<?php $modal_body = ob_get_clean(); ?>

<?php ob_start(); ?>
    <button class="btn btn-primary" data-dismiss="modal" id="save_modal_image">Apply</button>
    <button class="btn btn-cancel" data-dismiss="modal" aria-hidden="true" id="cancel_modal">Cancel</button>
<?php $modal_footer = ob_get_clean(); ?>

<?php
echo View::factory('snippets/modal')->set([
    'id'     => 'image_modal',
    'title'  => 'Edit menu item',
    'body'   => $modal_body,
    'footer' => $modal_footer
]);
?>