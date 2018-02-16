<?php

namespace SimpleAclTest;

use PHPUnit\Framework\TestCase;
use SimpleAcl\BaseObject;

/**
 * Class ObjectTest
 *
 * @package SimpleAclTest
 */
class ObjectTest extends TestCase
{
  public function testName()
  {
    /** @var BaseObject $object */
    $object = $this->getMockForAbstractClass('SimpleAcl\BaseObject', ['TestName']);

    self::assertSame($object->getName(), 'TestName');
    $object->setName('NewName');
    self::assertSame($object->getName(), 'NewName');
  }
}
