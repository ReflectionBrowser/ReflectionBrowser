<?php

final class RBErrorException extends ErrorException {
  private $mapContext;

  public function __construct ($strMessage, $intSeverity, $strFileName, $intFileLine, array $mapContext) {
    parent::__construct($strMessage, 0, $intSeverity, $strFileName, $intFileLine);
    $this->mapContext = $mapContext;
  }

  public function getContext() {
    if (empty($this->mapContext)) {
      return null;
    }

    return $this->mapContext;
  }
}
