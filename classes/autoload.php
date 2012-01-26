<?php

/**
 * @todo replace
 * @param string $class 
 */
function __autoload($class)
{
    require_once dirname(__FILE__)  . DIRECTORY_SEPARATOR  . $class .'.php';
}

spl_autoload_register('__autoload');