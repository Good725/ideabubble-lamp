<div class="text-center m-auto live-msg"  style="max-width: 470px;">
	<?php $full_url = URL::base().'event/'.$url; ?>

	<?php if ($payment_method_reminder): ?>
		<h2><?= __('Almost there!') ?></h2>
		<p>You just need to set up how you wish to be paid in <a href="/admin/profile/edit?section=contact">your profile settings</a>. Then your event will be visible online.</p>
	<?php else: ?>
		<h2><?= __("Congratulations! Your event is now live. Here is your link, now let's get selling! ") ?></h2>
	<?php endif; ?>
	<h3 class="my-2"><a href="/event/<?= $url ?>" id="preview-event-url"><?= $full_url ?></a></h3>

	<div>
		<div class="form-group">
			<button type="button" class="form-control btn text-uppercase" id="preview-event-copy-url" style="max-width: 336px;">Copy URL</button>
			<h2 class="mt-4" id="event_get_social_msg">Get Social..</h2>
		</div>
		<div class="form-group">
			<a
				href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($full_url) ?>"
				class="form-control btn"
				id="facebook-share-event"
				>Share on Facebook</a>
		</div>
		<div class="form-group">
			<a
				href="http://twitter.com/home/?status=<?= urlencode("New event posted\n".$full_url) ?>"
				class="form-control btn"
				id="tweet-published-event"
				>Tweet Event</a>
		</div>

		<?php $addthis_id = trim(Settings::instance()->get('addthis_id')); ?>

		<?php if ($addthis_id): ?>
			<div
				class="addthis_toolbox addthis_default_style addthis_32x32_style" style="height: 100px;"
				data-url="<?= $full_url ?>"
				addthis:url="<?= $full_url ?>"
				data-title="<?= $event['name'] ?>"
				addthis:title="<?= $event['name'] ?>"
				>
				<a href="https://www.addthis.com/bookmark.php?v=250&amp;username=ra-<?= $addthis_id ?>" class="addthis_button_compact">Share</a>
				<a class="addthis_button_preferred_1"></a>
				<a class="addthis_button_preferred_2"></a>
				<a class="addthis_button_preferred_3"></a>
				<a class="addthis_button_preferred_4"></a>
			</div>
			<script type="text/javascript" src="https://s7.addthis.com/js/250/addthis_widget.js#username=ra-<?= $addthis_id ?>"></script>
		<?php endif; ?>
	</div>

	<?php if ( ! $payment_method_reminder): ?>
		<div class="well" style="margin-top: 20px">
			<?php if ($account['iban'] === false && $account['bic'] == false && $account['stripe_auth'] === false) : ?>
				How would you like to receive payments for your event?<br/>
				You may change this at any time in <a href="/admin/profile/edit?section=contact">your profile settings</a>
			<?php else : ?>
				You have chosen to receive payments via: <strong>
					<?php if($account['use_stripe_connect'] == 1) : ?>
						<?=__('Stripe (as tickets are sold)')?>
					<?php else : ?>
						<?=__('Bank Transfer (1st working day after event)')?>
					<?php endif; ?>
				</strong><br/><br/>
				Please confirm this is correct<br/>
				You may change this at any time in <a href="/admin/profile/edit?section=contact">your profile settings</a>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</div>

<script>
	$('#facebook-share-event, #tweet-published-event').on('click', function(ev)
	{
		ev.preventDefault();
		var width  = 500;
		var height = 300;
		var left   = screen.width/2-250;
		var url    = this.href;

		window.open(url, 'newwindow', 'width='+width+', height='+height+', left='+left);
	});

	// Copy the URL when the button is clicked
	$('#preview-event-copy-url').on('click', function(ev)
	{
		'use strict';

		var link_node = document.getElementById('preview-event-url');
		var range     = document.createRange();
		range.selectNode(link_node);
		window.getSelection().addRange(range);

		try
		{
			if (document.execCommand('copy'))
			{
				alert('The text \"'+link_node.innerHTML+'\" has been copied to your clipboard');
			}
		}
		catch (error)
		{
			console.log(error);
		}

	});

</script>
<style>
	.at-icon-wrapper {
		border-radius: 50%;
	}
	.addthis_button_compact {
		font-size: 0;
	}
</style>
