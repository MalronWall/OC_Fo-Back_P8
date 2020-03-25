<?php

declare(strict_types=1);

/**
 * (c) Thibaut Tourte <thibaut.tourte17@gmail.com>
 */

namespace App\Tests\Controller;

use App\Controller\SecurityController;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Twig\Environment;

class SecurityComponentTest extends TestCase
{
    public function testReturnResponseWithError()
    {
        $controller = new SecurityController();

        /** @var AuthenticationUtils|MockObject $authenticationUtils */
        $authenticationUtils = $this->createMock(AuthenticationUtils::class);

        $twig = $this->createMock(Environment::class);
        $twig->method('render')->willReturn("Une vue");
        $container = new Container();
        $container->set('twig', $twig);
        $controller->setContainer($container);

        $authenticationUtils->method("getLastAuthenticationError")->willReturn(new AuthenticationException("L'erreur Guard"));
        $authenticationUtils->method("getLastUsername")->willReturn("Username Guard");

        $response = $controller->loginAction($authenticationUtils);

        self::assertInstanceOf(Response::class, $response);
        self::assertNotInstanceOf(RedirectResponse::class, $response);
    }
}
