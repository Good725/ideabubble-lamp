<div id="footer">
    <div class="main_footer">
        <div class="contact_footer">
            <h1>SEND YOUR ENQUIRIES TO</h1>

            <p><?php echo @settings::instance()->get('addres_line_1') ?></p>
            <p><?php echo @settings::instance()->get('addres_line_2') ?></p>
            <p><?php echo @settings::instance()->get('email') ?></p>
            <p>PH: <?php echo @settings::instance()->get('telephone') ?></p>
        </div>
        <div class="menu_footer">
            <?php menuhelper::add_menu_editable_heading('footer') ?>
        </div>
        <div style="clear:both;"></div>
    </div>
    <div class="footer_copiright">
        <div class="footer_copiright_left">&copy; MILITARY & CAMPING</div>
        <div class="footer_copyright_right">
            <?= (Settings::instance()->get('cms_copyright') == '') ? 'Powered by <a href="https://ideabubble.ie">Idea Bubble</a> <a href="https://ideabubble.ie/website-cms-solutions.html">CMS</a>' : Settings::instance()->get('cms_copyright'); ?>
        </div>
    </div>
</div>