<?php

namespace Acquia\LightningExtension\Context;

use Acquia\LightningExtension\AwaitTrait;
use Drupal\DrupalExtension\Context\DrupalSubContextBase;

/**
 * Contains steps for awaiting various conditions.
 */
class AwaitContext extends DrupalSubContextBase {

  use AwaitTrait;

  /**
   * Waits for an element to exist.
   *
   * @param string $selector
   *   The CSS selector to wait for.
   * @param int $timeout
   *   (optional) How many seconds to wait before timing out.
   *
   * @When I wait for the :selector element to exist
   * @When I wait :timeout seconds for the :selector element to exist
   */
  public function awaitElementStep($selector, $timeout = 10) {
    $this->awaitElement($selector, $timeout);
  }

}
