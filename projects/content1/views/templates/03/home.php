<?php include 'template_views/header.php'; ?>

<?php $feature_panels = $panel_model->get_panels('home_content', ($settings_instance->get('localisation_content_active') == '1')); ?>
<?php if (count($feature_panels)): ?>
    <section class="gray-band">
        <div class="featured-services">
            <div class="row">
                <div class="small-12 columns">
                    <ul class="small-block-grid-2 medium-block-grid-4 large-block-grid-4 text-center">
                        <?php foreach ($feature_panels as $feature): ?>
                            <li class="feature-block">
                                <a<?= $feature['link_url'] ? ' href="'.$feature['link_url'].'"' : '' ?>>
                                    <div class="feature-block-icon">
                                        <img height="150" src="<?= $media_path ?>panels/<?= $feature['image'] ?>" class="attachment-thumbnail size-thumbnail" alt="" />
                                    </div>

                                    <h3><?= $feature['title'] ?></h3>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </section>
<?php endif; ?>

<div class="row">
    <div class="columns medium-8 large-9">
        <article>
            <section class="entry-content"><?= $page_data['content'] ?></section>
        </article>
    </div>

    <?php $testimonials = Model_Testimonials::get_all_items_front_end(null, 'testimonials'); ?>

    <?php if (isset($testimonials[0])): ?>
        <?php $testimonial = $testimonials[0]; ?>
        <div class="columns medium-4 large-3">
            <div class="testimonial-block">
                <div class="panel testimonial-panel"><i><?= $testimonial['summary'] ?></i></div>

                <h4><?= $testimonial['item_signature'] ?></h4>

                <p><?= $testimonial['item_company'] ?></p>

                <a class="button" href="/testimonials"><?= __('See more testimonials') ?></a>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'template_views/footer.php'; ?>
