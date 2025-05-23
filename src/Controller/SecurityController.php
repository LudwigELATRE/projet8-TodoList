<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class SecurityController extends AbstractController
{

    public function __construct(private readonly AuthenticationUtils $authenticationUtils)
    {
    }

    #[Route("/login", name: "login")]
    public function login(): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('default');
        }

        $error = $this->authenticationUtils->getLastAuthenticationError();
        $lastUsername = $this->authenticationUtils->getLastUsername();
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route("/login_check", name: "login_check")]
    /**
     * @codeCoverageIgnore
     */
    public function loginCheck(): void
    {
        // This code is never executed.
        throw new \LogicException('Cette méthode ne doit jamais être exécutée directement. Elle est gérée par le firewall.');
    }

    #[Route("/logout", name: "logout")]
    /**
     * @codeCoverageIgnore
     */
    public function logout(): Response
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
