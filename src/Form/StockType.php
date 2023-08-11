<?php

namespace App\Form;

use App\Entity\Stock;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class StockType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('TypeCarte', ChoiceType::class, [
                "attr" => [
                    "class" => "form-control mb-2"
                ],
                'choices'  => [
                    "LOW" => "LOW",
                    'MID' => "MID",
                    'HIGH' => "HIGH",
                ],
            ])
            ->add('Quantite', IntegerType::class, [
                "attr" => [
                    "class" => "form-control mb-2"
                ]                
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Stock::class,
        ]);
    }
}
