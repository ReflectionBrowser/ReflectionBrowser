<?php

final class RBClassesRequestHandler extends RBClassRequestHandler {
  protected function masterName() {
    return 'Classes';
  }

  protected function masterTemplateClass() {
    return 'RBClassesTemplateTable';
  }
}
