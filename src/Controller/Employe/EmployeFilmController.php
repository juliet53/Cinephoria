<?php

namespace App\Controller\Employe;

use App\Entity\Film;
use App\Form\FilmType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/employe/films', name: 'employe_films_')]
class EmployeFilmController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $films = $entityManager->getRepository(Film::class)->findAll();

        return $this->render('admin/films/index.html.twig', [
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
            // Gérer l'image uploadée (facultatif si Vich gère tout seul, mais on garde pour cohérence)
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $film->setImageFile($imageFile);
            }

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
            // Gérer l'image uploadée
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $film->setImageFile($imageFile);
            }

            // Mettre à jour les genres
            $film->getGenres()->clear();
            foreach ($form->get('genres')->getData() as $genre) {
                $film->addGenre($genre);
            }

            $entityManager->flush();

            return $this->redirectToRoute('employe_films_index');
        }

        return $this->render('admin/films/edit.html.twig', [
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
