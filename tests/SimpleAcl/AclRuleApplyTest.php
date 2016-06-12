<?php
namespace SimpleAclTest;

use PHPUnit_Framework_TestCase;
use SimpleAcl\Acl;
use SimpleAcl\Resource;
use SimpleAcl\Resource\ResourceAggregate;
use SimpleAcl\Role;
use SimpleAcl\Role\RoleAggregate;
use SimpleAcl\Rule;
use SimpleAcl\RuleResult;
use SimpleAcl\RuleWide;

/**
 * Class AclRuleApplyTest
 *
 * @package SimpleAclTest
 */
class AclRuleApplyTest extends PHPUnit_Framework_TestCase
{
  public function testEmpty()
  {
    $acl = new Acl;

    self::assertFalse($acl->isAllowed('User', 'Page', 'View'));
  }

  public function testUnDefinedRule()
  {
    $acl = new Acl;
    $acl->addRule(new Role('User'), new Resource('Page'), new Rule('View'), true);

    self::assertFalse($acl->isAllowed('User', 'Page', 'UnDefinedRule'));
  }

  public function testUnDefinedRoleOrResource()
  {
    $acl = new Acl;
    $acl->addRule(new Role('User'), new Resource('Page'), new Rule('View'), true);

    self::assertFalse($acl->isAllowed('NotDefinedRole', 'Page', 'View'));
    self::assertFalse($acl->isAllowed('User', 'NotDefinedResource', 'View'));
    self::assertFalse($acl->isAllowed('NotDefinedRole', 'NotDefinedResource', 'View'));
  }

  public function testOneRoleOneResourceOneRule()
  {
    $acl = new Acl;
    $acl->addRule(new Role('User'), new Resource('Page'), new Rule('View'), true);
    self::assertTrue($acl->isAllowed('User', 'Page', 'View'));

    $acl = new Acl;
    $acl->addRule(new Role('User'), new Resource('Page'), new Rule('View'), false);
    self::assertFalse($acl->isAllowed('User', 'Page', 'View'));
  }

  public function testOneRoleOneResourceMultipleRule()
  {
    $acl = new Acl;

    $user = new Role('User');
    $resource = new Resource('Page');

    $acl->addRule($user, $resource, new Rule('View'), true);
    $acl->addRule($user, $resource, new Rule('Edit'), true);
    $acl->addRule($user, $resource, new Rule('Remove'), true);

    self::assertTrue($acl->isAllowed('User', 'Page', 'View'));
    self::assertTrue($acl->isAllowed('User', 'Page', 'Edit'));
    self::assertTrue($acl->isAllowed('User', 'Page', 'Remove'));

    $acl = new Acl;

    $user = new Role('User');
    $resource = new Resource('Page');

    $acl->addRule($user, $resource, new Rule('View'), false);
    $acl->addRule($user, $resource, new Rule('Edit'), false);
    $acl->addRule($user, $resource, new Rule('Remove'), false);

    self::assertFalse($acl->isAllowed('User', 'Page', 'View'));
    self::assertFalse($acl->isAllowed('User', 'Page', 'Edit'));
    self::assertFalse($acl->isAllowed('User', 'Page', 'Remove'));
  }

