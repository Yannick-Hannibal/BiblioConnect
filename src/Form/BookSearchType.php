<?php

namespace App\Form;

use App\Entity\Book;
use App\Entity\Author;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class BookSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'required' => false,
                'label' => 'Titre',
                'attr' => ['placeholder' => 'Rechercher par titre'],
            ])
            // ->add('description', EntityType::class, [
            //     'required' => false,
            //     'class' => Author::class,
            //     'choice_label' => 'name', // Assurez-vous que l'entité Author a une propriété 'name'
            //     'placeholder' => 'Choisir un auteur',
            //     'label' => 'Auteur',
            // ])
            ->add('author', EntityType::class, [
                'required' => false,
                'class' => Author::class,
                'choice_label' => 'name',
                'placeholder' => 'Choisir un auteur',
                'label' => 'Auteur',
            ])            
            // ->add('language')
            ->add('category',EntityType::class, [
                'required' => false,
                'class' => Category::class,
                'choice_label' => 'name', // Assurez-vous que l'entité Category a une propriété 'name'
                'placeholder' => 'Choisir une catégorie',
                'label' => 'Catégorie',
            ])
            // ->add('available')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Book::class,
            'method' => 'GET',
        ]);
    }
}
