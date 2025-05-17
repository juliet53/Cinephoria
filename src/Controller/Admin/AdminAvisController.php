<?php

namespace App\Controller\Admin;

use App\Entity\Avis;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Security\Http\Attribute\IsGranted as AttributeIsGranted;

#[Route('/admin/avis')]
#[AttributeIsGranted('ROLE_ADMIN')]
final class AdminAvisController extends AbstractController
{
        #[Route('/', name: 'app_admin_avis')]
        public function index(EntityManagerInterface $entityManager): Response
        {
                $avis = $entityManager->getRepository(Avis::class)->findBy([], ['id' => 'DESC']);

                return $this->render('admin/admin_avis/index.html.twig', [
                        'avis' => $avis,
                ]);
        }

        #[Route('/avis/valider/{id}', name: 'app_admin_avis_valider')]
        public function valider(Avis $avis, EntityManagerInterface $entityManager): Response
        {
                $avis->setValide(true);
                $entityManager->persist($avis);
                $entityManager->flush();

                $this->addFlash('success', 'L\'avis a été validé avec succès.');

                return $this->redirectToRoute('app_admin_avis');
        }

        #[Route('/avis/invalider/{id}', name: 'app_admin_avis_invalider')]
        public function invalider(Avis $avis, EntityManagerInterface $entityManager): Response
        {
                $avis->setValide(false);
                $entityManager->persist($avis);
                $entityManager->flush();

                $this->addFlash('success', 'L\'avis a été invalidé avec succès.');

                return $this->redirectToRoute('app_admin_avis');
        }
}