  public function testMultipleRolesMultipleResourcesMultipleRules()
  {
    $runChecks = function (PHPUnit_Framework_TestCase $phpUnit, Acl $acl, $allowed) {
      // Checks for page
      $phpUnit::assertEquals($allowed, $acl->isAllowed('User', 'Page', 'View'));
      $phpUnit::assertEquals($allowed, $acl->isAllowed('User', 'Page', 'Edit'));
      $phpUnit::assertEquals($allowed, $acl->isAllowed('User', 'Page', 'Remove'));

      $phpUnit::assertEquals($allowed, $acl->isAllowed('Moderator', 'Page', 'View'));
      $phpUnit::assertEquals($allowed, $acl->isAllowed('Moderator', 'Page', 'Edit'));
      $phpUnit::assertEquals($allowed, $acl->isAllowed('Moderator', 'Page', 'Remove'));

      $phpUnit::assertEquals($allowed, $acl->isAllowed('Admin', 'Page', 'View'));
      $phpUnit::assertEquals($allowed, $acl->isAllowed('Admin', 'Page', 'Edit'));
      $phpUnit::assertEquals($allowed, $acl->isAllowed('Admin', 'Page', 'Remove'));

      // Checks for blog
      $phpUnit::assertEquals($allowed, $acl->isAllowed('User', 'Blog', 'View'));
      $phpUnit::assertEquals($allowed, $acl->isAllowed('User', 'Blog', 'Edit'));
      $phpUnit::assertEquals($allowed, $acl->isAllowed('User', 'Blog', 'Remove'));

      $phpUnit::assertEquals($allowed, $acl->isAllowed('Moderator', 'Blog', 'View'));
      $phpUnit::assertEquals($allowed, $acl->isAllowed('Moderator', 'Blog', 'Edit'));
      $phpUnit::assertEquals($allowed, $acl->isAllowed('Moderator', 'Blog', 'Remove'));

      $phpUnit::assertEquals($allowed, $acl->isAllowed('Admin', 'Blog', 'View'));
      $phpUnit::assertEquals($allowed, $acl->isAllowed('Admin', 'Blog', 'Edit'));
      $phpUnit::assertEquals($allowed, $acl->isAllowed('Admin', 'Blog', 'Remove'));

      // Checks for site
      $phpUnit::assertEquals($allowed, $acl->isAllowed('User', 'Site', 'View'));
      $phpUnit::assertEquals($allowed, $acl->isAllowed('User', 'Site', 'Edit'));
      $phpUnit::assertEquals($allowed, $acl->isAllowed('User', 'Site', 'Remove'));

      $phpUnit::assertEquals($allowed, $acl->isAllowed('Moderator', 'Site', 'View'));
      $phpUnit::assertEquals($allowed, $acl->isAllowed('Moderator', 'Site', 'Edit'));
      $phpUnit::assertEquals($allowed, $acl->isAllowed('Moderator', 'Site', 'Remove'));

      $phpUnit::assertEquals($allowed, $acl->isAllowed('Admin', 'Site', 'View'));
      $phpUnit::assertEquals($allowed, $acl->isAllowed('Admin', 'Site', 'Edit'));
      $phpUnit::assertEquals($allowed, $acl->isAllowed('Admin', 'Site', 'Remove'));
    };

    $acl = new Acl;

    $user = new Role('User');
    $moderator = new Role('Moderator');
    $admin = new Role('Admin');

    $page = new Resource('Page');
    $blog = new Resource('Blog');
    $site = new Resource('Site');

    $runChecks($this, $acl, false);

    // Rules for page
    $acl->addRule($user, $page, new Rule('View'), true);
    $acl->addRule($user, $page, new Rule('Edit'), true);
    $acl->addRule($user, $page, new Rule('Remove'), true);

    $acl->addRule($moderator, $page, new Rule('View'), true);
    $acl->addRule($moderator, $page, new Rule('Edit'), true);
    $acl->addRule($moderator, $page, new Rule('Remove'), true);

    $acl->addRule($admin, $page, new Rule('View'), true);
    $acl->addRule($admin, $page, new Rule('Edit'), true);
    $acl->addRule($admin, $page, new Rule('Remove'), true);

    // Rules for blog
    $acl->addRule($user, $blog, new Rule('View'), true);
    $acl->addRule($user, $blog, new Rule('Edit'), true);
    $acl->addRule($user, $blog, new Rule('Remove'), true);

    $acl->addRule($moderator, $blog, new Rule('View'), true);
    $acl->addRule($moderator, $blog, new Rule('Edit'), true);
    $acl->addRule($moderator, $blog, new Rule('Remove'), true);

    $acl->addRule($admin, $blog, new Rule('View'), true);
    $acl->addRule($admin, $blog, new Rule('Edit'), true);
    $acl->addRule($admin, $blog, new Rule('Remove'), true);

    // Rules for site
    $acl->addRule($user, $site, new Rule('View'), true);
    $acl->addRule($user, $site, new Rule('Edit'), true);
    $acl->addRule($user, $site, new Rule('Remove'), true);

    $acl->addRule($moderator, $site, new Rule('View'), true);
    $acl->addRule($moderator, $site, new Rule('Edit'), true);
    $acl->addRule($moderator, $site, new Rule('Remove'), true);

    $acl->addRule($admin, $site, new Rule('View'), true);
    $acl->addRule($admin, $site, new Rule('Edit'), true);
    $acl->addRule($admin, $site, new Rule('Remove'), true);

    $runChecks($this, $acl, true);

  }

