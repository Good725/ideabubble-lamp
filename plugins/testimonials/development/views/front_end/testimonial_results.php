<?php
$total            = isset($total) ? $total : count($testimonials);
$items_per_page   = (int) Settings::instance()->get('testimonials_feed_item_count');
$items_per_page   = $items_per_page ? $items_per_page : 3;
$pagination_count = ceil($total / $items_per_page);
$current_page     = isset($current_page) ? $current_page : 1;
?>

<?php for ($i = 0; $i < $items_per_page && $i < count($testimonials); $i++): ?>
    <?php $testimonial = is_array($testimonials[$i]) ? new Model_Testimonial($testimonials[$i]['id']) : $testimonials[$i]; ?>

    <div class="testimonial-block" id="testimonial-<?= $testimonial->id ?>">
        <div class="testimonial-block-content-wrapper">
            <blockquote class="testimonial-content"><?= $testimonial->content ?></blockquote>

            <?php $type = $testimonial->course->type; ?>

            <?php if ($type->type): ?>
                <div class="testimonial-course-type"
                    <?= $type->color ? ' style="color: '.htmlspecialchars($type->color).';"' : '' ?>
                >
                    <?= htmlspecialchars($type->type) ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="testimonial-footer">
            <?php if ($testimonial->image): ?>
                <div class="testimonial-image">
                    <img src="<?= $testimonial->get_image_url() ?>" alt="" />
                </div>
            <?php endif; ?>

            <div>
                <?php if (trim($testimonial->item_signature)): ?>
                    <cite class="testimonial-signature"><?= htmlentities($testimonial->item_signature) ?></cite>
                <?php endif; ?>

                <?php if (trim($testimonial->item_position)): ?>
                    <p class="testimonial-position"><?= htmlentities($testimonial->item_position) ?></p>
                <?php endif; ?>

                <?php if (trim($testimonial->item_company)): ?>
                    <p class="testimonial-company"><?= htmlentities($testimonial->item_company) ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php endfor; ?>

<?php if ($pagination_count > 1): ?>
    <?= View::factory('front_end/pagination')->set([
        'current_page' => $current_page,
        'total_pages' => $pagination_count
    ]) ?>
<?php endif; ?>
