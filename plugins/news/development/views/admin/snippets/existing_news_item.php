<div class="edit-news-existing_item">
    <span class="edit-news-existing_item-title"><?= isset($existing_item['title']) ? $existing_item['title'] : '' ?></span>
    <a
        class="edit-news-existing_item-link"
        href="/admin/news/add_edit_item/<?= isset($existing_item['id']) ? $existing_item['id'] : '' ?>"
        >view article</a>
</div>