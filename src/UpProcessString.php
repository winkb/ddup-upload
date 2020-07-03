<?php

namespace Ddup\Upload;


use Ddup\Part\Libs\Str;
use Ddup\Upload\Contracts\UploaderProcessInterface;


class UpProcessString implements UploaderProcessInterface
{

    private $resource;
    private $code = null;
    private $type;


    public function getCode()
    {
        return $this->code;
    }

    public function parse($string)
    {
        if (!$string) {
            $this->code = 'empty_img_string';
            return false;
        }

        $preFix   = Str::first($string, ';');
        $fileType = Str::last($preFix, '/');

        $content = file_get_contents($string);
        $size    = strlen($content);

        if (!$content) {
            $this->code = 'empty_img_content';
            return false;
        }

        $this->resource = imagecreatefromstring($content);
        $this->type     = $fileType;

        return [
            $fileType, $this->getExt($fileType), $size, null
        ];
    }

    private function getExt($fileType)
    {
        return $fileType;
    }

    public function save($file)
    {
        if (!$this->imageFun($this->getType())($this->resource, $file)) {
            $this->code = 'save_fail';
            return false;
        }
        return true;
    }

    private function imageFun($type)
    {
        return 'image' . $type;
    }

    private function getType()
    {
        return $this->type;
    }

    public function getError()
    {
        switch ((string)$this->code) {
            case 'empty_img_string':
                return '获取不到图片数据';
            case 'empty_img_content':
                return '无效的图片数据';
            case 'save_fail':
                return '文件生成失败';
            default:
                return '未知错误1';
        }
    }

}