<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookSearchType;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

final class AccueilController extends AbstractController
{
    #[Route('/', name: 'app_accueil')]
    public function index(Request $request, BookRepository $bookRepository): Response
    {
        $books = $bookRepository->findBookLast();
        $category = "";
        $author = "";
        $langue = "";

        foreach ($books as $book) {
            $category = $book->getCategory();
            $author = $book->getAuthor();
            $langue = $book->getLanguage();
        }

        return $this->render('accueil/index.html.twig', [
            'controller_name' => 'AccueilController',
            'books' => $books,
            'category' => $category,
            'author' => $author,
            'langue' => $langue,
        ]);
    }
}
