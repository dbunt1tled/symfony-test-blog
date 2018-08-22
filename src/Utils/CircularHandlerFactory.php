<?php
/**
 * Created by PhpStorm.
 * User: sid
 * Date: 21.08.18
 * Time: 23:57
 */

namespace App\Utils;

class CircularHandlerFactory
{
    /**
     * @return \Closure
     */
    public static function getId()
    {
        return function ($object) {
            return $object->getId();
        };
    }
}