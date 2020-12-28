/*
ts:2019-12-11 16:30:00
*/
DELIMITER ;;
UPDATE
  `engine_settings`
SET
  `value_dev` = '<style>
\na:link {color: $email_link_color;} 
\n</style> 
\n<table bgcolor="$theme_color" border="0" cellpadding="0" cellspacing="0" width="100%" style="background: $theme_color; overflow; hidden;" width="100%"> 
\n    <tbody> 
\n        <tr align="center" style="background: #f5f5f5;"> 
\n            <td>&nbsp;</td> 
\n            <td style="padding: 16px"><img alt="$company_name" src="$logo_src" height="88" /></td> 
\n            <td>&nbsp;</td> 
\n        </tr> 
\n        <tr style="background: #f5f5f5;"> 
\n            <td height="50">&nbsp;</td> 
\n            <td rowspan="2" width="512"> 
\n                <div style="background: #ffffff; border-radius: 6px 6px 0 0; min-height: 50px; padding: $content_padding $content_padding 10px;">$message_body</div> 
\n            </td> 
\n            <td height="50">&nbsp;</td> 
\n        </tr> 
\n        <tr> 
\n            <td>&nbsp;</td> 
\n            <td>&nbsp;</td> 
\n        </tr> 
\n        <tr> 
\n            <td></td> 
\n            <td><div style="background: #fff;padding-top: 6px;border-radius: 0 0 6px 6px;"></div></td> 
\n            <td></td> 
\n       </tr> 
\n        <tr> 
\n            <td>&nbsp;</td> 
\n            <td>&nbsp;</td> 
\n            <td>&nbsp;</td> 
\n        </tr> 
\n        <tr> 
\n            <td>&nbsp;</td> 
\n            <td>
\n                <div style="background: #ffffff; border-radius: 6px; padding: 16px; text-align: center;">$need_help_text</div> 
\n            </td> 
\n            <td>&nbsp;</td> 
\n        </tr> 
\n        <tr> 
\n            <td>&nbsp;</td> 
\n            <td>&nbsp;</td> 
\n            <td>&nbsp;</td> 
\n        </tr> 
\n        <tr> 
\n            <td>&nbsp;</td> 
\n            <td> 
\n                <div>&nbsp;</div> 
\n 
\n                <div style="text-align: center;" style="padding: 16px;">$footer_text</div> 
\n            </td> 
\n            <td>&nbsp;</td> 
\n        </tr> 
\n    </tbody> 
\n</table>'
WHERE
  `variable` = 'email_wrapper_html'
;;

-- Same value for each environment
UPDATE
  `engine_settings`
SET
  `value_test`  = `value_dev`,
  `value_stage` = `value_dev`,
  `value_live`  = `value_dev`
WHERE
  `variable` = 'email_wrapper_html' /* SLS 1 (change this number, to force this line to rerun) */
;;