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
        dd($this->gestion->generateCode('ADULTE'));
        return $this->render('home/index.html.twig');
    }
}
