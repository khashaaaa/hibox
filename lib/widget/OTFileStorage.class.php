<?php

OTBase::import('system.uploader.php.UploadHandler');

class OTFileStorage extends UploadHandler implements WidgetInterface
{
    private $language;
    private $fileType;

    static $acceptByExtension = array(
        'xls' => 'application/vnd.ms-excel',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'txt' => 'text/plain',
    );

    public function __construct($language, $fileType, $options = null, $error_messages = null, $uploadFolder = false)
    {
        parent::__construct($options, false, $error_messages, $uploadFolder);
        $this->language = $language;
        $this->fileType = $fileType;
    }

    public static function getWidget(array $options = [])
    {
        AssetsMin::registerJsFile('/js/vendor/blueimp/jQuery-File-Upload/js/vendor/jquery.ui.widget.js');
        AssetsMin::registerJsFile('/js/vendor/blueimp/JavaScript-Load-Image/js/load-image.all.min.js');
        AssetsMin::registerJsFile('/js/vendor/blueimp/JavaScript-Canvas-to-Blob/js/canvas-to-blob.min.js');
        AssetsMin::registerJsFile('/js/vendor/blueimp/jQuery-File-Upload/js/jquery.iframe-transport.js');
        AssetsMin::registerJsFile('/js/vendor/blueimp/jQuery-File-Upload/js/jquery.fileupload.js');
        AssetsMin::registerJsFile('/js/vendor/blueimp/jQuery-File-Upload/js/jquery.fileupload-process.js');
        AssetsMin::registerJsFile('/js/vendor/blueimp/jQuery-File-Upload/js/jquery.fileupload-image.js');
        AssetsMin::registerJsFile('/js/vendor/blueimp/jQuery-File-Upload/js/jquery.fileupload-validate.js');
        AssetsMin::registerCssFile('/js/vendor/blueimp/jQuery-File-Upload/css/jquery.fileupload.css');

        AssetsMin::registerJsFile('/js/libs/jquery/jquery.colorbox/jquery.colorbox-min.js');
        AssetsMin::registerCssFile('/css/libs/jquery/jquery.colorbox/jquery-colorbox.css');


        $defaultOptions = array(
            'identifier' => 'prf' . rand(10000, 99999) . '_fileuploadd', // id для input[type=file]
            'displayName' => '',                                         // заголовок
            'saveUrl' => '/?p=upload',                                   // url для сохранения
            'maxCount' => 0,                                             // максимальное кол-во файлов, 0 - без ограничения
            'language' => Session::getActiveLang(),                      // текущий язык интерфейса
            'fileType' => 'Any',                                         // тип файлов (Any, Image)
            'readOnly' => 0,                                             // флаг "только для чтения"
            'required' => 0,                                             // флаг "обязательно для заполнения"
            'uploadedFiles' => array(),                                  // загруженные файлы
            'acceptFileTypes' => array(),                                // допустимые расширения файлов
            'acceptInputAttribute' => array(),                           // возможные расширения файлов при выборе
        );
        $options = array_merge($defaultOptions, $options);

        // добавим noimg для не картинок
        foreach ($options['uploadedFiles'] as &$uploadedFile) {
            if (empty($uploadedFile['previewUrl'])) {
                $uploadedFile['previewUrl'] = '/i/noimg.png';
            }
        }
        // добавим acceptFileTypes для картинок
        if (empty($options['acceptFileTypes']) && $options['fileType'] === 'Image') {
            $options['acceptFileTypes'] = array('gif', 'jpg', 'jpeg', 'png');
        }
        // добавим acceptInputAttribute
        if (empty($options['acceptInputAttribute']) && $options['fileType'] === 'Image') {
            $options['acceptInputAttribute'] = array('image/*');
        }
        if (empty($options['acceptInputAttribute']) && !empty($options['acceptFileTypes'])) {
            foreach ($options['acceptFileTypes'] as $type) {
                $options['acceptInputAttribute'][] = '.' . $type;
                if (
                    isset(self::$acceptByExtension[$type]) &&
                    !in_array(self::$acceptByExtension[$type], $options['acceptInputAttribute'])
                ) {
                    $options['acceptInputAttribute'][] = self::$acceptByExtension[$type];
                }
            }
        }

        return General::viewFetch('/widget/file', array(
                'path' => CFG_VIEW_ROOT,
                'vars' => array(
                    'options' => $options
                )
            )
        );
    }

    /**
     * Получить адрес загрузки файла
     *
     * @param string $fileName
     * @return OtapiUploadUrlInfo
     */
    protected function getFileUploadUrl($fileName)
    {
        /**
         * @var OtapiUploadUrlInfoAnswer $answer
         */
        OTAPILib2::GetFileUploadUrl($this->language, $fileName, $this->fileType, $answer);
        OTAPILib2::makeRequests();
        return $answer->GetResult();
    }

