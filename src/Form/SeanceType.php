<?php

namespace App\Form;

use App\Entity\Film;
use App\Entity\Salle;
use App\Entity\Seance;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SeanceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateHeureDebut', null, [
                'widget' => 'single_text',
            ])
            ->add('dateHeureFin', null, [
                'widget' => 'single_text',
            ])
            ->add('qualite', ChoiceType::class, [
                'choices' => [
                    'Standard' => 'Standard',
                    '4DX' => '4DX',
                    '3D' => '3D',
                    '4K' => '4K',
                    'IMAX' => 'IMAX',
                ],
            ])
            ->add('placeDisponible')
            ->add('film', EntityType::class, [
                'class' => Film::class,
                'choice_label' => 'title',
            ])
            ->add('salle', EntityType::class, [
                'class' => Salle::class,
                'choice_label' => function (Salle $salle) {
                    return $salle->getNumero() . ' - ' . ($salle->getCinema() ? $salle->getCinema()->getNom() : 'Pas de cinéma');
                },
                'placeholder' => 'Choisir une salle',
            ])
            ->add('prix', NumberType::class, [
                'label' => 'Prix',
                'attr' => ['class' => 'form-control'],
                'scale' => 2, // Précision à 2 décimales
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Seance::class,
        ]);
    }
}
