<?php

use App\Models\Database;

if(empty($_FILES['image']) || $_FILES['image']['error'] != 0) {
    ApiResponse([
        'status' => false,
        'message' => "Mohon upload avatar",
        'response' => []
    ],400);
}

$uploadFile = upload_myfile($_FILES['image'], "avatar");
if(!is_array($uploadFile) || !array_key_exists("filename", $uploadFile)) {
    ApiResponse([
        'status' => false,
        'message' => $uploadFile ?? "Gagal mengupload avatar",
        'response' => []
    ],400);
}

/** Update avatar */
$update = Database::updateWithArray("tb_member", ['MBR_AVATAR' => $uploadFile['filename']], ['MBR_ID' => $userData['MBR_ID']]);
if(!$update) {
    ApiResponse([
        'status' => false,
        'message' => "Gagal memperbarui avatar",
        'response' => []
    ],400);
}

newInsertLog([
    'mbrid' => $userData['MBR_ID'],
    'module' => "profile",
    'message' => "Memperbarui Avatar",
    'device' => "mobile",
    'data'  => $uploadFile
]);

ApiResponse([
    'status' => true,
    'message' => "Avatar berhasil diperbarui",
    'response' => []
]);