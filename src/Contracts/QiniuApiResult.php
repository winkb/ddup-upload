<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2018/7/21
 * Time: 下午7:21
 */

namespace Ddup\Upload\Contracts;


use Ddup\Part\Api\ApiResultInterface;
use Illuminate\Support\Collection;


class QiniuApiResult implements ApiResultInterface
{


    /**
     * @var \Qiniu\Http\Error
     */
    private $error_reponse;
    private $data;

    public function __construct($ret)
    {
        $this->data          = new Collection($ret['data']);
        $this->error_reponse = $ret['err'];
    }

    public function getData():Collection
    {
        return $this->data;
    }

    public function getMsg()
    {
        return $this->error_reponse ? $this->error_reponse->message() : '';
    }

    public function isSuccess()
    {
        return is_null($this->error_reponse);
    }

    public function getCode()
    {
        return $this->error_reponse ? $this->error_reponse->code() : 0;
    }

    public function get($name)
    {
        return $this->data->get($name);
    }

}