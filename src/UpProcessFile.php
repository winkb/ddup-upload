<?php

namespace Ddup\Upload;


use Ddup\Part\Libs\Str;
use Ddup\Upload\Contracts\UploaderProcessInterface;


class UpProcessFile implements UploaderProcessInterface
{
    private $error;
    private $type;
    private $size;
    private $tmpName;
    private $name;

    public function getCode()
    {
        return $this->error;
    }

    private function parseType()
    {
        if (Str::first($this->type, '/') == 'image') {
            return Str::last($this->type, '/');
        }
        return Str::last($this->name, '.');
    }

    private function parseExt()
    {
        return Str::last($this->name, '.');
    }

    public function parse($file)
    {
        $this->size    = $file['size'];
        $this->type    = $file['type'];
        $this->name    = $file['name'];
        $this->tmpName = $file['tmp_name'];

        $ext  = $this->parseExt();
        $type = $this->parseType();

        $this->type = $type;

        if (!$this->filter($file)) {
            return false;
        }

        return [
            $type, $ext, $this->size, $this->name
        ];
    }

    public function save($newPath)
    {
        if (!move_uploaded_file($this->tmpName, $newPath)) {
            $this->error = -2;
            return false;
        }

        return true;
    }

    private function filter($file)
    {
        switch (true) {
            case $file['error']://如果文件上传出错了返回false
                $this->error = $file['error'];
                return false;
            case !$this->isValidFile($file)://不是合法的上传文件
                $this->error = -1;
                return false;
        }
        return true;
    }

    private function isValidFile($file)
    {
        return is_uploaded_file($file['tmp_name']);
    }

    public function getError()
    {
        switch ($this->error) {
            case 1:
                $msg = '上传文件超过php配置大小';
                break;

            case 2:
                $msg = '上传文件超过表单设置最大限制';
                break;
            case 3:
                $msg = '文件只上传了一部分';
                break;
            case 4:
                $msg = '个别文件没有被上传';
                break;
            case -1:
                $msg = '不是合法的上传文件';
                break;
            case -2:
                $msg = '上传失败';
                break;
            default:
                $msg = '未知错误';
                break;
        }
        return $msg;
    }

}