<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use DateTime;

#[MongoDB\Document(collection: "ReservationFilm")]
class MongoReservation
{
    #[MongoDB\Id]
    protected ?string $id = null;

    #[MongoDB\Field(type: "string")]
    protected ?string $filmId = null;

    #[MongoDB\Field(type: "date")]
    protected ?DateTime $reservationDate = null;

    #[MongoDB\Field(type: "int")]
    protected ?int $numberOfTickets = null;

    #[MongoDB\Field(type: "float")]
    protected ?float $totalPrice = null;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setFilmId(string $filmId): self
    {
        $this->filmId = $filmId;
        return $this;
    }

    public function getFilmId(): ?string
    {
        return $this->filmId;
    }

    public function setReservationDate(DateTime $reservationDate): self
    {
        $this->reservationDate = $reservationDate;
        return $this;
    }

    public function getReservationDate(): ?DateTime
    {
        return $this->reservationDate;
    }

    public function setNumberOfTickets(int $numberOfTickets): self
    {
        $this->numberOfTickets = $numberOfTickets;
        return $this;
    }

    public function getNumberOfTickets(): ?int
    {
        return $this->numberOfTickets;
    }

    public function setTotalPrice(float $totalPrice): self
    {
        $this->totalPrice = $totalPrice;
        return $this;
    }

    public function getTotalPrice(): ?float
    {
        return $this->totalPrice;
    }
}
