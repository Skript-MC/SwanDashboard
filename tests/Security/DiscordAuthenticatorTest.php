<?php

namespace App\Tests\Security;

use App\Document\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DiscordAuthenticatorTest extends WebTestCase
{

    private KernelBrowser $client;
    private User $user;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $dm = static::$container->get('doctrine_mongodb.odm.default_document_manager');
        $this->user = $dm->getRepository(User::class)
            ->findOneBy(['_id' => 752259261475586139]);
    }

    function testAuthenticationFailure(): void
    {
        $this->client->request('GET', '/login/authenticate?code=sO1yJuxWKbLuFxmPsmPMg');
        $this->assertEquals(307, $this->client->getResponse()->getStatusCode());
        $crawler = $this->client->followRedirect();
        $error = $crawler->filter('div')
            ->filter('.text-center')
            ->filter('p')
            ->text();
        $this->assertEquals('Nous n\'avons pas pu accéder à votre compte afin de vous authentifier. Si le problème persiste, n\'hésitez pas à nous contacter sur Discord.', $error);
    }

    function testUnauthorizedException(): void
    {
        // Log in the user into the client.
        $this->client->loginUser($this->user);

        $crawler = $this->client->request('GET', '/users');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
        $error = $crawler->filter('div')
            ->filter('.text-center')
            ->filter('p')
            ->text();
        $this->assertEquals('Vous n\'avez pas accès à cette ressource.', $error);
    }

}
