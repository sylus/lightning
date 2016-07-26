<?php

namespace Acquia\LightningExtension\Context;

use Acquia\LightningExtension\AwaitTrait;
use Acquia\LightningExtension\ElementManipulationTrait;
use Drupal\DrupalExtension\Context\DrupalSubContextBase;

/**
 * Contains steps for working with Panelizer layouts.
 */
class PanelizerContext extends DrupalSubContextBase {

  use AwaitTrait;
  use ElementManipulationTrait;

  /**
   * Saves the current Panels layout as a custom layout.
   *
   * @When I the layout as the default
   */
  public function saveDefaultLayout() {
    $this->saveLayout('default');
  }

  /**
   * Saves the current Panels layout as a custom layout.
   *
   * @When I save the layout as a custom override
   */
  public function saveCustomLayout() {
    $this->saveLayout('custom');
  }

  /**
   * Saves the Panels IPE layout.
   *
   * @param string $type
   *   The layout type. Can be 'default' or 'custom'.
   */
  protected function saveLayout($type) {
    $this->clickSelector('a[title="Save"]');
    $this->awaitAjax();

    $this->clickSelector('a.panelizer-ipe-save-' . $type);
    $this->awaitAjax();
  }

}
