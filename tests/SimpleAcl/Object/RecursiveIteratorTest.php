<?php
namespace SimpleAclTest;

use PHPUnit_Framework_TestCase;
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
}