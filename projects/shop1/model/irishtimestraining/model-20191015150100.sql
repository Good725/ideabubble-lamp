/*
ts:2019-10-15 15:01:00
*/

DELIMITER ;;

-- Add the "team" page, if it does not already exist.
INSERT IGNORE INTO
  `plugin_pages_pages` (`name_tag`, `title`, `content`,`date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`)
SELECT
  'teams',
  'Courses for Teams',
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
WHERE NOT EXISTS (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` = 'teams' AND `deleted` = 0)
LIMIT 1;;

UPDATE `plugin_pages_pages`
SET
  `modified_by`   = (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  `last_modified` = CURRENT_TIMESTAMP,
  `content` = '
<div class="simplebox simplebox-raised">
	<div class="simplebox-columns" style="max-width:800px">
		<div class="simplebox-column simplebox-column-1">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<h2>Bespoke courses for groups</h2>

				<p>Our nationwide in-company service offers choice, value and the opportunity to tailor learning solutions to specifically meet your organisational needs.</p>

				<p>We work in partnership with you to assess your team&#39;s current training needs and design a learning and development solution specific to your organisation.</p>

				<p><a class="button mt-4" href="/course-list">CTA Button</a></p>
			</div>
		</div>
	</div>
</div>

<div class="simplebox simplebox-thin-margins simplebox-equal-heights">
	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1" style="max-width: 50%;">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<p><img src="/shared_media/irishtimestraining/media/photos/content/understand.png" alt="1. Understand your unique goals and challenges" style="height: 245px; width: 245px;" /></p>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2" style="max-width: 50%;">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<p><img src="/shared_media/irishtimestraining/media/photos/content/design.png" alt="2. Design a tailor-made training plan" style="height: 245px; width: 245px;" /></p>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-3" style="max-width: 50%;">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<p><img src="/shared_media/irishtimestraining/media/photos/content/deliver.png" alt="3. Deliver a practical, relevant course" style="height: 245px; width: 245px;" /></p>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-4" style="max-width: 50%;">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<p><img src="/shared_media/irishtimestraining/media/photos/content/measure.png" alt="4. Measure your team&#39;s progress" style="height: 245px; width: 245px;" /></p>
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

				<h2>Text Section 2</h2>

				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna.</p>

				<p>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur</p>

				<p><a class="button bg-primary mt-4" href="/course-list">Find my course</a></p>
			</div>
		</div>
	</div>
</div>

<div class="bg-success bg-md-none mb-5 mt-4 simplebox simplebox-align-top text-white">
	<div class="bg-success simplebox-columns">
		<div class="simplebox-column simplebox-column-1 px-md-1 mr-0" style="width:72%">
			<div class="p-md-5 simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<h2>Text Section 3</h2>

				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna.</p>

				<p>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur</p>

				<p><a class="button bg-primary mt-2 mb-md-2" href="/course-list">Find my course</a></p>
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
		<h2>Unique Selling Points</h2>
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

								<h5>Unique Selling Point 1</h5>

								<p style="line-height: 1.5;">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
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

								<div><img alt="" src="/shared_media/irishtimestraining/media/photos/content/check_color.svg" style="height:72px; width:72px"></div>
							</div>
						</div>

						<div class="simplebox-column simplebox-column-2" style="width: calc(100% - 90px);">
							<div class="simplebox-content">
								<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

								<h5 class="mt-0">Unique Selling Point 2</h5>

								<p style="line-height: 1.5;">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
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

								<div><img alt="" src="/shared_media/irishtimestraining/media/photos/content/check_color.svg" style="height:72px; width:72px"></div>
							</div>
						</div>

						<div class="simplebox-column simplebox-column-2 m-0" style="width: calc(100% - 90px);">
							<div class="simplebox-content">
								<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

								<h5>Unique Selling Point 3</h5>

								<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
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

								<div><img alt="" src="/shared_media/irishtimestraining/media/photos/content/check_color.svg" style="height:72px; width:72px"></div>
							</div>
						</div>

						<div class="simplebox-column simplebox-column-2" style="width: calc(100% - 90px);">
							<div class="simplebox-content">
								<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px"></button></div>

								<h5>Unique Selling Point 4</h5>

								<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
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
		<div class="simplebox-column simplebox-column-1" style="width: 70%;">
			<div class="simplebox-content mx-1 my-4">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<h2>We are nationwide</h2>

				<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex .</p>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2" style="width: 30%;">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<p><a class="button bg-success" href="/course-list">Find my course</a></p>
			</div>
		</div>
	</div>
</div>

<div>{course_testimonials-}</div>

<div class="get_in_touch simplebox mb-0" style="background-color: #eee;">
	<div>
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
  `name_tag` = 'teams';;
