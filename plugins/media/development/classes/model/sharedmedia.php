<?php

/**
 * Model for media items that could be uploaded to server 
 *
 * @author peeter
 */
//define('SHARED_MEDIA_LIBRARY_PATH', ROOTPATH . DIRECTORY_SEPARATOR . 'www-media' . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR);
define(
	'SHARED_MEDIA_LIBRARY_RELATIVE_PATH',
	'www' . DIRECTORY_SEPARATOR . 'media'
);
define(
	'SHARED_MEDIA_LIBRARY_ABSOLUTE_PATH',
	PROJECTPATH.
			'www' . DIRECTORY_SEPARATOR .
				'media'
);

class Model_Sharedmedia extends Model {


   /**
     * Fn copies file to media directory. Validates type and size.
     * 
     * @param array $fileinfo 
     * return array ( filename,filesize, md5,imagesize ) 
     */
    public function receive_file(array $fileinfo) {

        $newfilename = $fileinfo['name'];
        $tmpfilename = $fileinfo['tmp_name'];
        $filesize = $fileinfo['size'];

        if (strlen($newfilename) > 200) {
            throw new Exception('Filename was too long.');
        }

        if ($filesize > 1024 * 1024 * 10 /* 10Mb */) {
            throw new Exception('File was too big. Maximum allowed size is 10Mb.');
        }

        // check if file is already added
        $md5 = md5_file($tmpfilename);
        $file_is_in_db = DB::query(Database::SELECT, 'SELECT id FROM plugin_media_shared_media WHERE hash = :hash')
				->param(':hash', $md5)
				->execute()
				->count();
        if ($file_is_in_db) {
            throw new Exception('File is already uploaded.');
        }

        //check if file is a picture
        $imageinfo = getimagesize($tmpfilename);
        if (!$imageinfo) {
            throw new Exception('Uploaded file type is not an image.');
        }
        /* $imageinfo content
         *   array
         *     0 => int 550 - width
         *     1 => int 373 - height
         *     2 => int 2 - image type
         *     3 => string - 'width="550" height="373"' (length=24)
         *     'bits' => int - number of bits for each color (8)
         *     'channels' => int - 3 for RGB pictures and 4 for CMYK pictures
         *     'mime' => string - 'image/jpeg' (length=10)
         */
        $imagesize = $imageinfo[0] . 'x' . $imageinfo[1];

        //move file to media library - generates path to new file similar to:  /Users/kosta/Sites/wpp/projects/alpha/www/media/media_test_image.jpg
        $target_file = SHARED_MEDIA_LIBRARY_ABSOLUTE_PATH . DIRECTORY_SEPARATOR . basename($newfilename);
//exit('move file to: '.$target_file. ' now');
        if (!move_uploaded_file($tmpfilename, $target_file)) {
            throw new Exception('Cant move file to shared-media library!');
        }
        return array('filename' => $newfilename, 'filesize' => $filesize, 'md5' => $md5, 'imagesize' => $imagesize);
    }

    /**
     * Save info about uploaded file into database 'shared-media' table
     * 
     * @param array $imageinfo  array('filename' => $newfilename, 'filesize'=>$filesize, 'md5'=> $md5, 'imagesize'=>$imagesize); 
     */
    public function add_shared_media_item(array $imageinfo) {

        //insert record to databse
        $data['filename'] = $imageinfo['filename'];
        $data['size'] = $imageinfo['filesize'];
        $data['hash'] = $imageinfo['md5'];
        $data['preset'] = $imageinfo['imagesize'];
        //todo: preset?

        DB::insert('plugin_media_shared_media', array('filename', 'size', 'hash', 'preset'))
                ->values($data)
                ->execute();
    }

    public function get_shared_media_items() {
        $mediaitems = DB::query(Database::SELECT, "SELECT * FROM plugin_media_shared_media")
                ->as_assoc()
                ->execute();
        return $mediaitems;
    }

