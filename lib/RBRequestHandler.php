<?php

abstract class RBRequestHandler {
  private $objApplication;
  private $intStatusCode;

  public function __construct(RBApplication $objApplication) {
    $this->objApplication = $objApplication;
  }

  final public function application() {
    return $this->objApplication;
  }

  final public function runtime() {
    return $this->objApplication->environment()->runtime();
  }

  final public function setStatusCode($intStatusCode) {
    $this->intStatusCode = $intStatusCode;
  }

  final public function handleRequest(RBRequest $objRequest) {
    $objTemplate = new RBTemplate($this->objApplication);
    $this->processRequest($objRequest, $objTemplate);

    if (!$this->objApplication->isInErrorMode() && ob_get_length() > 0) {
      throw new RBOutputBufferException('There is data written directly to standard out');
    }

    if (!$objTemplate->hasPageContent()) {
      throw new LogicException('There is no output page content');
    }

    $objResponse = new RBResponse();
    $objResponse->setBody($objTemplate->render());

    if ($this->intStatusCode) {
      $objResponse->setStatusCode($this->intStatusCode);
    }

    return $objResponse;
  }

  abstract protected function processRequest(RBRequest $objRequest, RBXMLFragmentStore $objRootFragment);
}

final class RBOutputBufferException extends RuntimeException {}