  public function testRoles()
  {
    $acl = new Acl;

    $user = new Role('User');
    $moderator = new Role('Moderator');
    $admin = new Role('Admin');

    $page = new Resource('Page');
    
    $acl->addRule($user, $page, new Rule('View'), true);

    self::assertTrue($acl->isAllowed('User', 'Page', 'View'));
    self::assertFalse($acl->isAllowed('Moderator', 'Page', 'View'));
    self::assertFalse($acl->isAllowed('Admin', 'Page', 'View'));

    $acl = new Acl;
    
    $acl->addRule($admin, $page, new Rule('View'), true);
    
    self::assertTrue($acl->isAllowed('Admin', 'Page', 'View'));

    // but last added rules wins
    $acl->addRule($user, $page, new Rule('View'), false);
    $acl->addRule($moderator, $page, new Rule('View'), false);

    self::assertFalse($acl->isAllowed('User', 'Page', 'View'));
    self::assertFalse($acl->isAllowed('Moderator', 'Page', 'View'));
    self::assertTrue($acl->isAllowed('Admin', 'Page', 'View'));
  }

  public function testResources()
  {
    $acl = new Acl;

    $user = new Role('User');

    $page = new Resource('Page');
    $blog = new Resource('Blog');
    $site = new Resource('Site');
    
    $acl->addRule($user, $page, new Rule('View'), true);

    self::assertTrue($acl->isAllowed('User', 'Page', 'View'));
    self::assertFalse($acl->isAllowed('User', 'Blog', 'View'));
    self::assertFalse($acl->isAllowed('User', 'Site', 'View'));

    $acl = new Acl;
    
    $acl->addRule($user, $site, new Rule('View'), true);

    // but last added rules wins
    $acl->addRule($user, $page, new Rule('View'), false);
    $acl->addRule($user, $blog, new Rule('View'), false);

    self::assertFalse($acl->isAllowed('User', 'Page', 'View'));
    self::assertFalse($acl->isAllowed('User', 'Blog', 'View'));
    self::assertTrue($acl->isAllowed('User', 'Site', 'View'));
  }

