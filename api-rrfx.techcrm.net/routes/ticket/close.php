<?php

use App\Models\Helper;
use App\Models\Ticket;
use Config\Core\Database;

/** check code */
$ticketCode = Helper::form_input($_POST['code']);
$ticket = Ticket::findByCode($ticketCode);
if(!$ticket) {
    ApiResponse([
        'status' => false,
        'message' => "Invalid Code",
        'response' => []
    ]);
}

/** Check status */
if($ticket['TICKET_STS'] != -1) {
    ApiResponse([
        'status' => false,
        'message' => "Invalid Status",
        'response' => []
    ]);
}

$update = Database::update("tb_ticket", ['TICKET_STS' => 1], ['TICKET_CODE' => $ticketCode]);
if(!$update) {
    ApiResponse([
        'status' => false,
        'message' => "Penutupan tiket gagal",
        'response' => []
    ]);
}

ApiResponse([
    'status' => true,
    'message' => "Berhasil",
    'response' => []
]);