<?php

namespace App\Controller;

use App\Entity\Documentation;
use App\Form\DocumentationFormType;
use App\Repository\DocumentationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UDocumentationController extends AbstractController
{
    #[Route('/u-documentation', name: 'app_udocumentation')]
    public function index(Request $request, DocumentationRepository $documentationRepository): Response
    {
        $criteria = [];

        if (!empty($search = $request->query->get('recherche'))) {
            $criteria['search'] = $search;
        }

        if (!empty($category = $request->query->get('categorie'))) {
            $criteria['category'] = $category;
        }

        $docs = $documentationRepository->findAllByCriteria($criteria);

        $listDocs = [];
        $allCategory = [];
        foreach ($docs as $doc) {
            $listDocs[] = [
                'id' => $doc->getId(),
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
            'categories' => $allCategory,
            'criteria' => $criteria
        ]);
    }

    #[Route('/u-documentation/ordre', name: 'app_udocumentation_order')]
    public function allDocumentationOrder(Request $request, DocumentationRepository $documentationRepository): JsonResponse
    {
        $listDocs = [];
        if (!empty($orderName = $request->query->get('orderBy'))) {

            $criteria = [];

            if (!empty($search = $request->query->get('recherche'))) {
                $criteria['search'] = urldecode($search);
            }

            if (!empty($category = $request->query->get('categorie'))) {
                $criteria['category'] = urldecode($category);
            }

            $criteria['order'] = $orderName;

            $docs = $documentationRepository->findAllByCriteria($criteria);

            foreach ($docs as $doc) {
                $listDocs[] = [
                    'id' => $doc->getId(),
                    'title' => $doc->getTitle(),
                    'excerpt' => !empty($doc->getExcerpt()) ? $doc->getExcerpt() : null,
                    'releaseDate' => $doc->getReleaseDate()->format('Y-m-d'),
                    'releaseDateLong' => $doc->getReleaseDate()->format('d/m/Y à H:i'),
                    'updateDate' => !empty($doc->getUpdateDate()) ? $doc->getUpdateDate()->format('Y-m-d') : null,
                    'updateDateLong' => !empty($doc->getUpdateDate()) ? $doc->getUpdateDate()->format('d/m/Y à H:i') : null,
                    'category' => !empty($doc->getExcerpt()) ? $doc->getCategory() : null,
                    'author' => $doc->getAuthor()->getPseudo()
                ];
            }
        }

        return $this->json($listDocs);
    }

    #[Route('/u-documentation/nouvelle-documentation', name: 'app_udocumentation_new')]
    #[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
    public function newDocumentation(Request $request, EntityManagerInterface $entityManager): Response
    {
        $doc = new Documentation();
        $form = $this->createForm(DocumentationFormType::class, $doc);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $arrayData = explode(',', $form->get('categories')->getData());

            $date = new \DateTime();
            $date->setTimezone(new \DateTimeZone('Europe/Paris'));

            $doc->setReleaseDate($date);
            $doc->setAuthor($this->getUser());
            $doc->setCategory($arrayData);

            $entityManager->persist($doc);
            $entityManager->flush();

            return $this->redirectToRoute('app_udocumentation');
        }

        $statusbar = [
            'links' => [
                ['title' => 'Liste des documentations', 'url' => $this->generateUrl("app_udocumentation"), 'target' => false],
                ['title' => 'Créer une documentation', 'url' => $this->generateUrl("app_udocumentation_new"), 'target' => false],
            ],
        ];

        return $this->render('udocumentation/new_documentation.html.twig', [
            'dataStatusbar' => $statusbar,
            'newDocumentationForm' => $form->createView(),
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
