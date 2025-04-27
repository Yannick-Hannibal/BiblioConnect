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
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


#[Route('/Admin')]
final class GestionCatalogueAdminController extends AbstractController
{
    #[Route('/gestion/', name: 'app_gestion_catalogue_admin')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_LIBRARIAN')) {
            throw $this->createAccessDeniedException('Accès refusé');
        }
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
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_LIBRARIAN')) {
            throw $this->createAccessDeniedException('Accès refusé');
        }

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
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_LIBRARIAN')) {
            throw $this->createAccessDeniedException('Accès refusé');
        }

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
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_LIBRARIAN')) {
            throw $this->createAccessDeniedException('Accès refusé');
        }

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
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_LIBRARIAN')) {
            throw $this->createAccessDeniedException('Accès refusé');
        }

        if ($this->isCsrfTokenValid('delete' . $book->getId(), $request->request->get('_token'))) {
            $entityManager->remove($book);
            $entityManager->flush();
            return $this->redirectToRoute('app_gestion_catalogue_admin');
        }
    }

    #[Route('/suivit/', name: 'app_gestion_catalogue_admin_suivit')]
    public function suivit(Request $request, EntityManagerInterface $em): Response
    {
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_LIBRARIAN')) {
            throw $this->createAccessDeniedException('Accès refusé');
        }

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
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_LIBRARIAN')) {
            throw $this->createAccessDeniedException('Accès refusé');
        }

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
    #[IsGranted('ROLE_ADMIN')]
    public function deletereservation(Reservation $reservation, BookRepository $bookRepository, EntityManagerInterface $entityManager, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $reservation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($reservation);
            $entityManager->flush();
            return $this->redirectToRoute('app_gestion_catalogue_admin_suivit');
        }
    }

    #[Route('/commentaires/', name: 'app_gestion_catalogue_admin_commentaire')]
    #[IsGranted('ROLE_ADMIN')]
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
    #[IsGranted('ROLE_ADMIN')]
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
    // #[IsGranted('ROLE_ADMIN')]
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
    // #[IsGranted('ROLE_ADMIN')]
    public function editUser(
        Request $request,
        User $user,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ) {
        // if (!$this->isGranted('ROLE_ADMIN')) {
        //     throw $this->createAccessDeniedException('Accès refusé');
        // }

        // Récupération de l'utilisateur
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $data = $form->getData();

            if ($form->get('email')->isSubmitted() && $form->get('email')->isValid()) {
                $user->setEmail($data->getEmail());
            }

            $plainPassword = $form->get('plainPassword')->getData();
            if (!empty($plainPassword)) {
                $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));
            }

            $user->setRoles($form->get('roles')->getData());

            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur modifié avec succès.');

            return $this->redirectToRoute('admin_users');
        }

        return $this->render('gestion_catalogue_admin/users/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/user/new', name: 'admin_user_new')]
    // #[IsGranted('ROLE_ADMIN')]
    public function newUser(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager
    ) {
        // if (!$this->isGranted('ROLE_ADMIN')) {
        //     throw $this->createAccessDeniedException('Accès refusé');
        // }

        // Récupération de l'utilisateur
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plaintextPassword = $form->get('plainPassword')->getData();
            if (!empty($plaintextPassword)) {
                $hashedPassword = $passwordHasher->hashPassword($user, $plaintextPassword);
                $user->setPassword($hashedPassword);
            }


            // Gestion des rôles : si aucun rôle sélectionné, on met ROLE_USER
            if (empty($user->getRoles())) {
                $user->setRoles(['ROLE_USER']);
            }

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Compte créé avec succès !');
            return $this->redirectToRoute('admin_users');
        }

        return $this->render('gestion_catalogue_admin/users/new.html.twig', [
            'form' => $form->createView(),
        ]);

    }

    #[Route('/dashboard', name: 'app_dashboard')]
    public function dashboard()
    {
        if (!$this->isGranted('ROLE_ADMIN') && !$this->isGranted('ROLE_LIBRARIAN')) {
            throw $this->createAccessDeniedException('Accès refusé');
        }

        // Récupération de l'utilisateur

        return $this->render('gestion_catalogue_admin/dashboard.html.twig');

    }
}
