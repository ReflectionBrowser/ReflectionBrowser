<?php

final class RBApplication {
  private $objEnvironment;
  private $mapSERVER;
  private $mapGET;

  private $objRequestHandler;
  private $lstAttributions = [];

  private $blnErrorMode = false;
  private $objErrorRequestHandler;

  public function __construct(array $mapENV, array $mapSERVER, array $mapGET) {
    $this->mapGET = $mapGET;

    set_error_handler([$this, 'errorHandler']);
    set_exception_handler([$this, 'exceptionHandler']);
    ob_start();

    $this->objEnvironment = new RBEnvironment($mapENV);
    $this->mapSERVER = $mapSERVER;
  }

  public static function fromSuperglobals() {
    return new static($_ENV, $_SERVER, $_GET);
  }

  public function errorHandler($intLevel, $strMessage, $strFile, $intLine, $mapContext) {
    if ($intLevel === 0) {
      return true;
    }

    throw new RBErrorException($strMessage, $intLevel, $strFile, $intLine, $mapContext);
  }

  public function exceptionHandler(Exception $objException) {
    $this->blnErrorMode = true;
    require_once(__DIR__ . '/../requesthandlers/_error.php');

    $objRequestHandler = new RBErrorHandler($this, $objException);
    $this->handleRequestWithRequestHandler($objRequestHandler);
  }

  public function run() {
    if (strpos($this->mapSERVER['REQUEST_URI'], basename($this->URI())) === false) {
      $this->redirectToURL($this->URI());
    }

    $strPageName = $this->activePage();

    $strFileName = __DIR__ . "/../requesthandlers/$strPageName.php";
    if (file_exists($strFileName)) {
      require_once($strFileName);

      $strClassName = 'RB' . ucfirst($strPageName) . 'RequestHandler';
      $objRequestHandler = new $strClassName($this);
      $this->handleRequestWithRequestHandler($objRequestHandler);
    } else {
      require_once(__DIR__ . '/../requesthandlers/_notfound.php');

      $objRequestHandler = new RBNotFoundHandler($this);
      $this->handleRequestWithRequestHandler($objRequestHandler);
    }
  }

  public function redirectToURL($strURL) {
    ob_end_clean();

    header("Location: $strURL");
    exit;
  }

  private function handleRequestWithRequestHandler(RBRequestHandler $objRequestHandler) {
    if (!$this->blnErrorMode) {
      $this->objRequestHandler = $objRequestHandler;
    } else {
      $this->objErrorRequestHandler = $objRequestHandler;
    }

    $objRequest = new RBRequest([], '', $this->URI(), $this->mapGET);
    $objResponse = $objRequestHandler->handleRequest($objRequest);

    $intStatusCode = $objResponse->statusCode();
    if ($intStatusCode !== 200) {
      header("Status: $intStatusCode");
    }

    foreach ($objResponse->headers() as $strKey => $strValue) {
      header("$strKey: $strValue");
    }

    echo $objResponse->body();

    $this->objRequestHandler = null;
    $this->objErrorRequestHandler = null;
  }

  public function environment() {
    return $this->objEnvironment;
  }

  public function currentRequestHandler() {
    return $this->objRequestHandler;
  }

  public function isInErrorMode() {
    return $this->blnErrorMode;
  }

  public function URI() {
    return $this->mapSERVER['PHP_SELF'];
  }

  public function navigationItems() {
    $objRuntime = $this->objEnvironment->runtime();

    $lstItems = [
      new RBNavigationItem('Overview', 'home'),
      new RBReflectOnNavigationItem('functions', 'Functions', 'record'),
      new RBReflectOnNavigationItem('classes', 'Classes', 'unchecked'),
      new RBReflectOnNavigationItem('interfaces', 'Interfaces', 'check'),
      new RBReflectOnNavigationItem('traits', 'Traits', 'share'),
      new RBReflectOnNavigationItem('constants', 'Constants', 'tag'),
      new RBReflectOnNavigationItem('configuration', 'Configuration', 'cog'),
      new RBReflectOnNavigationItem('extensions', 'Extensions', 'wrench'),
      // new RBReflectOnNavigationItem('namespaces', 'Namespaces', 'briefcase'),
      new RBReflectOnNavigationItem('globals', 'Globals', 'globe'),
      // new RBDividerNavigationItem(),
      // new RBNavigationItem('Load code', 'folder-open'),
    ];

    if ($lstRuntimeItems = $objRuntime->navigationItems()) {
      $lstItems[] = new RBDividerNavigationItem();
      $lstItems = array_merge($lstItems, $lstRuntimeItems);
    }

    return $lstItems;
  }

  public function addAttribution(RBXMLFragment $objFragment) {
    $this->lstAttributions[] = $objFragment;
  }

  public function attributions() {
    return $this->lstAttributions;
  }

  public function breadcrumbs() {
    if ($this->objErrorRequestHandler) {
      return $this->objErrorRequestHandler->breadcrumbs();
    }

    if ($lstBreadcrumbs = $this->objRequestHandler->breadcrumbs()) {
      return $lstBreadcrumbs;
    }

    return [];
  }

  public function activePage() {
    if (!isset($this->mapGET['reflectOn'])) {
      return 'overview';
    }

    return $this->mapGET['reflectOn'];
  }

  public function isNavigationItemActive(RBNavigationItem $objNavItem) {
    switch (get_class($objNavItem)) {
      case 'RBNavigationItem':
        return ($this->activePage() === 'overview');
        break;
      case 'RBReflectOnNavigationItem':
        return ($this->activePage() === $objNavItem->reflectOn());
        break;

      default:
        return false;
        break;
    }
  }
}
