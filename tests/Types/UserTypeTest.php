<?php

declare(strict_types=1);

/**
 * (c) Thibaut Tourte <thibaut.tourte17@gmail.com>
 */

namespace App\Tests\Types;

use App\Domain\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserTypeTest extends KernelTestCase
{
    public function testBuildFormReturn()
    {
        $kernel = self::bootKernel();
        $factory = $kernel->getContainer()->get('form.factory');
        $user = new User();
        $form = $factory->create(UserType::class, $user);
        $datas = [
            "username" => "John",
            "password" => [
                "first" => "JohnPassword",
                "second" => "JohnPassword"
            ],
            "email" => "john@doe.com",
            "roles" => "ROLE_USER"
        ];

        $form->submit($datas);

        self::assertEquals($datas["username"], $user->getUsername());
        self::assertEquals($datas["password"]["first"], $user->getPassword());
        self::assertEquals($datas["email"], $user->getEmail());
        self::assertEquals($datas["roles"], $user->getRoles()[0]);
    }
}
