<?php

require_once(__DIR__ . '/lib/RBReflection.php');

spl_autoload_register(function($strClassName) {
  $strLibraryPath = __DIR__ . '/lib';

  if (!file_exists("$strLibraryPath/$strClassName.php")) {
    $mapSymbolTranslation = [
      'RBOutputBufferException' => 'RBRequestHandler',
      'RBKeyValueTemplateTable' => 'RBKeyTupleTemplateTable',
      'RBVariablesMapTemplateList' => 'RBVariablesMapTemplateFragment',
      'RBVariablesMapTemplateTable' => 'RBVariablesMapTemplateFragment',
      'RBDumpedVariableValue' => 'RBVariableValue',
      'RBEscapedVariableValue' => 'RBVariableValue',
      'RBDumpedEscapedVariableValue' => 'RBVariableValue',
      'RBXMLFragment' => 'RBXMLTag',
      'RBXMLFragmentStore' => 'RBXMLTag',
      'RBXMLEscapedFragment' => 'RBXMLTag',
      'RBXMLInjectFreeformXSSFragment' => 'RBXMLTag',
    ];

    if (!isset($mapSymbolTranslation[$strClassName])) {
      throw new RuntimeException("Class $strClassName not found");
    }

    $strSymbolName = $mapSymbolTranslation[$strClassName];
  } else {
    $strSymbolName = $strClassName;
  }

  $strFileName = "$strLibraryPath/$strSymbolName.php";

  if (file_exists($strFileName)) {
    require_once($strFileName);
  }
});

$objApplication = RBApplication::fromSuperglobals();
$objApplication->run();
