<div id="header" class="header">
    <a href="/" class="logo">
        <img src="<?= $page_data['logo'] ?>"" alt="<?= Settings::instance()->get('company_title') ?>" />
    </a>

    <div class="header_callback">
        <a href="/request-a-callback.html" class="primary_button callback_button">Request Callback</a>
        <p>Call Us</p>
        <dl>
            <dt>Nenagh</dt>
            <dd>067 43000</dd>

            <dt>Thurles</dt>
            <dd>0504 21400</dd>

            <dt>Roscrea</dt>
            <dd>0505 22335</dd>
        </dl>
    </div>

    <div id="main_menu" class="main_menu">
        <?php menuhelper::add_menu_editable_heading('main')?>
    </div>

</div>