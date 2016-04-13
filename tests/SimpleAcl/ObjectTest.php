<?php
namespace SimpleAclTest;

use PHPUnit_Framework_TestCase;
use SimpleAcl\Object;

/**
 * Class ObjectTest
 *
 * @package SimpleAclTest
 */
class ObjectTest extends PHPUnit_Framework_TestCase
{
  public function testName()
  {
    /** @var Object $object */
    $object = $this->getMockForAbstractClass('SimpleAcl\Object', array('TestName'));

    self::assertEquals($object->getName(), 'TestName');
    $object->setName('NewName');
    self::assertEquals($object->getName(), 'NewName');
  }

  public function testAddChild()
  {
    /** @var Object $parent */
    $parent = $this->getMockForAbstractClass('SimpleAcl\Object', array('Parent'));

    $child = $this->getMockForAbstractClass('SimpleAcl\Object', array('Child'));

    $parent->addChild($child);

    self::assertEquals(1, count($parent->getChildren()));
    self::assertSame($child, $parent->hasChild($child));
    self::assertSame($child, $parent->hasChild('Child'));
  }

  public function testRemoveChild()
  {
    /** @var Object $parent */
    $parent = $this->getMockForAbstractClass('SimpleAcl\Object', array('Parent'));

    $child = $this->getMockForAbstractClass('SimpleAcl\Object', array('Child'));

    self::assertFalse($parent->removeChild($child));

    $parent->addChild($child);

    self::assertEquals(1, count($parent->getChildren()));
    self::assertSame($child, $parent->hasChild($child));
    self::assertSame($child, $parent->hasChild('Child'));

    self::assertTrue($parent->removeChild($child));

    self::assertNull($parent->hasChild($child));
    self::assertEquals(0, count($parent->getChildren()));
  }

  public function testAddSameChild()
  {
    /** @var Object $parent */
    $parent = $this->getMockForAbstractClass('SimpleAcl\Object', array('Parent'));

    $child = $this->getMockForAbstractClass('SimpleAcl\Object', array('Child'));

    $parent->addChild($child);

    self::assertEquals(1, count($parent->getChildren()));
    self::assertSame($child, $parent->hasChild($child));

    $parent->addChild($child);
    self::assertEquals(1, count($parent->getChildren()));

    $child2 = $this->getMockForAbstractClass('SimpleAcl\Object', array('Child'));

    $parent->addChild($child2);

    self::assertEquals(1, count($parent->getChildren()));
    self::assertSame($child, $parent->hasChild('Child'));
    self::assertSame($child, $parent->hasChild($child));
    self::assertSame($child, $parent->hasChild($child2));

    self::assertNotSame($child2, $parent->hasChild($child2));
  }

  public function testGetChildren()
  {
    $parent = $this->getMockForAbstractClass('SimpleAcl\Object', array('TestName'));

    $child1 = $this->getMockForAbstractClass('SimpleAcl\Object', array('TestNameChild1'));
    $parent->addChild($child1);

    $child2 = $this->getMockForAbstractClass('SimpleAcl\Object', array('TestNameChild2'));
    $parent->addChild($child2);

    self::assertSame(array($child1, $child2), $parent->getChildren());
  }
}