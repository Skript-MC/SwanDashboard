<?php

namespace App\Tests\Security;

use App\Document\DiscordUser;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class DiscordAuthenticatorTest extends WebTestCase
{

    private KernelBrowser $client;
    private DiscordUser $user;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $dm = static::getContainer()->get(DocumentManager::class);
        $this->user = $dm->getRepository(DiscordUser::class)
            ->findOneBy(['userId' => 752259261475586139]);
    }

    function testAuthenticationFailure(): void
    {
        $this->client->request('GET', '/login/authenticate?code=sO1yJuxWKbLuFxmPsmPMg');
        $this->assertEquals(Response::HTTP_TEMPORARY_REDIRECT, $this->client->getResponse()->getStatusCode());
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
        $this->assertEquals(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
        $error = $crawler->filter('div')
            ->filter('.text-center')
            ->filter('p')
            ->text();
        $this->assertEquals('Vous n\'avez pas accès à cette ressource.', $error);
    }

}
