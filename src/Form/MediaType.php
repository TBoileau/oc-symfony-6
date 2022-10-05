<?php

declare(strict_types=1);

namespace App\Form;

use App\Doctrine\Entity\VideoProvider;
use App\Form\DataTransferObject\MediaDto;
use App\Form\DataTransformer\MediaTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Dropzone\Form\DropzoneType;

final class MediaType extends AbstractType
{
    public function __construct(private readonly MediaTransformer $mediaTransformer)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', HiddenType::class)
            ->add('file', DropzoneType::class, [
                'label' => 'Image',
                'required' => false,
            ])
            ->add('alt', TextType::class, [
                'label' => 'Texte alternatif',
                'required' => false,
                'empty_data' => '',
                'attr' => [
                    'placeholder' => 'Texte alternatif de l\'image',
                ],
            ])
            ->add('url', UrlType::class, [
                'label' => 'Vidéo',
                'empty_data' => '',
                'required' => false,
            ])
            ->add('provider', EnumType::class, [
                'label' => 'Fournisseur',
                'class' => VideoProvider::class,
            ])
            ->addModelTransformer($this->mediaTransformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', MediaDto::class);
    }
}
