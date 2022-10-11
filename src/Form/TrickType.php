<?php

declare(strict_types=1);

namespace App\Form;

use App\Doctrine\Entity\Category;
use App\Doctrine\Entity\Trick;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\UX\Dropzone\Form\DropzoneType;

final class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'empty_data' => '',
                'attr' => [
                    'placeholder' => 'Nom de la figure',
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'empty_data' => '',
                'attr' => [
                    'placeholder' => 'Description de la figure',
                ],
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'label' => 'Catégorie',
            ])
            ->add('coverFile', DropzoneType::class, [
                'label' => 'Image de couverture',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Glisser et déposer un fichier ou cliquer pour sélectionner un fichier',
                ],
            ])
            ->add('medias', CollectionType::class, [
                'label' => 'Médias',
                'entry_type' => MediaType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefault('data_class', Trick::class);
    }
}
