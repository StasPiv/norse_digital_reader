<?php

namespace AppBundle\Tests\Model;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use AppBundle\Model\User;

class UserTest extends KernelTestCase
{

    public function testAuth()
    {
        $user = new User('staspivovartsev@gmail.com');
        $password = 'testpassword';
        $this->assertTrue($user->auth($password));
    }
}