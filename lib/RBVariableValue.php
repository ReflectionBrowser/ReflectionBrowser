<?php

class RBVariableValue extends RBTemplateFragment {
  public function __construct($mixValue, $blnEmbed = false) {
    parent::__construct($this->fragmentForValue($mixValue, $blnEmbed));
  }

  private function fragmentForValue($mixValue, $blnEmbed) {
    if ($mixValue instanceof RBTemplate) {
      $mixValue = $mixValue->__debugInfo();
    }

    if ($mixValue instanceof RBXMLFragment) {
      return $this->fragmentForString(html_entity_decode($mixValue->render()), $blnEmbed);
    }

    if ($mixValue instanceof SimpleXMLElement) {
      return $this->fragmentForString($mixValue->asXML(), $blnEmbed);
    }

    if (is_array($mixValue) || $mixValue instanceof ArrayAccess || $mixValue instanceof Traversable) {
      if (count($mixValue) === 0) {
        return new RBKeyValueTemplateTable([], 'empty array');
      } else {
        return new RBVariablesMapTemplateTable($mixValue, $blnEmbed);
      }
    }

    return $this->fragmentForString($this->stringForValue($mixValue), $blnEmbed);
  }

  protected function stringForValue($mixValue) {
    return (string)$mixValue;
  }

  private function fragmentForString($strValue, $blnEmbed) {
    $strClasses = 'variables';

    if ($blnEmbed) {
      $strClasses .= ' variables-table';
    } else {
      $strClasses .= ' variables-list';
    }

    return new RBXMLTag('pre', ['class' => $strClasses], [$strValue]);
  }
}

trait RBDumpedVariableTransformer {
  protected function stringForValue($mixValue) {
    ob_start();
    var_dump($mixValue);
    return trim(ob_get_clean());
  }
}

trait RBEscapedVariableTransformer {
  protected function stringForValue($mixValue) {
    return str_replace(
      ["\0", "\t", "\n", "\v", "\r"],
      ['\0', '\t', '\n', '\v', '\r'],
      (string)$mixValue
    );
  }
}

final class RBDumpedVariableValue extends RBVariableValue {
  use RBDumpedVariableTransformer;
}

final class RBEscapedVariableValue extends RBVariableValue {
  use RBEscapedVariableTransformer;
}

final class RBDumpedEscapedVariableValue extends RBVariableValue {
  use RBDumpedVariableTransformer, RBEscapedVariableTransformer {
    RBDumpedVariableTransformer::stringForValue as dumpedStringForValue;
    RBEscapedVariableTransformer::stringForValue as escapedStringForValue;
  }

  protected function stringForValue($mixValue) {
    return $this->escapedStringForValue($this->dumpedStringForValue($mixValue));
  }
}
