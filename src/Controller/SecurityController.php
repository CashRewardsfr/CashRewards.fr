<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Repository\UserRepository;
use Psr\Log\LoggerInterface;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/disconnect', name: 'app_disconnect')]
    public function disconnect(UserRepository $userRepository): Response
    {
        $user = $this->getUser();
        $user->setConnected(0);
        $userRepository->add($user);
        return $this->redirectToRoute('app_logout');
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(UserRepository $userRepository): Response
    {

    }
}
