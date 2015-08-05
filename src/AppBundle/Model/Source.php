<?php

namespace AppBundle\Model;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Validation;
use AppBundle\Entity\FeedSource as SourceEntity;
use AppBundle\Entity\FeedUser as UserEntity;
use AppBundle\Tests\Model\App;

class Source
{
    const SOURCE_TYPE_RSS = 1;
    const SOURCE_TYPE_FACEBOOK = 2;

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
     * @param string $source
     * @param string $type
     * @param integer $userId
     * @return bool
     * @throws Exception
     */
    public function add($source, $type = self::SOURCE_TYPE_RSS, $userId = null)
    {
        if (0) {
            return false;
        }

        if (is_null($userId)) {
            $userId = App::getCurrentUserId();
        }

        $entity = new SourceEntity();
        $entity->setSource($source);
        $entity->setUserId($userId);
        $entity->setType($type);
        $this->getEm()->persist($entity);

        $this->getEm()->flush();

        return true;
    }

    /**
     * @param string $source
     * @param integer $userId
     * @return boolean
     */
    public function remove($source, $userId = null)
    {

    }

    /**
     * @param string $source
     * @return boolean
     */
    public function update($source)
    {

    }

}