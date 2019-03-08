<?php

namespace img\mail;

require_once __DIR__ . '/Messages.php';
require_once __DIR__ . '/EmailFactoryJSON.php';
require_once __DIR__ . '/EmailFactoryXML.php';

use img\mail\Messages;
use img\mail\EmailFactoryJSON;
use img\mail\EmailFactoryXML;

class EmailService
{
    const RESPONSE_FORMAT_XML = 'xml';

    const RESPONSE_FORMAT_JSON = 'json';

    const OPTIONS_EMAIL_KEY = 'email';

    const OPTIONS_FORMAT_KEY = 'format';

    protected $_options = [];

    protected $_format;

    public function __construct (array $options)
    {
        $this->_options = $options;
        if(!isset($this->_options[static::OPTIONS_EMAIL_KEY]) || empty($this->_options[static::OPTIONS_EMAIL_KEY])) {
            throw new \Exception(Messages::get('MissingEmailAddress'));
        }
        $this->_format = $this->getResponseFormat();
        if($this->_format == static::RESPONSE_FORMAT_JSON) {
            new EmailFactoryJSON($this->_options[static::OPTIONS_EMAIL_KEY]);
        } else {
            new EmailFactoryXML($this->_options[static::OPTIONS_EMAIL_KEY]);
        }
    }

    protected static function getFormatFactory()
    {
        return [
            static::RESPONSE_FORMAT_JSON,
            static::RESPONSE_FORMAT_XML,
        ];
    }

    protected function getResponseFormat()
    {
        $defaultFormat = static::RESPONSE_FORMAT_XML;
        if(!isset($this->_options[static::OPTIONS_FORMAT_KEY]) || empty($this->_options[static::OPTIONS_FORMAT_KEY])) {

            return $this->_options[static::OPTIONS_FORMAT_KEY] = $defaultFormat;
        } elseif(!in_array($this->_options[static::OPTIONS_FORMAT_KEY], static::getFormatFactory())) {

            return $this->_options[static::OPTIONS_FORMAT_KEY] = $defaultFormat;
        }
        return $this->_options[static::OPTIONS_FORMAT_KEY];
    }
}