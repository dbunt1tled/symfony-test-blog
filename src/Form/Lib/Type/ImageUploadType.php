<?php
/**
 * Created by PhpStorm.
 * User: sid
 * Date: 05.08.18
 * Time: 9:48
 */

namespace App\Form\Lib\Type;

use App\Entity\Image;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImageUploadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('file', FileType::class,[
            'required' => false,
            'attr' => [
                'accept' => 'image/*',
                /*'multiple' => 'multiple'/**/
            ],
        ]);
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Image::class,
        ]);
    }
}