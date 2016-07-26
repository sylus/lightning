<?php

namespace Acquia\LightningExtension\Context;

use Acquia\LightningExtension\AwaitTrait;
use Acquia\LightningExtension\ElementManipulationTrait;
use Drupal\DrupalExtension\Context\DrupalSubContextBase;

/**
 * Contains steps for dealing with Panels layouts using the in-place editor.
 */
class PanelsInPlaceEditorContext extends DrupalSubContextBase {

  use AwaitTrait;
  use ElementManipulationTrait;

  /**
   * Places a block into a Panels layout.
   *
   * @param string $plugin_id
   *   The block plugin ID.
   * @param string $category
   *   The block's category.
   *
   * @When I place the :plugin_id block from the :category category
   */
  public function placeBlock($plugin_id, $category) {
    $this->clickSelector('a[title="Manage Content"]');
    $this->awaitAjax();

    $this->clickSelector('a[data-category="' . $category . '"]');
    $this->awaitAjax();

    $this->clickSelector('a[data-plugin-id="' . $plugin_id . '"]');
    $this->awaitAjax();

    $this->clickSelector('[value="Add"]');
    $this->awaitAjax();
  }

  /**
   * Asserts that a block with a specific plugin ID is present.
   *
   * @param string $plugin_id
   *   The block plugin ID.
   *
   * @return \Behat\Mink\Element\NodeElement
   *   The block element.
   *
   * @Then I should see a :plugin_id block
   */
  public function assertBlockPresent($plugin_id) {
    return $this->assertSession()->elementExists('css', '[data-block-plugin-id="' . $plugin_id . '"]');
  }

  /**
   * Asserts that a block with a specific plugin ID is not present.
   *
   * @param string $plugin_id
   *   The block plugin ID.
   *
   * @Then I should not see a :plugin_id block
   */
  public function assertBlockNotPresent($plugin_id) {
    $this->assertSession()->elementNotExists('css', '[data-block-plugin-id ="' . $plugin_id . '"]');
  }

}
