<?php namespace Ddup\Upload;


use Ddup\Part\Libs\Arr;
use Ddup\Part\Libs\Str;
use Ddup\Part\Libs\Unit;
use Ddup\Upload\Contracts\UploaderProcessInterface;

class Uploader
{

    private $dir;
    private $code;
    private $types   = ['jpg', 'png', 'gif', 'jpeg'];
    private $path;
    private $maxSize = 20971520;
    private $processor;
    private $size;
    private $name;
    private $ext;
    private $rename  = false;
    private $type;
    private $file;
    private $domain;

    public function __construct(UploaderProcessInterface $processor)
    {
        $this->processor = $processor;
    }

    public function setFile($file)
    {
        $this->file = $file;
        return $this;
    }

    public function setType(Array $type)
    {
        $this->types = Arr::format($type, 'strtolower');
        return $this;
    }

    public function setDir($dir)
    {
        $this->dir = $dir;
        return $this;
    }

    public function setMaxSize($formatSize)
    {
        $this->maxSize = Unit::toSize($formatSize);
        return $this;
    }

    public function setDomain($domain)
    {
        $this->domain = $domain;
        return $this;
    }

    private function mkdir()
    {
        if (!$this->dir) return true;
        return is_dir($this->dir) || mkdir($this->dir, 0777, true);
    }

    private function parse($file)
    {
        $res = $this->processor->parse($file);
        if (!$res) return false;

        list($type, $ext, $size, $oldName) = $res;
        $this->ext  = $ext;
        $this->size = $size;
        $this->type = $type;
        $this->name = $this->newFileName($oldName);
        $this->path = $this->newPath();

        return $this->filterType($type);
    }

    private function save($path)
    {
        return $this->processor->save($path);
    }

    private function newPath()
    {
        return $this->dir . '/' . $this->name;
    }

    public function up()
    {
        if (!$this->parse($this->file)) {
            return false;
        }

        if (!self::validateSize()) {
            return false;
        }

        $this->mkdir();

        return $this->save($this->path);
    }

    private function newFileName($oldName)
    {
        if ($oldName && !$this->rename) {
            return $oldName;
        }

        return time() . Str::rand(6) . '.' . $this->ext;
    }

    private function filterType($type)
    {
        if (in_array('*', $this->types)) {
            return true;
        }

        if (!in_array(strtolower($type), $this->types)) {
            $this->code = 'not_allow_type';
            return false;
        }
        return true;
    }

    private function validateSize()
    {
        if ($this->size > $this->maxSize) {
            $this->code = 'size_too_large';
            return false;
        }
        return true;
    }

    public function getUploadedInfo()
    {
        return array(
            'path' => $this->path,
            'src'  => $this->domain ? ($this->domain . '/' . $this->path) : $this->path,
            'name' => $this->name
        );
    }

    public function getError()
    {
        if ($this->processor->getCode() !== null) {
            return $this->processor->getError();
        }

        switch ((string)$this->code) {
            case 'size_too_large':
                $msg = '文件尺寸' . Unit::formatSize($this->size) . '>' . Unit::formatSize($this->maxSize);
                break;
            case 'not_allow_type':
                $msg = '文件类型' . $this->type . '不在[' . implode(',', $this->types) . ']中';
                break;
            default:
                $msg = '未知错误0';
                break;
        }
        return $msg;
    }

}