  public function testRolesAndResources()
  {
    $user = new Role('User');
    $moderator = new Role('Moderator');
    $admin = new Role('Admin');

    $page = new Resource('Page');
    $blog = new Resource('Blog');
    $site = new Resource('Site');

    $acl = new Acl;

    $acl->addRule($user, $page, new Rule('View'), true);

    self::assertTrue($acl->isAllowed('User', 'Page', 'View'));
    self::assertFalse($acl->isAllowed('Moderator', 'Page', 'View'));
    self::assertFalse($acl->isAllowed('Admin', 'Page', 'View'));

    self::assertFalse($acl->isAllowed('User', 'Blog', 'View'));
    self::assertFalse($acl->isAllowed('Moderator', 'Blog', 'View'));
    self::assertFalse($acl->isAllowed('Admin', 'Blog', 'View'));

    self::assertFalse($acl->isAllowed('User', 'Site', 'View'));
    self::assertFalse($acl->isAllowed('Moderator', 'Site', 'View'));
    self::assertFalse($acl->isAllowed('Admin', 'Site', 'View'));

    $acl = new Acl;

    $acl->addRule($admin, $page, new Rule('View'), true);

    self::assertTrue($acl->isAllowed('Admin', 'Page', 'View'));
    self::assertFalse($acl->isAllowed('Admin', 'Blog', 'View'));
    self::assertFalse($acl->isAllowed('Admin', 'Site', 'View'));

    //self::assertTrue($acl->isAllowed('User', 'Page', 'View'));
    self::assertFalse($acl->isAllowed('User', 'Blog', 'View'));
    self::assertFalse($acl->isAllowed('User', 'Site', 'View'));

    //self::assertTrue($acl->isAllowed('Moderator', 'Page', 'View'));
    self::assertFalse($acl->isAllowed('Moderator', 'Blog', 'View'));
    self::assertFalse($acl->isAllowed('Moderator', 'Site', 'View'));

    $acl = new Acl;

    $acl->addRule($user, $site, new Rule('View'), true);

    self::assertTrue($acl->isAllowed('User', 'Site', 'View'));
    self::assertFalse($acl->isAllowed('Moderator', 'Site', 'View'));
    self::assertFalse($acl->isAllowed('Admin', 'Site', 'View'));

    //self::assertTrue($acl->isAllowed('User', 'Page', 'View'));
    //self::assertFalse($acl->isAllowed('Moderator', 'Page', 'View'));
    //self::assertFalse($acl->isAllowed('Admin', 'Page', 'View'));

    //self::assertTrue($acl->isAllowed('User', 'Blog', 'View'));
    //self::assertFalse($acl->isAllowed('Moderator', 'Blog', 'View'));
    //self::assertFalse($acl->isAllowed('Admin', 'Blog', 'View'));

    // test add rule in the middle
    $acl = new Acl;

    $acl->addRule($moderator, $blog, new Rule('View'), true);

    //self::assertTrue($acl->isAllowed('User', 'Page', 'View'));
    //self::assertTrue($acl->isAllowed('User', 'Blog', 'View'));
    //self::assertFalse($acl->isAllowed('User', 'Site', 'View'));

    //self::assertTrue($acl->isAllowed('Moderator', 'Page', 'View'));
    //self::assertTrue($acl->isAllowed('Moderator', 'Blog', 'View'));
    //self::assertFalse($acl->isAllowed('Moderator', 'Site', 'View'));

    self::assertFalse($acl->isAllowed('Admin', 'Page', 'View'));
    self::assertFalse($acl->isAllowed('Admin', 'Blog', 'View'));
    self::assertFalse($acl->isAllowed('Admin', 'Site', 'View'));

    // test add rule on the top
    $acl = new Acl;

    $acl->addRule($admin, $site, new Rule('View'), true);

    //self::assertTrue($acl->isAllowed('User', 'Page', 'View'));
    //self::assertTrue($acl->isAllowed('User', 'Blog', 'View'));
    //self::assertTrue($acl->isAllowed('User', 'Site', 'View'));

    //self::assertTrue($acl->isAllowed('Admin', 'Page', 'View'));
    //self::assertTrue($acl->isAllowed('Admin', 'Blog', 'View'));
    //self::assertTrue($acl->isAllowed('Admin', 'Site', 'View'));

    //self::assertTrue($acl->isAllowed('Moderator', 'Page', 'View'));
    //self::assertTrue($acl->isAllowed('Moderator', 'Blog', 'View'));
    //self::assertTrue($acl->isAllowed('Moderator', 'Site', 'View'));
  }

  public function testAggregateBadRolesAndResources()
  {
    $acl = new Acl;

    $user = new Role('User');

    $page = new Resource('Page');

    $acl->addRule($user, $page, new Rule('View'), true);

    self::assertFalse($acl->isAllowed('User', new \stdClass(), 'View'));
    self::assertFalse($acl->isAllowed(new \stdClass(), 'Page', 'Edit'));
  }

  public function testAggregateEmptyRolesAndResources()
  {
    $acl = new Acl;

    $user = new Role('User');
    $moderator = new Role('Moderator');
    $admin = new Role('Admin');

    $page = new Resource('Page');
    $blog = new Resource('Blog');
    $site = new Resource('Site');

    $userGroup = new RoleAggregate();
    $siteGroup = new ResourceAggregate();

    $acl->addRule($user, $page, new Rule('View'), true);
    $acl->addRule($moderator, $blog, new Rule('Edit'), true);
    $acl->addRule($admin, $site, new Rule('Remove'), true);

    self::assertFalse($acl->isAllowed($userGroup, $siteGroup, 'View'));
    self::assertFalse($acl->isAllowed($userGroup, $siteGroup, 'Edit'));
    self::assertFalse($acl->isAllowed($userGroup, $siteGroup, 'Remove'));
  }

  public function testAggregateRoles()
  {
    $acl = new Acl;

    $user = new Role('User');
    $moderator = new Role('Moderator');
    $admin = new Role('Admin');

    $page = new Resource('Page');
    $blog = new Resource('Blog');
    $site = new Resource('Site');

    $userGroup = new RoleAggregate();

    $userGroup->addRole($user);
    $userGroup->addRole($moderator);
    $userGroup->addRole($admin);

    $acl->addRule($user, $page, new Rule('View'), true);
    $acl->addRule($moderator, $blog, new Rule('Edit'), true);
    $acl->addRule($admin, $site, new Rule('Remove'), true);

    self::assertTrue($acl->isAllowed($userGroup, 'Page', 'View'));
    self::assertTrue($acl->isAllowed($userGroup, 'Blog', 'Edit'));
    self::assertTrue($acl->isAllowed($userGroup, 'Site', 'Remove'));
  }

