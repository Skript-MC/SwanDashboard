<?php

namespace App\Tests\Controller;

use App\Document\DiscordUser;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProfileControllerTest extends WebTestCase
{

    private KernelBrowser $client;
    private DiscordUser $user;
    private DiscordUser $adminUser;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $dm = static::$container->get('doctrine_mongodb.odm.default_document_manager');
        $this->adminUser = $dm->getRepository(DiscordUser::class)
            ->findOneBy(['userId' => 191495299884122112]);
        $this->user = $dm->getRepository(DiscordUser::class)
            ->findOneBy(['userId' => 191495299884122110]);
    }

    public function testAuthorization(): void
    {
        // The request should return a redirect response to login page.
        $this->client->request('GET', '/profile');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        // Log in the user into the client.
        $this->client->loginUser($this->adminUser);

        // The request should have been authorized and return the page.
        $this->client->request('GET', '/profile');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    // Disabled test.
    public function accountDeleteWhileAdmin(): void
    {
        // Log in the user into the client.
        $this->client->loginUser($this->adminUser);

        $this->client->request('POST', '/profile/delete');
        $crawler = $this->client->followRedirect();
        $alert = $crawler->filter('div')
            ->filter('.alert')
            ->filter('.alert-danger')
            ->text();
        $this->assertEquals('Les administrateurs ne peuvent pas supprimer leurs comptes.', $alert);
    }

    // Disabled test.
    public function accountDelete(): void
    {
        // Log in the user into the client.
        $this->client->loginUser($this->user);

        // Check we are redirected to home because the user has been deleted.
        $this->client->request('POST', '/profile/delete');
        $this->assertEquals('/', $this->client->getResponse()->headers->get('Location'));
    }
}
