<?php

namespace App\Form;

use App\Entity\Documentation;
use Eckinox\TinymceBundle\Form\Type\TinymceType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
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
            ->add('text', TinymceType::class, [
                'label' => 'Contenu',
                'required' => true,
                "attr" => [
                    "selector" => "textarea",
                    "menubar" => "file edit insert format table tools",
                    "toolbar" => "undo redo | bold italic underline | styles | alignleft aligncenter alignright alignjustify | bullist numlist",
                    "plugins" => "autolink link image table quickbars pagebreak lists advlist charmap preview wordcount searchreplace emoticons anchor",
                    "quickbars_insert_toolbar" => "quicktable quicklink hr pagebreak",
                ],
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
