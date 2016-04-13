<?php
namespace SimpleAclTest;

use PHPUnit_Framework_TestCase;
use SimpleAcl\Acl;
use SimpleAcl\Resource;
use SimpleAcl\Role;
use SimpleAcl\Rule;

/**
 * Class AclTest
 *
 * @package SimpleAclTest
 */
class AclTest extends PHPUnit_Framework_TestCase
{
  public function testThrowsExceptionWhenBadRule()
  {
    $acl = new Acl;
    $this->setExpectedException('SimpleAcl\Exception\InvalidArgumentException', 'SimpleAcl\Rule or string');
    $acl->addRule(new Role('User'), new Resource('Page'), new \stdClass(), true);
  }

  public function testThrowsExceptionWhenBadArgumentsCount()
  {
    $this->setExpectedException('SimpleAcl\Exception\InvalidArgumentException', 'accepts only one, tow, three or four arguments');

    $acl = new Acl;
    $acl->addRule(new Role(1), new Resource(1), new Rule(1), true, 'test');
  }

  public function testThrowsExceptionWhenBadRole()
  {
    $this->setExpectedException('SimpleAcl\Exception\InvalidArgumentException', 'Role must be an instance of SimpleAcl\Role or null');

    $acl = new Acl;
    $acl->addRule(new \stdClass(1), new Resource('test'), new Rule('test'), true);
  }

  public function testThrowsExceptionWhenBadResource()
  {
    $this->setExpectedException('SimpleAcl\Exception\InvalidArgumentException', 'Resource must be an instance of SimpleAcl\Resource or null');

    $acl = new Acl;
    $acl->addRule(new Role('test'), new \stdClass(1), new Rule('test'), true);
  }

  public function testSetRuleClassOriginal()
  {
    $acl = new Acl;
    $acl->setRuleClass('SimpleAcl\Rule');

    self::assertEquals('SimpleAcl\Rule', $acl->getRuleClass());
  }

  public function testSetRuleNotExistingClass()
  {
    $this->setExpectedException('SimpleAcl\Exception\RuntimeException', 'Rule class not exist');

    $acl = new Acl;
    $acl->setRuleClass('BadClassTest');

    self::assertEquals('SimpleAcl\Rule', $acl->getRuleClass());
  }

  public function testSetRuleNotInstanceOfRuleClass()
  {
    $this->setExpectedException('SimpleAcl\Exception\RuntimeException', 'Rule class must be instance of SimpleAcl\Rule');

    eval('class NotInstanceOfRuleClass {}');

    $acl = new Acl;
    $acl->setRuleClass('NotInstanceOfRuleClass');

    self::assertEquals('SimpleAcl\Rule', $acl->getRuleClass());
  }

  public function testSetRuleClass()
  {
    eval('class GoodRuleClass extends \SimpleAcl\Rule {}');

    $acl = new Acl;
    $acl->setRuleClass('GoodRuleClass');

    self::assertEquals('GoodRuleClass', $acl->getRuleClass());
  }

  public function testAddSameRules()
  {
    $acl = new Acl;

    $rule = new Rule('Edit');

    $user = new Role('User');
    $page = new Resource('Page');

    $superUser = new Role('SuperUser');
    $superPage = new Resource('SuperPage');

    $acl->addRule($user, $page, $rule, true);

    self::assertSame($rule->getRole(), $user);
    self::assertSame($rule->getResource(), $page);

    // If rule already exist don't add it in Acl, but change Role, Resource and Action
    $acl->addRule($superUser, $superPage, $rule, true);

    self::assertNotSame($rule->getRole(), $user);
    self::assertNotSame($rule->getResource(), $page);

    self::assertSame($rule->getRole(), $superUser);
    self::assertSame($rule->getResource(), $superPage);

    self::assertFalse($acl->isAllowed('User', 'Page', 'Edit'));
    self::assertTrue($acl->isAllowed('SuperUser', 'SuperPage', 'Edit'));

    $acl->addRule($superUser, $superPage, $rule, false);

    self::assertFalse($acl->isAllowed('SuperUser', 'SuperPage', 'Edit'));

    self::assertAttributeCount(1, 'rules', $acl);

    // rule should overwrite $role, $resource and $action when they actually used in addRule

    $acl->addRule($superUser, $superPage, $rule);
    self::assertFalse($acl->isAllowed('SuperUser', 'SuperPage', 'Edit'));
    self::assertAttributeCount(1, 'rules', $acl);

    $acl->addRule($rule);
    self::assertFalse($acl->isAllowed('SuperUser', 'SuperPage', 'Edit'));
    self::assertSame($rule->getRole(), $superUser);
    self::assertSame($rule->getResource(), $superPage);

    $acl->addRule($rule, true);
    self::assertTrue($acl->isAllowed('SuperUser', 'SuperPage', 'Edit'));
    self::assertSame($rule->getRole(), $superUser);
    self::assertSame($rule->getResource(), $superPage);
  }

