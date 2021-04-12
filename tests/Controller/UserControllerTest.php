<?php

namespace App\Tests\Controller;

use App\Document\DiscordUser;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private DiscordUser $adminUser;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $dm = static::$container->get('doctrine_mongodb.odm.default_document_manager');
        $this->adminUser = $dm->getRepository(DiscordUser::class)
            ->findOneBy(['userId' => 191495299884122112]);
    }

    public function testAuthorization(): void
    {
        // The request should return a redirect response to login page.
        $this->client->request('GET', '/users');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        // Log in the user into the client.
        $this->client->loginUser($this->adminUser);

        // The request should have been authorized and return the page.
        $this->client->request('GET', '/users');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testViewUser(): void
    {
        // Log in the user into the client.
        $this->client->loginUser($this->adminUser);

        $crawler = $this->client->request('GET', '/users');
        $data = $crawler->filter('tbody')
            ->filter('td');
        $this->assertEquals('Romitou#9685', $data->eq(0)->text());
        $this->assertEquals('ROLE_ADMIN, ROLE_USER', $data->eq(1)->text());
        $this->assertEquals('191495299884122112', $data->eq(3)->text());

        $link = $data->eq(4)->filter('a')->attr('href');
        $this->assertEquals('/users/191495299884122112', $link);

        $crawler = $this->client->request('GET', $link);
        $form = $crawler->selectButton('Sauvegarder les modifications')->form();
        $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $success = $crawler->filter('div')
            ->filter('.alert')
            ->filter('.alert-success')
            ->text();
        $this->assertEquals('Vos modifications ont bien été enregistrées.', $success);
    }

    public function testValidForm(): void
    {
        // Log in the staff user into the client.
        $this->client->loginUser($this->adminUser);

        $crawler = $this->client->request('GET', '/users/191495299884122112');
        $form = $crawler->selectButton('Sauvegarder les modifications')->form();
        $form['discordAvatar']->setValue('');
        $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $error = $crawler->filter('div')
            ->filter('.alert')
            ->filter('.alert-danger')
            ->text();
        $this->assertEquals('Certains champs sont vides ou incorrects.', $error);

    }

}
