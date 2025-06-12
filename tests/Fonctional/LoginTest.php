<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\User;
use Doctrine\ORM\Tools\SchemaTool;

class AdminAccessTest extends WebTestCase
{
    private $client;
    private $entityManager;
    private $passwordHasher;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $container = $this->client->getContainer();

        $this->entityManager = $container->get('doctrine')->getManager();
        $this->passwordHasher = $container->get('security.password_hasher');

        // Réinitialise la base de données
        $schemaTool = new SchemaTool($this->entityManager);
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);

        // Crée un utilisateur admin
        $admin = new User();
        $admin->setEmail('admin@test.com');
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'adminpass'));
        $admin->setRoles(['ROLE_ADMIN']);

        $this->entityManager->persist($admin);
        $this->entityManager->flush();
    }

    public function testAdminCanAccessAdminDashboard(): void
    {
        $admin = $this->entityManager->getRepository(User::class)->findOneBy(['email' => 'admin@test.com']);

        // Simule la connexion de l'admin
        $this->client->loginUser($admin);

        // Accède à la route admin (change la route si nécessaire)
        $this->client->request('GET', '/admin');

        // Vérifie que la réponse est 200 OK
        $this->assertResponseIsSuccessful();

    }

    public function testAnonymousCannotAccessAdminDashboard(): void
    {
        // Un utilisateur non connecté tente d'accéder
        $this->client->request('GET', '/admin');

        // Vérifie que l'utilisateur est redirigé vers /login (ou autre)
        $this->assertResponseRedirects('/login');
    }
}
