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
 * @category   Wacow
 * @package    Wacow_Controller
 * @subpackage Wacow_Controller_Action_Helper
 * @copyright  Copyright (c) 2007-2009 Wabow Information Inc. (http://www.wabow.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @see Zend_Controller_Action_Helper_Abstract
 */
require_once 'Zend/Controller/Action/Helper/Abstract.php';

/**
 * Page Messenger - implement page messages
 *
 * @category   Wacow
 * @package    Wacow_Controller
 * @subpackage Wacow_Controller_Action_Helper
 * @copyright  Copyright (c) 2007-2009 Wabow Information Inc. (http://www.wabow.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Wacow_Controller_Action_Helper_PageMessenger extends Zend_Controller_Action_Helper_Abstract implements IteratorAggregate, Countable
{
    /**
     * $_messages - Messages from previous request
     *
     * @var array
     */
    protected $_messages = array();

    /**
     * addMessage() - Add a message to flash message
     *
     * @param string $message
     * @param string $name OPTIONAL
     * @return Wacow_Controller_Action_Helper_PageMessenger
     */
    public function addMessage($message, $name = null)
    {
        if (null == $name) {
            $this->_messages[] = $message;
        } else {
            $this->_messages[$name] = $message;
        }
        return $this;
    }

    /**
     * hasMessages() - Wether a specific namespace has messages
     *
     * @param string $namespace
     * @return bool
     */
    public function hasMessages()
    {
        return 0 < count($this->_messages);
    }

    /**
     * getMessages() - Get messages from a specific namespace
     *
     * @return array
     */
    public function getMessages()
    {
        if ($this->hasMessages()) {
            return $this->_messages;
        }

        return array();
    }

    /**
     * Clear all messages from the current namespace
     *
     * @return bool True if messages were cleared, false if none existed
     */
    public function clearMessages()
    {
        if ($this->hasMessages()) {
            $this->_messages = array();
            return true;
        }

        return false;
    }

    /**
     * getIterator() - complete the IteratorAggregate interface, for iterating
     *
     * @return ArrayObject
     */
    public function getIterator()
    {
        if ($this->hasMessages()) {
            return new ArrayObject($this->getMessages());
        }

        return new ArrayObject();
    }

    /**
     * count() - Complete the countable interface
     *
     * @return int
     */
    public function count()
    {
        if ($this->hasMessages()) {
            return count($this->getMessages());
        }

        return 0;
    }
}
