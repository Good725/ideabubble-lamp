<?php include 'template_views/html_document_header.php'; ?>
<body id="<?=$page_data['layout']?>" class="<?=$page_data['category']?> layout-<?= $page_data['layout'] ?>">
<div id="wrap">
    <div id="container">
        <?php include 'header.php' ?>
        <div id="main">
            <div id="sideLt">
                <?php
                $categories = ORM::factory('News_Category')->order_by('order')->find_all_published();
                $recent_posts = ORM::factory('News_Item')
                    ->where('date_publish', '<=', date('Y-m-d H:i:s'))
                    ->where('date_remove',  '>',  date('Y-m-d H:i:s'))
                    ->order_by(DB::expr("ifnull(`event_date`, `date_created`)"), 'desc')
                    ->order_by('date_created', 'desc')
                    ->limit(5)->find_all_published();
                ?>

                <?php if ($categories OR $recent_posts): ?>
                    <div class="sidebar-news">
                        <?php if (count($categories)): ?>
                            <div class="sidebar-news-categories">
                                <h2><?= __('Categories') ?></h2>
                                <ul>
                                    <?php foreach ($categories as $category): ?>
                                        <li>
                                            <a href="/<?= $page_data['name_tag'].'/'.$category->category ?>">
                                                <?= $category->category ?>
                                            </a>
                                        </li>
                                    <?php endforeach ;?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <?php if (count($recent_posts)): ?>
                            <?php
                            $media_folder = (Kohana::$config->load('config')->project_media_folder);
                            $media_path = '/shared_media/'.$media_folder.'/media/photos/news/';
                            $code_media_path = PROJECTPATH.'www/shared_media/'.$media_folder.'/media/photos/news/';
                            ?>
                            <div class="sidebar-news-recent">
                                <h2><?= __('Recent Posts') ?></h2>
                                <ul>
                                    <?php foreach ($recent_posts as $recent_post): ?>
                                        <?php $link = $page_data['name_tag'].'/'.$recent_post->category->category.'/'.$recent_post->title; ?>
                                        <li class="sidebar-news-recent-item">
                                            <?php if ($recent_post->image): ?>
                                                <?php
                                                $image_path = $media_path.(file_exists($code_media_path.'_thumbs/'.$recent_post->image) ? '_thumbs/' : '').$recent_post->image;
                                                ?>

                                                <a href="/<?= $link ?>" tabindex="-1" class="sidebar-news-image">
                                                    <img src="<?= $image_path ?>" alt="<?= $recent_post->alt_text ?>" />
                                                </a>
                                            <?php endif; ?>

                                            <div class="sidebar-news-recent-item-details">
                                                <p><?= $recent_post->seo_title ? htmlentities($recent_post->seo_title) : $recent_post->title ?></p>
                                                <a href="/<?= $link ?>" class="news-read_more"><?= __('Read more') ?></a>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div id="ct">
                <div id="banner"><?= Model_PageBanner::render_frontend_banners($page_data['banner_photo']); ?></div>

                <div id="ct_left" class="column">
                    <div class="content"><?= $page_data['content'] ?></div>

                    <div class="content news-content">
                        <?= Model_News::get_plugin_items_front_end_list($page_data['current_item_identifier'], $page_data['current_item_category']) ?>
						<?php if (trim($page_data['footer'])): ?>
							<div class="page-footer"><?= $page_data['footer'] ?></div>
						<?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div id="footer">
            <?php include 'footer.php' ?>
        </div>
    </div>
</div>
<?= Settings::instance()->get('footer_html'); ?>
</body>
</html>