<?php

declare(strict_types=1);

/**
 * (c) Thibaut Tourte <thibaut.tourte17@gmail.com>
 */

namespace App\Tests\security;

use App\Domain\Entity\User;
use App\Security\LoginAuthenticator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

class LoginAuthenticatorTest extends TestCase
{
    /** @var UrlGeneratorInterface|MockObject */
    private $urlGenerator;
    /** @var UserPasswordEncoderInterface|MockObject */
    private $passwordEncoder;
    /** @var Session|MockObject */
    private $session;
    /** @var LoginAuthenticator */
    private $loginAuthenticator;
    /** @var UserProviderInterface|MockObject */
    private $userProviderInterface;

    public function setUp()
    {
        $this->urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $this->urlGenerator->method("generate")->willReturn("/url");

        $this->userProviderInterface = $this->createMock(UserProviderInterface::class);

        $this->passwordEncoder = $this->createMock(UserPasswordEncoderInterface::class);

        $this->session = new Session(new MockArraySessionStorage());

        $this->loginAuthenticator = new LoginAuthenticator($this->urlGenerator, $this->passwordEncoder, $this->session);
    }

    // supports
    public function testSupportsIfRouteNotEqualsToLogin()
    {
        $request = Request::create(
            "/autre",
            "GET"
        );
        $request->attributes->add(["_route" => "autre"]);

        $return = $this->loginAuthenticator->supports($request);
        self::assertFalse($return);
    }

    public function testSupportsIfMethodIsNotPost()
    {
        $request = Request::create("/login", "GET");
        $request->attributes->add(["_route" => "login"]);

        $return = $this->loginAuthenticator->supports($request);
        self::assertFalse($return);
    }

    public function testSupportsIfAllIsValid()
    {
        $request = Request::create("/login", "POST");
        $request->attributes->add(["_route" => "login"]);

        $return = $this->loginAuthenticator->supports($request);
        self::assertTrue($return);
    }

    // getCredentials

    public function testGetCredentialsReturnArrayWithKeys()
    {
        $request = Request::create(
            "/login",
            Request::METHOD_POST,
            [
                '_username' => 'UserName',
                '_password' => 'UserPassword'
            ]
        );

        $return = $this->loginAuthenticator->getCredentials($request);

        self::assertIsArray($return);
        self::assertArrayHasKey("username", $return);
        self::assertArrayHasKey("password", $return);
    }

    // getUser
    public function testGetUserNotFound()
    {
        $this->expectException(CustomUserMessageAuthenticationException::class);

        $credentials = [
            "username" => "UserName",
            "password" => "UserPassword"
        ];

        $this->userProviderInterface->method("loadUserByUsername")
            ->willThrowException(new UsernameNotFoundException());

        $this->loginAuthenticator->getUser($credentials, $this->userProviderInterface);
    }

    public function testGetUserFound()
    {
        $credentials = [
            "username" => "UserName",
            "password" => "UserPassword"
        ];

        $this->userProviderInterface->method("loadUserByUsername")
                                    ->willReturn(new User());

        $return = $this->loginAuthenticator->getUser($credentials, $this->userProviderInterface);

        self::assertInstanceOf(UserInterface::class, $return);
    }

    // checkCredentials
    public function testCheckCredentialsIfNotValid()
    {
        $this->expectException(CustomUserMessageAuthenticationException::class);

        $credentials = [
            "username" => "UserName",
            "password" => "UserPassword"
        ];

        $this->passwordEncoder->method("isPasswordValid")->willReturn(false);

        $this->loginAuthenticator->checkCredentials($credentials, new User());
    }

    public function testCheckCredentialsIfValid()
    {
        $credentials = [
            "username" => "UserName",
            "password" => "UserPassword"
        ];

        $this->passwordEncoder->method("isPasswordValid")->willReturn(true);

        $return = $this->loginAuthenticator->checkCredentials($credentials, new User());

        self::assertTrue($return);
    }

    // onAuthenticationSuccess
    public function testOnAuthentificationSuccess()
    {
        $providerKey = 'main';
        $return = $this->loginAuthenticator->onAuthenticationSuccess(new Request(), new PostAuthenticationGuardToken(new User(), $providerKey, []), $providerKey);

        self::assertEquals('Connexion réussie !', $this->session->getFlashBag()->get("success")[0]);
        self::assertInstanceOf(RedirectResponse::class, $return);
    }

    // getLoginUrl
    public function testGetLoginUrlReturnString()
    {
        $return = $this->loginAuthenticator->getLoginUrl();

        self::assertIsString($return);
    }
}
