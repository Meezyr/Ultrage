<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskFormType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UTacheController extends AbstractController
{
    #[Route('/u-tache', name: 'app_utache')]
    public function index(TaskRepository $taskRepository): Response
    {
        $statusbar = [
            'links' => [
                ['title' => 'Tableau des tâches', 'url' => $this->generateUrl("app_utache"), 'target' => false],
            ],
        ];

        if ($this->getUser() != null) {
            $statusbar['links'][] = ['title' => 'Créer un tâche', 'url' => $this->generateUrl("app_utache_new"), 'target' => false];
        }

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
    public function newTask(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser() == null) {
            return $this->redirectToRoute('app_utache');
        }

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

    #[Route('/u-tache/modifier-statut', name: 'app_utache_modify_status')]
    public function modifyStatusTask(EntityManagerInterface $entityManager, TaskRepository $taskRepository): RedirectResponse
    {
        if ($this->getUser() != null) {
            $idTask = $_POST['idTask'];
            $moveStatus = $_POST['moveStatus'];

            $task = $taskRepository->find($idTask);
            $task->setStatus($moveStatus);
            $task->setUpdateDate(new \DateTime());

            $entityManager->flush();
        }

        return $this->redirectToRoute('app_utache');
    }
}
