<?php

namespace Acquia\LightningExtension\Context;

use Behat\Mink\Exception\ExpectationException;
use Drupal\Component\Serialization\Json;
use Drupal\DrupalExtension\Context\DrupalSubContextBase;

/**
 * Contains steps for interacting with CKEditor instances.
 */
class CkEditorContext extends DrupalSubContextBase {

  /**
   * Inserts text or HTML into a CKEditor.
   *
   * @param string $text
   *   The text or HTML to insert.
   * @param string $instance
   *   (optional) The instance ID.
   *
   * @When I insert :text into CKEditor
   * @When I insert :text into CKEditor :instance
   */
  public function insert($text, $instance = NULL) {
    $instance = $instance ?: $this->getDefaultInstance();

    $this->getSession()
      ->executeScript('CKEDITOR.instances["' . $instance . '"].insertHtml("' . $text . '")');
  }

  /**
   * Asserts the existence of a CKEditor instance.
   *
   * @param string $id
   *   (optional) The instance ID. If omitted, the assertion will pass if any
   *   CKEditor instances exist.
   *
   * @throws \Behat\Mink\Exception\ExpectationException
   *   If $id is set and no such instance exists, or if $id is null and no
   *   instances exist.
   *
   * @Then CKEditor should be present
   * @Then CKEditor :id should be present
   */
  public function assertInstance($id = NULL) {
    $instances = $this->getInstances();

    $driver = $this->getSession()->getDriver();

    if ($id && !in_array($id, $instances)) {
      throw new ExpectationException('CKEditor ' . $id . ' does not exist.', $driver);
    }
    elseif (empty($instances)) {
      throw new ExpectationException('No CKEditor instances exist.', $driver);
    }
  }

  /**
   * Asserts that a CKEditor's contents contains a string.
   *
   * @param string $text
   *   The string to assert.
   * @param string $instance
   *   (optional) The instance ID.
   *
   * @throws \Behat\Mink\Exception\ExpectationException
   *   If the editor's contents does not contain the string.
   *
   * @Then CKEditor should contain :text
   * @Then CKEditor :instance should contain :text
   */
  public function assertContains($text, $instance = NULL) {
    $contents = $this->getContents($instance);

    if (strpos($contents, $text) === FALSE) {
      throw new ExpectationException(
        'CKEditor contents do not contain text: ' . $text,
        $this->getSession()->getDriver()
      );
    }
  }

  /**
   * Asserts that a CKEditor's contents matches a regular expression.
   *
   * @param string $expression
   *   The regular expression.
   * @param string $instance
   *   (optional) The instance ID.
   *
   * @throws \Behat\Mink\Exception\ExpectationException
   *   If the editor's contents does not match the expression.
   *
   * @Then CKEditor should match :expression
   * @Then CKEditor :instance should match :expression
   */
  public function assertMatch($expression, $instance = NULL) {
    $contents = $this->getContents($instance);

    if (!preg_match($expression, $contents)) {
      throw new ExpectationException(
        'CKEditor contents do not match expression ' . $expression,
        $this->getSession()->getDriver()
      );
    }
  }

  /**
   * Executes a command in a CKEditor instance.
   *
   * @param string $command
   *   The command to execute.
   * @param string $instance
   *   (optional) The instance ID.
   * @param array $data
   *   (optional) Additional data to pass to the command.
   *
   * @return mixed
   *   The value returned by the command.
   *
   * @When I execute the :command command in CKEditor
   * @When I execute the :command command in CKEditor :instance
   */
  public function execute($command, $instance = NULL, array $data = NULL) {
    $instance = $instance ?: $this->getDefaultInstance();

    $js = 'CKEDITOR.instances["' . $instance . '"].execute("' . $command . '"';
    if (isset($data)) {
      $js .= ', ' . Json::encode($data);
    }
    $js .= ')';

    return $this->getSession()->evaluateScript($js);
  }

  /**
   * Returns the contents of a CKEditor instance.
   *
   * @param string $instance
   *   (optional) The instance ID.
   *
   * @return string
   *   The editor's contents.
   */
  protected function getContents($instance = NULL) {
    $instance = $instance ?: $this->getDefaultInstance();

    return $this->getSession()
      ->evaluateScript('CKEDITOR.instances["' . $instance . '"].getData()');
  }

  /**
   * Returns the default CKEditor instance ID.
   *
   * @return string
   *   The instance ID.
   */
  protected function getDefaultInstance() {
    $instances = $this->getInstances();
    return reset($instances);
  }

  /**
   * Returns all CKEditor instance IDs.
   *
   * @return string[]
   *   The instance IDs.
   */
  protected function getInstances() {
    $instances = $this->getSession()
      ->evaluateScript('Object.keys(CKEDITOR.instances).join(",")');

    return explode(',', $instances);
  }

}
