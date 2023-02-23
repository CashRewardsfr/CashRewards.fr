<?php

namespace App\Controller\Admin;

use App\Entity\Avatar;
use App\Form\Avatar1Type;
use App\Repository\AvatarRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/avatar')]
class AdminAvatarController extends AbstractController
{
    #[Route('/', name: 'app_admin_avatar_index', methods: ['GET'])]
    public function index(AvatarRepository $avatarRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $avatars = $paginator->paginate(
            $avatarRepository->findByDateDesc(),
            $request->query->getInt('page', 1),
            20
        );

        return $this->render('admin/admin_avatar/index.html.twig', [
            'avatars' => $avatars,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_avatar_delete', methods: ['POST'])]
    public function delete(Request $request, Avatar $avatar, AvatarRepository $avatarRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$avatar->getId(), $request->request->get('_token'))) {
            $avatarRepository->remove($avatar);
        }

        $this->addFlash('success', 'Image supprimée avec succès');

        return $this->redirectToRoute('app_admin_avatar_index', [], Response::HTTP_SEE_OTHER);
    }
}
