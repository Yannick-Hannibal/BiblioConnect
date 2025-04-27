<?php

namespace App\Controller;

use App\Entity\Reservation;
use App\Entity\User;
use App\Entity\Book;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Repository\BookRepository;

final class ReservationController extends AbstractController
{
    #[Route('/reservation/{slug}', name: 'app_reservation')]
    public function index(Request $request, string $slug, EntityManagerInterface $entityManager): Response
    {
        $book = $entityManager->getRepository(Book::class)->findOneBy(['slug' => $slug]);
        $user = $this->getUser();

        if (!$book || !$user) {
            throw $this->createNotFoundException('Livre ou utilisateur non trouvé.');
        }

        $existingReservation = $entityManager->getRepository(Reservation::class)->findOneBy([
            'book' => $book,
            'user' => $user,
        ]);

        if ($existingReservation) {
            $this->addFlash('warning', 'Vous avez déjà réservé ce livre.');
            return $this->redirectToRoute('app_book_view', ['slug' => $book->getSlug()]);
        }
        $quantity = (int) $request->request->get('quantity', 1);

        $reservation = new Reservation();
        $reservation->setBook($book);
        $reservation->setUser($user);
        $reservation->setReservedAt(new \DateTime());
        $reservation->setQuantity($quantity);
        $reservation->setStatus(Reservation::STATUS_EN_ATTENTE);

        $entityManager->persist($reservation);
        $entityManager->flush();

        $this->addFlash('success', 'Réservation effectuée avec succès !');

        return $this->redirectToRoute('app_book_view', ['slug' => $book->getSlug()]);
    }
}
