<?php

namespace App\Controller\Employe;

use App\Entity\Film;
use App\Form\FilmType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/employe/films', name: 'employe_films_')]

class EmployeFilmController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $films = $entityManager->getRepository(Film::class)->findAll();

        return $this->render('admin/films/index.html.twig', [ // Réutilisation des vues admin
            'films' => $films,
        ]);
    }

    #[Route('/new', name: 'new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $film = new Film();
        $form = $this->createForm(FilmType::class, $film);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($form->get('genres')->getData() as $genre) {
                $film->addGenre($genre);
            }

            $entityManager->persist($film);
            $entityManager->flush();

            return $this->redirectToRoute('employe_films_index');
        }

        return $this->render('admin/films/new.html.twig', [ 
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'edit')]
    public function edit(Film $film, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(FilmType::class, $film);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $film->getGenres()->clear();
            foreach ($form->get('genres')->getData() as $genre) {
                $film->addGenre($genre);
            }

            $entityManager->flush();

            return $this->redirectToRoute('employe_films_index');
        }

        return $this->render('admin/films/edit.html.twig', [ // Réutilisation de la vue admin
            'form' => $form->createView(),
            'film' => $film,
        ]);
    }

    #[Route('/{id}/delete', name: 'delete')]
    public function delete(Film $film): Response
    {
        // Bloquer la suppression pour les employés
        $this->addFlash('error', 'Vous n’avez pas l’autorisation de supprimer un film.');
        return $this->redirectToRoute('employe_films_index');
    }
}
