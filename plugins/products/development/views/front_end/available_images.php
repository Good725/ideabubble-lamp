<div id="available_images_wrapper">
    <?php if (count($products) > 0): ?>
        <?php foreach ($products as $product): ?>
            <?php
            $dimensions = explode('x', $product['dimensions']);
            $width      = (isset($dimensions[0])) ? $dimensions[0] : '';
            $height     = (isset($dimensions[1])) ? $dimensions[1] : '';
            $fileinfo   = pathinfo($product['filename']);
            ?>
            <figure title="<?= $product['filename'] ?>">
                <?php if ((isset($fileinfo['extension']) and $fileinfo['extension'] == 'svg')): ?>
                    <img src="<?= str_replace(' ', '%20', (str_replace('/_thumbs/', '/', $filepath)).$product['filename']) ?>" />
                <?php else: ?>
                    <img data-width="<?= $width ?>" data-height="<?= $height ?>" src="<?= str_replace(' ', '%20', $filepath.$product['filename']) ?>" />
                <?php endif; ?>
            </figure>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No images found</p>
    <?php endif; ?>
</div>