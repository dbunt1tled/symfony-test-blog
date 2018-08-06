<?php
/**
 * Created by PhpStorm.
 * User: sid
 * Date: 04.08.18
 * Time: 13:03
 */

namespace App\Form\Lib\Type;

use App\Form\Lib\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;

class HiddenDateTimeType extends AbstractType
{
    public function __construct() { }

    public function getName()
    {
        return 'hidden_datetime';
    }

    public function getParent()
    {
        return HiddenType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new DateTimeToStringTransformer();
        $builder->addModelTransformer($transformer);
    }


}