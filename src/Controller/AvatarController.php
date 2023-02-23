<?php

namespace App\Controller;

use App\Entity\Avatar;
use App\Form\AvatarType;
use App\Repository\AvatarRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/avatar')]
class AvatarController extends AbstractController
{
    #[Route('/uploader-une-image', name: 'app_avatar_avatar', methods: ['GET', 'POST'])]
    public function new(Request $request, AvatarRepository $avatarRepository): Response
    {
        $userAvatar = $avatarRepository->findOneBy(['user' => $this->getUser()]);

        if($userAvatar){

            $avatar = $userAvatar;

        }else{

            $avatar = new Avatar();
        }
        
        $form = $this->createForm(AvatarType::class, $avatar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $avatar->setUser($this->getUser());
            $avatarRepository->add($avatar);
            return $this->redirectToRoute('app_user_profil', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('avatar/new.html.twig', [
            'avatar' => $avatar,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_avatar_delete', methods: ['POST'])]
    public function delete(Request $request, Avatar $avatar, AvatarRepository $avatarRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$avatar->getId(), $request->request->get('_token'))) {
            $avatarRepository->remove($avatar);
        }

        return $this->redirectToRoute('app_avatar_index', [], Response::HTTP_SEE_OTHER);
    }
}
