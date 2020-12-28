<div class="widget widget--venue">
    <div class="widget-heading">
        <a href="<?= $venue->get_url() ?>">
            <h3 class="widget-title text-primary"><?= __('Venue Details') ?></h3>
        </a>
    </div>
    
    <div class="widget-body">
        <h2>
            <span class="line"><?= $venue->name ?></span>

            <?php if (trim($venue->address_1)): ?>
                <span class="line"><?= $venue->address_1 ?></span>
            <?php endif; ?>

            <?php if (trim($venue->address_2)): ?>
                <span class="line"><?= $venue->address_2 ?></span>
            <?php endif; ?>

            <?php if (trim($venue->city)): ?>
                <span class="line"><?= $venue->city ?></span>
            <?php endif; ?>
        </h2>

        <div class="row widget-contact_details">
            <?= View::factory('frontend/snippets/widget_contact_details')->set(array(
                'contact_text' => __('Contact Venue'),
                'email'        => $venue->email,
                'type'         => 'venue',
                'website'      => $venue->website,
            )) ?>
        </div>

        <?= View::factory('frontend/snippets/widget_social_media')->set('social_media', $venue->get_social_media()) ?>

        <p class="widget-view_more">
            <a href="/venue/<?= $venue->url ?>"><?= __('View More from Venue') ?> →</a>
        </p>
    </div>

    <?php
    $venue_id = $venue->id;
    include Kohana::find_file('views', 'frontend/snippets/contact_venue_modal');
    ?>
</div>