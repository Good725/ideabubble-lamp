3213213131321
<?php
$feed = new Model_Feeds;
$display = $feed->display_feed(__FILE__);
echo '<pre>';
print_r($feed_items);
if ( ! empty($feed_items)): ?>
	<section class="full-row feedback-column">
		<div class="fix-container">
			<div class="swiper-container" id="courses-carousel">
				<ul class="swiper-wrapper">
					<?php foreach ($feed_items as $slide): ?>
							<li class="swiper-slide">
								<div class="panel-body">
									<p><?= $slide['content'];?> </p>
								</div>
								<div class="author-name">
									<?php
									echo $slide['item_signature'];

									if(!empty($slide['item_company']))
									{
										echo ' ,'.$slide['item_company'];
									}
									if(!empty($slide['item_website']))
									{
										echo ' ,'.$slide['item_website'];
									}
									?>
								</div>
							</li>
					<?php endforeach;?>
				</ul>
			</div>

			<?php if ( ! empty($feed_items)): ?>
					<div class="swiper-button-prev" id="courses-carousel-prev">4</div>
					<div class="swiper-button-next" id="courses-carousel-next">5</div>
			<?php endif;?>

		</div>
	</section>
<?php endif;?>

