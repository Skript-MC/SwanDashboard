<?php

namespace App\Tests\Controller;

use App\Document\DiscordUser;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SanctionControllerTest extends WebTestCase
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
        $this->client->request('GET', '/sanctions');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        // Log in the user into the client.
        $this->client->loginUser($this->adminUser);

        // The request should have been authorized and return the page.
        $this->client->request('GET', '/sanctions');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testSearch(): void
    {
        // Log in the user into the client.
        $this->client->loginUser($this->adminUser);

        $crawler = $this->client->request('GET', '/sanctions');
        $form = $crawler->selectButton('Rechercher')->form();

        $form['memberId']->setValue('191495299884122112');
        $form['moderatorId']->setValue('752259261475586139');
        $form['sanctionType']->setValue('ban');

        $crawler = $this->client->submit($form);
        $data = $crawler->filter('tbody')
            ->filter('td');
        $this->assertEquals('Bannissement', $data->eq(0)->text());
        $this->assertEquals('191495299884122112', $data->eq(1)->text());
        $this->assertEquals('752259261475586139', $data->eq(2)->text());
        $this->assertEquals('I wanted to!', $data->eq(3)->text());
        $this->assertEquals('23/01/2021 à 19:10', $data->eq(4)->text());
        $this->assertEquals('23/01/2021 à 19:12', $data->eq(5)->text());
        $this->assertEquals('En cours', $data->eq(6)->text());
    }

}
