<?php

use App\Models\FileUpload;
use App\Models\Helper;
use App\Models\Ticket;
use Config\Core\Database;

$ticketCode = Helper::form_input($_POST['code']);
$message = Helper::form_input($_POST['message']);

/** check code */
$ticket = Ticket::findByCode($ticketCode);
if(!$ticket) {
    JsonResponse([
        'success' => false,
        'message' => "Invalid Code",
        'data' => []
    ]);
}

/** Check status */
if($ticket['TICKET_STS'] != -1) {
    JsonResponse([
        'success' => false,
        'message' => "Invalid Status",
        'data' => []
    ]);
}

/** check Attachment */
if(!empty($_FILES['attachment']) && $_FILES['attachment']['error'] == 0) {
    $uploadAttachment = FileUpload::upload_myfile($_FILES['attachment']);
    if(!is_array($uploadAttachment) || !array_key_exists("filename", $uploadAttachment)) {
        JsonResponse([
            'success' => false,
            'message' => "Upload file gagal",
            'data' => []
        ]);
    }

    /** Insert Attachment */
    $insertAttachment = Database::insert("tb_ticket_detail", [
        'TDETAIL_TCODE' => $ticketCode,
        'TDETAIL_FROM' => $user['MBR_ID'],
        'TDETAIL_TYPE' => "member",
        'TDETAIL_CONTENT_TYPE' => "image",
        'TDETAIL_CONTENT' => FileUpload::awsFile($uploadAttachment['filename']),
        'TDETAIL_DATETIME' => date("Y-m-d H:i:s")
    ]);

    if(!$insertAttachment) {
        JsonResponse([
            'success' => false,
            'message' => "File gagal disimpan",
            'data' => []
        ]);
    }
}

/** Insert message */
$insert = Database::insert("tb_ticket_detail", [
    'TDETAIL_TCODE' => $ticketCode,
    'TDETAIL_FROM' => $user['MBR_ID'],
    'TDETAIL_TYPE' => "member",
    'TDETAIL_CONTENT_TYPE' => "message",
    'TDETAIL_CONTENT' => $message,
    'TDETAIL_DATETIME' => date("Y-m-d H:i:s")
]);

if(!$insert) {
    JsonResponse([
        'success' => false,
        'message' => "File gagal disimpan",
        'data' => []
    ]);
}

JsonResponse([
    'success' => true,
    'message' => "Berhasil",
    'data' => []
]);