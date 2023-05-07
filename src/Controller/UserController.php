<?php

namespace App\Controller;

use App\Form\EditeProfilFormType;
use App\Form\AvatarFormType;
use App\Form\ChangePasswordFormType;
use App\Repository\UserRepository;
use App\Repository\PaiementRepository;
use App\Repository\MissionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\UrlHelper;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Mission;

#[Route('/users')]
class UserController extends AbstractController
{
    private UrlHelper $urlHelper;

    public function __construct(UrlHelper $urlHelper)
    {
        $this->urlHelper = $urlHelper;
    }

    #[Route('/dashboard', name: 'app_user_dashboard')]
    public function dashboard(Request $request, MissionRepository $missionRepository, UserRepository $userRepository, PaginatorInterface $paginator, PaiementRepository $paiementRepository): Response
    {
        $user = $this->getUser();
        $lastBonus = null;
        $pointsEnCirculation = 0;
        $mission = null;

        if($user){
            $dateDujour = date('d/m/Y');

            if($user->getLastBonusDate() != $dateDujour){
                // 2 Attribution du bonus journalière
                $user->setLastBonusDate($dateDujour);
                $user->setPoints($user->GetPoints() + 15);
                $user->setConnected(1);
                $userRepository->add($user);

                $mission = new Mission();

                // Inssertion
                $mission->setUser($user);
                $mission->setAmount(15);
                $mission->setDescription('Bonus Journalier');
                $mission->setOfferId(0);
                $mission->setTransactionId('');
                $mission->setStatut(1);
    
                // Enregistrement dans la base de données
                $missionRepository->add($mission);

                $this->addFlash('success', 'Vous avez gagnez 15 points de bonus journalier');
            }
            
            $pointsEnCirculation = number_format(array_values($userRepository->countPoints())[0]);
            $mission = $missionRepository->findLastByUser($user);
        }

        $parrain = $this->getUser();

        $affiliers = $paginator->paginate(
            $userRepository->findByParrain($parrain),
            $request->query->getInt('page', 1),
            10
        );

        $paiements = $paginator->paginate(
            $paiementRepository->findByUser($user),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('user/dashboard.html.twig', [
            'pointsEnCirculation' => $pointsEnCirculation,
            'mission' => $mission,
            'affiliers' => $affiliers,
            'paiements' => $paiements,
            'parrainageLink' => $this->urlHelper->getAbsoluteUrl('/parrainage/parrain_id=' . $this->getUser()->getId()),
        ]);
    }

    #[Route('/missions', name: 'app_user_missions')]
    public function missions(Request $request, MissionRepository $missionRepository, PaginatorInterface $paginator): Response
    {
        $user = $this->getUser();

        $page = 1;
        if ($request->query->get('page')) {
            $page = $request->query->get('page');
        }

        $missions = $paginator->paginate(
            $missionRepository->findByUser($user),
            $request->query->getInt('page', $page),
            10
        );

        return $this->render('user/missions.html.twig', [
            'missions' => $missions,
        ]);
    }

    #[Route('/affiliers', name: 'app_user_affiliers')]
    public function affiliers(Request $request, UserRepository $userRepository, PaginatorInterface $paginator): Response
    {
        $parrain = $this->getUser();

        $affiliers = $paginator->paginate(
            $userRepository->findByParrain($parrain),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('user/affiliers.html.twig', [
            'affiliers' => $affiliers,
        ]);
    }

    #[Route('/paiements', name: 'app_user_paiements')]
    public function paiements(Request $request, PaiementRepository $paiementRepository, PaginatorInterface $paginator): Response
    {
        $user = $this->getUser();

        $paiements = $paginator->paginate(
            $paiementRepository->findByUser($user),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('user/paiements.html.twig', [
            'paiements' => $paiements,
        ]);
    }

    #[Route('/profil', name: 'app_user_profil')]
    public function userProfil(Request $request): Response
    {

        return $this->render('user/profil.html.twig', [
            'parrainageLink' => $this->urlHelper->getAbsoluteUrl('/parrainage/parrain_id=' . $this->getUser()->getId()),
        ]);
    }

    #[Route('/edition-du-profil', name: 'app_user_edit_profil', methods: ['GET', 'POST'])]
    public function editProfile(Request $request, UserRepository $userRepository): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(EditeProfilFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $userRepository->add($user);

            $this->addFlash('success', 'Votre profile a bien été mise à jour');

            return $this->redirectToRoute('app_user_profil', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/editProfil.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }


    /**
     * Validates and process the reset URL that the user clicked in their email.
     */
    #[Route('/edition-mot-de-passe', name: 'app_user_reset_password')]
    public function reset(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserRepository $userRepository): Response
    {
        $user = $this->getUser();

        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Encode(hash) the plain password, and set it.
            $encodedPassword = $userPasswordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            );

            $user->setPassword($encodedPassword);
            $userRepository->add($user);
            //$this->entityManager->flush();

            // The session is cleaned up after the password has been changed.
            //$this->cleanSessionAfterReset();

            $this->addFlash('success', 'Votre mot de passe a bien été mise à jour avec succès');

            return $this->redirectToRoute('app_user_profil');
        }

        return $this->render('user/changePassword.html.twig', [
            'resetForm' => $form->createView(),
        ]);
    }

    #[Route('/change-avatar', name: 'app_user_change_avatar')]
    public function changeAvatar(Request $request, UserRepository $userRepository): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(AvatarFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $userRepository->add($user);

            $this->addFlash('success', 'Votre profil a bien été mise à jour');
            return $this->redirectToRoute('app_user_profil', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/changeAvatar.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/classement', name: 'app_user_classements')]
    public function classement(Request $request, UserRepository $userRepository, PaginatorInterface $paginator): Response
    {
        $page = $request->query->getInt('page', 1);
        $users = $paginator->paginate(
            $userRepository->findUsersByClassement(),
            $page,
            10
        );

        return $this->render('user/classement.html.twig', [
            'users' => $users,
            'page' => $page,
        ]);
    }
}
