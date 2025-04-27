<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Attribute\IsGranted;

use App\Entity\Reservation;
use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;

final class ProfilController extends AbstractController
{
    #[Route('/profil', name: 'app_profil')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        // Si aucun utilisateur n'est connecté, rediriger vers la page d'accueil
        if (!$user) {
            return $this->redirectToRoute('app_accueil');
        }

        $email = $user->getEmail();

        $roleLabels = [
            'ROLE_USER' => 'Utilisateur',
            'ROLE_LIBRARIAN' => 'Bibliothécaire',
            'ROLE_ADMIN' => 'Administrateur',
        ];
        
        $userRoles = $user->getRoles();
        
        $priorityRoles = ['ROLE_ADMIN', 'ROLE_LIBRARIAN', 'ROLE_USER'];
        
        $mainRole = null;
        foreach ($priorityRoles as $priorityRole) {
            if (in_array($priorityRole, $userRoles)) {
                $mainRole = $roleLabels[$priorityRole];
                break;
            }
        }
        
        if (!$mainRole && $userRoles) {
            $mainRole = $roleLabels[$userRoles[0]];
        }
        
        // $id = $user->getId();

        $existingReservations = $entityManager->getRepository(Reservation::class)->findBy([
            'user' => $user,
        ]);
        // Passer les réservations à la vue
        foreach ($existingReservations as $reservation) {
            $book = $reservation->getBook();
            $auteur = $book->getAuthor(); // Récupérer l'auteur du livre
            // Maintenant, $auteur contient l'objet Author associé à ce livre
        }

        // Rendre la vue avec les informations de l'utilisateur
        return $this->render('profil/index.html.twig', [
            'controller_name' => 'ProfilController',
            'email' => $email,
            'roles' => $mainRole,
            'reservations' => $existingReservations,
        ]);
    }
}
