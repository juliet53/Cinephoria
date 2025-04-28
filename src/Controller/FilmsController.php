<?php


namespace App\Controller;

use App\Repository\CinemaRepository;
use App\Repository\FilmRepository;
use App\Repository\GenreRepository;
use App\Repository\SeanceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;


class FilmsController extends AbstractController
{
    #[Route('/films', name: 'films_index')]
    public function index(
        FilmRepository $filmRepository,
        GenreRepository $genreRepository,
        SeanceRepository $seanceRepository,
        CinemaRepository $cinemaRepository
    ) {
        $films = $filmRepository->findBy([], ['id' => 'DESC']);
        $genres = $genreRepository->findAll();
        $seances = $seanceRepository->findAll(); 
        $cinemas = $cinemaRepository->findAll();

        return $this->render('films/index.html.twig', [
            'films' => $films,
            'genres' => $genres,
            'seances' => $seances,
            'cinemas' => $cinemas,
        ]);
    }
    #[Route('/films/{id}', name: 'app_film_show')]
    public function show(int $id, FilmRepository $filmRepository): Response
    {
        // Récupère le film par son ID
        $film = $filmRepository->find($id);

        // Si le film n'existe pas, redirige vers la page d'accueil ou affiche une erreur
        if (!$film) {
            throw $this->createNotFoundException('Le film demandé n\'existe pas.');
        }

        // Render le template avec les informations du film
        return $this->render('films/show.html.twig', [
            'film' => $film,
        ]);
    }

    
}
