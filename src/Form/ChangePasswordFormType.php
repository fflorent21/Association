<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ChangePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'required' => true,
                'first_options'  => array('label' => 'Nouveau mot de passe :'),
                'second_options' => array('label' => 'Confirmer votre nouveau mot de passe :'),
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
