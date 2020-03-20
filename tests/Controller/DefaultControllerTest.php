<?php

declare(strict_types=1);

/**
 * (c) Thibaut Tourte <thibaut.tourte17@gmail.com>
 */

namespace App\Tests\Controller;

use App\Controller\DefaultController;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class DefaultControllerTest extends TestCase
{
    // https://symfony.com/blog/new-in-symfony-4-1-simpler-service-testing
    public function testReturnResponse()
    {
        $controller = new DefaultController();

        $twig = $this->createMock(Environment::class);
        $twig->method('render')->willReturn("Une vue");
        $container = new Container();
        $container->set('twig', $twig);
        $controller->setContainer($container);

        $response = $controller->indexAction();

        self::assertInstanceOf(Response::class, $response);
        self::assertNotInstanceOf(RedirectResponse::class, $response);
    }
}
