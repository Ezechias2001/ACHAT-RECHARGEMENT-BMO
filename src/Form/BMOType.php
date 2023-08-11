<?php

namespace App\Form;

use App\Entity\BMO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class BMOType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                "attr" => [
                    "class" => "form-control mb-2"
                ] 
            ] )
            ->add('Numero', IntegerType::class, [
                "attr" => [
                    "class" => "form-control mb-2"
                ]                
            ])
            ->add('Montant', IntegerType::class, [
                "attr" => [
                    "class" => "form-control mb-2"
                ]                
            ])
            ->add('type', ChoiceType::class, [
                "attr" => [
                    "class" => "form-control mb-2"
                ],
                'choices'  => [
                    'Depot' => "Dépôt",
                    'Retrait' => "Retrait",
                ],
            ])
            ->add('date', TextType::class, [
                'attr' => [
                    'class' => 'form-control mb-2 flatpickr-input'
                ]
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BMO::class,
        ]);
    }
}
