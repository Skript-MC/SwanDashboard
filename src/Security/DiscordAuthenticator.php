<?php

namespace App\Security;

use App\Document\DashUser;
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

    public function __construct(ClientRegistry $clientRegistry, DocumentManager $dm, RouterInterface $router)
    {
        $this->clientRegistry = $clientRegistry;
        $this->dm = $dm;
        $this->router = $router;
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
            new UserBadge($accessToken->getToken(), function () use ($accessToken, $client, $request) {
                /** @var DiscordRessourceOwner $discordUser */
                $discordUser = $this
                    ->getDiscordClient()
                    ->fetchUserFromToken($accessToken);

                if (!is_numeric($discordUser->getId())) {
                    throw new \Exception('Discord user ID is not numeric');
                }
                $userId = (int) $discordUser->getId();

                /* @var $existingUser DashUser */
                $existingUser = $this->dm
                    ->getRepository(DashUser::class)
                    ->findOneBy(['userId' => $userId]);

                // Merge existing user and new user, this will update the existing user if it is found
                $user = $existingUser ?: new DashUser();
                // If we have an existing user, don't refresh these data.
                if (!$existingUser)
                    $user->setDiscordId($userId);

                $user->setDiscordRoles([]); // TODO
                $user->setHasMFA($discordUser->hasMfaEnabled());

                $this->dm->persist($user);
                $this->dm->flush();

                return $user;
            })
        );
    }

    private function getDiscordClient(): OAuth2ClientInterface
    {
        return $this
            ->clientRegistry
            ->getClient('discord');
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): Response
    {
        $error = $request->query->getAlpha('error');
        if ($error === 'accessdenied') {
            $request->getSession()->getFlashBag()->add('error', 'Vous n\'avez pas autorisé la connexion avec Discord, nous ne sommes pas en mesure de vous authentifier.');
        } else {
            $request->getSession()->getFlashBag()->add('error', 'Une erreur est survenue lors de l\'authentification. Réessayez dans quelques minutes ou contactez-nous si le problème persiste.' . $error);
        }
        return new RedirectResponse(
            $this->router->generate('home'),
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
