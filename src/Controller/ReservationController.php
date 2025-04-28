<?php
namespace App\Controller;

use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\SeanceRepository;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReservationController extends AbstractController
{
    #[Route('/reservation', name: 'app_reservation')]
    public function reserver(
        Request $request,
        SeanceRepository $seanceRepository,
        ReservationRepository $reservationRepository,
        EntityManagerInterface $entityManager,
        Security $security
    ): Response {
        $seances = $seanceRepository->findAll();
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);

        // Récupérer les places réservées pour chaque séance
        $reservedSeatsBySeance = [];
        foreach ($seances as $seance) {
            $reservations = $reservationRepository->findBy(['seance' => $seance]);
            $reservedSeats = [];
            foreach ($reservations as $res) {
                $seats = $res->getSeats();
                if (is_array($seats)) {
                    $reservedSeats = array_merge($reservedSeats, $seats);
                }
            }
            $reservedSeatsBySeance[$seance->getId()] = array_unique($reservedSeats);
        }

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            // Débogage : inspecter les données soumises et les erreurs
            dump($request->request->all());
            dump($form->getErrors(true, true));
            dump($form->get('seance')->getData());
            dump($form->get('seats')->getData());
            dump($form->get('numPersons')->getData());
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $seance = $form->get('seance')->getData(); // Récupère l'entité Seance
            $selectedSeatsJson = $form->get('seats')->getData();
            $numPersons = $form->get('numPersons')->getData();

            // Valider la séance
            if (!$seance) {
                $this->addFlash('error', 'Séance non trouvée.');
                return $this->redirectToRoute('app_reservation');
            }

            // Valider les sièges
            $selectedSeats = json_decode($selectedSeatsJson, true);
            if (!is_array($selectedSeats) || empty($selectedSeats)) {
                $this->addFlash('error', 'Aucune place sélectionnée ou format invalide.');
                return $this->redirectToRoute('app_reservation');
            }

            $placeReserve = count($selectedSeats);
            if ($placeReserve !== $numPersons) {
                $this->addFlash('error', "Le nombre de places sélectionnées ($placeReserve) ne correspond pas au nombre de personnes ($numPersons).");
                return $this->redirectToRoute('app_reservation');
            }

            if ($seance->getPlaceDisponible() < $placeReserve) {
                $this->addFlash('error', 'Pas assez de places disponibles.');
                return $this->redirectToRoute('app_reservation');
            }

            // Vérifier si les places sélectionnées sont déjà réservées
            $reservedSeats = $reservedSeatsBySeance[$seance->getId()] ?? [];
            foreach ($selectedSeats as $seat) {
                if (in_array($seat, $reservedSeats)) {
                    $this->addFlash('error', "La place $seat est déjà réservée.");
                    return $this->redirectToRoute('app_reservation');
                }
            }

            // Calculer le prix
            $prixUnitaire = $seance->getPrix();
            if ($prixUnitaire === null) {
                $this->addFlash('error', 'Le prix de la séance n\'est pas défini.');
                return $this->redirectToRoute('app_reservation');
            }
            $prixTotal = $prixUnitaire * $placeReserve;

            // Définir les données de la réservation
            $reservation->setSeance($seance);
            $reservation->setSeats($selectedSeats);
            $reservation->setPlaceReserve($placeReserve);
            $reservation->setPrix($prixTotal);
            $reservation->setUser($security->getUser());

            // Mettre à jour les places disponibles
            $seance->reserverPlaces($placeReserve);

            // Persister les données
            $entityManager->persist($reservation);
            $entityManager->persist($seance);
            $entityManager->flush();

            $this->addFlash('success', 'Réservation réussie !');
            return $this->redirectToRoute('app_user_reservations');
        }

        return $this->render('reservation/index.html.twig', [
            'form' => $form->createView(),
            'seances' => $seances,
            'reservedSeatsBySeance' => $reservedSeatsBySeance,
        ]);
    }
}