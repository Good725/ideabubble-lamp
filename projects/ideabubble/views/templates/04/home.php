<?php
if (file_exists(PROJECTPATH . '../kilmartin/views/templates/kes1/Home.php')) {
    include PROJECTPATH . '../kilmartin/views/templates/kes1/Home.php';
} else {
    include PROJECTPATH . '../../kilmartin/active/views/templates/kes1/Home.php';
}