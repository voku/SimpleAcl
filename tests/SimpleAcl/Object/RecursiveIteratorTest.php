<?php

namespace SimpleAclTest;

use PHPUnit\Framework\TestCase;
use SimpleAcl\BaseObject;
use SimpleAcl\Object\RecursiveIterator;

/**
 * Class RecursiveIteratorTest
 *
 * @package SimpleAclTest
 */
class RecursiveIteratorTest extends TestCase
{
  /**
   * @param mixed $name
   *
   * @return BaseObject|\PHPUnit\Framework\MockObject\MockObject
   */
  protected function getObject($name)
  {
    return $this->getMockForAbstractClass('SimpleAcl\BaseObject', [$name]);
  }

  public function testKey()
  {
    $iterator = new RecursiveIterator([]);
    self::assertNull($iterator->key());

    $iterator = new RecursiveIterator([$this->getObject('Test')]);
    self::assertSame('Test', $iterator->key());
  }

  public function testCurrent()
  {
    $iterator = new RecursiveIterator([]);
    self::assertFalse($iterator->current());

    $test = $this->getObject('Test');
    $iterator = new RecursiveIterator([$test]);
    self::assertSame($test, $iterator->current());
  }

  public function testValidNextRewind()
  {
    $iterator = new RecursiveIterator([]);
    self::assertFalse($iterator->valid());

    $test1 = $this->getObject('Test1');
    $test2 = $this->getObject('Test2');

    $iterator = new RecursiveIterator([$test1, $test2]);
    self::assertTrue($iterator->valid());
    self::assertSame($test1, $iterator->current());
    self::assertSame('Test1', $iterator->key());

    $iterator->next();
    self::assertTrue($iterator->valid());
    self::assertSame($test2, $iterator->current());
    self::assertSame('Test2', $iterator->key());
  }
}