    /**
     * @param string $fileId
     * @return OtapiFileInfo
     */
    protected function getFileInfo($fileId)
    {
        /**
         * @var OtapiFileInfoAnswer $answer
         */
        OTAPILib2::GetFileInfo($this->language, $fileId, $answer);
        OTAPILib2::makeRequests();
        return $answer->GetResult();
    }

    public function post($print_response = false)
    {
        $upload = isset($_FILES[$this->options['param_name']]) ?
            $_FILES[$this->options['param_name']] : null;
        // Parse the Content-Disposition header, if available:
        $fileName = isset($_SERVER['HTTP_CONTENT_DISPOSITION']) ?
            rawurldecode(preg_replace(
                '/(^[^"]+")|("$)/',
                '',
                $_SERVER['HTTP_CONTENT_DISPOSITION']
            )) : null;
        // Parse the Content-Range header, which has the following form:
        // Content-Range: bytes 0-524287/2000000
        $content_range = isset($_SERVER['HTTP_CONTENT_RANGE']) ?
            preg_split('/[^0-9]+/', $_SERVER['HTTP_CONTENT_RANGE']) : null;
        $size =  $content_range ? $content_range[3] : null;
        $files = array();
        if ($upload && is_array($upload['tmp_name'])) {
            // param_name is an array identifier like "files[]",
            // $_FILES is a multi-dimensional array:
            foreach ($upload['tmp_name'] as $index => $value) {
                $fileName = $fileName ? $fileName : $upload['name'][$index];

                $files[] = $this->handle_file_upload(
                    $upload['tmp_name'][$index],
                    $fileName,
                    $size ? $size : $upload['size'][$index],
                    $upload['type'][$index],
                    $upload['error'][$index],
                    $index,
                    $content_range
                );
            }
        } else {
            // param_name is a single object identifier like "file",
            // $_FILES is a one-dimensional array:
            $fileName = $fileName ? $fileName : $upload['name'];

            $files[] = $this->handle_file_upload(
                isset($upload['tmp_name']) ? $upload['tmp_name'] : null,
                $fileName,
                $size ? $size : (isset($upload['size']) ?
                    $upload['size'] : $_SERVER['CONTENT_LENGTH']),
                isset($upload['type']) ?
                    $upload['type'] : $_SERVER['CONTENT_TYPE'],
                isset($upload['error']) ? $upload['error'] : null,
                null,
                $content_range
            );
        }

        return array($this->options['param_name'] => $files);
    }

    protected function handle_file_upload($uploaded_file, $name, $size, $type, $error,
                                          $index = null, $content_range = null)
    {
        /* get uploaded url */
        $fileUploadUrl = $this->getFileUploadUrl($name);

        $file = new stdClass();
        $file->name = $this->get_file_name($name, $type, $index, $content_range);
        $file->size = $this->fix_integer_overflow(intval($size));
        $file->type = $type;

        if ($uploaded_file && is_uploaded_file($uploaded_file)) {
            if ($in = fopen($uploaded_file, 'r')) {
                /* push file */
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_PUT, 1);
                curl_setopt($curl, CURLOPT_INFILESIZE, filesize($uploaded_file));
                curl_setopt($curl, CURLOPT_INFILE, $in);
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
                curl_setopt($curl, CURLOPT_URL, $fileUploadUrl->GetUploadUrl());
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
                $output = curl_exec($curl);

                fclose($in);
                if ($this->options['discard_aborted_uploads']) {
                    if ($output === false) {
                        $file->error = OTBase::isTest() ? curl_error($curl) : Lang::get('error_loading_file');
                    } else {
                        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

                        if ($httpCode !== 200) {
                            $file->error = OTBase::isTest() ? 'HTTP ' . $httpCode : Lang::get('error_loading_file');
                        }
                    }
                }
                curl_close($curl);
            } else {
                if ($this->options['discard_aborted_uploads']) {
                    $file->error = 'access denied';
                }
            }
        } elseif ($error && OTBase::isTest()) {
            // https://www.php.net/manual/ru/features.file-upload.errors.php
            $phpFileUploadErrors = array(
                0 => 'There is no error, the file uploaded with success',
                1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
                2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
                3 => 'The uploaded file was only partially uploaded',
                4 => 'No file was uploaded',
                6 => 'Missing a temporary folder',
                7 => 'Failed to write file to disk',
                8 => 'A PHP extension stopped the file upload',
            );
            $file->error = isset($phpFileUploadErrors[$error]) ? $phpFileUploadErrors[$error] : Lang::get('error_loading_file');
        } else {
            $file->error = Lang::get('error_loading_file');
        }

        $fileInfo = $this->getFileInfo($fileUploadUrl->GetId());

        $file->url = $fileInfo->GetUrl();
        $file->fileId = $fileUploadUrl->GetId();
        $file->thumbnailUrl = $fileInfo->GetPreviewUrl();
        $file->size = $fileInfo->GetSize();

        return $file;
    }
}