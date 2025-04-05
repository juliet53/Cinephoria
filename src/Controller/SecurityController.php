<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Si l'utilisateur est déjà connecté, redirige-le vers le tableau de bord approprié en fonction de son rôle
        if ($this->getUser()) {
            // Si l'utilisateur a le rôle "ROLE_ADMIN"
            if ($this->isGranted('ROLE_ADMIN')) {
                return $this->redirectToRoute('admin_home'); // Redirection vers le tableau de bord admin
            } 
            // Si l'utilisateur a le rôle "ROLE_EMPLOYER"
            elseif ($this->isGranted('ROLE_EMPLOYER')) {
                return $this->redirectToRoute('employe_home_index'); // Redirection vers le tableau de bord employé
            }
            // Si aucun rôle défini, redirige vers la page d'accueil (par défaut)
            return $this->redirectToRoute('app_home');
        }

        // Récupérer l'erreur de connexion si il y en a une
        $error = $authenticationUtils->getLastAuthenticationError();
        // Récupérer le dernier nom d'utilisateur entré par l'utilisateur
        $lastUsername = $authenticationUtils->getLastUsername();

        // Affichage du formulaire de connexion
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Cette méthode peut être vide, elle sera interceptée par la clé "logout" dans la configuration de ton firewall.
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
