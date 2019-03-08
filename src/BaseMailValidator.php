<?php

namespace img\mail;

require_once __DIR__ . '/Messages.php';

use img\mail\Messages;

abstract class BaseMailValidator
{
    /**
     * Порт для подключения к SMTP-серверу
     * @access public
     * @var int
     */
    public $smtpPort = 25;

    /**
     * Домен, используемый при отправке HELO в SMTP-сессии
     * @access public
     * @var string
     */
    public $fromDomain = 'gmail.com';

    /**
     * Email, используетмый при указании RCPT TO в SMTP-сессии
     * @access public
     * @var string
     */
    public $fromEmail = 'info@gmail.com';

    /**
     * Time-out установки соединения с SMTP-сервером
     * @access public
     * @var int
     */
    public $socketTimeout = 10;

    /**
     * Ключ массива, возвращающий список MX-серверов
     */
    const DNS_TARGET = 'target';

    /**
     * Ключ массива, содержащий статус выполнения
     * Может принимать значения true|false
     */
    const RESPONSE_CODE_KEY = 'response';

    /**
     * Ключ массива, содержащий сообщение о выполнении
     */
    const RESPONSE_MSG_KEY = 'message';

    const RESPONSE_MX_SERVER = 'mx';

    const RESPONSE_MX_SERVERS_SECTION = 'mxrecords';

    /**
     * Код ответа, используется при успешном выполнении
     */
    const RESPONSE_CODE_TRUE = 'true';

    /**
     * Код ответа, используется при неудачном выполнении
     */
    const RESPONSE_CODE_FALSE = 'false';

    /**
     * Хранит состояние сокета при подключении к SMTP-серверу
     * @access protected
     * @var resource
     */
    protected $_socket;

    /**
     * Email, подлежащий валидации
     * @access protected
     * @var string
     */
    protected $_email;

    /**
     * Доменная часть email-адреса, подлежащего валидации
     * @access protected
     * @see $this->getDomainPartFromEmail()
     * @var string
     */
    protected $_emailDomain;

    /**
     * @access protected
     * @see $this->getMXArray()
     * @var array
     */
    protected $_mxServers = [];

    /**
     * Хранит состояние исполнения методов, включая сообщения об ошибках
     * @access protected
     * @see $this->setResponseCode()
     * @var array
     */
    protected $_response;

    /**
     * BaseMailValidator constructor.
     * @param $email string email-адрес, подлежащий валидации
     */
    public function __construct ($email)
    {
//        if(!isset($email) || empty($email)) {
//            $this->setResponseCode(static::RESPONSE_CODE_FALSE, Messages::get('MissingEmailAddress'));
//        }
        $this->_email = $email;
        $this->getMXArray();
    }

    /**
     * Осуществляет валидацию формата email-адреса
     * @access protected
     * @return array
     */
    protected function validateEmailFormat()
    {
        if($this->getResponseCode()[static::RESPONSE_CODE_KEY] !== static::RESPONSE_CODE_FALSE) {
            if (filter_var($this->_email, FILTER_VALIDATE_EMAIL)) {
                $this->setResponseCode(static::RESPONSE_CODE_TRUE);
                return $this->getResponseCode();
            }
            $this->setResponseCode(static::RESPONSE_CODE_FALSE, Messages::get('IncorrectEmailFormat'));
            return $this->getResponseCode();
        }
        return $this->getResponseCode();
    }

    /**
     * Получает доменную часть, переданного email-адреса
     * @access protected
     * @return array
     */
    protected function getDomainPartFromEmail()
    {
        $this->validateEmailFormat();

        if($this->getResponseCode()[static::RESPONSE_CODE_KEY] == static::RESPONSE_CODE_TRUE) {
            $this->_emailDomain = substr($this->_email, strpos($this->_email, '@') + 1);
            $this->setResponseCode(static::RESPONSE_CODE_TRUE);
        }
        return $this->getResponseCode();
    }

    /**
     * @access protected
     * @return array
     */
    protected function getMXArray()
    {
        $this->getDomainPartFromEmail();
        if($this->getResponseCode()[static::RESPONSE_CODE_KEY] == static::RESPONSE_CODE_TRUE) {
            $dnsMXArray = dns_get_record($this->_emailDomain, DNS_MX);
            if (empty($dnsMXArray)) {
                return $this->setResponseCode(static::RESPONSE_CODE_FALSE, Messages::get('DNSLookupError'));
            }
            $this->_mxServers = array_column($dnsMXArray, static::DNS_TARGET);
            $this->setResponseCode(static::RESPONSE_CODE_TRUE);
            return $this->getResponseCode();
        }
        return $this->getResponseCode();
    }

    /**
     * @access protected
     * @param $code
     * @param null $msg
     * @return array
     */
    protected function setResponseCode($code, $msg = null)
    {
        unset ($this->_response);
        $this->_response[static::RESPONSE_CODE_KEY] = $code;
        $this->_response[static::RESPONSE_MSG_KEY] = $msg;
        return $this->_response;
    }

    /**
     * @return array
     */
    protected function getResponseCode()
    {
        return $this->_response;
    }
}