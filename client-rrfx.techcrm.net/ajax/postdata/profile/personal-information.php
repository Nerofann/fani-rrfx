<?php

use App\Models\FileUpload;
use App\Models\Helper;
use Config\Core\Database;

$data = Helper::getSafeInput($_POST);
$required = [
    'city' => "Kota",
    'zip' => "Kodepos",
    'tempat_lahir' => "Tempat Lahir",
    'tanggal_lahir' => "Tanggal Lahir",
    // 'identity_type' => "Tipe Identitas",
    // 'identity_number' => "Nomor Identitas",
    'address' => "Alamat Lengkap"
];

foreach($required as $key => $text) {
    if(empty($data[ $key ])) {
        JsonResponse([
            'success' => false,
            'message' => "Kolom {$text} wajib diisi",
            'data' => []
        ]);
    }
}

/* Update **/
$updateData = [
    'MBR_CITY' => $data['city'],
    'MBR_ZIP' => $data['zip'],
    'MBR_TMPTLAHIR' => $data['tempat_lahir'],
    'MBR_TGLLAHIR' => date("Y-m-d", strtotime($data['tanggal_lahir'])),
    'MBR_ADDRESS' => $data['address']
];

/** check Avatar */
if(!empty($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
    $uploadFile = FileUpload::upload_myfile($_FILES['avatar'], "avatar");
    if(!is_array($uploadFile) || !array_key_exists("filename", $uploadFile)) {
        JsonResponse([
            'success' => false,
            'message' => $uploadFile ?? "Upload avatar gagal",
            'data' => []
        ]);
    }

    $updateData['MBR_AVATAR'] = $uploadFile['filename'];
}

$update = Database::update("tb_member", $updateData, ['MBR_ID' => $user['MBR_ID']]);
if(!$update) {
    JsonResponse([
        'success' => false,
        'message' => "Gagal memperbarui data",
        'data' => []
    ]);
}

JsonResponse([
    'success' => true,
    'message' => "Berhasil",
    'data' => []
]);
