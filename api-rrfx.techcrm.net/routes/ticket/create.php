<?php

use App\Models\Helper;
use App\Models\Logger;
use Config\Core\Database;

$data = Helper::getSafeInput($_POST);
if(empty($data['subject'])) {
    ApiResponse([
        'status' => false,
        'message' => "Kolom Subjek perlu diisi",
        'response' => []
    ]);
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
mysqli_begin_transaction($db);

/** Insert */
$ticketCode = uniqid();
$datetime = date("Y-m-d H:i:s");
$insert = Database::insert("tb_ticket", [
    'TICKET_CODE' => $ticketCode,
    'TICKET_MBR' => $user['MBR_ID'],
    'TICKET_SUBJECT' => $data['subject'],
    'TICKET_STS' => -1,
    'TICKET_DATETIME' => $datetime
]);

if(!$insert) {
    $db->rollback();
    ApiResponse([
        'status' => false,
        'message' => "Gagal membuat ticket",
        'response' => []
    ]);
}

/** first message */
if(!empty($data['desc'])) {
    $insertFirstMessage = Database::insert("tb_ticket_detail", [
        'TDETAIL_TCODE' => $ticketCode,
        'TDETAIL_FROM' => $user['MBR_ID'],
        'TDETAIL_TYPE' => "member",
        'TDETAIL_CONTENT_TYPE' => "message",
        'TDETAIL_CONTENT' => $data['desc'],
        'TDETAIL_DATETIME' => $datetime,
    ]);

    if(!$insertFirstMessage) {
        $db->rollback();
        ApiResponse([
            'status' => false,
            'message' => "Gagal membuat pesan",
            'response' => []
        ]);
    }
}

Logger::client_log([
    'mbrid' => $user['MBR_ID'],
    'module' => "ticket",
    'message' => "Membua ticket baru dengan kode: $ticketCode, subjek: ".$data['subject'],
    'data' => $data
]);

$db->commit();
ApiResponse([
    'status' => true,
    'message' => "Berhasil",
    'response' => [
        'ticketCode' => $ticketCode
    ]
]);