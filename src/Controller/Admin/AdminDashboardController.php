<?php

namespace App\Controller\Admin;

use App\Entity\Search;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\PaiementRepository;
use App\Repository\UserRepository;
use App\Repository\MissionRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/admin/dashboard')]
class AdminDashboardController extends AbstractController
{
    #[Route('/', name: 'app_admin_dashboard')]
    public function index(Request $request, PaiementRepository $paiementRepository, UserRepository $userRepository, MissionRepository $missionRepository): Response
    {
        $paiements = $paiementRepository->countByCritere('statut');

        return $this->render('admin/admin_dashboard/index.html.twig', [
            'paiements' => $paiements,
            'users' =>  $userRepository->findAll(),
            'missions' => $missionRepository->findAll(),
        ]);
    }
}
