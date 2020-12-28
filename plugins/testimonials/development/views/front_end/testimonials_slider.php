<?php if (!empty($testimonials)): ?>
<div class="bg-light fullwidth py-4 py-md-5">
    <h2 class="mx-3 mx-md-0 mt-md-5 text-center">What do <?= Settings::instance()->get('company_title') ?> clients think?</h2>

    <div id="testimonials-section">
        <div class="testimonials-slider swiper-container p-3 p-md-5" id="testimonials-slider" data-autoplay="5000" data-direction="horizontal" data-effect="slide" data-slides="<?= count($testimonials) ?>" data-speed="300">
            <div class="swiper-wrapper">
                <?php foreach ($testimonials as $testimonial): ?>
                    <div class="swiper-slide">
                        <div class="bg-white row gutters vertically_center mx-auto mb-5 mb-md-0 p-3 p-md-0">
                            <div class="col-sm-8 col-md-7 p-0 px-md-5 py-md-4">

                                <div class="testimonial-slider-quote"><?= file_get_contents(ENGINEPATH.'plugins/testimonials/development/assets/img/quote_open.svg') ?></div>

                                <div class="testimonials-slider-content">
                                    <?= $testimonial->content ?>
                                </div>

                                <div class="testimonial-slider-quote text-right"><?= file_get_contents(ENGINEPATH.'plugins/testimonials/development/assets/img/quote_close.svg') ?></div>
                            </div>

                            <div class="col-sm-4 col-md-5 m-auto p-4 text-center">
                                <div>
                                    <?php if ($testimonial->image): ?>
                                        <div>
                                            <img src="<?= $testimonial->get_image_url() ?>" alt="" />
                                        </div>
                                    <?php endif; ?>

                                    <div>
                                        <h3 class="hidden--tablet hidden--desktop mt-0 mb-2"><?= htmlspecialchars($testimonial->item_signature) ?></h3>
                                        <h6 class="hidden--mobile mt-0 mb-2"><?= htmlspecialchars($testimonial->item_signature) ?></h6>

                                        <p class="my-2" style="font-size: 14px;"><?= str_replace(',', ',<br />', htmlspecialchars($testimonial->item_company)) ?></p>
                                    </div>
                                </div>
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
