<?php

namespace App\Tests\Unit;

use App\Entity\Film;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FilmTest extends KernelTestCase
{
    public function testSomething(): void
    {
         self::bootKernel();
         $container = static::getContainer();

         $film = New Film();

         $film->setTitle("Inception");
         $film->setDescription("Un thriller de science-fiction");
         $film->setCrush(true);
         $film->setDirector("Christopher Nolan");
         $film->setCreatedAt(new \DateTimeImmutable("2024-05-01"));
         $film->setUpdatedAt(new \DateTimeImmutable("2024-05-02"));
         

        $errors = $container->get('validator')->validate($film);
        $this->assertCount(0, $errors);
    }


        
}

