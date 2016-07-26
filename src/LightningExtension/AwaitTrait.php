<?php

namespace Acquia\LightningExtension;

use Drupal\DrupalExtension\Context\MinkContext;

/**
 * Contains helper methods for awaiting various conditions.
 */
trait AwaitTrait {

  /**
   * Waits for a JavaScript expression to become truthy.
   *
   * @param string $expression
   *   The JavaScript expression to await.
   * @param int $timeout
   *   (optional) How many seconds to wait before timing out.
   *
   * @throws \Exception
   *   If the expression times out.
   */
  protected function awaitExpression($expression, $timeout = 10) {
    $done = $this->getSession()->wait($timeout * 1000, $expression);

    if (!$done) {
      throw new \Exception('JavaScript expression timed out: ' . $expression);
    }
  }

  /**
   * Waits for jQuery AJAX operations to complete.
   *
   * This will use the Drupal Extension's MinkContext::iWaitForAjaxToFinish()
   * method if available, otherwise it will wait the full timeout.
   *
   * @param int $timeout
   *   (optional) How many seconds to wait before timing out.
   */
  protected function awaitAjax($timeout = 10) {
    $mink_context = $this->getContext(MinkContext::class);

    if ($mink_context) {
      $mink_context->iWaitForAjaxToFinish();
    }
    else {
      sleep($timeout);
    }
  }

  /**
   * Waits for an element to be present.
   *
   * @param string $selector
   *   The CSS selector.
   * @param int $timeout
   *   (optional) How many seconds to wait before timing out.
   */
  protected function awaitElement($selector, $timeout = 10) {
    $this->awaitExpression('document.querySelector("' . addslashes($selector) . '")', $timeout);
  }

  /**
   * Waits for an iFrame to exist.
   *
   * @param string $frame
   *   The iFrame's name.
   * @param int $timeout
   *   (optional) How many seconds to wait before timing out.
   */
  public function awaitFrame($frame, $timeout = 10) {
    $this->awaitExpression('window.frames["' . $frame . '"]', $timeout);
  }

}