  public function testRemoveAllRules()
  {
    $acl = new Acl;

    $user = new Role('User');
    $resource = new Resource('Page');

    $acl->addRule($user, $resource, new Rule('View'), true);
    $acl->addRule($user, $resource, new Rule('Edit'), true);
    $acl->addRule($user, $resource, new Rule('Remove'), true);

    self::assertAttributeCount(3, 'rules', $acl);

    $acl->removeAllRules();

    self::assertAttributeCount(0, 'rules', $acl);
  }

  public function testRemoveRuleActAsRemoveAllRules()
  {
    $acl = new Acl;

    $user = new Role('User');
    $resource = new Resource('Page');

    $acl->addRule($user, $resource, new Rule('View'), true);
    $acl->addRule($user, $resource, new Rule('Edit'), true);
    $acl->addRule($user, $resource, new Rule('Remove'), true);

    self::assertAttributeCount(3, 'rules', $acl);

    $acl->removeRule();

    self::assertAttributeCount(0, 'rules', $acl);
  }

  public function testRemoveRuleNotMatch()
  {
    $acl = new Acl;

    $user = new Role('User');
    $moderator = new Role('Moderator');
    $admin = new Role('Admin');

    $page = new Resource('Page');
    $blog = new Resource('Blog');
    $site = new Resource('Site');

    $acl->addRule($user, $page, new Rule('View'), true);
    $acl->addRule($moderator, $blog, new Rule('Edit'), true);
    $acl->addRule($admin, $site, new Rule('Remove'), true);

    self::assertAttributeCount(3, 'rules', $acl);
    $acl->removeRule('RoleNotMatch');
    self::assertAttributeCount(3, 'rules', $acl);

    self::assertAttributeCount(3, 'rules', $acl);
    $acl->removeRule(null, 'ResourceNotMatch');
    self::assertAttributeCount(3, 'rules', $acl);

    self::assertAttributeCount(3, 'rules', $acl);
    $acl->removeRule(null, 'ResourceNotMatch');
    self::assertAttributeCount(3, 'rules', $acl);

    self::assertAttributeCount(3, 'rules', $acl);
    $acl->removeRule(null, null, 'RuleNotMatch');
    self::assertAttributeCount(3, 'rules', $acl);

    self::assertAttributeCount(3, 'rules', $acl);
    $acl->removeRule('RoleNotMatch', 'ResourceNotMatch', 'RuleNotMatch');
    self::assertAttributeCount(3, 'rules', $acl);
  }

