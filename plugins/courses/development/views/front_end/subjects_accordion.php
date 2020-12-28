<?php
// todo: create a setting
$button_type = Settings::instance()->get('assets_folder_path') == '51' ? 'read_more' : 'see_courses';
?>

<div class="row no-gutters d-md-flex course-subject-accordion-wrapper" style="--tab-count: <?= count($items)?>">
    <div class="col-sm-5">
        <ul class="list-unstyled m-0" id="course-subject-accordion">
            <?php foreach ($items as $i => $item): ?>
                <?php
                $name = $type == 'category' ? $item->category : $item->name;
                ?>

                <li class="course-subject-accordion-li border-left-category<?= ($i == 0) ? ' active' : '' ?>" style="--category-color: <?= isset($item->color) ? $item->color: '#ccc'; ?>">
                    <h6 class="m-0">
                        <button
                            type="button"
                            data-accordion-hide=".course-subject-accordion"
                            data-accordion-show=".course-subject-accordion-<?= $item->id ?>"
                            class="course-subject-accordion-toggle<?= ($i == 0) ? ' active' : '' ?>"
                            >
                            <?= htmlspecialchars($name) ?>
                        </button>
                    </h6>

                    <?php // mobile only ?>
                    <div class="hidden--tablet hidden--desktop course-subject-accordion course-subject-accordion-<?= $item->id ?> clearfix px-3 px-md-4 pb-4<?= ($i == 0) ? '' : ' hidden' ?>">
                        <?php if ($type == 'content'): ?>
                            <?= $item->render() ?>
                        <?php else: ?>
                            <?php $image = ($type == 'subject' && $item->image) || ($type == 'category' && $item->file_id) ? $item->get_image_url() : ''; ?>

                            <?php if ($image): ?>
                                <img class="w-100" src="<?= $image ?>" alt="" />
                            <?php endif; ?>

                            <h4 class="my-2"><?= htmlspecialchars($name) ?></h4>

                            <?php if ($item->summary): ?>
                                <p class="my-3"><?= nl2br(htmlspecialchars($item->summary)) ?></p>
                            <?php endif; ?>

                            <?php if ($button_type == 'read_more'): ?>
                                <a href="/course-list.html?<?= $type ?>=<?= $item->id ?>" class="read_more">See programmes</a>
                            <?php else: ?>
                                <a href="/course-list.html?<?= $type ?>=<?= $item->id ?>" class="button bg-white border-md-2 border-category text-category">See courses</a>
                            <?php endif; ?>

                        <?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>

    <?php // desktop/tablet only ?>
    <div class="col-sm-7 d-md-flex mb-2 hidden--mobile">
        <?php foreach ($items as $i => $item): ?>
            <?php
            $name = $type == 'category' ? $item->category : $item->name;
            ?>

            <div class="course-subject-accordion course-subject-accordion-<?= $item->id ?> w-100 p-4<?= ($i == 0) ? '' : ' hidden' ?>"<?= isset($item->color) ? ' style="--category-color: '.$item->color.'"' : '' ?>>
                <?php if ($type == 'content'): ?>
                    <?= $item->render() ?>
                <?php else: ?>
                    <?php $image = ($type == 'subject' && $item->image) || ($type == 'category' && $item->file_id) ? $item->get_image_url() : ''; ?>

                    <?php if ($image): ?>
                        <img src="<?= $image ?>" alt="" />
                    <?php endif; ?>

                    <h3 class="my-2"><?= htmlspecialchars($name) ?></h3>

                    <?php if ($item->summary): ?>
                        <p class="mt-2"><?= nl2br(htmlspecialchars($item->summary)) ?></p>
                    <?php endif; ?>

                    <?php if ($button_type == 'read_more'): ?>
                        <a href="/course-list.html?<?= $type ?>=<?= $item->id ?>" class="course-subject-accordion-button read_more">See programmes</a>
                    <?php else: ?>
                        <a href="/course-list.html?<?= $type ?>=<?= $item->id ?>" class="button course-subject-accordion-button bg-white border-2 border-category text-category">See courses</a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>