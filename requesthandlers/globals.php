<?php

final class RBGlobalsRequestHandler extends RBRequestHandler {
  public function breadcrumbs() {
    return ['Globals'];
  }

  protected function processRequest(RBRequest $objRequest, RBXMLFragmentStore $objRootFragment) {
    $objRootFragment->appendContentFragment(new RBPageTitle('Globals'));
    $objRootFragment->appendContentFragment($this->fragmentForGlobalVariable($_SERVER, '_SERVER'));
    $objRootFragment->appendContentFragment($this->fragmentForGlobalVariable($_ENV, '_ENV'));
  }

  private function fragmentForGlobalVariable(array $mapGlobal, $strName) {
    $objRow = new RBXMLTag('div', ['class' => 'row']);
    $objColumn = $objRow->appendContentFragment(new RBXMLTag('div', ['class' => 'col-lg-12']));
    $objColumn->appendContentFragment(new RBXMLTag('h2', ['style' => 'margin-top: 0;'], ["\$$strName"]));

    ksort($mapGlobal, SORT_NATURAL | SORT_FLAG_CASE);
    $objColumn->appendContentFragment(new RBVariablesMapTemplateTable($mapGlobal));

    return $objRow;
  }
}
