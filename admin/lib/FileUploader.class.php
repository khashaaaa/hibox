<?php

/**
 * Класс занимается загрузкой файлов
 * Class FileUploader
 */
class FileUploader {
    private $thumbnails = array();
    private $allowedExtensions = array();

    public function AddThumbnailSize($width, $height, $quality){
        $this->thumbnails[] = array(
            'width' => $width,
            'height' => $height,
            'quality' => $quality
        );
    }

    public function SetAllowedExtensions($extensions){
        $this->allowedExtensions = $extensions;
    }

    public function UploadSingleFile($path, $fileInfo, $newFileName){
        $pathInfo = pathinfo($fileInfo['name']);
        $newFilePath = rtrim($path, '/\\') . '/' . $newFileName . '.' . $pathInfo['extension'];

        $this->checkExtensions($pathInfo['extension']);
        $this->moveFile($fileInfo['tmp_name'], $newFilePath);

        return file_exists($newFilePath) ? $newFilePath : false;
    }

    public function checkExtensions($fileExtension){
        $allowed = empty($this->allowedExtensions);
        foreach($this->allowedExtensions as $extension){
            $allowed = $allowed || ($fileExtension == $extension);
        }
        if(!$allowed){
            throw new Exception('File with type ' . $fileExtension . ' is not allowed');
        }
    }

    public function moveFile($tmpPath, $newPath){
        $result = move_uploaded_file($tmpPath, $newPath);
        if($result === false){
            throw new Exception('File cannot be moved');
        }
    }
}