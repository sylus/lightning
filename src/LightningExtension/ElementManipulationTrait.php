<?php

namespace Acquia\LightningExtension;

use Behat\Mink\Exception\UnsupportedDriverActionException;

/**
 * Contains helper methods for interacting with elements using CSS selectors.
 */
trait ElementManipulationTrait {

  /**
   * Scrolls an element into the viewport.
   *
   * @param string $selector
   *   The element's CSS selector.
   */
  protected function scrollTo($selector) {
    $script = 'document.querySelector("' . addslashes($selector) . '").scrollIntoView()';
    $this->getSession()->executeScript($script);
  }

  /**
   * Clicks an element.
   *
   * @param string $selector
   *   The element's CSS selector.
   */
  protected function clickSelector($selector) {
    $element = $this->assertSession()->elementExists('css', $selector);
    try {
      // Errors may be thrown if the element is not in the viewport.
      $this->scrollTo($selector);
    }
    catch (UnsupportedDriverActionException $e) {
      // Don't worry about it.
    }
    $element->click();
  }

}
