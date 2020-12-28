/*
ts:2020-05-12 18:03:00
*/

/** Campaign **/

DELIMITER ;;

-- Insert the "campaign" page, if it doesn't already exist
INSERT IGNORE INTO `plugin_pages_pages` (`name_tag`, `title`, `content`,`date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`)
SELECT
  'campaign',
  'Campaign',
  '<h1>Campaign</h1>',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '1',
  '1',
  '0',
  '1',
  (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'content_wide' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'DEFAULT' LIMIT 1)
FROM `plugin_pages_pages`
WHERE NOT EXISTS (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` IN ('campaign', 'campaign.html') AND `deleted` = 0)
LIMIT 1;;

-- Update the page
UPDATE
  `plugin_pages_pages`
SET
  `name_tag`      = 'campaign',
  `title`         = 'Campaign',
  `layout_id`     = (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'content_wide' AND `deleted` = 0 LIMIT 1),
  `last_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = 1,
  `content`       = '<div class="page-intro">
	<h2>Page Text Box</h2>

	<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Habitasse platea dictumst vestibulum rhoncus est pellentesque elit ullamcorper. Suspendisse ultrices gravida dictum fusce ut placerat orci. At consectetur lorem donec massa. Bibendum arcu vitae elementum curabitur vitae nunc sed. Adipiscing elit ut aliquam purus sit amet luctus venenatis lectus. Lorem ipsum dolor sit amet consectetur adipiscing elit pellentesque. Enim nec dui.</p>

	<p>Suspendisse ultrices gravida dictum fusce ut placerat orci. At consectetur lorem donec massa. Bibendum arcu vitae elementum curabitur vitae nunc sed. Adipiscing elit ut aliquam purus sit amet luctus venenatis lectus. Lorem ipsum dolor sit amet consectetur adipiscing elit pellentesque. Adipiscing elit ut aliquam purus sit amet luctus venenatis lectus.</p>

	<p>Lorem ipsum dolor sit amet consectetur adipiscing elit pellentesque. Enim nec dui. Adipiscing elit ut aliquam purus sit amet luctus venenatis lectus. Lorem ipsum dolor sit amet consectetur adipiscing elit pellentesque. Enim nec dui. Adipiscing elit ut aliquam purus sit amet luctus venenatis lectus.</p>

	<p><a class="read_more-lg" href="/contact-us">Contact us</a></p>
</div>

<div class="bg-lighter simplebox mb-0" style="margin-top: 59px; border-top: 1px solid transparent; padding-top: 15px;">
	<div class="simplebox-title">
		<h2 style="font-size: 35px; margin-bottom: 49px;">Programme categories</h2>
	</div>

	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<p>{course_categories-}</p>
			</div>
		</div>
	</div>
</div>

<div class="simplebox additional_features">
	<div class="simplebox-title">
		<h2>Additional Features</h2>
	</div>

	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<h3>Psychometric Testing</h3>
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Habitasse platea dictumst vestibulum rhoncus est pellentesque elit ullam bulia formas nu.</p>
				<p style="text-align: right; font-size: 18px;"><a class="read_more" href="/coming-soon">More info</a></p>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<h3>Facilitation of Teams</h3>
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Habitasse platea dictumst vestibulum rhoncus est pellentesque elit ullam bulia formas nu.</p>
				<p style="text-align: right; font-size: 18px;"><a class="read_more" href="/coming-soon">More info</a></p>
			</div>
		</div>
	</div>
</div>

<div class="simplebox additional_features">
	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<h3>Strategy Work and Training</h3>
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Habitasse platea dictumst vestibulum rhoncus est pellentesque elit ullam bulia formas nu.</p>
				<p style="text-align: right; font-size: 18px;"><a class="read_more" href="/coming-soon">More info</a></p>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<h3>Coaching to Embed the Learning</h3>
				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Habitasse platea dictumst vestibulum rhoncus est pellentesque elit ullam bulia formas nu.</p>
				<p style="text-align: right; font-size: 18px;"><a class="read_more" href="/coming-soon">More info</a></p>
			</div>
		</div>
	</div>
</div>

<div>{testimonial-Rosderra}</div>

<div class="simplebox simplebox-align-top simplebox-video">
	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1" style="width: 32.6%;">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<h2>Video</h2>

				<p style="line-height: 1.25;">Description of video. Lorem ipsum dolor sit amet, consectetur. Lorem ipsum dolor sit amet, consectetur. Lorem ipsum dolor sit amet, consectetur. Lorem ipsum dolor sit amet, consectetur</p>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2" style="width: 67.4%;">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<div>{video-NycTraffic.mp4}</div>
			</div>
		</div>
	</div>
</div>

<div>{our_clients-}</div>

<div class="bg-light simplebox simplebox-equal-heights our_approach" style="border-top: 1px solid transparent;">
	<div class="simplebox-title">
		<h2>Our Approach</h2>
	</div>

	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<h2>Discovery</h2>
				<p>Understand your needs and specific goals to build your values, culture and people</p>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<h2>Design</h2>
				<p>Design a practical programme with relevant applied learning</p>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-3">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<h2>Delivery</h2>
				<p>Match the right trainer to your organisation</p>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-4">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<h2>Debrief and Impact</h2>
				<p>Pre &amp; post course work to embed the learning </p>
			</div>
		</div>
	</div>
</div>

<div class="bg-primary simplebox simplebox-primary">
	<div class="simplebox-columns" style="max-width: 1136px;">
		<div class="simplebox-column simplebox-column-1 hidden\-\-mobile">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<div style="text-align: center;"><img alt="TU Dublin (Technological University Dublin, Ollscoil Teichneolaíochta Bhaile Átha Cliath)" src="/shared_media/ibec/media/photos/content/TUD_white.png" style="height: 197px; width: 312px;" /></div>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2 accredited-partner-text">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<h2 style="font-size: 35px;">Accredited Partner</h2>

				<p style="font-size: 18px; line-height: 23px;">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Habitasse platea dictumst vestibulum rhoncus est pellentesque elit ullam bulia formas nu.</p>
				<p><a href="#" class="read_more-lg text-white"><strong>Read more about our partnership</strong></a></p>

				<div class="hidden\-\-desktop hidden\-\-tablet"><img alt="TU Dublin (Technological University Dublin, Ollscoil Teichneolaíochta Bhaile Átha Cliath)" src="/shared_media/ibec/media/photos/content/TUD_white.png" style="height: 197px; width: 312px;" /></div>
			</div>
		</div>
	</div>
</div>

<div class="bg-lighter simplebox simplebox-featured">
	<div class="simplebox-title hidden\-\-tablet hidden\-\-desktop">
		<h2>Featured Seminar</h2>
	</div>

	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<div class="hidden\-\-desktop hidden\-\-tablet"><img alt="" src="/shared_media/ibec/media/photos/content/featured-seminar.jpg" style="height: 400px; width: 435px;" /></div>

				<h1 class="hidden\-\-mobile">Featured Seminar</h1>
				<h3>Seminar date and info</h3>

				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip.</p>
				<p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Duis aute irure dolor.

				<p style="font-size: 18px;"><a class="read_more-lg-arrow" href="/coming-soon"><strong>Call to action link</strong></a></p>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2 hidden\-\-mobile">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<div><img alt="" src="/shared_media/ibec/media/photos/content/featured-seminar.jpg" style="float:right; height: 400px; width: 435px;" /></div>
			</div>
		</div>
	</div>
</div>
\n
\n<p>{download_brochure-}</p>
\n
\n<div class="bg-lighter fullwidth">{news_category-Emerging Trends-Description of emerging trends}</div>
\n
\n<p>{spotlights-}</p>
\n
\n<p>{get_started-}</p>
\n
<h2 style="font-size: 35px; margin-top: 38px; margin-bottom: 34px;">Questions? <span class="text-primary" style="font-weight: normal;">/ Read our FAQs</span></h2>

<ul class="accordion-basic">
	<li>
		<h3 class="active">Question text for panel 1?</h3>

		<p> Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Amet porttitor eget dolor morbi non arcu risus. Egestas quis ipsum suspendisse ultrices gravida. Vitae purus faucibus ornare suspendisse sed nisi lacus sed. Ut placerat orci nulla pellentesque. Vivamus arcu felis bibendum ut. Pellentesque diam volutpat commodo sed egestas. Nibh ipsum consequat nisl vel pretium lectus quam id leo. Tortor id aliquet lectus proin. Eros in cursus turpis massa tincidunt dui ut.</p>
	</li>

	<li>
		<h3>Question text for panel 2?</h3>

		<p> Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Amet porttitor eget dolor morbi non arcu risus. Egestas quis ipsum suspendisse ultrices gravida. Vitae purus faucibus ornare suspendisse sed nisi lacus sed. Ut placerat orci nulla pellentesque. Vivamus arcu felis bibendum ut. Pellentesque diam volutpat commodo sed egestas. Nibh ipsum consequat nisl vel pretium lectus quam id leo. Tortor id aliquet lectus proin. Eros in cursus turpis massa tincidunt dui ut.</p>
	</li>

	<li>
		<h3>Question text for panel 3?</h3>

		<p> Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Amet porttitor eget dolor morbi non arcu risus. Egestas quis ipsum suspendisse ultrices gravida. Vitae purus faucibus ornare suspendisse sed nisi lacus sed. Ut placerat orci nulla pellentesque. Vivamus arcu felis bibendum ut. Pellentesque diam volutpat commodo sed egestas. Nibh ipsum consequat nisl vel pretium lectus quam id leo. Tortor id aliquet lectus proin. Eros in cursus turpis massa tincidunt dui ut.</p>
	</li>

	<li>
		<h3>Question text for panel 4?</h3>

		<p> Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Amet porttitor eget dolor morbi non arcu risus. Egestas quis ipsum suspendisse ultrices gravida. Vitae purus faucibus ornare suspendisse sed nisi lacus sed. Ut placerat orci nulla pellentesque. Vivamus arcu felis bibendum ut. Pellentesque diam volutpat commodo sed egestas. Nibh ipsum consequat nisl vel pretium lectus quam id leo. Tortor id aliquet lectus proin. Eros in cursus turpis massa tincidunt dui ut.</p>
	</li>

	<li>
		<h3>Question text for panel 5?</h3>

		<p> Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Amet porttitor eget dolor morbi non arcu risus. Egestas quis ipsum suspendisse ultrices gravida. Vitae purus faucibus ornare suspendisse sed nisi lacus sed. Ut placerat orci nulla pellentesque. Vivamus arcu felis bibendum ut. Pellentesque diam volutpat commodo sed egestas. Nibh ipsum consequat nisl vel pretium lectus quam id leo. Tortor id aliquet lectus proin. Eros in cursus turpis massa tincidunt dui ut.</p>
	</li>

	<li>
		<h3>Question text for panel 6?</h3>

		<p> Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Amet porttitor eget dolor morbi non arcu risus. Egestas quis ipsum suspendisse ultrices gravida. Vitae purus faucibus ornare suspendisse sed nisi lacus sed. Ut placerat orci nulla pellentesque. Vivamus arcu felis bibendum ut. Pellentesque diam volutpat commodo sed egestas. Nibh ipsum consequat nisl vel pretium lectus quam id leo. Tortor id aliquet lectus proin. Eros in cursus turpis massa tincidunt dui ut.</p>
	</li>

	<li>
		<h3>Question text for panel 7?</h3>

		<p> Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Amet porttitor eget dolor morbi non arcu risus. Egestas quis ipsum suspendisse ultrices gravida. Vitae purus faucibus ornare suspendisse sed nisi lacus sed. Ut placerat orci nulla pellentesque. Vivamus arcu felis bibendum ut. Pellentesque diam volutpat commodo sed egestas. Nibh ipsum consequat nisl vel pretium lectus quam id leo. Tortor id aliquet lectus proin. Eros in cursus turpis massa tincidunt dui ut.</p>
	</li>
</ul>
'
WHERE
  `name_tag` IN ('campaign', 'campaign.html');;

UPDATE
  `plugin_courses_categories`
SET
  `order`   = 0,
  `summary` = 'Category description text. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Habitasse platea dictumst vestibulum rhoncus est pellentesque elit ullamcorper'
WHERE
  `category` = 'Management Development';;