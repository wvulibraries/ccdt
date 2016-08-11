<?php
//form builder css defaults
  $patterns = templates::getTemplatePatterns();
  if (isset($patterns['formBuilder'])) {
    print '{form display="assets"}';
  }

?>
