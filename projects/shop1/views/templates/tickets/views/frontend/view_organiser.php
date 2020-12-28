<?php require_once Kohana::find_file('template_views', 'header'); ?>
<?php if ($organiser == null): ?>
    <div class="row row--event">
        <p><?= __('No organiser found.') ?></p>
    </div>
<?php else: ?>
    <div class="row row--event organiser--event">
        <div class="widget banner-outer org-banner">
            <?php if ($organiser['banner_media_id'] > 0) : ?>
                <img src="<?=$organiser['banner_media_url']?>" alt="" />
            <?php else :?>
				<img src="<?=$organiser['profile_media_url']?>" alt="" />
			<?php endif; ?>

            <?php if ($organiser['profile_media_id'] > 0 && $organiser['banner_media_id'] > 0): ?>
                <img class="org_profile" src="<?=$organiser['profile_media_url']?>" alt="" width="100" height="100"/>
            <?php endif; ?>
        </div>

        <div class="widget">
			<div class="widget-body text-center">
				<h5 class="text-secondary sub-heading"><?= trim($organiser['first_name'] . ' ' . $organiser['last_name']) ?></h5>

				<address>
					<span class="line"><?= $organiser['address1'] ?></span>
					<span class="line"><?= $organiser['address2'] ?></span>
					<span class="line"><?= $organiser['address3'] ?></span>
					<span class="line"><?= $organiser['address4'] ?></span>
				</address>
				<?php $has_website = (isset($organiser['website']) && $organiser['website'] != '') ?>
				<?php $has_email = (isset($organiser['email']) && $organiser['email'] != ''); ?>
				<div class="row widget-contact_details text-center">
				    <?php if ($has_email): ?>
						<span class="widget-contact_details-item">
							<button class="button--plain" data-open="modal--contact_organizer">
								<span class="flaticon-envelope"></span>
								<span><?= __('Contact Organiser') ?></span>
							</button>
						</span>
					<?php endif; ?>
					<?php if ($has_website): ?>
						<span class="widget-contact_details-item">
							<?php if(!empty($organiser['website'])) { ?>
								<span class="flaticon-domain"></span>
								<?php $link = (!preg_match('#^(http|https)://#i', $organiser['website']) ? 'http://' : '') . $organiser['website'] ?>
								<span><a href="<?= $link ?>" target="_blank">Website</a></span>
							<?php } ?>
						</span>
					<?php endif; ?>
				</div>
				<ul class="social_media-list">
					<?php if ($organiser['twitter']): ?>
						<li>
							<a target="_blank" href="http://twitter.com/<?= $organiser['twitter'] ?>" title="<?= __('Twitter') ?>">
								<span class="flaticon-twitter"></span>
							</a>
						</li>
					<?php endif; ?>
					<?php if ($organiser['facebook']): ?>
						<li>
							<a target="_blank" href="http://facebook.com/<?= $organiser['facebook'] ?>" title="<?= __('Facebook') ?>">
								<span class="flaticon-facebook"></span>
							</a>
						</li>
					<?php endif; ?>
					<?php if ( ! empty($organiser['instagram'])): ?>
						<li>
							<a target="_blank" href="http://instagram.com/<?= $organiser['instagram'] ?>" title="<?= __('Instagram') ?>">
								<span class="flaticon-instagram"></span>
							</a>
						</li>
					<?php endif; ?>
					<?php if ( ! empty($organiser['snapchat'])): ?>
						<li>
							<a target="_blank" href="http://snapchat.com/add/<?= $organiser['snapchat'] ?>" title="<?= __('Snapchat') ?>">
								<span class="flaticon-snapchat"></span>
							</a>
						</li>
					<?php endif; ?>
				</ul>
			</div>
		</div>
    </div>

	<?php if ( ! empty($events)): ?>
		<div class="row row--event">
			<h3 class="text-primary text-center upcoming-events-heading"><?= __('Upcoming Events') ?></h3>
		</div>

		<div class="row row--event organiser--event">
			<div id="organizer-events-list">
				<?php foreach ($events as $event) include 'snippets/event_widget.php'; ?>
			</div>

            <?php if ($total_events > $results_per_page): ?>
                <div class="text-center">
                    <button
                        type="button"
                        class="button large button--more_events"
                        data-offset="<?= $results_per_page ?>"
                        data-type="contact"
                        data-feed="#organizer-events-list"
                        data-id="<?= $organiser['contact_id'] ?>"
                        ><?= __('See More Events')?></button>
                </div>
            <?php endif; ?>
		</div>
    <?php endif; ?>

        <div class="reveal" id="modal--contact_organizer" data-reveal aria-labelledby="modal--contact_organizer--title" aria-hidden="true" role="dialog">
            <h3 class="text-primary" id="modal--contact_organizer--title"><?= __('Contact Organiser') ?></h3>
            <button type="button" class="close-button" data-close aria-label="<?= __('Close') ?>"><span aria-hidden="true">&#215;</span></button>
			<form action="/frontend/events/contact" method="post" class="validate-on-submit">
				<input type="hidden" name="organiser_id" value="<?=$organiser['id']?>" />

				<div class="row">
					<label class="columns small-4" for="modal--contact_organizer-name">
						<?= __('Name') ?>
					</label>
					<div class="columns small-8">
						<input type="text" class="form_field" id="modal--contact_organizer-name" name="name" />
					</div>
				</div>

				<div class="row">
					<label class="columns small-4" for="modal--contact_organizer-email">
						<?= __('Email') ?>
					</label>
					<div class="columns small-8">
						<input type="text" class="form_field validate[required,custom[email]]" id="modal--contact_organizer-email" name="email" />
					</div>
				</div>

				<div class="row">
					<label class="columns small-4" for="modal--contact_organizer-telephone">
						<?= __('Telephone') ?>
					</label>
					<div class="columns small-8">
						<input type="text" class="form_field" id="modal--contact_organizer-email" name="telephone" />
					</div>
				</div>

				<div class="row">
					<label class="columns small-4" for="modal--contact_organizer-message">
						<?= __('Message') ?>
					</label>
					<div class="columns small-8">
						<textarea class="form_field" id="modal--contact_organizer-message" name="message" rows="4"></textarea>
					</div>
				</div>

				<div class="row">
					<div class="columns small-offset-2 small-8">
						<button type="submit" class="button button--full secondary"><?= __('Send') ?></button>
					</div>
				</div>
            </form>
        </div>
<?php endif; ?>
<?php require_once Kohana::find_file('template_views', 'footer'); ?>