  public function testAggregateResources()
  {
    $acl = new Acl;

    $user = new Role('User');
    $moderator = new Role('Moderator');
    $admin = new Role('Admin');

    $page = new Resource('Page');
    $blog = new Resource('Blog');
    $site = new Resource('Site');

    $siteGroup = new ResourceAggregate();

    $siteGroup->addResource($page);
    $siteGroup->addResource($blog);
    $siteGroup->addResource($site);

    $acl->addRule($user, $page, new Rule('View'), true);
    $acl->addRule($moderator, $blog, new Rule('Edit'), true);
    $acl->addRule($admin, $site, new Rule('Remove'), true);

    self::assertTrue($acl->isAllowed('User', $siteGroup, 'View'));
    self::assertTrue($acl->isAllowed('Moderator', $siteGroup, 'Edit'));
    self::assertTrue($acl->isAllowed('Admin', $siteGroup, 'Remove'));
  }

  public function testAggregateRolesAndResources()
  {
    $acl = new Acl;

    $user = new Role('User');
    $moderator = new Role('Moderator');
    $admin = new Role('Admin');

    $page = new Resource('Page');
    $blog = new Resource('Blog');
    $site = new Resource('Site');

    $userGroup = new RoleAggregate();
    $userGroup->addRole($user);
    $userGroup->addRole($moderator);
    $userGroup->addRole($admin);

    $siteGroup = new ResourceAggregate();
    $siteGroup->addResource($page);
    $siteGroup->addResource($blog);
    $siteGroup->addResource($site);

    $acl->addRule($user, $page, new Rule('View'), true);
    $acl->addRule($moderator, $blog, new Rule('Edit'), true);
    $acl->addRule($admin, $site, new Rule('Remove'), true);

    self::assertTrue($acl->isAllowed($userGroup, $siteGroup, 'View'));
    self::assertTrue($acl->isAllowed($userGroup, $siteGroup, 'Edit'));
    self::assertTrue($acl->isAllowed($userGroup, $siteGroup, 'Remove'));
  }

  public function testStringAsRule()
  {
    $acl = new Acl;

    $user = new Role('User');
    $resource = new Resource('Page');

    $acl->addRule($user, $resource, 'View', true);
    $acl->addRule($user, $resource, 'Edit', true);
    $acl->addRule($user, $resource, 'Remove', true);

    self::assertTrue($acl->isAllowed('User', 'Page', 'View'));
    self::assertTrue($acl->isAllowed('User', 'Page', 'Edit'));
    self::assertTrue($acl->isAllowed('User', 'Page', 'Remove'));

    $acl = new Acl;

    $acl->setRuleClass('SimpleAcl\Rule');

    $user = new Role('User');
    $resource = new Resource('Page');

    $acl->addRule($user, $resource, 'View', false);
    $acl->addRule($user, $resource, 'Edit', false);
    $acl->addRule($user, $resource, 'Remove', false);

    self::assertFalse($acl->isAllowed('User', 'Page', 'View'));
    self::assertFalse($acl->isAllowed('User', 'Page', 'Edit'));
    self::assertFalse($acl->isAllowed('User', 'Page', 'Remove'));
  }

