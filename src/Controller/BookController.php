<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\Request;

use App\Repository\BookRepository;
use App\Repository\CategoryRepository;
use App\Repository\CommentRepository;
use App\Repository\AuthorRepository;
use App\Form\CommentType;
use App\Entity\Comment;

use Doctrine\ORM\EntityManagerInterface;


#[Route('/Book')]
final class BookController extends AbstractController
{
    #[Route('/', name: 'app_book')]
    public function index(): Response
    {

        return $this->render('accueil/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }

    #[Route('/Detail/{slug}', name: 'app_book_view')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function view(BookRepository $bookRepository, CommentRepository $commentRepository, string $slug, EntityManagerInterface $entityManager, Request $request): Response
    {
        $book = $bookRepository->findOneBy(['slug' => $slug]);
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);

        if (!$book) {
            throw $this->createNotFoundException('Livre non trouvé.');
        }

        $auteur = $book->getAuthor();
        $category = $book->getCategory();
        $language = $book->getLanguage();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setStatus('pending');
            $comment->setUser($this->getUser());
            $comment->setBook($book);
            $entityManager->persist($comment);
            $entityManager->flush();

            $this->addFlash('success', 'Votre commentaire a été enregistré.');

            return $this->redirectToRoute('app_book_view', ['slug' => $book->getSlug()]);
        }

        $comments = $commentRepository->findBy([
            'book' => $book,
            'status' => 'approved'
        ]);

        return $this->render('book/detail.html.twig', [
            'controller_name' => 'BookController',
            'ouvrages' => $book,
            'author' => $auteur,
            'category' => $category,
            'language' => $language,
            'form' => $form,
            'comments' => $comments
        ]);
    }
}
