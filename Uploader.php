<?php

/**
 * Class Uploader
 *
 * Config example:
 * ```php
 * $config = [
 *     'scene1' => [
 *          'baseDir' => 'path/to/save/image/',
 *          'rules' => [
 *              'size' => [1000000, '文件大小不超过1M'],
 *              'type' => [['image/jpeg', 'image/png'], 'Only jpg and png allowed']，
 *              'exts' => [['jpg', 'png'], 'Only jpg and png allowed'],
 *              'dims' => [[600,800], 'Required image width is 600 and height is 800']
 *          ]
*      ]
 * ]
 * ```
 */
class Uploader
{
    private $uploadedFileInfo = [], $baseDir, $rules, $urlPrefix;
    private $errorMessage;
    private $validatorsMap = [
        'size' => 'validateSize',
        'type' => 'validateType',
        'dims' => 'validateDimension',
        'exts' => 'validateExtension'
    ];

    public function __construct($config) {
        $this->baseDir = $config['baseDir'];
        $this->rules = $config['rules'];
        $this->urlPrefix = $config['urlPrefix'];
    }

    public function validateSize($file, $rule) {
        return $file->getSize() <= $rule[0];
    }

    public function validateDimension($file, $rule) {
        $dims = getimagesize($file->getTempName());
        if ($dims) {
            list($width, $height) = $dims;
            return ($width !== $rule[0][0] || $height !== $rule[0][1]);
        } else {
            return false;
        }
    }

    public function validateExtension($file, $rule) {
        $ext = $file->getExtension();
        return $ext && in_array($ext, $rule[0]);
    }

    public function validateMimeType($file, $rule) {
        return in_array($file->getRealType(), $rule[0]);
    }

    public function getSubDir() {
        return date("Y/m/d/", TIMESTAMP);
    }

    public function save($file) {
        $newName = uniqid() . "." . $file->getExtension();
        $this->uploadedFileInfo['newName'] = $newName;
        $subDir = $this->getSubDir();
        $this->uploadedFileInfo['subDir'] = $subDir;
        $this->uploadedFileInfo['imgUrl'] = $this->urlPrefix . $subDir . $newName;
        $fullDir = $this->baseDir . $subDir;
        if (!is_dir($fullDir)) {
            if (!mkdir($fullDir, 755, true)) {
                return false;
            }
        }
        $fullName = $fullDir . $newName;
        return $file->moveTo($fullName);
    }

    public function upload($file) {
        foreach ($this->rules as $key => $rule) {
            $func = $this->validatorsMap[$key];
            if (!($this->$func)($file, $rule)) {
                $this->errorMessage = $rule[1];
                return false;
            }
        }

        if (!$this->save($file)) {
            $this->errorMessage = "文件保存失败";
            return false;
        }

        return true;
    }

    public function getUploadedFileInfo() {
        return $this->uploadedFileInfo;
    }

    public function getError() {
        return $this->errorMessage;
    }
}
