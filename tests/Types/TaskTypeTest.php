<?php

declare(strict_types=1);

/**
 * (c) Thibaut Tourte <thibaut.tourte17@gmail.com>
 */

namespace App\Tests\Types;

use App\Domain\Entity\Task;
use App\Domain\Entity\User;
use App\Form\TaskType;
use Symfony\Component\Form\Test\TypeTestCase;

class TaskTypeTest extends TypeTestCase
{
    public function testBuildFormReturn()
    {
        $task = new Task(new User());
        $form = $this->factory->create(TaskType::class, $task);
        $datas = [
            "title" => "Title of the task",
            "content" => "The content of the task"
        ];

        $form->submit($datas);

        self::assertEquals($datas["title"], $task->getTitle());
        self::assertEquals($datas["content"], $task->getContent());
    }
}
