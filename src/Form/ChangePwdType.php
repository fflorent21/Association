<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ChangePwdType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('oldPassword', PasswordType::class, [
                'required' => true,
                'label' => 'Mot de passe actuel :',
                'attr' => [
                    'placeholder' => 'Mot de passe actuel...'
                ],
            ])
            ->add('newPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passes doivent correspondre.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => true,
                'first_options'  => ['label' => 'Nouveau mot de passe :'],
                'second_options' => ['label' => 'Confirmer votre nouveau mot de passe :'],
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
                    'placeholder' => "Nouveau mot de passe..."
                ]],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => ['class' => 'btn-success']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
