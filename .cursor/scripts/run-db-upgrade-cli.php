<?php

chdir(dirname(__DIR__, 2));

$root = getcwd();
$environment = 'production';

$_SERVER['HTTP_HOST'] = '127.0.0.1:8080';
$_SERVER['SERVER_NAME'] = '127.0.0.1';
$_SERVER['SERVER_PORT'] = '8080';
$_SERVER['REQUEST_URI'] = '/';
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_SERVER['REMOTE_ADDR'] = '127.0.0.1';
$_SERVER['REQUEST_METHOD'] = 'GET';

$system_path = $root . DIRECTORY_SEPARATOR . 'system';
$application_folder = $root . DIRECTORY_SEPARATOR . 'application';

if (realpath($system_path) !== false) {
    $system_path = realpath($system_path) . DIRECTORY_SEPARATOR;
} else {
    $system_path = rtrim($system_path, '/\\') . DIRECTORY_SEPARATOR;
}

define('BASEPATH', str_replace('\\', '/', $system_path));
define('APPPATH', $application_folder . DIRECTORY_SEPARATOR);
define('VIEWPATH', APPPATH . 'views' . DIRECTORY_SEPARATOR);
define('EXT', '.php');
define('ENVIRONMENT', $environment);
define('FCPATH', $root . DIRECTORY_SEPARATOR);

if (file_exists(APPPATH . 'config/' . ENVIRONMENT . '/constants.php')) {
    require APPPATH . 'config/' . ENVIRONMENT . '/constants.php';
} else {
    require APPPATH . 'config/constants.php';
}

require BASEPATH . 'core/Common.php';

if (file_exists(APPPATH . 'vendor/autoload.php')) {
    require_once APPPATH . 'vendor/autoload.php';
}

require_once APPPATH . 'config/hooks.php';
require_once APPPATH . 'hooks/App_Autoloader.php';
(new App_Autoloader())->register();

if (extension_loaded('mbstring')) {
    define('MB_ENABLED', true);
    mb_substitute_character('none');
} else {
    define('MB_ENABLED', false);
}

define('ICONV_ENABLED', extension_loaded('iconv'));

$GLOBALS['CFG'] = &load_class('Config', 'core');
$GLOBALS['UNI'] = &load_class('Utf8', 'core');

if (file_exists(BASEPATH . 'core/Security.php')) {
    $GLOBALS['SEC'] = &load_class('Security', 'core');
}

load_class('Router', 'core');
load_class('Input', 'core');
load_class('Lang', 'core');

require BASEPATH . 'core/Controller.php';

function &get_instance()
{
    return CI_Controller::get_instance();
}

new CI_Controller();

require_once APPPATH . 'hooks/InitHook.php';
_app_init_load();

$ci = &get_instance();
$ci->load->database();
$ci->load->library('app');
$ci->load->helper('settings');

$ci->load->config('migration');

$beforeUpdateVersion = $ci->app->get_current_db_version();
$updateToVersion = (int) $ci->config->item('migration_version');

if ((int) $beforeUpdateVersion === $updateToVersion) {
    echo "Database already at version {$beforeUpdateVersion}.\n";
    exit(0);
}

echo "Upgrading database from {$beforeUpdateVersion} to {$updateToVersion}...\n";

$ci->load->library('migration', [
    'migration_enabled' => true,
    'migration_type' => $ci->config->item('migration_type'),
    'migration_table' => $ci->config->item('migration_table'),
    'migration_auto_latest' => $ci->config->item('migration_auto_latest'),
    'migration_version' => $updateToVersion,
    'migration_path' => $ci->config->item('migration_path'),
]);

hooks()->do_action('before_update_database', $updateToVersion);
define('DOING_DATABASE_UPGRADE', true);

if ($ci->migration->current() === false) {
    fwrite(STDERR, $ci->migration->error_string() . PHP_EOL);
    exit(1);
}

delete_option('upgraded_from_version');
add_option('upgraded_from_version', $beforeUpdateVersion);
hooks()->do_action('database_updated', $updateToVersion);

$version = $ci->app->get_current_db_version();

if ((int) $version !== $updateToVersion) {
    fwrite(STDERR, "Expected database version {$updateToVersion}, got {$version}.\n");
    exit(1);
}

echo "Database upgraded to version {$version}.\n";
