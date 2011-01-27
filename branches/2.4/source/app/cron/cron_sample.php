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
 * Time:
 * Description:
 *
 */

/**
 * Include defination
 */
require_once dirname(__FILE__) . '/../etc/defination.php';

/**
 * Bootstrap
 */
require_once 'App/Bootstrap.php';

/**
 * Define task
 *
 * @param Wacow_Application $app
 */
function task(Wacow_Application $app)
{
    // Get database connection
    $db = $app->getDbAdapter();

    // Get SMTP Mailer
    $mailer = $app->getMailer();

    // Get View
    $view = $app->getView();

    echo $view->fetch('cron_sample');
}

/**
 * Start task
 */
App_Bootstrap::run('task');