<?php
namespace SimpleAclTest;

use PHPUnit_Framework_TestCase;
use SimpleAcl\Rule;
use SimpleAcl\RuleResult;
use SimpleAcl\RuleResultCollection;

/**
 * Class RuleResultCollectionTest
 *
 * @package SimpleAclTest
 */
class RuleResultCollectionTest extends PHPUnit_Framework_TestCase
{
  public function testEmpty()
  {
    $collection = new RuleResultCollection();
    self::assertFalse($collection->any());
    self::assertFalse($collection->get());
  }

  public function testAddNull()
  {
    $collection = new RuleResultCollection();

    $collection->add(null);

    self::assertFalse($collection->any());
    self::assertFalse($collection->get());
  }

  public function testAdd()
  {
    $collection = new RuleResultCollection();

    $rule = new Rule('Test');
    $result = new RuleResult($rule, 'testNeedRole', 'testNeedResource');

    $collection->add($result);

    self::assertTrue($collection->any());
    self::assertSame($result->getAction(), $collection->get());

    $index = 0;
    foreach ($collection as $r) {
      self::assertSame($result, $r);
      $index++;
    }
    self::assertSame(0, $index);
  }

  public function testMultipleAdd()
  {
    $collection = new RuleResultCollection();

    $rule = new Rule('Test');
    $result = new RuleResult($rule, 'testNeedRole', 'testNeedResource');

    $rule2 = new Rule('Test2');
    $result2 = new RuleResult($rule2, 'testNeedRole', 'testNeedResource');

    $collection->add($result);
    $collection->add($result2);

    self::assertTrue($collection->any());

    $results = array($result2, $result);

    $index = 0;
    foreach ($collection as $r) {
      //self::assertSame($results[$index], $r);
      $index++;
    }
    self::assertSame(2, $index);
  }

  public function testResultWithNullAction()
  {
    $collection = new RuleResultCollection();

    $rule = new Rule('Test');
    $rule->setAction(null);
    $result = new RuleResult($rule, 'testNeedRole', 'testNeedResource');

    $rule2 = new Rule('Test2');
    $rule2->setAction(true);
    $result2 = new RuleResult($rule2, 'testNeedRole', 'testNeedResource');

    $collection->add($result);
    self::assertFalse($collection->get());

    $collection->add($result2);
    self::assertTrue($collection->get());
  }
}
