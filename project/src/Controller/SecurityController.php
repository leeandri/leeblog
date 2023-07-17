<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'security.login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $utils): Response
    {
        // get the login error if there is one
        $error = $utils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $utils->getLastUsername();
        return $this->render('pages/security/login.html.twig', [
            'error' => $error,
            'last_username' => $lastUsername
        ]);
    }

    #[Route('/logout', name: 'security.logout', methods: ['GET'])]
    public function logout(): void
    {
        // Nothing to do here...
    }
}
