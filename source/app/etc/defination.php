<?php
/**
 * %ProjectName%
 *
 * %License%
 *
 * @category    %ProjectName%
 * @copyright
 * @version     $Id: defination.php 612 2009-01-06 09:15:26Z jaceju $
 */

/**
 * Define the public web resource path
 *
 */
define('WF_PUB_WEBPATH', '/pub');

/**
 * Define all related path of application
 */
define('WF_ROOT_PATH',   str_replace('\\', '/', dirname(dirname(dirname(__FILE__)))));
define('WF_APP_PATH',    WF_ROOT_PATH . '/app');
define('WF_ETC_PATH',    WF_APP_PATH  . '/etc');
define('WF_LIB_PATH',    WF_APP_PATH  . '/lib');
define('WF_TMP_PATH',    WF_APP_PATH  . '/tmp');
define('WF_CRON_PATH',   WF_APP_PATH  . '/cron');
define('WF_MODULE_PATH', WF_APP_PATH  . '/mod');
define('WF_CACHE_PATH',  WF_TMP_PATH  . '/cache');
define('WF_UPLOAD_PATH', WF_TMP_PATH  . '/upload');
define('WF_COMMON_PATH', WF_MODULE_PATH . '/common');
define('WF_PUB_PATH',    WF_ROOT_PATH . WF_PUB_WEBPATH);

/**
 * Define the setting of configuration file
 *
 */
define('WF_CONFIG_TYPE',       'ini');
define('WF_CONFIG_PATH',       WF_ETC_PATH . '/config.ini');
define('WF_CONFIG_CACHE_PATH', WF_CACHE_PATH . '/config');

/**
 * Set include_path
 */
$includePath = array(WF_LIB_PATH, '.');
set_include_path(join(PATH_SEPARATOR, $includePath));

/**
 * Set view options
 *
 * Do not change the options if you don't know what it is.
 */
$viewOptions = array(
    'engine'            => 'smarty',
    'encoding'          => 'UTF-8',
    'commonTemplateDir' => ':commonPath/views/scripts',
    'compileDir'        => ':tmpPath/compile/:moduleName',
    'cacheDir'          => ':tmpPath/html/:moduleName',
    'configDir'         => ':etcPath',
    'viewSuffix'        => 'tpl.htm',
    'leftDelimiter'     => '<%',
    'rightDelimiter'    => '%>',
    'filters'           => array(
        'html'          => 'output',
));
require_once 'Wacow/View.php';
Wacow_View::setStaticOptions($viewOptions);

/**
 * User custom constants
 */
require dirname(__FILE__) . '/constant.php';