<?php

namespace App\Security;

use App\Document\User;
use App\Service\DiscordService;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\MongoDBException;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use League\OAuth2\Client\Token\AccessToken;
use Romitou\OAuth2\Client\DiscordRessourceOwner;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class DiscordAuthenticator extends SocialAuthenticator
{
    use TargetPathTrait;

    private ClientRegistry $clientRegistry;
    private DocumentManager $dm;
    private RouterInterface $router;
    private DiscordService $discordService;

    public function __construct(ClientRegistry $clientRegistry, DocumentManager $dm, RouterInterface $router, DiscordService $discordService)
    {
        $this->clientRegistry = $clientRegistry;
        $this->dm = $dm;
        $this->router = $router;
        $this->discordService = $discordService;
    }

    public function supports(Request $request): bool
    {
        // continue ONLY if the current ROUTE matches the check ROUTE
        return $request->attributes->get('_route') === 'connect_discord_check';
    }

    public function getCredentials(Request $request): AccessToken
    {
        return $this->fetchAccessToken($this->getDiscordClient());
    }

    /**
     * @return OAuth2ClientInterface
     */
    private function getDiscordClient(): OAuth2ClientInterface
    {
        return $this->clientRegistry->getClient('discord');
    }

    /**
     * @param $credentials
     * @param UserProviderInterface $userProvider
     * @return User
     * @throws MongoDBException
     * @codeCoverageIgnore The Discord OAuth authentication cannot be unit tested.
     */
    public function getUser($credentials, UserProviderInterface $userProvider): User
    {
        /** @var DiscordRessourceOwner $discordUser */
        $discordUser = $this
            ->getDiscordClient()
            ->fetchUserFromToken($credentials);

        /* @var $existingUser User */
        $existingUser = $this->dm
            ->getRepository(User::class)
            ->findOneBy(['_id' => $discordUser->getId()]);

        // Merge existing user and new user, this will update the existing user if it is found
        $user = $existingUser ? $existingUser : new User();
        $discordMember = $this->discordService->getMember($discordUser->getId());

        // If we have an existing user, don't refresh these data.
        if (!$existingUser) {
            $user->setId($discordUser->getId());
            $user->setRoles(['ROLE_USER']);
        }

        $user->setUsername($discordUser->getCompleteUsername());
        $user->setAvatarUrl($discordUser->getAvatarUrl() ?? 'https://cdn.discordapp.com/embed/avatars/0.png');
        $user->setDiscordRoles($discordMember?->roles ?? []); // The discordMember can be null if the API is unavailable, so give no roles
        $user->setHasMFA($discordUser->hasMfaEnabled());

        $this->dm->persist($user);
        $this->dm->flush();

        return $user;
    }

    /**
     * @param Request $request
     * @param TokenInterface $token
     * @param string $providerKey
     * @return RedirectResponse
     * @codeCoverageIgnore The Discord OAuth authentication cannot be unit tested.
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): RedirectResponse
    {

        return new RedirectResponse($this->getTargetPath($request->getSession(), 'discord') ?? $this->router->generate('home'));
    }

    /**
     * @param Request $request
     * @param AuthenticationException $exception
     * @return Response
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        return new RedirectResponse(
            $this->router->generate('error', ['message' => 'Nous n\'avons pas pu accéder à votre compte afin de vous authentifier.']),
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }

    /**
     * Called when authentication is needed.
     * @param Request $request
     * @param AuthenticationException|null $authException
     * @return RedirectResponse
     */
    public function start(Request $request, AuthenticationException $authException = null): RedirectResponse
    {
        $this->saveTargetPath($request->getSession(), 'discord', $request->getRequestUri());
        return new RedirectResponse($this->router->generate('home'));
    }
}
