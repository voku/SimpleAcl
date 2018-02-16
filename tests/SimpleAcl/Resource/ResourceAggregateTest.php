<?php

namespace SimpleAclTest\Resource;

use PHPUnit\Framework\TestCase;
use SimpleAcl\Resource;
use SimpleAcl\Resource\ResourceAggregate;

/**
 * Class ResourceAggregateTest
 *
 * @package SimpleAclTest\Resource
 */
class ResourceAggregateTest extends TestCase
{
  public function testSetAndGetResources()
  {
    $resources = [new Resource('One'), new Resource('Tow')];

    $site = new ResourceAggregate();

    self::assertCount(0, $site->getResources());

    $site->setResources($resources);

    self::assertSame($resources, $site->getResources());

    self::assertCount(2, $site->getResources());
  }

  public function testResourceAdd()
  {
    $site = new ResourceAggregate();

    $resource1 = new Resource('One');
    $resource2 = new Resource('Tow');

    self::assertCount(0, $site->getResources());

    $site->addResource($resource1);
    $site->addResource($resource2);

    self::assertCount(2, $site->getResources());

    self::assertSame([$resource1, $resource2], $site->getResources());
  }

  public function testGetResourcesNames()
  {
    $site = new ResourceAggregate();

    $resource1 = new Resource('One');
    $resource2 = new Resource('Tow');

    self::assertCount(0, $site->getResources());

    $site->addResource($resource1);
    $site->addResource($resource2);

    self::assertCount(2, $site->getResources());

    self::assertSame(['One', 'Tow'], $site->getResourcesNames());
  }

  public function testRemoveResources()
  {
    $site = new ResourceAggregate();

    $resource1 = new Resource('One');
    $resource2 = new Resource('Tow');

    self::assertCount(0, $site->getResources());

    $site->addResource($resource1);
    $site->addResource($resource2);

    self::assertCount(2, $site->getResources());

    $site->removeResources();

    self::assertCount(0, $site->getResources());

    self::assertNull($site->getResource('One'));
    self::assertNull($site->getResource('Tow'));
  }

  public function testRemoveResource()
  {
    $site = new ResourceAggregate();

    $resource1 = new Resource('One');
    $resource2 = new Resource('Tow');

    self::assertCount(0, $site->getResources());

    $site->addResource($resource1);
    $site->addResource($resource2);

    self::assertCount(2, $site->getResources());

    $site->removeResource('One');
    self::assertCount(1, $site->getResources());
    self::assertSame($resource2, $site->getResource('Tow'));

    $site->removeResource('UnDefinedTow');
    self::assertCount(1, $site->getResources());

    $site->removeResource($resource2);
    self::assertCount(0, $site->getResources());
  }

  public function testAddResourceWithSameName()
  {
    $site = new ResourceAggregate();

    $resource1 = new Resource('One');
    $resource2 = new Resource('One');

    $site->addResource($resource1);
    $site->addResource($resource2);

    self::assertCount(1, $site->getResources());
    self::assertSame($resource1, $site->getResource('One'));
  }
}
