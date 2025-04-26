<?php


namespace App\Controller;

use App\Repository\FilmRepository;
use App\Repository\GenreRepository;
use App\Repository\SeanceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class FilmsController extends AbstractController
{
    #[Route('/films', name: 'films_index')]
    public function index(
        FilmRepository $filmRepository,
        GenreRepository $genreRepository,
        SeanceRepository $seanceRepository
    ) {
        $films = $filmRepository->findAll();
        $genres = $genreRepository->findAll();
        $seances = $seanceRepository->findAll(); 

        return $this->render('films/index.html.twig', [
            'films' => $films,
            'genres' => $genres,
            'seances' => $seances, 
        ]);
    }
}
