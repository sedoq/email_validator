<?php

require '../vendor/autoload.php';

use img\mail\EmailService;

$email = isset($_GET['mail']) ? $_GET['mail'] : null;
$format = isset($_GET['format']) ? $_GET['format'] : null;

new EmailService(['email' => $email, 'format' => $format]);