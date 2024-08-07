<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('Content',TextareaType::class)
            ->add('Envoyer',SubmitType::class)
           //Sélectionner Template avec un menu déroulant
            ->add('Template', ChoiceType::class, [
                'choices'  => [
                    'Signup GC' => 'emails/signup.html.twig',
                    'Rappel' => 'emails/rappel.html.twig',
                    'Compte-rendu' => 'emails/compterendu.html.twig',
                    'marketing' => 'emails/marketing.html.twig',
                ],
            ])
            
             

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
