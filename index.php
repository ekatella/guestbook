<?php

// TODO: Registry pattern for configs
define('DOMAIN_SUFFIX', '');
//I can take it dynamicly - but more secure set domain name in configs
define('DOMAIN_NAME', 'localhost');  //current domain
define('BASE_DOMAIN', DOMAIN_NAME . DOMAIN_SUFFIX);
define('SITE_NAME', basename(__DIR__));
define('SITE_DIR', dirname(__DIR__));
define('SITE_PATH', dirname(__FILE__));
define('SITE_CONFIG_DIR', SITE_PATH . DIRECTORY_SEPARATOR . 'config');
define('SITE_TEMPLATES_DIR', SITE_PATH . DIRECTORY_SEPARATOR . 'template');
define('SITE_COMPILE_DIR', SITE_PATH . DIRECTORY_SEPARATOR . 'tmp');
define('BASE_NAMESPASE', SITE_NAME . '\app');


require_once SITE_PATH . '/engine/Autoloader/Autoloader.php';

// register autoloader
if (!Autoloader::getInstance()->isRegistered()) {
	Autoloader::getInstance()->register();
}

require_once SITE_PATH . '/vendor/autoload.php';

guestbook\engine\Routing\Router::getInstance()->run();






