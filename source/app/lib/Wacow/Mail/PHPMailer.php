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
 * @see Wacow_Mail
 */
require_once 'Wacow/Mail.php';

/**
 * @see PHPMailer
 */
require_once 'Wacow/vendor/PHPMailer/class.phpmailer.php';

/**
 * Adapter of PHPMailer
 *
 * @category   Wacow
 * @package    Wacow_Mail
 * @copyright  Copyright (c) 2007-2009 Wabow Information Inc. (http://www.wabow.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
final class Wacow_Mail_PHPMailer extends Wacow_Mail
{
    /**
     * Mailer to help send mail.
     *
     * @var PHPMailer
     */
    private $_mailer = null;

    /**
     * Constructor
     *
     * @param string $host
     * @param array $config
     */
    public function __construct($host, array $config)
    {
        $this->_mailer = new PHPMailer();
        $this->_mailer->Host = $host;
        if (isset($config['mailer'])) {
            $this->_mailer->Mailer = $config['mailer'];
        } else {
            $this->_mailer->Mailer = 'smtp';
        }
        if (isset($config['charset'])) {
            $this->_mailer->CharSet = $config['charset'];
        }
        if (isset($config['username'])) {
            $this->_mailer->Username = $config['username'];
        }
        if (isset($config['password'])) {
            $this->_mailer->Password = $config['password'];
        }
        if (isset($config['auth']) && $config['auth'] == 'login') {
            $this->_mailer->SMTPAuth = true;
        } else {
            $this->_mailer->SMTPAuth = false;
        }
        if (isset($config['port'])) {
            $this->_mailer->Port = $config['port'];
        }
        if (isset($config['from'])) {
            list($from, $fromName) = self::parseEmail($config['from']);
            $this->setFrom($from, $fromName);
        }
    }

    /**
     * Sets the SMTP hosts
     *
     * @param string $host
     * @return Wacow_Mail_PHPMailer
     */
    public function setHost($host)
    {
        $this->_mailer->Host = $host;
        return $this;
    }

    /**
     * Sets the default SMTP server port.
     *
     * @param int $port
     * @return Wacow_Mail_PHPMailer
     */
    public function setPort($port)
    {
        $this->_mailer->Port = $port;
        return $this;
    }

    /**
     * charset of content
     *
     * @param string $charset
     * @return Wacow_Mail_PHPMailer
     */
    public function setCharset($charset)
    {
        $this->_mailer->CharSet = $charset;
        return $this;
    }

    /**
     * Name and email of sender
     *
     * @param string $from
     * @param string $fromName
     * @return Wacow_Mail_PHPMailer
     */
    public function setFrom($from, $fromName)
    {
        $this->_mailer->From = $from;
        if ($fromName) {
            $this->_mailer->FromName = $fromName;
        }
        return $this;
    }

    /**
     * Html content of mail
     *
     * @param string $html
     * @return Wacow_Mail_PHPMailer
     */
    public function setBodyHtml($html)
    {
        $this->_mailer->IsHTML(true);
        $this->_mailer->Body = $html;
        return $this;
    }

    /**
     * Text content of mail
     *
     * @param string $txt
     * @return Wacow_Mail_PHPMailer
     */
    public function setBodyText($txt)
    {
        $this->_mailer->IsHTML(false);
        $this->_mailer->Body = $html;
        return $this;
    }

    /**
     * Subject of mail
     *
     * @param string $subject
     * @return Wacow_Mail_PHPMailer
     */
    public function setSubject($subject)
    {
        $this->_mailer->Subject = $subject;
        return $this;
    }

    /**
     * Adds a "To" address.
     *
     * @param string $email
     * @param string $name
     * @return Wacow_Mail_PHPMailer
     */
    public function addTo($email, $name = '')
    {
        $this->_mailer->AddAddress($email, $name);
        return $this;
    }

    /**
     * Adds a "Cc" address.
     *
     * @param string $email
     * @param string $name
     * @return Wacow_Mail_PHPMailer
     */
    public function addCc($email, $name = '')
    {
        $this->_mailer->AddCC($email, $name);
        return $this;
    }

    /**
     * Adds a "Bcc" address.
     *
     * @param string $email
     * @param string $name
     * @return Wacow_Mail_PHPMailer
     */
    public function addBcc($email, $name = '')
    {
        $this->_mailer->AddBCC($email, $name);
        return $this;
    }

    /**
     * Adds a "Reply-to" address.
     *
     * @param string $email
     * @param string $name
     * @return Wacow_Mail_PHPMailer
     */
    public function addReplyTo($email, $name = '')
    {
        $this->_mailer->AddReplyTo($email, $name);
        return $this;
    }

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
    public function addAttachment($path, $name = "", $encoding = "base64", $type = "application/octet-stream")
    {
        return $this->_mailer->AddAttachment($path, $name, $encoding, $type);
    }

    /**
     * Creates message and assigns Mailer. If the message is
     * not sent successfully then it returns false.  Use the ErrorInfo
     * variable to view description of the error.
     *
     * @return bool
     */
    public function send()
    {
        return $this->_mailer->send();
    }

    /**
     * Clears all recipients assigned in the TO array.
     *
     * @return Wacow_Mail_PHPMailer
     */
    public function clearAddresses()
    {
        $this->_mailer->ClearAddresses();
        return $this;
    }

    /**
     * Clears all recipients assigned in the CC array.
     *
     * @return Wacow_Mail_PHPMailer
     */
    public function clearCCs()
    {
        $this->_mailer->ClearCCs();
        return $this;
    }

    /**
     * Clears all recipients assigned in the BCC array.
     *
     * @return Wacow_Mail_PHPMailer
     */
    public function clearBCCs()
    {
        $this->_mailer->clearBCCs();
        return $this;
    }

    /**
     * Get the most recent mailer error message.
     *
     * @return string
     */
    public function getErrorInfo()
    {
        return $this->_mailer->ErrorInfo;
    }
}