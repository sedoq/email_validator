<?php

namespace img\mail;

require_once __DIR__ . '/BaseEmailFactory.php';

use img\mail\BaseEmailFactory;

class EmailFactoryXML extends BaseEmailFactory
{

    public function __construct ($email)
    {
        parent::__construct($email);
    }

    protected function run ()
    {
        $mail = new MailValidator($this->email);
        $result = $mail->validate();
        $mxs = $mail->getMxServers();

        $xml = new XML();
        $xml->comment(Messages::get('validateResult'));
        $xml->writeElement(MailValidator::BOOL_RESULT, $result[MailValidator::RESPONSE_CODE_KEY]);
        $xml->comment(Messages::get('validateResultText'));
        $xml->writeElement(MailValidator::BOOL_RESULT_TEXT, $result[MailValidator::RESPONSE_MSG_KEY]);
        if(isset($mxs) && !empty($mxs) && is_array($mxs) && $result[MailValidator::RESPONSE_CODE_KEY] == MailValidator::RESPONSE_CODE_TRUE) {
            $xml->comment(Messages::get('mxServersList'));
            $xml->startElement(MailValidator::RESPONSE_MX_SERVERS_SECTION);
            foreach ($mxs as $mx) {
                $xml->writeElement(MailValidator::RESPONSE_MX_SERVER, $mx);
            }
            $xml->endElement();
        }
    }
}