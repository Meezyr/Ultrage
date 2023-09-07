<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UMotDePasseController extends AbstractController
{
    #[Route('/u-motdepasse', name: 'app_umotdepasse')]
    public function index(): Response
    {
        $statusbar = ['links' => [],];

        return $this->render('umotdepasse/umotdepasse.html.twig', [
            'dataStatusbar' => $statusbar,
        ]);
    }
}
