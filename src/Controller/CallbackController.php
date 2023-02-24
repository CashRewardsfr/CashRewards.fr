<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\UserRepository;
use App\Repository\MissionRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Mission;
use App\Repository\AvatarRepository;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/callback')]
class CallbackController extends AbstractController
{
    #[Route('/makemoney', name: 'app_callback_makemoney')]
    public function makemoney(UserRepository $userRepository, MissionRepository $missionRepository, Request $request): Response
    {
        $vanishIpOffer = '63.32.127.99';
        $serverIp = $request->getClientIp();
        $userId = $request->get('user_id');
        $amount = $request->get('amount');
        $offerId = $request->get('offerid');
        $offername = $request->get('offername');
        if ($serverIp === $vanishIpOffer) {
            $user = $userRepository->findOneBy(['id' => $userId]);
            if ($user) {
                $mission = new Mission();
                $mission->setUser($user);
                $mission->setAmount(intval($amount));
                $mission->setOfferId($offerId);
                $mission->setDescription('[MakeMoney] ' . $offername);
                $missionRepository->add($mission);

                $user->setPoints($user->getPoints() + intval($amount));
                $userRepository->add($user);

                return new Response(1);
            }
            return new Response(-2);
        }
        return new Response(-1);
    }
    
    #[Route('/offertoro', name: 'app_callback_offertoro')]
    public function offertoro(UserRepository $userRepository, MissionRepository $missionRepository, Request $request): Response
    {
        $serverIp = $request->getClientIp();
        $vanishIpOffer = '54.175.173.245';
        $userId = $request->get('user_id');
        $offerId = $request->get('oid');
        $amount = $request->get('amount');
        $description = $request->get('o_name');
        if ($serverIp === $vanishIpOffer) {
            $user = $userRepository->findOneBy(['id' => $userId]);
            if ($user) {
                $mission = new Mission();
                $mission->setUser($user);
                $mission->setAmount(intval($amount));
                $mission->setOfferId($offerId);
                $mission->setDescription("[Offertoro] " . $description);
                $missionRepository->add($mission);

                $user->setPoints($user->getPoints() + intval($amount));
                $userRepository->add($user);

                return new Response(1);
            }
            return new Response(-2);
        }
        return new Response(-1);
    }

    #[Route('/ayet', name: 'app_callback_ayet')]
    public function ayet(UserRepository $userRepository, MissionRepository $missionRepository, Request $request): Response
    {
        $serverIp = $request->getClientIp();
        $vanishIpOffer = ['35.165.166.40', '35.166.159.131', '52.40.3.140'];
        $userId = $request->get('uid');
        $description = $request->get('offer_name');
        $amount = $request->get('currency_amount');
        if(in_array($serverIp, $vanishIpOffer)){
            $user = $userRepository->findOneBy(['id' => $userId]);
            if ($user) {
                $mission = new Mission();
                $mission->setUser($user);
                $mission->setAmount(intval($amount));
                $mission->setDescription('[Ayet] ' . $description);
                $missionRepository->add($mission);

                $user->setPoints($user->getPoints() + intval($amount));
                $userRepository->add($user);

                return new Response(1);
            }
            return new Response(-2);
        }
        return new Response(-1);
    }
    
    #[Route('/lootably', name: 'app_callback_lootably')]
    public function lootably(UserRepository $userRepository, MissionRepository $missionRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $key = $request->get('hash');
        $userId = $request->get('userID');
        $ip = $request->get('ip');
        $amount = $request->get('revenue');
        $currencyReward = $request->get('currencyReward');
        $description = $request->get('offerName');
        $mykey = hash("sha256", strval($userId) . strval($ip) . strval($amount) . strval($currencyReward) . "xt03d98zF3N8Jfmh7kOY50RdBOMFMjoQPMilKJZ9juYGMK38O5Ahs036yXtK6sshCZtoSVf7XUpXes3cLqA");
        if($mykey == $key){
            $mission = new Mission();
            $user = $userRepository->findOneBy(['id' => $userId]); // Recupération de l'utilisateur
            if ($user) {
                $user->setPoints($user->getPoints() + intval($currencyReward)); // Incrémentation des points dans la base de données
                $mission->setUser($user);
                $mission->setAmount(intval($currencyReward));
                $mission->setDescription("[Lootably] " . $description);
                $missionRepository->add($mission);
                $userRepository->add($user);
                return new Response(1);
            }
        }
        return new Response(0);
    }    

    #[Route('/adgem', name: 'app_callback_adgem')]
    public function adgem(UserRepository $userRepository, MissionRepository $missionRepository,  Request $request): Response
    {
        
        $serverIp = $request->getClientIp();
        $userId = $request->get('player_id');
        $amount = $request->get('amount');
        $description = $request->get('campaign_name');
        $offerId = $request->get('campaign_id');

        if('57dk1m9c4elidh417c6e2i25' === $request->get('ADGEM_POSTBACK_KEY')){
            $mission = new Mission();

            // Recupération de l'utilisateur
            $user = $userRepository->findOneBy(['id' => $userId]);

            // Recupération du parrain
            $parrain = $user->getParrain();

            // Incrémentation des points dans la base de données
            $user->setPoints($user->getPoints() + intval($amount));

            // Inssertion
            $mission->setUser($user);
            $mission->setAmount($amount);
            $mission->setDescription("[Adgem] " . $description);
            $mission->setOfferId($offerId);

            // Enregistrement dans la base de données
            $missionRepository->add($mission);
            $userRepository->add($user);
            //$userRepository->add($parrain);
        }
        
        return new JsonResponse([], Response::HTTP_OK);
    }

