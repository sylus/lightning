<?php

namespace Acquia\LightningExtension\Context;

use Behat\Gherkin\Node\PyStringNode;
use Behat\Mink\Exception\ExpectationException;
use Drupal\DrupalExtension\Context\DrupalSubContextBase;

/**
 * Contains steps for working with permissions.
 */
class PermissionsContext extends DrupalSubContextBase {

  /**
   * Asserts that a role has a set of permissions.
   *
   * @param string $role_id
   *   The role ID.
   * @param \Behat\Gherkin\Node\PyStringNode $permissions
   *   The permissions to assert.
   *
   * @Then the :role_id role should have permissions:
   */
  public function assertPermissions($role_id, PyStringNode $permissions) {
    $this->visitPath('admin/people/permissions/' . $role_id);

    foreach ($permissions->getStrings() as $permission) {
      $this->assertSession()->checkboxChecked($role_id . '[' . $permission . ']');
    }
  }

  /**
   * Asserts that a role does not have a set of permissions.
   *
   * @param string $role_id
   *   The role ID.
   * @param \Behat\Gherkin\Node\PyStringNode $permissions
   *   The permissions to assert.
   *
   * @Then the :role_id role should not have permissions:
   */
  public function assertNotPermissions($role_id, PyStringNode $permissions) {
    $this->visitPath('admin/people/permissions/' . $role_id);

    $assert = $this->assertSession();
    foreach ($permissions->getStrings() as $permission) {
      $checkbox = $role_id . '[' . $permission . ']';
      try {
        $assert->fieldNotExists($checkbox);
      }
      catch (ExpectationException $e) {
        $assert->checkboxNotChecked($checkbox);
      }
    }
  }

}
