<?php include 'template_views/header.php'; ?>

<?php
$side_panels = $panel_model->get_panels('content_right', ($settings_instance->get('localisation_content_active') == '1'));
$testimonials = Model_Testimonials::get_all_items_front_end(null, 'testimonials');
?>

<div class="row">
    <div class="column-content columns <?= count($side_panels) ? 'medium-7 large-8' : 'medium-12' ?>">
        <article>
            <section class="entry-content"><?= $page_data['content'] ?></section>
        </article>

        <section id="testimonials-section">
            <?php if (count($testimonials)): ?>
                <?php foreach($testimonials as $testimonial): ?>
                    <div class="testimonial-block">
                        <div class="panel testimonial-panel"><?= $testimonial['content'] ?></div>

                        <h4><?= htmlentities($testimonial['item_signature']) ?></h4>

                        <p><?= htmlentities($testimonial['item_company']) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>
    </div>

    <?php include 'template_views/content_sidebar.php'; ?>
</div>

<?php include 'template_views/footer.php'; ?>
