<?php

namespace App\Controller;

use App\Entity\Documentation;
use App\Repository\DocumentationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UDocumentationController extends AbstractController
{
    #[Route('/u-documentation', name: 'app_udocumentation')]
    public function index(DocumentationRepository $documentationRepository): Response
    {
        $docs = $documentationRepository->findBy(['publish' => true]);

        $listDocs = [];
        $allCategory = [];
        foreach ($docs as $doc) {
            $listDocs[] = [
                'id' => $doc->getId(),
                'title' => $doc->getTitle(),
                'excerpt' => $doc->getExcerpt(),
                'releaseDate' => $doc->getReleaseDate(),
                'updateDate' => $doc->getUpdateDate(),
                'category' => $doc->getCategory(),
                'author' => $doc->getAuthor()->getPseudo()
            ];

            if (!empty($doc->getCategory())) {
                foreach ($doc->getCategory() as $category) {
                    if (!in_array(strtolower($category), $allCategory)) {
                        $allCategory[] = strtolower($category);
                    }
                }
            }
        }

        $statusbar = [
            'links' => [
                ['title' => 'Liste des documentations', 'url' => $this->generateUrl("app_udocumentation"), 'target' => false],
                ['title' => 'Créer une documentation', 'url' => $this->generateUrl("app_udocumentation_new"), 'target' => false],
            ],
        ];

        return $this->render('udocumentation/udocumentation.html.twig', [
            'dataStatusbar' => $statusbar,
            'docs' => $listDocs,
            'categories' => $allCategory
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

    #[Route('/u-documentation/documentation/{id}', name: 'app_udocumentation_view')]
    public function viewDocumentation(Documentation $documentation): Response
    {
        dump($documentation);

        $statusbar = [
            'links' => [
                ['title' => 'Liste des documentations', 'url' => $this->generateUrl("app_udocumentation"), 'target' => false],
                ['title' => 'Créer une documentation', 'url' => $this->generateUrl("app_udocumentation_new"), 'target' => false],
            ],
        ];

        return $this->render('udocumentation/view_documentation.html.twig', [
            'dataStatusbar' => $statusbar,
        ]);
    }
}
