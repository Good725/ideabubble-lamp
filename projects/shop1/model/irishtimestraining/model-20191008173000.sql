/*
ts:2019-10-08 17:30:00
*/


DELIMITER ;;

-- Add the "course-list" page, if it does not already exist.
INSERT IGNORE INTO
  `plugin_pages_pages` (`name_tag`, `title`, `content`,`date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`)
SELECT
  'course-list',
  'Find a Course',
  '',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  '0',
  '0',
  '1',
  (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'course_list2' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'DEFAULT' LIMIT 1)
FROM `plugin_pages_pages`
WHERE NOT EXISTS (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` = 'course-list' AND `deleted` = 0)
LIMIT 1;;

UPDATE
  `plugin_pages_pages`
SET
  `title`         = 'Find a Course',
  `content`       = '<h1 class="mb-3">Find a Course</h1>\n\n<p class="mt-3">Small brief intro paragraph. Small brief intro paragraph. Small brief intro paragraph.</p>',
  `layout_id`     = (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'course_list2' LIMIT 1),
  `modified_by`   = (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  `last_modified` = CURRENT_TIMESTAMP
WHERE
  `name_tag` = 'course-list'
;;

UPDATE
  `plugin_pages_pages`
SET
  `title`         = 'Course details',
  `content`       = '',
  `layout_id`     = (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'course_detail2' LIMIT 1),
  `modified_by`   = (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  `last_modified` = CURRENT_TIMESTAMP
WHERE
  `name_tag` = 'course-detail'
;;

-- Set the home page content
UPDATE
  `plugin_pages_pages`
SET
  `content` = '<div class="simplebox simplebox-align-top simplebox-equal-heights simplebox-raised hidden\-\-mobile">
	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<h4><img alt="" src="/shared_media/irishtimestraining/media/photos/content/individual.png" style="height:30px; width:31px; margin-bottom: -.25em;" />&nbsp;&nbsp;For individuals</h4>

				<p>Two lines for text description. Two lines for text description.<br />Two lines for text description.</p>

				<p><a class="button" href="/individuals" style="min-width: 180px; padding: .75rem;">Find a course</a></p>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<h4><img alt="" src="/shared_media/irishtimestraining/media/photos/content/team.png" style="height:29px; width:41px; margin-bottom: -.25em;" />&nbsp;&nbsp;&nbsp;For teams</h4>

				<p>Two lines for text description. Two lines for text description.<br />Two lines for text description.</p>

				<p><a class="button" href="/teams" style="min-width: 180px; padding: .75rem;">Get started</a></p>
			</div>
		</div>
	</div>
</div>

<div class="hidden\-\-desktop hidden\-\-tablet simplebox p-0" style="margin: -1.5rem auto 0;">
    <a class="d-block bg-primary p-3" href="/course-list">
       <h5 class="text-white m-0">Find a course <span style="float: right;">&raquo;</span></h5>
    </a>
</div>

<h1>Course Topics</h1>

<p>{course_topics-}</p>

<div class="bg-light fullwidth">
	<p>{news_category-Blog-Text intro paragraph for News / Blog. Text intro paragraph for News / Blog. Text intro paragraph for News / Blog.}</p>

	<h1 class="border-title-both">Some of our clients</h1>

	<p>&nbsp;</p>
</div>

<div class="bg-white simplebox get_in_touch">
	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<div><img class="d-block" alt="" src="/shared_media/irishtimestraining/media/photos/content/resources-image.png" style="height:514px; width:575px" /></div>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<h1>Let&#39;s talk!</h1>

				<p>Contact us to find out more about any of our courses and what we can do for you.</p>

				<p><a class="button bg-success" href="/contact-us">Contact us</a>
				   <a class="button bg-primary" href="/request-a-callback">Request a callback</a></p>
			</div>
		</div>
	</div>
</div>
'
WHERE
  `name_tag` IN ('home', 'home.html')
;;

INSERT IGNORE INTO `plugin_news_categories` (`category`, `order`, `date_modified`, `publish`, `delete`) VALUES
('Blog',   '1', CURRENT_TIMESTAMP, '1', '0'),
('Events', '3', CURRENT_TIMESTAMP, '1', '0');;

UPDATE `plugin_news_categories` SET `order` = '1' WHERE `category` = 'News';;

INSERT INTO `plugin_news` (`category_id`, `title`, `image`, `event_date`, `order`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES
((SELECT `id` FROM `plugin_news_categories` WHERE `category` = 'Blog' LIMIT 1), 'Battling burnout; five steps to lighten the load', 'blog1.png', '2019-10-05 00:00:00', '0', '2019-10-08 10:33:07', '2019-10-08 10:39:35', '2', '2', '1', '0'),
((SELECT `id` FROM `plugin_news_categories` WHERE `category` = 'Blog' LIMIT 1), 'How to future-proof your leadership skills',       'blog2.png', '2019-10-06 00:00:00', '0', '2019-10-08 10:33:51', '2019-10-08 10:33:51', '2', '2', '1', '0'),
((SELECT `id` FROM `plugin_news_categories` WHERE `category` = 'Blog' LIMIT 1), 'Building a board to face the future',              'blog3.png', '2019-10-03 00:00:00', '0', '2019-10-08 10:34:16', '2019-10-08 10:34:16', '2', '2', '1', '0');;

INSERT INTO `plugin_news` (`category_id`, `title`, `image`, `event_date`, `order`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES
((SELECT `id` FROM `plugin_news_categories` WHERE `category` = 'Blog' LIMIT 1), 'The social customer service playbook',                   'benefits.png',              '2019-09-25 00:00:00', '0', '2019-10-08 10:33:07', '2019-10-08 10:39:35', '2', '2', '1', '0'),
((SELECT `id` FROM `plugin_news_categories` WHERE `category` = 'Blog' LIMIT 1), 'Five ways to start building a culture of open...',       'walk-the-talk-2.png',       '2019-09-16 00:00:00', '0', '2019-10-08 10:33:51', '2019-10-08 10:33:51', '2', '2', '1', '0'),
((SELECT `id` FROM `plugin_news_categories` WHERE `category` = 'Blog' LIMIT 1), 'Workplace wellbeing - Get the fundamentals right first', 'body-mind-spirit-soul.png', '2019-09-13 00:00:00', '0', '2019-10-08 10:34:16', '2019-10-08 10:34:16', '2', '2', '1', '0');;

UPDATE
  `plugin_pages_pages`
SET
  `title`     = 'Resources and Events',
  `layout_id` = (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'news2' LIMIT 1),
  `content` = '<h1 class="mb-0">Resources &amp; Events</h1>\n\n<p>Choose a category. Brief intro paragraph</p>'
WHERE
  `name_tag` = 'news'
;;

UPDATE `plugin_news`
SET `content` = '<p><span style="color:#777; font-size:12px">BY DEIRDRE CURRAN - LEARNING AND DEVELOPMENT SPECIALIST AND CHARTERED ORGANISATIONAL PSYCHOLOGIST | 10 SEP 2019</span></p>

<div>{addthis_toolbox-}</div>

<p>Workplace burnout is on the increase. Recent research carried out by the Economic and Social Research Institute revealed that the number of Irish workers experiencing stress doubled between 2010 and 2015.</p>

<p>The World Health Organisation defines burnout as a &ldquo;syndrome conceptualized as resulting from chronic workplace stress that has not been successfully managed,&rdquo; and describes the symptoms of exhaustion, feelings of negativity and reduced productivity.</p>

<p>So, why are we finding it increasingly difficult to cope with the demands of working life?</p>

<p>There are many factors that could explain the rise in workplace burnout. One is the concept of &lsquo;Workism&rsquo;, a term described by Derek Thompson as the modern idea of work being at the centre of our lives and our identities. In simple terms, we value ourselves through our work and feel bad when we aren&rsquo;t &lsquo;doing&rsquo; something and doing it as efficiently as possible. The consequence is that everything gets turned into an endless to-do list.</p>

<p>Another factor is the merging of work and leisure time.</p>

<p>Before mobile connectivity became ubiquitous, the moment you left the office, you were no longer working. Now, we can potentially always be working. The Spillover Model in psychology can be used to describe how the way in which work can bleed into all aspects of our lives, whether that&rsquo;s through bringing work home or endlessly ruminating about work issues while we are supposed to be relaxing. Either way, the result is a sense of overwhelm, a feeling that we are always behind and always working. In this way we are left feeling burnt out.</p>

<h3>How can we avoid burnout?</h3>

<p>We won&rsquo;t change our pervasive work culture overnight, or indeed the technological advancements that have made it so difficult to draw clear boundaries between work and leisure. That said, individuals differ in their levels of resilience and how they respond to challenges. We can make small changes to our day to day structure which can have big impacts on our resilience levels.</p>

<p>Building resilience is a hot topic and there are countless books and resources promoting everything from mindful colouring to digital detox. We know that burnout and resilience are layered and complex subjects and that there is no magic wand that will make working life less stressful. But, in the short-term, there are some straightforward (and possibly surprising) steps you can take that require little or no extra time.</p>

<p>These five measures are each widely supported by solid research showing that, when practiced consistently, they can increase positive emotion, reduce stress and build resilience.</p>

<p><img alt="" src="/shared_media/irishtimestraining/media/photos/content/burnout2.png" style="height:366px; width:700px" /></p>

<h4>Acts of kindness</h4>

<p><a href="#">Seligman</a>, the father of the positive psychology movement, found that doing something for another person, no matter how small, is the single most powerful predictor of our own feelings of wellbeing. There are several studies showing our positive emotions increase and stress decreases to a greater extent when we do something for others rather than for ourselves. Some studies even show significantly higher levels of wellbeing when individuals spent money on someone else rather than on themselves! Try to do a small kindness each day. This can be something as simple as bringing a coffee to a colleague.</p>

<h3>The social buffer</h3>

<p>You might already be aware of the power of relationships with family and friends as a buffer against stress and there are numerous studies supporting this. Interestingly, recent research has found a stress-busting gem we may have overlooked. In a paper entitled &ldquo;Social interactions and wellbeing: the surprising power of weak ties&rdquo;, researchers found that interactions with those we consider strangers or acquaintances (such as the person who served you coffee recently) can significantly bolster our feelings of wellbeing and reduce stress. Even the introverts among us are happier after having a positive interaction with a stranger. The key takeaway here is to turn our transactions into interactions. This can be as basic as asking someone how their day is going.</p>

<h3>Three good things</h3>

<p>If the idea of gratitude journaling strikes you as a bit fluffy &ndash; you might be interested to know that simply writing down three good things each day can make a measurable difference to your resilience levels in as little as one week. Barbara Fredrickson, a psychologist who specialises in the topic of resilience, has written in her book &lsquo;LOVE 2.0&rsquo;, about the technique of &lsquo;un-adapting&rsquo;, where we try to appreciate things we take for granted such as family, having an income and having a roof over our head. Our &lsquo;three good things&rsquo; each day could be as basic as being grateful for meeting a friend for lunch, or not getting caught in the rain. It&rsquo;s quick and simple to do but the benefits are shown to be long-ranging and can combat the negative effects of stress. Try it for a week.</p>

<h3>Challenging the inner critic</h3>

<p>Most of us will be familiar with that chattering negative voice that pipes up when we least need to hear it. The inner-critic deals in negative hyperbole rather than any balanced dialogue and will, over time, erode our resilience levels. Research shows that resilient individuals don&rsquo;t think in absolute terms, such as a situation being all good or all bad, but instead think in iterations; of something being a mix of positive and negative. If something goes badly, they will acknowledge the aspect that could have been better but will also identify that it was a &lsquo;partial success&rsquo;. They will see the good and bad aspects, side by side and then move on, rather than focusing on the negative and catastrophising. This thought re-framing is a continuous process. Like any muscle, the brain will adapt over time. The next time you catch your inner critic berating you for something, try to see and articulate the partial successes. Over time, your resilience levels will benefit.</p>

<h3>Job crafting</h3>

<p>Anne Helen Petersen has written about the topic of burnout and has spoken about how this links to the expectation that we should have &lsquo;meaningful&rsquo; careers. This high expectation that our demanding jobs should also be fulfilling and full of meaning can create disillusionment and lead to burnout. Much of the research on this topic indicates that it&rsquo;s not so much about finding a &lsquo;meaningful&rsquo; job but about making meaning in the job we have. If we make meaning in what we are doing, we will be happier and more resilient. You may derive meaning from relationships with colleagues, from the work itself, from the purpose of the organisation &ndash; it doesn&rsquo;t really matter where you find it &ndash; the important thing is that you derive meaning from some aspect of your work.</p>

<p>Amy Wrzesniewski, A Yale professor, discovered an interesting phenomenon she came to describe as &lsquo;job crafting&rsquo;. She studied janitorial staff in hospitals and found that while many of them disliked what they saw as a menial job and said they only did it for the benefits, there was another group, with the exact same job description, who loved their jobs and described it in entirely different terms. They described how they created sterile spaces to promote healing and talked about the importance of the relationships they developed with patients. The jobs were the same on paper, but the second group were doing entirely different work. Job crafting is the idea of shaping your job or aspects of it into something you can derive meaning and satisfaction from. Two common symptoms of burnout are the loss of enjoyment and pessimism. Ask yourself which elements of your job you enjoy and how you might do more of these.</p>

<h2><img alt="" src="/shared_media/irishtimestraining/media/photos/content/Relax.png" style="height:366px; width:700px" /></h2>

<h4>&ldquo;The sweetness of doing nothing&rdquo;</h4>

<p>When we aren&rsquo;t actually working, we would do well to take heed of the beautiful Italian phrase &ldquo;Dolce far Niente&rdquo;, which means &ldquo;the sweetness of doing nothing.&rdquo; What we do when we leave our work is as important. The Spillover Model mentioned earlier, where our work can pervade all aspects of our lives, is a sure-fire recipe for burnout. A psychologist colleague of mine used an analogy with a client who was experiencing high stress. He likened it to his client carrying a heavy box around all day. He said that everyone else is carrying a heavy box too, the difference is, he told him, they put it down when they leave work, you carry it all the time.</p>

<p>Burnout doesn&rsquo;t happen overnight. It is a slow and corrosive process that can leave us feeling exhausted, negative and anxious. Bouncing back from burnout won&rsquo;t happen overnight either, but by making small changes each day, we can start to replenish our resilience levels, weather those tough days more easily and begin to find more enjoyment in our working lives.</p>

<div class="fullwidth simplebox" style="background: #f6f6f6;">
	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1" style="width: 27%;">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<div><img class="d-block" alt="" src="/shared_media/irishtimestraining/media/photos/content/deirdre.png" style="height:250px; width:250px" /></div>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2" style="width: 63%;">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<h4 class="text-black">About the author</h4>

				<div style="max-width: 650px;">
					<p>Deirdre is a Chartered Organisational Psychologist with 14 years&#39; experience in learning and development, communications and employee wellness. Deirdre holds a BA in Applied Psychology, an MSc in Organisational Psychology and more recently, a Diploma in Business and Executive Coaching.</p>

					<p>Deirdre has worked across industry in both the private and public sectors in the design and delivery of learning programmes, organisational development and selection and assessment</p>
				</div>
			</div>
		</div>
	</div>
</div>
'
WHERE `title` = 'Battling burnout; five steps to lighten the load'
;;