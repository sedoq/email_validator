<?php

namespace img\mail;

require_once __DIR__ . '/MailValidator.php';
require_once __DIR__ . '/Messages.php';
require_once __DIR__ . '/XML.php';

abstract class BaseEmailFactory
{
    public $email;

    public function __construct ($email)
    {
        $this->email = $email;
        $this->run();
    }

    abstract protected function run();
}