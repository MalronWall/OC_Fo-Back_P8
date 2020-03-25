<?php

declare(strict_types=1);

/**
 * (c) Thibaut Tourte <thibaut.tourte17@gmail.com>
 */

namespace App\Tests\Controller;

use App\Controller\UserController;
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
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Twig\Environment;

class UserControllerTest extends TestCase
{
    /** @var UserController */
    private $controller;
    /** @var ObjectRepository|MockObject */
    private $userRepository;
    /** @var AuthorizationCheckerInterface|MockObject */
    private $security;
    /** @var FormInterface|MockObject */
    private $formType;
    /** @var UserPasswordEncoder|MockObject */
    private $encoder;
    /** @var Session|MockObject */
    private $session;

    public function SetUp()
    {
        $this->controller = new UserController();
        $container = new Container();

        $twig = $this->createMock(Environment::class);
        $twig->method('render')->willReturn("Une vue");
        $container->set('twig', $twig);

        $doctrine = $this->createMock(ManagerRegistry::class);
        $this->userRepository = $this->createMock(ObjectRepository::class);
        $doctrine->method("getRepository")->willReturn($this->userRepository);
        $entityManager = $this->createMock(EntityManagerInterface::class);
        $doctrine->method("getManager")->willReturn($entityManager);
        $container->set('doctrine', $doctrine);

        $this->security = $this->createMock(AuthorizationCheckerInterface::class);
        $container->set('security.authorization_checker', $this->security);

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

        $this->encoder = $this->createMock(UserPasswordEncoder::class);
        $this->encoder->method("encodePassword")->willReturn("Password encoded");

        $this->controller->setContainer($container);
    }

    // listAction
    public function testListActionIfNotConnectedReturnRedirectResponse()
    {
        $this->expectException(AccessDeniedException::class);

        $this->security->method("isGranted")->willReturn(false);

        $this->controller->listAction();
    }

    public function testListActionIfConnectedReturnResponse()
    {
        $this->security->method("isGranted")->willReturn(true);

        $this->userRepository->method("findAll")->willReturn([]);

        $response = $this->controller->listAction();

        self::assertInstanceOf(Response::class, $response);
        self::assertNotInstanceOf(RedirectResponse::class, $response);
    }

    // createAction
    public function testCreateActionIfIsNotSubmittedReturnResponse()
    {
        $request = Request::create("/users/create");

        $this->formType->method("isSubmitted")->willReturn(false);

        $encoder = new UserPasswordEncoder(new EncoderFactory([]));

        $response = $this->controller->createAction($request, $encoder);

        self::assertInstanceOf(Response::class, $response);
        self::assertNotInstanceOf(RedirectResponse::class, $response);
    }

    public function testCreateActionIfIsNotValidReturnResponse()
    {
        $request = Request::create("/users/create");

        $this->formType->method("isSubmitted")->willReturn(true);
        $this->formType->method("isValid")->willReturn(false);

        $encoder = new UserPasswordEncoder(new EncoderFactory([]));

        $response = $this->controller->createAction($request, $encoder);

        self::assertInstanceOf(Response::class, $response);
        self::assertNotInstanceOf(RedirectResponse::class, $response);
    }

    public function testCreateActionIfIsSubmittedAndIsValidReturnResponse()
    {
        $request = Request::create("/users/create");

        $this->formType->method("isSubmitted")->willReturn(true);
        $this->formType->method("isValid")->willReturn(true);

        $response = $this->controller->createAction($request, $this->encoder);

        self::assertEquals('L\'utilisateur a bien été ajouté.', $this->session->getFlashBag()->get("success")[0]);
        self::assertInstanceOf(RedirectResponse::class, $response);
    }

    // editAction
    public function testEditActionIfNotConnectedReturnRedirectResponse()
    {
        $request = Request::create("/users/edit");

        $this->expectException(AccessDeniedException::class);

        $this->security->method("isGranted")->willReturn(false);

        $this->controller->editAction(new User(), $request, $this->encoder);
    }

    public function testEditActionIfIsNotSubmittedReturnResponse()
    {
        $request = Request::create("/users/edit");

        $this->security->method("isGranted")->willReturn(true);

        $this->formType->method("isSubmitted")->willReturn(false);

        $response = $this->controller->editAction(new User(), $request, $this->encoder);

        self::assertInstanceOf(Response::class, $response);
        self::assertNotInstanceOf(RedirectResponse::class, $response);
    }

    public function testEditActionIfIsNotValidReturnResponse()
    {
        $request = Request::create("/users/edit");

        $this->security->method("isGranted")->willReturn(true);

        $this->formType->method("isSubmitted")->willReturn(true);
        $this->formType->method("isValid")->willReturn(false);

        $response = $this->controller->editAction(new User(), $request, $this->encoder);

        self::assertInstanceOf(Response::class, $response);
        self::assertNotInstanceOf(RedirectResponse::class, $response);
    }

    public function testEditActionIfIsSubmittedAndIsValidReturnRedirectResponse()
    {
        $request = Request::create("/users/edit");

        $this->security->method("isGranted")->willReturn(true);

        $this->formType->method("isSubmitted")->willReturn(true);
        $this->formType->method("isValid")->willReturn(true);

        $response = $this->controller->editAction(new User(), $request, $this->encoder);

        self::assertEquals('L\'utilisateur a bien été modifié', $this->session->getFlashBag()->get("success")[0]);
        self::assertInstanceOf(RedirectResponse::class, $response);
    }
}
