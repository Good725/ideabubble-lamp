<?php if (sizeof($packages) > 0):?>
    <h2>Packages Available</h2>

    <?php foreach ($packages as $package): ?>
        <div class="package-wrap">
            <div class="package-wrap-inner">
            <div class="table-box">
                <div class="imgbox">
                    <?php $filename = ( ! empty($package['filename'])) ? $package['filename'] : 'course-placeholder.png'; ?>

                    <img src="<?= Model_Media::get_image_path($filename, 'courses') ?>" alt="" />

                </div>

                <div class="available-text">
                    <h4><?= $package['title'] ?></h4>

                    <p><?= $package['summary'] ?></p>

                    <a href="javascript:void(0)" data-id="<?= $package['id'] ?>" class="show-more button-toggle">
                        <span class="show-txt">Show courses <span class="fa fa-angle-down" aria-hidden="true"></span></span>
                        <span class="hide-txt">Hide courses <span class="fa fa-angle-up" aria-hidden="true"></span></span>
                    </a>
                </div>
            </div>
            </div>
            <div class="select-package" id="<?= $package['id'] ?>"></div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>