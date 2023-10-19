<?php

namespace App\Controller;

use App\Entity\Color;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserRepository;
use App\Repository\ColorRepository;

class UCouleurController extends AbstractController
{
    #[Route('/u-couleur/{code?null}', name: 'app_ucouleur')]
    public function index(string $code, ManagerRegistry $doctrine, UserRepository $userRepository, ColorRepository $colorRepository): Response
    {
        $couleurs = [];

        if ($this->getUser() != null) {
            $userAuthor = $userRepository->find($this->getUser()->getId());

            $couleurs = $colorRepository->findBy(['userAuthor' => $userAuthor]);
        }

        $users = $userRepository->findAll();

        $statusbar = ['links' => [],];

        return $this->render('ucouleur/ucouleur.html.twig', [
            'couleurs' => $couleurs,
            'users' => $users,
            'dataStatusbar' => $statusbar,
            'code' => $code,
        ]);
    }

    #[Route('/color/addcolor', name: 'app_ucouleur_add_color')]
    #[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
    public function addColor(EntityManagerInterface $entityManager): JsonResponse
    {
        $colorCode = $_POST['colorCode'];
        $arrayKeyword = $_POST['arrayKeyword'];

        $keyword = array_merge($arrayKeyword);

        $color = new Color();
        $color->setColorCode($colorCode);
        $color->setCreationDate(new \DateTime());
        $color->setUserAuthor($this->getUser());
        $color->setKeyword($keyword);

        $entityManager->persist($color);
        $entityManager->flush();

        return $this->json([$arrayKeyword, $colorCode]);
    }
}
