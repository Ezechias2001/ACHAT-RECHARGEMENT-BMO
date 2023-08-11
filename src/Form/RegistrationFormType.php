<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label'=> false,
                'attr' =>  [
                    'class'=> 'form-control',
                    'placeholder'=> 'Nom'
                ],
                'label'=> false
            ])
            ->add('email', EmailType::class, [
                'attr' =>  [
                    'class'=> 'form-control',
                    'placeholder'=> 'nom@gmail.com'
                ],
                'label'=> false
            ])
            ->add('plainPassword', RepeatedType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'type' => PasswordType::class,
                'invalid_message' => 'Mot de passe incorrect',
                'required' => true,
                'mapped' => false,
                'label'=> false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Entrer un mot de passe',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Votre mot de passe doit avoir au moins {{ limit }} characteres',
                        // max length allowed by Symfony for security reasons
                        'max' => 15,
                    ]),
                ],
                'first_options' => [
                    'label'=>false,
                    'attr' => [
                        'autocomplete' => 'new-password',
                        'class'=>'form-control form-icon-input',
                        'placeholder'=> 'Mot de passe...'
                    ],
                ],
                'second_options' => [
                    'label'=>false,
                    'attr' => [
                        'autocomplete' => 'confirm-password',
                        'class'=>'form-control form-icon-input',
                        'placeholder'=> 'Confirmer mot de passe...'
                    ],
                ]
            ])            
            ->add('agreeTerms', CheckboxType::class, [
                'label'=> false,
                'attr' =>  [
                    'class'=> 'form-check-input'
                ],
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'Vous acceptez nos termes.',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
