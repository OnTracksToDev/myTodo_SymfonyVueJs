<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TaskValidationTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->validator = static::getContainer()->get(ValidatorInterface::class);
    }

    private function getErrors(Task $task)
    {
        return $this->validator->validate($task);
    }

    public function testValidTask(): void
    {
        $task = (new Task())
            ->setTitle('Faire les courses')
            ->setDescription('Acheter du pain et du lait')
            ->setIsCompleted(false);

        $errors = $this->getErrors($task);
        $this->assertCount(0, $errors, 'Une tâche valide ne doit pas générer d’erreurs de validation.');
    }

    public function testTitleIsRequired(): void
    {
        $task = (new Task())
            ->setTitle('')
            ->setDescription('Test sans titre');

        $errors = $this->getErrors($task);
        $this->assertGreaterThan(0, count($errors), 'Le titre vide doit générer une erreur.');
    }

    public function testTitleTooLong(): void
    {
        $task = (new Task())
            ->setTitle(str_repeat('A', 60)) // plus de 50 caractères
            ->setDescription('Trop long');

        $errors = $this->getErrors($task);
        $this->assertGreaterThan(0, count($errors), 'Un titre trop long doit générer une erreur.');
    }

    public function testDescriptionTooLong(): void
    {
        $task = (new Task())
            ->setTitle('Tâche normale')
            ->setDescription(str_repeat('B', 200)); // dépasse la limite de 150 caractères

        $errors = $this->getErrors($task);
        $this->assertGreaterThan(0, count($errors), 'Une description trop longue doit générer une erreur.');
    }

    public function testIsCompletedMustBeBoolean(): void
    {
        $task = (new Task())
            ->setTitle('Tâche')
            ->setDescription('Test')
            ->setIsCompleted(false);

        $errors = $this->getErrors($task);
        $this->assertCount(0, $errors, 'Un booléen valide ne doit pas générer d’erreur.');
    }
    public function testTitleBlankShowsCorrectMessage(): void
    {
        $task = (new Task())
            ->setTitle('')
            ->setDescription('Test sans titre');

        $errors = $this->getErrors($task);

        $this->assertGreaterThan(0, count($errors), 'Un titre vide doit générer une erreur.');

        $this->assertSame(
            'Le titre est obligatoire',
            $errors[0]->getMessage(),
            'Le message d’erreur attendu pour un titre vide est incorrect.'
        );
    }

    public function testTitleTooLongShowsCorrectMessage(): void
    {
        $task = (new Task())
            ->setTitle(str_repeat('A', 60)) // plus de 50 caractères
            ->setDescription('Test longueur titre');

        $errors = $this->getErrors($task);

        $this->assertGreaterThan(0, count($errors), 'Un titre trop long doit générer une erreur.');

        $this->assertSame(
            'Le titre ne peut pas dépasser 50 caractères',
            $errors[0]->getMessage(),
            'Le message d’erreur attendu pour un titre trop long est incorrect.'
        );
    }

    public function testDescriptionTooLongShowsCorrectMessage(): void
    {
        $task = (new Task())
            ->setTitle('Tâche correcte')
            ->setDescription(str_repeat('B', 200));

        $errors = $this->getErrors($task);

        $this->assertGreaterThan(0, count($errors), 'Une description trop longue doit générer une erreur.');

        $this->assertSame(
            'La description ne peut pas dépasser 150 caractères',
            $errors[0]->getMessage(),
            'Le message d’erreur attendu pour une description trop longue est incorrect.'
        );
    }
}
