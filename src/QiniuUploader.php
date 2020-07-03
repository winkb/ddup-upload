<?php namespace Ddup\Upload;

use Ddup\Part\Api\ApiResultInterface;
use Ddup\Part\Api\ApiResulTrait;
use Ddup\Part\Libs\Str;
use Ddup\Upload\Config\ConfigStruct;
use Ddup\Upload\Contracts\QiniuApiResult;
use Ddup\Upload\Contracts\UploaderInterface;
use Qiniu\Auth;
use Qiniu\Storage\UploadManager;


class QiniuUploader implements UploaderInterface
{
    use ApiResulTrait;

    private $token;
    private $auth;
    private $config;

    public function newResult($ret):ApiResultInterface
    {
        return new QiniuApiResult($ret);
    }

    function __construct(ConfigStruct $config)
    {
        $this->config = $config;
        $this->auth   = new Auth($config->access_key, $config->secret_key);
    }

    private function getToken()
    {
        if (is_null($this->token)) {
            $this->token = $this->auth->uploadToken($this->config->bucket);
        }
        return $this->token;
    }

    public function uploadFile($file)
    {
        $uploader = new UploadManager();
        $saveAs   = basename($file);

        list($ret, $err) = $uploader->putFile($this->getToken(), $saveAs, $file);

        $this->filePath($err, $ret);

        $result = $this->parseResult(['data' => $ret, 'err' => $err]);

        return $result->isSuccess();
    }

    private function uniqueName()
    {
        return time() . Str::rand(20) . '.jpg';
    }

    public function uploadString($string, $file_name = null)
    {

        $file_name = is_null($file_name) ? $this->uniqueName() : $file_name;
        $uploader  = new UploadManager();

        list($ret, $err) = $uploader->put($this->getToken(), $file_name, $string, null, 'application/octet-stream');

        $this->filePath($err, $ret);

        $result = $this->parseResult(['data' => $ret, 'err' => $err]);
        return $result->isSuccess();
    }

    private function filePath($err, &$ret)
    {
        if ($err == null) {

            $uri = strpos($this->config->domain, 'http') === 0 ? $this->config->domain : 'http://' . $this->config->domain;

            $ret['path'] = $uri . '/' . $ret['key'];
        }
    }

}