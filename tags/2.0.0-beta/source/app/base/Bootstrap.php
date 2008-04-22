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
 * @see Wacow_Application
 */
require_once 'Wacow/Application.php';

/**
 * A bootstrap class for application
 *
 * @category    ProjectName
 * @package     Base
 * @copyright
 */
class Bootstrap
{
    /**
     * Setting for config loading
     *
     * @var array
     */
    private static $_setting = array(
        'type'      => WF_CONFIG_TYPE,
        'path'      => WF_CONFIG_PATH,
        'cachePath' => WF_CACHE_PATH,
    );

    /**
     * Runtime paths translate
     *
     * @var array
     */
    private static $_paths = array(
        ':rootPath'     => WF_ROOT_PATH,
        ':appPath'      => WF_APP_PATH,
        ':etcPath'      => WF_ETC_PATH,
        ':tmpPath'      => WF_TMP_PATH,
        ':modulePath'   => WF_MODULE_PATH,
        ':basePath'     => WF_BASE_PATH,
        ':cronPath'     => WF_CRON_PATH,
        ':libPath'      => WF_LIB_PATH,
        ':pubPath'      => WF_PUB_PATH,
        ':cachePath'    => WF_CACHE_PATH,
        ':uploadPath'   => WF_UPLOAD_PATH,
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
    public static function run()
    {
        // setup application
        Wacow_Application::setConfigSettings(self::$_setting);
        Wacow_Application::setRuntimePaths(self::$_paths);

        // run
        Wacow_Application::run();
    }
}