<?php
/**********************************************************\
|                                                          |
| The implementation of PHPRPC Protocol 3.0                |
|                                                          |
| phprpc_client.php                                        |
|                                                          |
| Release 3.0.0                                            |
| Copyright (c) 2005-2008 by Team-PHPRPC                   |
|                                                          |
| WebSite:  http://www.phprpc.org/                         |
|           http://www.phprpc.net/                         |
|           http://www.phprpc.com/                         |
|           http://sourceforge.net/projects/php-rpc/       |
|                                                          |
| Authors:  Ma Bingyao <andot@ujn.edu.cn>                  |
|                                                          |
| This file may be distributed and/or modified under the   |
| terms of the GNU Lesser General Public License (LGPL)    |
| version 3.0 as published by the Free Software Foundation |
| and appearing in the included file LICENSE.              |
|                                                          |
\**********************************************************/

/* PHPRPC Client for PHP.
 *
 * Copyright (C) 2005-2008 Ma Bingyao <andot@ujn.edu.cn>
 * Version: 3.0.0
 * LastModified: Dec 1, 2007
 * This library is free.  You can redistribute it and/or modify it.
 *
/*
 * Interfaces
 *
 * $rpc_client = new PHPRPC_Client();
 * $rpc_client->setProxy(NULL);
 * $rpc_client->useService('http://www.phprpc.org/server.php');
 * $rpc_client->setKeyLength(1024);
 * $rpc_client->setEncryptMode(3);
 * $args = array(1, 2);
 * echo $rpc_client->invoke('add', &$args);
 * echo "<br />";
 * $n = 3;
 * $args = array(&$n);
 * echo $rpc_client->invoke('inc', &$args, true);
 * echo "<br />";
 * echo $rpc_client->sub(3, 2);
 * echo "<br />";
 * // error handle
 * $result = $rpc_client->mul(1, 2);  // no mul function
 * if (is_a($result, "PHPRPC_Error")) {
 *     echo $result->toString();
 * }
 */

class PHPRPC_Error {
    var $Number;
    var $Message;
    function PHPRPC_Error($errno, $errstr) {
        $this->Number = $errno;
        $this->Message = $errstr;
    }
    function toString() {
        return $this->Number . ":" . $this->Message;
    }
    function __toString() {
        return $this->toString();
    }
    function getNumber() {
        return $this->Number;
    }
    function getMessage() {
        return $this->Message;
    }
}

