<?php
  require_once '/home/www.libraries.wvu.edu/phpincludes/engine/engineAPI/4.0/engine.php';
  $engine = EngineAPI::singleton();
  errorHandle::errorReporting(errorHandle::E_ALL);

  // Set localVars and engineVars variables
  $localvars  = localvars::getInstance();
  $enginevars = enginevars::getInstance();

  print "----Engine loaded----";
