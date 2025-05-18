<?php

namespace App\Controller\Admin;

use App\Document\MongoReservation;
use App\Repository\FilmRepository;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @IsGranted("ROLE_ADMIN")
 */
class AdminstatController extends AbstractController
{
    #[Route('/admin/reservations/stats', name: 'admin_reservation_stats')]
    public function reservationStats(DocumentManager $documentManager, FilmRepository $filmRepository): Response
    {
        $sevenDaysAgo = new DateTime('-7 days');

        $reservations = $documentManager->createQueryBuilder(MongoReservation::class)
            ->field('reservationDate')->gte($sevenDaysAgo)
            ->getQuery()
            ->execute();

        $totalTickets = 0;
        $totalRevenue = 0;
        $reservationsByFilm = [];

        /** @var MongoReservation $reservation */
        foreach ($reservations as $reservation) {
            $totalTickets += $reservation->getNumberOfTickets();
            $totalRevenue += $reservation->getTotalPrice();
            $filmId = $reservation->getFilmId();
            if (!isset($reservationsByFilm[$filmId])) {
                $reservationsByFilm[$filmId] = [
                    'tickets' => 0,
                    'revenue' => 0,
                    'title' => $filmId // Par défaut, utilise l'ID si le titre n'est pas trouvé
                ];
                // Récupérer le titre du film
                $film = $filmRepository->find($filmId);
                if ($film) {
                    $reservationsByFilm[$filmId]['title'] = $film->getTitle(); // Assumes Film has getTitle()
                }
            }
            $reservationsByFilm[$filmId]['tickets'] += $reservation->getNumberOfTickets();
            $reservationsByFilm[$filmId]['revenue'] += $reservation->getTotalPrice();
        }

        return $this->render('admin/reservationstat/reservation_stats.html.twig', [
            'stats' => [
                'total_reservations' => $reservations->count(),
                'total_tickets' => $totalTickets,
                'total_revenue' => $totalRevenue,
                'reservations_by_film' => $reservationsByFilm,
            ],
        ]);
    }
}