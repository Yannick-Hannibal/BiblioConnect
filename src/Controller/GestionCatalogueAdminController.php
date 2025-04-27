<?php

namespace App\Controller;

use App\Form\ReservationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Book;
use App\Entity\Reservation;
use App\Entity\Comment;
use App\Entity\User;
use App\Form\BookSearchType;
use App\Repository\BookRepository;
use App\Repository\ReservationRepository;
use App\Form\BookType;
use App\Form\UserType;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


#[Route('/Admin')]
// #[IsGranted('ROLE_ADMIN')]
final class GestionCatalogueAdminController extends AbstractController
{
    #[Route('/gestion/', name: 'app_gestion_catalogue_admin')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $existinglistBook = $em->getRepository(Book::class)->findAll();

        // Passer les réservations à la vue
        foreach ($existinglistBook as $listBook) {
            $category = $listBook->getCategory();
            $auteur = $listBook->getAuthor();
        }

        return $this->render('gestion_catalogue_admin/index.html.twig', [
            'controller_name' => 'GestionCatalogueAdminController',
            'catalogues' => $existinglistBook
        ]);
    }

    #[Route('/detail/{slug}', name: 'app_gestion_catalogue_admin_detail')]
    public function view(BookRepository $bookRepository, string $slug, EntityManagerInterface $entityManager, Request $request): Response
    {
        $book = $bookRepository->findOneBy(['slug' => $slug]);

        if (!$book) {
            throw $this->createNotFoundException('Livre non trouvé.');
        }

        $auteur = $book->getAuthor();
        $category = $book->getCategory();
        $language = $book->getLanguage();

        return $this->render('gestion_catalogue_admin/view.html.twig', [
            'controller_name' => 'GestionCatalogueAdminController',
            'ouvrages' => $book,
            'author' => $auteur,
            'category' => $category,
            'language' => $language
        ]);
    }

    #[Route('/new/', name: 'app_gestion_catalogue_admin_new')]
    public function new(EntityManagerInterface $entityManager, Request $request): Response
    {

        $book = new Book();
        $form = $this->createForm(BookType::class, $book);

        $form->handleRequest($request);
        // permet de vérifier si le formulaire est submit et valid
        if ($form->isSubmitted() && $form->isValid()) {

            //Permet de persisté les données
            $entityManager->persist($book);
            //Permet d'envoyer les données dans la BDD
            $entityManager->flush();

            //permet de retourne sur la route designer par son name
            return $this->redirectToRoute('app_gestion_catalogue_admin');
        }
        return $this->render('gestion_catalogue_admin/new.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/edit/{slug}', name: 'app_gestion_catalogue_admin_edit')]
    public function edit(BookRepository $bookRepository, string $slug, EntityManagerInterface $entityManager, Request $request): Response
    {
        $book = $bookRepository->findOneBy(['slug' => $slug]);

        if (!$book) {
            throw $this->createNotFoundException('Livre non trouvé.');
        }

        $form = $this->createForm(BookType::class, $book);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush(); // Pas besoin de persist() car l'objet est déjà connu
            $this->addFlash('success', 'Livre mis à jour avec succès.');

            return $this->redirectToRoute('app_gestion_catalogue_admin_detail', ['slug' => $book->getSlug()]); // ou vers un détail par exemple
        }

        return $this->render('gestion_catalogue_admin/edit.html.twig', [
            'controller_name' => 'GestionCatalogueAdminController',
            'form' => $form->createView(),
            'ouvrages' => $book,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_gestion_catalogue_admin_delete')]
    public function delete(Book $book, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $book->getId(), $request->request->get('_token'))) {
            $entityManager->remove($book);
            $entityManager->flush();
            return $this->redirectToRoute('app_gestion_catalogue_admin');
        }
    }

    #[Route('/suivit/', name: 'app_gestion_catalogue_admin_suivit')]
    public function suivit(Request $request, EntityManagerInterface $em): Response
    {
        $existinglistreservation = $em->getRepository(Reservation::class)->findAll();

        // Passer les réservations à la vue
        foreach ($existinglistreservation as $listreservation) {
            $Book = $listreservation->getBook();
            $User = $listreservation->getUser();
        }

        return $this->render('gestion_catalogue_admin/suivit.html.twig', [
            'controller_name' => 'GestionCatalogueAdminController',
            'reservations' => $existinglistreservation
        ]);
    }

    #[Route('/editreservation/{slug}', name: 'app_gestion_catalogue_admin_editreservation')]
    public function editreservation(ReservationRepository $reservationRepository, BookRepository $bookRepository, string $slug, EntityManagerInterface $entityManager, Request $request, EntityManagerInterface $em): Response
    {
        $book = $bookRepository->findOneBy(['slug' => $slug]);

        if (!$book) {
            throw $this->createNotFoundException('Livre non trouvé.');
        }

        $reservation = $reservationRepository->findOneBy(['book' => $book]);


        if (!$reservation) {
            throw $this->createNotFoundException('Réservation non trouvé.');
        }

        $form = $this->createForm(ReservationType::class, $reservation);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush(); // Pas besoin de persist() car l'objet est déjà connu
            $this->addFlash('success', 'Livre mis à jour avec succès.');

            return $this->redirectToRoute('app_gestion_catalogue_admin_suivit', ['slug' => $book->getSlug()]); // ou vers un détail par exemple
        }

        return $this->render('gestion_catalogue_admin/editreservation.html.twig', [
            'controller_name' => 'GestionCatalogueAdminController',
            'form' => $form->createView(),
            'reservation' => $reservation,
            'User' => $reservation->getUser(),
            'Book' => $book
        ]);
    }

    #[Route('/deletereservation/{id}', name: 'app_gestion_catalogue_admin_deletereservation')]
    public function deletereservation(Reservation $reservation, BookRepository $bookRepository, EntityManagerInterface $entityManager, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $reservation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($reservation);
            $entityManager->flush();
            return $this->redirectToRoute('app_gestion_catalogue_admin_suivit');
        }
    }

    #[Route('/commentaires/', name: 'app_gestion_catalogue_admin_commentaire')]
    public function commentaires(Request $request, EntityManagerInterface $em): Response
    {
        $existinglistBook = $em->getRepository(Comment::class)->findBy([
            'status' => 'pending'
        ]);

        // Passer les réservations à la vue
        foreach ($existinglistBook as $listBook) {
            $User = $listBook->getUser();
            $Book = $listBook->getBook();
        }

        return $this->render('gestion_catalogue_admin/moderercommentaires.html.twig', [
            'controller_name' => 'GestionCatalogueAdminController',
            'comments' => $existinglistBook
        ]);
    }

    #[Route('/commentaires/approve/{id}', name: 'admin_comment_approve')]
    public function approve(Comment $comment, EntityManagerInterface $entityManager): Response
    {
        $comment->setStatus('approved');
        $entityManager->flush();

        return $this->redirectToRoute('app_gestion_catalogue_admin_commentaire');
    }

    #[Route('/commentaires/reject/{id}', name: 'admin_comment_reject')]
    public function reject(Comment $comment, EntityManagerInterface $entityManager): Response
    {
        $comment->setStatus('rejected');
        $entityManager->flush();

        return $this->redirectToRoute('app_gestion_catalogue_admin_commentaire');
    }

    #[Route('/users', name: 'admin_users')]
    public function users(EntityManagerInterface $em)
    {
        // Vérifier si l'utilisateur a le rôle ADMIN
        // if (!$this->isGranted('ROLE_ADMIN')) {
        //     throw $this->createAccessDeniedException('Accès refusé');
        // }

        $users = $em->getRepository(User::class)->findAll();

        return $this->render('gestion_catalogue_admin/users/users.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/admin/user/{id}/edit', name: 'admin_user_edit')]
    public function editUser(Request $request, User $user, EntityManagerInterface $em, UserPasswordEncoderInterface $passwordEncoder, int $id)
    {
        // if (!$this->isGranted('ROLE_ADMIN')) {
        //     throw $this->createAccessDeniedException('Accès refusé');
        // }

        // Récupération de l'utilisateur
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);

        // Création du formulaire
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();

            // Si un mot de passe a été fourni, on le hache et on l'attribue à l'utilisateur
            if ($plainPassword) {
                $user->setPassword($passwordEncoder->encodePassword($user, $plainPassword));
            }

            // Mise à jour des rôles
            $user->setRoles($form->get('roles')->getData());

            // Sauvegarde des changements dans la base de données
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            // Message flash pour indiquer la réussite de l'opération
            $this->addFlash('success', 'Utilisateur modifié avec succès.');

            // Redirection ou retour à une page de confirmation
            return $this->redirectToRoute('admin_user_list');
        }


        return $this->render('gestion_catalogue_admin/users/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
