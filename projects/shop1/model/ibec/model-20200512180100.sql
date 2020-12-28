/*
ts:2020-05-12 18:01:00
*/

-- About us

DELIMITER ;;

-- Insert the "about-us" page, if it doesn't already exist
INSERT IGNORE INTO `plugin_pages_pages` (`name_tag`, `title`, `content`,`date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`)
SELECT
  'about-us',
  'About us',
  '<h1>About us</h1>',
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
WHERE NOT EXISTS (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` IN ('about-us', 'about-us.html') AND `deleted` = 0)
LIMIT 1;;

-- Update the "about-us" page content.
UPDATE
  `plugin_pages_pages`
SET
  `name_tag`      = 'about-us',
  `title`         = 'About us',
  `layout_id`     = (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'content_wide' AND `deleted` = 0 LIMIT 1),
  `last_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = 1,
  `content`       = '<div class="page-intro">

<h2>Page Text Box</h2>

<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Habitasse platea dictumst vestibulum rhoncus est pellentesque elit ullamcorper. Suspendisse ultrices gravida dictum fusce ut placerat orci. At consectetur lorem donec massa. Bibendum arcu vitae elementum curabitur vitae nunc sed. Adipiscing elit ut aliquam purus sit amet luctus venenatis lectus. Lorem ipsum dolor sit amet consectetur adipiscing elit pellentesque. Enim nec dui.</p>

<p>Suspendisse ultrices gravida dictum fusce ut placerat orci. At consectetur lorem donec massa. Bibendum arcu vitae elementum curabitur vitae nunc sed. Adipiscing elit ut aliquam purus sit amet luctus venenatis lectus. Lorem ipsum dolor sit amet consectetur adipiscing elit pellentesque. Adipiscing elit ut aliquam purus sit amet luctus venenatis lectus.</p>

<p>Lorem ipsum dolor sit amet consectetur adipiscing elit pellentesque. Enim nec dui. Adipiscing elit ut aliquam purus sit amet luctus venenatis lectus. Lorem ipsum dolor sit amet consectetur adipiscing elit pellentesque. Enim nec dui. Adipiscing elit ut aliquam purus sit amet luctus venenatis lectus.</p>

<p><a class="read_more-lg" style="font-weight:400;" href="/contact-us">Contact us</a></p>
</div>

<div class="bg-light simplebox simplebox-equal-heights why_us">
	<div class="simplebox-title">
		<h2>Why us?</h2>
	</div>

	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<h3>We know business</h3>
				<p>Lorem ipsum dolor sit amet consectetur adipiscing elit pellentesque. Enim nec dui. Adipiscing elit ut aliquam purus sit amet luctus</p>
				<p><a class="read_more" href="/coming-soon">More info</a></p>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<h3>Applied learning</h3>
				<p>Lorem ipsum dolor sit amet consectetur adipiscing elit pellentesque. Enim nec dui. Adipiscing elit ut aliquam purus sit amet luctus</p>
				<p><a class="read_more" href="/coming-soon">More info</a></p>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-3">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<h3>Industry collaboration</h3>
				<p>Lorem ipsum dolor sit amet consectetur adipiscing elit pellentesque. Enim nec dui. Adipiscing elit ut aliquam purus sit amet luctus</p>
				<p><a class="read_more" href="/coming-soon">More info</a></p>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-4">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<h3>Accreditation</h3>
				<p>Lorem ipsum dolor sit amet consectetur adipiscing elit pellentesque. Enim nec dui. Adipiscing elit ut aliquam purus sit amet luctus</p>
				<p><a class="read_more" href="/coming-soon">More info</a></p>
			</div>
		</div>
	</div>
</div>

<div class="bg-dark_purple simplebox simplebox-panel">
	<div class="simplebox-title">
		<h2>NEW PANEL 1</h2>
	</div>

	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<p><a class="read_more" href="/coming-soon">Find out more</a></p>
			</div>
		</div>
	</div>
</div>

<div class="bg-dark_gray simplebox simplebox-panel">
	<div class="simplebox-title">
		<h2>NEW PANEL 2</h2>
	</div>

	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<p><a class="read_more" href="/coming-soon">Find out more</a></p>
			</div>
		</div>
	</div>
</div>

<div class="bg-lighter simplebox mb-0 pt-4">
	<div class="simplebox-title">
		<h2 style="font-size: 35px; margin-bottom: 1em;">About Ibec</h2>
	</div>

	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<p>{accordion-Placeholder}</p>
			</div>
		</div>
	</div>
</div>

<div class="simplebox simplebox-align-top simplebox-video bg-light mb-0 py-md-5">
	<div class="simplebox-columns my-sm-4">
		<div class="simplebox-column simplebox-column-1" style="width: 32.6%;">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<h2 style="margin-bottom: .5em;">Video</h2>

				<p style="line-height: 1.25;">Description of video. Lorem ipsum dolor sit amet, consectetur. Lorem ipsum dolor sit amet, consectetur. Lorem ipsum dolor sit amet, consectetur. Lorem ipsum dolor sit amet, consectetur</p>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2" style="width: 67.4%;">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<p>{video-NycTraffic.mp4}</p>
			</div>
		</div>
	</div>
</div>

<div class="simplebox simplebox-overlap-right simplebox-trainers">
	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1">
			<div class="simplebox-content" style="">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<h1 style="margin-top: 5px;">Our trainers</h1>

				<ul>
					<li>Over 30 years training managers</li>
					<li>Innovative portfolio of in-company programmes, online courses, seminars and short courses nationwide</li>
					<li>70 highly qualified facilitators</li>
				</ul>

				<p><a class="read_more-lg" href="/coming-soon" style="font-size: 18px;">Call to action link</a></p>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<div><img alt="" src="/shared_media/ibec/media/photos/content/team.jpg" style="float:right; height:420px; width:740px" /></div>
			</div>
		</div>
	</div>
</div>

<div class="bg-lighter pt-4 pt-sm-5 simplebox">
	<div class="simplebox-title">
		<h2 class="mt-2">Explore what we do</h2>
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

<div class="bg-primary mb-0 simplebox simplebox-accredited2">
	<div class="simplebox-title">
		<h1>Accredited Partners</h1>

		<p style="text-align: center;"><img alt="TU Dublin (Technological University Dublin, Ollscoil Teichneolaíochta Bhaile Átha Cliath)" src="/shared_media/ibec/media/photos/content/TUD_white.png" style="height:197px; width:312px; margin: 7px 36px -9px 0;"></p>
	</div>

	<div class="simplebox-columns" style="border-top: 1px dashed #fff9; padding-top: 30px;">
		<div class="simplebox-column simplebox-column-1">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<div style="text-align: center;"><img alt="EMCC (European Mentoring &amp; Coaching Council) EQA (EMCC European Quality Award)" src="/shared_media/ibec/media/photos/content/emcc-white.png" style="height:128px; width:199px"></div>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<div style="text-align: center;"><img alt="mii (The Mediators&#39; Institute of Ireland)" src="/shared_media/ibec/media/photos/content/mii-white.png" style="height:81px; width:168px"></div>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-3">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<div style="text-align: center;"><img alt="QQI Award" src="/shared_media/ibec/media/photos/content/qqi-award.png" style="height:158px; width:148px"></div>
			</div>
		</div>
	</div>
</div>

<div class="bg-dark_purple mb-0 text-white simplebox simplebox-training2">
	<div class="simplebox-background_image">
		<p><img alt="" src="/shared_media/ibec/media/photos/content/new-home-banner_1.jpg" style="height:830px; width:2560px"></p>
	</div>

	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1" style="max-width: 546px;">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

				<h2>Do you require customised,<br />in-company training?</h2>

				<p style="font-size: 18px; line-height: 23px;">Ibec&#39;s Management Training team can tailor our most popular courses to your requirements, and run them on an in-company basis, saving you time and money.</p>

				<p class="mt-5" style="font-size: 18px;"><a class="read_more text-bright_pink" href="/contact-us"><strong>Contact us</strong></a></p>
			</div>
		</div>
	</div>
</div>

\n
\n<p>{spotlights-}</p>
\n
\n<p>{get_started-}</p>
'
WHERE
  `name_tag` IN ('about-us', 'about-us.html');;

