<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Author;
use App\Entity\Category;
use App\Entity\Book;
use App\Entity\Language;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        // Persister les auteurs
        for ($i = 0; $i < 20; $i++) {
            $author = new Author();
            $author->setName('Author ' . $i);
            $manager->persist($author);
        }

        // Persister les catégories
        for ($i = 0; $i < 20; $i++) {
            $category = new Category();
            $category->setName('Categorie ' . $i);
            $manager->persist($category);
        }

        $languages = ['Français', 'Anglais', 'Espagnol', 'Allemand', 'Italien', 'Japonais', 'Chinois', 'Arabe', 'Portugais', 'Russe'];

        foreach ($languages as $languageName) {
            $language = new Language();
            $language->setName($languageName);
            $manager->persist($language);
        }

        // Appeler flush pour enregistrer auteurs et catégories
        $manager->flush();

        // Récupérer les auteurs et catégories maintenant qu'ils sont enregistrés
        $authors = $manager->getRepository(Author::class)->findAll();
        $categories = $manager->getRepository(Category::class)->findAll();
        $languages = $manager->getRepository(Language::class)->findAll();

        // Créer les livres
        for ($i = 0; $i < 20; $i++) {
            $book = new Book();
            $book->setTitle('Book ' . $i);
            $book->setDescription('description ' . $i);
            $book->setSlug('book-' . $i);

            $randomStock = rand(1, 100);
            $book->setStock($randomStock);

            // Choisir un auteur et une catégorie au hasard
            $author = $authors[array_rand($authors)];
            $category = $categories[array_rand($categories)];
            $language = $languages[array_rand($languages)];

            $book->setAuthor($author);
            $book->setCategory($category);
            $book->setLanguage($language);
            $manager->persist($book);
        }

        // Enregistrer les livres
        $manager->flush();

    }
}
