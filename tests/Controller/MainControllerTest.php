<?php

namespace App\Tests\Controller;

use App\Document\User;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MainControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private DocumentManager $dm;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->dm = static::$container->get('doctrine_mongodb.odm.default_document_manager');
    }

    public function testAuthorization(): void
    {
        // Make the request as anonymous user.
        $crawler = $this->client->request('GET', '/');
        $pageTitle = $crawler->filter('html')
            ->filter('title')
            ->text();
        $this->assertEquals('Swan - Accueil', $pageTitle);

        // Log in the user into the client.
        $user = $this->dm->getRepository(User::class)
            ->findOneBy(['_id' => 191495299884122112]);
        $this->client->loginUser($user);

        // The request should have been authorized and return the dashboard page.
        $crawler = $this->client->request('GET', '/');
        $pageTitle = $crawler->filter('html')
            ->filter('title')
            ->text();
        $this->assertEquals('Swan - Tableau de bord', $pageTitle);
    }

    /**
     * @dataProvider provideRedirectPathUrls
     * @param $url
     */
    public function testRedirectPath($url): void
    {
        $this->client->request('GET', $url);
        $crawler = $this->client->followRedirect();
        $redirect = $crawler
            ->filter('.text-gray-400')
            ->filter('.text-center')
            ->filter('.small')
            ->first()
            ->text();
        $this->assertEquals($url, str_replace('Après votre connexion, vous serez redirigé vers ', '', $redirect));
    }

    public function provideRedirectPathUrls(): array
    {
        return [
            ['/profile'],
            ['/messages'],
            ['/history']
        ];
    }

    public function testDiscordOauth(): void
    {
        $this->client->request('GET', '/login');
        $this->assertStringStartsWith('https://discord.com/api/v8/oauth2/authorize', $this->client->getResponse()->headers->get('Location'));
    }

    public function testErrorPage(): void
    {
        $crawler = $this->client->request('GET', '/error');
        $errorMessage = $crawler->filter('div')
            ->filter('.text-center')
            ->children('p')
            ->text();
        $this->assertEquals('Nous ne savons pas exactement ce qu\'il s\'est passé. Si le problème persiste, n\'hésitez pas à nous contacter sur Discord.', $errorMessage);
    }

}
