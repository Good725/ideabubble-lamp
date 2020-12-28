/*
ts:2019-11-28 18:30:00
*/

DELIMITER ;;

INSERT INTO `engine_feeds` (`name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `short_tag`) VALUES
(
 'Our clients',
 CURRENT_TIMESTAMP,
 CURRENT_TIMESTAMP,
 (SELECT `id` from `engine_users` where `email` = 'super@ideabubble.ie'),
 (SELECT `id` from `engine_users` where `email` = 'super@ideabubble.ie'),
 '1',
 '0',
 'our_clients'
);;

UPDATE `engine_feeds`
SET `content` = '<div class="fullwidth">
	<h1 class="border-title-both">Some of our clients</h1>

	<div class="mb-md-4 simplebox text-center">
		<div class="simplebox-columns" style="max-width:1440px">
			<div class="simplebox-column simplebox-column-1">
				<div class="simplebox-content">
					<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

					<p><img alt="" src="/shared_media/irishtimestraining/media/photos/content/client_logo.png" style="height:154px; width:248px" /></p>
				</div>
			</div>

			<div class="simplebox-column simplebox-column-2">
				<div class="simplebox-content">
					<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

					<p><img alt="" src="/shared_media/irishtimestraining/media/photos/content/client_logo.png" style="height:154px; width:248px" /></p>
				</div>
			</div>

			<div class="simplebox-column simplebox-column-3">
				<div class="simplebox-content">
					<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

					<p><img alt="" src="/shared_media/irishtimestraining/media/photos/content/client_logo.png" style="height:154px; width:248px" /></p>
				</div>
			</div>

			<div class="simplebox-column simplebox-column-4">
				<div class="simplebox-content">
					<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

					<p><img alt="" src="/shared_media/irishtimestraining/media/photos/content/client_logo.png" style="height:154px; width:248px" /></p>
				</div>
			</div>

			<div class="simplebox-column simplebox-column-5">
				<div class="simplebox-content">
					<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

					<p><img alt="" src="/shared_media/irishtimestraining/media/photos/content/client_logo.png" style="height:154px; width:248px" /></p>
				</div>
			</div>
		</div>
	</div>

	<div class="mb-md-4 pb-md-4 simplebox text-center">
		<div class="simplebox-columns" style="max-width:1440px">
			<div class="simplebox-column simplebox-column-1">
				<div class="simplebox-content">
					<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

					<p><img alt="" src="/shared_media/irishtimestraining/media/photos/content/client_logo.png" style="height:154px; width:248px" /></p>
				</div>
			</div>

			<div class="simplebox-column simplebox-column-2">
				<div class="simplebox-content">
					<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

					<p><img alt="" src="/shared_media/irishtimestraining/media/photos/content/client_logo.png" style="height:154px; width:248px" /></p>
				</div>
			</div>

			<div class="simplebox-column simplebox-column-3">
				<div class="simplebox-content">
					<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

					<p><img alt="" src="/shared_media/irishtimestraining/media/photos/content/client_logo.png" style="height:154px; width:248px" /></p>
				</div>
			</div>

			<div class="simplebox-column simplebox-column-4">
				<div class="simplebox-content">
					<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

					<p><img alt="" src="/shared_media/irishtimestraining/media/photos/content/client_logo.png" style="height:154px; width:248px" /></p>
				</div>
			</div>

			<div class="simplebox-column simplebox-column-5">
				<div class="simplebox-content">
					<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

					<p><img alt="" src="/shared_media/irishtimestraining/media/photos/content/client_logo.png" style="height:154px; width:248px" /></p>
				</div>
			</div>
		</div>
	</div>
</div>'
WHERE `short_tag` = 'our_clients'
;;


-- Add English as a language, if it has not already been added
INSERT IGNORE INTO
  `engine_localisation_languages` (`code`, `title`, `created_on`, `created_by`, `updated_on`, `updated_by`)
VALUES (
  'en',
  'English',
  CURRENT_TIMESTAMP,
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  CURRENT_TIMESTAMP,
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1)
);;

-- Enable localisation
UPDATE `engine_settings` SET `value_dev` =  '1', `value_test` = '1', `value_stage` = '1', `value_live` = '1' WHERE `variable` = 'localisation_content_active';;

-- Add some "translations" (replace some text)
INSERT IGNORE INTO `engine_localisation_messages` (`message`, `created_on`, `updated_on`) VALUES
('Newsletter signup', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('No results found',  CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);;

INSERT IGNORE INTO `engine_localisation_translations` (`language_id`, `message_id`, `translation`) VALUES (
  (SELECT `id` FROM `engine_localisation_languages` WHERE `code` = 'en'),
  (SELECT `id` FROM `engine_localisation_messages` WHERE `message` = 'Newsletter signup'),
  'Get the newsletter'
);;

INSERT IGNORE INTO `engine_localisation_translations` (`language_id`, `message_id`, `translation`) VALUES (
  (SELECT `id` FROM `engine_localisation_languages` WHERE `code` = 'en'),
  (SELECT `id` FROM `engine_localisation_messages` WHERE `message` = 'No results found'),
  'Can&#39;t find what you are looking for? Contact the team on (01) 472 7101 or email gencourses@irishtimes.com to discuss co-creating a training solution to meet your needs with our <a href="/for-teams">tailor-made service</a>.'
);;