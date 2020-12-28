                <?php $partners = Menuhelper::get_all_published_menus('partners'); ?>
                <?php if ( ! empty($partners)): ?>
                    <section class="client-block">
                        <div class="row">
                            <div class="small-12">
                                <h2 class="text-center"><?= __('Our Partners') ?></h2>

                                <ul class="partners">
                                    <?php foreach ($partners as $partner): ?>
                                        <li>
                                            <?php $link = menuhelper::get_link($partner); ?>

                                            <?php if ($link): ?><a href="<?= $link ?>"><?php endif; ?>
                                                <img width="150" height="150"
                                                     src="/shared_media/<?= $project_media_folder ?>/media/photos/menus/<?= isset($partner['filename']) ? $partner['filename'] : '' ?>"
                                                     alt="<?= $partner['title'] ?>"
                                                     class="attachment-thumbnail size-thumbnail"
                                                    />
                                            <?php if ($link): ?></a><?php endif; ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </section>
                <?php endif; ?>
            </main>

            <footer class="block-footer" role="contentinfo">
                <section class="footer-menu">
                    <div class="row footer-columns">
                        <div class="columns medium-3 footer-column">
                            <h5><?= __('Contact us') ?></h5>

                            <ul class="no-bullet">
                                <?php if (trim($settings_instance->get('company_title'))): ?>
                                    <li><?= $settings_instance->get('company_title') ?></li>
                                <?php endif; ?>

                                <?php if (trim($settings_instance->get('telephone'))): ?>
                                    <li><span class="contact-label">Tel</span> <?= $settings_instance->get('telephone')?></li>
                                <?php endif; ?>

                                <?php if (trim($settings_instance->get('fax'))): ?>
                                    <li><span class="contact-label">Fax</span> <?= $settings_instance->get('fax')?></li>
                                <?php endif; ?>

                                <?php if (trim($settings_instance->get('email'))): ?>
                                    <li><span class="contact-label">Email</span> <?= $settings_instance->get('email')?></li>
                                <?php endif; ?>
                            </ul>
                        </div>

                        <?php $footer_links = Menuhelper::get_all_published_menus('footer'); ?>

                        <?php foreach ($footer_links as $level1): ?>
                            <?php if ($level1['level'] == 1): ?>
                                <div class="footer-column columns medium-3">
                                    <?php $link = menuhelper::get_link($level1); ?>
                                    <h5><?= $link ? '<a href="'.$link.'">'.$level1['title'].'</a>' : $level1['title'] ?></h5>

                                    <?php if ($level1['has_sub']): ?>
                                        <ul>
                                            <?php foreach ($footer_links as $level2): ?>
                                                <?php if ($level2['level'] == 2 && $level2['parent_id'] == $level1['id']): ?>
                                                    <li class="menu-item">
                                                        <a href="<?= menuhelper::get_link($level2) ?>"><?= $level2['title'] ?></a>
                                                    </li>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>

                    <div class="row footer-follow">
                        <div class="small-12 large-3 columns">
                            <?php
                            $social_media['facebook_url']  = trim($settings_instance->get('facebook_url'));
                            $social_media['twitter_url']   = trim($settings_instance->get('twitter_url'));
                            $social_media['linkedin_url']  = trim($settings_instance->get('linkedin_url'));
                            $social_media['instagram_url'] = trim($settings_instance->get('instagram_url'));
                            $social_media['snapchat_url']  = trim($settings_instance->get('snapchat_url'));

                            if ($social_media['facebook_url'] != '' && strpos($social_media['facebook_url'], 'facebook.com/') == false) {
                                $social_media['facebook_url'] = 'https://www.facebook.com/'.$social_media['facebook_url'];
                            }
                            if ($social_media['twitter_url'] != '' && strpos($social_media['twitter_url'], 'twitter.com/') == false) {
                                $social_media['twitter_url'] = 'https://twitter.com/'.$social_media['twitter_url'];
                            }
                            if ($social_media['linkedin_url'] != '' && strpos($social_media['linkedin_url'], 'linkedin.com/') == false) {
                                $social_media['linkedin_url'] = 'https://linkedin.com/in/'.$social_media['linkedin_url'];
                            }
                            if ($social_media['instagram_url'] != '' && strpos($social_media['instagram_url'], 'instagram.com/') == false) {
                                $social_media['instagram_url'] = 'http://instagram.com/'.$social_media['instagram_url'];
                            }
                            if ($social_media['snapchat_url'] != '' && strpos($social_media['snapchat_url'], 'snapchat.com/') == false) {
                                $social_media['snapchat_url'] = 'http://snapchat.com/add/'.$social_media['snapchat_url'];
                            }
                            ?>

                            <?php if (count($social_media)): ?>
                                <div class="footer-social">
                                    <h5>Follow Us</h5>

                                    <ul class="inline-list" style="display: inline-block;">
                                        <?php
                                        $name = $icon = 'facebook';
                                        include 'footer_social_item.php';
                                        $name = $icon = 'twitter';
                                        include 'footer_social_item.php';
                                        $name = $icon = 'linkedin';
                                        include 'footer_social_item.php';
                                        $name = $icon = 'instagram';
                                        include 'footer_social_item.php';
                                        $name = 'snapchat';
                                        $icon = 'snapchat-ghost';
                                        include 'footer_social_item.php';
                                        ?>
                                    </ul>
                                </div>

                            <?php endif; ?>

                        </div>

                        <div class="medium-12 large-6 columns small-only-text-center footer-slogan"><?= htmlentities($settings_instance->get('company_slogan')) ?></div>

                        <?php if ($settings_instance->get('site_footer_logo')): ?>
                            <div class="medium-12 large-3 columns small-only-text-center footer-logo"><img src="<?= $media_path ?>logos/<?= $settings_instance->get('site_footer_logo') ?>" /></div>
                        <?php endif; ?>
                    </div>

                    <div class="row footer-copyright">
                        <div class="columns medium-6 small-text-center medium-text-left footer-copyright-company"><?= $settings_instance->get('company_copyright') ?></div>
                        <div class="columns medium-6 small-text-center medium-text-right footer-copyright-cms"><?= (trim($settings_instance->get('cms_copyright')) == '') ? 'Powered by <a href="https://ideabubble.ie">Idea Bubble</a>' : $settings_instance->get('cms_copyright') ?></div>
                    </div>
                </section>
            </footer>
        </div><?php // .wrapper ?>

        <script type="text/javascript" src="/assets/<?= $assets_folder_path ?>/js/jquery.validationEngine2.js"></script>
        <script type="text/javascript" src="/assets/<?= $assets_folder_path ?>/js/jquery.validationEngine2-en.js"></script>
        <script type="text/javascript" src="/assets/<?= $assets_folder_path ?>/js/general.js<?= file_exists($assets_folder_code_path.'/js/general.js') ? '?ts='.filemtime($assets_folder_code_path.'/js/general.js') : '' ?>"></script>
    </body>
</html>