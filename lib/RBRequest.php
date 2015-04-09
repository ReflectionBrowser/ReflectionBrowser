<?php

final class RBRequest extends RBMessage {
  private $strURI;
  private $mapQueryParameters;

  public function __construct(array $mapHeaders, $strBody, $strURI, array $mapQueryParameters) {
    parent::__construct($mapHeaders, $strBody);

    $this->strURI = $strURI;
    $this->mapQueryParameters = $mapQueryParameters;
  }

  public function URI() {
    return $this->strURI;
  }

  public function queryParameters() {
    return $this->mapQueryParameters;
  }

  public function queryParameterKeys() {
    return array_keys($this->mapQueryParameters);
  }

  public function queryParameterForKey($strKey) {
    if (isset($this->mapQueryParameters[$strKey])) {
      return $this->mapQueryParameters[$strKey];
    }

    return null;
  }
}
