<?php

class ClearCacheShell extends Shell {

    function main() {
		apc_clear_cache();
    }

}

?>
