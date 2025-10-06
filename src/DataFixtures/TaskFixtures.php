<?php

namespace App\DataFixtures;

use App\Entity\Task;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class TaskFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $tasks = [
            [
                'title' => 'Faire les courses',
                'description' => 'Acheter du lait, des œufs, du pain et des fruits',
                'isCompleted' => false,
            ],
            [
                'title' => 'Répondre aux emails professionnels',
                'description' => 'Vérifier et répondre aux emails en attente',
                'isCompleted' => true,
            ],
            [
                'title' => 'Préparer la réunion d\'équipe',
                'description' => 'Créer l\'ordre du jour et la présentation',
                'isCompleted' => false,
            ],
            [
                'title' => 'Faire 30 minutes de sport',
                'description' => 'Course à pied ou séance de yoga',
                'isCompleted' => false,
            ],
            [
                'title' => 'Lire le nouveau livre',
                'description' => 'Terminer les chapitres 5 et 6',
                'isCompleted' => true,
            ]
        ];

        foreach ($tasks as $taskData) {
            $task = new Task();
            $task->setTitle($taskData['title']);
            $task->setDescription($taskData['description']);
            $task->setIsCompleted($taskData['isCompleted']);

            $manager->persist($task);
        }

        $manager->flush();
    }
}
