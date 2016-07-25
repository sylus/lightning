<?php

/**
 * @file
 * Contains \MediaLibrarySubContext.
 */

use Drupal\DrupalExtension\Context\DrupalSubContextBase;
use Drupal\DrupalExtension\Context\DrupalSubContextInterface;

/**
 * Contains step definitions for testing the CKEditor media widget.
 */
class MediaLibrarySubContext extends DrupalSubContextBase implements DrupalSubContextInterface {

  /**
   * The Mink context.
   *
   * @var \Drupal\DrupalExtension\Context\MinkContext
   */
  protected $minkContext;

  /**
   * Pre-scenario hook.
   *
   * @BeforeScenario
   */
  public function gatherContexts() {
    $this->minkContext = $this->getContext('\Drupal\DrupalExtension\Context\MinkContext');
  }

  /**
   * Opens the media browser, obviously.
   *
   * @param string $button
   *   (optional) The embed button ID.
   *
   * @When I open the media browser
   */
  public function openMediaBrowser($button = 'media_browser') {
    /** @var \CkEditorSubContext $ck_context */
    $ck_context = $this->getContext('\CkEditorSubContext');
    $ck_context->execute('editdrupalentity', NULL, ['id' => $button]);

    $this->minkContext->iWaitForAjaxToFinish();
    // I tried awaiting, but it didn't seem to work.
    sleep(3);
    $this->getSession()->switchToIFrame('entity_browser_iframe_' . $button);
  }

  /**
   * Selects an item from the media library.
   *
   * @param int $n
   *   The one-based index of the item to select.
   *
   * @When I select item :n in the media browser
   */
  public function selectNthItem($n) {
    sleep(5);
    $row = floor($n / 4) + 1;
    $column = ($n % $row) + 1;

    $selector = '.view-media .row-' . $row . ' .col-' . $column;
    /** @var \LightningSubContext $lightning */
    $lightning = $this->getContext('\LightningSubContext');
    $lightning->clickElementBySelector($selector);
  }

  /**
   * Completes the media browser selection.
   *
   * @When I complete the media browser selection
   */
  public function completeSelection() {
    /** @var \Behat\Mink\Session $session */
    $session = $this->getSession();
    $session->switchToWindow();
    $session->executeScript('window.frames.entity_browser_iframe_media_browser.document.forms[0].elements.op.click()');
    $this->await('!window.frames.entity_browser_iframe_media_browser');
    $this->minkContext->iWaitForAjaxToFinish();
    $this->awaitElement('form.entity-embed-dialog');
    $session->executeScript('document.querySelector("form.entity-embed-dialog").elements.op[1].click()');
    $this->minkContext->iWaitForAjaxToFinish();
  }

  /**
   * Awaits the presence of an element on the page.
   *
   * @param string $selector
   *   The element's CSS selector.
   */
  protected function awaitElement($selector) {
    $this->await('document.querySelector("' . addslashes($selector) . '")');
  }

  /**
   * Waits 10 seconds for a JavaScript condition to be truthy.
   *
   * @param string $condition
   *   The JavaScript condition to wait for.
   *
   * @throws \Exception
   *   If the condition times out.
   */
  protected function await($condition) {
    $done = $this->getSession()->wait(10000, $condition);
    if ($done == FALSE) {
      throw new \Exception('JavaScript condition timed out: ' . $condition);
    }
  }

  /**
   * Waits for the inline entity form (containing required fields) to exist.
   */
  protected function awaitEntityForm() {
    $this->minkContext->iWaitForAjaxToFinish();
    $this->await('document.getElementById("ief-target").textContent.length');
  }

  /**
   * Uploads a file in the media browser.
   *
   * @param string $file
   *   The path to the file, relative to the test files directory.
   *
   * @When I upload :file
   */
  public function upload($file) {
    $this->minkContext->clickLink('Upload');
    $this->minkContext->attachFileToField('File', $file);
    $this->awaitEntityForm();
  }

  /**
   * Enters an embed code in the media browser.
   *
   * @param string $code
   *   The embed code.
   *
   * @When I enter embed code :code
   */
  public function embed($code) {
    $this->minkContext->clickLink('Create Embed');
    $this->minkContext->fillField('embed_code', $code);
    // The change event, which triggers AJAX, is fired automatically after 600
    // milliseconds.
    sleep(1);
    $this->awaitEntityForm();
  }

}
