<?php

declare(strict_types=1);

namespace App\Form;

use App\Doctrine\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Dropzone\Form\DropzoneType;

final class UpdateAvatarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('avatarFile', DropzoneType::class, [
                'label' => 'Avatar',
                'attr' => [
                    'placeholder' => 'Glisser et déposer un fichier ou cliquer pour sélectionner un fichier',
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', User::class);
        $resolver->setDefault('validation_groups', ['avatar', 'Default']);
    }
}
