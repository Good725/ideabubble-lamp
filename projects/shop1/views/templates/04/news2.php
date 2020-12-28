<?php
// todo: Merge these project folders, so we don't need these includes.
if (file_exists(PROJECTPATH.'../kilmartin/views/templates/kes1/news2.php')) {
    include PROJECTPATH.'../kilmartin/views/templates/kes1/news2.php';
} else {
    include PROJECTPATH.'../../kilmartin/active/views/templates/kes1/news2.php';
}