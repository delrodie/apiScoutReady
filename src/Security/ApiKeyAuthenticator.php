<?php

namespace App\Security;

use App\Repository\ApiClientRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class ApiKeyAuthenticator implements AuthenticatorInterface
{

    public function __construct(private ApiClientRepository $apiClientRepository)
    {
    }

    public function supports(Request $request): ?bool
    {
        return $request->headers->has('X-API-KEY');
    }


    public function authenticate(Request $request): Passport
    {
        $apiKey = $request->headers->get('X-API-KEY');
        if ($apiKey === null){
            throw new AuthenticationException("Aucune clé API fournie");
        }

        return new SelfValidatingPassport(new UserBadge($apiKey, function ($key) {
            return $this->apiClientRepository->findOneBy(['apiKey' => $key]);
        }));
    }

    /**
     * @inheritDoc
     */
    public function createToken(Passport $passport, string $firewallName): TokenInterface
    {
        // TODO: Implement createToken() method.
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    /**
     * @inheritDoc
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse([
            'error' => "Clé API invalide",
        ], Response::HTTP_UNAUTHORIZED);
    }
}