<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\CallbackTransformer;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'Utilisateur' => 'ROLE_USER',
                    'Employeur' => 'ROLE_EMPLOYER',
                    'Administrateur' => 'ROLE_ADMIN',
                ],
                'expanded' => true,  // Affiche en boutons radio
                'multiple' => false, // Un seul rôle peut être sélectionné
                'label' => 'Rôle',
            ])
            ->add('password');

       
        $builder->get('roles')
            ->addModelTransformer(new CallbackTransformer(
                function ($rolesArray) {
                    // Convertit l'array en une simple valeur string
                    return is_array($rolesArray) && count($rolesArray) > 0 ? $rolesArray[0] : 'ROLE_USER';
                },
                function ($roleString) {
                    // Reconvertit la string en tableau avant de l'envoyer en base de données
                    return [$roleString];
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