  public function testGetResult()
  {
    $self = $this;

    $testReturnResult = function ($result, $expected) use ($self) {
      $index = 0;
      foreach ($result as $r) {
        /* @var RuleResult $r */
        //$self::assertSame($expected[$index], $r->getRule());
        $index++;
      }
      /** @noinspection PhpUnitTestsInspection */
      if ($index != 1) {
        $self::assertEquals(count($expected), $index);
      }
    };

    $acl = new Acl;

    $user = new Role('User');
    $resource = new Resource('Page');

    $view = new Rule('View');
    $edit = new Rule('Edit');
    $remove = new Rule('Remove');

    $acl->addRule($user, $resource, $view, true);
    $acl->addRule($user, $resource, $edit, true);
    $acl->addRule($user, $resource, $remove, true);

    self::assertTrue($acl->isAllowed('User', 'Page', 'View'));
    self::assertTrue($acl->isAllowed('User', 'Page', 'Edit'));
    self::assertTrue($acl->isAllowed('User', 'Page', 'Remove'));

    $testReturnResult($acl->isAllowedReturnResult('User', 'Page', 'View'), array($view));
    $testReturnResult($acl->isAllowedReturnResult('User', 'Page', 'Edit'), array($edit));
    $testReturnResult($acl->isAllowedReturnResult('User', 'Page', 'Remove'), array($remove));

    $acl = new Acl;

    $acl->addRule($user, $resource, $view, false);
    $acl->addRule($user, $resource, $edit, false);
    $acl->addRule($user, $resource, $remove, false);

    self::assertFalse($acl->isAllowed('User', 'Page', 'View'));
    self::assertFalse($acl->isAllowed('User', 'Page', 'Edit'));
    self::assertFalse($acl->isAllowed('User', 'Page', 'Remove'));

    $testReturnResult($acl->isAllowedReturnResult('User', 'Page', 'View'), array($view));
    $testReturnResult($acl->isAllowedReturnResult('User', 'Page', 'Edit'), array($edit));
    $testReturnResult($acl->isAllowedReturnResult('User', 'Page', 'Remove'), array($remove));

    // test RuleResult order
    $acl = new Acl;

    $view1 = new Rule('View');
    $view2 = new Rule('View');
    $view3 = new Rule('View');
    $view4 = new Rule('View');

    $acl->addRule($user, $resource, $view, false);
    $acl->addRule($user, $resource, $view1, true);
    $acl->addRule($user, $resource, $view2, false);
    $acl->addRule($user, $resource, $view3, true);
    $acl->addRule($user, $resource, $view4, false);

    $testReturnResult(
        $acl->isAllowedReturnResult('User', 'Page', 'View'),
        array(
            $view4,
            $view3,
            $view2,
            $view1,
            $view,
        )
    );
  }

  public function testRuleWithNullActionNotCounts()
  {
    $acl = new Acl;

    $user = new Role('User');
    $resource = new Resource('Resource');

    $nullAction = new Rule('View');

    $acl->addRule($user, $resource, 'View', true);
    $acl->addRule($user, $resource, $nullAction, null);

    self::assertTrue($acl->isAllowed('User', 'Resource', 'View'));
  }

  public function testActionCallable()
  {
    $acl = new Acl;

    $user = new Role('User');
    $resource = new Resource('Resource');

    $acl->addRule(
        $user, $resource, 'View', function () {
      return true;
    }
    );

    self::assertTrue($acl->isAllowed('User', 'Resource', 'View'));
  }

  public function testSetAggregates()
  {
    $acl = new Acl();

    $u = new Role('U');
    $r = new Resource('R');

    $roleAgr = new RoleAggregate();
    $roleAgr->addRole($u);

    $resourceAgr = new ResourceAggregate();
    $resourceAgr->addResource($r);

    $self = $this;

    $rule = new Rule('View');

    $acl->addRule(
        $u, $r, $rule, function (RuleResult $r) use ($roleAgr, $resourceAgr, $self) {
      $self::assertSame($roleAgr, $r->getRoleAggregate());
      $self::assertSame($resourceAgr, $r->getResourceAggregate());

      return true;
    }
    );

    self::assertTrue($acl->isAllowed($roleAgr, $resourceAgr, 'View'));

    $rule->setAction(
        function (RuleResult $r) use ($self) {
//          $self::assertNull($r->getRoleAggregate());
//          $self::assertNull($r->getResourceAggregate());

          return true;
        }
    );

    self::assertTrue($acl->isAllowed('U', 'R', 'View'));
  }

  public function testAddRuleOneArgument()
  {
    $acl = new Acl();

    $rule = new Rule('View');

    $acl->addRule($rule);

    // only action determines is access allowd or not for rule with null role and resource
    self::assertFalse($acl->isAllowed('U', 'R', 'View'));

    $rule->setAction(true);

    // rule matched any role or resource as it have null for both
    self::assertTrue($acl->isAllowed('U', 'R', 'View'));

    // nothing is change if only one argument use, action is not overwritten to null
    $acl->addRule($rule);
    self::assertTrue($acl->isAllowed('U', 'R', 'View'));

    // rule not matched if wrong rule name used
    self::assertFalse($acl->isAllowed('U', 'R', 'NotMatchedView'));

    $u = new Role('U1');
    $rule->setRole($u);

    $r = new Resource('R1');
    $rule->setResource($r);

    $acl->addRule($rule);

    self::assertFalse($acl->isAllowed('U', 'R', 'View'));
    // role and resource not overwritten
    self::assertTrue($acl->isAllowed('U1', 'R1', 'View'));
    self::assertSame($u, $rule->getRole());
    self::assertSame($r, $rule->getResource());
  }

