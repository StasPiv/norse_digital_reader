<?php

namespace AppBundle\Model;
use Doctrine\ORM\EntityManager;

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
        $entity = $this->getEm()->getRepository('AppBundle:FeedUser')
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
        return true;
    }

    /**
     * @return boolean
     */
    public function checkIfExists()
    {

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

    }
}