-- ------------------------------
-- Add Panels Plugin to the Plugins Table: `plugins`
-- ------------------------------
INSERT INTO `plugins`
(`name`, `version`, `folder`, `menu`, `type`, `is_frontend`, `is_backend`, `enabled`)
VALUES
( 'panels', 'development', 'panels', 'Panels', NULL, 0, 1, 1);


-- ------------------------------
-- Update Panels Plugin to request the use of the Media Filesystem
-- ------------------------------
UPDATE `plugins` SET requires_media = 1, media_folder = 'panels' WHERE name = 'panels';