#!/usr/bin/php
<?php

(php_sapi_name() !== 'cli') && die();

if ($argc > 1){
	define('BUILD_THEME_NAME', $argv[1]);
}

defined('BUILD_THEME_PATH') || define('BUILD_THEME_PATH', realpath('./web/themes'));
defined('BUILD_THEME_NAME') || define('BUILD_THEME_NAME', 'dashboardtheme');

if (!is_dir(BUILD_THEME_PATH.DIRECTORY_SEPARATOR.BUILD_THEME_NAME)) {
	die('No theme dir found');
}

define('BUILD_TS', date(DATE_COOKIE));
define('SYSTEM_PATH', realpath(__DIR__ . DIRECTORY_SEPARATOR . 'system'));

defined('BUILD_CLEAN_INSTALL') || define('BUILD_CLEAN_INSTALL', SYSTEM_PATH . DIRECTORY_SEPARATOR . 'install_clean.sql');
defined('BUILD_OUTPUT') || define('BUILD_OUTPUT', SYSTEM_PATH . DIRECTORY_SEPARATOR . 'install.sql');

$path = BUILD_THEME_PATH . DIRECTORY_SEPARATOR . BUILD_THEME_NAME . DIRECTORY_SEPARATOR;
$files = glob($path . '*.html', GLOB_BRACE);

$output = file_get_contents(BUILD_CLEAN_INSTALL) . PHP_EOL;
$output .= PHP_EOL.'-- build on '.BUILD_TS.' theme "'.BUILD_THEME_NAME.'"'.PHP_EOL;
$output .= "INSERT INTO `plugin_dashboard_theme` (`name`, `value`) VALUES ('themeName', '" . BUILD_THEME_NAME . "');" . PHP_EOL;

foreach ($files as &$file) {
	$content = file_get_contents($file);
	if ($content) {
		$content = addslashes($content);
		$content = strtr($content, array(
			'{website:url}' => '{$website:url}',
			'{theme:name}'  => BUILD_THEME_NAME
		));
	}
	$file = str_replace($path, '', $file);
	$output .= PHP_EOL."INSERT INTO `plugin_dashboard_theme` (`name`, `value`) VALUES ('" . $file . "', '" . $content . "');".PHP_EOL;
}

$output .= PHP_EOL."INSERT INTO `plugin_dashboard_theme` (`name`, `value`) VALUES ('themeHtml', '" . serialize($files) . "');";

if (false === file_put_contents(BUILD_OUTPUT, $output)){
	die('Can\'t write '.BUILD_OUTPUT.' file');
}

exit('done!');