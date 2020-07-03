<?php namespace Ddup\Upload;


class UploadFactory
{

    private static function newUploader($string, $formName)
    {
        if ($_FILES) {
            $uploader = new Uploader(new UpProcessFile());
            $file     = $_FILES[$formName];
        } else {
            $uploader = new Uploader(new UpProcessString());
            $file     = $string;
        }
        return $uploader->setFile($file);
    }

    public static function image($string, $formName):Uploader
    {
        $uploader = self::newUploader($string, $formName);

        return $uploader;
    }

}