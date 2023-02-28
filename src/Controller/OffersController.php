<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/offers')]
class OffersController extends AbstractController
{   
    #[Route('/offertoro', name: 'app_offers_offertoro')]
    public function offertoro(): Response
    {
        return $this->render('offers/offertoro.html.twig', [
            'offer' => 'offertorro',
            'https' =>  'https://www.offertoro.com/ifr/show/29928/'. $this->userId() .'/14002'
        ]);
    }

    #[Route('/ayet', name: 'app_offers_ayet')]
    public function ayet(): Response
    {
        return $this->render('offers/ayet.html.twig', [
            'offer' => 'ayet',
            'https' =>  'https://www.ayetstudios.com/offers/web_offerwall/6110?external_identifier='. $this->userId() .''
        ]);
    }

    // Lootably et lootv ont le meme lien
    #[Route('/lootably', name: 'app_offers_lootably')]
    public function lootably(): Response
    {
        return $this->render('offers/lootably.html.twig', [
            'offer' => 'lootably',
            'https' =>  'https://wall.lootably.com/?placementID=ckun7bl0u00ay01177xs81j0u&sid='. $this->userId() .''
        ]);
    }

    #[Route('/adgem', name: 'app_offers_adgem')]
    public function adgem(): Response
    {
        return $this->render('offers/adgem.html.twig', [
            'offer' => 'adgem',
            'https' => 'https://api.adgem.com/v1/wall?appid=24485&playerid='. $this->userId() .''
        ]);
    }
    
    #[Route('/mmwall', name: 'app_offers_mmwall')]
    public function mmwall(): Response
    {
        return $this->render('offers/mmwall.html.twig', [
            'offer' => 'mmwall',
            'https' => 'https://wall.make-money.top/?p=340&u='. $this->userId() .''
        ]);
    }
    
    #[Route('/monlix', name: 'app_offers_monlix')]
    public function monlix(): Response
    {
        return $this->render('offers/monlix.html.twig', [
            'offer' => 'monlix',
            'https' => 'https://offers.monlix.com/?appid=1491&userid='. $this->userId() .''
        ]);
    }

    #[Route('/bitlabs', name: 'app_offers_bitlabs')]
    public function bitlabs(): Response
    {
        return $this->render('offers/bitlabs.html.twig', [
            'offer' => 'bitlabs',
            'https' =>  'https://web.bitlabs.ai/?token=4fd211a2-fdff-4296-a71d-681fb99fd160&uid='. $this->userId().'',
        ]);
    }

    #[Route('/lootv', name: 'app_offers_lootv')]
    public function lootv(): Response
    {
        return $this->render('offers/lootv.html.twig', [
            'offer' => 'lootv',
            'https' =>  'https://api.lootably.com/api/offerwall/redirect/offer/101-999?placementID=ckun7bl0u00ay01177xs81j0u&rawPublisherUserID='. $this->userId() .''
        ]);
    }

    #[Route('/adgate', name: 'app_offers_adgate')]
    public function adgate(): Response
    {
        return $this->render('offers/adgate.html.twig', [
            'offer' => 'adgate',
            'https' =>  'https://wall.adgaterewards.com/oKuUqw/'. $this->userId() .''
        ]);
    }

    private function userId()
    {
        if ($this->getUser() == null) {
            header('Location: /login', true, 303);
            die();
        } else {
            return $this->getUser()->getId();   
        }
    }
}
