<div class="Bg-wrapper">
        <div class="middle-align">
            <div class="row">
                <div class="small-container small-width">
                    <div class="theme-form">
                        <div class="inn-logo">
                            <a href="#"><img src="<?php echo URL::get_engine_theme_assets_base().'img/inn-logo.png';?>"></a>
                        </div>
                        <?= (isset($alert)) ? $alert : '' ?>
                        <form method="post">

                            <input type="hidden" name="email" value="<?=html::chars($email)?>" />
                            <input type="hidden" name="mobile" value="<?=html::chars($mobile)?>" />
                            <div class="usefull-links">Set Password</div>
                            <div class="form-wrap">
                                <label class="pwd icon-wrap">    
                                    <input type="password" name="password" placeholder="New Password"  required="required">
                                </label>
                            </div>   
                            <div class="form-wrap">
                                <label class="pwd icon-wrap">    
                                    <input type="password" name="mpassword" placeholder="Confirm new Password"  required="required">
                                </label>
                            </div>
                            <div class="form-wrap align-center">
                                <input type="submit" class="blueBtn" value="Confirm">
                            </div>                  
                        </form>

                    </div>                  
                    <div class="bottom-section">
                        <?php if (!empty($footer_links)) { ?>
                            <ul class="usefull-links">
                                <?php foreach ($footer_links as $link): ?>
                                    <li>
                                        <a href="/<?= $link['name_tag'] ?>"><?= $link['title'] ?></a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php } else { ?>
                            <div class="poweredby"><p><?= Settings::instance()->get('cms_copyright') ?></p></div>
                        <?php } ?>
                    </div>

                </div>
            </div>
        </div>
    </div>
