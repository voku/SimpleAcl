<?php
namespace SimpleAclTest\Resource;

use PHPUnit_Framework_TestCase;
use SimpleAcl\Resource;
use SimpleAcl\Resource\ResourceAggregate;

/**
 * Class ResourceAggregateTest
 *
 * @package SimpleAclTest\Resource
 */
class ResourceAggregateTest extends PHPUnit_Framework_TestCase
{
  public function testSetAndGetResources()
  {
    $resources = array(new Resource('One'), new Resource('Tow'));

    $site = new ResourceAggregate();

    self::assertEquals(0, count($site->getResources()));

    $site->setResources($resources);

    self::assertEquals($resources, $site->getResources());

    self::assertEquals(2, count($site->getResources()));
  }

  public function testResourceAdd()
  {
    $site = new ResourceAggregate();

    $resource1 = new Resource('One');
    $resource2 = new Resource('Tow');

    self::assertEquals(0, count($site->getResources()));

    $site->addResource($resource1);
    $site->addResource($resource2);

    self::assertEquals(2, count($site->getResources()));

    self::assertEquals(array($resource1, $resource2), $site->getResources());
  }

  public function testGetResourcesNames()
  {
    $site = new ResourceAggregate();

    $resource1 = new Resource('One');
    $resource2 = new Resource('Tow');

    self::assertEquals(0, count($site->getResources()));

    $site->addResource($resource1);
    $site->addResource($resource2);

    self::assertEquals(2, count($site->getResources()));

    self::assertSame(array('One', 'Tow'), $site->getResourcesNames());
  }

  public function testRemoveResources()
  {
    $site = new ResourceAggregate();

    $resource1 = new Resource('One');
    $resource2 = new Resource('Tow');

    self::assertEquals(0, count($site->getResources()));

    $site->addResource($resource1);
    $site->addResource($resource2);

    self::assertEquals(2, count($site->getResources()));

    $site->removeResources();

    self::assertEquals(0, count($site->getResources()));

    self::assertNull($site->getResource('One'));
    self::assertNull($site->getResource('Tow'));
  }

  public function testRemoveResource()
  {
    $site = new ResourceAggregate();

    $resource1 = new Resource('One');
    $resource2 = new Resource('Tow');

    self::assertEquals(0, count($site->getResources()));

    $site->addResource($resource1);
    $site->addResource($resource2);

    self::assertEquals(2, count($site->getResources()));

    $site->removeResource('One');
    self::assertEquals(1, count($site->getResources()));
    self::assertEquals($resource2, $site->getResource('Tow'));

    $site->removeResource('UnDefinedTow');
    self::assertEquals(1, count($site->getResources()));

    $site->removeResource($resource2);
    self::assertEquals(0, count($site->getResources()));
  }

  public function testAddResourceWithSameName()
  {
    $site = new ResourceAggregate();

    $resource1 = new Resource('One');
    $resource2 = new Resource('One');

    $site->addResource($resource1);
    $site->addResource($resource2);

    self::assertEquals(1, count($site->getResources()));
    self::assertSame($resource1, $site->getResource('One'));
  }
}