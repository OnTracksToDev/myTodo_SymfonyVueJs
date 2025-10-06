<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use PHPUnit\Framework\TestCase;
use DateTimeInterface;

class TaskTest extends TestCase
{
    public function testInitialValues(): void
    {
        $task = new Task();

        $this->assertNull($task->getId(), 'L’ID doit être nul avant la persistance.');
        $this->assertFalse($task->getIsCompleted(), 'Une nouvelle tâche doit être non complétée.');
        $this->assertInstanceOf(\DateTimeInterface::class, $task->getCreatedAt(), 'createdAt doit être un DateTime valide.');
        $this->assertInstanceOf(\DateTimeInterface::class, $task->getUpdatedAt(), 'updatedAt doit être un DateTime valide.');
    }

    public function testSetAndGetTitle(): void
    {
        $task = new Task();
        $title = 'Faire les courses';
        $task->setTitle($title);

        $this->assertSame($title, $task->getTitle());
    }

    public function testSetAndGetDescription(): void
    {
        $task = new Task();
        $description = 'Acheter du lait, du pain et du fromage.';
        $task->setDescription($description);

        $this->assertSame($description, $task->getDescription());
    }

    public function testSetAndGetIsCompleted(): void
    {
        $task = new Task();
        $task->setIsCompleted(true);

        $this->assertTrue($task->getIsCompleted());
    }

    public function testSetAndGetCreatedAtAndUpdatedAt(): void
    {
        $task = new Task();

        $newCreatedAt = new \DateTime('-1 day');
        $newUpdatedAt = new \DateTime();

        $task->setCreatedAt($newCreatedAt);
        $task->setUpdatedAt($newUpdatedAt);

        $this->assertSame($newCreatedAt, $task->getCreatedAt());
        $this->assertSame($newUpdatedAt, $task->getUpdatedAt());
    }
}
