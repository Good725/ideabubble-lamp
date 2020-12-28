/*
ts:2019-10-15 15:00:00
*/

DELIMITER ;;

-- Add the "individuals" page, if it does not already exist.
INSERT IGNORE INTO
  `plugin_pages_pages` (`name_tag`, `title`, `content`,`date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`)
SELECT
  'individuals',
  'Individuals',
  '',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  '1',
  '0',
  '1',
  (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'content_wide' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'DEFAULT' LIMIT 1)
FROM `plugin_pages_pages`
WHERE NOT EXISTS (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` = 'individuals' AND `deleted` = 0)
LIMIT 1;;

UPDATE `plugin_pages_pages`
SET
  `modified_by`   = (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  `last_modified` = CURRENT_TIMESTAMP,
  `title`   = 'Courses for Individuals',
  `content` = '
<div class="simplebox simplebox-raised">
	<div class="simplebox-columns" style="max-width:800px">
		<div class="simplebox-column simplebox-column-1">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<h2>Tried, Tested and Ready-To-Go</h2>

				<p>Whether you want to strengthen your skills or learn something completely new, Irish Times Training can help.</p>

				<p>Our ready-made courses are designed and taught by industry professionals. With real-life experience, they give you the practical tools that you need in your career.</p>

				<p><a class="button bg-primary-hover mt-2" href="/course-list">Find my course</a></p>
			</div>
		</div>
	</div>
</div>

<div class="simplebox simplebox-equal-heights">
	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1" style="max-width: 50%;">
			<div class="simplebox-content bg-primary text-white p-4">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<h5><span style="font-size:44px;position: relative; top: .2em;">1</span> Choose</h5>

				<p style="font-size: 16px; line-height: 1.1875;">Pick from dozens of ready-made Irish Times Training courses</p>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2" style="max-width: 50%;">
			<div class="simplebox-content bg-primary-hover text-white p-4">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<h5><span style="font-size:44px;position: relative; top: .2em;">2</span> Attend</h5>

				<p style="font-size: 16px; line-height: 1.1875;">Go to your classroom and get to know like-minded classmates</p>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-3" style="max-width: 50%;">
			<div class="simplebox-content bg-success-hover text-white p-4">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<h5><span style="font-size:44px;position: relative; top: .2em;">3</span> Learn</h5>

				<p style="font-size: 16px; line-height: 1.1875;">Your trainer uses up-to-date case studies to give you the latest insights</p>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-4" style="max-width: 50%;">
			<div class="simplebox-content bg-success text-white p-4">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<h5><span style="font-size:44px;position: relative; top: .2em;">4</span> Apply</h5>

				<p style="font-size: 16px; line-height: 1.1875;">Get practical tools relevant to your industry and use them straight away</p>
			</div>
		</div>
	</div>
</div>

<div class="simplebox simplebox-overlap-left">
	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<p><img alt="" src="/shared_media/irishtimestraining/media/photos/content/group.png" style="height:452px; width:678px" /></p>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<h2>Why Is Professional Development Important?</h2>

				<p>Professional development is the key to keeping your skills sharp and progressing your career. It keeps things interesting for you and makes you more valuable to your company too.</p>

				<p><a class="button bg-primary-hover mt-2" href="/course-list">Find my course</a></p>
			</div>
		</div>
	</div>
</div>

<div class="bg-md-none mb-5 mt-4 simplebox simplebox-align-top">
	<div class="bg-primary-hover simplebox-columns text-white">
		<div class="simplebox-column simplebox-column-1 px-4 px-md-1 mr-0" style="width:72%">
			<div class="p-md-5 simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<h2>Over 40 years of trust</h2>

				<p>For more than four decades, thousands of individuals and organisations have trusted us to deliver the training they need. At Irish Times Training, we know that professional development never stops.</p>

				<p><a class="button bg-success mt-2 mb-md-2" href="/course-list">Find my course</a></p>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2" style="width:28%">
			<div class="pt-md-5 simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<p class="text-center text-md-left"><img alt="" src="/shared_media/irishtimestraining/media/photos/content/walk_the_talk.png" style="height:264px; width:264px" /></p>
			</div>
		</div>
	</div>
</div>

<div class="simplebox simplebox-align-top simplebox-icons">
	<div class="simplebox-title">
		<h2>A Fresh Approach To Your Professional Development</h2>
	</div>

	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<div class="simplebox simplebox-align-top stay_inline">
					<div class="simplebox-columns">
						<div class="simplebox-column simplebox-column-1" style="width: 90px;">
							<div class="simplebox-content" contenteditable="true">
								<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

								<div><img alt="" src="/shared_media/irishtimestraining/media/photos/content/check_color.svg" style="height:72px; width:72px"></div>
							</div>
						</div>

						<div class="simplebox-column simplebox-column-2" style="width: calc(100% - 90px);">
							<div class="simplebox-content">
								<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

								<h5>The Spice of Life</h5>

								<p>From sales skills to mastering management, there&rsquo;s a ready-made Irish Times Training course for everyone</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<div class="simplebox simplebox-align-top stay_inline">
					<div class="simplebox-columns">
						<div class="simplebox-column simplebox-column-1" style="width: 90px;">
							<div class="simplebox-content" contenteditable="true">
								<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

								<p><img alt="" src="/shared_media/irishtimestraining/media/photos/content/check_color.svg" style="height:72px; width:72px"></p>
							</div>
						</div>

						<div class="simplebox-column simplebox-column-2" style="width: calc(100% - 90px);">
							<div class="simplebox-content">
								<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

								<h5>Open Door</h5>

								<p>Anyone is welcome to join our ready-made courses. They&rsquo;re suitable for individuals or for groups from a team</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="simplebox simplebox-align-top simplebox-icons">
	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<div class="simplebox simplebox-align-top stay_inline">
					<div class="simplebox-columns">
						<div class="simplebox-column simplebox-column-1" style="width: 90px;">
							<div class="simplebox-content" contenteditable="true">
								<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

								<p><img alt="" src="/shared_media/irishtimestraining/media/photos/content/check_color.svg" style="height:72px; width:72px"></p>
							</div>
						</div>

						<div class="simplebox-column simplebox-column-2" style="width: calc(100% - 90px);">
							<div class="simplebox-content">
								<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

								<h5>A Class Act</h5>

								<p>Our ready-made courses are taught in a classroom. That means you can get to know your trainer and your classmates too</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<div class="simplebox simplebox-align-top stay_inline">
					<div class="simplebox-columns">
						<div class="simplebox-column simplebox-column-1 m-0" style="width: 90px;">
							<div class="simplebox-content" contenteditable="true">
								<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

								<p><img alt="" src="/shared_media/irishtimestraining/media/photos/content/check_color.svg" style="height:72px; width:72px"></p>
							</div>
						</div>

						<div class="simplebox-column simplebox-column-2 m-0" style="width: calc(100% - 90px);">
							<div class="simplebox-content">
								<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

								<h5>Are you Experienced</h5>

								<p>Our trainers aren&#39;;t just gifted teachers. They&#39;re hand-picked because of their real-life industry experience</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="simplebox">
	<div class="simplebox-columns bg-primary-hover text-white px-3 px-md-5 py-md-3">
		<div class="simplebox-column simplebox-column-1" style="width: 60%;">
			<div class="simplebox-content mx-1 my-4">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<h2 class="mb-0">Take the next step</h2>

				<p>Looking for something more in-depth? Then take a look at our range of University Accredited courses.</p>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2" style="width: 40%;">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<p><a class="button bg-success d-block" href="#">Find university-accredited courses</a></p>
			</div>
		</div>
	</div>
</div>

<div>{course_testimonials-}</div>

<div class="get_in_touch mb-0 simplebox" style="background-color: #eee;">
	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<div><img alt="" src="/shared_media/irishtimestraining/media/photos/content/get_in_touch_guy.png" /></div>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<h2>Get in touch</h2>

				<p>Contact us to discuss <span class="nowrap">tailor-made</span> courses for your team.</p>

				<p><a class="button bg-success" href="/contact-us">Contact us</a>
				   <a class="button bg-primary" href="/request-a-callback">Request a callback</a></p>
			</div>
		</div>
	</div>
</div>

<p class="my-1">&nbsp;</p>

<h1 class="border-title-both">Some of our clients</h1>
'
WHERE
  `name_tag` = 'individuals';;
