<?php
if (file_exists(PROJECTPATH.'../kilmartin/views/templates/kes1/course_detail.php')) {
    include PROJECTPATH.'../kilmartin/views/templates/kes1/course_detail.php';
} else {
    include PROJECTPATH.'../../kilmartin/active/views/templates/kes1/course_detail.php';
}