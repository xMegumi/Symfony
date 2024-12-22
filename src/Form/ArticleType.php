<?php

namespace App\Form;

use App\Entity\Article;
use Symfony\Component\TypeInfo\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;  
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('titre', TextType::class, [
            'attr' => [
                'class' => 'form-control',
                'placeholder' => 'Titre de l\'article' 
            ],
            'label' => 'Titre de l\'article', 
            'label_attr' => ['class' => 'form-label'],
        ])
        ->add('texte', TextareaType::class, [
            'attr' => [
                'class' => 'form-control',
                'placeholder' => 'Contenu de l\'article' 
            ],
            'label' => 'Contenu de l\'article',
            'label_attr' => ['class' => 'form-label'], 
            'required' => true, 
        ])

        ->add('brochure', FileType::class, [
            'label' => 'Brochure (JPG or PNG file)',

            'mapped' => false,
            'required' => false,
            
            'constraints' => [
                new File([
                    'maxSize' => '2028k',
                    'mimeTypes' => [
                        'image/jpeg',
                        'image/png',
                    ],
                    'mimeTypesMessage' => 'Please upload a valid JPG or PNG image',
                ]),
            ],
        ])

        

        ->add('publie', CheckboxType::class, [
            'attr' => [
                'class' => 'form-check-input',
            ],
            'label' => 'Publier l\'article ', 
            'label_attr' => ['class' => 'form-check-label'], 
            'required' => false, 
        ])
        ->add('date', DateTimeType::class, [
            'widget' => 'single_text',
            'attr' => [
                'class' => 'form-control', 
                'placeholder' => 'Date et heure de publication'
            ],
            'label' => 'Date de publication',
            'label_attr' => ['class' => 'form-label'],
        ])
        ->add('save', SubmitType::class, [
            'label' => 'Sauvegarder l\'article',
            'attr' => [
                'class' => 'btn btn-primary mt-4 w-100' 
            ]
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
