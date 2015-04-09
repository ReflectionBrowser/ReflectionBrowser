<?php

final class RBConfigurationRequestHandler extends RBRequestHandler {
  public function breadcrumbs() {
    return ['Configuration'];
  }

  protected function processRequest(RBRequest $objRequest, RBXMLFragmentStore $objRootFragment) {
    $objRootFragment->appendContentFragments([
      new RBPageTitle('Configuration'),
      new RBXMLTag('div', ['class' => 'row'], [
        new RBXMLTag('div', ['class' => 'col-lg-12'], [
          new RBConfigurationTemplateTable($this->runtime()),
        ]),
      ]),
    ]);
  }
}
