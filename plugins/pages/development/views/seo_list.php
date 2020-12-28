<?php
if (isset($alert))
{
    echo $alert;
}
?>
<?php
if(isset($alert)){
?>
	<script>
		remove_popbox();
	</script>
<?php
}
?>

<form method="post">
  <div class="form-action-group fixedMenu">
    <button type="submit" class="btn" id="btn_save">Save</button>
  </div>

  <table class='table table-striped dataTable'>
    <thead>
      <tr>
        <th>Page Name</th>
        <th>Title</th>
        <th>SEO Keywords</th>
        <th>Meta Description</th>
        <th>Footer Text</th>
        <th>X-Robots-Tag</th>
        <th>Options</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($pages as $id => $page): ?>
        <?php
          if(strrpos($page['name_tag'], '.')){
              $page_tag = $page['name_tag'];
              $found_position = strrpos($page_tag, '.');
              $page['name_tag'] = substr($page_tag, 0, $found_position);
          }
        ?>
        <tr>
          <input type="hidden" value="<?= $page['id'] ?>" name="seo[<?=$id?>][id]">
          <td><a href='<?php echo URL::Site('admin/pages/edit_pag/' . $page['id']); ?>'><?php echo $page['name_tag']; ?></a></td>
          <td><input value="<?php echo $page['title']; ?>" name="seo[<?=$id?>][title]" /></td>
          <td><input value="<?php echo trim($page['seo_keywords']); ?>" name="seo[<?=$id?>][seo_keywords]"</td>
          <td><input value="<?php echo trim($page['seo_description']); ?>" name="seo[<?=$id?>][seo_description]"</td>
          <td><textarea name="seo[<?=$id?>][footer]"><?php echo $page['footer']; ?></textarea></td>
          <td>
            <select class="form-control" name="seo[<?=$id?>][x_robots_tag]" id="edit_page_x_robots_tag">
              <option value=""></option>
              <?=HTML::optionsFromArray(
                  array(
                      'noindex' => 'noindex',
                      'nofollow' => 'nofollow',
                      'noarchive' => 'noarchive',
                      'noindex,nofollow' => 'noindex,nofollow'
                  ),
                  @$page['x_robots_tag']
              )?>
            </select>
          </td>
          <td><a href='<?php echo URL::Site('/' . $page['name_tag'].'.html'); ?>' target="_blank">View</a></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</form>