class _PHPRPC_Client {
    var $_server;
    var $_timeout;
    var $_output;
    var $_warning;
    var $_proxy;
    var $_key;
    var $_keylen;
    var $_encryptMode;
    var $_cookie;
    var $_charset;
    var $_socket;
    var $_http_version;
    var $_keep_alive;
    // Public Methods
    function _PHPRPC_Client($serverURL = '') {
        require_once('compat.php');
        register_shutdown_function(array(&$this, "_disconnect"));
        $this->_proxy = NULL;
        $this->_timeout = 30;
        $this->_socket = false;
        if ($serverURL != '') {
            $this->useService($serverURL);
        }
    }
    function useService($serverURL, $username = NULL, $password = NULL) {
        $this->_disconnect();
        $this->_http_version = "1.1";
        $this->_keep_alive = true;
        $this->_server = array();
        $this->_key = NULL;
        $this->_keylen = 128;
        $this->_encryptMode = 0;
        $this->_cookie = '';
        $this->_charset = 'utf-8';
        $urlparts = parse_url($serverURL);
        if (!isset($urlparts['host'])) {
            if (isset($_SERVER["HTTP_HOST"])) {
                $urlparts['host'] = $_SERVER["HTTP_HOST"];
            }
            else if (isset($_SERVER["SERVER_NAME"])) {
                $urlparts['host'] = $_SERVER["SERVER_NAME"];
            }
            else {
                $urlparts['host'] = "localhost";
            }
            if (!isset($_SERVER["HTTPS"]) ||
                $_SERVER["HTTPS"] == "off"  ||
                $_SERVER["HTTPS"] == "") {
                $urlparts['scheme'] = "http";
            }
            else {
                $urlparts['scheme'] = "https";
            }
            $urlparts['port'] = $_SERVER["SERVER_PORT"];
        }

        if (!isset($urlparts['port'])) {
            if ($urlparts['scheme'] == "https") {
                $urlparts['port'] = 443;
            }
            else {
                $urlparts['port'] = 80;
            }
        }

        if (!isset($urlparts['path'])) {
            $urlparts['path'] = "/";
        }
        else if (($urlparts['path']{0} != '/') && ($_SERVER["PHP_SELF"]{0} == '/')) {
            $urlparts['path'] = substr($_SERVER["PHP_SELF"], 0, strrpos($_SERVER["PHP_SELF"], '/') + 1) . $urlparts['path'];
        }

        if (isset($urlparts['query'])) {
            $urlparts['path'] .= '?' . $urlparts['query'];
        }

        if (!isset($urlparts['user']) || !is_null($username)) {
            $urlparts['user'] = $username;
        }

        if (!isset($urlparts['pass']) || !is_null($password)) {
            $urlparts['pass'] = $password;
        }

        $this->_server['scheme'] = $urlparts['scheme'];
        $this->_server['host'] = $urlparts['host'];
        $this->_server['port'] = $urlparts['port'];
        $this->_server['path'] = $urlparts['path'];
        $this->_server['user'] = $urlparts['user'];
        $this->_server['pass'] = $urlparts['pass'];
    }
    function setProxy($host, $port = NULL, $username = NULL, $password = NULL) {
        if (is_null($host)) {
            $this->_proxy = NULL;
        }
        else {
            if (is_null($port)) {
                $urlparts = parse_url($host);
                if (isset($urlparts['host'])) {
                    $host = $urlparts['host'];
                }
                if (isset($urlparts['port'])) {
                    $port = $urlparts['port'];
                }
                else {
                    $port = 80;
                }
                if (isset($urlparts['user']) && is_null($username)) {
                    $username = $urlparts['user'];
                }
                if (isset($urlparts['pass']) && is_null($password)) {
                    $password = $urlparts['pass'];
                }
            }
            $this->_proxy = array();
            $this->_proxy['host'] = $host;
            $this->_proxy['port'] = $port;
            $this->_proxy['user'] = $username;
            $this->_proxy['pass'] = $password;
        }
    }
    function setKeyLength($keylen) {
        if (!is_null($this->_key)) {
            return false;
        }
        else {
            $this->_keylen = $keylen;
            return true;
        }
    }
    function getKeyLength() {
        return $this->_keylen;
    }
    function setEncryptMode($encryptMode) {
        if (($encryptMode >= 0) && ($encryptMode <= 3)) {
            $this->_encryptMode = (int)($encryptMode);
            return true;
        }
        else {
            $this->_encryptMode = 0;
            return false;
        }
    }
    function getEncryptMode() {
        return $this->_encryptMode;
    }
    function setCharset($charset) {
        $this->_charset = $charset;
    }
    function getCharset() {
        return $this->_charset;
    }
    function setTimeout($timeout) {
        $this->_timeout = $timeout;
    }
    function getTimeout() {
        return $this->_timeout;
    }
    function invoke($funcname, &$args, $byRef = false) {
        $result = $this->_key_exchange();
        if (is_a($result, 'PHPRPC_Error')) {
            return $result;
        }
        $request = "phprpc_func=$funcname";
        if (count($args) > 0) {
            $request .= "&phprpc_args=" . base64_encode($this->_encrypt(serialize($args), 1));
        }
        $request .= "&phprpc_encrypt={$this->_encryptMode}";
        if (!$byRef) {
            $request .= "&phprpc_ref=false";
        }
        $request = str_replace('+', '%2B', $request);
        $result = $this->_post($request);
        if (is_a($result, 'PHPRPC_Error')) {
            return $result;
        }
        $this->_warning = new PHPRPC_Error((int)$result['phprpc_errno'], base64_decode($result['phprpc_errstr']));
        if (array_key_exists('phprpc_output', $result)) {
            $this->_output = base64_decode($result['phprpc_output']);
            if ($this->_server['version'] >= 3) {
                $this->_output = $this->_decrypt($this->_output, 3);
            }
        }
        else {
            $this->_output = '';
        }
        if (array_key_exists('phprpc_result', $result)) {
            if (array_key_exists('phprpc_args', $result)) {
                $arguments = unserialize($this->_decrypt(base64_decode($result['phprpc_args']), 1));
                for ($i = 0; $i < count($arguments); $i++) {
                    $args[$i] = $arguments[$i];
                }
            }
            $result = unserialize($this->_decrypt(base64_decode($result['phprpc_result']), 2));
        }
        else {
            $result = $this->_warning;
        }
        return $result;
    }

