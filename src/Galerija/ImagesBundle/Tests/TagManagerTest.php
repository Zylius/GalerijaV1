<?php
/**
 * Created by PhpStorm.
 * User: Zylius
 * Date: 12/5/2015
 * Time: 20:41
 */

namespace Galerija\ImagesBundle\Tests;

use Doctrine\ORM\EntityManager;
use Galerija\ImagesBundle\Entity\Image;
use Galerija\ImagesBundle\Entity\Tag;
use Galerija\ImagesBundle\Services\TagManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TagManagerTest extends WebTestCase
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|EntityManager $em
     */
    private $em;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->container = self::createClient()->getContainer();

        /** @var \PHPUnit_Framework_MockObject_MockObject|EntityManager $em */
        $this->em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->setMethods(['flush', 'persist', 'getRepository', 'findAll'])
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Returns tag manager to test with.
     *
     * @return TagManager
     */
    protected function getTagManager()
    {
        return new TagManager($this->em, $this->container->get('form.factory'), $this->container->get('router'));
    }

    /**
     * Checks if getForm return the expected form.
     */
    public function testGetForm()
    {
        $tagManager = $this->getTagManager();
        $tag = new Tag();
        $form = $tagManager->getForm($tag);

        $this->assertEquals('/tagCreate', $form->getConfig()->getOption('action'));
        $this->assertEquals($tag, $form->getData());
        $this->assertEquals('tag_create', $form->getName());
    }

    /**
     * Check if entity manager is called.
     */
    public function testSave()
    {
        $tag = new Tag();
        $this->em->expects($this->once())->method('persist')->with($tag);
        $this->em->expects($this->once())->method('flush')->with(null);

        $this->getTagManager()->save($tag);
    }

    /**
     * Data provider for testFormatTags.
     *
     * @return array
     */
    public function formatTagsData()
    {
        $out = [];

        #0 Check if completely empty string is returned as expected for no tags.
        $out[] = ['image' => new Image(), 'expectedTagString' => ''];

        #1 Check if tags are added into string as expected.
        $tag1 = new Tag();
        $tag1->setName('test1');
        $tag2 = new Tag();
        $tag2->setName('test2');
        $image = new Image();
        $image->addTag($tag1);
        $image->addTag($tag2);
        $out[] = ['image' => $image, 'expectedTagString' => ' tag-test1 tag-test2'];

        return $out;
    }


    /**
     * Check if tags are formatted as expected.
     *
     * @dataProvider formatTagsData
     *
     * @param string $expectedTagString
     * @param Image $image
     */
    public function testFormatTags(Image $image, $expectedTagString)
    {
        $this->assertEquals($expectedTagString, $this->getTagManager()->formatTags($image));
    }

    /**
     * Check if all tags from db are formatted correctly.
     */
    public function testFormatAllTags()
    {
        $tag1 = new Tag();
        $tag1->setName('test1');
        $tag2 = new Tag();
        $tag2->setName('test2');

        $this->em->expects($this->once())->method('getRepository')->with('GalerijaImagesBundle:Tag')->willReturnSelf();
        $this->em->expects($this->once())->method('findAll')->willReturn([$tag1, $tag2]);

        $this->assertEquals('test1,test2', $this->getTagManager()->formatAllTags());
    }
}
