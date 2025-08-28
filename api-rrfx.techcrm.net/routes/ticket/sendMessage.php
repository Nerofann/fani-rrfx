<?php

use App\Models\Database;

$data = $helperClass->getSafeInput($_POST);
foreach(['code'] as $req) {
    if(empty($data[$req])) {
        ApiResponse([
            'status' => false,
            'message' => "{$req} is required",
            'response' => [] 
        ]);
    }
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

/** check apakah tidak ada file / pesan yang dikirim */
if((empty($_FILES['image']) || $_FILES['image']['error'] != 0) && empty($data['message'])) {
    ApiResponse([
        'status' => false,
        'message' => "Message field is required",
        'response' => [] 
    ]);
}

/** Upload File */
if(!empty($_FILES['image']) && $_FILES['image']['error'] == 0) {
    $uploadFile = upload_myfile($_FILES['image'], "ticket_img");
    if(!is_array($uploadFile) || !array_key_exists("filename", $uploadFile)) {
        ApiResponse([
            'status' => false,
            'message' => $uploadFile ?? "Failed to upload image",
            'response' => [] 
        ]);
    }

    /** Insert Image */
    $insertImage = Database::insertWithArray("tb_ticket_detail", [
        'TDETAIL_TCODE' => $ticketCode,
        'TDETAIL_TYPE' => "member",
        'TDETAIL_FROM' => $userData['MBR_ID'],
        'TDETAIL_CONTENT_TYPE' => "image",
        'TDETAIL_CONTENT' => $aws_folder . $uploadFile['filename'],
        'TDETAIL_DATETIME' => date("Y-m-d H:i:s")
    ]);

    if(!$insertImage) {
        ApiResponse([
            'status' => false,
            'message' => "Failed to save image",
            'response' => [] 
        ]);
    }
}

if(!empty($data['message'])) {
    /** insert */
    $insert = Database::insertWithArray("tb_ticket_detail", [
        'TDETAIL_TCODE' => $ticketCode,
        'TDETAIL_TYPE' => "member",
        'TDETAIL_FROM' => $userData['MBR_ID'],
        'TDETAIL_CONTENT' => $data['message'],
        'TDETAIL_DATETIME' => date("Y-m-d H:i:s")
    ]);
    
    if(!$insert) {
        ApiResponse([
            'status' => false,
            'message' => "Failed to send message",
            'response' => [] 
        ]);
    }
}

ApiResponse([
    'status' => true,
    'message' => "Send message sucessfully",
    'response' => [] 
]);