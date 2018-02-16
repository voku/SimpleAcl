<?php

namespace SimpleAclTest;

use PHPUnit\Framework\TestCase;
use SimpleAcl\Rule;
use SimpleAcl\RuleResult;

/**
 * Class RuleResultTest
 *
 * @package SimpleAclTest
 */
class RuleResultTest extends TestCase
{
  public function testRuleResult()
  {
    $roleAggregate = $this->createMock('SimpleAcl\Role\RoleAggregateInterface');
    $resourceAggregate = $this->createMock('SimpleAcl\Resource\ResourceAggregateInterface');

    $rule = new Rule('Test');

    $rule->setRoleAggregate($roleAggregate);
    $rule->setResourceAggregate($resourceAggregate);
    $rule->setAction(true);
    $result = new RuleResult($rule, 'testNeedRole', 'testNeedResource');

    self::assertSame($rule, $result->getRule());
    self::assertSame('testNeedRole', $result->getNeedRoleName());
    self::assertSame('testNeedResource', $result->getNeedResourceName());
    self::assertSame($rule->getAction($result), $result->getAction());

    self::assertSame($roleAggregate, $result->getRoleAggregate());
    self::assertSame($resourceAggregate, $result->getResourceAggregate());

    self::assertNotEmpty($result->getId());
  }
}
