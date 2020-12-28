<?php
if (file_exists(PROJECTPATH . '../kilmartin/views/templates/kes1/content_wide.php')) {
    include PROJECTPATH . '../kilmartin/views/templates/kes1/home_page_content_above.php';
} else {
    include PROJECTPATH . '../../kilmartin/active/views/templates/kes1/home_page_content_above.php';
}