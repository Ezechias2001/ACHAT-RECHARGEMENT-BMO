<?php

namespace App\Form;

use App\Entity\Rechargement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class RechargementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Nom', TextType::class, [
                "attr" => [
                    "class" => "form-control mb-2"
                ] 
            ] )
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
            ->add('NumeroCarte', IntegerType::class, [
                "attr" => [
                    "class" => "form-control mb-2"
                ]                
            ])
            ->add('montant', IntegerType::class, [
                "attr" => [
                    "class" => "form-control mb-2"
                ]                
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
            'data_class' => Rechargement::class,
        ]);
    }
}
