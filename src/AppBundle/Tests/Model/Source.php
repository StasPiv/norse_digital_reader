<?php

namespace AppBundle\Tests\Model;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use AppBundle\Model\Source;
use AppBundle\Entity\FeedSource as SourceEntity;

class SourceTest extends KernelTestCase
{
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
        $rssSource = 'https://news.yandex.ru/hardware.rss';
        $this->assertTrue($source->add($rssSource));

        $entity = $this->em->getRepository('AppBundle:FeedSource')->findOneBy(['source' => $rssSource]);
        $this->assertNotNull($entity);

        $this->em->remove($entity);
        $this->em->flush();
    }

    public function testAddFacebook()
    {
        $source = new Source();
        $source->setEm($this->em);
        $facebookSource = 'https://graph.facebook.com/fg.gov.ua/posts?access_token=' . App::FB_ACCESS_TOKEN;
        $this->assertTrue($source->add($facebookSource, Source::SOURCE_TYPE_FACEBOOK));

        $entity = $this->em->getRepository('AppBundle:FeedSource')->findOneBy(['source' => $facebookSource]);
        $this->assertNotNull($entity);

        $this->em->remove($entity);
        $this->em->flush();
    }


}