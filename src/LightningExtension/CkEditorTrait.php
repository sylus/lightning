<?php

namespace Acquia\LightningExtension;

use Drupal\Component\Serialization\Json;

/**
 * Contains helper methods for interacting with CKEditor instances.
 */
trait CkEditorTrait {

  use AwaitTrait;

  /**
   * Awaits the existence of a CKEditor instance.
   *
   * @param string $id
   *   The instance ID.
   */
  protected function awaitEditor($id) {
    $this->awaitExpression('CKEDITOR.instances["' . $id . '"]');
  }

  /**
   * Returns the name of a CKEditor instance's iFrame.
   *
   * @param string $id
   *   The instance ID.
   *
   * @return string
   *   The randomly-generated frame name.
   */
  protected function getEditorFrame($id) {
    $frame = uniqid('ckeditor_');

    $this->getSession()
      ->executeScript('CKEDITOR.instances["' . $id . '"].window.$.frameElement.name = "' . $frame . '"');

    return $frame;
  }

  /**
   * Inserts HTML code into a CKEditor instance.
   *
   * @param string $id
   *   The instance ID.
   * @param string $text
   *   The HTML code to insert.
   */
  protected function insertEditorText($id, $text) {
    $this->getSession()
      ->executeScript('CKEDITOR.instances["' . $id . '"].insertHtml("' . $text . '")');
  }

  /**
   * Executes a command in a CKEditor instance.
   *
   * @param string $id
   *   The instance ID.
   * @param string $command
   *   The command to execute.
   * @param array $data
   *   Optional data to pass to the command.
   *
   * @return mixed
   *   The value returned by the command.
   */
  protected function executeEditorCommand($id, $command, array $data = NULL) {
    $js = 'CKEDITOR.instances["' . $id . '"].execute("' . $command . '"';

    if (isset($data)) {
      $js .= ', ' . Json::encode($data);
    }
    $js .= ')';

    return $this->getSession()->evaluateScript($js);
  }

  /**
   * Returns the contents of a CKEditor instance.
   *
   * @param string $id
   *   The instance ID.
   *
   * @return string
   *   The editor's contents.
   */
  protected function getEditorContents($id) {
    $this->awaitEditor($id);

    return $this->getSession()
      ->evaluateScript('CKEDITOR.instances["' . $id . '"].getData()');
  }

  /**
   * Returns all active CKEditor instance IDs.
   *
   * @return string[]
   *   The current CKEditor instance IDs.
   */
  protected function getEditorInstances() {
    $instances = $this->getSession()
      ->evaluateScript('Object.keys(CKEDITOR.instances).join(",")');

    return explode(',', $instances);
  }

}
