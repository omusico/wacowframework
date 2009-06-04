<?php
/**
 * %ProjectName%
 *
 * %License%
 *
 * @category    %ProjectName%
 * @package     App
 * @subpackage  App_Db
 * @copyright
 * @version     $Id$
 */

/**
 * @see Wacow_Db_Table
 */
require_once 'Wacow/Db/Table.php';

/**
 * %Description%
 *
 * @category    %ProjectName%
 * @package     App
 * @subpackage  App_Db
 * @copyright
 */
class App_Db_Table extends Wacow_Db_Table
{
    /**
     * Classname for row
     *
     * @var string
     */
    protected $_rowClass = 'App_Db_Table_Row';
}