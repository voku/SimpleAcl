<?php

namespace SimpleAclTest;

use PHPUnit\Framework\TestCase;
use SimpleAcl\Resource;
use SimpleAcl\Role;
use SimpleAcl\Rule;
use SimpleAcl\RuleResult;

/**
 * Class RuleTest
 *
 * @package SimpleAclTest
 */
class RuleTest extends TestCase
{
  public function testName()
  {
    $rule = new Rule('Rule');
    self::assertSame($rule->getName(), 'Rule');
    $rule->setName('NewRuleName');
    self::assertSame($rule->getName(), 'NewRuleName');
  }

  public function testAction()
  {
    $rule = new Rule('Rule');
    $ruleResult = new RuleResult($rule, 'testNeedRoleName', 'testNeedResourceName');

    $rule->setAction(true);
    self::assertTrue($rule->getAction($ruleResult));
    self::assertTrue($rule->getAction());

    $rule->setAction(false);
    self::assertFalse($rule->getAction($ruleResult));
    self::assertFalse($rule->getAction());

    // Action can be mixed, but getAction must return bool
    $a = [];
    $rule->setAction($a);
    self::assertFalse($rule->getAction($ruleResult));
    self::assertFalse($rule->getAction());
    self::assertAttributeEquals($a, 'action', $rule);

    $a = [1, 2, 3];
    $rule->setAction($a);
    self::assertTrue($rule->getAction($ruleResult));
    self::assertTrue($rule->getAction());
    self::assertAttributeEquals($a, 'action', $rule);
  }

  public function testRolesAndResources()
  {
    $rule = new Rule('Rule');

    $role = new Role('Role');
    $rule->setRole($role);
    self::assertSame($rule->getRole(), $role);

    $resource = new Resource('Resource');
    $rule->setResource($resource);
    self::assertSame($rule->getResource(), $resource);
  }

  public function testId()
  {
    $rule = new Rule('Rule');

    self::assertNotNull($rule->getId());
  }

  public function testActionCallableWithNullRuleResult()
  {
    $rule = new Rule('Rule');
    $ruleResult = new RuleResult($rule, 'testNeedRoleName', 'testNeedResourceName');

    $self = $this;
    $isCalled = false;

    $rule->setAction(
        function () use (&$isCalled, $self) {
          $isCalled = true;

          return false;
        }
    );

    self::assertTrue($rule->getAction());
    self::assertFalse($isCalled);

    self::assertFalse($rule->getAction($ruleResult));
    self::assertTrue($isCalled);
  }

  public function testActionCallable()
  {
    $rule = new Rule('Rule');
    $ruleResult = new RuleResult($rule, 'testNeedRoleName', 'testNeedResourceName');

    $self = $this;
    $isCalled = false;

    $rule->setAction(
        function (RuleResult $r) use (&$isCalled, $self) {
          $isCalled = true;
          $self::assertSame('testNeedRoleName', $r->getNeedRoleName());
          $self::assertSame('testNeedResourceName', $r->getNeedResourceName());

          return true;
        }
    );

    self::assertTrue($rule->getAction($ruleResult));

    self::assertTrue($isCalled);
  }

  public function testNullRoleOrResource()
  {
    $rule = new Rule('Rule');

    self::assertNull($rule->isAllowed('NotMatchedRule', 'Role', 'Resource'));
    self::assertInstanceOf('SimpleAcl\RuleResult', $rule->isAllowed('Rule', 'Role', 'Resource'));

    $rule = new Rule('Rule');
    $rule->setRole(new Role('Role'));

    self::assertNull($rule->isAllowed('Rule', 'NotMatchedRole', 'Resource'));
    self::assertInstanceOf('SimpleAcl\RuleResult', $rule->isAllowed('Rule', 'Role', 'Resource'));

    $rule = new Rule('Rule');
    $rule->setResource(new Resource('Resource'));

    self::assertNull($rule->isAllowed('Rule', 'Role', 'NotMatchedResource'));
    self::assertInstanceOf('SimpleAcl\RuleResult', $rule->isAllowed('Rule', 'Role', 'Resource'));

  }
}
