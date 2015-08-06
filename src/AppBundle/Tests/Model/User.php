<?php

namespace AppBundle\Tests\Model;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use AppBundle\Model\User;
use AppBundle\Entity\FeedUser as UserEntity;
use AppBundle\Model\App;

class UserTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @var array
     */
    private $testEmails = [
        'feed_user_for_test1@gmail.com',
        'feed_user_for_test2@gmail.com'
    ];

    private $testPassword = 'testpassword';

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        self::bootKernel();
        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager()
        ;
    }

    public function testAuth()
    {
        $this->deleteTestUsers();

        foreach ($this->testEmails as $email) {
            $user = new User($email);
            $user->setEm($this->em);
            $this->assertFalse($user->auth($this->testPassword));
        }

        $this->createTestUsers();

        $success = $fail = true;

        foreach ($this->testEmails as $email) {
            $entity = $this->findByEmail($email);
            $user = new User($email);
            $user->setEm($this->em);
            $success &= $user->auth($this->testPassword);
            $success &= $entity->getId() == App::getCurrentUserId();
            $fail &= $user->auth($this->testPassword . 'fake');
        }

        $this->deleteTestUsers();

        $this->assertTrue((bool)$success);
        $this->assertFalse((bool)$fail);
    }

    public function testFirstRegisterAndSecondRegisterWithSameEmail()
    {
        $email = $this->testEmails[0];

        $user = new User($email);
        $user->setEm($this->em);
        $this->assertTrue($user->register($this->testPassword, $this->testPassword));

        $entity = $this->findByEmail($email);
        $this->assertNotNull($entity);

        $this->assertFalse($user->register($this->testPassword, $this->testPassword));

        $this->em->remove($entity);
        $this->em->flush();
    }

    public function testWrongRepeatPassword()
    {
        $email = $this->testEmails[0];

        $user = new User($email);
        $this->assertFalse($user->register($this->testPassword, $this->testPassword . 'fake'));

    }

    public function testPasswordWeight()
    {
        $user = new User('test@gmail.com');

        $this->assertEquals(1, $user->getPasswordWeight('1'));
        $this->assertEquals(2, $user->getPasswordWeight('12345'));
        $this->assertEquals(2, $user->getPasswordWeight('123456'));
        $this->assertEquals(2, $user->getPasswordWeight('testlowercase123'));
        $this->assertEquals(2, $user->getPasswordWeight('InCorrect', $str));
        $this->assertNotEmpty($str);
        $this->assertEquals(3, $user->getPasswordWeight('Correct666', $str), $str);
    }

    public function testWrongEmail()
    {
        $user = new User('not_email');
        $this->assertFalse($user->isValidEmail());
    }

    public function testSuccessfulEmail()
    {
        $user = new User('true_email@gmail.com');
        $this->assertTrue($user->isValidEmail());
    }

    private function createTestUsers()
    {
        foreach ($this->testEmails as $email) {
            $entity = new UserEntity();
            $entity->setEmail($email);
            $entity->setPassword(md5($this->testPassword));

            $this->em->persist($entity);
        }
        $this->em->flush();
    }

    private function deleteTestUsers()
    {
        foreach ($this->testEmails as $email) {
            $entity = $this->em->getRepository('AppBundle:FeedUser')->findOneBy(['email' => $email]);
            if (is_null($entity)) {
                continue;
            }
            $this->em->remove($entity);
        }
        $this->em->flush();
    }

    /**
     * @param $email
     * @return \AppBundle\Entity\FeedUser
     */
    private function findByEmail($email)
    {
        return $this->em->getRepository('AppBundle:FeedUser')->findOneBy(['email' => $email]);
    }
}