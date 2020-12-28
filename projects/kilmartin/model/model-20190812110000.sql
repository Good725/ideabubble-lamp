/*
ts:2019-08-12 11:00:00
*/

DELIMITER ;;
UPDATE `plugin_messaging_notification_templates`
SET `message` = '<p>Hello $name $email,</p>
\n
\n<p>All of us here at Kilmartin Educational Services are delighted to welcome you to our brand new online platform. Once you complete your account with us you will be able to be to view and book our wide range of grinds, supervised study and courses at the click of a button.</p>
\n
\n<p style="text-align: center;">
\n    <a href="$url_join" style="background: #44c6ee;
\n    border-radius: 3px;
\n    color: #fff;
\n    display: inline-block;
\n    min-width: 4em;
\n    padding: .5em 1em;
\n    text-align: center;
\n    text-decoration: none;">Join</a>
\n&nbsp;
\n    <a href="$url_reject" style="background: #0e2a6b;
\n    border-radius: 3px;
\n    color: #fff;
\n    display: inline-block;
\n    min-width: 4em;
\n    padding: .5em 1em;
\n    text-align: center;
\n    text-decoration: none;">Reject</a>
\n</p>
\n
\n<p>Additionally, you will be able to update attendance, manage your bookings and accounts through your very own account.</p>
\n
\n<p>Keep an eye out on our website and mobile app to be up to date with all our special offers and new courses.</p>
\n
\n<p>We are looking forward to helping you in any way we can. If you require any assistance please call 061-444989</p>'
WHERE `name` = 'contact-invite-family-member';;