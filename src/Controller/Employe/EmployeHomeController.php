<?php

namespace App\Controller\Employe;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

#[Route('/employe', name: 'employe_home')]
class EmployeHomeController extends AbstractController
{
    #[Route('', name: '_index')]
    public function index(): Response
    {
        return $this->render('employe/home/index.html.twig');
    }
}
