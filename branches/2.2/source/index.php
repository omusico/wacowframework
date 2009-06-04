<?php
/**
 * %ProjectName%
 *
 * %License%
 *
 * @category    %ProjectName%
 * @copyright
 * @version     $Id: index.php 598 2008-12-21 08:03:51Z jaceju $
 */

/**
 * Include defination
 */
require_once dirname(__FILE__) . '/app/etc/defination.php';

/**
 * Run application!
 */
require_once 'App/Bootstrap.php';
App_Bootstrap::run();