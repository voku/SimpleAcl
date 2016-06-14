<?php
namespace SimpleAclTest;

use PHPUnit_Framework_TestCase;
use SimpleAcl\Rule;
use SimpleAcl\RuleResult;

/**
 * Class RuleResultTest
 *
 * @package SimpleAclTest
 */
class RuleResultTest extends PHPUnit_Framework_TestCase
{
  public function testRuleResult()
  {
    $roleAggregate = $this->getMock('SimpleAcl\Role\RoleAggregateInterface');
    $resourceAggregate = $this->getMock('SimpleAcl\Resource\ResourceAggregateInterface');

    $rule = new Rule('Test');

    $rule->setRoleAggregate($roleAggregate);
    $rule->setResourceAggregate($resourceAggregate);
    $rule->setAction(true);
    $result = new RuleResult($rule, 'testNeedRole', 'testNeedResource');

    self::assertSame($rule, $result->getRule());
    self::assertEquals('testNeedRole', $result->getNeedRoleName());
    self::assertEquals('testNeedResource', $result->getNeedResourceName());
    self::assertEquals($rule->getAction($result), $result->getAction());

    self::assertSame($roleAggregate, $result->getRoleAggregate());
    self::assertSame($resourceAggregate, $result->getResourceAggregate());

    self::assertNotEmpty($result->getId());
  }
}
