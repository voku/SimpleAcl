<?php

namespace SimpleAcl;

/**
 * Class RuleWide
 *
 * @package SimpleAcl
 */
class RuleWide extends Rule
{
  /**
   * Wide rule always works.
   *
   * @param string|null $neeRuleName
   *
   * @return bool
   */
  protected function isRuleMatched($neeRuleName): bool
  {
    return true;
  }
}
