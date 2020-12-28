<div class="widget widget--organizers">
    <div class="widget-heading">
        <a href="<?= $organizer->get_url() ?>">
            <h3 class="widget-title"><?= __('Organiser Details') ?></h3>
        </a>
    </div>

    <div class="widget-body">
        <h2><?= $organizer->get_name() ?></h2>

        <?= View::factory('frontend/snippets/widget_contact_details')->set(array(
            'contact_text' => __('Contact Organiser'),
            'email'        => $organizer->email,
            'type'         => 'organizer',
            'website'      => $organizer->website,
        )) ?>

        <?= View::factory('frontend/snippets/widget_social_media')->set('social_media', $organizer->get_social_media()) ?>

        <p class="widget-view_more">
            <a href="<?= $organizer->get_url() ?>"><?= __('View More from Organiser') ?> →</a>
        </p>
    </div>

    <?php if (isset($other_organizers) && count($other_organizers)): ?>
        <div class="widget--organizers-other">
            <p><?= (count($other_organizers) == 1) ? __('Other Organiser') : __('Other Organisers') ?></p>

            <div class="clearfix">
                <?php foreach ($other_organizers as $other_organizer): ?>

                    <a class="widget--organizers-other-link<?= (count($other_organizers) > 3) ? ' fullwidth' : '' ?>" href="<?= $organizer->get_url ?>" title="<?= $organizer->get_name() ?>">
                        <img src="<?= $organizer->get_image() ?>" alt="<?= $organizer->get_name() ?>" />
                        <span class="widget--organizers-other-name"><?= $organizer->get_name() ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
    
    <?php
    $organizer_id = $organizer->contact_id;
    include Kohana::find_file('views', 'frontend/snippets/contact_organizer_modal');
    ?>
</div>