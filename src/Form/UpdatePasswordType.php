<?php

declare(strict_types=1);

namespace App\Form;

use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\NotBlank;

final class UpdatePasswordType extends ResetPasswordType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('currentPassword', PasswordType::class, [
                'label' => 'Mot de passe actuel',
                'empty_data' => '',
                'mapped' => false,
                'attr' => [
                    'placeholder' => 'Votre mot de passe actuel',
                ],
                'constraints' => [
                    new UserPassword(),
                    new NotBlank(),
                ],
            ]);
    }
}
