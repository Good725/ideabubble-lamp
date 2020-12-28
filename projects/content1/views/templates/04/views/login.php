<?php
if (file_exists(PROJECTPATH.'../kilmartin/views/login.php')) {
    include PROJECTPATH.'../kilmartin/views/login.php';
} else {
    include PROJECTPATH.'../../kilmartin/active/views/login.php';
}
