<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UDocumentationController extends AbstractController
{
    #[Route('/u-documentation', name: 'app_udocumentation')]
    public function index(): Response
    {
        $statusbar = [
            'links' => [
                ['title' => 'Liste des documentations', 'url' => $this->generateUrl("app_udocumentation"), 'target' => false],
                ['title' => 'Créer une documentation', 'url' => $this->generateUrl("app_udocumentation_new"), 'target' => false],
            ],
        ];

        return $this->render('udocumentation/udocumentation.html.twig', [
            'dataStatusbar' => $statusbar,
        ]);
    }

    #[Route('/u-documentation/nouvelle-documentation', name: 'app_udocumentation_new')]
    public function newDocumentation(): Response
    {
        $statusbar = [
            'links' => [
                ['title' => 'Liste des documentations', 'url' => $this->generateUrl("app_udocumentation"), 'target' => false],
                ['title' => 'Créer une documentation', 'url' => $this->generateUrl("app_udocumentation_new"), 'target' => false],
            ],
        ];

        return $this->render('udocumentation/new_documentation.html.twig', [
            'dataStatusbar' => $statusbar,
        ]);
    }
}