    function getOutput() {
        return $this->_output;
    }

    function getWarning() {
        return $this->_warning;
    }

    function _connect() {
        if (is_null($this->_proxy)) {
            $host = (($this->_server['scheme'] == "https") ? "ssl://" : "") . $this->_server['host'];
            $this->_socket = @fsockopen($host, $this->_server['port'], $errno, $errstr, $this->_timeout);
        }
        else {
            $host = (($this->_server['scheme'] == "https") ? "ssl://" : "") . $this->_proxy['host'];
            $this->_socket = @fsockopen($host, $this->_proxy['port'], $errno, $errstr, $this->_timeout);
        }
        if ($this->_socket === false) {
            return new PHPRPC_Error($errno, $errstr);
        }
        socket_set_timeout($this->_socket, $this->_timeout);
        return true;
    }

    function _disconnect() {
        if ($this->_socket !== false) {
            fclose($this->_socket);
            $this->_socket = false;
        }
    }

    function _socket_read($size) {
        $content = "";
        while (!feof($this->_socket) && ($size > 0)) {
            $str = fread($this->_socket, $size);
            $content .= $str;
            $size -= strlen($str);
        }
        return $content; 
    }
    function _post($request_body, $times = 0) {
        if ($times > 2) {
            return new PHPRPC_Error(E_ERROR, "Illegal HTTP server.");
        }
        $times++;
        if ($this->_socket === false) {
            $error = $this->_connect();
            if (is_a($error, 'PHPRPC_Error')) {
                return $error;
            }
        }
        if (is_null($this->_proxy)) {
            $url = $this->_server['path'];
            $connection = "Connection: " . ($this->_keep_alive ? 'Keep-Alive' : 'close') . "\r\n" .
                          "Pragma: no-cache\r\n" .
                          "Cache-Control: no-cache\r\n";
        }
        else {
            $url = "{$this->_server['scheme']}://{$this->_server['host']}:{$this->_server['port']}{$this->_server['path']}";
            $connection = "Proxy-Connection: " . ($this->_keep_alive ? 'Keep-Alive' : 'close') . "\r\n";
            if (!is_null($this->_proxy['user'])) {
                $connection .= "Proxy-Authorization: Basic " . base64_encode($this->_proxy['user'] . ":" . $this->_proxy['pass']) . "\r\n";
            }
        }
        $auth = '';
        if (!is_null($this->_server['user'])) {
            $auth = "Authorization: Basic " . base64_encode($this->_server['user'] . ":" . $this->_server['pass']) . "\r\n";
        }
        $cookie = '';
        if ($this->_cookie) {
            $cookie = "Cookie: " . $this->_cookie . "\r\n";
        }
        $content_len = strlen($request_body);
        $request =
            "POST $url HTTP/{$this->_http_version}\r\n" .
            "Host: {$this->_server['host']}:{$this->_server['port']}\r\n" .
            "User-Agent: PHPRPC Client 3.0 for PHP\r\n" .
            $auth .
            $connection .
            $cookie .
            "Accept: */*\r\n" .
            "Accept-Encoding: gzip,deflate\r\n" .
            "Content-Type: application/x-www-form-urlencoded; charset={$this->_charset}\r\n" .
            "Content-Length: {$content_len}\r\n" .
            "\r\n" .
            $request_body;
        fputs($this->_socket, $request, strlen($request));
        $continue = true;
        while ($continue) {
            $header = '';
            while ((($line = fgets($this->_socket)) != "\r\n") && !feof($this->_socket)) {
                $header .= $line;
            }
            $response_header = $this->_parseHeader($header);
            if (!$response_header) {
                $this->_disconnect();
                return $this->_post($request_body, $times);
            }
            if (is_a($response_header, 'PHPRPC_Error')) {
                $this->_disconnect();
                return $response_header;
            }
            $continue = ($response_header['status'] == 100);
            if ($continue) fputs($this->_socket, "\r\n");
        }
        $response_body = '';
        if (strtolower($response_header['transfer_encoding']) == 'chunked') {
            $s = fgets($this->_socket);
            if ($s == "") {
                $this->_disconnect();
                return array();
            }
            $chunk_size = (int)hexdec($s); 
            while ($chunk_size > 0) {
                $response_body .= $this->_socket_read($chunk_size); 
                if (fgets($this->_socket) != "\r\n") {
                    $this->_disconnect();
                    return new PHPRPC_Error(1, "Response is incorrect.");                    
                }
                $chunk_size = (int)hexdec(fgets($this->_socket));
            }
            fgets($this->_socket);
        }
        elseif (!is_null($response_header['content_length'])) {
            $response_body = $this->_socket_read($response_header['content_length']); 
        }
        else {
            while (!feof($this->_socket)) {
                $response_body .= fread($this->_socket, 4096);
            }
            $this->_keep_alive = false;
            $this->_disconnect();
        }
        if (strtolower($response_header['content_encoding']) == 'gzip') {
            $response_body = gzdecode($response_body);
        }
        if (!$this->_keep_alive) $this->_disconnect();
        if ($this->_keep_alive && strtolower($response_header['connection']) == 'close') {
            $this->_keep_alive = false;
            $this->_disconnect();
        }
        return $this->_parseBody($response_body);
    }
    function _parseHeader($header) {
        if (preg_match('/HTTP\/(\d\.\d)\s+(\d+)([^(\r|\n)]*)(\r\n|$)/i', $header, $match)) {
            $this->_http_version = $match[1];
            $status = (int)$match[2];
            $status_message = trim($match[3]);
            if (($status != 100) && ($status != 200)) {
                return new PHPRPC_Error($status, $status_message);
            }
        }
        else {
            return false;
        }
        if (preg_match('/X\-Powered\-By\: PHPRPC Server\/([^(\r|\n)]*)(\r\n|$)/i', $header, $match)) {
            $this->_server['version'] = (float)$match[1];
        }
        else {
            return new PHPRPC_Error(E_ERROR, "Illegal PHPRPC server.");
        }
        if (preg_match('/Content\-Type\: text\/plain\; charset\=([^(\r|\n)]*)(\r\n|$)/i', $header, $match)) {
            $this->_charset = $match[1];
        }
        if (preg_match('/\r\nSet\-Cookie\:([^(\r|\n)]*)(\r\n|$)/i', $header, $match)) {
            $this->_cookie = array();
            $cookie = explode(";", $match[1]);
            for ($i = 0; $i < count($cookie); $i++) {
                $cookie[$i] = trim($cookie[$i]);
                if ((substr($cookie[$i], 0, 5) != 'path=') and
                    (substr($cookie[$i], 0, 7) != 'domain=') and
                    (substr($cookie[$i], 0, 8) != 'expires=')) {
                    $this->_cookie[] = $cookie[$i];
                }
            }
            $this->_cookie = join('; ', $this->_cookie);
        }
        if (preg_match('/Transfer\-Encoding\:([^(\r|\n)]*)(\r\n|$)/i', $header, $match)) {
            $transfer_encoding = trim($match[1]);
        }
        else {
            $transfer_encoding = '';
        }
        if (preg_match('/Content\-Encoding\: ([^(\r|\n)]*)(\r\n|$)/i', $header, $match)) {
            $content_encoding = trim($match[1]);
        }
        else {
            $content_encoding = '';
        }
        if (preg_match('/Content\-Length\:([^(\r|\n)]*)(\r\n|$)/i', $header, $match)) {
            $content_length = (int)trim($match[1]);
        }
        else {
            $content_length = NULL;
        }
        if (preg_match('/Connection\:([^(\r|\n)]*)(\r\n|$)/i', $header, $match)) {
            $connection = trim($match[1]);
        }
        else {
            $connection = 'Keep-Alive';
        }
        return array('status' => $status,
                     'transfer_encoding' => $transfer_encoding,
                     'content_encoding' => $content_encoding,
                     'content_length' => $content_length,
                     'connection' => $connection);
    }
    function _parseBody($body) {
        $body = explode(";\r\n", $body);
        $result = array();
        $n = count($body);
        for ($i = 0; $i < $n; $i++) {
            $p = strpos($body[$i], '=');
            if ($p !== false) {
                $l = substr($body[$i], 0, $p);
                $r = substr($body[$i], $p + 1);
                $result[$l] = trim($r, '"');
            }
        }
        return $result;
    }
    function _key_exchange() {
        if (!is_null($this->_key) || ($this->_encryptMode == 0)) return true;
        $request = "phprpc_encrypt=true&phprpc_keylen={$this->_keylen}";
        $result = $this->_post($request);
        if (is_a($result, 'PHPRPC_Error')) {
            return $result;
        }
        if (array_key_exists('phprpc_keylen', $result)) {
            $this->_keylen = (int)$result['phprpc_keylen'];
        }
        else {
            $this->_keylen = 128;
        }
        if (array_key_exists('phprpc_encrypt', $result)) {
            $encrypt = unserialize(base64_decode($result['phprpc_encrypt']));
            require_once('bigint.php');
            require_once('xxtea.php');
            $x = bigint_random($this->_keylen - 1, true);
            $key = bigint_powmod(bigint_dec2num($encrypt['y']), $x, bigint_dec2num($encrypt['p']));
            if ($this->_keylen == 128) {
                $key = bigint_num2str($key);
            }
            else {
                $key = pack('H*', md5(bigint_num2dec($key)));
            }
            $this->_key = str_pad($key, 16, "\0", STR_PAD_LEFT);
            $encrypt = bigint_num2dec(bigint_powmod(bigint_dec2num($encrypt['g']), $x, bigint_dec2num($encrypt['p'])));
            $request = "phprpc_encrypt=$encrypt";
            $result = $this->_post($request);
            if (is_a($result, 'PHPRPC_Error')) {
                return $result;
            }
        }
        else {
            $this->_key = NULL;
            $this->_encryptMode = 0;
        }
        return true;
    }
    function _encrypt($str, $level) {
        if (!is_null($this->_key) && ($this->_encryptMode >= $level)) {
            $str = xxtea_encrypt($str, $this->_key);
        }
        return $str;
    }
    function _decrypt($str, $level) {
        if (!is_null($this->_key) && ($this->_encryptMode >= $level)) {
            $str = xxtea_decrypt($str, $this->_key);
        }
        return $str;
    }
}

if (function_exists("overload") && version_compare(phpversion(), "5", "<")) {
    eval('
    class PHPRPC_Client extends _PHPRPC_Client {
        function __call($function, $arguments, &$return) {
            $return = $this->invoke($function, $arguments);
            return true;
        }
    }
    overload("phprpc_client");
    ');
}
else {
    class PHPRPC_Client extends _PHPRPC_Client {
        function __call($function, $arguments) {
            return $this->invoke($function, $arguments);
        }
    }
}
?>