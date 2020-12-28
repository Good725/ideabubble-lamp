<?php if (strpos($message, '<html>') === false): ?>
    <!DOCTYPE html>
    <html>
        <head>
            <style>
                body {
                    /* Border top and bottom is to prevent margin collapsing. */
                    border: solid transparent;
                    border-width: 1px 0;
                    font-family: sans-serif;
                    margin: 0;
                }
            </style>
        </head>

        <body><?= $message ?></body>
    </html>
<?php else: ?>
    <?= $message ?>
<?php endif; ?>