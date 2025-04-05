<?php

namespace App\Controller\Employe;

use App\Entity\Seance;
use App\Form\SeanceType;
use App\Repository\SeanceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/employe/seance')]

class EmployeSeanceController extends AbstractController
{
    #[Route('/', name: 'employe_seance_index', methods: ['GET'])]
    public function index(SeanceRepository $seanceRepository): Response
    {
        return $this->render('admin/seances/index.html.twig', [
            'seances' => $seanceRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'employe_seance_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $seance = new Seance();
        $form = $this->createForm(SeanceType::class, $seance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($seance);
            $entityManager->flush();

            return $this->redirectToRoute('employe_seance_index');
        }

        return $this->render('admin/seances/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'employe_seance_edit', methods: ['GET', 'POST'])]
    public function edit(Seance $seance, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(SeanceType::class, $seance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('employe_seance_index');
        }

        return $this->render('admin/seances/edit.html.twig', [
            'form' => $form->createView(),
            'seance' => $seance,
        ]);
    }

    #[Route('/{id}/delete', name: 'employe_seance_delete', methods: ['POST'])]
    public function delete(): Response
    {
        // Bloquer la suppression pour les employés
        $this->addFlash('error', 'Vous n’avez pas l’autorisation de supprimer une séance.');
        return $this->redirectToRoute('employe_seance_index');
    }
}
