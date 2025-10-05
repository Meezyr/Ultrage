<?php

namespace App\Controller;

use App\Repository\ColorRepository;
use App\Repository\DocumentationRepository;
use App\Repository\LinkRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AccueilController extends AbstractController
{
    #[Route('/', name: 'app_accueil')]
    public function index(ColorRepository $colorRepository, DocumentationRepository $documentationRepository, LinkRepository $linkRepository): Response
    {
        $colors = $colorRepository->findFourOrderByDate('DESC');

        $docs = $documentationRepository->findFourOrderByDate('DESC');

        $links = $linkRepository->findFourOrderByDate('DESC');

        return $this->render('accueil/accueil.html.twig', [
            'colors' => $colors,
            'docs' => $docs,
            'links' => $links,
        ]);
    }
}
