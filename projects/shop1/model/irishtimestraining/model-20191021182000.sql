/*
ts:2019-10-21 18:20:00
*/
DELIMITER ;;
INSERT INTO `plugin_news` (`category_id`, `title`, `image`, `event_date`, `order`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES
((SELECT `id` FROM `plugin_news_categories` WHERE `category` = 'Events' LIMIT 1), 'Ulster University alumni and friends \'Women in Leadership\' event', 'blog2.png', '2019-09-03 18:00:00', '0', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '1', '1', '1', '0');;

INSERT INTO `plugin_news` (`category_id`, `title`, `image`, `event_date`, `order`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES
((SELECT `id` FROM `plugin_news_categories` WHERE `category` = 'Events' LIMIT 1), 'Example event', 'blog2.png', '2019-09-01 13:00:00', '0', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '1', '1', '1', '0');;


UPDATE
  `plugin_news`
SET
  `content` = '<div>{addthis_toolbox-}</div>

<p><strong><img alt="" src="/shared_media/irishtimestraining/media/photos/content/calendar.svg" style="margin-bottom: -5px; height:24px; width:24px">&nbsp;Tues 3 Sept 2019 <img alt="" src="/shared_media/irishtimestraining/media/photos/content/stopwatch.svg" style="margin-bottom: -5px; height:24px; width:22px">&nbsp;18:00 - 21:00 <img alt="" src="/shared_media/irishtimestraining/media/photos/content/address.svg" style="margin-bottom: -5px; height:24px; width:16px">&nbsp;No. 25 Fitzwilliam Place, Dublin</strong></p>

<p>In 2000, Irish Times Training formed a strategic alliance with Ulster University Business School and has been delivering Director and Senior Management programmes through our Executive Education programme ever since.</p>

<p>Over the past 18 years, we have welcomed hundreds of business leaders who have benefited from these programmes to advance their career and we are delighted to launch our upcoming Executive Education programmes in Management Practice &amp; Executive Leadership, enrolling now for autumn 2019.</p>

<p>Come and hear from a panel of Ulster University alumnae about their careers; business challenges and successes. Be inspired and network with fellow Ulster graduates and friends of the University.</p>

<p>Professor Gillian Armstrong – Director of Business Engagement, Ulster University Gillian Armstrong is a Professor of Business Education and is Ulster University Business School’s first Director of Business Engagement. She has been with Ulster University and Higher Education for over 20 years and has been actively involved in the development and management of academic excellence within the Business School. Gillian joined Ulster University in 1997 and was awarded a Senior Lectureship in 2007 and a Chair in Business Education in 2013. Prior to her current role, she was Head of the Department of Accounting, Finance and Economics from 2010 – 2018 at Ulster.</p>

<p>Host: Professor Paul Moore, Ulster University</p>

<div class="p-4" style="background: #f6f6f6;">
    <h4 class="border-title text-black">Register</h4>

    <div class="formrt formrt-vertical">{form-Contact Us}</div>
</div>
  '
WHERE
  `title` = 'Ulster University alumni and friends \'Women in Leadership\' event'
;;
