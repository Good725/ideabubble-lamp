/*
ts:2019-10-30 13:00:00
*/

DELIMITER ;
DROP PROCEDURE IF EXISTS set_theme_variable/* v20191206 */;

DELIMITER $$
CREATE PROCEDURE set_theme_variable(IN template_name VARCHAR(100), IN theme_name VARCHAR(100), IN var VARCHAR(100), IN val VARCHAR(100))
  BEGIN
    UPDATE `engine_site_theme_has_variables` `has_variable`
    INNER JOIN `engine_site_theme_variables` `variable` ON `has_variable`.`variable_id` = `variable`.`id`
    INNER JOIN `engine_site_themes`          `theme`    ON `has_variable`.`theme_id`    = `theme`.`id`
    INNER JOIN `engine_site_templates`       `template` ON `theme`.`template_id`        = `template`.`id`

    SET `has_variable`.`value`  = val COLLATE utf8_general_ci

    WHERE `theme`.`stub`        = theme_name    COLLATE utf8_general_ci
    AND   `template`.`stub`     = template_name COLLATE utf8_general_ci
    AND   `variable`.`variable` = var           COLLATE utf8_general_ci;
  END /* v20191206 */$$
DELIMITER ;

/* Set colour variables for the "49" (Irish Times Training) theme */

-- Set colour for each variable
CALL set_theme_variable('04', '49', 'primary',    '#19A29E');
CALL set_theme_variable('04', '49', 'secondary',  '#f5f5f5');
CALL set_theme_variable('04', '49', 'success',    '#19A29E');
CALL set_theme_variable('04', '49', 'tertiary',   '#19A29E');
CALL set_theme_variable('04', '49', 'info',       '#17a2b8');
CALL set_theme_variable('04', '49', 'warning',    '#ffc107');
CALL set_theme_variable('04', '49', 'danger',     '#dc3545');
CALL set_theme_variable('04', '49', 'dark',       '#333333');
CALL set_theme_variable('04', '49', 'light',      '#ffffff');
CALL set_theme_variable('04', '49', 'visited_link',      '#551a8b');
CALL set_theme_variable('04', '49', 'menu_inactive',     '#000000');
CALL set_theme_variable('04', '49', 'header_background', '#ffffff');
CALL set_theme_variable('04', '49', 'header_text',       '#19A29E');
CALL set_theme_variable('04', '49', 'login_header_background',  '#ffffff');
CALL set_theme_variable('04', '49', 'email_header_background',  '#ffffff');
CALL set_theme_variable('04', '49', 'search_background',        '#19A29E');
CALL set_theme_variable('04', '49', 'fixed_menu_background',    '#ffffff');
CALL set_theme_variable('04', '49', 'cookie_notice_background', '#19A29E');
CALL set_theme_variable('04', '49', 'loading_line',  '#1E3B49');
CALL set_theme_variable('04', '49', 'tab_marker',    '#1E3B49');
CALL set_theme_variable('04', '49', 'second',        '#19A29E');
CALL set_theme_variable('04', '49', 'chat_box',      '#333333');
CALL set_theme_variable('04', '49', 'button_color',  '#000000');
CALL set_theme_variable('04', '49', 'button_color1', '#000000');
CALL set_theme_variable('04', '49', 'submenu_unselected_color', '#19A29E');
CALL set_theme_variable('04', '49', 'submenu_selected_color',   '#19A29E');
CALL set_theme_variable('04', '49', 'alert_text_color', '#ffffff');