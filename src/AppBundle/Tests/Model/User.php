<?php

namespace AppBundle\Tests\Model;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use AppBundle\Model\User;
use AppBundle\Entity\FeedUser as UserEntity;

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
            $user = new User($email);
            $user->setEm($this->em);
            $success &= $user->auth($this->testPassword);
            $fail &= $user->auth($this->testPassword.'fake');
        }

        $this->deleteTestUsers();

        $this->assertTrue((bool)$success);
        $this->assertFalse((bool)$fail);
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
}