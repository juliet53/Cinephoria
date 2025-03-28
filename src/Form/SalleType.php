<?php

namespace App\Form;

use App\Entity\Cinema;
use App\Entity\Salle;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SalleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('numero')
            ->add('capacite')
            ->add('cinema', EntityType::class, [
                'class' => Cinema::class,        // Entité liée
                'choice_label' => 'nom',         // Champ à afficher dans le select (nom du cinéma)
                'placeholder' => 'Sélectionner un cinéma',  // Texte affiché par défaut
                'required' => true,              // Si tu veux rendre ce champ obligatoire
            ]);
           
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Salle::class,
        ]);
    }
}