  public function testRemoveRule()
  {
    $acl = new Acl;

    $user = new Role('User');
    $moderator = new Role('Moderator');
    $admin = new Role('Admin');

    $page = new Resource('Page');
    $blog = new Resource('Blog');
    $site = new Resource('Site');

    // Remove rules by Role
    $acl->addRule($user, $page, new Rule('View'), true);
    $acl->addRule($user, $blog, new Rule('Edit'), true);
    $acl->addRule($user, $site, new Rule('Remove'), true);

    self::assertAttributeCount(3, 'rules', $acl);
    $acl->removeRule('User');
    self::assertAttributeCount(0, 'rules', $acl);

    $acl->addRule($user, $page, new Rule('View'), true);
    $acl->addRule($user, $blog, new Rule('Edit'), true);
    $acl->addRule($moderator, $site, new Rule('Remove'), true);

    $acl->removeRule('User');
    self::assertAttributeCount(1, 'rules', $acl);

    $acl->removeRule();

    // Remove rules by Resource
    $acl->addRule($user, $page, new Rule('View'), true);
    $acl->addRule($moderator, $page, new Rule('Edit'), true);
    $acl->addRule($admin, $page, new Rule('Remove'), true);

    self::assertAttributeCount(3, 'rules', $acl);
    $acl->removeRule(null, 'Page');
    self::assertAttributeCount(0, 'rules', $acl);

    $acl->addRule($user, $page, new Rule('View'), true);
    $acl->addRule($moderator, $page, new Rule('Edit'), true);
    $acl->addRule($admin, $blog, new Rule('Remove'), true);

    self::assertAttributeCount(3, 'rules', $acl);
    $acl->removeRule(null, 'Page');
    self::assertAttributeCount(1, 'rules', $acl);

    $acl->removeRule();

    // Remove rules by Rule
    $acl->addRule($user, $page, new Rule('View'), true);
    $acl->addRule($moderator, $blog, new Rule('View'), true);
    $acl->addRule($admin, $site, new Rule('View'), true);

    self::assertAttributeCount(3, 'rules', $acl);
    $acl->removeRule(null, null, 'View');
    self::assertAttributeCount(0, 'rules', $acl);

    $acl->addRule($user, $page, new Rule('View'), true);
    $acl->addRule($moderator, $blog, new Rule('View'), true);
    $acl->addRule($admin, $site, new Rule('Edit'), true);

    self::assertAttributeCount(3, 'rules', $acl);
    $acl->removeRule(null, null, 'View');
    self::assertAttributeCount(1, 'rules', $acl);

    $acl->removeRule();

    // Remove rules by Role & Resource & Rule
    $acl->addRule($user, $page, new Rule('View'), true);
    $acl->addRule($moderator, $blog, new Rule('Edit'), true);
    $acl->addRule($admin, $site, new Rule('Remove'), true);

    self::assertAttributeCount(3, 'rules', $acl);
    $acl->removeRule('User', 'Page', 'View');
    self::assertAttributeCount(2, 'rules', $acl);
    $acl->removeRule('Moderator', 'Blog', 'Edit');
    self::assertAttributeCount(1, 'rules', $acl);
    $acl->removeRule('Admin', 'Site', 'Remove');
    self::assertAttributeCount(0, 'rules', $acl);

    // Remove rules by pairs
    $acl->addRule($user, $page, new Rule('View'), true);
    $acl->addRule($moderator, $blog, new Rule('Edit'), true);
    $acl->addRule($admin, $site, new Rule('Remove'), true);

    self::assertAttributeCount(3, 'rules', $acl);
    $acl->removeRule('User', 'Page');
    self::assertAttributeCount(2, 'rules', $acl);
    $acl->removeRule('Moderator', null, 'Edit');
    self::assertAttributeCount(1, 'rules', $acl);
    $acl->removeRule(null, 'Site', 'Remove');
    self::assertAttributeCount(0, 'rules', $acl);

    $acl->removeRule();
  }

  public function testRemoveRuleById()
  {
    $acl = new Acl;

    $user = new Role('User');

    $page = new Resource('Page');

    $rule1 = new Rule('View');
    $rule2 = new Rule('View');
    $rule3 = new Rule('View');

    $acl->addRule($user, $page, $rule1, true);
    $acl->addRule($user, $page, $rule2, true);
    $acl->addRule($user, $page, $rule3, true);

    $acl->removeRuleById('bad_id_test');

    self::assertAttributeCount(3, 'rules', $acl);

    $acl->removeRuleById($rule1->getId());

    self::assertAttributeCount(2, 'rules', $acl);

    $acl->removeRuleById($rule2->getId());
    $acl->removeRuleById($rule3->getId());

    self::assertAttributeCount(0, 'rules', $acl);
  }

  public function testHasRule()
  {
    $acl = new Acl;

    $user = new Role('User');

    $page = new Resource('Page');

    $rule1 = new Rule('View');
    $rule2 = new Rule('View');
    $rule3 = new Rule('View');

    $acl->addRule($user, $page, $rule1, true);
    $acl->addRule($user, $page, $rule2, true);

    self::assertSame($rule1, $acl->hasRule($rule1));
    self::assertSame($rule2, $acl->hasRule($rule2->getId()));
    self::assertFalse($acl->hasRule($rule3));
  }
}