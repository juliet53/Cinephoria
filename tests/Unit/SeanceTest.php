<?php

namespace App\tests\Unit;

use App\Entity\Seance;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SeanceTest extends KernelTestCase
{
    public function testSeanceisvalid(): void
    {
         self::bootKernel();
         $container = static::getContainer();

         $seance = new Seance();

         $dateDebut = new \DateTime('2025-01-01 14:00:00');
         $dateFin = new \DateTime('2025-01-01 16:00:00');
 
         $seance->setDateHeureDebut($dateDebut);
         $seance->setDateHeureFin($dateFin);
         $seance->setQualite('HD');
         $seance->setPlaceDisponible(100);
         $seance->setPrix(12.5);

         $errors = $container->get('validator')->validate($seance);
         $this->assertCount(0, $errors);  

    }
    public function testReserverPlacesSuccess(): void
    {
        $seance = new Seance();
        $seance->setPlaceDisponible(50);

        $result = $seance->reserverPlaces(20);

        $this->assertTrue($result);
        $this->assertEquals(30, $seance->getPlaceDisponible());
    }

    public function testReserverPlacesFailure(): void
    {
        $seance = new Seance();
        $seance->setPlaceDisponible(10);

        $result = $seance->reserverPlaces(20);

        $this->assertFalse($result);
        $this->assertEquals(10, $seance->getPlaceDisponible());
    }
}
