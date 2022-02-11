<?php

namespace App\Security;

use App\Document\DiscordUser;
use App\Service\DiscordService;
use Doctrine\ODM\MongoDB\DocumentManager;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use KnpU\OAuth2ClientBundle\Security\Authenticator\OAuth2Authenticator;
use Romitou\OAuth2\Client\DiscordRessourceOwner;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class DiscordAuthenticator extends OAuth2Authenticator implements AuthenticationEntryPointInterface
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

    private function getDiscordClient(): OAuth2ClientInterface
    {
        return $this
            ->clientRegistry
            ->getClient('discord');
    }

    public function supports(Request $request): bool
    {
        // continue ONLY if the current ROUTE matches the check ROUTE
        return $request->attributes->get('_route') === 'connect_discord_check';
    }

    public function authenticate(Request $request): Passport
    {
        $client = $this->getDiscordClient();
        $accessToken = $this->fetchAccessToken($client);

        return new SelfValidatingPassport(
            new UserBadge($accessToken->getToken(), function() use ($accessToken, $client, $request) {
                /** @var DiscordRessourceOwner $discordUser */
                $discordUser = $this
                    ->getDiscordClient()
                    ->fetchUserFromToken($accessToken);

                /* @var $existingUser DiscordUser */
                $existingUser = $this->dm
                    ->getRepository(DiscordUser::class)
                    ->findOneBy(['userId' => ''.$discordUser->getId()]);

                // Merge existing user and new user, this will update the existing user if it is found
                $user = $existingUser ? $existingUser : new DiscordUser();
                $discordMember = $this->discordService->getMember($discordUser->getId());
                if (!$discordMember)
                    return null;

                // If we have an existing user, don't refresh these data.
                if (!$existingUser)
                    $user->setUserId(''.$discordUser->getId());

                $user->setUsername($discordUser->getCompleteUsername());
                $user->setAvatarUrl($discordUser->getAvatarUrl() ?? 'https://cdn.discordapp.com/embed/avatars/0.png');
                $user->setDiscordRoles($discordMember?->roles ?? []); // The discordMember can be null if the API is unavailable, so give no roles
                $user->setHasMFA($discordUser->hasMfaEnabled());

                $this->dm->persist($user);
                $this->dm->flush();

                return $user;
            })
        );
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        return new RedirectResponse(
            $this->router->generate('login_error'),
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): RedirectResponse
    {
        return new RedirectResponse($this->getTargetPath($request->getSession(), 'discord') ?? $this->router->generate('home'));
    }

    public function start(Request $request, AuthenticationException $authException = null): RedirectResponse
    {
        $this->saveTargetPath($request->getSession(), 'discord', $request->getUri());

        return new RedirectResponse(
            $this->router->generate('login'),
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }
}
