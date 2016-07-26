<?php

namespace Acquia\LightningExtension;

use Behat\Mink\Exception\ExpectationException;

/**
 * Contains helper methods for dealing with HTML tables.
 */
trait TableTrait {

  /**
   * Asserts the presence of a table row that passes a filter function.
   *
   * @param string $table_selector
   *   The table's CSS selector.
   * @param callable $filter
   *   The filter function.
   *
   * @return \Behat\Mink\Element\NodeElement
   *   The table row element.
   *
   * @throws \Behat\Mink\Exception\ExpectationException
   *   If no rows passed the filter function.
   */
  protected function assertTableRow($table_selector, callable $filter) {
    $row = $this->getTableRow($table_selector, $filter);

    if (empty($row)) {
      throw new ExpectationException(
        'No rows in ' . $table_selector . ' matched filter function.',
        $this->getSession()->getDriver()
      );
    }
    return $row;
  }

  /**
   * Returns the first row in a table that passes a filter function.
   *
   * @param string $table_selector
   *   The table's CSS selector.
   * @param callable $filter
   *   The filter function.
   *
   * @return \Behat\Mink\Element\NodeElement|false
   *   The first row to pass the filter function, or false if no rows passed.
   */
  protected function getTableRow($table_selector, callable $filter) {
    $rows = $this->getTableRows($table_selector, $filter);

    return reset($rows);
  }

  /**
   * Returns all rows in a table, optionally filtered by a function.
   *
   * @param string $table_selector
   *   The table's CSS selector.
   * @param callable|null $filter
   *   (optional) The filter function. If omitted, all rows will be returned.
   *
   * @return \Behat\Mink\Element\NodeElement[]
   *   The table row elements.
   */
  protected function getTableRows($table_selector, callable $filter = NULL) {
    $table = $this->assertSession()->elementExists('css', $table_selector);

    return array_filter(
      ($table->find('css', 'tbody') ?: $table)->findAll('css', 'tr'),
      $filter
    );
  }

}
