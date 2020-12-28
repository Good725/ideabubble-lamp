<?php if (Settings::instance()->get('responsive_enabled') != "TRUE"): ?>
    <div id="nav">
        <?php menuhelper::add_menu_editable_heading('Main'); ?>
    </div>
<?php else: ?>
    <div id="nav" class="navbar-default" role="navigation">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div>
        <div class="collapse navbar-collapse navbar-ex1-collapse">
            <?php menuhelper::add_menu_editable_heading('Main', 'nav&#32;navbar-nav'); ?>
        </div>
    </div>
<?php endif; ?>