<?php

namespace App\Security;

use App\Repository\ApiClientRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\Authenticator\Token;
use Symfony\Component\Security\Http\Authenticator\Token\PostAuthenticationToken;

class ApiKeyAuthenticator implements AuthenticatorInterface
{

    public function __construct(private ApiClientRepository $apiClientRepository)
    {
    }

    /**
     * @inheritDoc
     */
    public function supports(Request $request): ?bool
    {
//        dd('ici cest');
        return $request->headers->has('X-API-KEY');
    }

    /**
     * @inheritDoc
     */
    public function authenticate(Request $request): Passport
    {
        $apiKey = $request->headers->get('X-API-KEY'); //dd("API Key réçue: {$apiKey}");
        if ($apiKey === null){
            throw new AuthenticationException("Aucune clé API fournie");
        }

        return new SelfValidatingPassport(new UserBadge($apiKey, function ($key) {
            $user = $this->apiClientRepository->findOneBy(['apiKey' => $key]);
            if (!$user) {
                throw new AuthenticationException('Clé API invalide');
            } //dd($user);

            return $user;
        }));

    }

    /**
     * @inheritDoc
     */
    public function createToken(Passport $passport, string $firewallName): PostAuthenticationToken
    {
//        dd('Utilisateur dans createToken : ', $passport->getUser()->getName(), 'Rôles : ', $passport->getUser()->getRoles());
        return new PostAuthenticationToken(
            $passport->getUser(),
            $firewallName,
            $passport->getUser()->getRoles()
        );
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
        // Tu peux aussi utiliser un logger ici
        return new JsonResponse([
            'message' => "Clé API invalide ou manquante",
            'erreur' => $exception->getMessage(),
        ], Response::HTTP_UNAUTHORIZED);
    }

}