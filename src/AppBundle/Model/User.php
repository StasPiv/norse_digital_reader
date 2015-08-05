<?php

namespace AppBundle\Model;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraints\Email as EmailConstraint;
use Symfony\Component\Validator\Validation;
use AppBundle\Entity\FeedUser as UserEntity;

class User
{
    /**
     * @var string
     */
    private $email;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @param string $email
     */
    public function __construct($email)
    {
        $this->email = $email;
    }

    /**
     * @param EntityManager $em
     */
    public function setEm($em)
    {
        $this->em = $em;
    }

    /**
     * @throws Exception
     * @return EntityManager
     */
    public function getEm()
    {
        if (!$this->em instanceof EntityManager) {
            throw new Exception('Need entityManager for FeedUser. Use setEm()');
        }

        return $this->em;
    }

    /**
     * @param string $password
     * @return boolean
     */
    public function auth($password)
    {
        /** @var \AppBundle\Entity\FeedUser $entity */
        $entity = $this->getRepository()
                           ->findOneBy(['email' => $this->email, 'password' => md5($password)]);
        return !is_null($entity);
    }

    /**
     * @param $password
     * @param $repeatPassword
     * @return boolean
     */
    public function register($password, $repeatPassword)
    {
        if (!$this->isValidEmail() || $password != $repeatPassword || $this->checkIfExists()) {
            return false;
        }

        $entity = new UserEntity();
        $entity->setEmail($this->email);
        $entity->setPassword(md5($password));
        $this->getEm()->persist($entity);

        $this->getEm()->flush();

        return true;
    }

    /**
     * @return boolean
     */
    public function checkIfExists()
    {
        /** @var \AppBundle\Entity\FeedUser $entity */
        $entity = $this->getRepository()
                       ->findOneBy(['email' => $this->email]);

        return !is_null($entity);
    }

    /**
     * @param string $password
     */
    public function getPasswordWeight($password)
    {

    }

    /**
     * @return boolean
     */
    public function isValidEmail()
    {
        $emailConstraint = new EmailConstraint();
        $emailConstraint->message = 'Email is not valid';

        $errors = $this->getValidator()->validate(
            $this->email,
            $emailConstraint
        );

        return count($errors) == 0;
    }

    /**
     * @return \Doctrine\ORM\EntityRepository
     * @throws Exception
     */
    private function getRepository()
    {
        return $this->getEm()->getRepository('AppBundle:FeedUser');
    }

    /**
     * @return \Symfony\Component\Validator\ValidatorInterface
     */
    private function getValidator()
    {
        return Validation::createValidator();
    }
}