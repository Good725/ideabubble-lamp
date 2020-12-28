<div
    class="testimonial-embed fullwidth"
    <?php if ($testimonial->banner_image): ?>
        style="background-image: url('<?= $testimonial->get_banner_image_url() ?>')"
    <?php endif; ?>
>
    <div class="container d-md-flex">
        <div class="testimonial-embed-text">
            <div class="testimonial-embed-content">
                <?= $testimonial->content ?>
            </div>

            <div class="testimonial-embed-signature">
                <strong><?= htmlspecialchars($testimonial->item_signature) ?></strong>

                <div><?= htmlspecialchars($testimonial->item_position) ?></div>
                <div><?= htmlspecialchars($testimonial->item_company) ?></div>
            </div>
        </div>

        <div class="testimonial-embed-more d-flex">
            <a class="mt-4 mt-sm-auto ml-sm-auto testimonial-embed-read_more" href="/testimonials">
                <strong>See all testimonials</strong>
            </a>
        </div>
    </div>
</div>

