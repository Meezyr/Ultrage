<?php

namespace App\Controller;

use App\Entity\Color;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use App\Repository\ColorRepository;

class UCouleurController extends AbstractController
{
    #[Route('/u-couleur', name: 'app_ucouleur')]
    public function index(ManagerRegistry $doctrine, UserRepository $userRepository, ColorRepository $colorRepository): Response
    {
        $couleurs = [];

        if ($this->getUser() != null) {
            $userAuthor = $userRepository->find($this->getUser()->getId());

            $couleurs = $colorRepository->findBy(['userAuthor' => $userAuthor]);
        }

        $users = $userRepository->findAll();

        return $this->render('ucouleur/ucouleur.html.twig', [
            'couleurs' => $couleurs,
            'users' => $users
        ]);
    }

    #[Route('/u-couleur/addcolor', name: 'app_ucouleur_add_color')]
    public function addColor(EntityManagerInterface $entityManager, UserRepository $userRepository, ColorRepository $colorRepository): Response
    {
        if ($this->getUser() != null) {
            $colorCode = $_POST['colorCode'];
            $arrayKeyword = $_POST['arrayKeyword'];

            $keyword = array_merge($arrayKeyword);

            $userAuthor = $userRepository->find($this->getUser()->getId());
            $couleursPaletteNumber = $colorRepository->findBy(['userAuthor' => $userAuthor], ['paletteNumber' => 'DESC']);
            $paletteNumber = $couleursPaletteNumber[0]->getPaletteNumber();

            $color = new Color();
            $color->setPaletteNumber($paletteNumber);
            $color->setColorCode($colorCode);
            $color->setCreationDate(new \DateTime());
            $color->setUserAuthor($this->getUser());
            $color->setKeyword($keyword);

            $entityManager->persist($color);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_ucouleur');
    }
}
