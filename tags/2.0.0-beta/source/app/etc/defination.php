<?php
/**
 * %Project Name%
 *
 * %License or Copyright Description%
 *
 * @category    ProjectName
 * @package     Base
 * @copyright
 * @version     $Id$
 */

/**
 * Define all related path of application
 */
define('WF_ROOT_PATH',   str_replace('\\', '/', dirname(dirname(dirname(__FILE__)))));

define('WF_APP_PATH',    WF_ROOT_PATH . '/app');
define('WF_ETC_PATH',    WF_APP_PATH  . '/etc');
define('WF_LIB_PATH',    WF_APP_PATH  . '/lib');
define('WF_TMP_PATH',    WF_APP_PATH  . '/tmp');
define('WF_BASE_PATH',   WF_APP_PATH  . '/base');
define('WF_CRON_PATH',   WF_APP_PATH  . '/cron');
define('WF_MODULE_PATH', WF_APP_PATH  . '/mod');
define('WF_CACHE_PATH',  WF_TMP_PATH  . '/cache');
define('WF_UPLOAD_PATH', WF_TMP_PATH  . '/upload');

define('WF_PUB_PATH', '/pub');

/**
 * Define the setting of configuration file
 *
 */
define('WF_CONFIG_TYPE', 'ini');
define('WF_CONFIG_PATH', WF_ETC_PATH . '/config.ini');

/**
 * Set include_path
 */
$includePath = array(WF_LIB_PATH, WF_BASE_PATH, get_include_path());
set_include_path(join(PATH_SEPARATOR, $includePath));

/**
 * Set view options
 */
$viewOptions = array(
    'engine'            => 'smarty',
    'encoding'          => 'UTF-8',
    'compileDir'        => ':moduleDir/caches/template',
    'cacheDir'          => ':moduleDir/caches/html',
    'configDir'         => ':etcPath',
    'viewSuffix'        => 'tpl.htm',
    'leftDelimiter'     => '<%',
    'rightDelimiter'    => '%>',
    'filters'           => array(
        'tag'           => 'pre',
        'html'          => 'output'
));
require_once 'Wacow/View.php';
Wacow_View::setStaticOptions($viewOptions);

/**
 * User custom defination
 *
 * If the prefix of defination is equals name of application,
 * then the defination will auto listed as an javascript variable.
 * The prefix name set by 'app.name' which defined in config.ini
 *
 * For example:
 *
 * define('WACOW_CUSTOM_DEF', 1);
 *
 * in javascript will be:
 *
 * $frontendVars.WACOW_CUSTOM_DEF = 1;
 */