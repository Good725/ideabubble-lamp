/*
ts:2020-04-27 18:00:00
*/

/* Add the '51' (IBEC) theme, if it does not already exist */
DELIMITER  ;;
INSERT INTO
  `engine_site_themes` (`title`, `stub`, `template_id`, `date_created`, `date_modified`)
  SELECT '51', '51', (SELECT `id`  FROM `engine_site_templates` WHERE `stub` = '04' LIMIT 1), CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
    FROM `engine_site_themes`
    WHERE NOT EXISTS (SELECT * FROM `engine_site_themes` WHERE `stub` = '51')
    LIMIT 1
;;

/* Add the '51' theme styles */
ALTER TABLE `engine_site_themes` CHANGE COLUMN `styles` `styles` MEDIUMBLOB NULL DEFAULT NULL ;;
UPDATE
  `engine_site_themes`
SET
  `date_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = IFNULL((SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1), 1),
  `styles`        = '@import url(\'https://use.typekit.net/eam0hts.css\');
\n
\n:root {
\n    \-\-bright_purple: #7e57c5;
\n    \-\-dark_purple: #1d1a3b;
\n    \-\-bright_pink: #e41395;
\n    \-\-dark_pink: #310f31;
\n    \-\-light_green: #76bc21;
\n    \-\-main_gray: #a7a8a9;
\n    \-\-light_gray: #e5e2ef;
\n    \-\-lighter_gray: #f4f4f4;
\n    \-\-dark_gray: #555;
\n    \-\-darkened_gray: #888;
\n    \-\-middle-gray: #c5c5c5;
\n
\n    \-\-primary: #7e57c5;   \-\-primary-hover: #9c7ed3;   \-\-primary-active: #53328e;
\n    \-\-secondary: #f5f5f5; \-\-secondary-hover: #d3d3d3; \-\-secondary-active: #e6e6e6;
\n    \-\-success: #1d1a3b;   \-\-success-hover: #322d67;   \-\-success-active: #0c0b19;
\n    \-\-info: #5bc0de;      \-\-info-hover: #31b0d5;      \-\-info-active: #269abc;
\n    \-\-warning: #e8a917;   \-\-warning-hover: #e7b236;   \-\-warning-active: #c89214;
\n    \-\-danger: #df1e39;    \-\-danger-hover: #bd362f;    \-\-danger-active: #c9302c;
\n
\n    \-\-arrow-bright_purple: url(\'data:image/svg+xml; utf8, <svg height=\"15\" viewBox=\"0 0 27 20\" fill=\"none\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M26.6667 9.48718L17.4359 0.25641C17.265 0.0854701 17.094 0 16.8376 0C16.6667 0 16.4103 0.0854701 16.2393 0.25641C16.0684 0.42735 16.0684 0.598291 15.9829 0.769231V0.854701C15.9829 1.02564 16.0684 1.28205 16.2393 1.36752L24.1026 9.23077H0.769231C0.34188 9.23077 0 9.57265 0 10C0 10.4274 0.34188 10.7692 0.769231 10.7692H24.188L16.4103 18.6325C16.0684 18.9744 16.0684 19.4872 16.4103 19.7436C16.5812 19.9145 16.7521 20 17.0085 20C17.1795 20 17.4359 19.9145 17.6068 19.7436L26.7521 10.5128C26.9231 10.3419 26.9231 9.82906 26.6667 9.48718Z\" fill=\"%237e57c5\" /></svg>\');
\n    \-\-arrow-dark_purple:   url(\'data:image/svg+xml; utf8, <svg height=\"15\" viewBox=\"0 0 27 20\" fill=\"none\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M26.6667 9.48718L17.4359 0.25641C17.265 0.0854701 17.094 0 16.8376 0C16.6667 0 16.4103 0.0854701 16.2393 0.25641C16.0684 0.42735 16.0684 0.598291 15.9829 0.769231V0.854701C15.9829 1.02564 16.0684 1.28205 16.2393 1.36752L24.1026 9.23077H0.769231C0.34188 9.23077 0 9.57265 0 10C0 10.4274 0.34188 10.7692 0.769231 10.7692H24.188L16.4103 18.6325C16.0684 18.9744 16.0684 19.4872 16.4103 19.7436C16.5812 19.9145 16.7521 20 17.0085 20C17.1795 20 17.4359 19.9145 17.6068 19.7436L26.7521 10.5128C26.9231 10.3419 26.9231 9.82906 26.6667 9.48718Z\" fill=\"%231d1a3b\" /></svg>\');
\n    \-\-arrow-bright_pink:   url(\'data:image/svg+xml; utf8, <svg height=\"15\" viewBox=\"0 0 27 20\" fill=\"none\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M26.6667 9.48718L17.4359 0.25641C17.265 0.0854701 17.094 0 16.8376 0C16.6667 0 16.4103 0.0854701 16.2393 0.25641C16.0684 0.42735 16.0684 0.598291 15.9829 0.769231V0.854701C15.9829 1.02564 16.0684 1.28205 16.2393 1.36752L24.1026 9.23077H0.769231C0.34188 9.23077 0 9.57265 0 10C0 10.4274 0.34188 10.7692 0.769231 10.7692H24.188L16.4103 18.6325C16.0684 18.9744 16.0684 19.4872 16.4103 19.7436C16.5812 19.9145 16.7521 20 17.0085 20C17.1795 20 17.4359 19.9145 17.6068 19.7436L26.7521 10.5128C26.9231 10.3419 26.9231 9.82906 26.6667 9.48718Z\" fill=\"%23e41395\" /></svg>\');
\n    \-\-arrow-white:         url(\'data:image/svg+xml; utf8, <svg height=\"15\" viewBox=\"0 0 27 20\" fill=\"none\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M26.6667 9.48718L17.4359 0.25641C17.265 0.0854701 17.094 0 16.8376 0C16.6667 0 16.4103 0.0854701 16.2393 0.25641C16.0684 0.42735 16.0684 0.598291 15.9829 0.769231V0.854701C15.9829 1.02564 16.0684 1.28205 16.2393 1.36752L24.1026 9.23077H0.769231C0.34188 9.23077 0 9.57265 0 10C0 10.4274 0.34188 10.7692 0.769231 10.7692H24.188L16.4103 18.6325C16.0684 18.9744 16.0684 19.4872 16.4103 19.7436C16.5812 19.9145 16.7521 20 17.0085 20C17.1795 20 17.4359 19.9145 17.6068 19.7436L26.7521 10.5128C26.9231 10.3419 26.9231 9.82906 26.6667 9.48718Z\" fill=\"white\" /></svg>\');
\n    \-\-arrow-main_gray:     url(\'data:image/svg+xml; utf8, <svg height=\"15\" viewBox=\"0 0 27 20\" fill=\"none\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M26.6667 9.48718L17.4359 0.25641C17.265 0.0854701 17.094 0 16.8376 0C16.6667 0 16.4103 0.0854701 16.2393 0.25641C16.0684 0.42735 16.0684 0.598291 15.9829 0.769231V0.854701C15.9829 1.02564 16.0684 1.28205 16.2393 1.36752L24.1026 9.23077H0.769231C0.34188 9.23077 0 9.57265 0 10C0 10.4274 0.34188 10.7692 0.769231 10.7692H24.188L16.4103 18.6325C16.0684 18.9744 16.0684 19.4872 16.4103 19.7436C16.5812 19.9145 16.7521 20 17.0085 20C17.1795 20 17.4359 19.9145 17.6068 19.7436L26.7521 10.5128C26.9231 10.3419 26.9231 9.82906 26.6667 9.48718Z\" fill=\"%23a7a8a9\" /></svg>\');
\n    \-\-arrow-down-dark_purple: url(\'data:image/svg+xml; utf8, <svg height=\"16\" viewBox=\"0 0 17 16\" fill=\"none\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M9.31651 15.1148L16.7011 7.55744C16.8379 7.41749 16.9062 7.27753 16.9062 7.06761C16.9062 6.92765 16.8379 6.71772 16.7011 6.57777C16.5644 6.43782 16.4276 6.43782 16.2909 6.36785L16.2225 6.36785C16.0857 6.36785 15.8806 6.43782 15.8122 6.57777L9.52163 13.0156L9.52164 0.912131C9.52164 0.562252 9.24813 0.282348 8.90625 0.282348C8.56437 0.282348 8.29087 0.562252 8.29087 0.912131L8.29087 13.0855L2.00027 6.71773C1.72676 6.43782 1.31651 6.43782 1.11138 6.71773C0.974628 6.85768 0.906251 6.99763 0.906251 7.20756C0.906251 7.34751 0.974628 7.55744 1.11138 7.69739L8.49599 15.1848C8.63275 15.3248 9.043 15.3248 9.31651 15.1148Z\" fill=\"%231d1a3b\" /></svg>\');
\n    \-\-arrow-down-bright_purple: url(\'data:image/svg+xml; utf8, <svg height=\"16\" viewBox=\"0 0 17 16\" fill=\"none\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M9.31651 15.1148L16.7011 7.55744C16.8379 7.41749 16.9062 7.27753 16.9062 7.06761C16.9062 6.92765 16.8379 6.71772 16.7011 6.57777C16.5644 6.43782 16.4276 6.43782 16.2909 6.36785L16.2225 6.36785C16.0857 6.36785 15.8806 6.43782 15.8122 6.57777L9.52163 13.0156L9.52164 0.912131C9.52164 0.562252 9.24813 0.282348 8.90625 0.282348C8.56437 0.282348 8.29087 0.562252 8.29087 0.912131L8.29087 13.0855L2.00027 6.71773C1.72676 6.43782 1.31651 6.43782 1.11138 6.71773C0.974628 6.85768 0.906251 6.99763 0.906251 7.20756C0.906251 7.34751 0.974628 7.55744 1.11138 7.69739L8.49599 15.1848C8.63275 15.3248 9.043 15.3248 9.31651 15.1148Z\" fill=\"%237e57c5\" /></svg>\');
\n    \-\-arrow-down-bright_pink: url(\'data:image/svg+xml; utf8, <svg height=\"16\" viewBox=\"0 0 17 16\" fill=\"none\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M9.31651 15.1148L16.7011 7.55744C16.8379 7.41749 16.9062 7.27753 16.9062 7.06761C16.9062 6.92765 16.8379 6.71772 16.7011 6.57777C16.5644 6.43782 16.4276 6.43782 16.2909 6.36785L16.2225 6.36785C16.0857 6.36785 15.8806 6.43782 15.8122 6.57777L9.52163 13.0156L9.52164 0.912131C9.52164 0.562252 9.24813 0.282348 8.90625 0.282348C8.56437 0.282348 8.29087 0.562252 8.29087 0.912131L8.29087 13.0855L2.00027 6.71773C1.72676 6.43782 1.31651 6.43782 1.11138 6.71773C0.974628 6.85768 0.906251 6.99763 0.906251 7.20756C0.906251 7.34751 0.974628 7.55744 1.11138 7.69739L8.49599 15.1848C8.63275 15.3248 9.043 15.3248 9.31651 15.1148Z\" fill=\"%23e41395\" /></svg>\');
\n
\n
\n    \-\-arrow-bright_purple-lg: url(\'data:image/svg+xml; utf8, <svg width=\"32\" height=\"24\" viewBox=\"0 0 32 24\" fill=\"none\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M31.5 11.4L20.7 0.600049C20.5 0.400049 20.3 0.300049 20 0.300049C19.8 0.300049 19.5 0.400049 19.3 0.600049C19.1 0.800049 19.1 1.00005 19 1.20005V1.30005C19 1.50005 19.1 1.80005 19.3 1.90005L28.5 11.1H1.19999C0.699988 11.1 0.299988 11.5 0.299988 12C0.299988 12.5 0.699988 12.9 1.19999 12.9H28.6L19.5 22.1C19.1 22.5 19.1 23.1 19.5 23.4C19.7 23.6 19.9 23.7001 20.2 23.7001C20.4 23.7001 20.7 23.6 20.9 23.4L31.6 12.6C31.8 12.4 31.8 11.8 31.5 11.4Z\" fill=\"%237e57c5\"/></svg>\');
\n    \-\-arrow-bright_pink-lg:   url(\'data:image/svg+xml; utf8, <svg width=\"32\" height=\"24\" viewBox=\"0 0 32 24\" fill=\"none\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M31.5 11.4L20.7 0.600049C20.5 0.400049 20.3 0.300049 20 0.300049C19.8 0.300049 19.5 0.400049 19.3 0.600049C19.1 0.800049 19.1 1.00005 19 1.20005V1.30005C19 1.50005 19.1 1.80005 19.3 1.90005L28.5 11.1H1.19999C0.699988 11.1 0.299988 11.5 0.299988 12C0.299988 12.5 0.699988 12.9 1.19999 12.9H28.6L19.5 22.1C19.1 22.5 19.1 23.1 19.5 23.4C19.7 23.6 19.9 23.7001 20.2 23.7001C20.4 23.7001 20.7 23.6 20.9 23.4L31.6 12.6C31.8 12.4 31.8 11.8 31.5 11.4Z\" fill=\"%23e41395\"/></svg>\');
\n    \-\-arrow-dark_purple-lg:   url(\'data:image/svg+xml; utf8, <svg width=\"32\" height=\"24\" viewBox=\"0 0 32 24\" fill=\"none\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M31.5 11.4L20.7 0.600049C20.5 0.400049 20.3 0.300049 20 0.300049C19.8 0.300049 19.5 0.400049 19.3 0.600049C19.1 0.800049 19.1 1.00005 19 1.20005V1.30005C19 1.50005 19.1 1.80005 19.3 1.90005L28.5 11.1H1.19999C0.699988 11.1 0.299988 11.5 0.299988 12C0.299988 12.5 0.699988 12.9 1.19999 12.9H28.6L19.5 22.1C19.1 22.5 19.1 23.1 19.5 23.4C19.7 23.6 19.9 23.7001 20.2 23.7001C20.4 23.7001 20.7 23.6 20.9 23.4L31.6 12.6C31.8 12.4 31.8 11.8 31.5 11.4Z\" fill=\"%231d1a3b\"/></svg>\');
\n    \-\-arrow-white-lg:         url(\'data:image/svg+xml; utf8, <svg width=\"32\" height=\"24\" viewBox=\"0 0 32 24\" fill=\"none\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M31.5 11.4L20.7 0.600049C20.5 0.400049 20.3 0.300049 20 0.300049C19.8 0.300049 19.5 0.400049 19.3 0.600049C19.1 0.800049 19.1 1.00005 19 1.20005V1.30005C19 1.50005 19.1 1.80005 19.3 1.90005L28.5 11.1H1.19999C0.699988 11.1 0.299988 11.5 0.299988 12C0.299988 12.5 0.699988 12.9 1.19999 12.9H28.6L19.5 22.1C19.1 22.5 19.1 23.1 19.5 23.4C19.7 23.6 19.9 23.7001 20.2 23.7001C20.4 23.7001 20.7 23.6 20.9 23.4L31.6 12.6C31.8 12.4 31.8 11.8 31.5 11.4Z\" fill=\"white\"/></svg>\');
\n    \-\-arrow-main_gray-lg:     url(\'data:image/svg+xml; utf8, <svg width=\"32\" height=\"24\" viewBox=\"0 0 32 24\" fill=\"none\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M31.5 11.4L20.7 0.600049C20.5 0.400049 20.3 0.300049 20 0.300049C19.8 0.300049 19.5 0.400049 19.3 0.600049C19.1 0.800049 19.1 1.00005 19 1.20005V1.30005C19 1.50005 19.1 1.80005 19.3 1.90005L28.5 11.1H1.19999C0.699988 11.1 0.299988 11.5 0.299988 12C0.299988 12.5 0.699988 12.9 1.19999 12.9H28.6L19.5 22.1C19.1 22.5 19.1 23.1 19.5 23.4C19.7 23.6 19.9 23.7001 20.2 23.7001C20.4 23.7001 20.7 23.6 20.9 23.4L31.6 12.6C31.8 12.4 31.8 11.8 31.5 11.4Z\" fill=\"%23a7a8a9\"/></svg>\');
\n    \-\-arrow-down-main_gray-lg: url(\'data:image/svg+xml; utf8, <svg height=\"32\" viewBox=\"0 0 32 24\" fill=\"none\" xmlns=\"http://www.w3.org/2000/svg\"><path d=\"M9.31651 15.1148L16.7011 7.55744C16.8379 7.41749 16.9062 7.27753 16.9062 7.06761C16.9062 6.92765 16.8379 6.71772 16.7011 6.57777C16.5644 6.43782 16.4276 6.43782 16.2909 6.36785L16.2225 6.36785C16.0857 6.36785 15.8806 6.43782 15.8122 6.57777L9.52163 13.0156L9.52164 0.912131C9.52164 0.562252 9.24813 0.282348 8.90625 0.282348C8.56437 0.282348 8.29087 0.562252 8.29087 0.912131L8.29087 13.0855L2.00027 6.71773C1.72676 6.43782 1.31651 6.43782 1.11138 6.71773C0.974628 6.85768 0.906251 6.99763 0.906251 7.20756C0.906251 7.34751 0.974628 7.55744 1.11138 7.69739L8.49599 15.1848C8.63275 15.3248 9.043 15.3248 9.31651 15.1148Z\" fill=\"%23a7a8a9\" /></svg>\');
\n}
\n
\nhtml,
\nh1, h2, h3, h4, h5, h6,
\nbutton {
\n    font-family: Soleil, Helvetica, Arial, sans-serif;
\n}
\n
\n.text-black { color: var(\-\-dark_gray) !important; }
\n.text-bright_purple { color: var(\-\-bright_purple) !important; }
\n.text-dark_purple   { color: var(\-\-dark_purple)   !important; }
\n.text-bright_pink   { color: var(\-\-bright_pink)   !important; }
\n.text-dark_pink     { color: var(\-\-dark_pink)     !important; }
\n.text-main_gray     { color: var(\-\-main_gray)     !important; }
\n.text-light_gray    { color: var(\-\-light_gray)    !important; }
\n.text-lighter_gray  { color: var(\-\-lighter_gray)  !important; }
\n.text-middle_gray   { color: var(\-\-middle_gray)   !important; }
\n.text-dark_gray     { color: var(\-\-dark_gray)     !important; }
\n
\n.bg-bright_purple { background-color: var(\-\-bright_purple) !important; }
\n.bg-dark_purple   { background-color: var(\-\-dark_purple)   !important; }
\n.bg-bright_pink   { background-color: var(\-\-bright_pink)   !important; }
\n.bg-dark_pink     { background-color: var(\-\-dark_pink)     !important; }
\n.bg-main_gray     { background-color: var(\-\-main_gray)     !important; }
\n.bg-light_gray    { background-color: var(\-\-light_gray)    !important; }
\n.bg-lighter_gray  { background-color: var(\-\-lighter_gray)  !important; }
\n.bg-middle_gray   { background-color: var(\-\-middle_gray)   !important; }
\n.bg-dark_gray     { background-color: var(\-\-dark_gray)     !important; }
\n
\n
\n.bg-light { background: var(\-\-light_gray) !important; }
\n.bg-lighter { background: var(\-\-lighter_gray) !important; }
\n.bg-light-gray-gradient {
\n      background: rgb(234,232,233);
\n      background: radial-gradient(circle, rgba(234,232,233,1) 61%, rgba(194,194,194,1) 92%);
\n  }
\n
\n.border, .border-top, .border-right, .border-bottom, .border-left { border-color: var(\-\-light_gray) !important; }
\n
\n.simplebox-columns,
\n.simplebox-title,
\n.row,
\n.footer-copyright .row {
\n    max-width: 1140px;
\n}
\n
\n@media (max-width: 1140px) {
\n
\n   body,
\n     .wrapper {
\n        overflow-x: hidden;
\n        width: 100vw;
\n        max-width: 100%;
\n   }
\n
\n}
\n
\n@media (max-width: 1140px) and (orientation: landscape) {
\n
\n     body,
\n       .wrapper {
\n          overflow-x: hidden;
\n          width: 100vw;
\n          max-width: 100%;
\n     }
\n
\n}
\n@media screen and (min-width: 1025px) and  (max-width: 1170px) {
\n    .simplebox {
\n        margin-left: 0;
\n        width: auto;
\n    }
\n}

\n
\n\/\* Heading sizes \*\/
\n@media screen and (max-width: 767px) {
\n    h1, .page-content h1, .banner-slide h1 {font-size: 36px; font-weight: 700; line-height: 54px; }
\n    h2, .page-content h2, .banner-slide h2 {font-size: 35px; font-weight: 700; line-height: 42px; margin: 0 0 .8rem; }
\n    h3, .page-content h3, .banner-slide h3 {font-size: 24px; font-weight: 700; line-height: 30px; }
\n    h4, .page-content h4, .banner-slide h4 {font-size: 20px; font-weight: 700; line-height: 25px; }
\n    h5, .page-content h5, .banner-slide h5 {font-size: 15px; font-weight: 700; line-height: 19px; }
\n    h6, .page-content h6, .banner-slide h6 {font-size: 12px; font-weight: 700; line-height: 16px; }
\n
\n    .banner-slide h1 { font-size: 36px; line-height: 1.138888889; margin: .5rem 0 .2777777778em;}
\n    .banner-slide p { font-size: 16px; line-height: 1.1875; margin: .5rem 0;}
\n    .layout-home .banner-slide p  { font-size: 1rem; line-height: 1.25; margin-top: 0; }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    h1, .page-content h1, .banner-slide h1 {font-size: 48px; font-weight: 700; line-height: 54px; }
\n    h2, .page-content h2, .banner-slide h2 {font-size: 35px; font-weight: 700; line-height: 42px; margin-bottom: .16666667em; }
\n    h3, .page-content h3, .banner-slide h3 {font-size: 24px; font-weight: 700; line-height: 30px; }
\n    h4, .page-content h4, .banner-slide h4 {font-size: 20px; font-weight: 700; line-height: 25px; }
\n    h5, .page-content h5, .banner-slide h5 {font-size: 15px; font-weight: 700; line-height: 19px; }
\n    h6, .page-content h6, .banner-slide h6 {font-size: 12px; font-weight: 700; line-height: 16px; }
\n
\n    .banner-slide h1 { margin: .75rem 0; }
\n    .layout-home .banner-slide h1 { font-size: 48px; line-height: 1.125; }
\n    .layout-home .banner-slide { margin: 0;}
\n}
\n
\nbody {
\n    background-color: #fff;
\n    color: var(\-\-dark_gray);
\n}
\n
\n.container {
\n    max-width: 1170px;
\n    padding-left: 15px;
\n    padding-right: 15px;
\n}
\n
\n.row.gutters {
\n    margin-left: -15px;
\n    margin-right: -15px;
\n}
\n
\n.col-xs-1, .col-sm-1, .col-md-1, .col-lg-1, .col-xs-2, .col-sm-2, .col-md-2, .col-lg-2, .col-xs-3, .col-sm-3, .col-md-3, .col-lg-3, .col-xs-4, .col-sm-4, .col-md-4, .col-lg-4, .col-xs-5, .col-sm-5, .col-md-5, .col-lg-5, .col-xs-6, .col-sm-6, .col-md-6, .col-lg-6, .col-xs-7, .col-sm-7, .col-md-7, .col-lg-7, .col-xs-8, .col-sm-8, .col-md-8, .col-lg-8, .col-xs-9, .col-sm-9, .col-md-9, .col-lg-9, .col-xs-10, .col-sm-10, .col-md-10, .col-lg-10, .col-xs-11, .col-sm-11, .col-md-11, .col-lg-11, .col-xs-12, .col-sm-12, .col-md-12, .col-lg-12 {
\n    padding-left: 15px;
\n    padding-right: 15px;
\n}
\n
\n.simplebox-column {
\n    margin-left: 15px;
\n    margin-right: 15px;
\n}
\n
\n.simplebox-thin-margins .simplebox-column {
\n    margin-left: 10px;
\n    margin-right: 10px;
\n}
\n
\n.simplebox-title { text-align: left; }
\n
\n.form-input {
\n    border-color: #cdcdcd;
\n    border-radius: 3px;
\n}
\n
\n.form-checkbox-helper {
\n    border-radius: 0;
\n    border-color: var(\-\-main_gray);
\n}
\n
\n:checked + .form-checkbox-helper:after {
\n    color: var(\-\-primary);
\n}
\n
\n.layout-checkout .form-checkbox-helper {
\n    background: var(\-\-light_gray);
\n    border-radius: 2px;
\n    border-color: #dbdbdb;
\n}
\n
\n.form-select.form-select:before {
\n    background: none;
\n}
\n .contact\-\-left .theme-form-content, .billing-content, .privacy-content
\n {
\n      border: 1px solid var(\-\-middle-gray);
\n      margin-bottom: 1.25em;
\n }
\n .contact\-\-left .theme-form-content .form-input, .billing-content .form-input, .privacy-content .form-input
\n {
\n      border: 1px solid var(\-\-middle-gray);
\n }
\n#payment-tabs-credit_card .form-input
\n{
\n border: 1px solid var(\-\-middle-gray);
\n}
\n
\n.form-select:after {
\n    border-color: currentColor;
\n}
\n
\n.table thead {
\n    background: var(\-\-primary);
\n    color: #FFF;
\n}
\n
\n.badge {
\n    background: var(\-\-primary);
\n    color: #fff;
\n}
\n
\n.db-sidebar .sidebar-menu li a:hover,
\n.db-sidebar .sidebar-menu li a.active {
\n    background-color: var(\-\-primary);
\n    color: #fff;
\n}
\n
\n.popup-header {
\n    background: var(\-\-primary);
\n    color: #fff;
\n}
\n
\n.button.course-banner-button.cl_bg {
\n    background-color: var(\-\-bright_purple);
\n}
\n
\n.course-widget-links .button.button\-\-cl_remove {
\n    background-color: #f60000;
\n}
\n
\n\/\* Autotimetables \*\/
\n.autotimetable tbody tr:nth-child(even) {
\n    background: #f9f9f9;
\n}
\n
\n.autotimetable tbody tr td a {
\n    color: #244683;
\n}
\n
\n.autotimetable tbody a:hover {
\n    color: var(\-\-primary);
\n}
\n
\n.autotimetable .new_date {
\n    border-color: var(\-\-primary);
\n}
\n
\n.autotimetable .new_date td:nth-child(1) {
\n    background-color: var(\-\-primary);
\n    color: #fff;
\n}
\n
\n:checked + .seating-selector-checkbox-helper:after {
\n    color: var(\-\-bright_purple);
\n}
\n
\n.seating-selector-option-radio:checked + .button {
\n    background: var(\-\-bright_purple);
\n    color: #fff;
\n}
\n
\n.seating-selector-option-hover {
\n    background-color: var(\-\-bright_purple);
\n    color: #fff;
\n}
\n
\n\/\* Forms \*\/
\n.select:after {
\n    border-top-color: var(\-\-primary);
\n}
\n
\n.form-select:before {
\n    background-image: -webkit-linear-gradient(left, transparent 0, transparent calc(100% - 2.75em), var(\-\-primary) calc(100% - 2.75em), var(\-\-primary) 100%);
\n    background-image: linear-gradient(to right, transparent 0, transparent calc(100% - 2.75em), var(\-\-primary) calc(100% - 2.75em), var(\-\-primary) 100%);
\n}
\n
\n.form-select-plain select {
\n    border-color: #333;
\n    border-radius: 0;
\n}
\n
\n.form-select-plain .form-select:after {
\n    border-color: #333;
\n}
\n
\n.button,
\n.formrt button,
\n.formrt [type=\"submit\"],
\n.formrt [type=\"reset\"] {
\n    background-color: var(\-\-primary);
\n    border-radius: 0;
\n}
\n
\n.button:hover,
\n.formrt button:hover,
\n.formrt [type=\"submit\"]:hover,
\n.formrt [type=\"reset\"]:hover {
\n    background-color: var(\-\-primary-hover);
\n}
\n
\n.button:active,
\n.formrt button:active,
\n.formrt [type=\"submit\"]:active,
\n.formrt [type=\"reset\"]:active {
\n    background-color: var(\-\-primary-active);
\n}
\n
\n.page-content .formrt ul li {
\n    padding: 0;
\n}
\n
\n.form-contact_us {
\n    background: #fff;
\n    box-shadow: 0 4px 25px rgba(0, 0, 0, .18);
\n    color: var(\-\-darkened_gray);
\n    font-weight: normal;
\n    margin: 24px 0 34px;
\n    padding: 23px 10px 26px;
\n    max-width: 600px;
\n}
\n
\n.form-contact_us ul::after {
\n    content: \'\';
\n    clear: both;
\n    display: table;
\n}
\n
\n.form-contact_us li {
\n    clear: none;
\n    float: left;
\n    width: 100%;
\n}
\n
\n.form-contact_us.form-contact_us ul li {
\n    clear: none;
\n    float: left;
\n    width: 100%;
\n    margin: 0;
\n    padding: 7px 10px 13px;
\n}
\n
\n.form-contact_us .form-row {
\n    margin-bottom: 1.5rem;
\n}
\n
\n.form-contact_us li > label:first-of-type {
\n    margin-bottom: 7px;
\n}
\n
\n.form-contact_us [type=\"date\"],
\n.form-contact_us [type=\"datetime-local\"],
\n.form-contact_us [type=\"email\"],
\n.form-contact_us [type=\"month\"],
\n.form-contact_us [type=\"number\"],
\n.form-contact_us [type=\"password\"],
\n.form-contact_us [type=\"search\"],
\n.form-contact_us [type=\"tel\"],
\n.form-contact_us [type=\"text\"],
\n.form-contact_us [type=\"time\"],
\n.form-contact_us [type=\"url\"],
\n.form-contact_us [type=\"week\"],
\n.form-contact_us select {
\n    min-height: 2.8125em;
\n    padding: 0 14px;
\n}
\n
\n.form-contact_us .form-input\-\-active .form-input\-\-pseudo-label {
\n    left: 14px;
\n}
\n
\n.form-contact_us select {
\n    color: var(\-\-darkened_gray);
\n}
\n
\n.form-contact_us [type=\"submit\"] {
\n    min-width: 11.875em;
\n}
\n
\n.form-contact_us textarea {
\n    min-height: 11.6875em;
\n    resize: none;
\n}
\n
\n.form-contact_us ::placeholder {
\n    color: var(\-\-darkened_gray);
\n    font-weight: 400;
\n}
\n
\n.formrt .form-checkbox-helper {
\n    border-radius: 2px;
\n    background: var(\-\-light_gray);
\n    border-color: var(\-\-main_gray);
\n    border-color: #dbdbdb;
\n}
\n
\n.form-contact_us [for=\"subscribe\"] {
\n    font-size: 14px;
\n    color: var(\-\-dark_gray);
\n}
\n
\n.contact_form-interested_in_schedule-li {
\n    display: none;
\n}
\n
\n.button\-\-continue {
\n    background-color: var(\-\-bright_purple);
\n    border-color: transparent;
\n    color: #fff;
\n}
\n
\n.button\-\-continue.inverse {
\n    background-color: #fff;
\n    border: 1px solid var(\-\-bright_purple);
\n    color: var(\-\-bright_purple);
\n}
\n
\n.button\-\-cancel {
\n    background: #fff;
\n    border: 1px solid #f00;
\n    color: #f00;
\n}
\n
\n .checkout-how-did-you-hear {
\n    color: var(\-\-darkened-gray);
\n}
\n.button\-\-pay {
\n    background-color: var(\-\-primary);
\n}
\n
\n.button\-\-pay.inverse {
\n    background: #FFF;
\n    border: 1px solid var(\-\-primary);
\n    color: var(\-\-primary);
\n}
\n
\n.button\-\-book {
\n    background-color: var(\-\-primary);
\n}
\n
\n.button\-\-book.inverse {
\n    background: #fff;
\n    border-color: var(\-\-primary);
\n    color: var(\-\-primary);
\n}
\n
\n.button\-\-book:disabled {
\n    background-color: #dbdbdb !important;
\n}
\n
\n.button\-\-book.inverse:disabled {
\n    background-color: #fff;
\n    border-color: var(\-\-darkened_gray);
\n    color: var(\-\-darkened_gray);
\n}
\n
\n.button\-\-send,
\n.btn-primary {
\n    background: var(\-\-bright_purple);
\n    color: #fff;
\n}
\n
\n.button\-\-send.inverse {
\n    background: #fff;
\n    border-color: var(\-\-bright_purple);
\n    color: var(\-\-bright_purple);
\n}
\n
\n.button\-\-enquire {
\n    background: var(\-\-primary);
\n    color: #fff;
\n}
\n.button\-\-brochure::after {
\n    content: var(\-\-arrow-down-dark_purple);
\n    margin-left: .5em;
\n    position: relative;
\n    top: 2px;
\n}
\n.button-full-brochure {
\n    color: var(\-\-main_gray)!important;
\n}
\n.button-full-brochure::after {
\n    content: var(\-\-arrow-down-main_gray-lg);
\n    margin-left: .5em;
\n    color: var(\-\-main_gray)!important;
\n    position: relative;
\n    top: .3em;
\n}
\n
\n.course-list-item-button  {
\n    background: none;
\n    border: 1px solid #333;
\n    color: #333;
\n}
\n
\n.course-list-item-button:hover {
\n    background: none;
\n}
\n
\n.header-action:nth-last-child(odd) .button {
\n    background: var(\-\-primary);
\n    color: #fff;
\n}
\n
\n.header-action:nth-last-child(even) .button,
\n.header-action.header-action\-\-login .button {
\n    background: var(\-\-bright_purple);
\n    color: #fff;
\n}
\n
\n.share_button {
\n    background: none;
\n    border: 1px solid #fff;
\n    border-radius: 0;
\n    box-shadow: none;
\n    color: #fff;
\n    display: block;
\n    font-size: 15px;
\n    height: auto;
\n    line-height: 1.666667;
\n    margin: 0 auto .5em;
\n    min-width: 203px;
\n    width: 203px;
\n    padding: .6em 2.444444em .6em 1em;
\n    position: relative;
\n    text-align: left;
\n    text-indent: 0;
\n    text-shadow: none;
\n}
\n
\n.share_button:before {
\n    display: none;
\n}
\n
\n.share_button:after {
\n    position: absolute;
\n    top: 0rem;
\n    right: .5em;
\n    font-size: 2em;
\n    line-height: 1.5;
\n    text-align: right;
\n}
\n
\n.share_button\-\-facebook:after { content: \'\\f09a\'; font-family: fontAwesome; }
\n.share_button\-\-twitter:after  { content: \'\\f099\'; font-family: fontAwesome; }
\n.share_button\-\-email:after    { content: \'\\f003\'; font-family: fontAwesome; }
\n
\na.share_button.share_button:hover {
\n    background: #fff;
\n    color: var(\-\-primary);
\n}
\n
\n@media screen and (max-width: 767px) {
\n    .page-content > .form-contact_us:first-child {
\n        margin-top: 30px;
\n    }
\n
\n    .form-contact_us.form-contact_us .contact_form-li\-\-country_code  { width: 50%; padding-top: 0; }
\n    .form-contact_us.form-contact_us .contact_form-li\-\-mobile_code   { width: 50%; padding-top: 0; }
\n    .form-contact_us.form-contact_us .contact_form-li\-\-mobile_number { padding-top: 7px; }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .formrt-raised.formrt-raised {
\n        position: relative;
\n        margin-top: -6.125rem;
\n    }
\n
\n    .form-contact_us {
\n        margin: 41px 0 69px;
\n        padding: 39px 30px 36px;
\n    }
\n
\n    .form-contact_us.form-contact_us .contact_form-li\-\-first_name    { width: 50%; }
\n    .form-contact_us.form-contact_us .contact_form-li\-\-last_name     { width: 50%; }
\n    .form-contact_us.form-contact_us .contact_form-li\-\-country_code  { width: 25%; padding-top: 0; }
\n    .form-contact_us.form-contact_us .contact_form-li\-\-mobile_code   { width: 25%; padding-top: 0; }
\n    .form-contact_us.form-contact_us .contact_form-li\-\-mobile_number { width: 50%; padding-top: 0; }
\n
\n    .share_button {
\n        display: inline-block;
\n        margin: 0 1.9em 0 0;
\n    }
\n}
\n
\n.header-left {
\n    display: flex;
\n    flex: 1
\n}
\n
\n.header-left .header-item:last-child {
\n    margin-left: auto;
\n}
\n
\n.header-actions {
\n    flex: unset;
\n    margin-left: 1rem;
\n}
\n
\n.header-action {
\n   padding-left: .5rem;
\n   padding-right: .5rem;
\n   text-transform: none;
\n}
\n
\n.header-actions .header-action a {
\n    border-radius: 0;
\n    font-size: .8rem;
\n    font-weight: 600;
\n    line-height: 1.25;
\n    padding: .5em 1.25em .375em;
\n}
\n
\n.header-actions > :last-child {
\n    padding-right: 0;
\n}
\n
\n.header-actions > :last-child > a:after {
\n    content: url(\'data:image/svg+xml; utf8, <svg width="27" height="20" viewBox="0 0 27 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M26.6667 9.48718L17.4359 0.25641C17.265 0.0854701 17.094 0 16.8376 0C16.6667 0 16.4103 0.0854701 16.2393 0.25641C16.0684 0.42735 16.0684 0.598291 15.9829 0.769231V0.854701C15.9829 1.02564 16.0684 1.28205 16.2393 1.36752L24.1026 9.23077H0.769231C0.34188 9.23077 0 9.57265 0 10C0 10.4274 0.34188 10.7692 0.769231 10.7692H24.188L16.4103 18.6325C16.0684 18.9744 16.0684 19.4872 16.4103 19.7436C16.5812 19.9145 16.7521 20 17.0085 20C17.1795 20 17.4359 19.9145 17.6068 19.7436L26.7521 10.5128C26.9231 10.3419 26.9231 9.82906 26.6667 9.48718Z" fill="white" /></svg>\');
\n    float: right;
\n    margin-left: .75em;
\n}
\n
\n.header-right {
\n    display: flex;
\n    padding: 0;
\n}
\n
\n.header-actions .header-item,
\n.header-right .header-item {
\n    margin: auto;
\n}
\n
\n.header-right .header-action:last-child {
\n    padding-right: 0;
\n}
\n
\n.header-menu-section\-\-account {
\n    display: none;
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .header-logo img {
\n        max-height: 60px;
\n    }
\n
\n    .sidebar-logo img[src*=\"svg\"],
\n    .header-logo img[src*=\"svg\"] {
\n        height: 60px;
\n    }
\n
\n    .header-item header-action {
\n        padding-top: 1.9em;
\n        padding-bottom: 1.9em;
\n    }
\n}
\n
\n.formErrorContent,
\n.formErrorArrow div {
\n    background: var(\-\-primary);
\n}
\n
\n\/\* Alerts \*\/
\n.alert-success {
\n    background-color: rgb(223, 240, 216);
\n    border-color: rgb(214, 233, 198);
\n    color: rgb(60, 118, 61);
\n}
\n
\n.alert-info {
\n    background-color: rgb(217, 237, 247);
\n    border-color: rgb(188, 232, 241);
\n    color: rgb(49, 112, 143);
\n}
\n
\n.alert-warning {
\n    background-color: rgb(252, 248, 227);
\n    border-color: rgb(250, 235, 204);
\n    color: rgb(138, 109, 59);
\n}
\n
\n.alert-danger {
\n    background-color: rgb(242, 222, 222);
\n    border-color: rgb(235, 204, 209);
\n    color: rgb(169, 68, 66);
\n}
\n
\n.popup_box { background-color: #fff; }
\n.popup_box.alert-success { border-color: var(\-\-bright_purple); color: var(\-\-bright_purple); }
\n.popup_box.alert-info    { border-color: #2472AC; }
\n.popup_box.alert-warning { border-color: #FCC14F; }
\n.popup_box.alert-danger,
\n.popup_box.alert-error   { border-color: #D74638; }
\n.popup_box.alert-add     { border-color: var(\-\-primary); }
\n.popup_box.alert-remove  { border-color: #b4b4b4; }
\n
\n.popup_box .alert-icon [fill]   {   fill: var(\-\-primary); }
\n.popup_box .alert-icon [stroke] { stroke: var(\-\-primary); }
\n
\n
\n\/\* Header \*\/
\n.header,
\n.mobile-breadcrumbs {
\n    background-color: #fff;
\n    color: var(\-\-primary);
\n}
\n
\n.mobile-breadcrumbs * { color: var(\-\-primary); }
\n
\n.header {
\n    padding: 0;
\n}
\n
\nbody:not(.has_banner) .header {
\n    border-bottom: 1px solid var(\-\-lighter_gray);
\n}
\n
\n.header > .row,
\n.header > div > .row {
\n   max-width: 1170px;
\n   padding-left: 15px;
\n   padding-right: 15px;
\n}
\n
\n.mobile-breadcrumbs {
\n    display: none;
\n}
\n
\n.header-top-nav {
\n    background: #1d1a3b;
\n    color: #fff;
\n}
\n
\n.header-top-nav a {
\n    color: #fff;
\n}
\n
\n.dropdown-menu-header {
\n    background-color: var(\-\-primary);
\n    color: #fff;
\n}
\n
\n.mobile-menu-toggle {
\n    color: var(\-\-dark_purple);
\n}
\n
\n.header-cart-button [fill] { fill: var(\-\-primary); }
\n.header-cart-button [stroke] { stroke: var(\-\-primary); }
\n
\n.header-menu.header-menu {
\n    background: var(\-\-primary);
\n    border-radius: 0;
\n    box-shadow: none;
\n    color: #fff;
\n    margin-top: 0;
\n}
\n
\n.header-menu.header-menu:not(.has_submenus) li {
\n    border-radius: 0;
\n}
\n
\n.header-menu.header-menu a,
\n.header-menu .level_1 > a {
\n    color: #fff;
\n    text-transform: none;
\n}
\n
\n.header-menu .level_2 a:hover,
\n.header-menu .level_2:hover > a {
\n    color: #fff;
\n}
\n
\n.header-item > a:not(.button) {
\n    color: #555;
\n    font-size: .8rem;
\n    font-weight: 600;
\n    line-height: 1.1111111111111;
\n    max-width: 8em;
\n    padding: 1.38888889em 1.1em;
\n    text-transform: none;
\n}
\n
\n.header-menu-section > a:after {
\n    display: none;
\n}
\n
\n.header-item > .header-menu-expand.expanded {
\n    background: var(\-\-primary);
\n    color: #fff;
\n}
\n
\n.header-menu-section > a:after {
\n    border-top-color: var(\-\-bright_purple);
\n}
\n
\n.header-menu-expand.expanded:after {
\n    border-top-color: #fff;
\n}
\n
\n.header-menu-expand > img {
\n    position: relative;
\n    top: -.5em;
\n}
\n
\n.header-menu-section > a {
\n    border: none;
\n    padding-left: 1em;
\n    padding-right: 1em;
\n}
\n
\n.header-item .button {
\n    border-radius: 2px;
\n    font-size: .75rem;
\n    line-height: 1.5;
\n    min-width: 100px;
\n    padding: .667em .9em;
\n}
\n
\n.mobile-menu .level_1 > a,
\n.mobile-menu .level_1 > button,
\n.mobile-menu-level3-section .mobile-menu-list > a {
\n    color: var(\-\-primary);
\n}
\n
\n.header-menu .level_2 a:before {
\n    border-left-color: var(\-\-primary);
\n}
\n
\n.header-menu .level_2 a:hover:before,
\n.header-menu .level_2:hover > a:hover {
\n    border-left-color: var(\-\-primary);
\n}
\n
\n.header-menu .level_2:not(.has_icon) a:before {
\n    color: #fff;
\n}
\n
\n.header-menu .level_3 {
\n    border-bottom-color: var(\-\-primary);
\n}
\n
\n.header-menu .level_2:not(.has_icon) a:before {
\n    background: #fff;
\n    border: 0;
\n    border-radius: 50%;
\n    width: .5em;
\n    height: .5em;
\n}
\n
\n.header-menu .level_1 > a {
\n    font-size: 1.5rem;
\n    line-height: 1.25;
\n}
\n
\n.header-menu .level_1:nth-child(1) { width: 44%; padding-right: 6.5rem; }
\n.header-menu .level_1:nth-child(2) { width: 35%; }
\n.header-menu .level_1:nth-child(3) { width: 21%; }
\n
\n.header-menu .level_1:nth-child(1) > ul > li,
\n.header-menu .level_1:nth-child(3) > ul > li {
\n    line-height: 1.25
\n}
\n
\n.header-menu .level_1:nth-child(1) > ul > li:last-child,
\n.header-menu .level_1:nth-child(3) > ul > li:last-child { margin-top: 2rem;}
\n
\n.header-menu .level_1:nth-child(1) > ul > li:last-child { font-weight: bold; }
\n
\n.header-menu li:nth-child(odd) > ul.level2 a {
\n    padding-left: 0;
\n}
\n
\n.header-menu li:nth-child(odd) > ul.level2 a:before {
\n    content: none;
\n}
\n
\n.mobile-menu-top strong,
\n.mobile-menu-top-avatar,
\n.mobile-menu-button-group-icon,
\n.header-cart-breakdown,
\n.final_price_value {
\n    color: var(\-\-primary);
\n}
\n
\n.header-cart-amount,
\n.mobile-menu li.active > a {
\n    color: var(\-\-primary);
\n}
\n
\n@media screen and (max-width: 767px) {
\n    .header-profile {
\n        color: var(\-\-dark_purple);
\n        display: block;
\n        margin: .125rem .5rem 0 0;
\n    }
\n
\n    .header > .row,
\n    .header-mobile.row {
\n        height: 60px;
\n    }
\n
\n    .header > .row {
\n        margin: 0 -5px;
\n        padding: 0;
\n    }
\n
\n    .header > .row > div:nth-child(2) {
\n        padding-left: 0;
\n        padding-right: 0;
\n    }
\n
\n    .header-logo img {
\n        max-height: 45px;
\n    }
\n
\n    .level_1.level_1.has_submenu.expanded {
\n        border-bottom: none;
\n        margin-bottom: 0;
\n    }
\n
\n    .level_1[data-menu=\"header\"] {
\n        text-align: center;
\n    }
\n
\n    .level_1.level_1[data-menu=\"header\"] a {
\n        background: var(\-\-bright_purple);
\n        border-radius: .25rem;
\n        color: #fff;
\n        display: inline-block;
\n        margin-top: .5rem;
\n    }
\n
\n    .level_1[data-menu=\"header\"] a:after {
\n        content: var(\-\-arrow-white);
\n        margin-left: .75em;
\n        position: relative;
\n        top: .125em;
\n    }
\n
\n    .mobile-menu .level2 .submenu-expand {
\n        display: none;
\n    }
\n
\n    \/\* Quick Contact \*\/
\n    .quick_contact {
\n        background: var(\-\-primary);
\n        color: #fff;
\n    }
\n
\n    .quick_contact a {
\n        color: #fff;
\n    }
\n
\n    .quick_contact-item-icon {
\n        font-size: 1.5rem;
\n}
\n
\n    .quick_contact-item > a.active,
\n    .quick_contact-item > a:hover,
\n    .quick_contact-item > a:active {
\n        color: #fff;
\n    }
\n}
\n
\n\/\* Sidebar \*\/
\n.sidebar-section > h2 {
\n    color: var(\-\-dark_gray);
\n    line-height: 1.25;
\n    margin-bottom: .83333em;
\n    margin-top: -.375em;
\n    padding: 0;
\n    text-align: left;
\n    text-transform: none;
\n}
\n
\n.search-filter-list {
\n    letter-spacing: .055em;
\n}
\n
\n.sidebar-section-collapse {
\n    display: none;
\n}
\n
\n.sidebar-section .form-input,
\n.sidebar-section .input_group {
\n    background: none;
\n    border-color: var(\-\-main_gray);
\n}
\n
\n.sidebar-section .form-input {
\n    letter-spacing: 0.06em;
\n    padding: .5em .5em .5em .75em;
\n}
\n
\n.sidebar-section .form-input::-webkit-input-placeholder { color: var(\-\-main_gray); }
\n
\n.course-filter-keyword-input-wrapper {
\n    margin-top: 0;
\n}
\n
\n.sidebar-section .input_group,
\n.sidebar-section .input_group-icon {
\n    border-radius: 0;
\n}
\n
\n.sidebar-section .input_group-icon {
\n    background: var(\-\-dark_purple);
\n    color: #fff;
\n}
\n
\n.sidebar-section-content ul {
\n    padding-left: 0;
\n    padding-right: 0;
\n}
\n
\n.sidebar-news-list li {
\n    border-bottom: 1px solid #bfbfbf;
\n    padding: .4em 1.5em .15em;
\n    margin-bottom: 1em;
\n}
\n
\na.sidebar-news-link,
\n.eventTitle {
\n    color: var(\-\-bright_purple);
\n}
\n
\n.search-criteria-remove .fa {
\n    color: #f60000;
\n}
\n
\n.sidebar-section li {
\n    font-size: .875rem;
\n}
\n
\n.sidebar-filter-li .form-checkbox {
\n    font-size: inherit;
\n    margin-right: 0;
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .sidebar {
\n        max-width: 277px;
\n    }
\n
\n    .sidebar + .content_area {
\n        padding-left: 112px;
\n        width: calc(100% - 277px);
\n    }
\n}
\n
\n\/\* Page content \*\/
\n.row.page-content {
\n    max-width: 1170px;
\n}
\n
\n.content-area {
\n    font-weight: 400;
\n}
\n
\n.page-content h1,
\n.page-content h2,
\n.page-content h3,
\n.page-content h4,
\n.page-content h5,
\n.page-content h6 {
\n    border: none;
\n}
\n
\n.page-content p {
\n    color: inherit;
\n}
\n
\n.page-content li {
\n    margin: 1.375em 0;
\n}
\n
\n.page-content ul > li:before {
\n    content: \'\';
\n    background: var(\-\-bright_purple);
\n    border-radius: 50%;
\n    display: block;
\n    position: absolute;
\n    top: .4375em;
\n    width: .5em;
\n    height: .5em;
\n}
\n
\n.page-content ul > li {
\n    padding-left: 1.25em;
\n}
\n
\n.page-content a:not([class]),
\n.page-content .button\-\-link {
\n    color: var(\-\-primary);
\n}
\n
\n.page-content .ib-video-wrapper + p,
\n.page-content .ib-audio-wrapper + p {
\n    margin-top: 2rem;
\n}
\n
\n.read_more::after,
\n.read_more-lg::after {
\n    content: var(\-\-arrow-bright_purple);
\n    margin-left: .75em;
\n    position: relative;
\n    top: 2px;
\n}
\n
\n.read_more-lg-arrow::after {
\n     content: var(\-\-arrow-bright_purple-lg);
\n     margin-left: .75em;
\n     position: relative;
\n     top: 0.5em;
\n}
\n
\n.read_more-lg {
\n    font-size: 1.5rem;
\n}
\n
\n.read_more-lg::after {
\n    content: var(\-\-arrow-bright_purple-lg);
\n    top: 4px;
\n}
\n
\na.read_more,
.read_more-lg-arrow,
\na.read_more-lg { color: var(\-\-bright_purple); }
\n
\n.button.read_more:after {
\n    content: var(\-\-arrow-white);
\n}
\n
\n.read_more.text-bright_pink::after { content: var(\-\-arrow-bright_pink); }
\n.read_more.text-dark_purple::after { content: var(\-\-arrow-dark_purple); }
\n.read_more.text-white::after { content: var(\-\-arrow-white); }
\n.read_more-lg-arrow.text-white::after { content: var(\-\-arrow-white-lg); }
\n.read_more-lg.text-bright_pink::after { content: var(\-\-arrow-bright_pink-lg); }
\n.read_more-lg.text-dark_purple::after { content: var(\-\-arrow-dark_purple-lg); }
\n.read_more-lg.text-white::after { content: var(\-\-arrow-white-lg); }
\n
\n.download_pdf::before {
\n    content: \'\\f1c1\';
\n    font-family: FontAwesome;
\n    font-size: 1.25em;
\n    margin-right: .75em;
\n}
\n
\n.download_pdf::after {
\n    content: var(\-\-arrow-white);
\n    transform: rotate(90deg);
\n    display: inline-block;
\n    margin-left: .25em;
\n}
\n
\n.banner-overlay-content .button,
\n.page-content .button {
\n    font-weight: bold;
\n    padding: 1em;
\n}
\n
\n.page-content a:not([class]):visited {
\n    color: #551a8b;
\n}
\n
\n.banner-overlay-content a {
\n    color: var(\-\-bright_pink);
\n}
\n
\n.banner-overlay-content a.read_more:after {
\n    content: var(\-\-arrow-bright_pink-lg);
\n    margin-left: 1em;
\n    top: .2222em;
\n}
\n
\n.page-content header {
\n    font-weight: normal;
\n}
\n
\n.page-content hr {
\n    border-color: var(\-\-primary);
\n}
\n
\n.page-content strong {
\n    font-weight: bold;
\n}
\n
\n.page-content figure {
\n    background: var(\-\-lighter_gray);
\n    margin: 2rem 0 1.6875rem;
\n}
\n
\n.page-content figure img {
\n    display: block;
\n}
\n
\n.page-content figcaption {
\n    font-size: .75rem;
\n    line-height: 2;
\n    padding: .33333em 1.25em .58333333em;
\n}
\n
\n.simplebox-icons .simplebox-content > :first-child{margin-top: 0;}
\n.simplebox-icons .simplebox-content > :last-child{margin-bottom: 0;}
\n
\n.simplebox-icons .simplebox .simplebox-column { margin: 0; }
\n
\n.additional_features .simplebox-content {
\n    box-shadow: 0 4px 35px rgba(0, 0, 0, 0.18);
\n    margin-bottom: 30px;
\n    padding: 25px 20px 28px;
\n}
\n
\n.additional_features h3 {
\n    margin-bottom: 18px;
\n}
\n
\n.additional_features.additional_features p {
\n    margin-bottom: 12px;
\n}
\n
\n.additional_features .simplebox-content > :first-child { margin-top: 0; }
\n.additional_features .simplebox-content > :last-child { margin-bottom: 0; }
\n
\n.additional_features .read_more::after {
\n    content: var(\-\-arrow-bright_purple-lg);
\n    top: 7px;
\n}
\n
\n.our_approach.simplebox {
\n    padding-top: 22px;
\n    padding-bottom: 89px;
\n}
\n.our_approach .simplebox-columns,
\n.our_approach .simplebox-title  {
\n    max-width: 1380px;
\n    padding-left: 15px;
\n    padding-right: 15px;
\n}
\n
\n.our_approach .simplebox-title {
\n    margin-bottom: 30px
\n}
\n
\n.our_approach h2 { font-size: 35px; line-height: 44px; }
\n
\n.our_approach .simplebox-columns { color: #fff; }
\n.our_approach .simplebox-content { padding: 27px 30px 35px 30px;}
\n.our_approach .simplebox-content h2 { margin-bottom: 0.75rem!important; }
\n.our_approach .simplebox-content p { font-size: 20px; line-height: 25px!important; margin: 0 0 2rem;}
\n.our_approach .simplebox-content > :first-child { margin-top: 0; }
\n.our_approach .simplebox-content > :list-child {margin-bottom: 0;}
\n
\n.our_approach .simplebox-columns > :nth-child(4n+1)  { background: var(\-\-bright_purple);}
\n.our_approach .simplebox-columns > :nth-child(4n+2)  { background: var(\-\-dark_purple); color: var(\-\-main_gray)}
\n.our_approach .simplebox-columns > :nth-child(4n+3)  { background: var(\-\-main_gray);}
\n.our_approach .simplebox-columns > :nth-child(4n+4)  { background: var(\-\-dark_pink); color: var(\-\-main_gray)}
\n
\n.our_approach .simplebox-columns > :nth-child(4n+1) h2 { color: var(\-\-dark_purple);}
\n.our_approach .simplebox-columns > :nth-child(4n+2) h2 { color: var(\-\-bright_purple);}
\n.our_approach .simplebox-columns > :nth-child(4n+3) h2 { color: var(\-\-dark_pink);}
\n.our_approach .simplebox-columns > :nth-child(4n+4) h2 { color: var(\-\-bright_pink);}
\n
\n
\n.simplebox.why_us {
\n    margin-top: 36px;
\n    padding-top: 27px;
\n    padding-bottom: 70px;
\n}
\n
\n.why_us .simplebox-title {
\n    margin-bottom: 26px
\n}
\n
\n.why_us .simplebox-column {
\n    box-shadow: 0 4px 35px rgba(0, 0, 0, .18);
\n}
\n
\n.simplebox.why_us h2 {
\n    font-size: 35px;
\n}
\n
\n.simplebox.why_us h3 {
\n    font-size: 35px;
\n    line-height: 1.26666667;
\n    margin-bottom: 15px;
\n}
\n
\n
\n.why_us .simplebox-content {
\n    padding: 22px 32px 20px 30px;
\n}
\n
\n.why_us.why_us .simplebox-content p {
\n    line-height: 1.25
\n}
\n
\n.why_us .simplebox-content > :first-child { margin-top: 0; }
\n.why_us .simplebox-content > :last-child { margin-bottom: 0; }
\n
\n.why_us .simplebox-column:nth-child(4n+1) { background: var(\-\-bright_purple); color: #fff; }
\n.why_us .simplebox-column:nth-child(4n+2) { background: var(\-\-dark_purple);   color: #fff; }
\n.why_us .simplebox-column:nth-child(4n+3) { background: var(\-\-main_gray);     color: #fff; }
\n.why_us .simplebox-column:nth-child(4n+4) { background: var(\-\-dark_pink);     color: #fff; }
\n
\n.why_us .simplebox-column:nth-child(4n+1) h3 { color: var(\-\-dark_purple);   }
\n.why_us .simplebox-column:nth-child(4n+2) h3 { color: var(\-\-bright_purple); }
\n.why_us .simplebox-column:nth-child(4n+3) h3 { color: var(\-\-dark_purple);   }
\n.why_us .simplebox-column:nth-child(4n+4) h3 { color: var(\-\-bright_pink);   }
\n
\n.why_us .simplebox-content { display: flex; flex-direction: column; }
\n
\n.why_us .simplebox-content > :last-child {
\n     margin-top: auto;
\n     margin-bottom: 0;
\n}
\n
\n.why_us .simplebox-column .read_more {
\n    color: #fff;
\n    float: right;
\n    font-size: 18px;
\n    margin-top: 1rem;
\n}
\n
\n.why_us .simplebox-column .read_more:after {
\n    content: var(\-\-arrow-white-lg);
\n    top: .35em;
\n}
\n
\n@media screen and (min-width: 768px) and (max-width: 1023px) {
\n    .simplebox.why_us .simplebox-columns,
\n    .simplebox-spotlights2 .simplebox-columns,
\n    .our_approach .simplebox-columns {
\n        flex-wrap: wrap;
\n        margin-left: -.5rem;
\n        margin-right: -.5rem;
\n    }
\n
\n    .simplebox.why_us .simplebox-column,
\n    .simplebox-spotlights2 .simplebox-column,
\n    .our_approach .simplebox-column {
\n        margin: .5rem;
\n        width: calc(50% - 1rem);
\n    }
\n}
\n
\n.simplebox-panel {
\n    min-height: 360px;
\n    display: flex;
\n    flex-direction: column;
\n    position: relative;
\n    color: var(\-\-main_gray);
\n}
\n
\n.simplebox-panel .simplebox-title {
\n    margin: auto;
\n    z-index: 1;
\n}
\n
\n.simplebox-panel .simplebox-columns {
\n    position: absolute;
\n    top: 0;
\n    right: 0;
\n    bottom: 0;
\n    left: 0
\n}
\n
\n.simplebox-panel .read_more,
\n.simplebox-panel .read_more-lg {
\n    color: #fff;
\n    font-size: 18px;
\n    position: absolute;
\n    bottom: 16.5%;
\n    right: 0;
\n}
\n
\n.simplebox-panel h2 { margin: 0; }
\n
\n.simplebox-panel .read_more::after {
\n    content: var(\-\-arrow-white-lg);
\n    top: 7px;
\n}
\n
\n.simplebox-video {
\n    background-color: #e5e2ef;
\n    padding: 6px 21px 14px;
\n}
\n
\n.simplebox-download_brochure {
\n    background: var(\-\-dark_purple);
\n    color: #fff;
\n}
\n
\n@media screen and (max-width: 767px) {
\n    .row.page-content {
\n        padding-left: 20px;
\n        padding-right: 20px;
\n    }
\n
\n    .simplebox {
\n        margin-left:  -24px;
\n        margin-right: -24px;
\n        padding-left:  20px;
\n        padding-right: 20px;
\n    }
\n
\n    .simplebox.additional_features {
\n        margin-bottom: 14px;
\n    }
\n
\n    .simplebox.additional_features .simplebox-title {
\n        margin-top: 27px;
\n        margin-bottom: 8px;
\n        padding: 0 5px;
\n    }
\n
\n    .additional_features .simplebox-columns {
\n        padding: 0 1px;
\n    }
\n
\n    .simplebox.additional_features .simplebox-title h2 {
\n        line-height: 1.25714285714;
\n    }
\n
\n    .additional_features .simplebox-content {
\n        margin-bottom: 0;
\n    }
\n
\n    .additional_features .simplebox-content h3 {
\n        margin-bottom: 13px;
\n    }
\n
\n    .additional_features .simplebox-content p {
\n        line-height: 1.5;
\n        margin-bottom: 7px;
\n    }
\n
\n    .simplebox.why_us.why_us.why_us {
\n        margin-bottom: 0;
\n        padding-bottom: 0;
\n    }
\n
\n    .simplebox.why_us .simplebox-title,
\n    .simplebox.why_us .simplebox-columns {
\n        padding-left: 7px;
\n        padding-right: 3px;
\n    }
\n
\n    .simplebox.why_us .simplebox-title h2 {
\n        margin-bottom: 30px;
\n    }
\n
\n    .simplebox.why_us.why_us.why_us.why_us .simplebox-column {
\n        margin-bottom: 30px;
\n        padding: 4px 0 11px;
\n    }
\n
\n    .page-content .simplebox.simplebox-panel.simplebox-panel {
\n        margin-bottom: 0;
\n    }
\n
\n    .simplebox-panel .read_more {
\n        right: 1.5rem;
\n        bottom: 1.875rem;
\n    }
\n
\n    .simplebox-accredited2 {
\n        padding: 24px 26px 15px;
\n    }
\n
\n    .simplebox-accredited2 h1 {
\n        font-size: 24px;
\n        margin: 0;
\n    }
\n
\n    .simplebox-directors h1 {
\n        font-size: 48px;
\n        line-height: 1.27;
\n    }
\n
\n    .simplebox-video .simplebox-column-1 {
\n        padding-bottom: 0;
\n    }
\n
\n    .simplebox-video h2 {
\n        margin-bottom: 1rem;
\n    }
\n
\n    .simplebox-video p {
\n        margin-bottom: 9px;
\n    }
\n
\n    .simplebox.our_approach {
\n        padding: 25px 10px 0;
\n    }
\n
\n    .our_approach .simplebox-column {
\n        margin-bottom: 30px;
\n        padding: 0 !important; /* todo: make the selector this is overwriting less-specific */
\n    }
\n
\n    .our_approach .simplebox-content {
\n        min-height: 320px;
\n        padding-right: 35px
\n    }
\n
\n    .simplebox-download_brochure {
\n        background-size: cover;
\n        min-height: 400px;
\n        padding: 6px 22px;
\n    }
\n
\n    .simplebox-download_brochure h1 {
\n        font-size: 35px;
\n        line-height: 1.28571428571;
\n        margin-top: 0;
\n        max-width: 290px;
\n    }
\n
\n    .simplebox-download_brochure h2 {
\n        font-size: 1.5rem;
\n        line-height: .5;
\n        margin-top: 47px;
\n        max-width: 290px;
\n    }
\n
\n    .button-full-brochure::after { top: .7em; }
\n}
\n
\n@media screen and (min-width: 768px) and (max-width: 1177px){
\n    .row.page-content {
\n        padding-left: 25px;
\n        padding-right: 15px;
\n    }
\n
\n    .simplebox-panel {
\n        min-height: 526px;
\n    }
\n
\n    .simplebox.additional_features {
\n        padding-bottom: 38px;
\n    }
\n
\n    .simplebox.additional_features .simplebox-title {
\n        margin-top: 49px;
\n    }
\n
\n    .additional_features .simplebox-content {
\n        padding: 35px;
\n    }
\n
\n    .simplebox.why_us {
\n        margin-top: 59px;
\n        padding-top: 24px;
\n    }
\n
\n.why_us .simplebox-column:nth-child(4n+2),
\n.why_us .simplebox-column:nth-child(4n+4) {
\n    color: var(\-\-main_gray);
\n}
\n
\n    .simplebox.why_us h3 {
\n        font-size: 30px;
\n    }
\n
\n    .simplebox-accredited2 {
\n        padding: 70px 0 38px;
\n    }
\n
\n    .simplebox-accredited2 h1 {
\n        margin: 0;
\n        text-align: center;
\n    }
\n
\n    .simplebox-video {
\n        padding: 50px 0;
\n    }
\n
\n    .our_approach .simplebox-title {
\n        margin-bottom: 25px;
\n    }
\n
\n    .simplebox-download_brochure {
\n        min-height: 533px;
\n        padding-top: 80px;
\n    }
\n
\n    .simplebox-download_brochure h2 {
\n        margin-top: 53px;
\n    }
\n}
\n
\n.simplebox-featured .simplebox-columns {
\n    align-items: flex-start;
\n    max-width: 960px;
\n    background: #fff;
\n    box-shadow: 0px 4px 35px rgba(0, 0, 0, 0.18);
\n    padding: 5px 20px;
\n}
\n
\n.simplebox-featured .simplebox-content > :first-child { margin-top: 0; }
\n.simplebox-featured .simplebox-content > :last-child { margin-bottom: 0; }
\n
\n.simplebox-featured .simplebox-content h1 { margin-bottom: 19px; }
\n.simplebox-featured h3 {margin-top:0;margin-bottom: 19px; }
\n
\nul.accordion-basic > li {
\n    margin: 1.25em 0;
\n    padding: 0;
\n    border: 0;
\n}
\n
\n.accordion-basic.accordion-basic h3 {
\n    background: var(\-\-light_gray);
\n    padding: 25px 30px;
\n}
\n
\n.accordion-basic h3::after {
\n    background-image: linear-gradient(var(\-\-bright_purple),var(\-\-bright_purple)), linear-gradient(var(\-\-bright_purple),var(\-\-bright_purple));
\n    background-position:center;
\n    background-size: 50% 3px, 3px 50%;
\n    background-repeat: no-repeat;
\n    border: none;
\n    display: inline-block;
\n    transform: none;
\n    top: 1rem;
\n    right: 1.25rem;
\n    width: 50px;
\n    height: 50px;
\n}
\n
\n.accordion-basic h3.active::after {
\n    background-size: 50% 3px, 0;
\n    transform: none;
\n}
\n
\n.accordion-basic h3.active ~ *  {
\n    margin-left: 50px;
\n    margin-right: 50px;
\n}
\n
\n.accordion-basic h3.active ~ :nth-child(2)  {
\n    margin-top: 29px;
\n}
\n
\n.accordion-basic h3.active ~ :last-child  {
\n    margin-bottom: 60px;
\n}
\n
\n.accordion-basic li:last-child {
\n    border: none;
\n    margin-bottom: 70px;
\n}
\n
\n@media screen and (max-width: 767px) {
\n    body:not(.has_banner) .content {
\n        margin-top: 30px;
\n    }
\n
\n    .page-content {
\n        line-height: 1.25;
\n    }
\n
\n    .page-content p {
\n        margin: 0 0 1.25rem;
\n   }
\n
\n    .banner-overlay-content .button,
\n    .page-content .button {
\n        font-size: 13px;
\n        min-width: 170px;
\n    }
\n
\n    .page-content .simplebox.team-section.team-section {
\n        margin-bottom: 0;
\n     }
\n
\n    .team-section .simplebox-columns {
\n        margin-left: -.75em;
\n        margin-right: -.75em
\n    }
\n
\n    .team-section .simplebox-column {
\n        padding: 0 .75em !important;
\n    }
\n
\n    .team-section .simplebox-content div > * {
\n        margin: 0;
\n    }
\n
\n    .team-section p {
\n        font-size: 9px;
\n        font-weight: normal;
\n        line-height: 2;
\n    }
\n
\n    .team-section.team-section.team-section .simplebox-column {
\n         width: 50% !important;
\n    }
\n
\n    .team-section .simplebox-column .simplebox-content img {
\n        width: 100% !important;
\n    }
\n
\n    .page-content .simplebox-icons:not(:last-child) {margin-bottom: 0;}
\n    .page-content .simplebox-icons + .simplebox-icons:not(:last-child) {margin-bottom: 30px;}
\n
\n    .simplebox-icons img { max-width: 40px; }
\n    .simplebox-icons h5 { margin-bottom: .5em; }
\n    .simplebox-icons .simplebox-title { text-align: center;}
\n    .simplebox-icons .simplebox.simplebox .simplebox-column-1 { width: 47px !important; }
\n    .simplebox-icons .simplebox.simplebox .simplebox-column-2 { width: calc(100% - 47px) !important; }
\n
\n    .simplebox-featured {
\n        padding: 30px 25px;
\n    }
\n
\n    .simplebox-featured .simplebox-title {
\n        margin-bottom: 22px;
\n    }
\n
\n    .simplebox-featured h3 {
\n        margin-top: 16px;
\n        margin-bottom: 9px;
\n    }
\n
\n    .accordion-basic.accordion-basic h3 {
\n        font-size: 18px;
\n        padding: 15px 20px;
\n    }
\n
\n    .accordion-basic h3.active ~ * {
\n        margin-left: 20px;
\n        margin-right: 20px;
\n    }
\n
\n    .accordion-basic h3::after {
\n        top: .25em;
\n        right: .5rem;
\n    }
\n
\n    ul.accordion-basic > li {
\n        margin: 10px 0;
\n    }
\n
\n    .accordion-basic li:last-child {
\n        margin-bottom: 40px;
\n    }
\n
\n    .page-intro {
\n        padding-left: 3px;
\n        padding-right: 3px;
\n    }
\n
\n    .page-intro h2 {
\n        margin-top: 26px;
\n        margin-bottom: 19px;
\n    }
\n
\n    .page-intro p {
\n         line-height: 1.5;
\n    }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .page-content {
\n        font-size: 1rem;
\n        line-height: 1.25;
\n    }
\n
\n    .page-content p {
\n        font-size: 1rem;
\n        margin: 0 0 1.6rem;
\n    }
\n
\n    .banner-overlay-content .button,
\n    .page-content .button {
\n        font-size: 1rem;
\n        min-width: 200px;
\n        min-width: min(200px, 100%);
\n    }
\n
\n    .team-section .simplebox-column {
\n         margin-bottom: 10px;
\n    }
\n
\n    .simplebox-icons img { max-width: 90px; }
\n    .simplebox-icons .simplebox-title { text-align: left;}
\n    .simplebox-icons .simplebox.simplebox .simplebox-column-1 { width: 90px; }
\n    .simplebox-icons .simplebox.simplebox .simplebox-column-2 { width: calc(100% - 90px); }
\n
\n    .simplebox-overlap-right .simplebox-column:first-child .simplebox-content {
\n        padding: 35px 40px;
\n    }
\n
\n    .simplebox-overlap-right .simplebox-column:first-child { width: 32.6%; }
\n    .simplebox-overlap-right .simplebox-column:last-child  { width: 67.4%; }
\n
\n    .simplebox-featured {
\n        padding-top: 75px;
\n        padding-bottom: 95px;
\n    }
\n
\n    .simplebox-featured .simplebox-columns {
\n        padding: 35px 30px;
\n    }
\n
\n    .page-intro h2 {
\n        margin-bottom: 1rem;
\n    }
\n
\n    .page-intro p {
\n        font-size: 1.25rem;
\n        font-weight: normal;
\n        line-height: 1.2;
\n    }
\n}
\n
\n\/\* Banner search \*\/
\n.banner-search-title {
\n    background: #0E918D;
\n    color: #fff;
\n}
\n
\n.banner-search form {
\n    background: var(\-\-primary);
\n}
\n
\n.banner-search .form-input {
\n   color: var(\-\-primary);
\n}
\n
\n.previous_search_text {
\n    color: #fff;
\n}
\n
\n.banner-overlay-content {
\n    color: #fff;
\n    font-size: 1.5rem;
\n    line-height: 1.25;
\n}
\n
\n.banner-overlay-content h1 { color: #fff; }
\n
\n.simplebox-raised .simplebox-content > :first-child,
\n.banner-overlay-content > :first-child {
\n    margin-top: 0;
\n}
\n
\n.simplebox-raised .simplebox-content > :last-child,
\n.banner-overlay-content > :last-child {
\n    margin-bottom: 0;
\n}
\n
\n.banner-overlay-content .button {
\n    min-width: 0;
\n    padding: .84em 2.65em;
\n}
\n
\n.banner-slide\-\-left .banner-overlay .row {
\n    background-image: url(\'\/media\/photos\/content\/banner_overlay_left.png\');
\n    background-position: left;
\n    background-repeat: no-repeat;
\n}
\n
\n.banner-slide\-\-right .banner-overlay .row {
\n    background-image: url(\'\/media\/photos\/content\/banner_overlay_right.png\');
\n    background-position: right;
\n    background-repeat: no-repeat;
\n}
\n
\n@media screen and (max-width: 767px) {
\n    .banner-image.banner-image\-\-mobile { display: block; }
\n    .banner-image.banner-image\-\-desktop { display: none;}
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .banner-image.banner-image\-\-mobile { display: none; }
\n    .banner-image.banner-image\-\-desktop { display: block; }
\n}
\n
\n.has_category_color .banner-image {
\n    background-color: var(\-\-category-color);
\n    background-blend-mode: multiply;
\n}
\n
\n.simplebox-overlap-left .simplebox-column:last-child .simplebox-content,
\n.simplebox-overlap-right .simplebox-column:first-child .simplebox-content {
\n    box-shadow: 0 1.125rem 5rem rgba(0, 0, 0, .18);
\n}
\
\n .layout-content_wide .simplebox-overlap-left .simplebox-content {
\n     padding-bottom: 3rem;
\n     margin-bottom: -2rem!important;
\n     padding-top: 1.375rem;
\n}
\n .layout-content_wide .simplebox-content h2 {
\n      margin-bottom: 1.5rem;
\n}
\n .layout-content_wide .simplebox-content p {
\n      line-height: 1.5625;
\n      font-weight: 400;
\n}
\n .layout-content_wide .simplebox.simplebox-success .simplebox-content{
\n       padding-top: 0!important;
\n       padding-bottom: 0!important;
\n}
\n .layout-content_wide .simplebox.simplebox-success .simplebox-columns{
\n    padding-top: 2.1rem
\n}
\n .layout-content_wide .simplebox.simplebox-success .simplebox-content p:last-child {
\n    margin-bottom: 0.5rem;
\n}
\n .layout-content_wide .simplebox.simplebox-success h2 {
\n    color: var(\-\-main_gray);
\n}
\n
\n.background-extended {
\n    position: relative;
\n}
\n
\n.background-extended:before {
\n    content: \'\';
\n    background: #f6f6f6;
\n    position: absolute;
\n    top: -50%;
\n    right: 0;
\n    bottom: -50%;
\n    left: 0;
\n    z-index: -1;
\n}
\n
\n.simplebox.spotlights {
\n    margin-bottom: 1.875rem;
\n}
\n
\n.spotlights .simplebox-column {
\n    \-\-bg_color: var(\-\-bright_purple);
\n    \-\-text_color: #fff;
\n    \-\-heading_color: var(\-\-dark_purple);
\n    \-\-arrow: var(\-\-arrow-white);
\n}
\n
\n.spotlights .simplebox-column:nth-child(3n + 2) {
\n    \-\-bg_color: var(\-\-dark_purple);
\n    \-\-text_color: var(\-\-main_gray);
\n    \-\-heading_color: var(\-\-bright_purple);
\n    \-\-arrow: var(\-\-arrow-main_gray);
\n}
\n
\n.spotlights .simplebox-column:nth-child(3n) {
\n    \-\-bg_color: var(\-\-dark_pink);
\n    \-\-text_color: var(\-\-main_gray);
\n    \-\-heading_color: var(\-\-bright_pink);
\n    \-\-arrow: var(\-\-arrow-main_gray);
\n}
\n
\n.spotlights .simplebox-column.simplebox-column {
\n    background: var(\-\-heading_color);
\n    color: var(\-\-text_color);
\n}
\n
\n.spotlights .simplebox-content {
\n    background: var(\-\-bg_color);
\n    border-radius: 0 0 2.5rem 0;
\n    display: flex;
\n    flex-direction: column;
\n    min-height: 210px;
\n    padding: 1.375rem 1.5rem 1.375rem 1.875rem;
\n}
\n
\n.spotlights .simplebox-content > :last-child {
\n    margin-top: auto;
\n}
\n
\n.spotlights a {
\n    color: inherit;
\n}
\n
\n.spotlights .simplebox-column h2 {
\n    color: var(\-\-heading_color);
\n    letter-spacing: -.01em;
\n    margin: 0;
\n}
\n
\n.spotlights .read_more:after {
\n    content: var(\-\-arrow);
\n}
\n
\n.layout-course_detail2 .spotlights {
\n    padding-top: 70px;
\n}
\n
\n.layout-course_detail2 .spotlights .simplebox-column {
\n    padding-right: 25px;
\n    \-\-arrow:var(\-\-arrow-white-lg);
\n}
\n
\n.layout-course_detail2 .spotlights .simplebox-content {
\n    padding-left: 50px;
\n    padding-right: 60px;
\n    padding-bottom: 90px;
\n}
\n
\n.layout-course_detail2 .spotlights h1 {
\n    line-height: 1.15;
\n    margin-bottom: 2rem;
\n}
\n
\n.layout-course_detail2 .spotlights h2 {
\n    margin-bottom: 30px;
\n}
\n
\n.layout-course_detail2 .spotlights a {
\n    text-decoration: none;
\n}
\n
\n.layout-course_detail2 .spotlights .read_more { font-size: 18px;}
\n.layout-course_detail2 .spotlights .read_more::after { top: .33333em;}
\n
\n
\n.layout-course_detail2 .spotlights .simplebox-column:nth-child(odd) {background-color: var(\-\-bright_purple);}
\n.layout-course_detail2 .spotlights .simplebox-column:nth-child(odd) .simplebox-content {background-color: var(\-\-dark_purple);}
\n.layout-course_detail2 .spotlights .simplebox-column:nth-child(odd) h1 {color: var(\-\-bright_purple);}
\n.layout-course_detail2 .spotlights .simplebox-column:nth-child(odd) h2 {color: var(\-\-main_gray);}
\n.layout-course_detail2 .spotlights .simplebox-column:nth-child(odd) a {color: #fff;}
\n
\n.layout-course_detail2 .spotlights .simplebox-column:nth-child(even) {background-color: var(\-\-dark_purple);}
\n.layout-course_detail2 .spotlights .simplebox-column:nth-child(even) .simplebox-content {background-color: var(\-\-bright_purple);}
\n.layout-course_detail2 .spotlights .simplebox-column:nth-child(even) h1 {color: var(\-\-dark_purple);}
\n.layout-course_detail2 .spotlights .simplebox-column:nth-child(even) h2 {color: #fff; }
\n.layout-course_detail2 .spotlights .simplebox-column:nth-child(even) a {color: #fff}
\n
\n.why-choose-us h1 {
\n    color: var(\-\-dark_purple);
\n}
\n
\n@media screen and (max-width: 767px) {
\n    .simplebox.simplebox-raised.spotlights {
\n        margin: 1.875rem -44px 0;
\n    }
\n
\n    .spotlights .simplebox-column.simplebox-column {
\n        padding: 0 .625rem 0 0;
\n    }
\n
\n    .simplebox.spotlights {
\n        padding-left: 24px;
\n        padding-right: 24px;
\n    }
\n
\n    [class*=\"layout-course\"] .spotlights {
\n        padding-top: 30px;
\n        padding-left: 23px
\n    }
\n
\n    [class*=\"layout-course\"] .spotlights .simplebox-column {
\n        padding: 0 10px 0 1px !important;
\n        margin-bottom: 30px;
\n    }
\n
\n    [class*=\"layout-course\"] .spotlights .simplebox-content {
\n        padding: 23px 20px 15px;
\n    }
\n
\n    [class*=\"layout-course\"] .spotlights h1 {
\n        font-size: 35px;
\n        line-height: 1.25714285714;
\n        margin: 0 0 27px;
\n    }
\n
\n    [class*=\"layout-course\"] .spotlights h2 {
\n        font-size: 1.5rem;
\n        line-height: 1.25;
\n        margin-bottom: 28px;
\n    }
\n
\n    .why-choose-us .simplebox-columns {
\n        flex-direction: column;
\n    }
\n
\n    .why-choose-us.why-choose-us.why-choose-us .simplebox-column-1 {
\n        margin-top: 30px;
\n        padding-left: 24px;
\n        padding-right: 24px;
\n    }
\n
\n    .why-choose-us .simplebox-column-2 {
\n        margin-top: -30px;
\n        margin-bottom: -85px
\n    }
\n
\n    .why-choose-us.why-choose-us .simplebox-column-1 .simplebox-content {
\n        padding-top: 13px 8px 11px 20px;
\n    }
\n
\n    .why-choose-us h1 {
\n        margin-bottom: .33em
\n    }
\n
\n    .why-choose-us li:first-child {
\n        margin-top: 0;
\n    }
\n
\n    .simplebox.simplebox.simplebox-accredited .simplebox-column-2 {
\n        margin: -42px -44px -33px
\n    }
\n
\n    .simplebox-accredited .read_more {
\n         font-size: 1rem;
\n    }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .layout-home .spotlights::before {
\n        content: \'\';
\n        background: var(\-\-light_gray);
\n        overflow: hidden;
\n        position: absolute;
\n        bottom: .5rem;
\n        width: 100%;
\n        height: 7.25rem;
\n        z-index: -1;
\n    }
\n
\n    .spotlights .simplebox-column {
\n        margin-bottom: 3.125rem;
\n    }
\n
\n    .spotlights .simplebox-column.simplebox-column {
\n        padding: 0 1.25rem 0 0;
\n    }
\n}
\n
\n.simplebox-spotlights2 {
\n    padding-top: 24px;
\n    padding-bottom: 80px;
\n}
\n
\n.simplebox-spotlights2 h2 { font-size: 35px; }
\n
\n.simplebox-spotlights2.simplebox-spotlights2 a:link {
\n    text-decoration: none;
\n}
\n
\n.simplebox-spotlights2.simplebox-spotlights2 a:hover {
\n    text-decoration: underline;
\n}
\n
\n.simplebox-spotlights2 .simplebox-title {
\n    margin-bottom: 2rem;
\n}
\n
\n.simplebox-spotlights2 .simplebox-column {
\n    box-shadow: 0 4px 15px rgba(0, 0, 0, .18);
\n}
\n
\n.simplebox-spotlights2 .simplebox-content {
\n    padding: .5rem 2rem;
\n    padding: 1.25rem;
\n}
\n
\n.simplebox-spotlights2 .simplebox-content h3 {
\n    font-size: 35px;
\n    line-height: 44px;
\n    margin-top: 0;
\n}
\n
\n.simplebox-spotlights2 img {
\n    margin-left: 1rem;
\n}
\n
\n.simplebox-training {
\n    background-color: #dadada;
\n    color: #000;
\n    font-weight: normal;
\n}
\n
\n.simplebox-training h2 {
\n    color: #555;
\n}
\n
\n.simplebox-training .read_more-lg {
\n    font-size: 18px;
\n}
\n
\n.simplebox-training .read_more-lg::after {
\n    top: 6px;
\n}
\n
\n.simplebox-strokes { margin-bottom: 2.5rem; }
\n
\n.simplebox-strokes .simplebox-column                 { \-\-stroke_color: var(\-\-bright_purple); }
\n.simplebox-strokes .simplebox-column:nth-child(3n+2) { \-\-stroke_color: var(\-\-dark_purple);   }
\n.simplebox-strokes .simplebox-column:nth-child(3n)   { \-\-stroke_color: var(\-\-bright_pink);   }
\n
\n.simplebox-strokes .simplebox-column.simplebox-column {
\n    background: var(\-\-stroke_color);
\n}
\n
\n.simplebox-strokes .simplebox-content {
\n    background: #fff;
\n    border-radius: 0 0 2.5rem 0;
\n}
\n
\n.simplebox-strokes h3 { color: var(\-\-dark_purple); }
\n
\n@media screen and (max-width: 767px) {
\n    .simplebox-spotlights2 {
\n        background: #fff;
\n        padding: 28px 23px 0 27px;
\n    }
\n
\n    .simplebox-spotlights2 .simplebox-column {
\n        margin-bottom: 30px;
\n        padding: 0 !important; /* Temporary. Need to make the other selector less-specific. */
\n    }
\n
\n    .simplebox-spotlights2 .simplebox-content {
\n        min-height: 285px;
\n    }
\n
\n    .simplebox-spotlights2 .simplebox-content::after {
\n        content: \'\';
\n        clear: both;
\n        display: table;
\n    }
\n
\n    .simplebox-training {
\n        padding-top: 46px;
\n    }
\n
\n    .simplebox-training .simplebox-column {
\n        padding-top: 0 !important; /* todo: make the rule that this is overwriting less specific */
\n        padding-bottom: 0 !important; /* todo: make the rule that this is overwriting less specific */
\n    }
\n
\n    .simplebox-training h2 {
\n        font-size: 1.5rem;
\n        line-height: 1.25;
\n        margin-bottom: 21px;
\n    }
\n
\n    .simplebox-training p {
\n        margin-bottom: 6px;
\n    }
\n
\n    .simplebox-training [src*=\"contact\"] {
\n        display: block;
\n        float: right;
\n        margin-top: -36px;
\n        max-width: 160px;
\n        position: relative;
\n        left: -10px;
\n    }
\n
\n    .simplebox-training2 {
\n        background-position: right;
\n    }
\n
\n    .simplebox-training2 .simplebox-columns {
\n        min-height: 450px;
\n        padding: 30px 0 90px;
\n    }
\n
\n    .simplebox-get_started {
\n        padding-top: 8px;
\n    }
\n
\n    .simplebox-get_started .simplebox-content p {
\n        line-height: 23px;
\n    }
\n
\n    .simplebox-get_started [src*=\"contact\"] {
\n        margin-right: -36px;
\n        margin-top: -65px;
\n        max-width: 236px;
\n    }
\n
\n    .simplebox-strokes .simplebox-column.simplebox-column {
\n        padding: 0 .625rem 0 0
\n    }
\n
\n    .simplebox-strokes .simplebox-content {
\n        padding: 19px 9px 19px 19px;
\n    }
\n
\n    .simplebox-strokes h3 { margin-bottom: 15px; }
\n
\n    .simplebox-strokes li:first-child { margin-top: 0; }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .simplebox-spotlights2 .simplebox-title {
\n        margin-bottom: 26px;
\n    }
\n
\n    .simplebox-spotlights2 .simplebox-content {
\n        padding: 27px 30px 5px 30px;
\n    }
\n
\n    .simplebox-spotlights2 .simplebox-content h3 {
\n        font-size: 30px;
\n        line-height: 38px;
\n    }
\n
\n    .simplebox-training h2 {
\n        margin-bottom: 26px;
\n    }
\n
\n    .simplebox-training p {
\n        font-size: 18px;
\n        line-height: 23px;
\n    }
\n
\n    .simplebox-training2 .simplebox-columns {
\n        padding: 10px 0;
\n    }
\n
\n    .simplebox-strokes .simplebox-column.simplebox-column {
\n        padding: 0 .9375rem 0 0
\n    }
\n
\n    .simplebox-strokes .simplebox-content {
\n        padding: 30px 33px;
\n    }
\n}
\n
\n.upcoming-courses-embed-see_more {
\n    color: var(\-\-primary);
\n    margin-top: 1.75rem;
\n}
\n
\n.upcoming-courses-embed-see_more::after {
\n    content: var(\-\-arrow-bright_purple-lg);
\n    margin-left: .6666666667em;
\n    position: relative;
\n    top: .2083em;
\n}
\n
\n@media screen and (max-width: 767px) {
\n    .featured_programme .simplebox-column-1 { padding: 0 !important;    } /* todo: update the rule that this is overwriting to be less specific */
\n    .featured_programme .simplebox-column-2 { padding: 19px !important; } /* todo: update the rule that this is overwriting to be less specific */
\n
\n    .simplebox-featured_programmes h2 {
\n        font-size: 28px;
\n        margin-bottom: 0;
\n    }
\n
\n    .upcoming-courses-embed {
\n        margin-bottom: 1.75rem;
\n    }
\n
\n    .upcoming-courses-embed-see_more {
\n        font-size: 1rem;
\n    }
\n
\n    .upcoming-courses-embed-see_more::after {
\n        top: .4em;
\n    }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .upcoming-courses-embed {
\n        margin-bottom: 3.75rem;
\n    }
\n}
\n
\n.simplebox-title > :last-child { margin-bottom: 0;}
\n
\n.featured_programme {
\n    color: #333;
\n    font-weight: normal;
\n}
\n
\n.featured_programme .simplebox-columns,
\n.featured_programme img {
\n    background: #fff;
\n    box-shadow: 0px 4px 25px rgba(0, 0, 0, 0.11);
\n}
\n
\n.featured_programme .simplebox-title {
\n    text-align: left;
\n    margin-bottom: 0;
\n    padding: 20px 0 24px;
\n}
\n
\n.featured_programme .simplebox-title p {
\n    color: #555;
\n    text-indent: .1875rem;
\n}
\n
\n
\n.featured_programme h6 {
\n    color: #666;
\n    font-size: .75rem;
\n    font-weight: normal;
\n    letter-spacing: -.01em;
\n    margin: 0 0 .33333333em;
\n}
\n
\n.featured_programme h3 {
\n    color: #333;
\n    letter-spacing: -.01em;
\n    margin-top: 0;
\n    padding-bottom: 16px;
\n    position: relative;
\n}
\n
\n.featured_programme h3:after {
\n    content: \'\';
\n    position: absolute;
\n    bottom: 0;
\n    left: -30px;
\n    right: 0;
\n    border-bottom: 1px solid var(\-\-light_gray);
\n}
\n
\n.featured_programme .simplebox-column:last-child {
\n    margin: 0;
\n    padding: 24px 27px 5px 5px;
\n}
\n
\n.featured_programme .simplebox-content p {
\n    font-size: .875rem;
\n    line-height: 1.3
\n}
\n
\n.featured_programme .simplebox-content > :last-child { margin-bottom: 0;}
\n
\n.featured_programme .simplebox-content p:not(:last-child) {
\n    max-width: 440px;
\n}
\n
\n.featured_programme .read_more {
\n    color: var(\-\-dark_purple);
\n    font-size: 1.125rem;
\n    margin-top: 1.25em;
\n    display: inline-block;
\n}
\n
\n.featured_programme .read_more:after {
\n    content: var(\-\-arrow-dark_purple-lg);
\n    top: .4em;
\n}
\n
\n.simplebox-office {
\n    padding-top: 50px;
\n    padding-bottom: 40px;
\n}
\n
\n.simplebox-office h2 {
\n    font-size: 35px;
\n}
\n
\n.simplebox-office p {
\n    line-height: 1.5625;
\n}
\n
\n.simplebox-office iframe {
\n    max-width: 300px;
\n}
\n
\n.simplebox-office .simplebox-content > h2:first-child {
\n    margin-top: -.34285714285em;
\n}
\n
\n.simplebox-office .simplebox-content > p:first-child {
\n    margin-top: -.125em;
\n}
\n
\n@media screen and (max-width: 767px) {
\n    .office-intro {
\n        padding: 10px 15px 24px 25px;
\n    }
\n
\n    .office-intro h1 {
\n        font-size: 35px;
\n        line-height: 44px;
\n        margin-top: 0;
\n        margin-bottom: 17px;
\n    }
\n
\n    .office-intro.office-intro p {
\n        line-height: 1.27;
\n    }
\n
\n    .office-intro .simplebox-content > :last-child {
\n        margin-bottom: 0;
\n    }
\n
\n    .office-intro + .simplebox-office {
\n        padding-top: 16px;
\n    }
\n
\n    .simplebox-office {
\n        padding-left: 25px;
\n    }
\n
\n    .simplebox-office .simplebox-column {
\n        padding-top: 0 !important; /* todo: make the selector this overwrites less-specific */
\n        padding-bottom: 0 !important; /* todo: make the selector this overwrites less-specific */
\n    }
\n
\n    .simplebox-office iframe {
\n        margin-top: 31px;
\n    }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .office-intro {
\n        font-size: 26px;
\n        line-height: 1.27;
\n        padding-top: 40px;
\n    }
\n
\n    .office-intro h1 {
\n        font-size: 46px;
\n        margin-bottom: 33px;
\n    }
\n
\n    .office-intro.office-intro p {
\n        font-size: 26px;
\n        line-height: 1.27;
\n        margin-bottom: 10px;
\n    }
\n
\n    .simplebox-office {
\n        padding-top: 70px;
\n        padding-bottom: 68px;
\n    }
\n}
\n
\n
\n
\n
\n@media screen and (max-width: 479px) {
\n    .banner-overlay-content p {
\n        max-width: 290px;
\n    }
\n}
\n
\n@media screen and (max-width: 767px) {
\n    .banner-section {min-height: 280px;}
\n
\n    .swiper-slide .banner-image {
\n        background-color: var(\-\-dark_purple);
\n        background-position: center;
\n        height: 340px
\n    }
\n
\n    .banner-search-title {
\n        border-bottom-color: #FFF;
\n    }
\n
\n    .banner-overlay {
\n        background: none;
\n        padding: 0 1px;
\n    }
\n
\n    .banner-overlay-content h1 {
\n        margin-bottom: 18px;
\n    }
\n
\n    .layout-home .banner-overlay-content h1 {
\n        margin-bottom: 0.277778em;
\n    }
\n
\n    .banner-overlay-content p {
\n        line-height: 1.25;
\n        margin: 14px 0;
\n    }
\n
\n    .banner-overlay-content a.read_more::after {
\n        margin-left: .6666em;
\n        top: .44444em;
\n    }
\n
\n    .banner-image {
\n        background-size: cover;
\n    }
\n
\n    .layout-home .banner-image {
\n        background-color: var(\-\-bright_purple);
\n    }
\n
\n    .banner-overlay .row {
\n        align-items: flex-end;
\n        padding-bottom: 50px;
\n    }
\n
\n    [class*=\"layout-course_list\"] .banner-slide,
\n    [class*=\"layout-course_list\"] .banner-image {
\n        height: 300px;
\n    }
\n
\n    [class*=\"layout-course_list\"] .banner-overlay .row {
\n        align-items: flex-start;
\n        padding-top: 36px;
\n    }
\n
\n    [class*=\"layout-course_list\"] .banner-overlay h1 {
\n        max-width: 300px;
\n    }
\n
\n    .has_banner .course-list-intro:not(.has_content) {
\n        margin-top: -5.5rem;
\n        margin-bottom: 1rem;
\n    }
\n
\n    .layout-home .banner-overlay .row {
\n        align-items: unset;
\n        padding-top: 2.25rem;
\n    }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .banner-overlay { background-repeat: no-repeat; }
\n    .swiper-slide .banner-image {
\n        background-position: center;
\n        height: var(\-\-slide-height, 600px);
\n    }
\n
\n    .swiper-slide .banner-overlay {
\n        background-position: top center;
\n    }
\n
\n    .banner-overlay-content {
\n        max-width: none;
\n    }
\n
\n    .layout-home .banner-overlay-content {
\n        max-width: 650px;
\n    }
\n
\n    body[data-page="subscribe"] .banner-overlay-content {
\n        bottom: 66px;
\n    }
\n
\n    .layout-home .banner-overlay-content p {
\n        max-width: 580px;
\n    }
\n
\n    .banner-slide\-\-center .banner-overlay {
\n        background: rgba(66, 91, 168, .333)
\n    }
\n
\n    .banner-overlay .row {
\n        display: flex;
\n        align-items: flex-end;
\n    }
\n
\n    .has_linked_subject .banner-overlay .row,
\n    .layout-home .banner-overlay .row {
\n        align-items: center;
\n    }
\n
\n    .simplebox.simplebox.simplebox-raised { margin-top: -162px; }
\n    .layout-home .simplebox.simplebox-raised { margin-top: -135px; }
\n    .has_linked_subject .simplebox.simplebox-raised { margin-top: -72px; }
\n
\n    /* So that vertical alignment is not thrown off by raised boxes */
\n    .banner-overlay-content { padding-bottom: 148px; }
\n    .layout-home .banner-overlay-content { padding-bottom: 66px; }
\n    .has_linked_subject .banner-overlay-content { padding-top: 20px;padding-bottom: 0; }
\n
\n
\n    .has_linked_subject .simplebox-raised .simplebox-column + .simplebox-column {
\n        padding-left: 15px;
\n        padding-right: 15px;
\n    }
\n
\n    .banner-overlay .read_more {
\n        display: inline-block;
\n        margin-top: .5rem;
\n    }
\n
\n    \/\* todo: set these as the default styles. And overwrite them on the layouts that are exceptions. \*\/
\n    body:not(.layout-home):not([class*="layout-course"]) .banner-overlay-content,
\n    body:not(.layout-home):not([class*="layout-course"]) .banner-overlay-content {
\n        max-width: 570px;
\n        padding-bottom: 0;
\n    }
\n
\n    body:not(.layout-home):not([class*="layout-course"]) .banner-overlay-content {
\n        padding-bottom: 51px;
\n    }
\n
\n    body:not(.layout-home):not([class*="layout-course"]) .banner-overlay-content h1 {
\n        margin-bottom: 43px
\n    }
\n
\n    body:not(.layout-home):not([class*="layout-course"]) .banner-overlay-content p {
\n        margin-bottom: 36px
\n    }
\n
\n    body:not(.layout-home):not([class*="layout-course"]) .banner-overlay-content > :last-child {
\n        margin-bottom: 0;
\n    }
\n}
\n
\n@media screen and (min-width: 1024px) {
\n    .header-menu-section {
\n        margin-right: 1.25rem;
\n    }
\n
\n    .header-actions .header-action a {
\n        font-size: 1rem;
\n    }
\n
\n    .header-item > a:not(.button) {
\n        font-size: 1.125rem;
\n    }
\n
\n    .course-details-menu { right: calc(50vw - 570px); }
\n}
\n
\n/* Hack for now */
\nbody[data-page="standard-page"] .banner-overlay-content.banner-overlay-content {
\n    font-size: 1rem;
\n    padding-bottom: 2.6875rem;
\n    max-width: 700px;
\n}
\n
\n.search-drilldown h3 {
\n    color: var(\-\-primary);
\n}
\n
\n.search-drilldown-column p {
\n    color: var(\-\-primary);
\n}
\n
\n.search-drilldown-column a.active {
\n    background-color: var(\-\-primary);
\n    color: #fff;
\n}
\n
\n@media screen and (max-width: 767px) {
\n    .search-drilldown-close:before,
\n    .search-drilldown-close:after {
\n        background-color: #303030;
\n    }
\n
\n    .search-drilldown-column\-\-category li {
\n        border-top-color: var(\-\-primary);
\n    }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .search-drilldown-column {
\n        border-color: var(\-\-primary);
\n    }
\n}
\n
\n\/\* Calendar \*\/
\n.eventCalendar-wrap {
\n    border-color: #bfbfbf;
\n}
\n
\n.eventsCalendar-slider {
\n    background: linear-gradient(var(\-\-primary), #137774);
\n    border-bottom-color: #bfbfbf;
\n}
\n
\n
\n.eventsCalendar-currentTitle {
\n    border-bottom-color: #bfbfbf;
\n}
\n
\n.eventsCalendar-currentTitle a {
\n    color: #fff;
\n}
\n
\n.eventCalendar-wrap .arrow span {
\n    border-color: #fff;
\n}
\n
\n.eventsCalendar-day-header,
\n.eventsCalendar-daysList {
\n    color: #fff;
\n}
\n
\n.eventsCalendar-day.today {
\n    background-color: #fff;
\n    border: 1px solid;
\n    border-color: var(\-\-primary) var(\-\-primary) var(\-\-primary) #137774;
\n    color: var(\-\-primary);
\n}
\n
\n.eventsCalendar-subtitle {
\n    color: var(\-\-primary);
\n}
\n
\n.eventsCalendar-list > li > time {
\n    color: var(\-\-primary);
\n}
\n
\n.eventsCalendar-list > li {
\n    border-bottom-color: #bfbfbf;
\n}
\n
\n\/\* News feeds \*\/
\n.layout-news3.has_banner{
\n    background: var(\-\-lighter_gray);
\n}
\n
\n.layout-news3 .page-footer {
\n    margin-top: 0;
\n    padding-top: 0;
\n    padding-bottom: 0;
\n}
\n
\n.news-filter-group .input_group-icon {
\n    background: var(\-\-dark_purple);
\n    border-radius: 0;
\n    color: #fff;
\n}
\n
\n.news-filter-keyword { padding-left: .75em; }
\n
\n
\n.news-list-by-media_type[data-media="Video"] { background: var(\-\-light_gray); }
\n.news-list-by-media_type[data-media="Podcast"] { background: var(\-\-dark_purple); }
\n.news-list-by-media_type[data-media="Podcast"] h2 { color: #fff; }
\n
\n.news-list-by-media_type {
\n    border: 1px solid transparent;
\n    border-width: 1px 0;
\n}
\n
\n.news-list-by-media_type:first-child {
\n    padding-top: 13px;
\n}
\n
\n.news-category-embed {
\n    padding-top: .625rem;
\n    padding-bottom: 2.5rem;
\n}
\n
\n.news-category-embed .container {
\n    max-width: 1170px;
\n}
\n
\n.news-category-embed-intro-title {
\n    color: var(\-\-dark_gray);
\n    font-size: 36px;
\n    font-weight: normal;
\n    line-height: 48px;
\n}
\n
\n.news-category-embed-intro-title-prefix { display: none; }
\n
\n.news-category-embed-intro-title a { color: inherit; }
\n
\n.news-feed-item-data {
\n    margin-top: .5rem!important;\/\* todo: remove absolute margin class from the element \*\/
\n    font-size: 10px;
\n    text-transform: uppercase;
\n}
\n
\n.news-category-embed header {
\n    margin-bottom: 1rem !important;\/\* todo: remove absolute margin class from the element \*\/
\n}
\n
\n.news-category-embed .read_more {
\n    margin-right: -.25rem;
\n    margin-bottom: .25rem;
\n}
\n
\n.page-content .news-category-embed-intro h1 {
\n    font-size: 28px;
\n    line-height: 36px;
\n    margin-top: 0;
\n}
\n
\n.page-content .news-category-embed-intro p {
\n        font-size: 16px;
\n        line-height: 1.25;
\n        margin-top: .5rem;
\n        margin-bottom: 1.7rem;
\n        max-width: 520px;
\n}
\n
\n.news-category-tabs.news-category-tabs {
\n    max-width: 1080px;
\n}
\n
\n.news-category-tabs-section + .fullwidth {
\n    background: #f6f6f6;
\n}
\n
\n.news-category-embed-see_more.news-category-embed-see_more {
\n    background: none;
\n    color: var(\-\-bright_purple);
\n    font-size:1rem;
\n    font-weight: 600;
\n    line-height: 1.25;
\n    min-width: 0;
\n    padding: 0;
\n}
\n
\n.news-category-embed-see_more:after {
\n    content: var(\-\-arrow-bright_purple-lg);
\n    margin-left: .5em;
\n    position: relative;
\n    top: .25em;
\n}
\n
\n.news-category-embed-see_more-type { display: none; }
\n
\n .news-feed-item-data time {
\n     letter-spacing: 1px;
\n}
\n
\n.news-feed-item-body {
\n    padding-top: 1.3rem;
\n}
\n.news-feed-item header {
\n   margin-bottom: 1.4em!important
\n}
\n
\n.news-feed-item .news-feed-item-title {
\n    font-size: 18px;
\n    line-height: 23px;
\n}
\n
\n.news-feed-item-button {
\n    float: right;
\n    font-weight: bold;
\n}
\n
\n.news-feed-item-button:after {
\n    content: var(\-\-arrow-bright_purple-lg);
\n    margin-left: .75em;
\n    position: relative;
\n    top: .5em;
\n}
\n
\n.news-feed-item-button.text-white:after {
\n    content: var(\-\-arrow-white-lg);
\n}
\n
\n.news-section {
\n    background: #fff;
\n    box-shadow: 1px 1px 10px #ccc;
\n}
\n
\n.news-slider-link {
\n  color: var(\-\-primary);
\n}
\n
\n.news-slider-title {
\n    color: var(\-\-primary);
\n    background-color: #fff;
\n}
\n
\n@media screen and (max-width: 767px) {
\n    .layout-news3 .page-footer {
\n        margin-left: -15px;
\n        width: calc(100vw);
\n    }
\n
\n    .news-filter-group h3,
\n    .sidebar-section .sidebar-section-title.sidebar-section-title {
\n        color: var(\-\-dark_gray);
\n        font-size: 1.5rem;
\n        line-height: 1.5;
\n        margin-bottom: 15px;
\n    }
\n
\n    .news-filter-group[data-filter=\"keyword\"] h3 {
\n        margin-bottom: 5px;
\n    }
\n
\n    .sidebar-filter-li {
\n        margin-bottom: .5em!important;
\n    }
\n
\n    .sidebar-filter-li label {
\n        display: block;
\n    }
\n
\n    .sidebar-filter-li label:after {
\n        content: \'\';
\n        display: table;
\n        clear: both;
\n    }
\n
\n    .sidebar-filter-li label > span {
\n        float: left;
\n        margin-left: 0;
\n    }
\n
\n    .sidebar-filter-li .form-checkbox {
\n        margin-left: 0;
\n        margin-right: .5em;
\n        margin-top: 3px;
\n    }
\n
\n    search-criteria-reset-li { margin-bottom: 0; }
\n
\n    .news-filter-reset,
\n    .search-criteria-reset {
\n        background: #f4f4f4;
\n        border-radius: 2em;
\n        color: #555;
\n        display: inline-block;
\n        font-size: 14px;
\n        line-height: 1.43;
\n        margin-bottom: 25px;
\n        padding: 0 10px;
\n        text-transform: lowercase;
\n    }
\n
\n    .news-filter-reset::before,
\n    .search-criteria-reset::before {
\n        content: \'x\\a0  \';
\n        font-weight: bold;
\n    }
\n
\n    .search-criteria-reset .fa-times { display: none; }
\n
\n    .news-category-embed {
\n        padding-left: 5px;
\n        padding-right: 5px;
\n}
\n
\n    .news-category-embed-see_more {
\n        margin-top: -1rem;
\n    }
\n
\n    .news-category-embed-see_more::after {
\n        top: .5em;
\n    }
\n
\n    .news-list-by-media_type {
\n        padding: 24px 5px;
\n    }
\n
\n    .news-list-by-media_type:nth-last-child(2) {
\n        padding-bottom: 0
\n    }
\n
\n    .news-feed-item-body {
\n        padding: 11px 21px 25px 17px;
\n    }
\n
\n    .news-feed-item-button {
\n        \-\-primary: var(\-\-dark_purple);
\n    }
\n
\n    .news-feed-item-button::after {
\n        content: var(\-\-arrow-dark_purple-lg);
\n    }
\n
\n    .news-category-column {
\n        margin-bottom: 31px;
\n    }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .news-list-by-media_type {
\n         padding: 68px 0 68px;
\n    }
\n
\n    .news-feed-item .news-feed-item-title {
\n        font-size: 24px;
\n        line-height: 30px;
\n    }
\n
\n    .page-content .news-category-embed-intro h1 {
\n        font-size: 36px;
\n        line-height: 46px;
\n        margin-top: 1em;
\n    }
\n
\n    .news-category-embed {
\n        padding-bottom: 4.65rem;
\n    }
\n
\n    .news-feed-item-data {
\n        font-size: .75rem;
\n    }
\n
\n    .news-category-embed-see_more.news-category-embed-see_more {
\n        float: right;
\n        font-size: 1.5rem;
\n    }
\n}
\n
\n.swiper-pagination-bullet {
\n    background-color: #fff;
\n    border-color: #A6AEAD;
\n    box-shadow: inset 0 1px 1px #aaa;
\n}
\n
\n.swiper-pagination-bullet-active {
\n    background-color: var(\-\-category-color, var(\-\-primary));
\n}
\n
\n.swiper-button-prev,
\n.swiper-button-next {
\n    background: none;
\n    border-radius: 50%;
\n    width: 50px;
\n    height: 50px;
\n}
\n
\n.swiper-button-prev::after,
\n.swiper-button-next::after {
\n    content: \'\';
\n    display: block;
\n    position: absolute;
\n    top: 30%;
\n    width: 20px;
\n    height: 20px;
\n    border: solid #c4c4c4;
\n    transform: rotate(45deg);
\n}
\n
\n.swiper-button-prev::after {
\n    border-width: 0 0 3px 3px;
\n    left: 39%;
\n}
\n
\n.swiper-button-next::after {
\n    border-width: 3px 3px 0 0;
\n    right: 39%;
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .swiper-button-prev,
\n    .swiper-button-next {
\n        background: #fff;
\n    }
\n}
\n
\n.news-result-date {
\n    background-color: var(\-\-primary);
\n    color: #FFF;
\n}
\n
\n.page-content .news-page-category-title {
\n    line-height: 1.14583333;
\n    margin-bottom: .958333333em;
\n}
\n
\n.news-page-image {
\n    margin-bottom: 0;
\n}
\n
\n.page-content .news-page-title {
\n    line-height: 1;
\n    margin-top: .541666667em;
\n}
\n
\n.page-content .news-page-date {
\n    color: var(\-\-darkened_gray);
\n    display: block;
\n    font-size: .75rem;
\n    letter-spacing: .0833333333em;
\n    line-height: 1.25;
\n    margin: 22px 0 35px;
\n}
\n
\n.news-page-content {
\n    font-weight: normal;
\n    line-height: 1.5;
\n}
\n
\n.news-page-content h4,
\n.news-page a {
\n    color: var(\-\-category-color);
\n}
\n
\n.addthis_toolbox {
\n    border-color: var(\-\-light_gray);
\n    border-style: dashed;
\n    margin: 30px 0;
\n}
\n
\n.news-page-content .ib-audio-wrapper {
\n    margin: 50px 0 23px;
\n}
\n
\n.news-page-content > .ib-video-wrapper { margin-top: 40px; }
\n
\n.news-sidebar-item.news-sidebar-item {
\n    border-bottom: 1px dashed var(\-\-light_gray);
\n    margin: 0;
\n    padding: 1em 0;
\n    width: auto;
\n}
\n
\n.news-sidebar-item a {
\n    color: #333;
\n}
\n
\n.news-sidebar-feed h3 {
\n    color: var(\-\-bright_purple);
\n    font-size: 1.75rem;
\n    margin-top: 37px;
\n}
\n
\n.news-sidebar-item h4 {
\n    color: var(\-\-dark_gray);
\n    font-size: 1.125rem;
\n    line-height: 1.27777778;
\n    margin: 0;
\n}
\n
\n.news-sidebar-date {
\n    font-size: .75rem;
\n    text-transform: uppercase;
\n    letter-spacing: .08333333em;
\n}
\n
\n.news-page-subscribe {
\n    margin: 34px 0 30px;
\n    width: auto;
\n}
\n
\n.news-page-subscribe.news-page-subscribe h4 {
\n    margin: 0 0 22px;
\n}
\n
\n.news-page-subscribe .form-input {
\n    height: 43px;
\n    padding: 13px;
\n}
\n
\n.news-page-subscribe .form-group {
\n    margin-bottom: .75rem;
\n}
\n
\n.news-page-subscribe .button {
\n    font-size: 18px;
\n    padding: 11px 12px 14px;
\n}
\n
\n@media screen and (max-width: 767px) {
\n    .news-container h2 {
\n        font-size: 28px;
\n    }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .news-page-image {
\n        display: flex;
\n        align-items: center;
\n        height: 500px;
\n        overflow: hidden;
\n    }
\n
\n    .row.row > .news-column-content {
\n        border-right: 1px dashed var(\-\-light_gray);
\n        padding-right: 30px;
\n        width: 66.325%;
\n    }
\n
\n    .row.row > .news-column-feed {
\n        padding-left: 29px;
\n        width: 33.675%;
\n    }
\n
\n    .news-page-content .fullwidth,
\n    .news-page-content .simplebox {
\n        margin-left: calc(79% - 50vw) !important;
\n    }
\n
\n    .news-page-content > .ib-video-wrapper,
\n    .news-page-content > .ib-audio-wrapper {
\n        width: calc(100% + 21px);
\n    }
\n
\n    .news-page-subscribe {
\n        margin: 34px 0 60px;
\n    }
\n}
\n
\n@media screen and (max-width: 1023px)
\n{
\n    .news-result + .news-result:before {
\n        background: linear-gradient(to right, transparent 0, var(\-\-primary) 10%, var(\-\-primary) 90%, transparent 100%);
\n    }
\n}
\n
\n@media screen and (min-width: 1024px)
\n{
\n    .news-result + .news-result {
\n        border-color: var(\-\-primary);
\n    }
\n}
\n
\n.news-story-navigation a {
\n    color: var(\-\-primary);
\n}
\n
\n.news-story-social {
\n    border-color: var(\-\-primary);
\n}
\n
\n.news-story-share_icon {
\n    color: var(\-\-primary);
\n}
\n
\n.news-story-social-link svg {
\n    background: var(\-\-primary);
\n}
\n
\n  .simplebox-accredited {
\n      padding-bottom: 2.4rem!important;
\n}
\n .accredited-partner-text {
\n     margin-top: 1.3em!important;
\n     font-size: 1.2rem!important;
\n}
\n .accredited-partner-text h3{
\n     margin-top: 0.2em!important;
\n     margin-bottom: 1.6rem!important;
\n
\n}
\n.testimonials-slider p {
\n    line-height: 23px;
\n    margin: 1.25rem 0;
\n}
\n
\n.testimonials-slider .row {
\n    max-width: 760px;
\n}
\n
\n.testimonial-embed-signature {
\n    color: var(-\-light_gray);
\n}
\n
\n.layout-testimonials .content_area:first-child {
\n    padding-bottom: 23px;
\n}
\n
\n.testimonials-section {
\n    margin-left: auto;
\n    margin-right: auto;
\n    max-width: 750px;
\n}
\n
\n.testimonial-block {
\n    box-shadow: 0 4px 25px rgba(0, 0, 0, .11);
\n    margin: 42px auto 0;
\n    padding: 30px 40px 27px;
\n    min-height: 10rem;
\n}
\n
\n.testimonials-section .pagination-wrapper {
\n    margin: 34px 0;
\n}
\n
\n.testimonial-block:first-child {
\n    margin-top: 17px;
\n}
\n
\n.testimonial-content {
\n    border: none;
\n    margin: 0;
\n    padding: 0;
\n}
\n
\n.testimonial-content p {
\n    line-height: 1.25;
\n}
\n
\n.testimonial-content::before,
\n.testimonial-content::after,
\n.testimonial-content p::before {
\n    display: none;
\n}
\n
\n.testimonial-content > :first-child { margin-top: 0; }
\n.testimonial-content > :last-child  { margin-bottom: 0; }
\n
\n.testimonial-course-type {
\n    color: var(\-\-bright_purple);
\n    font-size: .75rem;
\n    font-weight: normal;
\n    line-height: 1.25;
\n    margin-top: .5em;
\n    text-align: right;
\n}
\n
\n.testimonial-footer {
\n    display: flex;
\n    align-items: center;
\n    margin-top: 2.0625rem;
\n}
\n
\n.testimonial-image {
\n    width: 100px;
\n}
\n
\n.testimonial-image img {
\n    border-radius: 50%;
\n    display: block;
\n    width: 75px;
\n    margin-left: -5px;
\n}
\n
\n.testimonial-signature,
\n.testimonial-position,
\n.testimonial-company {
\n    font-size: 1rem;
\n    font-weight: normal;
\n    letter-spacing: -.01em;
\n    line-height: 1.25;
\n    margin: 0;
\n}
\n
\n.testimonial-signature:not(:last-child)::after,
\n.testimonial-signature ~ :not(:last-child)::after {
\n    content: \',\';
\n}
\n
\n.testimonial-company {
\n    font-weight: bold;
\n}
\n
\n.testimonial-videos .simplebox-columns {
\n    align-items: flex-start;
\n}
\n
\n.testimonial-videos .ib-video-wrapper { zoom: .6;}
\n
\n.testimonial-videos h3 {
\n    margin: 0;
\n    padding: 22px 20px 38px;
\n}
\n
\n@media screen and (max-width: 767px) {
\n    .testimonial-block {
\n        background-image: url(\'data:image/svg+xml,%3Csvg width=\"50\" height=\"35\" viewBox=\"0 0 50 35\" fill=\"none\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cpath d=\"M23.3871 24.4521C23.3871 27.4886 22.4194 29.8859 20.4839 31.9635C18.5484 33.8813 15.9677 35 12.9032 35C9.03225 35 5.96774 33.5617 3.54838 30.8448C1.29032 27.968 -4.30328e-06 24.6119 -3.63264e-06 20.7763C-2.62668e-06 15.0228 2.09677 10.2283 6.45161 6.23288C10.6452 2.07763 15.8065 2.76369e-06 21.7742 3.80712e-06L21.9355 1.758C18.2258 1.75799 14.8387 3.03653 11.7742 5.59361C8.70968 8.15069 7.25806 11.1872 7.25806 14.5434C7.25806 15.3425 7.41935 15.8219 7.58064 16.3014C7.74193 16.621 8.06451 16.9406 8.38709 16.9406C8.87096 16.9406 9.83871 16.621 10.8064 16.1416C11.9355 15.5023 12.9032 15.3425 13.7097 15.3425C16.2903 15.3425 18.5484 16.1416 20.4839 17.8996C22.4194 19.6575 23.3871 21.895 23.3871 24.4521ZM50 24.4521C50 27.4886 49.0323 29.8859 47.0968 31.9635C45.1613 33.8813 42.5806 35 39.5161 35C35.6452 35 32.5806 33.5617 30.1613 30.8448C27.7419 27.968 26.4516 24.6119 26.4516 20.7763C26.4516 15.0228 28.5484 10.2283 32.9032 6.23288C37.0968 2.07763 42.2581 7.38863e-06 48.3871 8.46027e-06L48.5484 1.758C44.8387 1.758 41.4516 3.03654 38.3871 5.59362C35.3226 8.15069 33.7097 11.1872 33.7097 14.5434C33.7097 15.3425 33.871 15.8219 34.0323 16.3014C34.1935 16.621 34.5161 16.9406 34.8387 16.9406C35.3226 16.9406 36.2903 16.621 37.2581 16.1416C38.3871 15.6621 39.3548 15.3425 40.1613 15.3425C42.7419 15.3425 45 16.1416 46.9355 17.8996C48.871 19.6575 50 21.895 50 24.4521Z\" fill=\"%23E5E2EF\"/%3E%3C/svg%3E\');
\n        background-repeat: no-repeat;
\n        background-position: 21px 21px;
\n        padding: 14px 21px 28px;
\n    }
\n
\n    .testimonial-block-content-wrapper {
\n        display: flex;
\n        flex-direction: column;
\n        position: relative;
\n    }
\n
\n    .testimonial-content {
\n        margin-top: 55px;
\n    }
\n
\n    .testimonial-course-type {
\n        order: -1;
\n        position: absolute;
\n        top: 0;
\n        right: 0
\n    }
\n
\n    [class*=\"layout-testimonial\"] .page-footer {
\n        margin-left: -23px;
\n        margin-right: -23px;
\n        width: calc(100% + 46px);
\n    }
\n
\n    .testimonial-videos {
\n        padding-top: 30px;
\n        padding-bottom: 20px;
\n    }
\n
\n    .accredited-partner-text .read_more-lg { font-size: 16px; }
\n    .accredited-partner-text .read_more-lg::after { top: 8px; }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .testimonial-block {
\n        background: #fff no-repeat url(\'data:image/svg+xml,%3Csvg width="70" height="49" viewBox="0 0 70 49" fill="none" xmlns="http://www.w3.org/2000/svg"%3E%3Cpath d="M37.2581 14.7671C37.2581 10.516 38.6129 7.15982 41.3226 4.25114C44.0323 1.56621 47.6452 -1.86038e-06 51.9355 -2.23545e-06C57.3548 -2.70922e-06 61.6452 2.0137 65.0323 5.81735C68.1935 9.84475 70 14.5434 70 19.9132C70 27.968 67.0645 34.6804 60.9677 40.274C55.0968 46.0913 47.871 49 39.5161 49L39.2903 46.5388C44.4839 46.5388 49.2258 44.7489 53.5161 41.169C57.8065 37.589 59.8387 33.3379 59.8387 28.6393C59.8387 27.5205 59.6129 26.8493 59.3871 26.1781C59.1613 25.7306 58.7097 25.2831 58.2581 25.2831C57.5806 25.2831 56.2258 25.7306 54.871 26.4018C53.2903 27.2968 51.9355 27.5205 50.8065 27.5205C47.1935 27.5205 44.0323 26.4018 41.3226 23.9406C38.6129 21.4795 37.2581 18.347 37.2581 14.7671ZM4.63666e-06 14.7671C4.26501e-06 10.516 1.35484 7.15982 4.06452 4.25114C6.7742 1.56621 10.3871 1.39683e-06 14.6774 1.02176e-06C20.0968 5.47981e-07 24.3871 2.0137 27.7742 5.81735C31.1613 9.84475 32.9677 14.5434 32.9677 19.9132C32.9677 27.968 30.0323 34.6804 23.9355 40.274C18.0645 46.0913 10.8387 49 2.25807 49L2.03226 46.5388C7.22581 46.5388 11.9678 44.7489 16.2581 41.169C20.5484 37.589 22.8065 33.3379 22.8065 28.6393C22.8065 27.5205 22.5806 26.8493 22.3548 26.1781C22.129 25.7306 21.6774 25.2831 21.2258 25.2831C20.5484 25.2831 19.1936 25.7306 17.8387 26.4018C16.2581 27.0731 14.9032 27.5205 13.7742 27.5205C10.1613 27.5205 7.00001 26.4018 4.29033 23.9406C1.58065 21.4795 4.94963e-06 18.347 4.63666e-06 14.7671Z" fill="%23E5E2EF"/%3E%3C/svg%3E\');
\n        background-position: calc(100% - 2.5rem) 4rem;
\n    }
\n
\n    .testimonial-block:first-child {
\n        margin-top: 40px;
\n    }
\n
\n    .testimonial-block-content-wrapper {
\n        display: flex;
\n    }
\n
\n    .testimonial-content {
\n        width: 73%
\n    }
\n
\n    .testimonial-course-type {
\n        width: 27%
\n    }
\n
\n    .testimonial-videos {
\n        padding-top: 75px;
\n        padding-bottom: 68px;
\n    }
\n}
\n
\n
\n
\n
\n\/\* Panels \*\/
\n.panel {
\n    background-color: #fff;
\n}
\n
\n.carousel-section .panel-title {
\n    background: #fff;
\n    color: #000;
\n}
\n
\n.carousel-section .panel-title h3 {
\n    font-weight: 400;
\n}
\n
\n.panels-feed\-\-home_content .panel-link {
\n    border-top: 1px solid #fff;
\n}
\n
\n.carousel-section .panel {
\n    border-color: #bfb8bf;
\n}
\n
\n@media screen and (max-width: 1023px)
\n{
\n    .panels-feed\-\-home_content > .column:after {
\n        background: var(\-\-bright_purple);
\n        background: linear-gradient(to right, #E6F3C8 0%, var(\-\-bright_purple) 20%, var(\-\-bright_purple) 80%, #E6F3C8 100%);
\n    }
\n}
\n
\n.bar {
\n    background: #F3F5F5;
\n    background: rgba(243, 245, 245, .8);
\n    box-shadow: 0 1px 1px #aaa;
\n}
\n
\n.bar-icon {
\n    background: var(\-\-primary);
\n    color: #fff;
\n}
\n
\n.bar-icon svg {
\n  fill: #fff;
\n}
\n
\n.bar-text {
\n    color: #222;
\n}
\n
\n.panel-item.has_form {
\n    background-color: var(\-\-bright_purple);
\n    color: #fff;
\n}
\n
\n.panel-item.has_form .button {
\n    background-color: #fff;
\n    border-color: var(\-\-bright_purple);
\n    color: var(\-\-bright_purple);
\n}
\n
\n.panel-item-image:after {
\n    background-image: url(\'\/media\/photos\/content\/panel_overlay.png\');
\n}
\n
\n.panel-item:nth-child(odd) .panel-item-image:after {
\n    background-image: url(\'\/media\/photos\/content\/panel_overlay_right.png\');
\n}
\n
\n.panel-item.has_image .panel-item-text {
\n    color: #fff;
\n    text-align: center;
\n    top: 20%;
\n}
\n
\n.panel-item.has_image:nth-child(even) .panel-item-text {
\n    right: 50%;
\n}
\n
\n.panel-item.has_image:nth-child(odd) .panel-item-text {
\n    left: 50%;
\n}
\n
\n\/\* Search results \*\/
\nbody.layout-course_list .content,
\nbody.layout-course_list2 .content,
\nbody.layout-course_detail2 .content,
\nbody.layout-news2 .content {
\n    margin-top: 0;
\n}
\n
\n.checkout-progress { display: block; }
\n
\n.checkout-progress li a:after {
\n    background-color: #fff;
\n    border-color: #fff;
\n    box-shadow: none;
\n}
\n
\n.checkout-progress li + li:before {
\n    border-color: var(\-\-primary);
\n}
\n
\n.checkout-progress ul li a {
\n    color: var(\-\-primary);
\n    font-weight: bold;
\n}
\n
\n.checkout-progress li.curr a:after {
\n    background: var(\-\-primary);
\n}
\n
\n.checkout-progress li:before {
\n    border-color: var(\-\-primary);
\n}
\n
\n.layout-checkout .checkout-progress li:before,
\n.layout-checkout .checkout-progress li + li:before,
\n.layout-checkout .checkout-progress li a:after {
\n    border-color: var(\-\-dark_purple);
\n}
\n
\n.layout-checkout .checkout-progress {
\n    margin-top: 10px;
\n    margin-bottom: 80px;
\n}
\n
\n.layout-checkout .checkout-progress ul li a {
\n    color: var(\-\-dark_purple);
\n}
\n
\n.layout-checkout .checkout-progress li.curr a:after {
\n    background: var(\-\-dark_purple);
\n}
\n
\n.checkout-progress .curr {
\n    font-weight: bold;
\n}
\n
\n.checkout-progress a {
\n    font-size: .75rem;
\n}
\n
\n.checkout-progress .curr ~ li > a::after {
\n    background: none;
\n}
\n
\n.course-list-intro,
\n.news-list-intro {
\n    background: var(\-\-primary);
\n    color: #fff;
\n}
\n
\n.course-list-intro *,
\n.news-list-intro *,
\n.course-list-intro .checkout-progress ul li,
\n.course-list-intro .checkout-progress a {
\n    color: inherit;
\n}
\n
\n.course-list-header,
\n.course-list-result_count {
\n    font-size: .875rem;
\n    font-weight: normal;
\n    letter-spacing: -0.01em;
\n    line-height: 1.28571429;
\n}
\n
\n.course-list-result_count {
\n    padding-left: 1px;
\n}
\n
\n.course-details-header {
\n    background-color: var(\-\-bright_purple);
\n    background-repeat: no-repeat;
\n    color: #fff;
\n}
\n
\n.course-details-header-breadcrumbs,
\n.course-details-header-summary {
\n    font-size: 1rem;
\n    line-height: 1.25;
\n    font-weight: normal;
\n}
\n
\n.course-details-header-summary {
\n    min-height: 0;
\n}
\n
\n.course-details-header-summary.is-empty {
\n    display: none;
\n}
\n
\n.course-details-header h1 {
\n    color: #fff;
\n    line-height: 1.1666667;
\n    margin: .208333333em 0 .625em;
\n}
\n
\n.course-details-header-main h1 {
\n    margin-top: -5px;
\n    overflow: hidden;
\n    padding-bottom: 5px;
\n    position: relative;
\n    top: 15px;
\n}
\n
\n.course-details-header h1.long-title { max-width: none; }
\n
\n.course-details-header .checkout-progress ul {
\n    width: calc(100% + 67px);
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .course-details-header-main {
\n        display: flex;
\n        flex-direction: column;
\n        height: 309px;
\n    }
\n
\n
\n    .course-details-header-main .checkout-progress {
\n        margin-top: auto;
\n    }
\n}
\n
\n.course-details-intro-timeslots li:before {
\n    background: var(\-\-dark_purple);
\n    opacity: .8;
\n}
\n
\n.checkout-progress ul li a {
\n    opacity: .5;
\n}
\n
\n.checkout-progress ul li.curr a {
\n    opacity: 1;
\n}
\n
\n.checkout-progress li a:after,
\n.checkout-progress li a:after {
\n    background: none;
\n    font-size: 1rem;
\n}
\n
\n.layout-course_detail2 .checkout-progress li.curr a:after,
\n.course-list-intro .checkout-progress li.curr a:after {
\n    background: #fff;
\n}
\n
\n.layout-course_detail2 .checkout-progress li:before,
\n.course-list-intro .checkout-progress li:before {
\n    border-color: #fff;
\n    opacity: .5;
\n}
\n
\n.course-page-content h2 {
\n    letter-spacing: -.02em;
\n    margin-bottom: 25px;
\n}
\n
\n.course-page-content,
\n.course-page-content p,
\n.course-page-content li  {
\n    line-height: 1.5
\n}
\n
\nul.small-bullets > li {
\n    padding-left: 1.5625em;
\n    margin-bottom: 1.625em;
\n}
\n
\nul.small-bullets > li:last-child {
\n    margin-bottom: .5em;
\n}
\n
\nul.small-bullets > li:before {
\n    border-radius: 50%;
\n    left: .625em;
\n    top: .625em;
\n    width: .375em;
\n    height: .375em;
\n    opacity: .8;
\n}
\n
\n.course-list-header {
\n    border-bottom: none;
\n    margin-bottom: .625rem;
\n}
\n
\n.course-list-display-option:after {
\n    background: #d0d0d0;
\n}
\n
\n.course-list.course-list\-\-grid {
\n    margin-left: -15px;
\n    margin-right: -15px;
\n}
\n
\n.course-list.course-list\-\-grid > div {
\n    padding-left: 15px;
\n    padding-right: 15px;
\n}
\n
\n.course-list\-\-grid .course-widget {
\n    border-color: #bfbfbf;
\n}
\n
\n.course-widget-category {
\n    background: var(\-\-bright_purple);
\n    color: #FFF;
\n}
\n
\n.course-list\-\-grid .course-widget-price {
\n    background-color: var(\-\-primary);
\n    color: #FFF;
\n}
\n
\n.course-list\-\-list .course-widget-price-original,
\n.course-list\-\-list .course-widget-price-current {
\n    color: var(\-\-primary);
\n}
\n
\n.course-list-grid .course-widget-time_and_date {
\n    border-color: #b7b7b7;
\n}
\n
\n.course-list\-\-list .course-list-item {
\n    margin-bottom: 2.5rem;
\n    padding: 1.25rem 1.875rem 1.125rem .6875rem;
\n}
\n
\n.course-list\-\-list .course-widget-location_and_tags { border-color: #ccc; }
\n
\n.course-list-item {
\n    \-\-category-color: var(\-\-dark_purple);
\n    font-weight: normal;
\n}
\n
\n.course-list-item-read_more {
\n    color: var(\-\-dark_purple);
\n}
\n
\n.course-list-item-read_more::after {
\n    content: var(\-\-arrow-dark_purple-lg);
\n    position: relative;
\n    top: 6px;
\n    margin-left: .5em;
\n}
\n
\n.course-list-item.list_only {
\n    border-left-width: 4px;
\n    box-shadow: 0px 4px 25px rgba(0, 0, 0, .11);
\n}
\n.course-list-item.grid_only {
\n    border-top-width: 4px;
\n}
\n
\n.course-list-item-data svg {
\n    color: var(\-\-dark_purple);
\n}
\n
\n.course-list-item-summary {
\n    font-size: .875rem;
\n    line-height: 1.25;
\n}
\n
\n.course-list-item p {
\n    margin-top: 0
\n}
\n
\n.course-list .course-list-item-header {
\n    font-size: 1.5rem;
\n    line-height: 1.25;
\n    margin-top: .1875rem;
\n    margin-bottom: .4375rem;
\n}
\n
\n@media screen and (max-width: 767px) {
\n    .course-list {
\n        padding-left: 1px;
\n        padding-right: 1px;
\n    }
\n
\n    .course-list-item.list_only {
\n        border-left-width: 2px;
\n        margin-bottom: 30px;
\n        padding-left: 18px;
\n        padding-right: 20px;
\n        padding-bottom: 12px;
\n    }
\n
\n    .course-list\-\-list .course-list-item-data {
\n        margin-top: .75rem;
\n    }
\n
\n    .course-list\-\-list .course-list-item-data > li {
\n        line-height: 1;
\n        min-width: 0;
\n        padding-right: .75em;
\n        white-space: nowrap;
\n    }
\n
\n    .course-list\-\-list .course-list-item-data > li[data-type=\"date\"] {
\n        flex: 2;
\n    }
\n
\n    .course-list-item-data > li svg { width: 17px; }
\n    .course-list-item-data > li[data-type=\"date\"] svg { width: 20px; }
\n
\n    .course-list-item p { line-height: 1.25; }
\n
\n    .course-list-item-read_more {
\n        float: right;
\n        font-size: 1rem;
\n    }
\n
\n    .course-list-item-read_more::after {
\n        margin-left: 3px;
\n    }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .course-list\-\-grid .course-list-item {
\n        padding: 1rem 1.3125rem;
\n    }
\n
\n    .course-list\-\-grid .course-list-item-category {
\n        margin-bottom: .5rem;
\n    }
\n
\n    .course-list\-\-grid .course-list-item-header {
\n        font-size: 1.25rem;
\n        letter-spacing: -.01em;
\n        line-height: 1.2;
\n    }
\n
\n    .course-list\-\-grid .course-list-item-data {
\n        margin-bottom: auto;
\n    }
\n
\n    .course-list\-\-grid .course-list-item-data > li {
\n        margin: .75rem 0;
\n    }
\n    .course-list\-\-list .course-list-item-data > li {
\n        min-width: 10em;
\n        white-space: nowrap;
\n    }
\n}
\n
\n
\n.simplebox-programme-for {
\n    background: var(\-\-dark_purple);
\n    color: #fff;
\n    font-weight: 300;
\n    padding-bottom: 2.25rem;
\n}
\n
\n.simplebox-programme-for,
\n.simplebox-programme-for p {
\n    font-size: 1.25rem;
\n    line-height: 1.25;
\n}
\n
\n.simplebox-programme-for h2 {
\n    font-size: 2.1875rem;
\n    letter-spacing: 0;
\n    line-height: 1.25714286;
\n    margin: 0 0 .5em;
\n}
\n
\n.simplebox-schedule .simplebox-column.simplebox-column {
\n    background: #e5e2ef;
\n    box-shadow: 0 4px 35px rgba(0, 0, 0, .18);
\n    margin-bottom: 30px;
\n    padding: 0 15px 0 0;
\n}
\n
\n.simplebox-schedule .simplebox-content {
\n    background: #fff;
\n    border-radius: 0 0 2.5rem 0;
\n    display: flex;
\n    flex-direction: column;
\n    padding: 24px 15px 24px 20px;
\n}
\n
\n.simplebox-schedule h3 {
\n    margin-bottom: 13px;
\n}
\n
\n.simplebox-schedule li {
\n    line-height: 1.25;
\n}
\n
\n.simplebox-schedule .simplebox-content > :first-child,
\n.simplebox-schedule ul > :first-child {
\n    margin-top: 0;
\n}
\n
\n.simplebox-schedule .simplebox-content > :last-child {
\n    margin-bottom: 0;
\n}
\n
\n.simplebox-shadowed > .simplebox-columns { margin-left: auto; margin-right: auto; }
\n.simplebox-shadowed > .simplebox-columns > :first-child { margin-left: 0 }
\n.simplebox-shadowed > .simplebox-columns > :last-child { margin-right: 0 }
\n
\n.course-details-header {
\n    background: none !important; /* overwrite inline style */
\n    position: relative;
\n}
\n
\n@media screen and (max-width: 767px) {
\n    .course-details-header-main {
\n        padding: 38px 20px 14px;
\n        position: relative;
\n    }
\n
\n    .course-details-header {
\n        background-color: transparent;
\n        background-position: center top;
\n        background-size: auto 350px;
\n        position: relative;
\n    }
\n
\n    .course-details-header-main:before {
\n        content: \'\';
\n        background-color: var(\-\-dark_purple);
\n        background-image: var(\-\-background-image);
\n        background-size: cover;
\n        position: absolute;
\n        top: 0;
\n        right: 0;
\n        left: 0;
\n        bottom: 0;
\n        z-index: -1;
\n    }
\n
\n    .course-details-header .course-details-header-breadcrumbs {
\n        font-size: 14px;
\n        margin-bottom: 30px;
\n    }
\n
\n    .course-details-header h1 {
\n        font-size: 35px;
\n        line-height: 1;
\n        max-width: 280px;
\n    }
\n
\n    .course-details-header-summary {
\n        font-size: 14px;
\n        min-height: 2.71428571429em;
\n    }
\n
\n    .simplebox-programme-for {
\n        margin-bottom: 38px;
\n        padding: 16px 18px 18px;
\n    }
\n
\n    .simplebox-schedule {
\n        padding-left: 29px;
\n        padding-right: 19px;
\n    }
\n
\n    .simplebox-schedule .simplebox-column {
\n        padding: 0 10px 0 0 !important;/* todo: make the rule this is overwriting less-specific */
\n    }
\n
\n    .simplebox-schedule .simplebox-content li:last-child {
\n        margin-bottom: .5rem;
\n    }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .course-details-header::before {
\n        content: \'\';
\n        background-color: var(\-\-primary);
\n        background-image: var(\-\-background-image);
\n        background-size: cover;
\n        position: absolute;
\n        top: 0;
\n        left: 0;
\n        right: 0;
\n        bottom: 0;
\n        z-index: -1
\n    }
\n
\n    .course-details-header-main {
\n        height: auto;
\n    }
\n
\n    .course-details-header-main .checkout-progress {
\n        margin-top: 13px;
\n        margin-bottom: 67px;
\n    }
\n
\n    .course-details-menu-inner {
\n        background: #fff;
\n        position: absolute;
\n        top: 0;
\n        right: 0;
\n        left: 0;
\n    }
\n
\n    #course-details-menu-sticky-start,
\n    .course-details-menu:not(.is_fixed) {
\n        background: #fff;
\n        position: relative;
\n        top: -24px;
\n        right: 0
\n    }
\n
\n    #course-details-menu-sticky-start {
\n        clear: both;
\n    }
\n
\n    .course-details-menu:not(.is_fixed) {
\n        left: calc(100% - 280px);
\n    }
\n
\n    .checkout-progress ul {
\n        margin-left: -33px;
\n    }
\n
\n    .course-details-header .checkout-progress ul {
\n        width: calc(100% + 33px);
\n        max-width: none;
\n    }
\n
\n    simplebox-course-intro .simplebox-column {
\n        width: calc(100% - 445px);
\n    }
\n
\n    .simplebox-programme-for,
\n    .simplebox-programme-for p {
\n        font-size: 1.5rem;
\n    }
\n
\n    .simplebox-programme-for {
\n        background-image: linear-gradient(76.5deg, var(\-\-dark_purple) calc(50vw + 277px), var(\-\-bright_purple) 9.0%);
\n        padding-top: 2.3125rem;
\n    }
\n
\n    .simplebox-programme-for .simplebox-column { max-width: 630px; }
\n
\n    .simplebox-schedule .simplebox-content {
\n        padding: 34px 12px 22px 35px;
\n    }
\n
\n    .simplebox-schedule h3 {
\n        margin-bottom: .83333333333em;
\n    }
\n
\n    .simplebox-programme-for p:last-child {
\n        margin-bottom: 0;
\n    }
\n}
\n@media screen and (min-width: 768px) and (max-width: 1024px) {
\n
\n    .course-details-menu.is_fixed {
\n        position: fixed;
\n        top: 0;
\n        right: 20px;
\n    }
\n}
\n
\n@media screen and (min-width: 1024px) {
\n    .checkout-progress ul {
\n        margin-left: -67px;
\n    }
\n
\n    .course-details-header .checkout-progress ul {
\n        width: calc(100% + 67px);
\n    }
\n}
\n
\n.pagination {
\n    text-align: center;
\n}
\n.course-details-menu-inner {
\n        background: #fff;
\n        position: absolute;
\n        top: 0;
\n        right: 0;
\n        left: 0;
\n}
\n
\n.pagination li {
\n    margin: 0;
\n}
\n
\n.pagination a {
\n    background: var(\-\-light-gray);
\n    color: var(\-\-main_gray);
\n    height: auto;
\n    padding: .5625em .5em .5em;
\n    text-decoration: none;
\n}
\n
\n.pagination a.current {
\n    background: var(\-\-lighter_gray);
\n    box-shadow: none;
\n    color: var(\-\-bright_purple);
\n}
\n
\n.pagination-prev a,
\n.pagination-next a {
\n    background: var(\-\-primary);
\n    color: #fff;
\n}
\n
\n.pagination-prev a::before,
\n.pagination-next a::before {
\n    border-color: #fff;
\n    position: relative;
\n    top: -1px;
\n}
\n
\n@media screen and (max-width: 767px) {
\n    .pagination-wrapper {
\n        margin-top: 30px;
\n        margin-bottom: 6px;
\n        text-align: center;
\n    }
\n
\n    .pagination a {
\n        font-size: 1rem;
\n        height: 2.1875em;
\n    }
\n
\n    .pagination a.current {
\n        box-shadow: none;
\n        color: var(\-\-dark_purple);
\n    }
\n
\n    .pagination a:not(.current) {
\n        background: #E6E7E7;
\n        color: #B2B2B2;
\n    }
\n
\n    .pagination .pagination-prev a,
\n    .pagination .pagination-next a {
\n        background: var(\-\-primary);
\n        color: #fff;
\n    }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .pagination-wrapper {
\n        text-align: right;
\n    }
\}
\n
\n.course-banner-overlay {
\n    background-color: rgba(255, 255, 255, .8);
\n    color: #000;
\n}
\n
\n.fixed_sidebar-header {
\n    background: var(\-\-primary);
\n    color: #fff;
\n}
\n
\n.booking-form h2 {
\n    border: none;
\n}
\n
\n.booking-required_field-note {
\n    color: var(\-\-primary);
\n}
\n
\n@media screen and (max-width: 767px) {
\n    .contact-map-overlay {
\n        background-color: var(\-\-primary);
\n    }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .contact-map-overlay-content {
\n        background: var(\-\-primary);
\n        background: rgba(111,120,170, .8);
\n    }
\n}
\n
\n.availability-timeslot .highlight {
\n    color: var(\-\-primary);
\n}
\n
\n.availability-timeslot.booked {
\n    border-color: var(\-\-primary);
\n}
\n
\n.availability-timeslot.booked .highlight {
\n    color: var(\-\-primary);
\n}
\n
\n.timeline-swiper .swiper-slide.selected {
\n    background: var(\-\-bright_purple);
\n    color: #fff;
\n}
\n
\n.timeline-swiper-highlight {
\n    border: 1px solid var(\-\-bright_purple);
\n    color: var(\-\-bright_purple);
\n}
\n
\n.timeline-swiper-prev,
\n.timeline-swiper-next {
\n    color: #var(\-\-bright_purple);
\n}
\n
\n\/\* Footer \*\/
\n.page-footer {
\n    margin-top: 1.5em;
\n    padding: 1.75em;
\n}
\n.page-footer .page-content h1 {
\n    border: none;
\n    color: inherit;
\n    margin: .5em 0;
\n}
\n
\n.footer {
\n    border-top: 1px solid #1d1a3b;/* To ensure the background covers the padding */
\n    background: #1d1a3b;
\n    color: #fff;
\n    margin-top: 0;
\n}
\n
\n.footer-logo img {
\n    width: 485px;
\n}
\n
\n.footer h2,
\n.footer-column-title {
\n    color: #fff;
\n    font-size: 1.5rem;
\n    line-height: 1.17;
\n    font-weight: bold;
\n    margin-bottom: 9px;
\n    text-transform: capitalize;
\n}
\n
\n.footer-stats {
\n    min-height: 0;
\n}
\n
\n.footer-stat {
\n    color: var(\-\-primary);
\n    font-size: 1rem;
\n}
\n
\n.footer-stats-row.footer-stats-row {
\n    padding-bottom: 1.25rem;
\n}
\n
\n.footer-stat h2:after {
\n    border-color: var(\-\-bright_purple);
\n}
\n
\n.footer-stats-row {
\n    border-bottom: 1px solid var(\-\-primary);
\n    max-width: 1040px;
\n}
\n
\n.footer-stats-list {
\n    align-items: center;
\n    flex-wrap: wrap;
\n}
\n
\n.footer-social {
\n    display: none;
\n}
\n
\n.footer-copyright {
\n    border: none;
\n    font-size: 13px;
\n    letter-spacing: -.03em;
\n    line-height: 1.30769231;
\n    margin-top: 1.1875rem;
\n    padding-top: 6px;
\n}
\n
\n.footer-columns {
\n    border-top: none;
\n    padding-top: 2.6875rem;
\n}
\n
\n.footer-columns .row {
\n    display: flex;
\n}
\n
\n.footer-column\-\-contact {
\n    order: 1;
\n}
\n
\n.footer-column-content {
\n   font-size: .875rem;
\n}
\n
\n.footer-column\-\-contact .footer-column-content {
\n   letter-spacing: -.035em;
\n}
\n
\n.footer-column h4 {
\n   line-height: 1.5;
\n}
\n
\n.footer-column\-\-contact h4 {
\n    font-weight: normal;
\n}
\n
\n.footer-address-line,
\n.footer-contact-items {
\n    line-height: 1.25;
\n}
\n
\n.footer-column li {
\n   line-height: 2;
\n   margin: 0;
\n}
\n
\n.footer-contact-items {
\n    margin-top: 23px;
\n}
\n
\n.footer-contact-items dt {
\n    display: none;
\n}
\n
\n.footer-contact-items dd {
\n    display: block;
\n    float: none;
\n}
\n
\n.footer .form-input {
\n    background: none;
\n    border-radius: 0;
\n    color: #fff;
\n    padding: .475em .8em;
\n}
\n
\n.footer .form-input::-webkit-input-placeholder { color: #fff; font-weight: 300; }
\n.footer .form-input::-moz-placeholder          { color: #fff; font-weight: 300; }
\n.footer .form-input:-ms-input-placeholder      { color: #fff; font-weight: 300; }
\n
\n.newsletter-signup-form {
\n    padding-top: 0;
\n}
\n
\n.newsletter-signup-form .button {
\n    border-radius: 0;
\n    font-size: 18px;
\n    font-weight: bold;
\n    padding: .7em;
\n    text-transform: none;
\n}
\n
\n.newsletter-signup-form .form-group {
\n    margin-bottom: 11px;
\n}
\n
\n.newsletter-signup-terms a {
\n    color: var(\-\-primary);
\n}
\n
\n.newsletter-signup-terms-text p {
\n    font-size: inherit;
\n    margin: 0;
\n}
\n
\n.newsletter-signup-terms .form-checkbox-helper {
\n    border: 1px solid #c4c4c4;
\n    border-radius: 0;
\n    background: none;
\n    font-size: 1.25rem;
\n    margin-right: .5em
\n}
\n
\n.footer .button {
\n    background: none;
\n    border: 1px solid #fff;
\n    color: #fff;
\n    font-size: 1.125rem;
\n    padding: .555555556em;
\n}
\n
\n.footer a.read_more {
\n    color: #fff;
\n    font-size: 1.25rem;
\n}
\n
\n.footer a.read_more:after {
\n    content: var(\-\-arrow-white-lg);
\n    margin-left: .5em;
\n    top: .25em;
\n}
\n
\n@media screen and (max-width: 767px) {
\n    .row.page-footer {
\n        padding-left: 19px;
\n        padding-right: 19px;
\n    }
\n
\n    .footer {
\n        padding: 10px 5px 0;
\n    }
\n
\n    .footer-columns {
\n        padding-top: 0;
\n    }
\n
\n    .footer-columns .row {
\n        flex-direction: column;
\n    }
\n
\n    .footer-column.has_sublist {
\n        border-color: rgba(255, 255, 255, .1);
\n    }
\n
\n    .footer .footer-column-content {
\n        padding-left: 0;
\n        padding-right: 0;
\n    }
\n
\n    .row > .footer-column.has_sublist {
\n        margin-left: 15px;
\n        margin-right: 15px;
\n        padding-left: 0;
\n        padding-right: 0;
\n    }
\n
\n    .footer-stats {
\n        margin-left: 15px;
\n        margin-right: 15px;
\n        padding-top: 0;
\n        padding-bottom: 0;
\n    }
\n
\n    .footer-stat.footer-stat {
\n        margin-top: 1rem;
\n        text-align: left;
\n        width: 100%;
\n    }
\n
\n    .footer-stat [src\*=\"app-store\"] {
\n        float: right;
\n        height: 40px;
\n        padding: 0 20px;
\n    }
\n
\n    .footer-column.has_sublist { border-bottom: 1px solid rgba(255, 255, 255, .1); }
\n
\n    .footer-column\-\-contact.has_sublist { border-bottom: none; }
\n
\n    .footer-contact-more { border-top: 1px solid rgba(255, 255, 255, .1); }
\n
\n    .footer-column-title {
\n        font-size: 18px;
\n        line-height: 25px;
\n        padding:  1rem 0 .5rem;
\n    }
\n
\n    .footer-column.has_sublist .footer-column-title:before {
\n        content: \'\';
\n        border: solid #fff;
\n        border-width: 0 1px 1px 0;
\n        float: right;
\n        width: 12px;
\n        height: 12px;
\n        position: relative;
\n        right: 17px;
\n        top: 4px;
\n        transform: rotate(45deg);
\n    }
\n
\n    .footer-column.has_sublist .footer-column-title.expanded:before {
\n        transform: rotate(225deg);
\n    }
\n
\n    .footer-social-icons {
\n        display: flex;
\n        align-items: center;
\n    }
\n
\n    .footer-social-icons h5 {
\n        margin: 1.25rem 0;
\n    }
\n
\n    .footer-social-icons p {
\n        margin-left: auto;
\n    }
\n
\n    .footer-social-icons img {
\n        width: 42px;
\n        height: 42px;
\n        margin-left: .5rem;
\n    }
\n
\n    .footer-copyright {
\n        font-size: .75rem;
\n        margin: 0;
\n        padding: 9px 0;
\n    }
\n
\n    .footer-copyright .row {
\n        padding-left: 15px;
\n        padding-right: 15px;
\n    }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .footer h2,
\n    .footer-column-title {
\n        color: var(\-\-primary);
\n    }
\n
\n    .footer-stats {
\n        padding-top: 4rem;
\n    }
\n
\n    .footer-stat {
\n        font-size: 1rem;
\n        width: auto;
\n        margin: 0 0 0 auto;
\n    }
\n
\n    .footer-stat:first-child {
\n        margin-left: 0;
\n    }
\n
\n    .footer-stat .button {
\n        min-width: 200px;
\n        padding: 1rem;
\n    }
\n
\n    .footer-social-icons h5 {
\n        margin: 0 0 .5rem;
\n    }
\n
\n    .footer-social-icons p {
\n        margin-bottom: 1rem;
\n    }
\n
\n    .footer-social-icons a + a img {
\n        margin-left: .8rem;
\n    }
\n}
\n
\n@media screen and (min-width: 1200px) {
\n    .footer-column\-\-contact {
\n        width: 310px;
\n    }
\n}
\n
\n
\n\/\* Dropdown filters \*\/
\n.search-filter-total {
\n    color: var(\-\-primary);
\n}
\n
\n.search-filters :checked ~ .form-checkbox-helper,
\n.search-filters :checked ~ .form-radio-helper,
\n.search-filters :checked ~ .form-checkbox-label,
\n.search-filters :checked ~ .form-radio-label {
\n    border-color: var(\-\-primary);
\n    color: var(\-\-primary);
\n}
\n
\n.search-filters :checked + .form-radio-helper:after {
\n    background-color: var(\-\-primary);
\n}
\n
\n    .footer-copyright-cms {
\n        border-bottom: 1px solid rgba(255, 255, 255, .1);
\n        float: left;
\n        text-align: left;
\n        padding-bottom: 20px;
\n        width: 100%;
\n    }
\n
\n    .footer-bottom .row {
\n        border-top: 0;
\n        padding: 0 15px;
\n    }
\n
\n    .footer-bottom .simplebox-column {
\n        margin: 0;
\n        padding: 1rem 0;
\n        width: 50%;
\n        height: 5.75rem;
\n    }
\n
\n    .footer-bottom .simplebox-column-2 {
\n        order: 1;
\n    }
\n
\n    .footer-bottom .simplebox-column-1,
\n    .footer-bottom .simplebox-column-3 {
\n        border-bottom: 1px solid rgba(255, 255, 255, .1);
\n    }
\n
\n    .footer-bottom img[src*=\"Ibec-Academy"] {
\n        max-width: 147px;
\n    }
\n
\n    .footer-bottom img[src*=\"EQA_"] {
\n        max-width: 100px;
\n    }
\n
\n    .footer-social-link {
\n        display: inline-block;
\n        margin-right: .5rem;
\n    }
\n
\n    .footer-social-link img {
\n        max-width: 42px;
\n    }
\n
\n    .footer-bottom-powered_by p {
\n        padding-top: 1rem;
\n    }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .search-filter-dropdown.filter-active > button,
\n    .search-filters-clear {
\n        color: var(\-\-primary);
\n    }
\n
\n    .footer-social-link {
\n        display: inline-block;
\n        margin-right: 1.5rem;
\n    }
\n}
\n
\/\* Login \*\/
\n.login-form-container.login-form-container .modal-header {
\n    background-color: #fff;
\n    color: var(\-\-primary);
\n}
\n
\n.login-form-container.login-form-container .nav-tabs > li > a {
\n    color: #303030;
\n    font-weight: bold;
\n    text-transform: uppercase;
\n}
\n
\n.signup-text a,
\n.login-form-container.login-form-container .modal-footer a,
\n.login-form-container.login-form-container .nav-tabs > li.active > a {
\n    color: var(\-\-primary);
\n}
\n
\n.signup-text a:hover,
\n.login-form-container .modal-footer a:hover,
\n.login-form-container .nav-tabs > li > a:hover {
\n    color: var(\-\-primary-hover);
\n}
\n
\n.login-buttons .btn {
\n    text-transform: uppercase;
\n}
\n
\n
\n
\n\/\* Misc \*\/
\n.contact\-\-left .ui-tabs-nav li a {
\n    background: var(\-\-light_gray);
\n    color: var(\-\-main_gray);
\n    width: auto;
\n    min-width: 9rem;
\n    padding: 0.5625em;
\n}
\n
\n.contact\-\-left .ui-tabs-nav .ui-tabs-active a {
\n    background: var(\-\-dark_purple);
\n    border-color:var(\-\-dark_purple);
\n    color: #fff;
\n}
\n
\n.contact\-\-left .ui-tabs-nav.ui-tabs-nav li a { font-weight: bold;}
\n
\n
\n.contact\-\-left .ui-widget-content {
\n    border-color: var(\-\-middle-gray);
\n    color: var(\-\-darkened_gray);
\n    font-weight: normal;
\n    line-height: 1.32;
\n}
\n
\n.contact\-\-left .ui-widget-content p {
\n    font-weight: inherit;
\n    letter-spacing: -.005em;
\n    margin-bottom: 1rem;
\n    padding: 0;
\n}
\n
\n.contact\-\-left .checkout-invoice-footer > :last-child {
\n    margin-bottom: 0;
\n}
\n
\n.checkout-invoice-wrapper .form-label {
\n    display: block;
\n    font-weight: bold;
\n    margin: .9em 0 .5625em;
\n}
\n
\n.checkout-invoice-wrapper abbr {
\n    text-decoration: none;
\n}
\n
\n.checkout-invoice-wrapper input.form-input,
\n.checkout-invoice-wrapper .form-input input {
\n    max-width: 250px;
\n    padding-top: .75em;
\n    padding-bottom: .8125em;
\n}
\n
\n.layout-checkout .header {
\n    border-bottom: 1px solid var(\-\-light_gray);
\n}
\n
\n.layout-checkout .content {
\n    margin-top: 35px;
\n}
\n .layout-content_wide .content {
\n    \/\*margin-top: 4.3rem;\*\/
\n}
\n
\n.layout-checkout h1 {
\n    color: var(\-\-dark_purple);
\n    margin-bottom: 0.145833333em;
\n}
\n
\n.checkout-sales_quote-checkbox-wrapper {
\n    margin-bottom: 3.125rem;
\n}
\n
\n.checkout-sales_quote-notice {
\n    max-width: 650px;
\n    margin-bottom: 4.375rem;
\n}
\n
\n.checkout-right-sect .item-summary-head {
\n    padding: 5px 20px 4px;
\n}
\n
\n.right-section .gray-box h4 {
\n    font-size: 18px;
\n    font-weight: bold;
\n    letter-spacing: -.045em;
\n    text-transform: lowercase;
\n}
\n
\n.right-section .gray-box h4:first-letter {
\n    text-transform: uppercase;
\n}
\n
\n
\n.checkout-right-sect .btn-close:hover {
\n    color: var(\-\-bright_purple);
\n    border-color: var(\-\-bright_purple);
\n}
\n
\n.prepay-box { padding: 1.25rem 1.25rem .625rem; }
\n
\n.prepay-box h5 { color: var(\-\-dark_purple); }
\n
\n.checkout-right-sect .sub-total,
\n.prepay-box li.total  {
\n    color: var(\-\-primary);
\n}
\n
\n.checkout-item .row.gutters { align-items: flex-start; margin: 0 0 .5rem; }
\n.checkout-item .row.gutters [class*=\"col\"] {padding-left: 0; padding-right: 0; }
\n
\n.checkout-right-sect .checkout-item-remove {
\n    color: var(\-\-dark_gray);
\n    font-size: 1rem;
\n    top: 2rem;
\n}
\n
\n.checkout-item-info,
\n.checkout-item-timeslots-count {
\n    font-size: 16px;
\n    font-weight: 400;
\n}
\n
\n.checkout-item-fee { font-size: 16px; }
\n
\n
\n.checkout-item-timeslots {
\n    color: var(\-\-main_gray);
\n    column-count: 2;
\n    font-size: 1em;
\n    font-weight: 400;
\n}
\n
\n.checkout-right-sect .total-pay {
\n    border-top: 0;
\n    padding: 0 1.25rem;
\n}
\n
\n.checkout-right-sect .total-pay > ul {
\n    border-top: 1px solid #dbdbdb;
\n    padding-top: 1rem;
\n}
\n.checkout-right-sect .discountItemPlaceholder {
\n    padding: 0;
\n}
\n
\n.discountItemPlaceholder ~ .discountItemPlaceholder {
\n    border: 0;
\n}
\n
\n.discountItemPlaceholder.discountItemPlaceholder p {
\n    font-size: .9375rem;
\n    font-weight: normal;
\n}
\n
\n.discountItemPlaceholder .amount {
\n    font-weight: inherit;
\n}
\n
\n.checkout-item + .discountItemPlaceholder {
\n    padding-top: 1.25rem;
\n}
\n
\n.checkout-breakdown.checkout-breakdown {
\n    margin-top: 1.25rem ;
\n}
\n
\n.checkout-coupon-wrapper .form-input {
\n    border-radius: 0;
\n    border-color: var(\-\-middle-gray);
\n}
\n
\n.right-section .button-action button {
\n    font-weight: bold;
\n    letter-spacing: -.025em;
\n}
\n
\n.checkout-captcha-container {
\n    padding: 0 20px;
\n    transform: scale(0.795) !important;
\n    transform-origin: 13px;
\n}
\n
\n.checkout-heading {
\n    background-color: var(\-\-dark_purple);
\n    border-radius: 0;
\n    color: #fff;
\n    margin-bottom: 0;
\n    padding-top: .416666667em;
\n    padding-bottom: .416666667em;
\n    text-transform: capitalize;
\n}
\n
\n.checkout-heading .fa {
\n    display: none;
\n}
\n
\n.checkout-form .theme-form-content {
\n    color: var(\-\-darkened_gray);
\n    margin-bottom: 3rem;
\n}
\n
\n/* temporary, until this is setting-controlled */
\n#checkout-need_details,
\n.checkout-privacy-header {
\n    display: none;
\n}
\n
\n.contact\-\-left .form-group {
\n    margin-left: -10px;
\n    margin-right: -10px;
\n}
\n
\n.contact\-\-left .form-group > [class*=\"col-\"] {
\n    padding-left: 10px;
\n    padding-right: 10px;
\n}
\n
\n.contact\-\-left .form-input\-\-pseudo input,
\n.contact\-\-left .form-input\-\-pseudo select {
\n    height: 2.7em;
\n}
\n
\n.checkout-form ::-webkit-input-placeholder, .bookings_form ::-webkit-input-placeholder {
\n    color: var(\-\-darkened_gray);
\n    font-weight: 300;
\n}
\n.checkout-form .form-input--select .form-input--pseudo-label , .bookings_form .form-input--select .form-input--pseudo-label {
\n    color: var(\-\-darkened_gray);
\n}
\n
\n.checkout-form label:not([class]),
\n.checkout-form .form-label {
\n    display: inline-block;
\n    line-height: 1.32;
\n    margin-bottom: .5em;
\n}
\n
\n.checkout-form .theme-form-inner-content,
\n.billing-inner-content {
\n    margin: 29px 19px 27px;
\n}
\n
\n.contact\-\-left .ui-tabs-panel {
\n    padding: 2rem 1.1875rem 1.6875rem;
\n}
\n
\n.delegate_box {
\n    border-color: var(\-\-primary) !important;/* todo: remove absolute classes from this section and style within template CSS. */
\n    border-radius: 0 !important;
\n}
\n
\n.pay-with > h2 { font-size: 1.25rem; }
\n
\n.checkout-processed_by { font-size: .75rem; }
\n
\n.checkout-privacy-header {
\n    color: var(\-\-primary);
\n}
\n
\n.privacy-content {
\n    border: none;
\n    letter-spacing: 0.015em;
\n    margin-top: 3rem;
\n    max-width: 745px;
\n}
\n
\n.privacy-inner-content {
\n    font-size: 14px;
\n    line-height: 1.71428571;
\n    margin: 0;
\n}
\n
\n.privacy-content h6 {
\n    font-size: 14px;
\n    margin: 0 0 .5em;
\n}
\n
\n.privacy-content p {
\n    margin: 0 0 2.5em;
\n}
\n
\n.privacy-content a {
\n    color: var(\-\-primary);
\n}
\n
\n.terms-txt {
\n    font-size: 12px;
\n    letter-spacing: -0.025em;
\n    line-height: 1.115;
\n}
\n
\n.terms-txt .form-row {
\n    margin: -5px 0 10px;
\n}
\n
\n.terms-txt .form-row > div {
\n    padding: 0 5px;
\n}
\n
\na.item-summary-head {
\n    color: var(\-\-bright_purple);
\n}
\n
\n.search-package-available h2 {
\n    color: #4f4e4f;
\n}
\n
\n.search-package-available .available-text  h4 {
\n    border-color: #eee;
\n    color: var(\-\-primary);
\n}
\n
\n.search-package-available .show-more {
\n    background: #fff;
\n    border: 1px solid #1E3B49;
\n    color: var(\-\-bright_purple);
\n}
\n
\n.prepay-box h6 {
\n    color: var(\-\-primary);
\n}
\n
\n.right-section .gray-box { border: none; border-radius: 0; }
\n
\n.right-section .button-action {
\n    padding: 0 21px 20px 19px;
\n}
\n
\n.checkout-right-sect li.sub-total.sub-total {
\n    background: var(\-\-dark_purple);
\n    color: #fff;
\n    font-weight: bold;
\n    letter-spacing: .04em;
\n    margin: 1.75rem -1.25rem 1.5625rem;
\n    padding: 1rem 1.25rem;
\n    text-transform: none;
\n}
\n
\n@media screen and (max-width: 767px) {
\n    .layout-checkout .content > .container {
\n        padding-left: 1px;
\n        padding-right: 1px;
\n    }
\n
\n    .layout-checkout .checkout-progress {
\n        margin-bottom: 0;
\n    }
\n
\n    .checkout-item-title {
\n        font-size: 17px;
\n        line-height: 22px;
\n        letter-spacing: -.0.02em;
\n        max-width: 200px;
\n    }
\n
\n    .checkout-heading {
\n        padding-left: 20px;
\n        padding-right: 20px;
\n    }
\n
\n    .checkout-form .theme-form-inner-content .form-group [class*=\"col\"],
\n    .billing-inner-content .form-group [class*=\"col\"] {
\n        margin-bottom: 19px;
\n    }
\n
\n    .checkout-form .theme-form-inner-content .form-group [class*=\"col\"]:last-child,
\n    .billing-inner-content .form-group [class*=\"col\"]:last-child {
\n        margin-bottom: 0;
\n    }
\n
\n    .checkout-form .theme-form-inner-content .form-checkbox-helper {
\n        float: left;
\n        margin-bottom: 1em;
\n        margin-right: 1em;
\n    }
\n
\n    .checkout-form .theme-form-inner-content .form-checkbox-label {
\n        color: var(\-\-dark_gray);
\n        font-size: 14px;
\n    }
\n
\n    [for=\"checkout-special_requirements\"] {
\n        display: inline-block;
\n        line-height: 1.32;
\n        margin-bottom: 18px;
\n    }
\n
\n    [for=\"checkout-cvv\"],
\n    label.new-card {
\n        margin-bottom: 9px;
\n    }
\n
\n    .delegate_box h3 {
\n        font-size: 1.25rem;
\n        letter-spacing: -.005em;
\n        line-height: 1.32;
\n    }
\n
\n    #payment-tabs > ul {
\n        padding-left: 20px;
\n    }
\n
\n    .contact\-\-left .ui-tabs-nav li {
\n        margin-right: .25em;
\n        margin-bottom: 0;
\n        width: auto;
\n}
\n
\n    .contact\-\-left .ui-tabs-nav li a { width: 6.5em; }
\n
\n    .contact\-\-left .ui-tabs-panel {
\n        border: 1px solid var(\-\-light_gray);
\n        clear: both;
\n    }
\n
\n    [id=\"checkout-continue\"] { margin-bottom: 40px; }
\n
\n    .checkout-right-sect.gray-box {
\n        background: var(\
-\-lighter_gray);
\n        margin: 20px
\n    }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .checkout-form .theme-form-inner-content,
\n    .billing-inner-content {
\n        margin: 39px 19px 20px;
\n    }
\n
\n    .privacy-content {
\n        margin-bottom: 5.5rem;
\n    }
\n}
\n
\n@media screen and (min-width: 1200px) {
\n    .left-section {
\n        width: 830px;
\n    }
\n
\n    .right-section, .right-section .gray-box {
\n        width: 280px;
\n    }
\n}
\n
\n.custom-calendar .booking-date-button {
\n    background-color: var(\-\-primary);
\n    color: #fff;
\n}
\n
\n.custom-calendar .booking-date-button:hover {
\n    background-color: var(\-\-bright_purple);
\n}
\n
\n.custom-calendar button.booking-date-button.active {
\n    background-color: #fff;
\n    color: var(\-\-primary);
\n}
\n
\n.course-activity-alert,
\n.details-wrap .left-place {
\n    color: #F75A5F;
\n}
\n
\n.number-of-people-viewing {
\n    color: var(\-\-primary);
\n}
\n
\n.search-calendar-course-image .fa {
\n    background-color: var(\-\-primary);
\n    color: #fff;
\n}
\n
\n.custom-calendar tbody td.active,
\n.custom-calendar tbody td.active:hover {
\n    background-color: #fff;
\n    color: var(\-\-primary);
\n}
\n
\n.custom-calendar tbody tr:first-child td {
\n    color: #222;
\n}
\n
\n.package-offers-wrap h2 {
\n    color: var(\-\-primary);
\n    border-color: #c5cecd;
\n}
\n
\n.package-offers-wrap h3 {
\n    color: var(\-\-primary);
\n}
\n
\n.package-offers-wrap .summary-wrap .more,
\n.classes-details-wrap .details-wrap li:first-child {
\n    color: var(\-\-primary);
\n}
\n
\n.classes-details-wrap .details-wrap li:first-child {
\n  background-color: var(\-\-primary);
\n}
\n
\n.details-wrap .remove-booking,
\n.details-wrap .wishlist.remove{
\n    color: var(\-\-primary);
\n}
\n
\n.sidelines:before,
\n.sidelines:after,
\n.details-wrap .price-wrap {
\n    border-color: #e4e4e4;
\n}
\n
\n.details-wrap .time,
\n.details-wrap .price,
\n.details-wrap .fa {
\n    color: var(\-\-primary);
\n}
\n
\n\/\* course results hover \*\/
\n.details-wrap:hover {
\n    background-color: #f9f9f9;
\n    border-color:#d8d8d8 ;
\n}
\n
\n.details-wrap:hover .time,
\n.details-wrap:hover .price,
\n.details-wrap:hover .fa-book {
\n    color: var(\-\-primary);
\n}
\n
\n.details-wrap:hover li:first-child {
\n    background-color: var(\-\-primary);
\n}
\n
\n.details-wrap:hover .sidelines::before,
\n.details-wrap:hover .sidelines::after,
\n.details-wrap:hover .price-wrap {
\n    border-color:var(\-\-primary);
\n}
\n
\n
\n\/\* course results booked \*\/
\n.details-wrap.booked {
\n    border-color:var(\-\-bright_purple);
\n    background-color: #f3f3f3;
\n}
\n
\n.details-wrap.booked .time,
\n.details-wrap.booked .price,
\n.details-wrap.booked .fa-book {
\n    color: var(\-\-bright_purple);
\n}
\n.details-wrap.booked li:first-child {
\n    background-color: var(\-\-bright_purple);
\n}
\n
\n.details-wrap.booked .sidelines::before,
\n.details-wrap.booked .sidelines::after,
\n.details-wrap.booked .price-wrap {
\n    border-color: var(\-\-bright_purple);
\n}
\n
\n.classes-details-wrap .alert-wrap {
\n    background-color: var(\-\-bright_purple);
\n    color: #fff;
\n}
\n
\n.custom-slider-arrow a {
\n    color: #0e2a6b;
\n}
\n
\n.search_courses_right:hover,
\n.search_courses_left:hover,
\n.arrow-left.for-time-slots:hover,
\n.arrow-right.for-time-slots:hover{
\n    color: var(\-\-primary);
\n}
\n
\n.custom-calendar .booking-date-button.already_booked {
\n    background-color: var(\-\-bright_purple);
\n    color: #fff;
\n}
\n
\n.search_history > a {
\n    color: #fff;
\n}
\n
\n.search_history .remove_search_history {
\n    color: #fff;
\n    border-color: #fff;
\n}
\n
\nbody > div > img {
\n  display: block;
\n}
\n
\n.submit-expand {
\n    background: none;
\n    border: none;
\n}
\n
\n.course-selector-form .form-select-plain select {
\n   border-color: #c4c4c4;
\n   border-radius: 5px;
\n}
\n
\n.course-selector-form .button,
\n.course-details-menu .button,
\n.course-details-brochure-modal .button,
\n.button.checkout-complete_booking{
\n    font-size: 1rem;
\n    font-weight: bold;
\n    line-height: 1.25;
\n    padding: .75em .75em .6875em;
\n}
\n
\n.course-details-menu {
\n    box-shadow: 0 4px 25px rgba(0, 0, 0, .11);
\n}
\n
\n..course-details-menu-body {
\n    padding: .8125rem 1.1rem;
\n}
\n
\n.course-details-menu-header {
\n    background: var(\-\-dark_purple);
\n    color: #fff;
\n}
\n
\n.course-details-menu h6 {
\n    color: var(\-\-dark_gray);
\n    font-weight: normal;
\n}
\n
\n.course-details-menu select {
\n    border: 1px solid #C4C4C4;
\n    border-radius: 4px;
\n}
\n
\n.course-details-menu .course-details-price { color: #fff; }
\n
\n.course-details-wishlist-checkbox ~ .checkbox-icon-checked {
\n    color: var(\-\-bright_purple);
\n}
\n
\n.course-details-menu-footer button {
\n    color: var(\-\-dark_purple);
\n    font-family: inherit;
\n    font-weight: normal;
\n}
\n
\n.course-subject-accordion { background-color: #fff; }
\n
\n.course-subject-accordion-li.course-subject-accordion-li {
\n    border-left-color: var(\-\-light_gray)!important;
\n    box-shadow: 0px 0px 3px var(\-\-light_gray) inset;
\n    margin-bottom: .5rem;
\n}
\n
\n.course-subject-accordion-li.active {
\n   border-left-color: var(\-\-bright_purple) !important;
\n   box-shadow: none;
\n}
\n
\n.course-subject-accordion-toggle { color: var(\-\-main_gray); }
\n
\n.course-subject-accordion-toggle.course-subject-accordion-toggle { font-size: 1.25rem; }
\n
\n.course-subject-accordion-toggle.active { color: var(\-\-dark_gray); }
\n
\n.course-subject-accordion-wrapper h3 {
\n   font-size: 28px;
\n   line-height: 1.28571429;
\n   margin-top: 1rem !important; \/\* todo: remove absolute classes from the HTML \*\/
\n   margin-bottom: 23px !important;
\n}
\n
\n.course-subject-accordion-button.read_more {
\n    font-size: 18px;
\n    font-weight: normal;
\n    float: right;
\n    margin-top: 11px;
\n}
\n
\n.course-subject-accordion-button.read_more:after {
\n    content: var(\-\-arrow-bright_purple-lg);
\n    top: 7px;
\n}
\n
\n#book-course {background: var(\-\-success)}
\n.course-details-actions [formaction=\"\/contact-us.html\"] {background: var(\-\-primary)!important;}/* Need to remove the absolute class from this element. */
\n#add_to_waitlist_button {background: var(\-\-success)}
\n
\n/* Re-order the buttons */
\n.course-details-actions {
\n    display: flex;
\n    flex-direction: column;
\n}
\n
\n.course-details-actions [id="book-course"] { order: 0; }
\n.course-details-actions [id="add_to_waitlist_button"] { order: 1; }
\n.course-details-actions [formaction="/contact-us.html"] { order: 2; margin-top: 1rem; }
\n
\n@media screen and (max-width: 767px) {
\n    .simplebox-course-columns .simplebox-columns {
\n        width: 100vw;
\n        max-width: 100%;
\n        margin-left: calc(50% - 50vw);
\n    }
\n
\n    .simplebox-course-columns .simplebox-content {
\n        padding-left: 15px;
\n        padding-right: 15px;
\n    }
\n
\n    .simplebox-course-columns .simplebox-content {
\n        padding-top: 2rem;
\n        padding-bottom: 2rem;
\n    }
\n
\n    .simplebox-course-columns .simplebox-content > :first-child { margin-top: 0;}
\n    .simplebox-course-columns .simplebox-content > :last-child { margin-bottom: 0;}
\n
\n    .simplebox-course-columns .simplebox-column-1 {
\n        background: var(\-\-category-color, var(\-\-primary));
\n        color: #fff;
\n    }
\n
\n    .simplebox-course-columns .simplebox-column-2 {
\n        background: var(\-\-success);
\n        color: #fff;
\n    }
\n
\n    .course-selector-form .form-select-plain {
\n        margin: 1.8rem 0;
\n    }
\n
\n    .course-subject-accordion-wrapper { margin-bottom: .5rem }
\n
\n    .course-subject-accordion-li { border-left-width: .25rem; }
\n
\n    .course-subject-accordion-li.course-subject-accordion-li.course-subject-accordion-li {
\n        padding-left: 5px;
\n    }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .simplebox-course-columns {
\n        background: linear-gradient(to right, var(\-\-category-color, var(\-\-primary)) 50%, var(\-\-success) 50%);
\n        color: #fff;
\n    }
\n
\n    .simplebox-course-columns .simplebox-columns {
\n        max-width: 1298px;
\n    }
\n
\n    .simplebox-course-columns .simplebox-column {
\n        padding: 70px 125px 60px 15px;
\n        margin: 0;
\n    }
\n
\n    .simplebox-course-columns .simplebox-column:nth-child(even) {
\n        padding-left: 117px;
\n        padding-right: 15px;
\n    }
\n
\n    .course-selector-form .form-select-plain {
\n        margin: 1rem 0 1.5rem;
\n    }
\n
\n    .course-subject-accordion-wrapper { margin-bottom: 4rem; }
\n
\n    .course-subject-accordion-wrapper > :nth-child(odd)  { width: 38.75%; }
\n    .course-subject-accordion-wrapper > :nth-child(even) { width: 61.25%; }
\n
\n    .course-subject-accordion {
\n        /*min-height: 36.25rem;*/
\n        max-height: calc(6.125rem * var(\-\-tab-count) - .5rem)
\n        overflow: auto;
\n    }
\n
\n    .course-subject-accordion-toggle.course-subject-accordion-toggle {
\n        font-size: 1.25rem;
\n        padding: 1.7em .5em 1.65em 2.2em;
\n    }
\n}
\n
\n.get_in_touch .simplebox-columns { max-width: 1200px; }
\n.get_in_touch .simplebox-column-1 img { display: block; }
\n.get_in_touch h2 { margin-bottom: 10px; }
\n.get_in_touch h2 { margin-bottom: .222em; }
\n.get_in_touch p { margin: .5rem 0 1.3rem; }
\n.get_in_touch p:last-child { margin-bottom: 2rem; }
\n
\n.simplebox-trainers h1 { color: var(\-\-dark_purple); }
\n
\n@media screen and (max-width: 479px) {
\n    .get_in_touch {
\n        min-height: 360px;
\n        padding-top: 2.5rem;
\n    }
\n
\n    .layout-home .get_in_touch {
\n        min-height: 410px;
\n        padding-top: 0;
\n    }
\n
\n    .get_in_touch .simplebox-column-1 img:only-child {
\n        width: 245px!important;
\n    }
\n
\n    .get_in_touch .simplebox-column-1 {
\n        position: absolute;
\n        bottom: 0;
\n        right: 17%
\n    }
\n
\n    .get_in_touch .simplebox-column-1 {
\n        padding: 0 !important;\/\* The rule this overwrites needs to be made less specific \*\/
\n    }
\n
\n    .get_in_touch .simplebox-column-2{
\n        z-index: 2;
\n    }
\n
\n    .get_in_touch .simplebox-column-2 .simplebox-content {
\n        padding-left: 90px;
\n    }
\n
\n    .get_in_touch .button {
\n        display: block;
\n        margin-left: auto;
\n        max-width: 170px;
\n    }
\n}
\n
\n@media screen and (min-width: 480px) {
\n
\n    .get_in_touch .button:first-child {
\n        margin-right: 1.5rem;
\n    }
\n
\n    .get_in_touch.get_in_touch.get_in_touch .simplebox-column-1 { width: 42% !important; }
\n    .get_in_touch.get_in_touch.get_in_touch .simplebox-column-2 { width: 58% !important; }
\n}
\n
\n@media screen and (max-width: 767px) {
\n    .get_in_touch .button {
\n        margin-bottom: 1rem;
\n    }
\n
\n    .get_in_touch p {
\n        font-size: 16px;
\n        line-height: 1.375;
\n    }
\n
\n    .course-subject-accordion-toggle.course-subject-accordion-toggle {
\n        font-size: 1rem;
\n        padding-top: 27px;
\n        padding-bottom: 27px;
\n    }
\n
\n    .course-subject-accordion h4 {
\n        font-size: 28px;
\n        line-height: 1.285714;
\n        padding-top: 8px
\n    }
\n
\n    .course-subject-accordion.course-subject-accordion p {
\n        font-size: 1rem;
\n        line-height: 1.5
\n    }
\n
\n    .course-subject-accordion .read_more {
\n        float: right;
\n        font-size: 18px;
\n        font-weight: normal;
\n        margin: 4px 0 10px;
\n    }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .get_in_touch p {
\n        font-size: 20px;
\n        line-height: 1.5;
\n    }
\n}
\n
\n
\n@media screen and (max-width: 767px) {
\n    .simplebox-contact .button {
\n        display: block;
\n        margin-bottom: 1rem;
\n    }
\n
\n    .simplebox-trainers {
\n        padding-bottom: 30px;
\n    }
\n
\n    .simplebox.simplebox.simplebox.simplebox-trainers img {
\n        width: 125vw !important;
\n        max-width: none;
\n        left: 12.5vw;
\n        position: relative;
\n    }
\n
\n    .simplebox.simplebox.simplebox-trainers .simplebox-column-1 {
\n        margin-top: -50px;
\n    }
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .simplebox-contact .simplebox-column-1 {margin:0;width:55%;}
\n    .simplebox-contact .simplebox-column-2 {margin:0;width:45%;}
\n
\n    .simplebox-contact .simplebox-column-2 .simplebox-content {
\n        padding-bottom: 135px;
\n   }
\n
\n   .simplebox-contact .button {
\n        margin-right: 1.5rem;
\n   }
\n   .simplebox-video p {
\n        margin-top: 1em;
\n    }
\n   .simplebox-primary {
\n       padding-bottom: 35px!important;
\n    }
\n   .simplebox-primary .accredited-partner-text p {
\n       margin: 0 0 1.2rem;
\n    }
\n   .simplebox-trainers {
\n        margin-top: 80px;
\n        margin-bottom: 69px;
\n        font-weight: 400;
\n    }
\n    .simplebox-trainers .simplebox-content {
\n        margin-top: -11px;
\n        padding-bottom: 45px!important;
\n    }
\n    .simplebox-trainers h1 {
\n        color: var(\-\-dark_purple);
\n    }
\n}
\n
\n.client-logos {
\n    display: flex;
\n    flex-wrap: wrap;
\n    margin: 0 -10px;
\n}
\n
\n.client-logos img {
\n    max-width: 25%;
\n    margin: 15px 10px !important; /* todo: remove inline styles */
\n    max-width: calc(25% - 20px);
\n    /* `height: auto;` does not work as expected in Safari. This `height` will force the 149:160 ratio to be maintained. */
\n    height: 107.382550336% !important;
\n}
\n
\n@media screen and (min-width: 768px) {
\n    .client-logos {
\n        margin: 0 -20px;
\n    }
\n
\n    .client-logos img {
\n        margin: 30px 20px !important; /* todo: remove inline styles */
\n        width: calc(16% - 40px) !important;
\n    }
\n}
\n
\n'
  WHERE
  `stub` = '51'
;;