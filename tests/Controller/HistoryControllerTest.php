<?php


namespace App\Tests\Controller;


use App\Document\SharedConfig;
use App\Document\User;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HistoryControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private DocumentManager $dm;
    private User $adminUser;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->dm = static::$container->get('doctrine_mongodb.odm.default_document_manager');
        $this->adminUser = $this->dm->getRepository(User::class)
            ->findOneBy(['discordId' => 191495299884122112]);
    }

    public function testAuthorization(): void
    {
        // The request should return a redirect response to login page.
        $this->client->request('GET', '/history');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        // Log in the user into the client.
        $this->client->loginUser($this->adminUser);

        // The request should have been authorized and return the page.
        $this->client->request('GET', '/history');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testEditionMessage(): void
    {
        // Log in the user into the client.
        $this->client->loginUser($this->adminUser);

        $crawler = $this->client->request('GET', '/history/channel/780877192753184868');
        $edition = $crawler->filter('div')
            ->filter('.card')
            ->filter('tr')
            ->filter('td');
        $this->assertEquals('Romitou#9685', $edition->first()->text());
        $this->assertEquals('Hi!', $edition->eq(1)->text());

        $crawler = $this->client->click($edition->eq(2)->filter('a')->link());
        $this->assertEquals('Romitou#9685', $crawler->filter('#discordUser')->eq(0)->text());
        $this->assertEquals('(191495299884122112)', $crawler->filter('#discordUser')->eq(1)->text());
        $this->assertEquals('780877192753184868', $crawler->filter('#discordChannel')->text());
        $this->assertEquals('Hi!', $crawler->filter('#oldMessage')->text());
        $this->assertEquals('Hello!', $crawler->filter('#messageEditions')->eq(0)->text());
        $this->assertEquals('Hey!', $crawler->filter('#messageEditions')->eq(1)->text());
        $this->assertEquals('How are you?', $crawler->filter('#newMessage')->text());
    }

    public function testDeletedMessage(): void
    {
        // Log in the user into the client.
        $this->client->loginUser($this->adminUser);

        $crawler = $this->client->request('GET', '/history/channel/780877192753184868');
        $edition = $crawler->filter('div')
            ->filter('.card')
            ->filter('tr')
            ->filter('td');
        $this->assertEquals('Romitou#9685', $edition->eq(3)->text());
        $this->assertEquals('Hello!', $edition->eq(4)->text());

        $crawler = $this->client->click($edition->eq(5)->filter('a')->link());
        $this->assertEquals('Romitou#9685', $crawler->filter('#discordUser')->eq(0)->text());
        $this->assertEquals('(191495299884122112)', $crawler->filter('#discordUser')->eq(1)->text());
        $this->assertEquals('780877192753184868', $crawler->filter('#discordChannel')->text());
        $this->assertEquals('Hello!', $crawler->filter('#oldMessage')->text());
        $this->assertEquals('Feet', $crawler->filter('#messageEditions')->eq(0)->text());
        $this->assertEquals('Pineapple', $crawler->filter('#messageEditions')->eq(1)->text());
    }

    function testRedirectInvalidMessage(): void
    {
        // Log in the user into the client.
        $this->client->loginUser($this->adminUser);

        $this->client->request('GET', '/history/message/928677192753619275');
        $this->assertEquals(303, $this->client->getResponse()->getStatusCode());
    }

    function testSearchForm(): void
    {
        // Log in the user into the client.
        $this->client->loginUser($this->adminUser);

        $crawler = $this->client->request('GET', '/history/search');
        $form = $crawler->selectButton('Rechercher')->form([
            'form[userId]' => 191495299884122112
        ]);
        $crawler = $this->client->submit($form);
        // 2 <tr> for table construction and 2 <tr> for table contents
        $this->assertEquals(4, $crawler->filter('tr')->count());
    }

    function testSwitchChannel(): void
    {
        $channels = $this->dm->getRepository(SharedConfig::class)->findOneBy(['name' => 'archived-channels']);
        $this->assertEquals([780877192753184868], $channels->getValue());

        // Log in the user into the client.
        $this->client->loginUser($this->adminUser);

        $this->client->request('POST', '/history/api/channels', [
            'channelId' => 780877192753184868,
            'checked' => false
        ]);
        $this->assertEquals('{"status":"OK"}', $this->client->getResponse()->getContent());

        $this->client->request('POST', '/history/api/channels', [
            'channelId' => 780877192753184868,
            'checked' => true
        ]);
        $this->assertEquals('{"status":"OK"}', $this->client->getResponse()->getContent());

        $channels = $this->dm->getRepository(SharedConfig::class)->findOneBy(['name' => 'archived-channels']);
        $this->assertEquals([780877192753184868], $channels->getValue());
    }

}
