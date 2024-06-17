<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('search',TextType::class , [
                'attr' => ['placeholder' => 'Search']
            ])
            ->add('searchButton',SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary'],
                'label' => 'Search',

            ]);
    }
}