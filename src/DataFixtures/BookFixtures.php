<?php

namespace App\DataFixtures;

use App\Entity\Book;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class BookFixtures extends Fixture
{
    public function load(ObjectManager $manager): void  // Ajout du type de retour : void
    {
        // Crée un livre
        $book1 = new Book();
        $book1->setTitle('Le Livre de Symfony')
            ->setAuthor('Jean Dupont')
            ->setDescription('Un guide complet pour Symfony.')
            ->setLanguage('Français')
            ->setCategory('Programmation')
            ->setAvailable(true);
        
        $manager->persist($book1);

        // Crée un autre livre
        $book2 = new Book();
        $book2->setTitle('Le Guide PHP')
            ->setAuthor('Marie Dupuis')
            ->setDescription('Apprenez PHP de manière interactive.')
            ->setLanguage('Français')
            ->setCategory('Programmation')
            ->setAvailable(true);
        
        $manager->persist($book2);

        // Enregistre les livres dans la base de données
        $manager->flush();
    }
}
