<?php

namespace AppBundle\Model;

class User
{
    /**
     * @var string
     */
    private $email;

    /**
     * @param string $email
     */
    public function __construct($email)
    {
        $this->email = $email;
    }

    /**
     * @param string $password
     * @return boolean
     */
    public function auth($password)
    {

    }

    /**
     * @param $password
     * @param $repeatPassword
     * @return boolean
     */
    public function register($password, $repeatPassword)
    {

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