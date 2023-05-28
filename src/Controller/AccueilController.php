<?php

namespace App\Controller;

use App\Repository\ColorRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AccueilController extends AbstractController
{
    #[Route('/', name: 'app_accueil')]
    public function index(ColorRepository $colorRepository): Response
    {
        $colors = $colorRepository->findAllOrderByDate('DESC');

        return $this->render('accueil/accueil.html.twig', [
            'colors' => $colors,
        ]);
    }
}
