<?php

namespace App\Controller;

use App\Entity\Documentation;
use App\Form\DocumentationFormType;
use App\Repository\DocumentationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\DomCrawler\Crawler;

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

        $nbDocs = count($docs);

        $allCategory = [];
        foreach ($docs as $doc) {
            if (!empty($doc->getCategory())) {
                foreach ($doc->getCategory() as $category) {
                    if (!in_array(strtolower($category), $allCategory)) {
                        $allCategory[] = strtolower($category);
                    }
                }
            }
        }

        return $this->render('udocumentation/udocumentation.html.twig', [
            'dataStatusbar' => $this->getStatusbar(),
            'nbDocs' => $nbDocs,
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

            $listDocs = $this->getArr($docs, $listDocs);
        }

        return $this->json($listDocs);
    }

    #[Route('/u-documentation/info-documentation', name: 'app_udocumentation_info')]
    #[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
    public function infoOneDocumentation(Request $request, DocumentationRepository $documentationRepository): JsonResponse
    {
        $listDocs = [];

        if (!empty($slug = $request->query->get('slug'))) {
            $doc = $documentationRepository->findOneBy(['slug' => $slug]);

            $listDocs = [
                'id' => $doc->getId(),
                'slug' => $doc->getSlug(),
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

        return $this->json($listDocs);
    }

    /**
     * @throws Exception
     */
    #[Route('/u-documentation/nouvelle-documentation', name: 'app_udocumentation_new')]
    #[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
    public function newDocumentation(Request $request, EntityManagerInterface $entityManager, DocumentationRepository $documentationRepository): Response
    {
        $doc = new Documentation();
        $form = $this->createForm(DocumentationFormType::class, $doc);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $arrayData = explode(',', $form->get('categories')->getData());

            $date = new \DateTime();
            $date->setTimezone(new \DateTimeZone('Europe/Paris'));

            $slugUnique = $this->getSlug($form->get('title')->getData(), $documentationRepository);

            $doc->setReleaseDate($date);
            $doc->setAuthor($this->getUser());
            $doc->setCategory($arrayData);
            $doc->setSlug($slugUnique);

            $entityManager->persist($doc);
            $entityManager->flush();

            return $this->redirectToRoute('app_udocumentation_view', ['slug' => $doc->getSlug()]);
        }

        return $this->render('udocumentation/new_documentation.html.twig', [
            'dataStatusbar' => $this->getStatusbar(),
            'newDocumentationForm' => $form->createView(),
        ]);
    }

    #[Route('/u-documentation/modifier-documentation/{slug}', name: 'app_udocumentation_modify')]
    #[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
    public function modifyDocumentation(Documentation $documentation, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser() === $documentation->getAuthor()) {
            $form = $this->createForm(DocumentationFormType::class, $documentation);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $arrayData = explode(',', $form->get('categories')->getData());

                $date = new \DateTime();
                $date->setTimezone(new \DateTimeZone('Europe/Paris'));

                $documentation->setUpdateDate($date);
                $documentation->setCategory($arrayData);

                $entityManager->flush();

                return $this->redirectToRoute('app_udocumentation_view', ['slug' => $documentation->getSlug()]);
            }

            return $this->render('udocumentation/new_documentation.html.twig', [
                'dataStatusbar' => $this->getStatusbar(),
                'newDocumentationForm' => $form->createView(),
            ]);
        }

        return $this->redirectToRoute('app_udocumentation_user');
    }

    #[Route('/u-documentation/supprimer-documentation/{id}', name: 'app_udocumentation_delete')]
    #[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
    public function deleteDocumentation(Documentation $documentation, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser() === $documentation->getAuthor()) {
            $entityManager->remove($documentation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_udocumentation_user');
    }

    #[Route('/u-documentation/documentation/{slug}', name: 'app_udocumentation_view')]
    public function viewDocumentation(Documentation $documentation): Response
    {
        $crawler = new Crawler($documentation->getText());
        $crawlerTitles = $crawler->filter('h2, h3')->each(function (Crawler $node, $i): array {
            return ['node' => $node->nodeName(), 'text' => $node->text()];
        });

        $summary = [];
        foreach ($crawlerTitles as $crawlerTitle) {
            if ($crawlerTitle['node'] == 'h2') {
                $summary[] = ['text' => $crawlerTitle['text'], 'child' => []];
            } elseif ($crawlerTitle['node'] == 'h3') {
                $summary[array_key_last($summary)]['child'][] = ['text' => $crawlerTitle['text']];
            }
        }

        $doc = [
            'id' => $documentation->getId(),
            'slug' => $documentation->getSlug(),
            'title' => $documentation->getTitle(),
            'excerpt' => !empty($documentation->getExcerpt()) ? $documentation->getExcerpt() : null,
            'text' => $documentation->getText(),
            'summary' => $summary,
            'releaseDate' => $documentation->getReleaseDate()->format('c'),
            'releaseDateLong' => $documentation->getReleaseDate()->format('d/m/Y à H:i'),
            'updateDate' => !empty($documentation->getUpdateDate()) ? $documentation->getUpdateDate()->format('c') : null,
            'updateDateLong' => !empty($documentation->getUpdateDate()) ? $documentation->getUpdateDate()->format('d/m/Y à H:i') : null,
            'category' => !empty($documentation->getExcerpt()) ? implode(", ", $documentation->getCategory()) : null,
            'author' => [
                'id' => $documentation->getAuthor()->getId(),
                'pseudo' => $documentation->getAuthor()->getPseudo(),
                'avatar' => !empty($documentation->getAuthor()->getAvatar()) ? $documentation->getAuthor()->getAvatar() : null,
            ]
        ];

        return $this->render('udocumentation/view_documentation.html.twig', [
            'dataStatusbar' => $this->getStatusbar(),
            'doc' => $doc,
        ]);
    }

    #[Route('/u-documentation/mes-documentations', name: 'app_udocumentation_user')]
    #[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
    public function userDocumentation(DocumentationRepository $documentationRepository): Response
    {
        $docsNotPublish = $documentationRepository->findBy(['publish' => false, 'author' => $this->getUser()], ['release_date' => 'DESC']);
        $docsPublish = $documentationRepository->findBy(['publish' => true, 'author' => $this->getUser()], ['release_date' => 'DESC']);

        $listDocsNotPublish = $this->getArr($docsNotPublish, []);
        $listDocsPublish = $this->getArr($docsPublish, []);

        $listDocs = [
            'notPublish' => $listDocsNotPublish,
            'publish' => $listDocsPublish,
        ];

        return $this->render('udocumentation/user_documentation.html.twig', [
            'dataStatusbar' => $this->getStatusbar(),
            'docs' => $listDocs,
        ]);
    }


    /**
     * @param array $docs
     * @param array $listDocs
     * @return array
     */
    public function getArr(array $docs, array $listDocs): array
    {
        foreach ($docs as $doc) {
            $listDocs[] = [
                'id' => $doc->getId(),
                'slug' => $doc->getSlug(),
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
        return $listDocs;
    }

    public function getStatusbar(): array
    {
        return [
            'links' => [
                ['title' => 'Liste des documentations', 'url' => $this->generateUrl("app_udocumentation"), 'target' => false],
                ['title' => 'Créer une documentation', 'url' => $this->generateUrl("app_udocumentation_new"), 'target' => false],
                ['title' => 'Mes documentations', 'url' => $this->generateUrl("app_udocumentation_user"), 'target' => false],
            ],
        ];
    }

    /**
     * @param string $title
     * @param $documentationRepository
     * @return string
     * @throws Exception
     */
    public function getSlug(string $title, $documentationRepository): string
    {
        $slugger = new AsciiSlugger();
        $slug = strtolower($slugger->slug($title, '-'));

        $docsSearchSlug = $documentationRepository->findBy(['slug' => $slug]);

        $slugTitle = $slug;

        if (!empty($docsSearchSlug)) {
            do {
                $slugTitle = $slug.'-'.random_int(1,50);
            } while (!empty($documentationRepository->findBy(['slug' => $slugTitle])));
        }

        return $slugTitle;
    }
}
