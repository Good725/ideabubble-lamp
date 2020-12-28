-- ------------------------------
-- Add News Plugin to the Plugins Table: `plugins`
-- ------------------------------
INSERT INTO `plugins`
(`name`, `version`, `folder`, `menu`, `type`, `is_frontend`, `is_backend`, `enabled`)
VALUES
( 'news', 'development', 'news', 'News', NULL, 0, 1, 1);


-- ------------------------------
-- Add Default News Category to the News Categories Table: `plugin_news_categories`
-- ------------------------------
INSERT INTO `plugin_news_categories`
(`category`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `delete`)
VALUES
( 'News', now(), now(), NULL, NULL, 1, 0);


-- ------------------------------
-- Update News Plugin to request the use of the Media Filesystem
-- ------------------------------
UPDATE `plugins` SET requires_media = 1, media_folder = 'news' WHERE name = 'news';