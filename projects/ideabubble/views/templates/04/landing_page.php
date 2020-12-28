<?php
if (file_exists(PROJECTPATH.'../kilmartin/views/templates/kes1/landing_page.php')) {
    include PROJECTPATH.'../kilmartin/views/templates/kes1/landing_page.php';
} else {
    include PROJECTPATH.'../../kilmartin/active/views/templates/kes1/landing_page.php';
}