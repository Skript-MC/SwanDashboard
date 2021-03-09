<?php

namespace App\Tests\Controller;

use App\Document\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MessageControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private User $staffUser;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $dm = static::$container->get('doctrine_mongodb.odm.default_document_manager');
        $this->staffUser = $dm->getRepository(User::class)
            ->findOneBy(['_id' => 752259261475586139]);
    }

    public function testAuthorization(): void
    {
        // The request should return a redirect response to login page.
        $this->client->request('GET', '/messages');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());

        // Log in the user into the client.
        $this->client->loginUser($this->staffUser);

        // The request should have been authorized and return the page.
        $this->client->request('GET', '/messages');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testCreateInvalidMessage(): void
    {
        // Log in the user into the client.
        $this->client->loginUser($this->staffUser);

        $this->client->request('POST', '/messages/new', [
            'name' => 'This an auto message',
            'type' => 'auto'
        ]);
        $crawler = $this->client->followRedirect();
        $error = $crawler->filter('.alert')
            ->filter('.alert-danger')
            ->text();
        $this->assertEquals('Certains champs sont incorrects, merci de réessayer votre édition.', $error);
    }

    public function testCreateNewMessage(): void
    {
        // Log in the user into the client.
        $this->client->loginUser($this->staffUser);

        $this->client->request('POST', '/messages/new', [
            'name' => 'This an auto message',
            'aliases' => ['autotest', 'testmsg'],
            'type' => 'auto',
            'content' => 'This is the content of my example message. Hello!'
        ]);
        $crawler = $this->client->followRedirect();
        $data = $crawler->filter('tbody')
            ->filter('td');
        $this->assertEquals('This an auto message', $data->eq(0)->text());
        $this->assertEquals('auto', $data->eq(1)->text());
        $this->assertEquals('', $data->eq(2)->text());

        $link = $data->eq(4)->filter('a')->attr('href');
        $this->assertStringStartsWith('/messages/view/', $link);
    }

    public function testApproveMessage(): void
    {
        // Log in the user into the client.
        $this->client->loginUser($this->staffUser);

        $crawler = $this->client->request('GET', '/messages/waiting');
        $link = $crawler->filter('tbody')
            ->filter('td')
            ->filter('a')
            ->attr('href');

        $this->assertStringStartsWith('/messages/view', $link);

        $crawler = $this->client->request('GET', $link);
        $form = $crawler->selectButton('Accepter ces modifications')->form();
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();
        $success = $crawler->filter('.alert')
            ->filter('.alert-success')
            ->text();
        $this->assertEquals('Cette suggestion de modification a été marquée comme validée.', $success);
    }

    public function testViewInvalidEdit(): void
    {
        // Log in the user into the client.
        $this->client->loginUser($this->staffUser);

        $this->client->request('GET', '/messages/view/computerman');
        $crawler = $this->client->followRedirect();
        $error = $crawler->filter('.alert')
            ->filter('.alert-danger')
            ->text();
        $this->assertEquals('Nous n\'avons pas pu trouver de message correspondant à cet identifiant.', $error);
    }

    public function testViewEdit(): void
    {
        // Log in the user into the client.
        $this->client->loginUser($this->staffUser);

        $crawler = $this->client->request('GET', '/messages/auto');
        $data = $crawler->filter('tbody')
            ->filter('td');
        $this->assertEquals('This an auto message', $data->eq(0)->text());
        $this->assertEquals('autotest, testmsg', $data->eq(1)->text());
        $this->assertEquals('This is the content of my example message. Hello!', $data->eq(2)->text());

        $link = $data->eq(3)->filter('a')->attr('href');
        $this->assertStringStartsWith('/messages/', $link);

        $this->client->request('GET', $link);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testEdit(): void
    {
        // Log in the user into the client.
        $this->client->loginUser($this->staffUser);

        $crawler = $this->client->request('GET', '/messages/auto');
        $link = $crawler->filter('tbody')
            ->filter('td')
            ->eq(3)
            ->filter('a')
            ->attr('href');

        // Test with an invalid message ID
        $this->client->request('POST', '/messages/edit', [
            'messageId' => 'allopatrique',
            'name' => 'This an auto message',
            'aliases' => ['autotest', 'testmsg'],
            'type' => 'addonpack',
            'content' => 'This is the new content of my example message. Hello again!'
        ]);
        $crawler = $this->client->followRedirect();
        $error = $crawler->filter('.alert')
            ->filter('.alert-danger')
            ->text();
        $this->assertEquals('Le message avec identifiant allopatrique n\'a pas été trouvé dans nos bases de données.', $error);

        $messageId = str_replace('/messages/', '', $link);

        // Test with an invalid message
        $this->client->request('POST', '/messages/edit', [
            'messageId' => $messageId,
            'name' => 'This an auto message',
            'aliases' => ['autotest', 'testmsg'],
            'type' => 'addonpack'
        ]);
        $crawler = $this->client->followRedirect();
        $error = $crawler->filter('.alert')
            ->filter('.alert-danger')
            ->text();
        $this->assertEquals('Certains champs sont vides, merci de réessayer votre édition.', $error);

        // Test with a valid message
        $this->client->request('POST', '/messages/edit', [
            'messageId' => $messageId,
            'name' => 'This an addonpack message',
            'aliases' => ['addonpacktest', 'testmsg'],
            'type' => 'addonpack',
            'content' => 'This is the new content of my example message. Hello again!'
        ]);
        $crawler = $this->client->followRedirect();
        $success = $crawler->filter('.alert')
            ->filter('.alert-success')
            ->text();
        $this->assertEquals('Votre suggestion de modification a été enregistrée. Elle sera traitée prochainement !', $success);

    }

    public function testApproveEdit(): void
    {
        // Log in the user into the client.
        $this->client->loginUser($this->staffUser);

        $crawler = $this->client->request('GET', '/messages/waiting');
        $link = $crawler->filter('tbody')
            ->filter('td')
            ->filter('a')
            ->attr('href');

        $crawler = $this->client->request('GET', $link);
        $form = $crawler->selectButton('Accepter ces modifications')->form();
        $this->client->submit($form);

        $crawler = $this->client->followRedirect();
        $success = $crawler->filter('.alert')
            ->filter('.alert-success')
            ->text();
        $this->assertEquals('Cette suggestion de modification a été marquée comme validée.', $success);
    }

    public function testNewMessage(): void
    {
        // Log in the user into the client.
        $this->client->loginUser($this->staffUser);

        $this->client->request('GET', '/messages/new');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider provideUrls
     * @param string $url
     */
    public function testViewMessages(string $url): void
    {
        // Log in the user into the client.
        $this->client->loginUser($this->staffUser);

        $this->client->request('GET', '/messages/' . $url);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function provideUrls(): array
    {
        return [
            ['auto'],
            ['error'],
            ['addonpack']
        ];
    }

}
