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

    const FB_ACCESS_TOKEN = 'CAACFb9bXwZBUBAEidei6U0xFTDOKttjIFx5j3DRiF2iI8o2VvLAWhjR0psv2XC6VVG8ZCASiP7EVNTXkoSBXAo2cx1E8FQhYOiHx8PixlLKnxuY4mxk7p5tt2ld1dpEfZAYOM1oru0kSFhncROl7bKNuNrcl3agY7A7dJzrP6wVSPvCCOGzAy1wZAgbv6QYapiSCfVHlE7WY2F6LNRhV';

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

        if ($type == self::SOURCE_TYPE_FACEBOOK) {
            if (preg_match('/\//', $source)) {
                return false;
            } else {
                $source = $this->combineFacebookSource($source);
            }
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
     * @param int $type
     * @param integer $userId
     * @return bool
     */
    public function remove($source, $type = self::SOURCE_TYPE_RSS, $userId = null)
    {
        if ($type == self::SOURCE_TYPE_FACEBOOK) {
            $source = $this->combineFacebookSource($source);
        }

        $entityForRemoving = $this->getBySource($source);

        if (is_null($entityForRemoving)) {
            return false;
        }

        $entity = $this->getEm()->getRepository('AppBundle:FeedSource')->findOneBy(['source' => $source]);

        if (!is_null($entity)) {
            $this->getEm()->remove($entity);
            $this->getEm()->flush();
        }

        return true;
    }

    /**
     * @throws Exception
     * @param string $source
     * @return boolean
     */
    public function update($source)
    {
        $entity = $this->getBySource($source);

        if (is_null($entity)) {
            return true;
        }

        if ($entity->getType() == self::SOURCE_TYPE_RSS) {
            return $this->updateRssContent($entity);
        } elseif ($entity->getType() == self::SOURCE_TYPE_FACEBOOK) {
            return $this->updateFacebookContent($entity);
        } else {
            throw new Exception('Unknown type for source: ' . $entity->getSource());
        }
    }

    /**
     * @param SourceEntity $entity
     * @return boolean
     */
    private function updateRssContent($entity)
    {
        $entity->setContent(@file_get_contents($entity->getSource()));
        $this->getEm()->persist($entity);
        $this->getEm()->flush();
    }

    /**
     * @param string $source
     * @return boolean
     */
    private function updateFacebookContent($source)
    {

    }

    /**
     * @param string $source
     * @return SourceEntity
     */
    public function getBySource($source)
    {
        return $this->getEm()->getRepository('AppBundle:FeedSource')->findOneBy(['source' => $source]);
    }

    /**
     * @param $source
     * @return string
     */
    private function combineFacebookSource($source)
    {
        return 'https://graph.facebook.com/' . $source . '/posts?access_token=' . self::FB_ACCESS_TOKEN;
    }

}