<?php

namespace App\Form\Admin\BlogPost;

use App\Entity\BlogPost;
use App\Form\Lib\Type\HiddenDateTimeType;
use App\Form\Lib\Type\ImageUploadType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BlogPostType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('slug')
            ->add('description',TextareaType::class,[
                'attr' => ['cols' => '5', 'rows' => '2', 'resize' => 'none'],
            ])
            ->add('body',TextareaType::class,[
                'attr' => ['class' => 'summernote']
            ])
            ->add('createdAt',HiddenDateTimeType::class, [
                'attr' => [],
            ])
            /*->add('images',FileType::class,[
                'label' => 'Photo (Image file)',
                'data_class' => null,
                'required' => false,
                'attr' => [
                    'accept' => 'image/*',
                    'multiple' => 'multiple'
                ],
            ])/**/
            /*->add('images', ImageUploadType::class, [
                'label' => 'Photo (Image file)',
                'data_class' => null,
            ])/**/
            /*->add('images', CollectionType::class, [
                'label' => 'Photo (Image file)',
                'entry_type' => ImageUploadType::class,
                'allow_add' => true,
                'by_reference' => false,
                'data_class' => null
            ])/**/
            ->add('uploadedFile', FileType::class, [
                'label' => 'Add Photo (Image file)',
                'data_class' => null,
                'required' => false,
            ])/**/
            ->add('author')
            ->add('category')
            ->add('status',ChoiceType::class,[
                'attr' => ['class' => 'statusPost'],
                'choices' => BlogPost::getStatuses(),
                'empty_data' => BlogPost::getDefaultStatus(),
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $post = $event->getData();
            $form = $event->getForm();

            $form->add('createdAtRaw',null,[
                'mapped'=>false,
                'data' => $post->getcreatedAt()->format('d F Y'),
                'attr' => [],
                'label' => 'Created At',
            ]);
        });
    }
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        //$view->vars['form']->children['images']['file']->vars['full_name'] .= '[]';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => BlogPost::class,
        ]);
    }


}
