<?php

namespace img\mail;

require_once __DIR__ . '/BaseEmailFactory.php';

use img\mail\BaseEmailFactory;


class EmailFactoryJSON extends BaseEmailFactory
{

    public function __construct ($email)
    {
        parent::__construct($email);
    }

    protected function run ()
    {
        $result = [];
        $mail = new MailValidator($this->email);
        $result = $mail->validate();
        $mxs = $mail->getMxServers();
        if(isset($mxs) && !empty($mxs) && is_array($mxs) && $result[MailValidator::RESPONSE_CODE_KEY] == MailValidator::RESPONSE_CODE_TRUE) {
            foreach ($mxs as $mx) {
                $result[MailValidator::RESPONSE_MX_SERVERS_SECTION][] = $mx;
            }
        }

        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }
}