<?php

declare(strict_types=1);

namespace App\Form;

use App\Form\DataTransformer\EmailUserTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ResetPasswordRequestType extends AbstractType
{
    public function __construct(private readonly EmailUserTransformer $emailUserTransformer)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('user', EmailType::class, [
            'label' => 'Email',
            'empty_data' => '',
            'attr' => [
                'placeholder' => 'Votre adresse email',
            ],
            'invalid_message' => 'Cette adresse email n\'existe pas.',
        ]);

        $builder->get('user')->addModelTransformer($this->emailUserTransformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
    }
}
