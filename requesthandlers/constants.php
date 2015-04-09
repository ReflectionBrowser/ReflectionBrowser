<?php

final class RBConstantsRequestHandler extends RBRequestHandler {
  public function breadcrumbs() {
    return ['Constants'];
  }

  protected function processRequest(RBRequest $objRequest, RBXMLFragmentStore $objRootFragment) {
    $objRootFragment->appendContentFragments([
      new RBPageTitle('Constants'),
      new RBConstantsTemplateTable($this->runtime()),
    ]);
  }
}