    #[Route('/monlix', name: 'app_callback_monlix')]
    public function monlix(UserRepository $userRepository, MissionRepository $missionRepository, Request $request): Response
    {
        $secretKey = $request->get('secretKey');
        $userId = $request->get('userId');
        $transactionId = $request->get('transactionId');
        $amount = $request->get('rewardValue');
        $description = $request->get('taskName');
        $monlixKey = "13801192946467d9437880de3b83c8adc85fe151edfc753878d77a410c318057";

        if($secretKey === $monlixKey){
            $mission = new Mission();

            // Recupération de l'utilisateur
            $user = $userRepository->findOneBy(['id' => $userId]);

            // Recupération du parrain
            $parrain = $user->getParrain();

            // Incrémentation des points dans la base de données
            $user->setPoints($user->getPoints() + $amount);

            // Inssertion
            $mission->setUser($user);
            $mission->setAmount($amount);
            $mission->setTransactionId($transactionId);
            $mission->setDescription("[Monlix] " . $description);

            // Enregistrement dans la base de données
            $missionRepository->add($mission);
            $userRepository->add($user);
            //$userRepository->add($parrain);
        }

        return new JsonResponse([], Response::HTTP_OK);
    }

    #[Route('/bitlabs', name: 'app_callback_bitlabs')]
    public function bitlabs(UserRepository $userRepository, MissionRepository $missionRepository, Request $request): Response
    {
        $key = $request->get('key');
        $userId = $request->get('user_id');
        $amount = $request->get('amount');
        $description = $request->get('offerName');
        $status = $request->get('status');


        if($key === "g5c21mln8j9jd6l1b8h129jc"){
            if($status == "COMPLETE") {
                $mission = new Mission();
    
                // Recupération de l'utilisateur
                $user = $userRepository->findOneBy(['id' => $userId]);
    
                // Recupération du parrain
                $parrain = $user->getParrain();
    
                // Incrémentation des points dans la base de données
                $user->setPoints($user->getPoints() + $amount);
    
                // Inssertion
                $mission->setUser($user);
                $mission->setAmount($amount);
                $mission->setDescription("[Bitlabs] " . $description);

                //$mission->setTransactionId($transactionId);
                //$mission->setStatut($statut);
    
                // Enregistrement dans la base de données
                $missionRepository->add($mission);
                $userRepository->add($user);
                //$userRepository->add($parrain);
            } else if($status == "SCREENOUT") {
                $mission = new Mission();
    
                // Recupération de l'utilisateur
                $user = $userRepository->findOneBy(['id' => $userId]);
    
                // Recupération du parrain
                $parrain = $user->getParrain();
    
                // Incrémentation des points dans la base de données
                $user->setPoints($user->getPoints() + $amount);
    
                // Inssertion
                $mission->setUser($user);
                $mission->setAmount($amount);
                $mission->setDescription("[Bitlabs] Compensation");

                //$mission->setTransactionId($transactionId);
                //$mission->setStatut($statut);
    
                // Enregistrement dans la base de données
                $missionRepository->add($mission);
                $userRepository->add($user);
                //$userRepository->add($parrain);
            }
        }

        return new JsonResponse([], Response::HTTP_OK);
    }

    #[Route('/lootv', name: 'app_callback_lootv')]
    public function lootv(UserRepository $userRepository, MissionRepository $missionRepository, Request $request): Response
    {
        return new JsonResponse([], Response::HTTP_OK);
    }

    #[Route('/adgate', name: 'app_callback_adgate')]
    public function adgate(UserRepository $userRepository, AvatarRepository $avatarRepository, MissionRepository $missionRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $serverIp = $request->getClientIp();
        $vanishIpOffer = "52.42.57.125";
        $userId = $request->get('s1');
        $offerId = $request->get('offer_id');
        $amount = $request->get('points');
        $description = $request->get('offer_name');
        if ($serverIp === $vanishIpOffer) {
            $user = $userRepository->findOneBy(['id' => $userId]);
            if ($user) {
                $mission = new Mission();
                $mission->setUser($user);
                $mission->setAmount(intval($amount));
                $mission->setOfferId($offerId);
                $mission->setDescription("[Adgate] " . $description);
                $missionRepository->add($mission);

                $user->setPoints($user->getPoints() + intval($amount));
                $userRepository->add($user);

                return new Response(1);
            }
            return new Response(-2);
        }
        return new Response(-1);
    }

    private function userId()
    {
        return $this->getUser()->getId();
    }
}