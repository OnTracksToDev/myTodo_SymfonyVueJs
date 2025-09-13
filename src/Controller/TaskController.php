<?php

namespace App\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TaskController extends AbstractController
{
    // Affichage app
    #[Route('/', name: 'app_index', methods: ['GET'])]
    public function todo(): Response
    {
        return $this->render('vue_app/index.html.twig');
    }

    // Récupére toutes les tâches avec filtre et tri
    #[Route('/api/tasks', name: 'api_tasks_list', methods: ['GET'])]
    public function listTasks(Request $request, TaskRepository $taskRepository, SerializerInterface $serializer): JsonResponse
    {
        $filter = $request->query->get('filter', 'all'); 
        $sort   = $request->query->get('sort', 'date_asc'); 
        $qb = $taskRepository->createQueryBuilder('t');

        // Filtrage
        if ($filter === 'active') {
            $qb->andWhere('t.isCompleted = false');
        } elseif ($filter === 'done') {
            $qb->andWhere('t.isCompleted = true');
        }

        // Tri
        if ($sort === 'date_asc') {
            $qb->orderBy('t.createdAt', 'ASC');
        } elseif ($sort === 'date_desc') {
            $qb->orderBy('t.createdAt', 'DESC');
        } elseif ($sort === 'status') {
            $qb->orderBy('t.isCompleted', 'ASC');
        }

        $tasks = $qb->getQuery()->getResult();
        $json = $serializer->serialize($tasks, 'json', ['groups' => 'task']);

        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

    // Crée nouvelle tâche
    #[Route('/api/tasks', name: 'api_tasks_create', methods: ['POST'])]
    public function createTask(Request $request, EntityManagerInterface $em, SerializerInterface $serializer, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $task = new Task();
        $task->setTitle($data['title'] ?? null);
        $task->setDescription($data['description'] ?? '');
        $task->setIsCompleted(false);
        $task->setCreatedAt(new \DateTime());
        $task->setUpdatedAt(new \DateTime());

        // Validation Validator
        $errors = $validator->validate($task);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getPropertyPath() . ': ' . $error->getMessage();
            }
            return $this->json(['errors' => $errorMessages], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $em->persist($task);
        $em->flush();

        $json = $serializer->serialize($task, 'json', ['groups' => 'task']);
        return new JsonResponse(json_decode($json, true), Response::HTTP_CREATED);
    }

    // Mise à jour tâche
    #[Route('/api/tasks/{id}', name: 'api_tasks_update', methods: ['PUT'])]
    public function updateTask(Task $task, Request $request, EntityManagerInterface $em, SerializerInterface $serializer, ValidatorInterface $validator): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['title']) && !empty(trim($data['title']))) {
            $task->setTitle($data['title']);
        }

        if (isset($data['description'])) {
            $task->setDescription($data['description']);
        }

        if (isset($data['isCompleted'])) {
            $task->setIsCompleted((bool)$data['isCompleted']);
        }

        $task->setUpdatedAt(new \DateTime());

        // Validation Validator
        $errors = $validator->validate($task);
        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[] = $error->getPropertyPath() . ': ' . $error->getMessage();
            }
            return $this->json(['errors' => $errorMessages], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $em->flush();

        $json = $serializer->serialize($task, 'json', ['groups' => 'task']);
        return new JsonResponse(json_decode($json, true), Response::HTTP_OK);
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
