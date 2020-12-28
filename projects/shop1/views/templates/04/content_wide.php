<?php
if (file_exists(PROJECTPATH.'../kilmartin/views/templates/kes1/content_wide.php')) {
    include PROJECTPATH.'../kilmartin/views/templates/kes1/content_wide.php';
} else {
    include PROJECTPATH.'../../kilmartin/active/views/templates/kes1/content_wide.php';
}