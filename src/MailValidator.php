<?php

namespace img\mail;

require_once __DIR__ . '/BaseMailValidator.php';
require_once __DIR__ . '/Messages.php';

use img\mail\BaseMailValidator;
use img\mail\Messages;

/**
 * Class MailValidator
 * @package img\mail
 */
class MailValidator extends BaseMailValidator
{
    /**
     * Используются при генерации комментариев XML-документа
     */
    const BOOL_RESULT = 'code';
    const BOOL_RESULT_TEXT = 'message';

    /**
     * MailValidator constructor.
     * @param $email string email-адрес, подлежащий валидации
     */
    public function __construct ($email)
    {
        if(!isset($email) || empty($email)) {
            $this->setResponseCode(static::RESPONSE_CODE_FALSE, Messages::get('MissingEmailAddress'));
        }
        parent::__construct($email);
    }

    /**
     * Осуществляет подключение к одному из SMTP-серверов, обслуживающих домен, указанного email-адреса, производит валидацию
     * @access public
     * @return mixed
     */
    public function validate()
    {
        if($this->getResponseCode()[static::RESPONSE_CODE_KEY] !== static::RESPONSE_CODE_FALSE) {
            foreach ($this->_mxServers as $mx) {
                if($this->smtpConnect($mx)) {
                    $this->sendCommandSMTP("HELO " . $this->fromDomain . "\r\n");
                    $this->sendCommandSMTP("MAIL FROM:<" . $this->fromEmail . ">\r\n");
                    $this->sendCommandSMTP("RCPT TO:<" . $this->_email . ">\r\n");
                    $result = $this->sendCommandSMTP("DATA\r\n");
                    $this->sendCommandSMTP("QUIT\r\n");
                    $this->smtpDisconnect();
                    $code = preg_split('/\s+/', $result);
                    $codeStatus = ($code[0] == 250) ? static::RESPONSE_CODE_TRUE : static::RESPONSE_CODE_FALSE;
                    $codeMsg = ($code[0] == 250) ? Messages::get('EmailExist') : Messages::get('EmailDoesntExist');
                    $this->setResponseCode($codeStatus, $codeMsg);
                    break;
                }
            }
        }
        return $this->getResponseCode();
    }

    /**
     * @access public
     * @return array
     */
    public function getMxServers()
    {
        return $this->_mxServers;
    }

    /**
     * Устанавливает соединение с сокетом
     * @access protected
     * @param $smtpHost string ip-адрес или fqdn smtp-сервера
     * @return resource
     */
    protected function smtpConnect($smtpHost)
    {
        return $this->_socket = fsockopen($smtpHost, $this->smtpPort, $errno, $errstr, $this->socketTimeout);
    }

    /**
     * Отправляет команды сокету
     * @access protected
     * @param $command string команда, отправляемая сокету
     * @return bool|string
     */
    protected function sendCommandSMTP($command)
    {
        fputs($this->_socket, $command);
        $data = fgets ($this->_socket,1024);
        return $data;
    }

    /**
     * Закрывает соединение с сокетом
     * @access protected
     * @return bool
     */
    protected function smtpDisconnect()
    {
        return fclose($this->_socket);
    }
}