<?php

namespace App\Form\Admin\Category;

use App\Entity\Category;
use App\Form\Lib\Type\HiddenDateTimeType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('slug')
            ->add('description',TextareaType::class,[
                'attr' => ['class' => 'summernote']
            ])
            ->add('status',ChoiceType::class,[
                  'attr' => ['class' => 'statusPost'],
                'choices' => Category::getStatuses(),
                'empty_data' => Category::getDefaultStatus(),
            ])
            ->add('image',FileType::class,[
                'label' => 'Image (jpg,png,gif)',
                'data_class' => null,
                'required' => false,
            ])
            ->add('createdAt',HiddenDateTimeType::class, [
                'attr' => [],
            ])
            ->add('parent')
        ;
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $category = $event->getData();
            $form = $event->getForm();

            $form->add('createdAtRaw',null,[
                'mapped'=>false,
                'data' => $category->getcreatedAt()->format('d F Y'),
                'attr' => [],
                'label' => 'Created At',
            ]);

        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
