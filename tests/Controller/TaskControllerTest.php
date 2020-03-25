<?php

declare(strict_types=1);

/**
 * (c) Thibaut Tourte <thibaut.tourte17@gmail.com>
 */

namespace App\Tests\Controller;

use App\Controller\TaskController;
use App\Domain\Entity\Task;
use App\Domain\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectRepository;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Twig\Environment;

class TaskControllerTest extends TestCase
{
    /** @var TaskController */
    private $controller;
    /** @var ObjectRepository|MockObject */
    private $taskRepository;
    /** @var AuthorizationChecker|MockObject */
    private $security;
    /** @var FormInterface|MockObject */
    private $formType;
    /** @var Session */
    private $session;
    /** @var TokenInterface|MockObject */
    private $token;
    /** @var User|MockObject */
    private $user;

    public function setUp()
    {
        $this->controller = new TaskController();

        $container = new Container();

        $twig = $this->createMock(Environment::class);
        $twig->method('render')->willReturn("Une vue");
        $container->set('twig', $twig);

        $doctrine = $this->createMock(ManagerRegistry::class);
        $this->taskRepository = $this->createMock(ObjectRepository::class);
        $doctrine->method("getRepository")->willReturn($this->taskRepository);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $doctrine->method("getManager")->willReturn($entityManager);
        $container->set('doctrine', $doctrine);

        $this->security = $this->createMock(AuthorizationCheckerInterface::class);
        $container->set('security.authorization_checker', $this->security);

        $this->token = $this->createMock(TokenInterface::class);
        $this->user = $this->createMock(User::class);
        $this->token->method("getUser")->willReturn($this->user);
        $tokenStorage = $this->createMock(TokenStorageInterface::class);
        $tokenStorage->method("getToken")->willReturn($this->token);
        $container->set('security.token_storage', $tokenStorage);

        $this->formType = $this->createMock(FormInterface::class);
        $this->formType->method("handleRequest")->willReturnSelf();
        $formFactory = $this->createMock(FormFactoryInterface::class);
        $formFactory->method("create")->willReturn($this->formType);
        $container->set('form.factory', $formFactory);

        $this->session = new Session(new MockArraySessionStorage());
        $container->set('session', $this->session);

        $router = $this->createMock(UrlGeneratorInterface::class);
        $router->method("generate")->willReturn("/url");
        $container->set('router', $router);

        $this->controller->setContainer($container);
    }

    // listAction()
    public function testListActionReturnResponse()
    {
        $this->taskRepository->method("findAll")->willReturn([]);

        $response = $this->controller->listAction();

        self::assertInstanceOf(Response::class, $response);
        self::assertNotInstanceOf(RedirectResponse::class, $response);
    }

    // createAction()
    public function testCreateActionIfNotConnectedReturnRedirectResponse()
    {
        $this->expectException(AccessDeniedException::class);

        $request = new Request();

        $this->security->method("isGranted")->willReturn(false);

        $this->controller->createAction($request);
    }

    public function testCreateActionIfFormIsNotSubmitted()
    {
        $request = Request::create("/tasks/create");

        $this->security->method("isGranted")->willReturn(true);

        $this->formType->method("isSubmitted")->willReturn(false);

        $response = $this->controller->createAction($request);

        self::assertInstanceOf(Response::class, $response);
        self::assertNotInstanceOf(RedirectResponse::class, $response);
    }

    public function testCreateActionIfFormIsNotValid()
    {
        $request = Request::create("/tasks/create");

        $this->security->method("isGranted")->willReturn(true);

        $this->formType->method("isSubmitted")->willReturn(true);
        $this->formType->method("isValid")->willReturn(false);

        $response = $this->controller->createAction($request);

        self::assertInstanceOf(Response::class, $response);
        self::assertNotInstanceOf(RedirectResponse::class, $response);
    }

    public function testCreateActionIfFormIsSubmittedAndValid()
    {
        $request = Request::create("/tasks/create");

        $this->security->method("isGranted")->willReturn(true);

        $this->formType->method("isSubmitted")->willReturn(true);
        $this->formType->method("isValid")->willReturn(true);

        $response = $this->controller->createAction($request);

        self::assertEquals('La tâche a été bien été ajoutée.', $this->session->getFlashBag()->get("success")[0]);
        self::assertInstanceOf(RedirectResponse::class, $response);
    }

    // editAction()
    public function testEditActionIfNotConnectedReturnRedirectResponse()
    {
        $this->expectException(AccessDeniedException::class);

        $task = new Task(new User());

        $request = new Request();

        $this->security->method("isGranted")->willReturn(false);

        $this->controller->editAction($task, $request);
    }

    public function testEditActionIfFormIsNotSubmitted()
    {
        $task = new Task(new User());

        $request = Request::create("/tasks/edit");

        $this->security->method("isGranted")->willReturn(true);

        $this->formType->method("isSubmitted")->willReturn(false);

        $response = $this->controller->editAction($task, $request);

        self::assertInstanceOf(Response::class, $response);
        self::assertNotInstanceOf(RedirectResponse::class, $response);
    }

