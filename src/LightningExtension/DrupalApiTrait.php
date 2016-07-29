<?php

namespace Acquia\LightningExtension;

/**
 * Contains helper methods for interacting with Drupal at the API level.
 */
trait DrupalApiTrait {

  /**
   * Asserts that the Drupal API is available.
   *
   * @throws \RuntimeException
   *   If Drupal is not bootstrapped.
   */
  protected function assertBootstrap() {
    if (!$this->getDriver()->isBootstrapped()) {
      throw new \RuntimeException('Drupal is not bootstrapped.');
    }
  }

}
