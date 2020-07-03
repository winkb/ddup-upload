<?php
/**
 * Created by PhpStorm.
 * User: lixingbo
 * Date: 2019/1/21
 * Time: 上午9:24
 */

namespace Ddup\Upload\Test;

use Ddup\Upload\Config\ConfigStruct;
use Ddup\Upload\QiniuUploader;

class QinuUploaderTest extends TestCase
{

    public function test_upload()
    {
        $uploader = new QiniuUploader(new ConfigStruct(require __DIR__ . "/config.php"));

        $this->assertTrue($uploader->uploadFile(self::file));

        $data = $uploader->result()->getData();

        $file = $data->get('path');

        $this->assertNotNull(imagecreatefromstring(file_get_contents($file)));
    }
}