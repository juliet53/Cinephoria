<?php

namespace App\Tests\Fonctional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ContactTest extends WebTestCase
{
    public function testContactform(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/contact');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Formulaire de contact');

        // Recup le form
        $submitButton = $crawler->selectButton('Envoyer');
        $form = $submitButton->form();

        $form["contact[email]"] = "test@example.com";
        $form["contact[description]"] = "Mon super test";
        $form["contact[content]"] = "Ceci est super test";

        //Soumettre le form
        $client->submit($form);

        //Vérif status HTTP
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        //Vérif l'envoi du mail
        $this->assertEmailCount(1);

        $client->followRedirect();

        //Verif texte de succes
        
        $this->assertSelectorTextContains('.alert.alert-success', 'Votre message a été envoyé avec succès !');

    }
}