    /**
     * Crops previously uploaded file and save result as "crp_$filename"
     * 
     * @param type $filename
     * @param type $x
     * @param type $y
     * @param type $w
     * @param type $h 
     * @param type $jcw - picture is scaled in browser - visible width
     * @param type $jch - picture is scaled in browser - visible height
     */
    public function crop_file($filename, $x, $y, $w, $h, $jcw, $jch) {
        $targ_w = $targ_h = 150;
        $jpeg_quality = 90;

        $tmpfilename = SHARED_MEDIA_LIBRARY_ABSOLUTE_PATH . $filename;

        switch (strtolower(substr(strrchr($filename, '.'), 1))) {
            case 'jpg':
            case 'jpeg':
                $img_r = @imagecreatefromjpeg($tmpfilename);
                $write_image = 'imagejpeg';
                $image_quality = isset($options['jpeg_quality']) ?
                        $options['jpeg_quality'] : 75;
                break;
            case 'gif':
                @imagecolortransparent($new_img, @imagecolorallocate($new_img, 0, 0, 0));
                $img_r = @imagecreatefromgif($tmpfilename);
                $write_image = 'imagegif';
                $image_quality = null;
                break;
            case 'png':
                @imagecolortransparent($new_img, @imagecolorallocate($new_img, 0, 0, 0));
                @imagealphablending($new_img, false);
                @imagesavealpha($new_img, true);
                $img_r = @imagecreatefrompng($tmpfilename);
                $write_image = 'imagepng';
                $image_quality = isset($options['png_quality']) ?
                        $options['png_quality'] : 9;
                break;
            default:
                $img_r = null;
        }
        
       
        // image size in browser (and coordinaates used by jcrop):  w: $_POST['jcw'] h: $_POST['jch']
        // to find real transformation coordinates we need to scale them to real image size

        $real_w = imagesx($img_r);
        $real_h = imagesy($img_r);
        $real_crop_x = $x * $real_w / $jcw;
        $real_crop_y = $y * $real_h / $jch;
        $real_crop_w = $w * $real_w / $jcw;
        $real_crop_h = $h * $real_h / $jch;
        $targ_w = $real_crop_w;
        $targ_h = $real_crop_h;

        $dst_r = ImageCreateTrueColor($targ_w, $targ_h);

        $success = $img_r && imagecopyresampled(
                        $dst_r, $img_r, 0, 0,  $real_crop_x, $real_crop_y, $targ_w, $targ_h, $real_crop_w, $real_crop_h
                ) && $write_image($dst_r, SHARED_MEDIA_LIBRARY_ABSOLUTE_PATH . 'crp_'.$filename, $image_quality);
        
        $this->create_thumbnail('crp_' . $filename);
    }

    /**
     * Save previously uploaded file to database
     * Wrapper to fn : add_shared_media_item
     * 
     * @param type $filename 
     */
    public function save_file($filename) {
        $tmpfilename = SHARED_MEDIA_LIBRARY_ABSOLUTE_PATH . $filename;
        $md5 = md5_file($tmpfilename);
        $filesize = filesize($tmpfilename);
        $getimagesize = getimagesize($tmpfilename);
        $imagesize = $getimagesize[0] . 'x' . $getimagesize[1];

        $imageinfo = array('filename' => $filename, 'filesize' => $filesize, 'md5' => $md5, 'imagesize' => $imagesize);

        $this->add_shared_media_item($imageinfo);
        $this->create_thumbnail($filename);
    }

    /**
     * Create thumbnail from previously uploaded file and save it in same media folder with name "thb_$filename"
     * 
     * @param type $filename
     * @return type 
     */
    public function create_thumbnail($filename) {
        $tmpfilename = SHARED_MEDIA_LIBRARY_ABSOLUTE_PATH . $filename;
        $thb_w = $thb_y = 75;
        
        list($img_width, $img_height) = @getimagesize($tmpfilename);
        if (!$img_width) return false;
        
        $scale = min($thb_w / $img_width, $thb_y / $img_height);
        if ($scale >= 1) {
            return copy($tmpfilename, SHARED_MEDIA_LIBRARY_ABSOLUTE_PATH . 'thb_'.$filename);
        }
        
        $new_width = $img_width * $scale;
        $new_height = $img_height * $scale;
        
        $new_img = @imagecreatetruecolor($new_width, $new_height);
        switch (strtolower(substr(strrchr($filename, '.'), 1))) {
            case 'jpg':
            case 'jpeg':
                $src_img = @imagecreatefromjpeg($tmpfilename);
                $write_image = 'imagejpeg';
                $image_quality = isset($options['jpeg_quality']) ?
                        $options['jpeg_quality'] : 75;
                break;
            case 'gif':
                @imagecolortransparent($new_img, @imagecolorallocate($new_img, 0, 0, 0));
                $src_img = @imagecreatefromgif($tmpfilename);
                $write_image = 'imagegif';
                $image_quality = null;
                break;
            case 'png':
                @imagecolortransparent($new_img, @imagecolorallocate($new_img, 0, 0, 0));
                @imagealphablending($new_img, false);
                @imagesavealpha($new_img, true);
                $src_img = @imagecreatefrompng($tmpfilename);
                $write_image = 'imagepng';
                $image_quality = isset($options['png_quality']) ?
                        $options['png_quality'] : 9;
                break;
            default:
                $src_img = null;
        }
        $success = $src_img && @imagecopyresampled(
                        $new_img, $src_img, 0, 0, 0, 0, $new_width, $new_height, $img_width, $img_height
                ) && $write_image($new_img, SHARED_MEDIA_LIBRARY_ABSOLUTE_PATH . 'thb_'.$filename, $image_quality);
        
        @imagedestroy($src_img);
        @imagedestroy($new_img);
        return $success;
        
    }


}

?>
