<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $placeReserve = null;

    #[ORM\Column]
    private ?float $prix = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    private ?Seance $seance = null;

    #[ORM\Column(type: 'json', nullable: true)] 
    private ?array $seats = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlaceReserve(): ?int
    {
        return $this->placeReserve;
    }

    public function PlaceReserve(int $placeReserve): static
    {
        $this->placeReserve = $placeReserve;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUserset(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getSeance(): ?Seance
    {
        return $this->seance;
    }

    public function setSeance(?Seance $seance): static
    {
        $this->seance = $seance;

        return $this;
    }

    public function getSeats(): ?array
    {
        return $this->seats;
    }

    public function setSeats(?array $seats): static
    {
        $this->seats = $seats;
        return $this;
    }
}
