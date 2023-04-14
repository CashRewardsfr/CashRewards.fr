<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EarnController extends AbstractController
{
    #[Route('/earn', name: 'app_earn')]
    public function earn(): Response
    {   
        return $this->render('earn/earn.html.twig');
    }
}
