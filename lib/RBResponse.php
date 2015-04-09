<?php

final class RBResponse extends RBMessage {
  private $intStatusCode = 200;

  public function statusCode() {
    return $this->intStatusCode;
  }

  public function setStatusCode($intStatusCode) {
    $this->intStatusCode = $intStatusCode;
  }
}
