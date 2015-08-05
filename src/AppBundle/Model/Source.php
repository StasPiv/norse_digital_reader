<?php

namespace AppBundle\Model;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Validation;
use AppBundle\Entity\FeedSource as FeedEntity;
use AppBundle\Entity\FeedUser as UserEntity;

class Source
{
    /**
     * @var EntityManager
     */
    private $em;

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
            throw new Exception('Need entityManager for FeedSource. Use setEm()');
        }

        return $this->em;
    }

    /**
     * @param string $type
     * @param string $source
     * @param integer $userId
     */
    public function add($type, $source, $userId = null)
    {

    }

    /**
     * @param string $source
     * @param integer $userId
     */
    public function remove($source, $userId = null)
    {

    }

    /**
     * @param string $source
     */
    public function update($source)
    {

    }

}