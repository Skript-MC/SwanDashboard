<?php

namespace App\Tests\Controller;

use App\Document\DiscordUser;
use App\Document\SwanChannel;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class LogControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private DocumentManager $dm;
    private DiscordUser $adminUser;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->dm = static::getContainer()->get(DocumentManager::class);
        $this->adminUser = $this->dm->getRepository(DiscordUser::class)
            ->findOneBy(['userId' => 191495299884122112]);
    }

    public function testAuthorization(): void
    {
        // The request should return a redirect response to login page.
        $this->client->request('GET', '/logs');
        $this->assertEquals(Response::HTTP_TEMPORARY_REDIRECT, $this->client->getResponse()->getStatusCode());

        // Log in the user into the client.
        $this->client->loginUser($this->adminUser);

        // The request should have been authorized and return the page.
        $this->client->request('GET', '/logs');
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    public function testEditionMessage(): void
    {
        // Log in the user into the client.
        $this->client->loginUser($this->adminUser);

        $crawler = $this->client->request('GET', '/logs/channel/780877192753184868');
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

        $crawler = $this->client->request('GET', '/logs/channel/780877192753184868');
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

        $this->client->request('GET', '/logs/message/928677192753619275');
        $this->assertEquals(REsponse::HTTP_SEE_OTHER, $this->client->getResponse()->getStatusCode());
    }

    function testSearchForm(): void
    {
        // Log in the user into the client.
        $this->client->loginUser($this->adminUser);

        $crawler = $this->client->request('GET', '/logs/search');
        $form = $crawler->selectButton('Rechercher')->form([
            'userId' => 191495299884122112
        ]);
        $crawler = $this->client->submit($form);
        // 2 <tr> for table construction and 2 <tr> for table contents
        $this->assertEquals(4, $crawler->filter('tr')->count());
    }

    function testSwitchChannel(): void
    {
        $channel = $this->dm->getRepository(SwanChannel::class)->findOneBy(['channelId' => '780877192753184868']);
        $this->assertNotNull($channel);

        // Log in the user into the client.
        $this->client->loginUser($this->adminUser);

        $this->client->request('POST', '/logs/api/channels', [
            'channelId' => 780877192753184868,
            'checked' => false
        ]);
        $this->assertEquals('{"status":"OK"}', $this->client->getResponse()->getContent());

        $this->client->request('POST', '/logs/api/channels', [
            'channelId' => 780877192753184868,
            'checked' => true
        ]);
        $this->assertEquals('{"status":"OK"}', $this->client->getResponse()->getContent());

        $channel = $this->dm->getRepository(SwanChannel::class)->findOneBy(['channelId' => '780877192753184868']);
        $this->assertNotNull($channel);
    }

}
