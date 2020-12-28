-- ------------------------------
-- Add testimonials Plugin to the Plugins Table: `plugins`
-- ------------------------------
INSERT INTO `plugins`
(`name`, `version`, `folder`, `menu`, `type`, `is_frontend`, `is_backend`, `enabled`)
VALUES
( 'testimonials', 'development', 'testimonials', 'Testimonal', NULL, 0, 1, 1);


-- ------------------------------
-- Add Default testimonials Category to the testimonials Categories Table: `plugin_testimonials_categories`
-- ------------------------------
INSERT INTO `plugin_testimonials_categories`
(`category`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `delete`)
VALUES
( 'Testimonials', now(), now(), NULL, NULL, 1, 0);


-- ------------------------------
-- Update testimonials Plugin to request the use of the Media Filesystem
-- ------------------------------
UPDATE `plugins` SET requires_media = 1, media_folder = 'testimonials' WHERE name = 'testimonials';