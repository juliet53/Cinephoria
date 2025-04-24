<?php

namespace App\Controller\Admin;

use App\Entity\Film;
use App\Form\FilmType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/films', name: 'admin_films_')]
class FilmController extends AbstractController
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
            $imageFile = $form->get('imageFile')->getData();

            // Si un fichier est téléchargé
            if ($imageFile) {
                // Récupérer les informations du fichier téléchargé
                $film->setImageName($imageFile->getClientOriginalName());
                $film->setImageSize($imageFile->getSize());

                // On peut aussi définir l'image avant de persister le film
                $film->setImageFile($imageFile);
            }

            // Persist le film dans la base de données
            $entityManager->persist($film);
            $entityManager->flush();

            return $this->redirectToRoute('admin_films_index');
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
                // Récupérer les informations du fichier téléchargé
                $film->setImageName($imageFile->getClientOriginalName());
                $film->setImageSize($imageFile->getSize());

                // On peut aussi définir l'image avant de persister le film
                $film->setImageFile($imageFile);
            }

            // Mettre à jour les genres
            $film->getGenres()->clear(); // Supprimer les anciens genres
            foreach ($form->get('genres')->getData() as $genre) {
                $film->addGenre($genre);
            }

            $entityManager->flush();

            return $this->redirectToRoute('admin_films_index');
        }

        return $this->render('admin/films/edit.html.twig', [
            'form' => $form->createView(),
            'film' => $film,
        ]);
    }

    #[Route('/{id}/delete', name: 'delete')]
    public function delete(Film $film, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($film);
        $entityManager->flush();

        return $this->redirectToRoute('admin_films_index');
    }
}
