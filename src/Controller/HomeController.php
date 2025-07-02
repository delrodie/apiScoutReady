<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\Gestion;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    public function __construct(
        private Gestion $gestion
    )
    {
    }

    #[Route('/')]
    public function index(): Response
    {
        // CF2506204147-F5
//        $code = $this->gestion->generateCode('ADULTE'); // CF2506204963-CE"
//        $verification = $this->gestion->verificationChecksum($code);

        //dd("$code - $verification");
        return $this->render('home/index.html.twig');
    }
}
