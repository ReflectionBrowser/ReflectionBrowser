<?php

abstract class RBMessage {
  protected $mapHeaders;
  protected $strBody;

  public function __construct(array $mapHeaders = [], $strBody = '') {
    $this->mapHeaders = $mapHeaders;
    $this->strBody = $strBody;
  }

  public function headers() {
    return $this->mapHeaders;
  }

  public function headerKeys() {
    return array_keys($this->mapHeaders);
  }

  public function headerForKey($strKey) {
    if (isset($this->mapHeaders[$strKey])) {
      return $this->mapHeaders[$strKey];
    }

    return null;
  }

  public function setHeaderForKey($strValue, $strKey) {
    $this->mapHeaders[$strName] = $strValue;
  }

  public function body() {
    return $this->strBody;
  }

  public function setBody($strBody) {
    $this->strBody = $strBody;
  }

  public function appendBodyString($strBody) {
    $this->strBody .= $strBody;
  }
}
