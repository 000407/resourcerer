<?php

define('WEBROOT', str_replace("webroot/index.php", "", $_SERVER["SCRIPT_NAME"]));
define('ROOT', str_replace("webroot/index.php", "", $_SERVER["SCRIPT_FILENAME"]));
define('RESOURCES', WEBROOT . "webroot/assets/");
define('APPROOT', "/Resourcerer");