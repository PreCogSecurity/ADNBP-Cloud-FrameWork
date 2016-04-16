<?php
namespace CloudFramework\Exceptions;

/**
 * Class LoaderException
 * @package CloudFramework\Exceptions
 */
class LoaderException extends \Exception
{
    public function __construct()
    {
        list($message, $code, $previus) = func_get_args();
        parent::__construct($message, $code, $previus);
    }
}
