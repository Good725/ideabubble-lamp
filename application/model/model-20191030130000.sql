/*
ts:2019-10-30 13:00:00
*/

/* Set variables for the "32" (Brookfield College) theme */

-- Create procedure
DELIMITER ;
DROP PROCEDURE IF EXISTS set_theme_variable/* v2 */;

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
  END /* v2 */$$
DELIMITER ;

-- Set colour for each variable
CALL set_theme_variable('04', '32', 'primary',    '#5f0f26')/* v2 */;
CALL set_theme_variable('04', '32', 'secondary',  '#f5f5f5')/* v2 */;
CALL set_theme_variable('04', '32', 'success',    '#5f0f26')/* v2 */;
CALL set_theme_variable('04', '32', 'tertiary',   '#5f0f26')/* v2 */;
CALL set_theme_variable('04', '32', 'info',       '#17a2b8')/* v2 */;
CALL set_theme_variable('04', '32', 'warning',    '#ffc107')/* v2 */;
CALL set_theme_variable('04', '32', 'danger',     '#dc3545')/* v2 */;
CALL set_theme_variable('04', '32', 'dark',       '#333333')/* v2 */;
CALL set_theme_variable('04', '32', 'light',      '#ffffff')/* v2 */;
CALL set_theme_variable('04', '32', 'visited_link',      '#551a8b')/* v2 */;
CALL set_theme_variable('04', '32', 'menu_inactive',     '#000000')/* v2 */;
CALL set_theme_variable('04', '32', 'header_background', '#5f0f24')/* v2 */;
CALL set_theme_variable('04', '32', 'header_text',       '#ffffff')/* v2 */;
CALL set_theme_variable('04', '32', 'login_header_background',  '#5f0f24')/* v2 */;
CALL set_theme_variable('04', '32', 'email_header_background',  '#5f0f24')/* v2 */;
CALL set_theme_variable('04', '32', 'search_background',        '#ffffff')/* v2.1 */;
CALL set_theme_variable('04', '32', 'fixed_menu_background',    '#ffffff')/* v2 */;
CALL set_theme_variable('04', '32', 'cookie_notice_background', '#5f0f24')/* v2 */;
CALL set_theme_variable('04', '32', 'loading_line',  '#bfb8bf')/* v2 */;
CALL set_theme_variable('04', '32', 'tab_marker',    '#5f0f26')/* v2 */;
CALL set_theme_variable('04', '32', 'second',        '#5f0f26')/* v2 */;
CALL set_theme_variable('04', '32', 'chat_box',      '#333333')/* v2 */;
CALL set_theme_variable('04', '32', 'button_color',  '#000000')/* v2 */;
CALL set_theme_variable('04', '32', 'button_color1', '#000000')/* v2 */;
CALL set_theme_variable('04', '32', 'submenu_unselected_color', '#ffffff')/* v2 */;
CALL set_theme_variable('04', '32', 'submenu_selected_color',   '#ffffff')/* v2 */;
CALL set_theme_variable('04', '32', 'alert_text_color', '#333333')/* v2 */;