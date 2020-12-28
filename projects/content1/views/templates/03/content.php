<?php include 'template_views/header.php'; ?>

<?php $side_panels = $panel_model->get_panels('content_right', ($settings_instance->get('localisation_content_active') == '1')); ?>

<div class="row">
    <div class="column-content columns <?= count($side_panels) ? 'medium-7 large-8' : 'medium-12' ?>">
        <article>
            <section class="entry-content"><?= $page_data['content'] ?></section>
        </article>
    </div>

    <?php include 'template_views/content_sidebar.php'; ?>
</div>

<?php include 'template_views/footer.php'; ?>
