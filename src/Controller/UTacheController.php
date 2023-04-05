<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UTacheController extends AbstractController
{
    #[Route('/u-tache', name: 'app_utache')]
    public function index(): Response
    {

        $statusbar = [
            'links' => [

            ],
        ];

        return $this->render('utache/utache.html.twig', [
            'dataStatusbar' => $statusbar,
        ]);
    }
}
