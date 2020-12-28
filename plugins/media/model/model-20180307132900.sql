/*
ts: 2018-03-07 13:29:00
*/


INSERT INTO plugin_media_shared_media_photo_presets
  (`title`, `directory`, `height_large`, `width_large`, `action_large`, `thumb`, `height_thumb`, `width_thumb`, `action_thumb`, `publish`, `deleted`)
  VALUES
  ('APP Home Banner', 'app_home_banners', 400, 400, 'fit', 0, 0, 0, '', 1, 0);

INSERT INTO `engine_settings`
  (`variable`, `linked_plugin_name`, `name`, `note`, `type`, `group`, `options`, `expose_to_api`)
  VALUES
  ('app_home_banner', 'courses', 'APP Home Banner', 'Image to be used in the app home.', 'select', 'Courses', 'Model_Media,get_app_home_banners_as_options', 1);
