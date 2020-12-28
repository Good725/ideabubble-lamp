<?php if (!empty($testimonials)): ?>
    <div id="testimonials-section">
        <div class="swiper-container" id="testimonials-slider" data-autoplay="5000" data-direction="horizontal" data-effect="slide" data-slides="<?= count($testimonials) ?>" data-speed="300">
            <div class="swiper-wrapper">
                <?php foreach ($testimonials as $testimonial): ?>
                    <div class="swiper-slide">
                        <div class="row gutters">
                            <?php if ($testimonial['image']): ?>
                                <div class="col-sm-3 text-center">
                                    <img class="testimonials-slider-image" src="<?= Model_Media::get_image_path($testimonial['image'], 'testimonials') ?>" alt="" />
                                </div>
                            <?php endif; ?>

                            <div class="col-sm-<?= ($testimonial['image']) ? '9' : '12' ?>">
                                <div class="testimonials-slider-testimonial">
                                    <?= $testimonial['summary'] ? '<p>'.nl2br(htmlentities($testimonial['summary'])).'</p>' : $testimonial['content']; ?>
                                </div>

                                <p><cite class="testimonials-slider-signature"><?= htmlentities($testimonial['item_signature']) ?></cite></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="swiper-pagination"></div>
        </div>
    </div>
<?php endif; ?>
