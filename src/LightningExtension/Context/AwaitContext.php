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

  /**
   * Waits for an iFrame to exist.
   *
   * @param string $frame
   *   The iFrame's name.
   * @param int $timeout
   *   (optional) How many seconds to wait before timing out.
   *
   * @When I wait for the :frame frame to appear
   * @When I wait :timeout seconds for the :frame frame to appear
   *
   * @Then the :frame frame should exist
   */
  public function awaitFrameStep($frame, $timeout = 10) {
    $this->awaitFrame($frame, $timeout);
  }

}