  public function testAddRuleTowArguments()
  {
    $acl = new Acl();

    $rule = new Rule('View');

    $rule->setAction(false);

    // rule overwrite action
    $acl->addRule($rule, true);

    // rule matched any role or resource as it have null for both
    self::assertTrue($acl->isAllowed('U', 'R', 'View'));

    // rule not matched if wrong rule name used
    self::assertFalse($acl->isAllowed('U', 'R', 'NotMatchedView'));

    $u = new Role('U1');
    $rule->setRole($u);

    $r = new Resource('R1');
    $rule->setResource($r);

    $acl->addRule($rule, true);

    self::assertFalse($acl->isAllowed('U', 'R', 'View'));
    // role and resource not overwritten
    self::assertTrue($acl->isAllowed('U1', 'R1', 'View'));

    $acl->addRule($rule, null);

    self::assertNull($rule->getAction());
  }

  public function testAddRuleThreeArguments()
  {
    $acl = new Acl();

    $rule = new Rule('View');

    $rule->setAction(false);

    $u = new Role('U');
    $r = new Resource('R');

    $acl->addRule($u, $r, $rule);

    self::assertFalse($acl->isAllowed('U', 'R', 'View'));
    $rule->setAction(true);
    self::assertTrue($acl->isAllowed('U', 'R', 'View'));

    $u1 = new Role('U1');
    $r1 = new Resource('R1');

    // role and resource changed
    $acl->addRule($u1, $r1, $rule);

    self::assertSame($u1, $rule->getRole());
    self::assertSame($r1, $rule->getResource());

    self::assertFalse($acl->isAllowed('U', 'R', 'View'));
    self::assertTrue($acl->isAllowed('U1', 'R1', 'View'));
  }

  public function testRuleOrResourceNull()
  {
    $acl = new Acl();

    $rule = new Rule('View');

    $rule->setAction(false);

    $u = new Role('U');
    $r = new Resource('R');

    $acl->addRule(null, $r, $rule, true);

    self::assertTrue($acl->isAllowed('Any', 'R', 'View'));
    self::assertFalse($acl->isAllowed('Any', 'R1', 'View'));
    self::assertNull($rule->getRole());
    self::assertSame($r, $rule->getResource());

    $acl->addRule($u, null, $rule, true);
    self::assertTrue($acl->isAllowed('U', 'Any', 'View'));
    self::assertFalse($acl->isAllowed('U1', 'Any', 'View'));
    self::assertNull($rule->getResource());
    self::assertSame($u, $rule->getRole());
  }

  public function testRuleWide()
  {
    $acl = new Acl();

    $rule = new RuleWide('RuleWide');

    $u = new Role('U');
    $r = new Resource('R');

    $acl->addRule($u, $r, $rule, true);

    self::assertTrue($acl->isAllowed('U', 'R', 'View'));
    self::assertTrue($acl->isAllowed('U', 'R', null));
    self::assertFalse($acl->isAllowed('NotExist', 'R', null));
    self::assertFalse($acl->isAllowed('U', 'NotExist', null));

    // null role and resource
    $acl = new Acl();

    $acl->addRule(null, null, $rule, true);

    self::assertTrue($acl->isAllowed('U', 'R', 'View'));
    self::assertTrue($acl->isAllowed('U', 'R', null));

    self::assertTrue($acl->isAllowed(null, null, null));
    $acl->removeRuleById($rule->getId());
    self::assertFalse($acl->isAllowed(null, null, null));

    // null resource
    $acl = new Acl();

    $u = new Role('U');

    $acl->addRule($u, null, $rule, true);

    self::assertTrue($acl->isAllowed('U', 'R', 'View'));
    self::assertTrue($acl->isAllowed('U', 'R', null));
    self::assertTrue($acl->isAllowed('U', null, 'View'));
    self::assertFalse($acl->isAllowed('NotExist', 'R', 'View'));
    self::assertFalse($acl->isAllowed(null, 'R', 'View'));

    // null role
    $acl = new Acl();

    $r = new Resource('R');

    $acl->addRule(null, $r, $rule, true);

    self::assertTrue($acl->isAllowed('U', 'R', 'View'));
    self::assertTrue($acl->isAllowed('U', 'R', null));
    self::assertTrue($acl->isAllowed(null, 'R', 'View'));
    self::assertFalse($acl->isAllowed('U', 'NotExist', 'View'));
    self::assertFalse($acl->isAllowed('U', null, 'View'));
  }

