<?php

namespace App\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TaskController extends AbstractController
{
    // Affichage app
    #[Route('/index', name: 'app_index', methods: ['GET'])]
    public function todo(): Response
    {
        return $this->render('vue_app/index.html.twig');
    }

    // Récupére toutes les tâches
    #[Route('/api/tasks', name: 'api_tasks_list', methods: ['GET'])]
    public function listTasks(TaskRepository $taskRepository, SerializerInterface $serializer): JsonResponse
    {
        $tasks = $taskRepository->findAll();
        $json = $serializer->serialize($tasks, 'json', ['groups' => 'task']);

        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    // Crée nouvelle tâche
    #[Route('/api/tasks', name: 'api_tasks_create', methods: ['POST'])]
    public function createTask(Request $request, EntityManagerInterface $em, SerializerInterface $serializer): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['title']) || empty(trim($data['title']))) {
            return $this->json(['error' => 'Title is required'], Response::HTTP_BAD_REQUEST);
        }

        $task = new Task();
        $task->setTitle($data['title']);
        $task->setDescription($data['description'] ?? '');
        $task->setIsCompleted(false);

        $em->persist($task);
        $em->flush();

        $json = $serializer->serialize($task, 'json', ['groups' => 'task']);
        return new JsonResponse($json, Response::HTTP_CREATED, [], true);
    }

    // Mise à jour tâche
    #[Route('/api/tasks/{id}', name: 'api_tasks_update', methods: ['PUT'])]
    public function updateTask(Task $task, Request $request, EntityManagerInterface $em, SerializerInterface $serializer): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['title']) && !empty(trim($data['title']))) {
            $task->setTitle($data['title']);
        }

        if (isset($data['description'])) {
            $task->setDescription($data['description']);
        }

        if (isset($data['isCompleted'])) {
            $task->setIsCompleted((bool) $data['isCompleted']);
        }

        $em->flush();

        $json = $serializer->serialize($task, 'json', ['groups' => 'task']);
        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    // Supprimer une tâche
    #[Route('/api/tasks/{id}', name: 'api_tasks_delete', methods: ['DELETE'])]
    public function deleteTask(Task $task, EntityManagerInterface $em): JsonResponse
    {
        $em->remove($task);
        $em->flush();

        return $this->json(['message' => 'Task deleted successfully'], Response::HTTP_NO_CONTENT);
    }
}
