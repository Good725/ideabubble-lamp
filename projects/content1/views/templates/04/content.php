<?php
if (file_exists(PROJECTPATH.'../kilmartin/views/templates/kes1/content.php')) {
    include PROJECTPATH.'../kilmartin/views/templates/kes1/content.php';
} else {
    include PROJECTPATH.'../../kilmartin/active/views/templates/kes1/content.php';
}