<?php

namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class AppCustomAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    public function __construct(private UrlGeneratorInterface $urlGenerator, private UserRepository $userRepository)
    {
    }

    public function authenticate(Request $request): Passport
    {

        $credentials = [
            'email_or_pseudo' => $request->request->get('email_or_pseudo'),
            'password' => $request->request->get('password', ''),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];

        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $credentials['email_or_pseudo']);
        return new Passport(
            new UserBadge($credentials['email_or_pseudo'], function ($userIdentifier) {
                $user =  $this->userRepository->findByEmailOrUsername($userIdentifier);
                if($user == null){
                    throw new UserNotFoundException();
                }
                if(!$user->isIsActif()){
                    throw new CustomUserMessageAuthenticationException("Utilisateur non actif. Contactez l'administrasteur pour plus d'informations");
                }
                return $user;
            }),
            new PasswordCredentials($credentials['password']),
            [
                new CsrfTokenBadge('authenticate', $credentials['csrf_token']),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $rememberMe = $request->request->get('_remember_me');
        $user = $token->getUser();

//        if ($user){
//            $response = new RedirectResponse($this->urlGenerator->generate('first_connection'));
//        }
//        else
            if($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            $response = new RedirectResponse($targetPath);
        }else{
            //TODO mettre home
            $response = new RedirectResponse($this->urlGenerator->generate('app_home'));
        }

        if ($rememberMe != null){
            $cookie = Cookie::create('REMEMBERME', $request->request->get('email_or_pseudo'), strtotime('+1 year'));
        }else{
            $cookie = Cookie::create('REMEMBERME', '', time() - 3600, '/');
        }
        $response->headers->setCookie($cookie);

        return $response;
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
