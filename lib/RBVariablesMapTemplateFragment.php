<?php

abstract class RBVariablesMapTemplateFragment extends RBTemplateFragment {
  private $mapVariables;

  public function __construct(array $mapVariables, RBXMLFragment $objFragment) {
    parent::__construct($objFragment);
    $this->mapVariables = $mapVariables;
  }

  public function render() {
    foreach ($this->mapVariables as $mixKey => $mixValue) {
      $this->appendContentFragments($this->fragmentsOfRowForVariable($mixKey, $mixValue));
    }

    return parent::render();
  }

  abstract protected function fragmentsOfRowForVariable($mixKey, $mixValue);
}

final class RBVariablesMapTemplateList extends RBVariablesMapTemplateFragment {
  public function __construct(array $mapVariables) {
    parent::__construct($mapVariables, new RBXMLTag('dl', ['class' => 'dl dl-horizontal']));
  }

  protected function fragmentsOfRowForVariable($mixKey, $mixValue) {
    return [
      new RBXMLTag('dt', ['class' => 'variables'], [(string)$mixKey]),
      new RBXMLTag('dd', [], [new RBDumpedVariableValue($mixValue)]),
    ];
  }
}

final class RBVariablesMapTemplateTable extends RBVariablesMapTemplateFragment {
  public function __construct(array $mapVariables, $blnEmbed = false) {
    $strClasses = 'table table-bordered';

    if ($blnEmbed) {
      $strClasses .= ' table-variables';
    }

    parent::__construct($mapVariables, new RBXMLTag('table', ['class' => $strClasses]));
  }

  protected function fragmentsOfRowForVariable($mixKey, $mixValue) {
    return [
      new RBXMLTag('tr', [], [
        new RBXMLTag('th', [], [(string)$mixKey]),
        new RBXMLTag('td', [], [new RBDumpedVariableValue($mixValue, true)]),
      ])
    ];
  }
}
