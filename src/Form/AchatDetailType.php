<?php

namespace App\Form;

use App\Entity\AchatDetail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class AchatDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                "attr" => [
                    "class" => "form-control mb-2"
                ] 
            ] )
            ->add('dateNaissance', TextType::class, [
                'attr' => [
                    'class' => 'form-control mb-2 flatpickr-input'
                ]
            ])
            ->add('sexe', ChoiceType::class, [
                "attr" => [
                    "class" => "form-control mb-2"
                ],
                'choices'  => [
                    "Masculin" => "Masculin",
                    'Feminin' => "Feminin",
                ],
            ])
            ->add('piece', ChoiceType::class, [
                "attr" => [
                    "class" => "form-control mb-2"
                ],
                'choices'  => [
                    "Carte d'identité" => "Carte d'identité",
                    'Passe-Port' => "Passe-Port",
                    "RAVIP" => "RAVIP",
                    'LEPI' => "LEPI",
                ],
            ])
            ->add('numeroPiece', IntegerType::class, [
                "attr" => [
                    "class" => "form-control mb-2"
                ]                
            ])
            ->add('imagePiece', FileType::class,[
                'data_class' => null,
                "attr" => [
                    "class" => "form-control mb-2"
                ]
            ]
            )
            ->add('typeCarte', ChoiceType::class, [
                "attr" => [
                    "class" => "form-control mb-2"
                ],
                'choices'  => [
                    "LOW" => "LOW",
                    'MID' => "MID",
                    'HIGH' => "HIGH",
                ],
            ])
            ->add('dateCreation', TextType::class, [
                'attr' => [
                    'class' => 'form-control mb-2 flatpickr-input'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AchatDetail::class,
        ]);
    }
}
