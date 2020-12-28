<?php if (!empty($testimonials)): ?>
    <div id="testimonials-section bg-category">
        <div class="testimonials-slider swiper-container" id="testimonials-slider" data-autoplay="5000" data-direction="horizontal" data-effect="slide" data-slides="<?= count($testimonials) ?>" data-speed="300">
            <div class="swiper-wrapper">
                <?php foreach ($testimonials as $testimonial): ?>
                    <div class="swiper-slide">
                        <div class="p-5">
                            <div class="testimonial-slider-quote"><?= file_get_contents(ENGINEPATH.'plugins/testimonials/development/assets/img/quote_open.svg') ?></div>

                            <div class="testimonials-slider-content">
                                <?= $testimonial->content ?>
                            </div>
                        </div>

                        <div class="d-flex align-items-center px-4 mb-2">
                            <?php if ($testimonial->image): ?>
                                <div class="mr-2">
                                    <img src="<?= $testimonial->get_image_url() ?>" alt="" />
                                    <div></div>
                                </div>
                            <?php endif; ?>

                            <div>
                                <h6 class="mt-0 mb-2"><?= htmlspecialchars($testimonial->item_signature) ?></h6>

                                <p class="my-2" style="font-size: 12px;"><?= htmlspecialchars($testimonial->item_company) ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <?php if (count($testimonials) > 1): ?>
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
                <div class="swiper-pagination"></div>
            <?php endif; ?>
        </div>
    </div>
<?php else: ?>
    <!-- No testimonials -->
<?php endif; ?>
