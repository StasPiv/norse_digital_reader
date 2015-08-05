<?php

namespace AppBundle\Tests\Model;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use AppBundle\Model\Source;
use AppBundle\Entity\FeedSource as SourceEntity;

class SourceTest extends KernelTestCase
{
    private $testRssSource = 'https://news.yandex.ru/hardware.rss';
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

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

    public function testAddRss()
    {
        $source = new Source();
        $source->setEm($this->em);
        $rssSource = $this->testRssSource;
        $this->assertTrue($source->add($rssSource));

        $entity = $this->em->getRepository('AppBundle:FeedSource')->findOneBy(['source' => $rssSource]);
        $this->assertNotNull($entity);

        $this->removeSourceEntity($rssSource);
    }

    public function testFailAddFacebook()
    {
        $source = new Source();
        $source->setEm($this->em);
        $facebookSource = 'https://graph.facebook.com/fg.gov.ua/posts?access_token=' . Source::FB_ACCESS_TOKEN;
        $this->removeSourceEntity($facebookSource);
        $this->assertFalse($source->add($facebookSource, Source::SOURCE_TYPE_FACEBOOK));

        $entity = $this->em->getRepository('AppBundle:FeedSource')->findOneBy(['source' => $facebookSource]);
        $this->assertNull($entity);
    }

    public function testSuccessAddFacebook()
    {
        $source = new Source();
        $source->setEm($this->em);
        $facebookSource = 'fg.gov.ua';
        $this->assertTrue($source->add($facebookSource, Source::SOURCE_TYPE_FACEBOOK));

        $fullSource = 'https://graph.facebook.com/' . $facebookSource . '/posts?access_token=' . Source::FB_ACCESS_TOKEN;

        /** @var SourceEntity $entity */
        $entity = $this->em->getRepository('AppBundle:FeedSource')->findOneBy(['source' => $fullSource]);

        $partOfFullSource = 'https://graph.facebook.com/fg.gov.ua/posts?access_token=';
        $result = !is_null($entity) && strpos($entity->getSource(), $partOfFullSource) !== false;

        if (!is_null($entity)) {
            $this->em->remove($entity);
            $this->em->flush();
        }

        $this->assertTrue($result, $entity->getSource() . ' <===> ' . $partOfFullSource);
    }

    public function testRemoveSource()
    {
        $testSource = 'test';
        $fullSource = 'https://graph.facebook.com/' . $testSource . '/posts?access_token=' . Source::FB_ACCESS_TOKEN;
        $this->removeSourceEntity($fullSource);

        $source = new Source();
        $source->setEm($this->em);

        $source->add($testSource, Source::SOURCE_TYPE_FACEBOOK, 2);

        $this->assertTrue($source->remove($testSource, Source::SOURCE_TYPE_FACEBOOK));
        $this->assertFalse($source->remove($testSource, Source::SOURCE_TYPE_FACEBOOK));
    }

    public function testUpdateRss()
    {
        $source = new Source();
        $source->setEm($this->em);

        $source->add($this->testRssSource, Source::SOURCE_TYPE_RSS, 2);
        $source->update($this->testRssSource);

        $entity = $this->getBySource($this->testRssSource);

        $content = $entity->getContent();

        $this->removeSourceEntity($this->testRssSource);

        $this->assertNotEmpty($content);
    }

    public function testUpdateFacebook()
    {

    }

    /**
     * @param $source
     */
    private function removeSourceEntity($source)
    {
        $entity = $this->getBySource($source);

        if (!is_null($entity)) {
            $this->em->remove($entity);
            $this->em->flush();
        }
    }

    /**
     * @param $source
     * @return SourceEntity
     */
    private function getBySource($source)
    {
        return $this->em->getRepository('AppBundle:FeedSource')->findOneBy(['source' => $source]);
    }


}