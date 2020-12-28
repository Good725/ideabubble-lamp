<?php
if (file_exists(PROJECTPATH.'../kilmartin/views/templates/kes1/packages_available.php')) {
    include PROJECTPATH.'../kilmartin/views/templates/kes1/packages_available.php';
} else {
    include PROJECTPATH.'../../kilmartin/active/views/templates/kes1/packages_available.php';
}