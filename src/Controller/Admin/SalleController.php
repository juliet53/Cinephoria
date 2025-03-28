<?php

namespace App\Controller\Admin;

use App\Entity\Salle;
use App\Form\SalleType;
use App\Repository\SalleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/salles', name: 'admin_salles_')]
class SalleController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(SalleRepository $salleRepository): Response
    {
        $salles = $salleRepository->findAll();
        return $this->render('admin/salles/index.html.twig', [
            'salles' => $salles,
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $salle = new Salle();
        $form = $this->createForm(SalleType::class, $salle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($salle);
            $entityManager->flush();
            return $this->redirectToRoute('admin_salles_index');
        }

        return $this->render('admin/salles/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Salle $salle, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SalleType::class, $salle);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('admin_salles_index');
        }

        return $this->render('admin/salles/edit.html.twig', [
            'form' => $form->createView(),
            'salle' => $salle,
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['POST'])]
    public function delete(Salle $salle, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($salle);
        $entityManager->flush();
        return $this->redirectToRoute('admin_salles_index');
    }
}