  /**
   * Testing edge conditions.
   */

  public function testEdgeConditionRolesAndResourcesWithMultipleRules()
  {
    $user = new Role('User');
    $moderator = new Role('Moderator');

    $page = new Resource('Page');
    $blog = new Resource('Blog');

    $acl = new Acl;

    $acl->addRule($moderator, $blog, new Rule('View'), true);
    $acl->addRule($user, $page, new Rule('View'), false);

    self::assertFalse($acl->isAllowed('User', 'Page', 'View'));
    self::assertFalse($acl->isAllowed('User', 'Site', 'View'));

    self::assertTrue($acl->isAllowed('Moderator', 'Blog', 'View'));
    self::assertFalse($acl->isAllowed('Moderator', 'Site', 'View'));

    self::assertFalse($acl->isAllowed('Admin', 'Page', 'View'));
    self::assertFalse($acl->isAllowed('Admin', 'Blog', 'View'));
    self::assertFalse($acl->isAllowed('Admin', 'Site', 'View'));
  }

  public function testEdgeConditionAggregate()
  {
    $acl = new Acl;

    $user = new Role('User');
    $moderator = new Role('Moderator');

    $page = new Resource('Page');

    $userGroup = new RoleAggregate();

    $userGroup->addRole($moderator);
    $userGroup->addRole($user);

    $acl->addRule($user, $page, new Rule('View'), true);
    //$acl->addRule($moderator, $page, new Rule('View'), false);

    self::assertTrue($acl->isAllowed($userGroup, 'Page', 'View'));

    $userGroup->removeRole('User');
    self::assertFalse($acl->isAllowed($userGroup, 'Page', 'View'));
    $userGroup->addRole($user);
    self::assertTrue($acl->isAllowed($userGroup, 'Page', 'View'));

    $acl = new Acl;

    $userGroup = new RoleAggregate();

    $userGroup->addRole($moderator);
    $userGroup->addRole($user);

    // changing rule orders don't change result
    $acl->addRule($moderator, $page, new Rule('View'), true);
    $acl->addRule($user, $page, new Rule('View'), true);

    self::assertTrue($acl->isAllowed($userGroup, 'Page', 'View'));

    $userGroup->removeRole('User');
    self::assertTrue($acl->isAllowed($userGroup, 'Page', 'View'));
    $userGroup->addRole($user);
    self::assertTrue($acl->isAllowed($userGroup, 'Page', 'View'));

    // test case when priority matter
    $acl = new Acl;

    $userGroup = new RoleAggregate();

    $userGroup->addRole($moderator);
    $userGroup->addRole($user);

    $contact = new Resource('Contact');

    $acl->addRule($moderator, $contact, new Rule('View'), true);
    $acl->addRule($user, $page, new Rule('View'), true);

    // user rule match first but moderator has higher priority
    self::assertTrue($acl->isAllowed($userGroup, 'Contact', 'View'));

    $acl->addRule($user, $contact, new Rule('View'), false);

    // priorities are equal
    self::assertTrue($acl->isAllowed($userGroup, 'Contact', 'View'));
  }

  public function testMoreRule()
  {
    $acl = new Acl();
    $rule = new Rule('View');
    $u = new Role('U');
    $r = new Resource('R');
    $acl->addRule($u, $r, $rule, true);
    self::assertTrue($acl->isAllowed('U', 'R', 'View'));
    self::assertFalse($acl->isAllowed('U', 'R1', 'View'));

    $acl = new Acl();
    $rule = new Rule('View');
    $u = new Role('U');
    $r = new Resource('R');
    $acl->addRule($u, $r, $rule, true);
    self::assertTrue($acl->isAllowed('U', 'R', 'View'));

    $acl = new Acl();
    $rule = new Rule('View');
    $u = new Role('U');
    $r = new Resource('R');
    $acl->addRule($u, $r, $rule, true);
    self::assertFalse($acl->isAllowed('anything', 'anything', 'anything'));
  }
}
