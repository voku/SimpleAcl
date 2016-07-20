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

    self::assertSame($object->getName(), 'TestName');
    $object->setName('NewName');
    self::assertSame($object->getName(), 'NewName');
  }
}
