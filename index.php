<?php

define("ROOT_PATH",  realpath(dirname(__FILE__)));
define("APPLICATION_PATH", ROOT_PATH . '/application');
define("LIB_PATH", APPLICATION_PATH . '/library');
define("COMMON_PATH", APPLICATION_PATH . '/common');
$app  = new Yaf_Application(ROOT_PATH . "/conf/application.ini");
$app->bootstrap()->run();
