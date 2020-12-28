<?php
if (isset($kohana_view_data) && !empty($kohana_view_data['is_backend'])) {
    include APPPATH.'views/footer.php';
} else {
    if (file_exists(PROJECTPATH.'../kilmartin/views/templates/kes1/views/footer.php')) {
        include PROJECTPATH.'../kilmartin/views/templates/kes1/views/footer.php';
    } else {
        include PROJECTPATH.'../../kilmartin/active/views/templates/kes1/views/footer.php';
    }
}