<?php
/**
 * Created by PhpStorm.
 * User: sid
 * Date: 04.08.18
 * Time: 12:52
 */

namespace App\Form\Lib\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

class DateTimeToStringTransformer implements DataTransformerInterface
{
    public function __construct() { }

    /**
     * @param \DateTime|null $datetime
     * @return string
     */
    public function transform($datetime)
    {
        if(is_null($datetime)) {
            return '';
        }
        return $datetime->format('Y-m-d H:i:s');
    }

    /**
     * @param string $datetimeString
     *
     * @return \DateTime
     */
    public function reverseTransform($datetimeString)
    {
        $datetime = new \DateTime($datetimeString);
        return $datetime;
    }
}