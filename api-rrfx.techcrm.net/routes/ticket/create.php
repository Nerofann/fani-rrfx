<?php

use App\Models\Database;

$subject = form_input($_POST['subject'] ?? "");
if(empty($subject)) {
    ApiResponse([
        'status' => false,
        'message' => "Subject field is required",
        'response' => []
    ], 400);
}

$code = uniqid();
$datetime = date("Y-m-d H:i:s");

/** Insert */
$insert = Database::insertWithArray("tb_ticket", [
    'TICKET_CODE' => $code,
    'TICKET_MBR' => $userData['MBR_ID'],
    'TICKET_SUBJECT' => $subject,
    'TICKET_STS' => -1,
    'TICKET_DATETIME' => $datetime
]);

if(!$insert) {
    ApiResponse([
        'status' => false,
        'message' => "Failed to create ticket",
        'response' => []
    ], 400);
}

ApiResponse([
    'status' => true,
    'message' => "Create ticket successfull",
    'response' => [
        'code' => $code
    ]
]);