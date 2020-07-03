<?php namespace Ddup\Upload\Contracts;

interface UploaderProcessInterface
{
    function save($path);

    function parse($file);

    function getCode();

    function getError();
}
