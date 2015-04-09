<?php

require_once(__DIR__ . '/../lib/RBTemplateFragment.php');

final class RBErrorHandler extends RBRequestHandler {
  private $objException;

  public function __construct(RBApplication $objApplication, Exception $objException) {
    parent::__construct($objApplication);
    $this->objException = $objException;
  }

  public function __debugInfo() {
    return [];
  }

  public function breadcrumbs() {
    if ($objHandler = $this->application()->currentRequestHandler()) {
      return $objHandler->breadcrumbs();
    }

    return ['Error'];
  }

  protected function processRequest(RBRequest $objRequest, RBXMLFragmentStore $objRootFragment) {
    $this->setStatusCode(500);

    if ($this->objException instanceof RBOutputBufferException) {
      $objRootFragment->appendContentFragment(new RBXMLTag('div', ['class' => 'row'], [
        new RBXMLTag('div', ['class' => 'col-lg-12'], [
          new RBXMLTag('h1', ['class' => 'page-header'], [
            'Leaked output ',
            new RBXMLTag('small', [], ['(there shouldn\'t be)']),
          ]),
          new RBXMLTag('p', [], ['The following data is for whatever reason leaked into the output which normally would be send to the user agent. This should not happen and is considered a programming error.']),
          new RBXMLTag('pre', [], [ob_get_clean()]),
        ]),
      ]));
    } else {
      $objException = $this->objException;
      $lstExceptions = [$objException];

      while ($objException = $objException->getPrevious()) {
        $lstExceptions[] = $objException;
      }

      foreach ($lstExceptions as $objException) {
        $objRootFragment->appendContentFragment($this->fragmentForException($objException));
      }

      if (ob_get_length() > 0) {
        $objRootFragment->appendContentFragment(new RBXMLTag('div', ['class' => 'row'], [
          new RBXMLTag('div', ['class' => 'col-lg-12'], [
            new RBXMLTag('h1', ['class' => 'page-header'], [
              'Standard out buffer ',
              new RBXMLTag('small', [], ['(should not exist)']),
            ]),
            new RBXMLTag('pre', [], [ob_get_clean()]),
          ]),
        ]));
      }
    }
  }

  private function fragmentForException(Exception $objException) {
    $objRoot = new RBXMLTag('div', ['class' => 'col-lg-12'], [
      new RBXMLTag('h1', ['class' => 'page-header'], [$objException->getMessage()]),
    ]);

    $objTable = $objRoot->appendContentFragment(new RBKeyValueTemplateTable());
    $objTable->appendValueRow('File', sprintf('%s(%d)', $objException->getFile(), $objException->getLine()));

    if ($objException instanceof ErrorException) {
      $objTable->appendValueRow('Severity', $this->descriptionForSeverity($objException));
    } else {
      $objTable->appendValueRow('Class', get_class($objException));
    }

    if ($intCode = $objException->getCode()) {
      $objTable->appendValueRow('Code', $intCode);
    }

    $objRoot->appendContentFragments([
      new RBXMLTag('h2', [], ['Stack trace']),
      new RBXMLTag('pre', ['class' => 'variables variables-list'], [$objException->getTraceAsString()]),
    ]);

    if ($objException instanceof RBErrorException && $mapContext = $objException->getContext()) {
      $objRoot->appendContentFragments([
        new RBXMLTag('h2', [], ['Context']),
        new RBVariablesMapTemplateList(array_combine(array_map(function($strKey) {
          return "\$$strKey";
        }, array_keys($mapContext)), array_values($mapContext))),
      ]);
    }

    return new RBXMLTag('div', ['class' => 'row'], [$objRoot]);
  }

  private function descriptionForSeverity(ErrorException $objException) {
    switch ($objException->getSeverity()) {
      case E_CORE_ERROR:
      case E_COMPILE_ERROR:
      case E_USER_ERROR:
        return 'Fatal error';
        break;
      case E_RECOVERABLE_ERROR:
        return 'Catchable fatal error';
        break;
      case E_WARNING:
      case E_CORE_WARNING:
      case E_COMPILE_WARNING:
      case E_USER_WARNING:
        return 'Warning';
        break;
      case E_PARSE:
        return 'Parse error';
        break;
      case E_NOTICE:
      case E_USER_NOTICE:
        return 'Notice';
        break;
      case E_STRICT:
        return 'Strict Standards';
        break;
      case E_DEPRECATED:
      case E_USER_DEPRECATED:
        return 'Deprecated';
        break;

      default:
        return 'Unknown error';
        break;
    }
  }
}
