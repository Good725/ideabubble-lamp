<?php
$settings = Settings::instance()->get();
$social_media['linkedin_url']  = isset($settings['linkedin_url']) ? trim($settings['linkedin_url']) : '';
$social_media['facebook_url']  = trim($settings['facebook_url']);
$social_media['twitter_url']   = trim($settings['twitter_url']);
$social_media['instagram_url'] = trim($settings['instagram_url']);
$social_media['snapchat_url']  = trim($settings['snapchat_url']);

if ($social_media['linkedin_url'] != '' AND strpos($social_media['linkedin_url'], 'linkedin.com/') == FALSE)
{
    $social_media['linkedin_url'] = 'https://linkedin.com/in/'.$social_media['linkedin_url'];
}
if ($social_media['facebook_url'] != '' AND strpos($social_media['facebook_url'], 'facebook.com/') == FALSE)
{
    $social_media['facebook_url'] = 'https://www.facebook.com/'.$social_media['facebook_url'];
}
if ($social_media['twitter_url'] != '' AND strpos($social_media['twitter_url'], 'twitter.com/') == FALSE)
{
    $social_media['twitter_url'] = 'https://twitter.com/'.$social_media['twitter_url'];
}
if ($social_media['snapchat_url'] != '' AND strpos($social_media['snapchat_url'], 'snapchat.com/') == FALSE)
{
    $social_media['snapchat_url'] = 'http://snapchat.com/add/'.$social_media['snapchat_url'];
}
if ($social_media['instagram_url'] != '' AND strpos($social_media['instagram_url'], 'instagram.com/') == FALSE)
{
    $social_media['instagram_url'] = 'http://instagram.com/'.$social_media['instagram_url'];
}
?>
	<footer class="footer">
        <div class="full-row bottom">
            <div class="fix-container">
                <div class="fl">
                    <?php if ( ! empty($social_media)): ?>
                        <div class="social-icon">
                            <ul>
                                <?php if ( ! empty($social_media['linkedin_url'])): ?>
                                    <li>
                                        <a target="_blank" href="<?= $social_media['linkedin_url'] ?>" title="<?= __('LinkedIn') ?>">
                                            <span aria-hidden="true" class="social_linkedin"></span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if (@$social_media['facebook_url']): ?>
                                    <li>
                                        <a target="_blank" href="<?= $social_media['facebook_url'] ?>" title="<?= __('Facebook') ?>">
                                            <span aria-hidden="true" class="social_facebook"></span>
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php if (@$social_media['twitter_url']): ?>
                                    <li>
                                        <a target="_blank" href="<?= $social_media['twitter_url'] ?>" title="<?= __('Twitter') ?>">
                                            <span aria-hidden="true" class="social_twitter"></span>
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php if (@$social_media['snapchat_url']): ?>
                                    <li>
                                        <a target="_blank" href="<?= $social_media['snapchat_url'] ?>" title="<?= __('Snapchat') ?>">
                                            <span aria-hidden="true" class="social_snapchat"></span>
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php if (@$social_media['instagram_url']): ?>
                                    <li>
                                        <a target="_blank" href="<?= $social_media['instagram_url'] ?>" title="<?= __('Instagram') ?>">
                                            <span aria-hidden="true" class="social_instagram"></span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <ul class="f-nav">
                        <?= menuhelper::add_menu_editable_heading('footer') ?>
                    </ul>
                </div>

                <div class="fr">
                    <div class="copyright">
                        <span><?= $settings['company_copyright'] ?></span>
                        &nbsp;
                        <span><?= $settings['cms_copyright'] ?></span>
                    </div>
                </div>
            </div>
        </div>

	</footer>

	</main><!-- main end -->

    <script type="text/javascript" src="/assets/ideabubble/js/jquery.validationEngine2.js"></script>
    <script type="text/javascript" src="/assets/ideabubble/js/jquery.validationEngine2-en.js"></script>

</body>
</html>
