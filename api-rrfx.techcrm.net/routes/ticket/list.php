<?php

use App\Models\Ticket;

$sqlGet = $db->query("SELECT * FROM tb_ticket WHERE MD5(MD5(TICKET_MBR)) = '{$userId}' ORDER BY TICKET_DATETIME DESC");
$result = [];
if($sqlGet) {
    foreach($sqlGet->fetch_all(MYSQLI_ASSOC) as $ticket) {
        $result[] = [
            'code' => $ticket['TICKET_CODE'],
            'subject' => $ticket['TICKET_SUBJECT'],
            'status' => strtolower(Ticket::$status[ $ticket['TICKET_STS'] ]['text'] ?? ""),
            'created_at' => date("Y-m-d H:i:s", strtotime($ticket['TICKET_DATETIME'])),
            'closed_at' => (empty($ticket['TICKET_DATETIME_CLOSE']))? "" : date("Y-m-d H:i:s", strtotime($ticket['TICKET_DATETIME_CLOSE'])),
        ];
    }
}

ApiResponse([
    'status' => true,
    'message' => "Berhasil",
    'response' => $result
]);