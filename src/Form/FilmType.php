<?php

namespace App\Form;

use App\Entity\Film;
use App\Entity\Genre;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class FilmType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description', TextareaType::class, [
                'attr' => ['rows' => 5, 'cols' => 50],  
                'required' => false,  
                'constraints' => [
                    new Length([
                        'max' => 1000,  
                        'maxMessage' => 'La description ne peut pas dépasser {{ limit }} caractères.',
                    ])
                ]
            ])
            ->add('crush')
            ->add('director')
            ->add('genres', EntityType::class, [
                'class' => Genre::class,
                'choice_label' => 'nom', // "nom" au lieu de "title" car c'est le champ dans Genre
                'multiple' => true,  // Permet de sélectionner plusieurs genres
                'expanded' => true,  // Affiche des cases à cocher au lieu d'une liste déroulante
                'by_reference' => false, // Important pour les relations ManyToMany
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Film::class,
        ]);
    }
}
