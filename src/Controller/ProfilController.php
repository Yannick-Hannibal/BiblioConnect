<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Security;

final class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'app_profil')]
    public function index(Security $security): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $security->getUser();

        // Si aucun utilisateur n'est connecté, rediriger vers la page d'accueil
        if (!$user) {
            return $this->redirectToRoute('app_accueil');
        }

        // Récupérer les informations de l'utilisateur
        $email = $user->getEmail();
        $roles = $user->getRoles(); // Si tu veux récupérer les rôles
        $id = $user->getId(); // Si tu veux récupérer l'ID

        // Rendre la vue avec les informations de l'utilisateur
        return $this->render('profil/index.html.twig', [
            'controller_name' => 'ProfilController',
            'email' => $email,
            'roles' => $roles, // Si tu souhaites afficher les rôles
        ]);
    }
}
