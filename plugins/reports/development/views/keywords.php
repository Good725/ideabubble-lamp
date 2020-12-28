<thead><tr><th>ID</th><th>Keyword</th><th>URL</th><th>Search Engine</th><th>Delete</th></tr></thead>
<tbody>
<?php
foreach($keywords AS $key=>$keyword)
{
    echo '<tr><td>',$keyword['id'],'</td><td>',$keyword['keyword'],'</td><td>',$keyword['url'],'</td><td>'.$search_engine.'</td><td><i class="icon-remove"></i></td></tr>';
}
?>
</tbody>
