<?php

declare(strict_types=1);

namespace App\Form;

use App\Doctrine\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'empty_data' => '',
                'attr' => [
                    'placeholder' => 'Votre adresse email',
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Mot de passe',
                'empty_data' => '',
                'attr' => [
                    'placeholder' => 'Votre mot de passe',
                ],
            ])
            ->add('nickname', TextType::class, [
                'label' => 'Pseudo',
                'empty_data' => '',
                'attr' => [
                    'placeholder' => 'Votre pseudo',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', User::class);
        $resolver->setDefault('validation_groups', ['password', 'Default']);
    }
}
