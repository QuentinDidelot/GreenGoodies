<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        
            ->add('lastName', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('firstName', TextType::class, [
                'label' => 'Prénom',
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse email',
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\Email(),
                ],
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options'  => ['label' => 'Mot de passe'],
                'second_options' => ['label' => 'Confirmation mot de passe'],
                'invalid_message' => 'Les mots de passe doivent correspondre.',
                'constraints' => [
                    new Assert\NotBlank(),
                ],
            ])
            ->add('acceptTerms', CheckboxType::class, [
                'label' => "J'accepte les CGU de GreenGoodies",
                'mapped' => false,
                'constraints' => [
                    new Assert\IsTrue([
                        'message' => 'Vous devez accepter les CGU pour continuer.',
                    ]),
                ],
                'attr' => ['class' => 'cgu-checkbox']
            ])            
            ->add('submit', SubmitType::class, [
                'label' => "S'inscrire",
                'attr' => ['class' => 'button-type-one']
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

