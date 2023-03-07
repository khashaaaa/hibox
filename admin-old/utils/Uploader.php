<?php

/**
 * Handle file uploads via XMLHttpRequest
 */
class qqUploadedFileXhr {
    /**
     * Save the file to the specified path
     * @return boolean TRUE on success
     */
    function save($path) {
        $input = fopen("php://input", "r");
        $temp = tmpfile();
        $realSize = stream_copy_to_stream($input, $temp);
        fclose($input);

        if ($realSize != $this->getSize()){
            return false;
        }

        $target = fopen($path, "w");
        fseek($temp, 0, SEEK_SET);
        stream_copy_to_stream($temp, $target);
        fclose($target);

        return true;
    }
    function getName() {
        return $_GET['qqfile'];
    }
    function getSize() {
        if (isset($_SERVER["CONTENT_LENGTH"])){
            return (int)$_SERVER["CONTENT_LENGTH"];
        } else {
            throw new Exception('Getting content length is not supported.');
        }
    }
}

/**
 * Handle file uploads via regular form post (uses the $_FILES array)
 */
class qqUploadedFileForm {
    /**
     * Save the file to the specified path
     * @return boolean TRUE on success
     */
    function save($path) {
        if(!move_uploaded_file($_FILES['qqfile']['tmp_name'], $path)){
            return false;
        }
        return true;
    }
    function getName() {
        return $_FILES['qqfile']['name'];
    }
    function getSize() {
        return $_FILES['qqfile']['size'];
    }
}

class qqFileUploader {
    private $allowedExtensions = array();
    private $sizeLimit = 1024;
    private $file;

    function __construct(array $allowedExtensions = array(), $sizeLimit = 10485760){
        $allowedExtensions = array_map("strtolower", $allowedExtensions);

        $this->allowedExtensions = $allowedExtensions;
        $this->sizeLimit = $sizeLimit;

        //$this->checkServerSettings();

        if (isset($_GET['qqfile'])) {
            $this->file = new qqUploadedFileXhr();
        } elseif (isset($_FILES['qqfile'])) {
            $this->file = new qqUploadedFileForm();
        } else {
            $this->file = false;
        }
    }

    private function checkServerSettings(){
        $postSize = $this->toBytes(ini_get('post_max_size'));
        $uploadSize = $this->toBytes(ini_get('upload_max_filesize'));

        if ($postSize < $this->sizeLimit || $uploadSize < $this->sizeLimit){
            $size = max(1, $this->sizeLimit / 1024 / 1024) . 'M';
            die("{'error':'increase post_max_size and upload_max_filesize to $size'}");
        }
    }

    private function toBytes($str){
        $val = trim($str);
        $last = strtolower($str[strlen($str)-1]);
        switch($last) {
            case 'g': $val *= 1024;
            case 'm': $val *= 1024;
            case 'k': $val *= 1024;
        }
        return $val;
    }

    private function resizeImage($img, $width, $height){
        $image_info = getimagesize($img);
        $image_type = $image_info[2];
        if ($image_type == IMAGETYPE_JPEG) {

            $image = imagecreatefromjpeg($img);
        } elseif ($image_type == IMAGETYPE_GIF) {

            $image = imagecreatefromgif($img);
        } elseif ($image_type == IMAGETYPE_PNG) {

            $image = imagecreatefrompng($img);
        }

        if (isset($_GET['prop']))
        {
            $srcW=imagesx($image);
            $srcH=imagesy($image);
            $thumbH=$height;
            $thumbW=$width;
            $scale = min($thumbW/$srcW, $thumbH/$srcH);
            if ($scale==0) $scale = max($thumbW/$srcW, $thumbH/$srcH);

            $dstW=round($srcW*$scale);
            $dstH=round($srcH*$scale);

            if (!isset($_GET['no_border']))
            {
                $thumbH=$dstH;
                $thumbW=$dstW;
            }
            //print($dstW.' '.$dstH);
            $new_image=imagecreatetruecolor($thumbW,$thumbH);
            if (isset($_GET['bg']))
            {
                $bg=$_GET['bg'];
            } else {
                $bg=255;
            }
            $background=imagecolorallocate ($new_image, $bg, $bg, $bg);
            imagefill($new_image,0,0,$background);
            ImageCopyResampled($new_image,$image,round(($thumbW-$dstW)/2),round(($thumbH-$dstH)/2),0,0,$dstW,$dstH,$srcW,$srcH);
            $image = $new_image;
        } else {
            $w = imagesx($image);
            $h = imagesy($image);
            $new_image = imagecreatetruecolor($width, $height);
            imagecopyresampled($new_image, $image, 0, 0, 0, 0, $width, $height, $w, $h);
            $image = $new_image;
        }
        $pathinfo = pathinfo($img);
        $filename = $pathinfo['filename'];
        $dir = $pathinfo['dirname'];
        $ext = $pathinfo['extension'];
        $prefix = '_' . $width . 'x' . $height;
        $new_file = $dir . '/' . $filename . $prefix . '.' . $ext;
        if ($image_type == IMAGETYPE_JPEG) {
            imagejpeg($image, $new_file, 100);
        } elseif ($image_type == IMAGETYPE_GIF) {
            imagegif($image, $new_file);
        } elseif ($image_type == IMAGETYPE_PNG) {
            imagepng($image, $new_file);
        }
    }

