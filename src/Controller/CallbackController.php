<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use App\Repository\UserRepository;
use App\Repository\LogRepository;
use App\Repository\MissionRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Mission;
use App\Entity\Log;
use App\Repository\AvatarRepository;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/callback')]
class CallbackController extends AbstractController
{
    #[Route('/offertoro', name: 'app_callback_offertoro')]
    public function offertoro(UserRepository $userRepository, MissionRepository $missionRepository, LogRepository $logRepository, Request $request): Response
    {
        $serverIp = $request->getClientIp();
        $vanishIpOffer = ['202.90.152.68', '203.177.59.200', '54.175.173.245'];
        $userId = $request->get('user_id');
        $offerId = $request->get('oid');
        $amount = $request->get('amount');
        $description = $request->get('o_name');

        $log = new Log();
        $log->setOfferwallName('offertoro');
        $log->setParams(array(
            "serverIp"=> $serverIp,
            "vanishIpOffer"=> $vanishIpOffer,
            "userId"=> $userId,
            "offerId"=> $offerId,
            "amount"=> $amount,
            "description"=> $description
        ));

        if (in_array($serverIp, $vanishIpOffer)) {
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

                $log->setResult(1);
                $logRepository->add($log);
                return new Response(1);
            }
            $log->setResult(-2);
            $logRepository->add($log);
            return new Response(-2);
        }
        $log->setResult(-1);
        $logRepository->add($log);
        return new Response(-1);
    }

    #[Route('/makemoney', name: 'app_callback_makemoney')]
    public function makemoney(UserRepository $userRepository, MissionRepository $missionRepository, LogRepository $logRepository, Request $request): Response
    {
        $vanishIpOffer = '63.32.127.99';
        $serverIp = $request->getClientIp();
        $userId = $request->get('user_id');
        $amount = $request->get('amount');
        $offerId = $request->get('offerid');
        $offername = $request->get('offername');

        $log = new Log();
        $log->setOfferwallName('makemoney');
        $log->setParams(array(
            "serverIp"=> $serverIp,
            "vanishIpOffer"=> $vanishIpOffer,
            "userId"=> $userId,
            "offerId"=> $offerId,
            "amount"=> $amount,
            "offername"=> $offername
        ));

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

                $log->setResult(1);
                $logRepository->add($log);
                return new Response(1);
            }
            $log->setResult(-2);
            $logRepository->add($log);
            return new Response(-2);
        }
        $log->setResult(-1);
        $logRepository->add($log);
        return new Response(-1);
    }
    
    #[Route('/ayet', name: 'app_callback_ayet')]
    public function ayet(UserRepository $userRepository, MissionRepository $missionRepository, LogRepository $logRepository, Request $request): Response
    {
        $serverIp = $request->getClientIp();
        $vanishIpOffer = ['35.165.166.40', '35.166.159.131', '52.40.3.140'];
        $userId = $request->get('uid');
        $description = $request->get('offer_name');
        $amount = $request->get('currency_amount');

        $log = new Log();
        $log->setOfferwallName('ayet');
        $log->setParams(array(
            "serverIp"=> $serverIp,
            "vanishIpOffer"=> $vanishIpOffer,
            "userId"=> $userId,
            "amount"=> $amount,
            "description" => $description
        ));

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

                $log->setResult(1);
                $logRepository->add($log);
                return new Response(1);
            }
            $log->setResult(-2);
            $logRepository->add($log);
            return new Response(-2);
        }
        $log->setResult(-1);
        $logRepository->add($log);
        return new Response(-1);
    }
    
    #[Route('/lootably', name: 'app_callback_lootably')]
    public function lootably(UserRepository $userRepository, MissionRepository $missionRepository, LogRepository $logRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $key = $request->get('hash');
        $userId = $request->get('userID');
        $ip = $request->get('ip');
        $amount = $request->get('revenue');
        $currencyReward = $request->get('currencyReward');
        $description = $request->get('offerName');
        $mykey = hash("sha256", strval($userId) . strval($ip) . strval($amount) . strval($currencyReward) . "xt03d98zF3N8Jfmh7kOY50RdBOMFMjoQPMilKJZ9juYGMK38O5Ahs036yXtK6sshCZtoSVf7XUpXes3cLqA");

        $log = new Log();
        $log->setOfferwallName('lootably');
        $log->setParams(array(
            "key"=> $key,
            "mykey"=> $mykey,
            "ip"=> $ip,
            "userId"=> $userId,
            "amount"=> $amount,
            "currencyReward"=> $currencyReward,
            "description" => $description
        ));

        if($mykey == $key){
            $mission = new Mission();
            $user = $userRepository->findOneBy(['id' => $userId]); // Recupération de l'utilisateur
            if ($user) {
                $mission->setUser($user);
                $mission->setAmount(intval($currencyReward));
                $mission->setDescription("[Lootably] " . $description);
                $missionRepository->add($mission);

                $user->setPoints($user->getPoints() + intval($currencyReward)); // Incrémentation des points dans la base de données
                $userRepository->add($user);

                $log->setResult(1);
                $logRepository->add($log);
                return new Response(1);
            }
            $log->setResult(-2);
            $logRepository->add($log);
            return new Response(-2);
        }
        $log->setResult(-1);
        $logRepository->add($log);
        return new Response(-1);
    }    

    #[Route('/adgem', name: 'app_callback_adgem')]
    public function adgem(UserRepository $userRepository, MissionRepository $missionRepository, LogRepository $logRepository, Request $request): Response
    {
        $serverIp = $request->getClientIp();
        $userId = $request->get('player_id');
        $amount = $request->get('amount');
        $description = $request->get('campaign_name');
        $offerId = $request->get('campaign_id');

        $log = new Log();
        $log->setOfferwallName('adgem');
        $log->setParams(array(
            "serverIp"=> $serverIp,
            "ADGEM_POSTBACK_KEY_SHOULD_BE" => "57dk1m9c4elidh417c6e2i25",
            "ADGEM_POSTBACK_KEY" => $request->get('ADGEM_POSTBACK_KEY'),
            "userId"=> $userId,
            "amount"=> $amount,
            "description" => $description,
            "offerId"=> $offerId,
        ));

        if ('57dk1m9c4elidh417c6e2i25' === $request->get('ADGEM_POSTBACK_KEY')) {
            $user = $userRepository->findOneBy(['id' => $userId]);
            if ($user) {
                $mission = new Mission();
                $mission->setUser($user);
                $mission->setAmount(intval($amount));
                $mission->setOfferId($offerId);
                $mission->setDescription("[Adgem] " . $description);
                $missionRepository->add($mission);

                $user->setPoints($user->getPoints() + intval($amount));
                $userRepository->add($user);

                $log->setResult(1);
                $logRepository->add($log);
                return new Response(1);
            }
            $log->setResult(-2);
            $logRepository->add($log);
            return new Response(-2);
        }
        $log->setResult(-1);
        $logRepository->add($log);
        return new Response(-1);
    }

    #[Route('/monlix', name: 'app_callback_monlix')]
    public function monlix(UserRepository $userRepository, MissionRepository $missionRepository, LogRepository $logRepository, Request $request): Response
    {
        $secretKey = $request->get('secretKey');
        $monlixKey = "13801192946467d9437880de3b83c8adc85fe151edfc753878d77a410c318057";
        $userId = $request->get('userId');
        $amount = $request->get('rewardValue');
        $description = $request->get('taskName');

        $log = new Log();
        $log->setOfferwallName('monlix');
        $log->setParams(array(
            "secretKey"=> $secretKey,
            "monlixKey"=> $monlixKey,
            "userId"=> $userId,
            "amount"=> $amount,
            "description" => $description,
        ));

        if ($secretKey === $monlixKey) {
            $user = $userRepository->findOneBy(['id' => $userId]);
            if ($user) {
                $mission = new Mission();
                $mission->setUser($user);
                $mission->setAmount(intval($amount));
                $mission->setDescription("[Adgem] " . $description);
                $missionRepository->add($mission);

                $user->setPoints($user->getPoints() + intval($amount));
                $userRepository->add($user);

                $log->setResult(1);
                $logRepository->add($log);
                return new Response(1);
            }
            $log->setResult(-2);
            $logRepository->add($log);
            return new Response(-2);
        }
        $log->setResult(-1);
        $logRepository->add($log);
        return new Response(-1);
    }

    #[Route('/bitlabs', name: 'app_callback_bitlabs')]
    public function bitlabs(UserRepository $userRepository, MissionRepository $missionRepository, LogRepository $logRepository, Request $request): Response
    {
        $key = $request->get('key');
        $userId = $request->get('user_id');
        $amount = $request->get('amount');
        $description = $request->get('offerName');
        $status = $request->get('status');

        $log = new Log();
        $log->setOfferwallName('bitlabs');
        $log->setParams(array(
            "key"=> $key,
            "userId"=> $userId,
            "amount"=> $amount,
            "description" => $description,
            "status" => $status,
        ));

        if($key === "g5c21mln8j9jd6l1b8h129jc"){
            $user = $userRepository->findOneBy(['id' => $userId]);
            if ($user) {
                if ($status == 'SCREENOUT') {
                    $description = 'Compensation';
                }
                $mission = new Mission();
                $mission->setUser($user);
                $mission->setAmount(intval($amount));
                $mission->setDescription('[Bitlabs] ' . $description);
                $missionRepository->add($mission);

                $user->setPoints($user->getPoints() + intval($amount));
                $userRepository->add($user);

                $log->setResult(1);
                $logRepository->add($log);
                return new Response(1);
            }
            $log->setResult(-2);
            $logRepository->add($log);
            return new Response(-2);
        }
        $log->setResult(-1);
        $logRepository->add($log);
        return new Response(-1);
    }

    #[Route('/lootv', name: 'app_callback_lootv')]
    public function lootv(UserRepository $userRepository, MissionRepository $missionRepository, LogRepository $logRepository, Request $request): Response
    {
        $log = new Log();
        $log->setOfferwallName('lootv');
        $log->setParams(array());
        $log->setResult(1);
        $logRepository->add($log);
        return new Response(1);
    }

    #[Route('/adgate', name: 'app_callback_adgate')]
    public function adgate(UserRepository $userRepository, AvatarRepository $avatarRepository, LogRepository $logRepository, MissionRepository $missionRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $serverIp = $request->getClientIp();
        $vanishIpOffer = "52.42.57.125";
        $userId = $request->get('s1');
        $offerId = $request->get('offer_id');
        $amount = $request->get('points');
        $description = $request->get('offer_name');

        $log = new Log();
        $log->setOfferwallName('adgate');
        $log->setParams(array(
            "serverIp"=> $serverIp,
            "vanishIpOffer"=> $vanishIpOffer,
            "userId"=> $userId,
            "offerId"=> $offerId,
            "amount"=> $amount,
            "description" => $description,
        ));

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

                $log->setResult(1);
                $logRepository->add($log);
                return new Response(1);
            }
            $log->setResult(-2);
            $logRepository->add($log);
            return new Response(-2);
        }
        $log->setResult(-1);
        $logRepository->add($log);
        return new Response(-1);
    }

    private function userId()
    {
        return $this->getUser()->getId();
    }
}