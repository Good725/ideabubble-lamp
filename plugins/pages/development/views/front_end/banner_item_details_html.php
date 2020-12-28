<li class="banner_<?=$count?><?=$first?><?=$last?>" >
	<? if(isset($link_to_page) AND $link_to_page != '') echo '<a href="'.URL::base().$link_to_page.'">'; ?>
	<img src="<?=$url?>" alt="<?=$banner['filename']?>" title="<?=$banner['filename']?>" width="<?=$width?>" height="<?=$height?>" >
	<? if(isset($link_to_page) AND $link_to_page != '') echo '</a>'; ?>
</li>