<?php

namespace App\Controller;

use App\Entity\Link;
use App\Form\LinkFormType;
use App\Repository\LinkRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ULienController extends AbstractController
{
    #[Route('/u-lien', name: 'app_ulien')]
    public function index(Request $request, LinkRepository $linkRepository): Response
    {
        $criteria = [];

        if (!empty($search = $request->query->get('recherche'))) {
            $criteria['search'] = $search;
        }

        if (!empty($category = $request->query->get('categorie'))) {
            $criteria['category'] = $category;
        }

        $links = $linkRepository->findAllByCriteria($criteria);

        $nbLinks = count($links);

        $allCategory = [];
        foreach ($links as $link) {
            if (!empty($link->getCategories())) {
                foreach ($link->getCategories() as $category) {
                    if (!in_array(strtolower($category), $allCategory)) {
                        $allCategory[] = strtolower($category);
                    }
                }
            }
        }

        return $this->render('ulien/ulien.html.twig', [
            'dataStatusbar' => $this->getStatusbar(),
            'nbLinks' => $nbLinks,
            'categories' => $allCategory,
            'criteria' => $criteria
        ]);
    }

    #[Route('/u-lien/ordre', name: 'app_ulien_order')]
    public function allLinkOrder(Request $request, LinkRepository $linkRepository): JsonResponse
    {
        $listLinks = [];
        if (!empty($orderName = $request->query->get('orderBy'))) {

            $criteria = [];

            if (!empty($search = $request->query->get('recherche'))) {
                $criteria['search'] = urldecode($search);
            }

            if (!empty($category = $request->query->get('categorie'))) {
                $criteria['category'] = urldecode($category);
            }

            $criteria['order'] = $orderName;

            $docs = $linkRepository->findAllByCriteria($criteria);

            $listLinks = $this->getArr($docs, $listLinks);
        }

        return $this->json($listLinks);
    }

    #[Route('/u-lien/info-lien', name: 'app_ulien_info')]
    #[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
    public function infoOneLink(Request $request, LinkRepository $linkRepository): JsonResponse
    {
        $listLinks = [];

        if (!empty($id = $request->query->get('id'))) {
            $link = $linkRepository->findOneBy(['id' => $id]);

            $listLinks = [
                'id' => $link->getId(),
                'title' => $link->getTitle(),
                'description' => !empty($link->getDescription()) ? $link->getDescription() : null,
                'url' => $link->getUrl(),
                'releaseDate' => $link->getReleaseDate()->format('Y-m-d'),
                'releaseDateLong' => $link->getReleaseDate()->format('d/m/Y à H:i'),
                'updateDate' => !empty($link->getUpdateDate()) ? $link->getUpdateDate()->format('Y-m-d') : null,
                'updateDateLong' => !empty($link->getUpdateDate()) ? $link->getUpdateDate()->format('d/m/Y à H:i') : null,
                'categories' => !empty($link->getCategories()) ? $link->getCategories() : null,
                'author' => $link->getAuthor()->getPseudo()
            ];
        }

        return $this->json($listLinks);
    }

    /**
     * @throws Exception
     */
    #[Route('/u-lien/nouveau-lien', name: 'app_ulien_new')]
    #[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
    public function newLink(Request $request, EntityManagerInterface $entityManager, LinkRepository $linkRepository): Response
    {
        $link = new Link();
        $form = $this->createForm(LinkFormType::class, $link);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $arrayData = explode(',', $form->get('categories')->getData());

            $date = new \DateTime();
            $date->setTimezone(new \DateTimeZone('Europe/Paris'));

            $link->setReleaseDate($date);
            $link->setAuthor($this->getUser());
            $link->setCategories($arrayData);

            $entityManager->persist($link);
            $entityManager->flush();

            return $this->redirectToRoute('app_ulien_user');
        }

        return $this->render('ulien/new_lien.html.twig', [
            'dataStatusbar' => $this->getStatusbar(),
            'newLinkForm' => $form->createView(),
        ]);
    }

    #[Route('/u-lien/modifier-lien/{id}', name: 'app_ulien_modify')]
    #[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
    public function modifyLink(Link $link, Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser() === $link->getAuthor()) {
            $form = $this->createForm(LinkFormType::class, $link);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $arrayData = explode(',', $form->get('categories')->getData());

                $date = new \DateTime();
                $date->setTimezone(new \DateTimeZone('Europe/Paris'));

                $link->setUpdateDate($date);
                $link->setCategories($arrayData);

                $entityManager->flush();

                return $this->redirectToRoute('app_ulien_user');
            }

            return $this->render('ulien/new_lien.html.twig', [
                'dataStatusbar' => $this->getStatusbar(),
                'newLinkForm' => $form->createView(),
            ]);
        }

        return $this->redirectToRoute('app_ulien_user');
    }

    #[Route('/u-lien/supprimer-lien/{id}', name: 'app_ulien_delete')]
    #[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
    public function deleteLink(Link $link, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser() === $link->getAuthor()) {
            $entityManager->remove($link);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_ulien_user');
    }

    #[Route('/u-lien/mes-liens', name: 'app_ulien_user')]
    #[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
    public function userLink(LinkRepository $linkRepository): Response
    {
        $links = $linkRepository->findBy(['author' => $this->getUser()], ['release_date' => 'DESC']);

        $listLinks = $this->getArr($links, []);

        return $this->render('ulien/user_lien.html.twig', [
            'dataStatusbar' => $this->getStatusbar(),
            'links' => $listLinks,
        ]);
    }


    /**
     * @param array $links
     * @param array $listLinks
     * @return array
     */
    public function getArr(array $links, array $listLinks): array
    {
        foreach ($links as $link) {
            $listLinks[] = [
                'id' => $link->getId(),
                'title' => $link->getTitle(),
                'description' => !empty($link->getDescription()) ? $link->getDescription() : null,
                'url' => $link->getUrl(),
                'releaseDate' => $link->getReleaseDate()->format('Y-m-d'),
                'releaseDateLong' => $link->getReleaseDate()->format('d/m/Y à H:i'),
                'updateDate' => !empty($link->getUpdateDate()) ? $link->getUpdateDate()->format('Y-m-d') : null,
                'updateDateLong' => !empty($link->getUpdateDate()) ? $link->getUpdateDate()->format('d/m/Y à H:i') : null,
                'categories' => !empty($link->getCategories()) ? $link->getCategories() : null,
                'author' => $link->getAuthor()->getPseudo()
            ];
        }
        return $listLinks;
    }

    public function getStatusbar(): array
    {
        return [
            'links' => [
                ['title' => 'Liste des liens', 'url' => $this->generateUrl("app_ulien"), 'target' => false],
                ['title' => 'Créer un lien', 'url' => $this->generateUrl("app_ulien_new"), 'target' => false],
                ['title' => 'Mes liens', 'url' => $this->generateUrl("app_ulien_user"), 'target' => false],
            ],
        ];
    }
}
