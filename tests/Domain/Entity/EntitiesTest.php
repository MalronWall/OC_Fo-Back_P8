<?php

declare(strict_types=1);

/**
 * (c) Thibaut Tourte <thibaut.tourte17@gmail.com>
 */

namespace App\Tests\Domain\Entity;

use App\Domain\Entity\Task;
use App\Domain\Entity\User;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class EntitiesTest extends KernelTestCase
{
    /** @var Task */
    private $task;

    /**
     * @throws \Doctrine\ORM\Tools\ToolsException
     * @throws \Doctrine\ORM\ORMException
     */
    public function setUp()
    {
        $kernel = self::bootKernel();
        $entityManager = $kernel->getContainer()->get("doctrine.orm.entity_manager");

        $schemaTool = new SchemaTool($entityManager);
        $schemaTool->dropSchema($entityManager->getMetadataFactory()->getAllMetadata());
        $schemaTool->createSchema($entityManager->getMetadataFactory()->getAllMetadata());

        $user = new User();
        $user->setUsername("Username");
        $user->setPassword("password");
        $user->setEmail("email@email.com");
        $user->setRoles(["ROLE_USER", "ROLE_ADMIN"]);

        $entityManager->persist($user);
        $entityManager->flush();

        $this->task = new Task($user);
        $this->task->setTitle("Title");
        $this->task->setContent("The content of the task");

        $entityManager->persist($this->task);
        $entityManager->flush();
    }

    // Task
    public function testTaskGetIdReturnInteger()
    {
        self::assertIsInt($this->task->getId());
    }

    public function testTaskSetGetDateReturnDatetimeObject()
    {
        self::assertInstanceOf(\DateTime::class, $this->task->getCreatedAt());

        $dateTime = new \DateTime();
        $this->task->setCreatedAt($dateTime);

        self::assertInstanceOf(\DateTime::class, $this->task->getCreatedAt());
        self::assertEquals($dateTime->getTimestamp(), $this->task->getCreatedAt()->getTimestamp());
    }

    public function testTaskSetGetTitle()
    {
        self::assertIsString($this->task->getTitle());
        self::assertEquals("Title", $this->task->getTitle());

        $this->task->setTitle("Title 2");

        self::assertIsString($this->task->getTitle());
        self::assertEquals("Title 2", $this->task->getTitle());
    }

    public function testTaskSetGetContent()
    {
        self::assertIsString($this->task->getContent());
        self::assertEquals("The content of the task", $this->task->getContent());

        $this->task->setContent("The content of the task 2");

        self::assertIsString($this->task->getContent());
        self::assertEquals("The content of the task 2", $this->task->getContent());
    }

    // User
    public function testUserGetIdReturnInteger()
    {
        self::assertIsInt($this->task->getUser()->getId());
    }

    public function testUserSetGetUsername()
    {
        self::assertIsString($this->task->getUser()->getUsername());
        self::assertEquals("Username", $this->task->getUser()->getUsername());

        $this->task->getUser()->setUsername("Username 2");

        self::assertIsString($this->task->getUser()->getUsername());
        self::assertEquals("Username 2", $this->task->getUser()->getUsername());
    }

    public function testUserSetGetEmail()
    {
        self::assertIsString($this->task->getUser()->getEmail());
        self::assertEquals("email@email.com", $this->task->getUser()->getEmail());

        $this->task->getUser()->setEmail("email2@email.com");

        self::assertIsString($this->task->getUser()->getEmail());
        self::assertEquals("email2@email.com", $this->task->getUser()->getEmail());
    }

    public function testUserSetGetRoles()
    {
        self::assertIsArray($this->task->getUser()->getRoles());
        self::assertEquals("ROLE_USER", $this->task->getUser()->getRoles()[0]);

        $this->task->getUser()->setRoles(["ROLE_USER", "ROLE_ADMIN"]);

        self::assertIsArray($this->task->getUser()->getRoles());
        self::assertEquals("ROLE_ADMIN", $this->task->getUser()->getRoles()[1]);
    }

    public function testUserGetSaltReturnEmptyString()
    {
        self::assertIsString($this->task->getUser()->getSalt());
        self::assertEmpty($this->task->getUser()->getSalt());
    }

    public function testUserGetEraseCredentialsReturnNull()
    {
        self::assertNull($this->task->getUser()->eraseCredentials());
    }
}
