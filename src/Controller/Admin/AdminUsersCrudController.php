<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\CreateUserFormType;
use App\Repository\UserRepository;
use App\Repository\MissionRepository;
use App\Repository\PaiementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/users')]
class AdminUsersCrudController extends AbstractController
{
    #[Route('/', name: 'app_admin_users_crud_index', methods: ['GET'])]
    public function index(UserRepository $userRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $users = $paginator->paginate(
            $userRepository->findByNomAsc(),
            $request->query->getInt('page', 1),
            20
        );

        return $this->render('admin/admin_users_crud/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/search/{query}', name: 'app_admin_users_crud_search', methods: ['GET'])]
    public function search(string $query, UserRepository $userRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $users = $paginator->paginate(
            $userRepository->findByNomAscQuery($query),
            $request->query->getInt('page', 1),
            20
        );

        return $this->render('admin/admin_users_crud/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/nouvel-utilisateur', name: 'app_admin_users_crud_new', methods: ['GET', 'POST'])]
    public function new(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(CreateUserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /* User parent
            if($this->getUser()){
                $user->setUser($this->getUser());
            }*/

            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    //$form->get('plainPassword')->getData()
                    '123456'
                )
            );

            $userRepository->add($user);

            $this->addFlash('success', 'Utilisateur enregistré avec succès');

            return $this->redirectToRoute('app_admin_users_crud_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/admin_users_crud/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_users_crud_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('admin/admin_users_crud/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/missions', name: 'app_admin_user_crud_missions')]
    public function missions(Request $request, User $user, MissionRepository $missionRepository, PaginatorInterface $paginator): Response
    {
        $missions = $paginator->paginate(
            $missionRepository->findByUser($user),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('admin/admin_users_crud/missions.html.twig', [
            'missions' => $missions,
            'user'  => $user,
        ]);
    }

    #[Route('/{id}/paiements', name: 'app_admin_user_crud_paiements')]
    public function paiements(Request $request, User $user, PaiementRepository $paiementRepository, PaginatorInterface $paginator): Response
    {
        $paiements = $paginator->paginate(
            $paiementRepository->findByUser($user),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('admin/admin_users_crud/paiements.html.twig', [
            'paiements' => $paiements,
            'user'  => $user,
        ]);
    }

    #[Route('/{id}/affiliers', name: 'app_admin_user_crud_affiliers')]
    public function affiliers(Request $request, User $user, UserRepository $userRepository, PaginatorInterface $paginator): Response
    {
        $affiliers = $paginator->paginate(
            $userRepository->findByParrain($user),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('admin/admin_users_crud/affiliers.html.twig', [
            'affiliers' => $affiliers,
            'user'  =>  $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_users_crud_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, UserRepository $userRepository): Response
    {
        // Seul l'administrateur auteur peut editer un utilisateur
        //$this->denyAccessUnlessGranted('user_edit', $user);
        
        $form = $this->createForm(CreateUserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userRepository->add($user);

            $this->addFlash('success', 'Utilisateur modifié avec succès');
            return $this->redirectToRoute('app_admin_users_crud_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/admin_users_crud/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_users_crud_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        // Seul l'administrateur auteur peut editer un utilisateur
        $this->denyAccessUnlessGranted('user_edit', $user);

        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $userRepository->remove($user);
        }

        $this->addFlash('success', 'Utilisateur supprimer avec succès');

        return $this->redirectToRoute('app_admin_users_crud_index', [], Response::HTTP_SEE_OTHER);
    }
}