    /**
     * Returns array('success'=>true) or array('error'=>'error message')
     */
    public function handleUpload($uploadDirectory, $replaceOldFile = FALSE)
    {
        if (!is_dir($uploadDirectory)) {
            if (!mkdir($uploadDirectory, 0777, true))
		return array('error' => "Server error. Directory not created.");
        }
        if (!is_writable($uploadDirectory)){
            return array('error' => "Server error. Upload directory isn't writable.");
        }

        if (!$this->file){
            return array('error' => 'No files were uploaded.');
        }

        $size = $this->file->getSize();

        if ($size == 0) {
            return array('error' => 'File is empty');
        }

        if ($size > $this->sizeLimit) {
            return array('error' => 'File is too large');
        }

        $pathinfo = pathinfo($this->file->getName());
        $filename = $pathinfo['filename'];
        // форматирование имени файла
        $filename = $this->_rus2translit($filename);
        $filename = str_replace('-', '_', $filename);
        $filename = preg_replace("/[^0-9a-zA-Z_]/", "", $filename);

        if (!$filename) {
            return array('error' => 'Invalid file name. Please use only English letters, numbers, and underscore `_`');
        }

        $ext = $pathinfo['extension'];

        if($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions)){
            $these = implode(', ', $this->allowedExtensions);
            unlink($uploadDirectory . $filename . '.' . $ext);
            return array('error' => 'File has an invalid extension, it should be one of '. $these . '.');
        }

        if(!$replaceOldFile){
            /// don't overwrite previous files that were uploaded
            while (file_exists($uploadDirectory . $filename . '.' . $ext)) {
                $filename .= rand(10, 99);
            }
        }

        if ($this->file->save($uploadDirectory . $filename . '.' . $ext)){
            if(isset($_GET['resize'])){
                foreach ($_GET['resize'] as $side) {
                    $this->resizeImage($uploadDirectory . $filename . '.' . $ext, $side, $side);
                }
            }
            return array('success'=>true, 'url' => 'http://'.dirname(dirname(dirname($_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']))).'/brands_uploads/'.$filename . '.' . $ext );
        } else {
            return array('error'=> 'Could not save uploaded file.' .
                'The upload was cancelled, or server error encountered');
        }

    }

    public function _rus2translit($string) {
        $converter = array(
            'а' => 'a', 'б' => 'b', 'в' => 'v',
            'г' => 'g', 'д' => 'd', 'е' => 'e',
            'ё' => 'e', 'ж' => 'zh', 'з' => 'z',
            'и' => 'i', 'й' => 'y', 'к' => 'k',
            'л' => 'l', 'м' => 'm', 'н' => 'n',
            'о' => 'o', 'п' => 'p', 'р' => 'r',
            'с' => 's', 'т' => 't', 'у' => 'u',
            'ф' => 'f', 'х' => 'h', 'ц' => 'c',
            'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch',
            'ь' => '', 'ы' => 'y', 'ъ' => '',
            'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
            'А' => 'A', 'Б' => 'B', 'В' => 'V',
            'Г' => 'G', 'Д' => 'D', 'Е' => 'E',
            'Ё' => 'E', 'Ж' => 'Zh', 'З' => 'Z',
            'И' => 'I', 'Й' => 'Y', 'К' => 'K',
            'Л' => 'L', 'М' => 'M', 'Н' => 'N',
            'О' => 'O', 'П' => 'P', 'Р' => 'R',
            'С' => 'S', 'Т' => 'T', 'У' => 'U',
            'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
            'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sch',
            'Ь' => '', 'Ы' => 'Y', 'Ъ' => '',
            'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya',
        );
        return strtr($string, $converter);
    }

}

$allowedExtensions = array("jpeg", "jpg", "swf");

$sizeLimit = 20 * 1024 * 1024;

$uploader = new qqFileUploader($allowedExtensions, $sizeLimit);

$result = $uploader->handleUpload(dirname(dirname(dirname(__FILE__))).'/brands_uploads/');
// to pass data through iframe you will need to encode all html tags
echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);
