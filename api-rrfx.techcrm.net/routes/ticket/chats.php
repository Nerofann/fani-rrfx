<?php

$data = $helperClass->getSafeInput($_GET);
if(empty($data['code'])) {
    ApiResponse([
        'status' => false,
        'message' => "Code is required",
        'response' => []
    ]);
}

/** check code */
$ticketCode = $data['code'];
$sqlGet = $db->query("SELECT * FROM tb_ticket WHERE TICKET_CODE = '{$ticketCode}' LIMIT 1");
$ticket = $sqlGet->fetch_assoc();
if($sqlGet->num_rows != 1) {
    ApiResponse([
        'status' => false,
        'message' => "Invalid Code",
        'response' => [] 
    ]);
}

/** Get chat history */
$chats = [];
$sqlGetChats = $db->query("
    SELECT 
        ttd.*,
        ta.ADM_NAME
    FROM tb_ticket_detail ttd 
    LEFT JOIN tb_admin ta ON (ta.ADM_ID = ttd.TDETAIL_FROM)
    WHERE ttd.TDETAIL_TCODE = '{$ticketCode}' 
    ORDER BY ttd.TDETAIL_DATETIME ASC
");

foreach($sqlGetChats->fetch_all(MYSQLI_ASSOC) as $chat) {
    switch($chat['TDETAIL_TYPE']) {
        case "member": $sender = "You"; break;
        case "admin": $sender = "Admin (".$chat['ADM_NAME'].")"; break;
        case "system": $sender = "System"; break;
        default: $sender = "-";
    }

    $chats[] = [
        'id' => md5($ticketCode.$chat['ID_TDETAIL']),
        'from' => $sender,
        'type' => $chat['TDETAIL_TYPE'],
        'content_type' => $chat['TDETAIL_CONTENT_TYPE'],
        'content' => $chat['TDETAIL_CONTENT'],
        'date' => date("Y-m-d", strtotime($chat['TDETAIL_DATETIME'])),
        'time' => date("H:i:s", strtotime($chat['TDETAIL_DATETIME'])),
    ];
}

ApiResponse([
    'status' => true,
    'message' => "Success",
    'response' => $chats
]);