<?php

namespace App\Controller\Admin;

use App\Entity\Mission;
use App\Form\MissionType;
use App\Repository\MissionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/missions/crud')]
class AdminMissionsCrudController extends AbstractController
{
    #[Route('/', name: 'app_admin_missions_crud_index', methods: ['GET'])]
    public function index(MissionRepository $missionRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $missions = $paginator->paginate(
            $missionRepository->findByDateDesc(),
            $request->query->getInt('page', 1),
            20
        );

        return $this->render('admin/admin_missions_crud/index.html.twig', [
            'missions' => $missions,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_missions_crud_delete', methods: ['POST'])]
    public function delete(Request $request, Mission $mission, MissionRepository $missionRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$mission->getId(), $request->request->get('_token'))) {
            $missionRepository->remove($mission);
        }

        $this->addFlash('success', 'Mission supprimer avec succès!');

        return $this->redirectToRoute('app_admin_missions_crud_index', [], Response::HTTP_SEE_OTHER);
    }
}
