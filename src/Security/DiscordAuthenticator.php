<?php

namespace App\Security;

use App\Document\User;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\MongoDBException;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class DiscordAuthenticator extends SocialAuthenticator
{
    private $clientRegistry;
    private $dm;
    private $router;

    public function __construct(ClientRegistry $clientRegistry, DocumentManager $dm, RouterInterface $router)
    {
        $this->clientRegistry = $clientRegistry;
        $this->dm = $dm;
        $this->router = $router;
    }

    public function supports(Request $request)
    {
        // continue ONLY if the current ROUTE matches the check ROUTE
        return $request->attributes->get('_route') === 'connect_discord_check';
    }

    public function getCredentials(Request $request)
    {
        return $this->fetchAccessToken($this->getDiscordClient());
    }

    /**
     * @param $credentials
     * @param UserProviderInterface $userProvider
     * @return User|UserInterface
     * @throws MongoDBException
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $discordUser = $this
            ->getDiscordClient()
            ->fetchUserFromToken($credentials);

        $existingUser = $this->dm
            ->getRepository(User::class)
            ->findOneBy(['discordId' => $discordUser->getId()]);

        if ($existingUser) return $existingUser;

        $user = new User();
        $user->setDiscordId($discordUser->getId());
        $user->setUsername($discordUser->getUsername() . '#' . $discordUser->getDiscriminator());
        $user->setAvatarUrl('https://cdn.discordapp.com/avatars/' . $discordUser->getId() . '/' . $discordUser->getAvatarHash() . '.png');
        $this->dm->persist($user);
        $this->dm->flush();

        return $user;
    }

    /**
     * @return OAuth2ClientInterface
     */
    private function getDiscordClient()
    {
        return $this->clientRegistry->getClient('discord');
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        return new RedirectResponse($this->router->generate('home'));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());
        return new Response($message, Response::HTTP_FORBIDDEN);
    }

    /**
     * Called when authentication is needed, but it's not sent.
     * This redirects to the 'login'.
     * @param Request $request
     * @param AuthenticationException|null $authException
     * @return RedirectResponse
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse(
            '/login',
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }
}
