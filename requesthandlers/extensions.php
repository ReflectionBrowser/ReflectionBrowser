<?php

final class RBExtensionsRequestHandler extends RBMasterDetailRequestHandler {
  protected function processMasterRequest(RBRequest $objRequest, RBXMLFragmentStore $objRootFragment) {
    $objRootFragment->appendContentFragments([
      new RBPageTitle('Extensions'),
      new RBExtensionsTemplateTable($this->runtime()),
    ]);
  }

  protected function processDetailRequest(RBRequest $objRequest, RBXMLFragmentStore $objRootFragment) {
    $strName = $objRequest->queryParameterForKey('name');
    $objRuntime = $this->runtime();
    $objExtension = new ReflectionExtension($strName);

    $mapInfo = array_merge(
      ['Version' => $objExtension->getVersion()],
      $objRuntime->extraInfoMapForExtension($objExtension)
    );

    $objRootFragment->appendContentFragments([
      new RBPageTitle($strName),
      new RBXMLTag('div', ['class' => 'row'], [
        new RBXMLTag('div', ['class' => 'col-lg-12'], [
          new RBKeyValueTemplateTable($mapInfo),
        ]),
      ]),
      new RBXMLTag('div', ['class' => 'row'], [
        new RBXMLTag('div', ['class' => 'col-lg-12'], [
          new RBXMLTag('h2', [], ['Dependencies']),
          new RBExtensionDependenciesTemplateTable($objRuntime, $strName),
        ]),
      ]),
      new RBXMLTag('div', ['class' => 'row'], [
        new RBXMLTag('div', ['class' => 'col-lg-12'], [
          new RBXMLTag('h2', [], ['Functions']),
          new RBFunctionsTemplateTable($objRuntime, $strName),
        ]),
      ]),
      new RBXMLTag('div', ['class' => 'row'], [
        new RBXMLTag('div', ['class' => 'col-lg-12'], [
          new RBXMLTag('h2', [], ['Classes']),
          new RBClassesTemplateTable($objRuntime, $strName),
        ]),
      ]),
      new RBXMLTag('div', ['class' => 'row'], [
        new RBXMLTag('div', ['class' => 'col-lg-12'], [
          new RBXMLTag('h2', [], ['Interfaces']),
          new RBInterfacesTemplateTable($objRuntime, $strName),
        ]),
      ]),
      new RBXMLTag('div', ['class' => 'row'], [
        new RBXMLTag('div', ['class' => 'col-lg-12'], [
          new RBXMLTag('h2', [], ['Traits']),
          new RBTraitsTemplateTable($objRuntime, $strName),
        ]),
      ]),
      new RBXMLTag('div', ['class' => 'row'], [
        new RBXMLTag('div', ['class' => 'col-lg-12'], [
          new RBXMLTag('h2', [], ['Constants']),
          new RBConstantsTemplateTable($objRuntime, $strName),
        ]),
      ]),
      new RBXMLTag('div', ['class' => 'row'], [
        new RBXMLTag('div', ['class' => 'col-lg-12'], [
          new RBXMLTag('h2', [], ['Configuration']),
          new RBConfigurationTemplateTable($objRuntime, $strName),
        ]),
      ]),
    ]);
  }
}
