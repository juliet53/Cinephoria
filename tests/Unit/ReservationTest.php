<?php

namespace App\Tests\Unit;

use App\Entity\Reservation;
use App\Entity\Seance;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ReservationTest extends KernelTestCase
{
    public function testreservationisvalid(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        
        $user = new User(); 
        $seance = new Seance(); 

        $reservation = New Reservation();
        $reservation->setPrix('10')
                    ->setUserset($user)
                    ->setSeance($seance)
                    ->setSeats(["B3"]);
                    


         $errors = $container->get('validator')->validate($reservation);
         $this->assertCount(0, $errors);

    }
}
