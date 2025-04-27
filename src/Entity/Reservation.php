<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;
use App\Entity\Book;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    public const STATUS_EN_ATTENTE = 'en_attente';
    public const STATUS_CONFIRMEE = 'confirmee';
    public const STATUS_ANNULEE = 'annulee';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $reservedAt = null;

    #[ORM\Column(length: 20)]
    private ?string $status = self::STATUS_EN_ATTENTE; // statut par défaut

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Book $book = null;

    #[ORM\Column(type: 'integer')]
    private ?int $quantity = 1; // Quantité réservée, 1 par défaut

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReservedAt(): ?\DateTimeInterface
    {
        return $this->reservedAt;
    }

    public function setReservedAt(\DateTimeInterface $reservedAt): self
    {
        $this->reservedAt = $reservedAt;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getBook(): ?Book
    {
        return $this->book;
    }

    public function setBook(?Book $book): self
    {
        $this->book = $book;
        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;
        return $this;
    }

    // Méthode pour vérifier si la quantité demandée est disponible dans le stock
    public function isStockAvailable(): bool
    {
        return $this->book->getStock() >= $this->quantity;
    }

    // Méthode pour mettre à jour le stock après réservation
    public function updateStock(): void
    {
        $this->book->setStock($this->book->getStock() - $this->quantity);
    }
}
