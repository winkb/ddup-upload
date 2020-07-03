<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/1/21
 * Time: 上午9:08
 */

namespace Ddup\Upload\Config;


use Ddup\Part\Struct\StructReadable;

class ConfigStruct extends StructReadable
{
    public $access_key = '';
    public $secret_key = '';
    public $bucket     = '';
    public $domain     = '';
}