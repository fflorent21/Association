<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('author', TextType::class, [
                'required' => true,
                'label' => 'Nom d\'utilisateur :',
                'attr' => [
                    'placeholder' => 'Nom d\'utilisateur...'
                ],
            ])
            ->add('first_name', TextType::class, [
                'required' => true,
                'label' => 'Prénom :',
                'attr' => [
                    'placeholder' => 'Prénom...'
                ],
            ])
            ->add('last_name', TextType::class, [
                'required' => true,
                'label' => 'Nom :',
                'attr' => [
                    'placeholder' => 'Nom...'
                ],
            ])
            ->add('phone', TelType::class, [
                'required' => true,
                'label' => 'Numéro de téléphone :',
                'attr' => [
                    'placeholder' => 'Numéro de téléphone...'
                ],
            ])
            ->add('email', EmailType::class, [
                'required' => true,
                'label' => 'Adresse électronique :',
                'attr' => [
                    'placeholder' => 'Adresse électronique...'
                ],
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passes doivent correspondre.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['label' => 'Mot de passe :'],
                'second_options' => ['label' => 'Confirmer votre mot de passe :'],
                'constraints' => [
                    new NotBlank(['message' => "Ce champ est obligatoire."]),
                    new Length([
                        'min' => 12,
                        'minMessage' => 'Le mot de passe doit comporter plus de {{ limit }} caractères.',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                    new Regex([
                        'pattern' => '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[-+!*$@%_])([-+!*$@%_\w]{8,})$/u',
                        'message' => 'Votre mot de passe doit contenir : Une lettre majuscule, une lettre minuscule, un chiffre et un caractère spécial ( -, +, !, *, $, @, %, _ )'
                    ]),
                ],
                'invalid_message' => 'Les mots de passe doivent être identiques.',
                'options' => ['attr' => [
                    'class' => 'password-field',
                    'placeholder' => "Mot de passe..."
                ]],
            ])
            ->add('rgpd', CheckboxType::class, [
                'required' => true,
                'label' => 'En cochant cette case, vous reconnaissez avoir pris connaissance et accepté les conditions générales d\'utilisation du site.',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
