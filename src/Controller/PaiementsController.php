<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\VirementPaypalFormType;
use App\Form\VirementLitecoinFormType;
use App\Form\VirementBitcoinFormType;
use App\Form\VirementAmazonFormType;
use App\Entity\Paiement;
use App\Repository\UserRepository;
use App\Repository\PaiementRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/users/demande-paiement')]
class PaiementsController extends AbstractController
{
    #[Route('/', name: 'app_user_demande_paiement', methods: ['GET', 'POST'])]
    public function paiement(Request $request, PaiementRepository $paiementRepository, UserRepository $userRepository): Response
    {
        $user = $this->getUser();

        $paiement = new Paiement();
        $paypalForm = $this->createForm(VirementPaypalFormType::class, $paiement);
        $paypalForm->handleRequest($request);

        $litecoinForm = $this->createForm(VirementLitecoinFormType::class, $paiement);
        $litecoinForm->handleRequest($request);

        $bitcoinform = $this->createForm(VirementBitcoinFormType::class, $paiement);
        $bitcoinform->handleRequest($request);

        $amazonform = $this->createForm(VirementAmazonFormType::class, $paiement);
        $amazonform->handleRequest($request);

        $successMessage = "Votre demande est en cours de validation. <br> &nbsp;Vous recevrez votre paiement dans quelques jours maximum.";

        $canceledMessage = "Votre demande n'a pas pu aboutir pour la raison suivante: <br> &nbsp;Vous n'avez pas assez de points pour faire une demande de paiement.";

        if ($paypalForm->isSubmitted() && $paypalForm->isValid()) {

            $totalPointsReduction = $paiement->getMontant() * 1000;

            /*
            * Décrémentation des points
            */
            if($totalPointsReduction <= $user->getPoints()){

                $user->setPoints($user->getPoints() - $totalPointsReduction);

            }else{
                $this->addFlash('danger', $canceledMessage);

                return $this->redirectToRoute('app_user_demande_paiement', [], Response::HTTP_SEE_OTHER);
            }

            $paiement->setUser($user);
            $paiement->setStatut(0);
            $paiement->setDeleted(0);
            $paiement->setPointReduction($totalPointsReduction);
            $paiement->setVirementType('Paypal');
            $paiementRepository->add($paiement);

            $userRepository->add($user);

            $this->addFlash('success', $successMessage);

            return $this->redirectToRoute('app_user_paiements', [], Response::HTTP_SEE_OTHER);
        }

        if ($litecoinForm->isSubmitted() && $litecoinForm->isValid()) {

            $totalPointsReduction = $paiement->getMontant() * 1000;

            /*
            * Décrémentation des points
            */
            if($totalPointsReduction <= $user->getPoints()){

                $user->setPoints($user->getPoints() - $totalPointsReduction);

            }else{
                
                $this->addFlash('danger', $canceledMessage);

                return $this->redirectToRoute('app_user_demande_paiement', [], Response::HTTP_SEE_OTHER);
            }

            $paiement->setUser($user);
            $paiement->setStatut(0);
            $paiement->setDeleted(0);
            $paiement->setPointReduction($totalPointsReduction);
            $paiement->setVirementType('Litecoin');
            $paiementRepository->add($paiement);

            $userRepository->add($user);

            $this->addFlash('success', $successMessage);

            return $this->redirectToRoute('app_user_paiements', [], Response::HTTP_SEE_OTHER);
        }

        if ($bitcoinform->isSubmitted() && $bitcoinform->isValid()) {

            $totalPointsReduction = $paiement->getMontant() * 1000;

            /*
            * Décrémentation des points
            */
            if($totalPointsReduction <= $user->getPoints()){

                $user->setPoints($user->getPoints() - $totalPointsReduction);

            }else{
                
                $this->addFlash('danger', $canceledMessage);

                return $this->redirectToRoute('app_user_demande_paiement', [], Response::HTTP_SEE_OTHER);
            }

            $paiement->setUser($user);
            $paiement->setStatut(0);
            $paiement->setDeleted(0);
            $paiement->setPointReduction($totalPointsReduction);
            $paiement->setVirementType('Bitcoin');
            $paiementRepository->add($paiement);

            $userRepository->add($user);

            $this->addFlash('success', $successMessage);

            return $this->redirectToRoute('app_user_paiements', [], Response::HTTP_SEE_OTHER);
        }

        if ($amazonform->isSubmitted() && $amazonform->isValid()) {

            $totalPointsReduction = $paiement->getMontant() * 1000;

            /*
            * Décrémentation des points
            */
            if($totalPointsReduction <= $user->getPoints()){

                $user->setPoints($user->getPoints() - $totalPointsReduction);

            }else{
                
                $this->addFlash('danger', $canceledMessage);

                return $this->redirectToRoute('app_user_demande_paiement', [], Response::HTTP_SEE_OTHER);
            }

            $paiement->setUser($user);
            $paiement->setStatut(0);
            $paiement->setDeleted(0);
            $paiement->setPointReduction($totalPointsReduction);
            $paiement->setVirementType('Amazon');
            $paiementRepository->add($paiement);

            $userRepository->add($user);

            $this->addFlash('success', $successMessage);

            return $this->redirectToRoute('app_user_paiements', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('paiements/demandes.html.twig', [
            //'user' => $user,
            'paypalform' => $paypalForm,
            'litecoinform' => $litecoinForm,
            'bitcoinform' => $bitcoinform,
            'amazonform' => $amazonform,
        ]);
    }
}
