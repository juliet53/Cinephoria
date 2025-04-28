<?php
namespace App\Controller;

use App\Entity\Reservation;
use App\Repository\ReservationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/user/reservations', name: 'app_user_reservations')]
    public function reservations(ReservationRepository $reservationRepository, Security $security): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $security->getUser();
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour voir vos réservations.');
            return $this->redirectToRoute('app_login');
        }

        // Récupérer les réservations de l'utilisateur
        $reservations = $reservationRepository->findBy(['user' => $user], ['id' => 'DESC']);

        return $this->render('user/reservations.html.twig', [
            'reservations' => $reservations,
        ]);
    }

    #[Route('/user/reservations/{id}', name: 'app_user_reservation_detail', requirements: ['id' => '\d+'])]
    public function reservationDetail(int $id, ReservationRepository $reservationRepository, Security $security): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $security->getUser();
        if (!$user) {
            $this->addFlash('error', 'Vous devez être connecté pour voir les détails de la réservation.');
            return $this->redirectToRoute('app_login');
        }

        // Récupérer la réservation
        $reservation = $reservationRepository->find($id);
        if (!$reservation) {
            $this->addFlash('error', 'Réservation non trouvée.');
            return $this->redirectToRoute('app_user_reservations');
        }


        return $this->render('user/reservation_detail.html.twig', [
            'reservation' => $reservation,
        ]);
    }
}