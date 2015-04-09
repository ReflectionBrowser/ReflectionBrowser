<?php

abstract class RBMasterDetailRequestHandler extends RBRequestHandler {
  private $lstBreadcrumbs = [];

  public function breadcrumbs() {
    return $this->lstBreadcrumbs;
  }

  protected function processRequest(RBRequest $objRequest, RBXMLFragmentStore $objRootFragment) {
    $strReflectOn = $objRequest->queryParameterForKey('reflectOn');

    if ($objRequest->queryParameterForKey('name')) {
      $this->lstBreadcrumbs[] = RBHyperlink::internalReflectOn($strReflectOn, ucfirst($strReflectOn));
      $this->lstBreadcrumbs[] = $objRequest->queryParameterForKey('name');

      $this->processDetailRequest($objRequest, $objRootFragment);
    } else {
      $this->lstBreadcrumbs[] = ucfirst($strReflectOn);
      $this->processMasterRequest($objRequest, $objRootFragment);
    }
  }

  abstract protected function processMasterRequest(RBRequest $objRequest, RBXMLFragmentStore $objRootFragment);
  abstract protected function processDetailRequest(RBRequest $objRequest, RBXMLFragmentStore $objRootFragment);
}
