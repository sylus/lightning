<?php

namespace Acquia\LightningExtension\Context;

use Acquia\LightningExtension\AwaitTrait;
use Acquia\LightningExtension\TableTrait;
use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Element\NodeElement;
use Drupal\DrupalExtension\Context\DrupalSubContextBase;

/**
 * Contains steps for interacting with the Field UI.
 */
class FieldUiContext extends DrupalSubContextBase {

  use AwaitTrait;
  use TableTrait;

  /**
   * Returns a filter function to find a field's row in a Field UI listing.
   *
   * @param string $id_or_label
   *   The field's ID or label.
   *
   * @return \Closure
   *   The filter function.
   */
  protected function isField($id_or_label) {
    return function (NodeElement $row) use ($id_or_label) {
      return (
        $row->getAttribute('id') == $id_or_label
        ||
        $row->find('css', 'td')->getText() == $id_or_label
      );
    };
  }

  /**
   * Asserts the presence of a field in a Field UI listing.
   *
   * @param string $id_or_label
   *   The field's ID or label.
   *
   * @return \Behat\Mink\Element\NodeElement
   *   The field's row in the listing.
   *
   * @Then the :id_or_label field should be present
   * @Then the field :id_or_label should be present
   */
  public function assertFieldPresent($id_or_label) {
    return $this->assertTableRow('main table', $this->isField($id_or_label));
  }

  /**
   * Asserts the absence of a field in a Field UI listing.
   *
   * @param string $id_or_label
   *   The field's ID or label.
   *
   * @Then the :id_or_label field should not be present
   * @Then the field :id_or_label should not be present
   */
  public function assertFieldNotPresent($id_or_label) {
    $this->assertNotTableRow('main table', $this->isField($id_or_label));
  }

  /**
   * Opens the settings for a field.
   *
   * @param string $id_or_label
   *   The field's ID or label.
   *
   * @return NodeElement
   *   The field's settings form.
   *
   * @When I open the settings for :id_or_label
   * @When I open the settings for the :id_or_label field
   */
  public function openFieldSettings($id_or_label) {
    $row = $this->assertFieldPresent($id_or_label);

    $this->assertSession()
      ->elementExists('css', 'input[name$="_settings_edit"]', $row)
      ->press();

    $this->awaitAjax();

    return $row->find('css', '.field-plugin-settings-edit-form');
  }

  /**
   * Configures a field's widget or display settings.
   *
   * @param string $id_or_label
   *   The field's ID or label.
   * @param \Behat\Gherkin\Node\TableNode $settings
   *   The configuration to apply, as a single-row table where the header row
   *   contains the configuration keys and the first row contains the
   *   configuration values.
   *
   * @When I configure the :id_or_label field with settings:
   */
  public function configureField($id_or_label, TableNode $settings) {
    $form = $this->openFieldSettings($id_or_label);

    foreach ($settings->getHash() as $key => $value) {
      $element = $form->find('css', '[name$="[settings_edit_form][settings][' . $key . ']"]');

      if ($element) {
        if ($element->getTagName() == 'input' && $element->getAttribute('type') == 'checkbox') {
          $value ? $element->check() : $element->uncheck();
        }
        else {
          $element->setValue($value);
        }
      }
    }
    $form->pressButton('Update');
    $this->awaitAjax();
    $this->getSession()->getPage()->pressButton('Save');
  }

}
