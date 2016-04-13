<?php
namespace SimpleAclTest;

use PHPUnit_Framework_TestCase;
use RecursiveIteratorIterator;
use SimpleAcl\Object;
use SimpleAcl\Object\RecursiveIterator;

/**
 * Class RecursiveIteratorTest
 *
 * @package SimpleAclTest
 */
class RecursiveIteratorTest extends PHPUnit_Framework_TestCase
{
  /**
   * @param $name
   *
   * @return Object
   */
  protected function getObject($name)
  {
    return $this->getMockForAbstractClass('SimpleAcl\Object', array($name));
  }

  public function testKey()
  {
    $iterator = new RecursiveIterator(array());
    self::assertNull($iterator->key());

    $iterator = new RecursiveIterator(array($this->getObject('Test')));
    self::assertEquals('Test', $iterator->key());
  }

  public function testCurrent()
  {
    $iterator = new RecursiveIterator(array());
    self::assertFalse($iterator->current());

    $test = $this->getObject('Test');
    $iterator = new RecursiveIterator(array($test));
    self::assertSame($test, $iterator->current());
  }

  public function testValidNextRewind()
  {
    $iterator = new RecursiveIterator(array());
    self::assertFalse($iterator->valid());

    $test1 = $this->getObject('Test1');
    $test2 = $this->getObject('Test2');

    $iterator = new RecursiveIterator(array($test1, $test2));
    self::assertTrue($iterator->valid());
    self::assertSame($test1, $iterator->current());
    self::assertEquals('Test1', $iterator->key());

    $iterator->next();
    self::assertTrue($iterator->valid());
    self::assertSame($test2, $iterator->current());
    self::assertEquals('Test2', $iterator->key());
  }

  public function testHasChildren()
  {
    $iterator = new RecursiveIterator(array());
    self::assertFalse($iterator->hasChildren());

    $parent = $this->getObject('Test1');

    $child = $this->getObject('Test2');
    $parent->addChild($child);

    $iterator = new RecursiveIterator(array($parent));

    self::assertTrue($iterator->hasChildren());
  }

  public function testIterate()
  {
    $parent = $this->getObject('parent');

    $oc00 = $this->getObject('child0.0');
    $oc01 = $this->getObject('child0.1');

    $parent->addChild($oc00);
    $parent->addChild($oc01);

    $oc10 = $this->getObject('child1.0');

    $oc00->addChild($oc10);

    $oc20 = $this->getObject('child2.0');
    $oc10->addChild($oc20);

    $oc21 = $this->getObject('child2.1');
    $oc10->addChild($oc21);

    $actual = array();
    $i = new RecursiveIteratorIterator($parent, RecursiveIteratorIterator::SELF_FIRST);
    foreach ($i as $k => $o) {
      $actual[$k] = $o;
    }

    $expected = array(
        'parent'   => $parent,
        'child0.0' => $oc00,
        'child1.0' => $oc10,
        'child2.0' => $oc20,
        'child2.1' => $oc21,
        'child0.1' => $oc01,
    );
    self::assertSame($expected, $actual);

    $actual = array();
    foreach (new RecursiveIteratorIterator($oc10, RecursiveIteratorIterator::SELF_FIRST) as $k => $o) {
      $actual[$k] = $o;
    }

    $expected = array('child1.0' => $oc10, 'child2.0' => $oc20, 'child2.1' => $oc21);
    self::assertSame($expected, $actual);

    $actual = array();
    foreach (new RecursiveIteratorIterator($oc20, RecursiveIteratorIterator::SELF_FIRST) as $k => $o) {
      $actual[$k] = $o;
    }

    $expected = array('child2.0' => $oc20);
    self::assertSame($expected, $actual);
  }
}