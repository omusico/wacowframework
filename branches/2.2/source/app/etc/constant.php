<?php
/**
 * %ProjectName%
 *
 * %License%
 *
 * @category    %ProjectName%
 * @copyright
 * @version     $Id$
 */

/**
 * User custom defination
 *
 * If the prefix of defination is equals name of application (case insensitive),
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
define('WACOW_LEVEL_ADMIN',     1);
define('WACOW_LEVEL_MEMBER',    2);
define('WACOW_LEVEL_EVERYONE',  null);