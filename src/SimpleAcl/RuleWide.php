<?php
namespace SimpleAcl;

/**
 * Class RuleWide
 *
 * @package SimpleAcl
 */
class RuleWide extends Rule
{
  /** @noinspection PhpMissingParentCallCommonInspection */
  /**
   * Wide rule always works.
   *
   * @param string $neeRuleName
   *
   * @return bool
   */
  protected function isRuleMatched($neeRuleName)
  {
    return true;
  }
}
