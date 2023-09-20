<?php

namespace App\Form;

use App\Entity\Documentation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DocumentationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'required' => true,
            ])
            ->add('excerpt', TextType::class, [
                'label' => 'Extrait',
                'required' => true,
            ])
            ->add('text', TextareaType::class, [
                'label' => 'Contenu',
                'required' => true,
            ])
            ->add('publish', ChoiceType::class, [
                'label' => 'Publier',
                'choices'  => [
                    'Oui' => true,
                    'Non' => false,
                ],
                'required' => true,
            ])
            ->add('categories', HiddenType::class, [
                'label' => 'Catégories',
                'required' => false,
                'mapped' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Documentation::class,
        ]);
    }
}
