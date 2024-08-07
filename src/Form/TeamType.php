<?php

namespace App\Form;

use App\Entity\Teams;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TeamType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) : void
    {
        $builder
            ->add('name', TextType::class)
            ->add('designation', TextType::class)
            ->add('teamPhoto',HiddenType::class,[
                'data_class' => null,
                'label' => false,
                'required' => false
            ])
            ->add('orderPriority',NumberType::class,[
                'label' => 'Order',
                'attr' => [
                    'style' => 'width:17%'
                ],
            ])
            ->add('save', SubmitType::class, [
                'attr' => [
                    'class' => 'btn btn-success',
                ],
                'label' => 'Save'
            ])
        ;

    }

    public function configureOptions(OptionsResolver $resolver) : void
    {
        $resolver->setDefaults([
            'data_class' => Teams::class,
        ]);
    }
}