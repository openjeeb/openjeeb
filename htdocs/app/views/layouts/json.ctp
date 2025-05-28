<?php
    //Set no caching
    header("Expires: Thu, 07 Apr 1983 03:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    Configure::write('debug', 0);
    echo $this->Javascript->object($response);
?>