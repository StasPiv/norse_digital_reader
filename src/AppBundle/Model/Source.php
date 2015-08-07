<?php

namespace AppBundle\Model;

use AppBundle\Entity\Feed as FeedEntity;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Form\Exception\RuntimeException;
use Symfony\Component\Validator\Validation;
use AppBundle\Entity\FeedSource as SourceEntity;
use AppBundle\Entity\FeedUser as UserEntity;
use AppBundle\Entity\UserSource as UserSourceEntity;
use AppBundle\Model\App;
use AppBundle\Parser\IParser;
use AppBundle\Parser\Facebook as FacebookParser;
use AppBundle\Parser\Rss as RssParser;

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
     * @param int $type
     * @param integer $userId
     * @param null $sourceId
     * @return bool
     */
    public function add($source, $type = self::SOURCE_TYPE_RSS, $userId = null, &$sourceId = null)
    {
        if (is_null($userId)) {
            $userId = App::getCurrentUserId();
        }

        if ($type == self::SOURCE_TYPE_FACEBOOK) {
            if (preg_match('/\//', $source)) {
                return false;
            } else {
                $source = $this->combineSource($source, $type);
            }
        }

        $existingEntity = $this->getBySource($source);

        if (!is_null($existingEntity)) {
            return $this->addSourceToUser($source, $userId);
        }

        $entity = new SourceEntity();
        $entity->setSource($source);
        $entity->setType($type);
        $this->getEm()->persist($entity);

        $this->getEm()->flush();

        if (!is_null($entity)) {
            $sourceId = $entity->getId();
            $this->update($sourceId);
            $this->addSourceToUser($source, $userId);
        }

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
        if (is_null($userId)) {
            $userId = App::getCurrentUserId();
        }

        if (is_numeric($source)) {
            $entityForRemoving = $this->getById((int)$source);
            if (!is_null($entityForRemoving)) {
                $source = $entityForRemoving->getSource();
            }
        } else {
            $source = $this->combineSource($source, $type);
            $entityForRemoving = $this->getBySource($source);
        }


        if (is_null($entityForRemoving)) {
            return false;
        }

        /** @var SourceEntity $entity */
        $entity = $this->getEm()->getRepository('AppBundle:FeedSource')->findOneBy(['source' => $source]);

        if (!is_null($entity)) {
            $this->removeUserSource($userId, $entity->getId());

            if (!$this->checkIfAnyUserHaveSource($entity->getId())) {
                $this->removeAllFeeds($entity);
                $this->getEm()->remove($entity);
                $this->getEm()->flush();
            }
        }

        return true;
    }

    /**
     * @param SourceEntity $entity
     */
    private function removeAllFeeds(SourceEntity $entity)
    {
        $feeds = $this->getEm()->getRepository('AppBundle:Feed')->findBy(['sourceId' => $entity->getId()]);

        foreach ($feeds as $feed) {
            $this->getEm()->remove($feed);
        }
    }

    /**
     *
     * @param string $source
     * @param $type
     * @return bool
     * @throws Exception
     */
    public function update($source, $type = self::SOURCE_TYPE_RSS)
    {
        if (is_numeric($source)) {
            $entity = $this->getById((int)$source);
            if (!is_null($entity)) {
                $type = $entity->getType();
            } else {
                return false;
            }
        } else {
            $entity = $this->getBySource($this->combineSource($source, $type));
        }

        if (is_null($entity)) {
            return true;
        }

        if ($type == self::SOURCE_TYPE_RSS) {
            return $this->updateRssContent($entity);
        } elseif ($type == self::SOURCE_TYPE_FACEBOOK) {
            return $this->updateFacebookContent($entity);
        } else {
            throw new Exception('Unknown type for source: ' . $entity->getSource());
        }
    }

    /**
     * @param SourceEntity $entity
     * @return boolean
     */
    private function updateRssContent(SourceEntity $entity)
    {
        $entity->setContent(@file_get_contents($entity->getSource()));

        if (!$entity->getContent()) {
            return false;
        }

        $this->updateFeeds(new RssParser(), $entity);

        $this->getEm()->persist($entity);
        $this->getEm()->flush();

        return true;
    }

    /**
     * @param SourceEntity $entity
     * @return boolean
     */
    private function updateFacebookContent(SourceEntity $entity)
    {
        $entity->setContent(@file_get_contents($entity->getSource()));

        if (!$entity->getContent()) {
            return false;
        }

        $this->updateFeeds(new FacebookParser(), $entity);

        $this->getEm()->persist($entity);
        $this->getEm()->flush();

        return true;
    }

    /**
     * @param IParser $parser
     * @param SourceEntity $entity
     * @throws Exception
     */
    private function updateFeeds(IParser $parser, SourceEntity $entity)
    {
        $existingFeeds = $this->getFeedsHashMap($entity);

        foreach ($parser->getItems($entity->getContent()) as $item) {
            if (isset($existingFeeds[(string)$item['title']])) {
                continue;
            }

            $feedEntity = new FeedEntity();
            $feedEntity->setTitle($item['title']);
            $feedEntity->setContent($item['content']);
            $feedEntity->setSourceId($entity->getId());
            $this->getEm()->persist($feedEntity);
        }
    }

    /**
     * @param SourceEntity $entity
     * @return array
     */
    private function getFeedsHashMap(SourceEntity $entity)
    {
        $feeds = [];
        foreach ($this->getEm()->getRepository('AppBundle:Feed')->findBy(['sourceId' => $entity->getId()]) as $feed) {
            /** @var FeedEntity $feed */
            $feeds[(string)$feed->getTitle()] = $feed->getContent();
        }
        return $feeds;
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
     * @param integer $id
     * @return SourceEntity
     */
    public function getById($id)
    {
        return $this->getEm()->getRepository('AppBundle:FeedSource')->findOneBy(['id' => $id]);
    }

    /**
     * @param $source
     * @param $type
     * @return string
     */
    private function combineSource($source, $type = self::SOURCE_TYPE_RSS)
    {
        if ($type == self::SOURCE_TYPE_RSS) {
            return $source;
        }

        return 'https://graph.facebook.com/' . $source . '/posts?access_token=' . self::FB_ACCESS_TOKEN;
    }

    /**
     * @param $source
     * @param $userId
     * @throws Exception
     * @return boolean
     */
    private function addSourceToUser($source, $userId)
    {
        $existingEntity = $this->getBySource($source);

        if ($this->checkIfUserHaveSource($userId, $existingEntity->getId())) {
            return false;
        }

        $userSourceEntity = new UserSourceEntity();
        $userSourceEntity->setSourceId($existingEntity->getId());
        $userSourceEntity->setUserId($userId);
        $this->getEm()->persist($userSourceEntity);
        $this->getEm()->flush();

        return true;
    }

    /**
     * @param integer $userId
     * @param integer $sourceId
     * @return boolean
     */
    private function checkIfUserHaveSource($userId, $sourceId)
    {
        return count($this->getEm()->getRepository('AppBundle:UserSource')
                             ->findBy(['sourceId' => $sourceId, 'userId' => $userId])) > 0;
    }

    /**
     * @param integer $userId
     * @param integer $sourceId
     * @return boolean
     */
    private function removeUserSource($userId, $sourceId)
    {
        $entity = $this->getEm()->getRepository('AppBundle:UserSource')->findOneBy(['sourceId' => $sourceId, 'userId' => $userId]);

        if (is_null($entity)) {
            return false;
        }

        $this->getEm()->remove($entity);
        $this->getEm()->flush();

        return true;
    }

    /**
     * @param integer $sourceId
     * @return boolean
     */
    private function checkIfAnyUserHaveSource($sourceId)
    {
        return count($this->getEm()->getRepository('AppBundle:UserSource')->findBy(['sourceId' => $sourceId])) > 0;
    }

}