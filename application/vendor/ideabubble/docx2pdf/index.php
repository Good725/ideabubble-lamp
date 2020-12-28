<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Michael
 * Date: 27/05/15
 * Time: 10:00
 * To change this template use File | Settings | File Templates.
 */

if (isset($_POST['ApiKey']) AND $_POST['ApiKey'] == '562713330' AND isset($_FILES['file']) AND $_FILES['file']['error'] == UPLOAD_ERR_OK)
{
    $time = vsprintf('%d%06d', gettimeofday());

    $docx = $time.'.docx';
    $pdf  = $time.'.pdf';

    move_uploaded_file($_FILES['file']['tmp_name'], $docx);
    exec('docx2pdf.bat '.getcwd().DIRECTORY_SEPARATOR.$docx);

    if (is_file($pdf))
    {
        header('Content-disposition: attachment; filename="'.$pdf.'"');
        header('Content-type: application/pdf');
        header('Result: True');

        @readfile($pdf);
        @unlink  ($pdf);
    }

    @unlink($docx);
}