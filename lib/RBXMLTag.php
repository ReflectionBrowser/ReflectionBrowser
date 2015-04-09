<?php

interface RBXMLFragment {
  public function render();
}

function RBXMLStringEscape($strSubject) {
  return htmlspecialchars($strSubject);
}

function RBXMLFragmentRender($mixFragment) {
  if ($mixFragment instanceof RBXMLFragment) {
    return $mixFragment->render();
  } else {
    return RBXMLStringEscape($mixFragment);
  }
}

abstract class RBXMLFragmentStore implements RBXMLFragment {
  protected $lstFragments = [];

  public function __construct(array $lstFragments) {
    $this->appendContentFragments($lstFragments);
  }

  public function appendContentString($strContent) {
    $this->lstFragments[] = (string)$strContent;
    return $strContent;
  }

  public function appendContentFragment(RBXMLFragment $objFragment) {
    $this->lstFragments[] = $objFragment;
    return $objFragment;
  }

  public function appendContentFragments(array $lstFragments) {
    $this->lstFragments = array_merge($this->lstFragments, array_map(function($mixFragment) {
      if ($mixFragment instanceof RBXMLFragment) {
        return $mixFragment;
      }

      return (string)$mixFragment;
    }, $lstFragments));
  }

  protected function renderFragments() {
    $strBuffer = '';

    foreach ($this->lstFragments as $mixFragment) {
      $strBuffer .= RBXMLFragmentRender($mixFragment);
    }

    return $strBuffer;
  }

  public function __debugInfo() {
    return $this->lstFragments;
  }
}

final class RBXMLTag extends RBXMLFragmentStore {
  private $strName;
  private $mapAttributes;
  private $blnEmptyTag;

  public function __construct($strName, array $mapAttributes = [], array $lstFragments = [], $blnEmptyTag = false) {
    parent::__construct($lstFragments);

    $this->strName = $strName;
    $this->setAttributes($mapAttributes);
    $this->blnEmptyTag = $blnEmptyTag;
  }

  public static function emptyTag($strName, array $mapAttributes = []) {
    return new static($strName, $mapAttributes, [], true);
  }

  public function setAttributeForKey($strValue, $strKey) {
    $this->mapAttributes[$strKey] = (string)$strValue;
  }

  public function setAttributes(array $mapAttributes) {
    $this->mapAttributes = array_map('strval', $mapAttributes);
  }

  public function render() {
    $strBuffer = "<{$this->strName}";

    foreach ($this->mapAttributes as $strKey => $strValue) {
      $strBuffer .= " $strKey=\"";
      $strBuffer .= RBXMLStringEscape($strValue);
      $strBuffer .= '"';
    }

    if ($this->blnEmptyTag) {
      $strBuffer .= '/>';
    } else {
      $strBuffer .= '>';
      $strBuffer .= $this->renderFragments();
      $strBuffer .= "</{$this->strName}>";
    }

    return $strBuffer;
  }

  public function __debugInfo() {
    $mapDebugInfo = [
      'name' => $this->strName,
      'attributes' => array_map('RBXMLStringEscape', $this->mapAttributes),
    ];

    if ($this->blnEmptyTag) {
      $mapDebugInfo['isEmpty'] = true;
    } else {
      $mapDebugInfo['fragments'] = parent::__debugInfo();
    }

    return $mapDebugInfo;
  }
}

final class RBXMLEscapedFragment extends RBXMLFragmentStore {
  public function render() {
    return $this->renderFragments();
  }
}

final class RBXMLInjectFreeformXSSFragment implements RBXMLFragment {
  private $mixContent;

  public function __construct($mixContent) {
    $this->mixContent = $mixContent;
  }

  public function render() {
    return (string)$this->mixContent;
  }
}
