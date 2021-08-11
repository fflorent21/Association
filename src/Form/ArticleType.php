<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'required' => true,
                'label' => 'Titre :',
            ])
            ->add('subtitle', TextType::class, [
                'required' => true,
                'label' => 'Sous Titre :',
            ])
            ->add('image', FileType::class,[
                'label' => 'Image :',
                'mapped' => false,
                'required' => true,
                'constraints' => [
					new File([
                        'maxSize' => '3M',
                        'mimeTypes' => [
                            'image/jpeg', 'image/png', 'image/gif', 'image/jpg'
                        ],
                        'mimeTypesMessage' => 'Le fichier n\'est pas valide, assurez vous d\'avoir un fichier au format JPEG, PNG, GIF ou JPG.'
                    ]),
                ]
            ])
            ->add('content', CKEditorType::class, [
                'required' => true,
                'label' => 'Contenu :',
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'label' => 'Catégorie :'
            ])
            ->add('status', ChoiceType::class, [
                'required' => true,
                'label' => 'Status :',
                'choices' => [
                    'Publié' => 'P',
                    'Non Publié' => 'NP',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
