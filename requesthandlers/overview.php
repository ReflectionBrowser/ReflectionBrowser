<?php

final class RBOverviewRequestHandler extends RBRequestHandler {
  public function breadcrumbs() {
    return ['Overview'];
  }

  protected function processRequest(RBRequest $objRequest, RBXMLFragmentStore $objRootFragment) {
    $objRuntime = $this->runtime();

    $objRootFragment->appendContentFragments([
      new RBPageTitle('ReflectionBrowser'),
      new RBKeyValueTemplateTable([
        'System' => php_uname(),
        'SAPI' => php_sapi_name(),
    	  'Version PHP' => phpversion(),
        'Version Zend' => zend_version(),
      ]),
      new RBXMLTag('div', ['class' => 'row'], [
        new RBXMLTag('div', ['class' => 'col-lg-12'], [
          new RBXMLTag('h2', [], [$objRuntime->name()]),
          new RBKeyValueTemplateTable($objRuntime->infoMap()),
        ]),
      ]),
    ]);
  }
}
