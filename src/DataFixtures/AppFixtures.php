<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Author;
use App\Entity\Category;
use App\Entity\Book;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        for ($i = 0; $i < 20; $i++) {
            $auther = new Author();
            $auther->setName('Author '.$i);
            $manager->persist($auther);
        }

        for ($i = 0; $i < 20; $i++) {
            $category = new Category();
            $category->setName('Categorie '.$i);
            $manager->persist($category);
        }

        for ($i = 0; $i < 20; $i++) {
            $Book = new Book();
            $Book->setTitle('Book '.$i);
            $Book->setDescription('description '.$i);
            $Book->setAuthor($i);
            $Book->setLanguage('Language '.$i);
            $Book->setCategory($i);
            $manager->persist($Book);
        }

        $manager->flush();
    }
}
