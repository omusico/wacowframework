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
 * @see Wacow_Db_Table_Row
 */
require_once 'Wacow/Db/Table/Row.php';

/**
 * %Description%
 *
 * @category    %ProjectName%
 * @package     App
 * @subpackage  App_Db
 * @copyright
 */
class App_Db_Table_Row extends Wacow_Db_Table_Row
{
    /**
     * Primary key
     *
     * @var string
     */
    protected $_primary = 'id';

    /**
     * Set create datetime
     *
     * @return void
     */
    protected function _insert()
    {
        if (array_key_exists('createDateTime', $this->_data)) {
            $this->createDateTime = date('Y-m-d H:i:s');
        }

        if (array_key_exists('serverIP', $this->_data)) {
            $this->serverIP = $_SERVER['SERVER_ADDR'];
        }

        if (array_key_exists('clientIP', $this->_data)) {
            $this->clientIP = App_Util::getClientIP();
        }
    }

    /**
     * Set update datetime
     *
     */
    protected function _update()
    {
        if (array_key_exists('updateDateTime', $this->_data)) {
            $this->updateDateTime = date('Y-m-d H:i:s');
        }
    }
}