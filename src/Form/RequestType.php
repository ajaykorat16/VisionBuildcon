<?php

namespace App\Form;

use App\Entity\Request;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RequestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options):void
    {
        $builder
            ->add('name', TextType::class,[
                'attr' => [
                    'class' => "wpforms-field-large wpforms-field-required",
                    'placeholder' => "Enter your name"
                ]
            ])
            ->add('email', EmailType::class,[
                'attr' => [
                    'class' => "wpforms-field-large wpforms-field-required",
                    'placeholder' => "Enter your email address"
                ]
            ])
            ->add('subject', TextType::class,[
                'attr' => [
                    'class' => "wpforms-field-large",
                    'placeholder' => "Subject"
                ]
            ])
            ->add('message', TextareaType::class,[
                'attr' => [
                    'class' => "wpforms-field-large wpforms-field-required",
                    'placeholder' => "Message"
                ]
            ])
            ->add('send_message', SubmitType::class,[
                'attr' => [
                    'class' => "wpforms-submit",
                    'label' => 'SEND MESSAGE'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver) : void
    {
        $resolver->setDefaults([
            'data_class' => Request::class,
        ]);
    }
}