<?php
/**
 * %ProjectName%
 *
 * %License%
 *
 * @category    %ProjectName%
 * @package     App
 * @copyright
 * @version     $Id$
 */

/**
 * @see Wacow_Application
 */
require_once 'Wacow/Application.php';

/**
 * @see Wacow_Controller_Plugin_Auth
 */
require_once 'Wacow/Controller/Plugin/Auth.php';

/**
 * A bootstrap class for application
 *
 * @category    %ProjectName%
 * @package     App
 * @copyright
 */
class App_Bootstrap
{
    /**
     * Setting for config loading
     *
     * @var array
     */
    private static $_setting = array(
        'type'      => WF_CONFIG_TYPE,
        'path'      => WF_CONFIG_PATH,
        'cacheStorage' => 'File',
        'cacheOptions' => array(
            'cache_dir' => WF_CONFIG_CACHE_PATH,
        ),
    );

    /**
     * Runtime paths translate
     *
     * @var array
     */
    private static $_paths = array(
        ':rootPath'      => WF_ROOT_PATH,
        ':appPath'       => WF_APP_PATH,
        ':etcPath'       => WF_ETC_PATH,
        ':tmpPath'       => WF_TMP_PATH,
        ':modulePath'    => WF_MODULE_PATH,
        ':cronPath'      => WF_CRON_PATH,
        ':libPath'       => WF_LIB_PATH,
        ':cachePath'     => WF_CACHE_PATH,
        ':uploadPath'    => WF_UPLOAD_PATH,
        ':commonPath'    => WF_COMMON_PATH,
        ':pubPath'       => WF_PUB_PATH,
        ':pubWebPath'    => WF_PUB_WEBPATH,
    );

    /**
     * Private constructor for singleton.
     *
     */
    private function __construct() {}

    /**
     * Application run
     *
     */
    public static function run($taskName = null)
    {
        // setup application
        Wacow_Application::setConfigSettings(self::$_setting);
        Wacow_Application::setRuntimePaths(self::$_paths);

        Wacow_Application::addActionHelper(new App_Controller_Action_Helper_AdminMenuHandler());

        // run
        Wacow_Application::run($taskName);
    }
}