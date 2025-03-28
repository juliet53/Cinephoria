<?php

namespace App\Controller\Admin;

use App\Entity\Genre;
use App\Form\GenreType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/genres', name: 'admin_genre_')]
class AdminGenreController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $genres = $entityManager->getRepository(Genre::class)->findAll();

        return $this->render('admin/genre/index.html.twig', [
            'genres' => $genres,
        ]);
    }

    #[Route('/new', name: 'new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $genre = new Genre();
        $form = $this->createForm(GenreType::class, $genre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($genre);
            $entityManager->flush();

            $this->addFlash('success', 'Le genre a été créé avec succès.');

            return $this->redirectToRoute('admin_genre_index');
        }

        return $this->render('admin/genre/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'edit')]
    public function edit(Genre $genre, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(GenreType::class, $genre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Le genre a été modifié avec succès.');

            return $this->redirectToRoute('admin_genre_index');
        }

        return $this->render('admin/genre/edit.html.twig', [
            'form' => $form->createView(),
            'genre' => $genre,
        ]);
    }

    #[Route('/{id}/delete', name: 'delete')]
    public function delete(Genre $genre, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($genre);
        $entityManager->flush();

        $this->addFlash('success', 'Le genre a été supprimé avec succès.');

        return $this->redirectToRoute('admin_genre_index');
    }
}
