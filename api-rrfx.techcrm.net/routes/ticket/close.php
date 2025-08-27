<?php

$data = $helperClass->getSafeInput($_POST);
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

if($ticket['TICKET_STS'] != -1) {
    ApiResponse([
        'status' => false,
        'message' => "Ticket already closed at " . date("Y-m-d H:i:s", strtotime($ticket['TICKET_DATETIME_CLOSE'])),
        'response' => [] 
    ]);
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
mysqli_begin_transaction($db);

/** Insert System Message */
$message = $userData['MBR_NAME']." Close the ticket";
$datetime = date("Y-m-d H:i:s");
$insert = $db->prepare("INSERT INTO tb_ticket_detail (TDETAIL_TCODE, TDETAIL_TYPE, TDETAIL_FROM, TDETAIL_CONTENT, TDETAIL_DATETIME) VALUES (?, 'system', ?, ?, ?)");
$insert->bind_param("siss", $data['code'], $userData['MBR_ID'], $message, $datetime);
if(!$insert->execute()) {
    $db->rollback();
    ApiResponse([
        'status' => false,
        'message' => "System failed to send message",
        'response' => []
    ]);
}

$updateStatus = $db->prepare("UPDATE tb_ticket SET TICKET_STS = 1, TICKET_DATETIME_CLOSE = ? WHERE TICKET_CODE = ?");
$updateStatus->bind_param("ss", $datetime, $data['code']);
if(!$updateStatus->execute()) {
    $db->rollback();
    ApiResponse([
        'status' => false,
        'message' => "Failed to close ticket",
        'response' => []
    ]);
}

$db->commit();
ApiResponse([
    'status' => true,
    'message' => "Successfully close ticket #{$ticketCode}",
    'response' => []
]);