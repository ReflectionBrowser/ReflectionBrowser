<?php

class RBKeyTupleTemplateTable extends RBTemplateFragment {
  public function __construct(array $mapKeyTuple = null, $strEmptyText = '') {
    parent::__construct(new RBXMLTag('table', ['class' => 'table table-bordered']));

    if ($mapKeyTuple !== null) {
      if (count($mapKeyTuple) === 0) {
        if (empty($strEmptyText)) {
          throw new InvalidArgumentException('Empty text cannot be empty');
        }

        $this->appendEmptyRow($strEmptyText);
      } else {
        foreach ($mapKeyTuple as $strKey => $tupValues) {
          $this->appendTupleRow([$strKey], $tupValues);
        }
      }
    }
  }

  public function appendTupleRow(array $tupKeys, array $tupValues) {
    $objRow = $this->appendContentFragment(new RBXMLTag('tr'));

    foreach ($tupKeys as $mixKey) {
      $objRow->appendContentFragment(new RBXMLTag('th', [], [$mixKey]));
    }

    foreach ($tupValues as $mixValue) {
      $objRow->appendContentFragment(new RBXMLTag('td', [], [$mixValue]));
    }
  }

  public function appendEmptyRow($strText) {
    $this->appendContentFragment(new RBXMLTag('tr', [], [
      new RBXMLTag('td', ['style' => 'color: #bbb;'], [
        new RBXMLTag('i', [], ["($strText)"]),
      ]),
    ]));
  }
}

final class RBKeyValueTemplateTable extends RBKeyTupleTemplateTable {
  public function __construct(array $mapKeyValue = null, $strEmptyText = '') {
    if ($mapKeyValue === null) {
      $mapKeyTuple = null;
    } else {
      $mapKeyTuple = array_combine(array_keys($mapKeyValue), array_map(function($frgValue) {
        return [$frgValue];
      }, array_values($mapKeyValue)));
    }

    parent::__construct($mapKeyTuple, $strEmptyText);
  }

  public function appendValueRow($frgKey, $frgValue) {
    parent::appendTupleRow([$frgKey], [$frgValue]);
  }
}
