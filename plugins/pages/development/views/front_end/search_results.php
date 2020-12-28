<?php require_once Kohana::find_file('template_views', 'header');?>

<div class="container">
    <div class="page-content search-results-wrapper">
        <?php if (empty($results)): ?>
            <div class="row">
                <h2><?= __('No results found') ?></h2>
                <p><?= __('Your search for $1 returned no results.', ['$1' => '<strong>'.html::entities(Request::$current->query('term')).'</strong>']) ?></p>
            </div>
        <?php else: ?>
            <?php foreach ($results as $result): ?>
                <div class="search-result p-3 border-bottom">
                    <a href="<?= $result->url ?>" class="text-decoration-none">
                        <h3 class="m-0 d-inline-block"><?= htmlspecialchars($result->search_title) ?></h3>
                    </a>
                    <div class="search-result-url">
                        <a href="<?= $result->url ?>" tabindex="-1"><?= trim(URL::site(), '/').$result->url ?></a>
                    </div>

                    <?php if ($result->is_content_match): ?>
                        <p class="m-0"><span class="search-result-content"><?= $result->get_matching_content($term) ?></span></p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once Kohana::find_file('views', 'footer'); ?>
