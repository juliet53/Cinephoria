<?php

namespace App\Controller\Admin;

use App\Entity\Cinema;
use App\Form\CinemaType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/cinema')]
class AdminCinemaController extends AbstractController
{
    #[Route('/', name: 'admin_cinema_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $cinemas = $entityManager->getRepository(Cinema::class)->findAll();

        return $this->render('admin/cinema/index.html.twig', [
            'cinemas' => $cinemas,
        ]);
    }

    #[Route('/new', name: 'admin_cinema_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $cinema = new Cinema();
        $form = $this->createForm(CinemaType::class, $cinema);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($cinema);
            $entityManager->flush();

            return $this->redirectToRoute('admin_cinema_index');
        }

        return $this->render('admin/cinema/new.html.twig', [
            'cinema' => $cinema,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_cinema_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Cinema $cinema, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CinemaType::class, $cinema);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('admin_cinema_index');
        }

        return $this->render('admin/cinema/edit.html.twig', [
            'cinema' => $cinema,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'admin_cinema_delete', methods: ['POST'])]
    public function delete(Request $request, Cinema $cinema, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cinema->getId(), $request->request->get('_token'))) {
            $entityManager->remove($cinema);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_cinema_index');
    }
}
