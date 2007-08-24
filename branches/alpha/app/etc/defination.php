<?php
/**
 * Wacow Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @copyright
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Define all related path of application
 */
define('ROOT_PATH',   str_replace('\\', '/', dirname(dirname(dirname(__FILE__)))));

define('APP_PATH',    ROOT_PATH . '/app');
define('PUB_PATH',    ROOT_PATH . '/pub');

define('ETC_PATH',    APP_PATH . '/etc');
define('LIB_PATH',    APP_PATH . '/lib');
define('LOG_PATH',    APP_PATH . '/log');
define('TMP_PATH',    APP_PATH . '/tmp');

define('MODEL_PATH',  APP_PATH . '/model');
define('MODULE_PATH', APP_PATH . '/module');

define('CACHE_PATH',  TMP_PATH . '/cache');
define('UPLOAD_PATH', TMP_PATH . '/upload');

define('ETC_CACHE_PATH', CACHE_PATH, '/etc');

/**
 * Define the setting of configuration file
 *
 */
define('CONFIG_TYPE', 'ini');
define('CONFIG_FILE', ETC_PATH . './config.ini');

/**
 * Define the setting of default cache
 */

/**
 * User custom defination
 *
 * If the prefix of defination is equals name of application,
 * then the defination will auto listed as an javascript variable.
 *
 * For example:
 *
 * define('WACOW_CUSTOM_DEF', 1);
 *
 * in javascript will be:
 *
 * var WACOW_CUSTOM_DEF = 1;
 */
