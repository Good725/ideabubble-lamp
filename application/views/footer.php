<hr/>
<footer class="cms-footer">
    <div class="container">
        <div class="row">
            <div class="col-sm-3 footer_copyright">
                <?php if (Kohana::$config->load('config')->get('footer_c_text')): ?>
                    <p>&copy; <?= Kohana::$config->load('config')->get('footer_c_text') ?> <?= date('Y', time()); ?></p>
                <?php else : ?>
                    <p>&copy; Idea Bubble 2008 to <?= date('Y', time()); ?></p>
                <?php endif; ?>
            </div>
            <div id="version" class="col-sm-<?= (Kohana::$environment !== Kohana::STAGING AND Kohana::$environment !== Kohana::PRODUCTION) ? '6' : '9'; ?>">
                <p>
				<?= Settings::instance()->get('cms_copyright') ?>
				</p>
            </div>
            <?php if (Kohana::$environment !== Kohana::STAGING AND Kohana::$environment !== Kohana::PRODUCTION): ?>
                <div class="col-sm-3">
                    <p class="right"><a onclick="$('#profiler').toggle();"><?= __('Show Profiler') ?></a></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</footer>
