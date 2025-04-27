<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookSearchType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

final class AccueilController extends AbstractController
{
    #[Route('/', name: 'app_accueil')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        // Créer le formulaire de recherche
        $form = $this->createForm(BookSearchType::class);
        $form->handleRequest($request);

        // Récupérer les données du formulaire
        $title = $form->get('title')->getData();
        $author = $form->get('author')->getData();
        $category = $form->get('category')->getData();

        // Construction de la requête
        $queryBuilder = $em->getRepository(Book::class)->createQueryBuilder('b');

        if ($title) {
            $queryBuilder->andWhere('b.title LIKE :title')
                ->setParameter('title', '%' . $title . '%');
        }

        if ($author) {
            $queryBuilder->andWhere('b.author = :author')
                ->setParameter('author', $author);
        }

        if ($category) {
            $queryBuilder->andWhere('b.category = :category')
                ->setParameter('category', $category);
        }

        // Exécuter la requête
        $books = $queryBuilder->getQuery()->getResult();

        return $this->render('accueil/index.html.twig', [
            'controller_name' => 'AccueilController',
            'form' => $form->createView(),
            'books' => $books,
        ]);
    }
}
