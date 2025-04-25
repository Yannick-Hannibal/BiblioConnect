<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            // Vérifier les rôles de l'utilisateur
            if (in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
                return $this->redirectToRoute('admin_dashboard');  // Remplace 'admin_dashboard' par la route de ton tableau de bord admin
            } elseif (in_array('ROLE_USER', $this->getUser()->getRoles())) {
                return $this->redirectToRoute('app_profil');  // Remplace 'user_dashboard' par la route de ton tableau de bord utilisateur
            }

            // Rediriger vers une page par défaut si aucun rôle spécifique n'est trouvé
            return $this->redirectToRoute('app_accueil');
        }

        // obtenir erreur et dernier nom d'utilisateur saisi
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();
        

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Symfony s'occupe de cette route automatiquement
        throw new \LogicException('Cette méthode peut rester vide, elle est interceptée par le pare-feu.');
    }
}
