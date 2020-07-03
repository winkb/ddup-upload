<?php namespace Ddup\Upload\Contracts;


interface UploaderInterface
{
    function uploadFile($file);

    function uploadString($string, $file_name = null);
}
