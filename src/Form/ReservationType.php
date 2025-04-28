<?php
namespace App\Form;

use App\Entity\Reservation;
use App\Entity\Seance;
use App\Repository\SeanceRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Form\CallbackTransformer;

class ReservationType extends AbstractType
{
    private $seanceRepository;

    public function __construct(SeanceRepository $seanceRepository)
    {
        $this->seanceRepository = $seanceRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('seance', HiddenType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez sélectionner une séance.']),
                ],
            ])
            ->add('seats', HiddenType::class, [
                'mapped' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez sélectionner des places.']),
                ],
            ])
            ->add('numPersons', IntegerType::class, [
                'mapped' => false,
                'required' => true,
                'attr' => ['min' => 1],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer le nombre de personnes.']),
                    new Positive(['message' => 'Le nombre de personnes doit être positif.']),
                ],
            ]);

        // Ajouter un DataTransformer pour convertir l'ID de séance en entité Seance
        $builder->get('seance')->addModelTransformer(new CallbackTransformer(
            function ($seance) {
                // Transforme l'entité Seance en ID (pour le formulaire)
                return $seance ? (string) $seance->getId() : null;
            },
            function ($seanceId) {
                // Transforme l'ID en entité Seance
                if (!$seanceId) {
                    return null;
                }
                $seance = $this->seanceRepository->find($seanceId);
                if (!$seance) {
                    throw new \Symfony\Component\Form\Exception\TransformationFailedException('Séance non trouvée.');
                }
                return $seance;
            }
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Reservation::class,
        ]);
    }
}