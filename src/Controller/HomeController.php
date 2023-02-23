<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\MissionRepository;
use App\Repository\UserRepository;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function home(MissionRepository $missionRepository, UserRepository $userRepository): Response
    {   
        $user = $this->getUser();

        if($user){

            $dateDujour = date('d/m/Y');

            if($user->getLastBonusDate() != $dateDujour){
                // 2 Attribution du bonus journalière
                $user->setLastBonusDate($dateDujour);
                $user->setPoints($user->GetPoints() + 15);
                $userRepository->add($user);

                $this->addFlash('success', 'Vous avez gagnez 15 points de bonus journalier + log');
            }            
        }

        return $this->render('home/home.html.twig', [
            'missions' =>  $missionRepository->findByDateDesc()
        ]);
    }

    #[Route('/politique', name: 'app_politique')]
    public function politique(): Response
    {   
        return $this->render('home/politique.html.twig');
    }
    
    #[Route('/conditions', name: 'app_condition')]
    public function conditions(): Response
    {   
        return $this->render('home/conditions.html.twig');
    }
}