    public function testEditActionIfFormIsNotValid()
    {
        $task = new Task(new User());
        $request = Request::create("/tasks/edit");

        $this->security->method("isGranted")->willReturn(true);

        $this->formType->method("isSubmitted")->willReturn(true);
        $this->formType->method("isValid")->willReturn(false);

        $response = $this->controller->editAction($task, $request);

        self::assertInstanceOf(Response::class, $response);
        self::assertNotInstanceOf(RedirectResponse::class, $response);
    }

    public function testEditActionIfFormIsSubmittedAndValid()
    {
        $task = new Task(new User());
        $request = Request::create("/tasks/edit");

        $this->security->method("isGranted")->willReturn(true);

        $this->formType->method("isSubmitted")->willReturn(true);
        $this->formType->method("isValid")->willReturn(true);

        $response = $this->controller->editAction($task, $request);

        self::assertEquals('La tâche a bien été modifiée.', $this->session->getFlashBag()->get("success")[0]);
        self::assertInstanceOf(RedirectResponse::class, $response);
    }

    // toggleAction()
    public function testToggleTaskAction()
    {
        $task = new Task(new User());
        $response = $this->controller->toggleTaskAction($task);

        self::assertEquals('La tâche  a bien été marquée comme faite.', $this->session->getFlashBag()->get("success")[0]);
        self::assertInstanceOf(RedirectResponse::class, $response);
    }

    // deleteAction()
    public function testDeleteActionIfNotConnectedReturnRedirectResponse()
    {
        $this->expectException(AccessDeniedException::class);

        $task = new Task(new User());

        $this->security->method("isGranted")->willReturn(false);

        $this->controller->deleteTaskAction($task);
    }

    /*
     * new Task(new User()); => true
     * new Task(null);       => false
     *
     * ->willReturnOnConsecutiveCalls(true, false); => true
     * ->willReturnOnConsecutiveCalls(true, true);  => false
     *
     * SI NEW USER():
     * $this->token->getUser()->setUsername("Utilisateur connecté différent"); => true
     * $this->token->getUser()->setUsername(null);                             => false (pas obligatoire)
     * SI NULL :
     * /!\
     */

    public function testDeleteActionIfAnonymAndRoleAdminReturnRedirectResponse()
    {
        // (false || false) && any
        $task = new Task(null);

        $this->security->method('isGranted')
                       ->withConsecutive(['IS_AUTHENTICATED_FULLY'], ['ROLE_ADMIN'])
                       ->willReturnOnConsecutiveCalls(true, true);

        $response = $this->controller->deleteTaskAction($task);

        self::assertEquals('La tâche a bien été supprimée.', $this->session->getFlashBag()->get("success")[0]);
        self::assertInstanceOf(RedirectResponse::class, $response);
    }

    public function testDeleteActionIfUserAndNotRoleAdminAndConnectedReturnRedirectResponse()
    {
        // (true || true) && false
        $task = new Task(new User());

        $this->security->method('isGranted')
                       ->withConsecutive(['IS_AUTHENTICATED_FULLY'], ['ROLE_ADMIN'])
                       ->willReturnOnConsecutiveCalls(true, false);

        $this->user->method("getId")->willReturn(null);

        $response = $this->controller->deleteTaskAction($task);

        self::assertEquals('La tâche a bien été supprimée.', $this->session->getFlashBag()->get("success")[0]);
        self::assertInstanceOf(RedirectResponse::class, $response);
    }

    public function testDeleteActionIfUserAndRoleAdminAndNotConnectedReturnRedirectResponse()
    {
        // (true || false) && true
        $task = new Task(new User());

        $this->security->method('isGranted')
                       ->withConsecutive(['IS_AUTHENTICATED_FULLY'], ['ROLE_ADMIN'])
                       ->willReturnOnConsecutiveCalls(true, true);

        $this->user->method("getId")->willReturn(0);

        $response = $this->controller->deleteTaskAction($task);

        self::assertEquals('Vous n\'avez pas les droits pour supprimer cette tâche.', $this->session->getFlashBag()->get("error")[0]);
        self::assertInstanceOf(RedirectResponse::class, $response);
    }

    public function testDeleteActionIfNotUserAndNotRoleAdminAndNotConnectedReturnRedirectResponse()
    {
        // (false || true) && true
        $task = new Task(null);

        $this->security->method('isGranted')
                       ->withConsecutive(['IS_AUTHENTICATED_FULLY'], ['ROLE_ADMIN'])
                       ->willReturnOnConsecutiveCalls(true, false);

        $this->user->method("getId")->willReturn(0);

        $response = $this->controller->deleteTaskAction($task);

        self::assertEquals('Vous n\'avez pas les droits pour supprimer cette tâche.', $this->session->getFlashBag()->get("error")[0]);
        self::assertInstanceOf(RedirectResponse::class, $response);
    }
}
