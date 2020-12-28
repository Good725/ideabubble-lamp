<?php if (!isset($slide['event']) || $slide['event'] === false): ?>
    <?php // Regular banner image ?>
    <div class="swiper-slide banner-slide banner-slide--<?= !empty($slide['overlay_position']) ? $slide['overlay_position'] : 'center' ?>">

        <?php
        $desktop_css = "background-image: url('".$slide['src']."');".(!empty($slide['max_height']) ? '--slide-height: '.$slide['max_height'].'px;' : '' );
        ?>

        <div class="banner">
            <?php if (!empty($slide['mobile_src'])): ?>
                <?php if ($slide['href']): ?>
                    <?php // Separate mobile and desktop images, each linked ?>
                    <a href="<?= $slide['href'] ?>" class="banner-image banner-image--mobile" style="background-image: url('<?= $slide['mobile_src'] ?>');"></a>
                    <a href="<?= $slide['href'] ?>" class="banner-image banner-image--desktop" style="<?= $desktop_css ?>"></a>
                <?php else: ?>
                    <?php // Separate mobile and desktop images, each unlinked ?>
                    <div class="banner-image banner-image--mobile" style="background-image: url('<?= $slide['mobile_src'] ?>');"></div>
                    <div class="banner-image banner-image--desktop" style="<?= $desktop_css ?>"></div>
                <?php endif; ?>

            <?php else: ?>

                <?php if ($slide['href']): ?>
                    <?php // Single image, linked ?>
                    <a href="<?= $slide['href'] ?>" class="banner-image" style="background-image: url('<?= $slide['src'] ?>');"></a>
                <?php else: ?>
                    <?php // Single image, unlinked ?>
                    <div class="banner-image" style="<?= $desktop_css ?>"></div>
                <?php endif; ?>
            <?php endif; ?>

            <?php if (!empty($slide['html_parsed'])): ?>
                <div class="banner-overlay<?= !empty($slide['html_is_sr_only']) ? ' sr-only' : ''?>">
                    <div class="row">
                        <div class="banner-overlay-content"><?= $slide['html_parsed'] ?></div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php else: ?>
    <?php // Event banner image ?>
        <div class="swiper-slide banner-slide banner-slide--columns banner-slide--left banner-slide--event">
            <div class="banner-slide-backdrop" style="background-image: url('<?= $slide['event']->get_image() ?>')"></div>

            <div class="banner row">
                <div class="row no-gutters">
                    <div class="col-sm-6">
                        <figure class="clearfix">
                            <img src="<?= $slide['image'] ?>" alt="" class="banner-column-image hidden--mobile" />

                            <figcaption>
                                <div>
                                    <?php $organizer = $slide['event']->get_primary_organizer(); ?>
                                    <?php if ($organizer->contact_id): ?>
                                        <a class="button button--enquire"
                                           href="<?= $organizer->get_url() ?>"
                                           title="<?= $organizer->get_name() ?>"
                                        >
                                            <?= __('Organiser') ?>
                                        </a>
                                    <?php endif; ?>

                                    <?php if ($slide['event']->venue->id): ?>
                                        <a class="button button--enquire"
                                           href="<?= $slide['event']->venue->get_url() ?>"
                                           title="<?= $slide['event']->venue->name ?>"
                                            >
                                            <?= __('Venue') ?>
                                        </a>
                                    <?php endif; ?>
                                </div>

                                <a href="<?= $slide['event']->get_url() ?>">
                                    <h2><?= htmlentities($slide['event']->name) ?></h2>
                                </a>

                                <div class="banner-event-detail row gutters">
                                    <div class="col-xs-2 col-sm-1">
                                        <span class="banner-event-detail-icon flaticon-location-pin"></span>
                                    </div>

                                    <div class="col-xs-10 col-sm-11">
                                        <strong><?= $slide['event']->venue->name ?></strong><br />
                                        <small><?= $slide['event']->venue->city ?></small>
                                    </div>
                                </div>


                                <div class="banner-event-detail row gutters">
                                    <div class="col-xs-2 col-sm-1">
                                        <span class="banner-event-detail-icon flaticon-calendar"></span>
                                    </div>

                                    <div class="col-xs-10 col-sm-11">
                                        <strong><?= date('l jS F Y', strtotime($slide['start_date'])) ?></strong><br />
                                        <small><?= date('H:i', strtotime($slide['start_date'])) ?></small>
                                    </div>
                                </div>
                            </figcaption>
                        </figure>

                    </div>

                    <div class="col-sm-6 text-center hidden--mobile">
                        <?php $videos = $slide['event']->get_videos(array('youtube', 'vimeo')); ?>

                        <?php if (isset($videos[0]) && !empty($videos[0]['embed_url'])): ?>
                            <div class="video-wrapper" data-provider="<?= $videos[0]['provider'] ?>">
                                <iframe src="<?= $videos[0]['embed_url'] ?>" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>

                                <button type="button" class="video-cover">
                                    <span class="sr-only"><?= __('Show video') ?></span>
                                    <span class="flaticon-play"></span>
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
<?php endif; ?>