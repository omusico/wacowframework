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
 * Include defination
 */
require_once './app/etc/defination.php';

/**
 * Set include_path
 */
$includePath = array(MODEL_PATH, LIB_PATH, get_include_path());
set_include_path(join(PATH_SEPARATOR, $includePath));

/**
 * Run application!
 */
require_once './app/Application.php';
$app = new Application(APP_PATH);
$app->run();
