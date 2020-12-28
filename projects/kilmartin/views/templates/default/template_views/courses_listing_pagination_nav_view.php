<?php

$actual_pages_count = ceil($total_pages);
$current_page = $page;

$prev_button_active = ($current_page > 1) ? TRUE : FALSE;
$next_button_active = ($current_page < $actual_pages_count) ? TRUE : FALSE;

// Build the general GET URL for the course-list.html Page
$pagination_base_url = URL::base() . 'course-list.html';
if ($title !== FALSE) $pagination_base_url .= (strpos($pagination_base_url, '/?') === FALSE) ? '/?title=' . urlencode($title) : '&amp;title=' . urlencode($title);
if ($location !== FALSE) $pagination_base_url .= (strpos($pagination_base_url, '/?') === FALSE) ? '/?location=' . urlencode($location) : '&amp;location=' . urlencode($location);
if ($level !== FALSE) $pagination_base_url .= (strpos($pagination_base_url, '/?') === FALSE) ? '/?level=' . urlencode($level) : '&amp;level=' . urlencode($level);
if ($category !== FALSE) $pagination_base_url .= (strpos($pagination_base_url, '/?') === FALSE) ? '/?category=' . urlencode($category) : '&amp;category=' . urlencode($category);
if ($sort !== FALSE) $pagination_base_url .= (strpos($pagination_base_url, '/?') === FALSE) ? '/?sort=' . urlencode($sort) : '&amp;sort=' . urlencode($sort);
$pagination_base_url .= (strpos($pagination_base_url, '/?') === FALSE) ? '/?page=' : '&amp;page=';


?>
<!-- Pagination Nav -->
<div class="courses_list_pagination left">
    <input type="hidden" id="current_page" value="<?= $current_page ?>"/>

    <div class="pagination-button left">
        <button class="<?= ($prev_button_active) ? 'active ' : 'disabled ' ?>button sky pagination_btn_prev"
                data-link_url="<?= ($prev_button_active) ? $pagination_base_url . urlencode(($current_page - 1)) : '' ?>">
			<span class="outer">
				<span class="inner">&laquo; Prev</span>
			</span>
        </button>
    </div>

    <div class="pagination-buttons-area left">
        <?php
        for ($page = 1; $page <= $actual_pages_count; $page++) {
            echo '<a href="' . $pagination_base_url . urlencode($page) . '" class="pagination_page_link' . (($page == $current_page) ? ' current_page' : '') . '">' . $page . '</a>';
        }
        ?>
    </div>

    <div class="pagination-button right">
        <button class="<?= ($next_button_active) ? 'active ' : 'disabled ' ?>button sky pagination_btn_next"
                data-link_url="<?= ($next_button_active) ? $pagination_base_url . urlencode(($current_page + 1)) : '' ?>">
			<span class="outer">
				<span class="inner">Next &raquo;</span>
			</span>
        </button>
    </div>
</div>
<!-- /Pagination Nav -->