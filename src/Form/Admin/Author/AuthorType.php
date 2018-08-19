<?php

namespace App\Form\Admin\Author;

use App\Entity\Author;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AuthorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('job')
            ->add('email')
            ->add('phone')
            ->add('image',FileType::class,[
                'label' => 'Image (jpg,png,gif)',
                'data_class' => null,
                'required' => false,
            ])
            ->add('shortBio')
            ->add('twitter')
            ->add('github')
            /*->add('password')/**/
            ->add('plainPassword')
            ->add('role')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Author::class,
        ]);
    }
}
