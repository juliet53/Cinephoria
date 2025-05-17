<?php
namespace App\Form;

use App\Entity\Avis;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class AvisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Commentaire', TextareaType::class, [
                'label' => 'Votre commentaire',
                'required' => true,
            ])
            ->add('Note', NumberType::class, [
                'label' => 'Note (sur 5)',
                'required' => true,
                'attr' => [
                    'min' => 0,
                    'max' => 5,
                    'step' => 1,
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Avis::class,
        ]);
    }
}