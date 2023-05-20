<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskFormType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UTacheController extends AbstractController
{
    #[Route('/u-tache', name: 'app_utache')]
    #[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
    public function index(TaskRepository $taskRepository): Response
    {
        $statusbar = [
            'links' => [
                ['title' => 'Tableau des tâches', 'url' => $this->generateUrl("app_utache"), 'target' => false],
                ['title' => 'Créer un tâche', 'url' => $this->generateUrl("app_utache_new"), 'target' => false]
            ],
        ];

        $allTask = $taskRepository->findBy(['editor' => $this->getUser()]);

        $arrayTask = [];
        foreach ($allTask as $task) {

            for ($i=0; $i<=3; $i++) {
                if ($task->getStatus() == $i) {
                    $arrayTask[$i][] = [
                        'id' => $task->getId(),
                        'status' => $task->getStatus(),
                        'title' => (strlen($task->getTitle()) > 26) ? substr($task->getTitle(),0,23).'...' : $task->getTitle(),
                        'state' => $task->getState(),
                        'release' => $task->getReleaseDate(),
                        'update' => $task->getUpdateDate(),
                    ];
                }
            }

        }

        return $this->render('utache/utache.html.twig', [
            'dataStatusbar' => $statusbar,
            'tasks' => $arrayTask,
        ]);
    }

    #[Route('/u-tache/nouvelle-tache', name: 'app_utache_new')]
    #[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
    public function newTask(Request $request, EntityManagerInterface $entityManager): Response
    {
        $task = new Task();
        $form = $this->createForm(TaskFormType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setReleaseDate(new \DateTime());
            $task->setEditor($this->getUser());

            $entityManager->persist($task);
            $entityManager->flush();

            return $this->redirectToRoute('app_utache');
        }

        $statusbar = [
            'links' => [
                ['title' => 'Tableau des tâches', 'url' => $this->generateUrl("app_utache"), 'target' => false],
                ['title' => 'Créer un tâche', 'url' => $this->generateUrl("app_utache_new"), 'target' => false],
            ],
        ];

        return $this->render('utache/new_task.html.twig', [
            'dataStatusbar' => $statusbar,
            'newTaskForm' => $form->createView(),
        ]);
    }

    #[Route('/u-tache/modifier-tache/{id}', name: 'app_utache_modify')]
    #[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
    public function modifyTask(Task $task, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TaskFormType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setUpdateDate(new \DateTime());

            $entityManager->flush();

            return $this->redirectToRoute('app_utache');
        }

        $statusbar = [
            'links' => [
                ['title' => 'Tableau des tâches', 'url' => $this->generateUrl("app_utache"), 'target' => false],
                ['title' => 'Créer un tâche', 'url' => $this->generateUrl("app_utache_new"), 'target' => false],
            ],
        ];

        return $this->render('utache/new_task.html.twig', [
            'dataStatusbar' => $statusbar,
            'newTaskForm' => $form->createView(),
        ]);
    }

    #[Route('/u-tache/modifier-statut', name: 'app_utache_modify_status')]
    #[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
    public function modifyStatusTask(EntityManagerInterface $entityManager, TaskRepository $taskRepository): RedirectResponse
    {
        $idTask = $_POST['idTask'];
        $moveStatus = $_POST['moveStatus'];

        $task = $taskRepository->find($idTask);
        $task->setStatus($moveStatus);
        $task->setUpdateDate(new \DateTime());

        $entityManager->flush();

        return $this->redirectToRoute('app_utache');
    }

    #[Route('/u-tache/commentaires', name: 'app_utache_all_comments')]
    #[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
    public function allCommentsTask(EntityManagerInterface $entityManager, TaskRepository $taskRepository): JsonResponse
    {
        $id = $_POST['id'];

        $allComments = [];

        if (!empty($id)) {
            $task = $taskRepository->find($id);
            $allComments = $task->getComment();
        }

        return $this->json($allComments);
    }

    #[Route('/u-tache/ajouter-commentaire', name: 'app_utache_add_comment')]
    #[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
    public function addCommentTask(EntityManagerInterface $entityManager, TaskRepository $taskRepository): JsonResponse
    {
        $id = $_POST['id'];
        $comment = $_POST['comment'];

        $allComments = null;

        if (!empty($id) && !empty($comment)) {
            $currentDate = new \DateTime();

            $task = $taskRepository->find($id);
            $allComments = $task->getComment();
            $allComments[] = [
                'text' => strip_tags($comment),
                'date' => $currentDate->format('Y-m-d H:i:s'),
                'author' => $this->getUser()->getId(),
            ];
            $task->setComment($allComments);
            $task->setUpdateDate(new \DateTime());

            $entityManager->flush();
        }

        return $this->json($allComments);
    }
}
