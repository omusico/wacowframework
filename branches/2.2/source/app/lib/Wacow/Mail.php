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
 * @package    Wacow_Mail
 * @copyright  Copyright (c) 2007-2009 Wabow Information Inc. (http://www.wabow.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Wacow_Mail
 *
 * @category   Wacow
 * @package    Wacow_Mail
 * @copyright  Copyright (c) 2007-2009 Wabow Information Inc. (http://www.wabow.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Wacow_Mail
{
    /**
     * Parse Email
     *
     * @param string $email
     * @return array (email, name)
     */
    public static function parseEmail($email)
    {
        if (preg_match('/^([^<]*)<([^\>]+)>?$/i', $email, $matches)) {
            return array($matches[2], $matches[1]);
        }
        return array($email, null);
    }

    /**
     * Sets the SMTP hosts.
     *
     * @param string $host
     */
    abstract public function setHost($host);

    /**
     * Sets the default SMTP server port.
     *
     * @param int $port
     */
    abstract public function setPort($port);

    /**
     * charset of content
     *
     * @param string $charset
     */
    abstract public function setCharset($charset);

    /**
     * Name and email of sender
     *
     * @param string $from
     * @param string $fromName
     * @return Wacow_Mail
     */
    abstract public function setFrom($from, $fromName);

    /**
     * Html content of mail
     *
     * @param string $html
     * @return Wacow_Mail
     */
    abstract public function setBodyHtml($html);

    /**
     * Text content of mail
     *
     * @param string $txt
     * @return Wacow_Mail
     */
    abstract public function setBodyText($txt);

    /**
     * Subject of mail
     *
     * @param string $subject
     * @return Wacow_Mail
     */
    abstract public function setSubject($subject);

    /**
     * Adds a "To" address.
     *
     * @param string $email
     * @param string $name
     * @return Wacow_Mail
     */
    abstract public function addTo($email, $name = '');

    /**
     * Adds a "Cc" address.
     *
     * @param string $email
     * @param string $name
     * @return Wacow_Mail
     */
    abstract public function addCc($email, $name = '');

    /**
     * Adds a "Bcc" address.
     *
     * @param string $email
     * @param string $name
     * @return Wacow_Mail
     */
    abstract public function addBcc($email, $name = '');

    /**
     * Adds a "Reply-to" address.
     *
     * @param string $email
     * @param string $name
     * @return Wacow_Mail
     */
    abstract public function addReplyTo($email, $name = '');

    /**
     * Adds an attachment from a path on the filesystem.
     * Returns false if the file could not be found
     * or accessed.
     *
     * @param string $path Path to the attachment.
     * @param string $name Overrides the attachment name.
     * @param string $encoding File encoding (see $Encoding).
     * @param string $type File extension (MIME) type.
     * @return bool
     */
    abstract public function addAttachment($path, $name = "", $encoding = "base64", $type = "application/octet-stream");

    /**
     * Creates message and assigns Mailer. If the message is
     * not sent successfully then it returns false.  Use the ErrorInfo
     * variable to view description of the error.
     *
     * @return bool
     */
    abstract public function send();

    /**
     * Clears all recipients assigned in the TO array.
     *
     * @return Wacow_Mail
     */
    abstract public function clearAddresses();

    /**
     * Clears all recipients assigned in the CC array.
     *
     * @return Wacow_Mail
     */
    abstract public function clearCCs();

    /**
     * Clears all recipients assigned in the BCC array.
     *
     * @return Wacow_Mail
     */
    abstract public function clearBCCs();

    /**
     * Get the most recent mailer error message.
     *
     * @return string
     */
    abstract public function getErrorInfo();
}