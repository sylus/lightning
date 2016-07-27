<?php

namespace Acquia\LightningExtension\Exception;

/**
 * Exception thrown when a JavaScript expression times out.
 */
class TimeoutException extends \Exception {

  /**
   * TimeoutException constructor.
   *
   * @param string $expression
   *   The JavaScript expression that timed out.
   * @param \Exception $previous
   *   (optional) The previous exception.
   */
  public function __construct($expression, \Exception $previous = NULL) {
    parent::__construct('JavaScript expression timed out: ' . $expression, 0, $previous);
  }

}
