<?php


namespace App\Controller;

use App\Entity\Avis;
use App\Form\AvisType;
use App\Repository\AvisRepository;
use App\Repository\CinemaRepository;
use App\Repository\FilmRepository;
use App\Repository\GenreRepository;
use App\Repository\SeanceRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    public function show(int $id, FilmRepository $filmRepository, EntityManagerInterface $entityManager, Request $request, AvisRepository $avisRepository): Response
    {
        // Récupérer le film par ID
        $film = $filmRepository->find($id);

        // Redirection si le film n'existe pas
        if (!$film) {
            throw $this->createNotFoundException('Le film demandé n\'existe pas.');
        }

        // Récupérer les trois derniers avis validés, triés par ID descendant
        $avisValides = $avisRepository->findBy(
            ['film' => $film, 'valide' => true],
            ['id' => 'DESC'],
            3 // Limiter à 3 avis
        );

        // Créer le formulaire uniquement si l'utilisateur est connecté
        $form = null;
        if ($this->isGranted('ROLE_USER')) {
            $avis = new Avis();
            $avis->setFilm($film);
            $avis->setUser($this->getUser()); // Associer l'utilisateur connecté
            $form = $this->createForm(AvisType::class, $avis);
            $form->handleRequest($request);

            // Traiter la soumission du formulaire
            if ($form->isSubmitted() && $form->isValid()) {
                $avis->setCommentaire(strip_tags($avis->getCommentaire())); // Nettoyer le commentaire
                $avis->setValide(false); // Par défaut, l'avis n'est pas validé

                $entityManager->persist($avis);
                $entityManager->flush();

                // Rediriger pour éviter la resoumission du formulaire
                return $this->redirectToRoute('app_film_show', ['id' => $film->getId()]);
            }
        }

        // Rendre le template avec le film, les avis et le formulaire
        return $this->render('films/show.html.twig', [
            'film' => $film,
            'avisValides' => $avisValides,
            'form' => $form ? $form->createView() : null,
            'userEmail' => $this->isGranted('ROLE_USER') ? $this->getUser()->getUserIdentifier() : null,
        ]);
    }
    
}
