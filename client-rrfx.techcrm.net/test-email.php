<?php

use Config\Core\EmailSender;

require_once __DIR__ . "/../config/setting.php";

$emailData = [
    'subject'   => "Email Verification",
    'code'  => 123,
];

$emailSender = EmailSender::init(['email' => "bomba.94@yopmail.com", 'name' => "Bomba 94"]);
$emailSender->useFile("register", $emailData);
$send = $emailSender->send();

print_r($send);