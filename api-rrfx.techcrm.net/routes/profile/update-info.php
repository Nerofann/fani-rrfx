<?php

use App\Models\FileUpload;
use App\Models\Helper;
use App\Models\Wilayah;
use Config\Core\Database;

$data = Helper::getSafeInput($_POST);
$required = [
    'zip' => "Kodepos",
    'place_of_birth' => "Tempat Lahir",
    'date_of_birth' => "Tanggal Lahir",
    'province' => "Provinsi",
    'city' => "Kabupaten/Kota",
    'district' => "Kecamatan",
    'villages' => "Kelurahan/Desa",
];

foreach($required as $key => $text) {
    if(empty($data[ $key ])) {
        ApiResponse([
            'status' => false,
            'message' => "Kolom {$text} wajib diisi",
            'response' => []
        ]);
    }
}

/** check kodepos */
if(is_numeric($data['zip']) === FALSE) {
    ApiResponse([
        'status' => false,
        'message' => "Nomor kodepos harus berupa angka",
        'response' => []
    ]);
}

$kodepos = Wilayah::postalCode($data['province'], $data['city'], $data['district'], $data['villages'], $data['zip']);
if(!$kodepos) {
    ApiResponse([
        'status' => false,
        'message' => "Nomor Kode Pos tidak valid / terdaftar",
        'response' => []
    ]);
}

/* Update **/
$gender = empty($data['gender'])? "" : strtoupper($data['gender']);
$address = empty($data['address'])? "" : $data['address'];
$updateData = [
    'MBR_PROVINCE' => $kodepos['KDP_PROV'],
    'MBR_CITY' => $kodepos['KDP_KABKO'],
    'MBR_DISTRICT' => $kodepos['KDP_KECAMATAN'],
    'MBR_VILLAGES' => $kodepos['KDP_KELURAHAN'],
    'MBR_ZIP' => $kodepos['KDP_POS'],
    'MBR_TMPTLAHIR' => $data['place_of_birth'],
    'MBR_TGLLAHIR' => date("Y-m-d", strtotime($data['date_of_birth'])),
    'MBR_ADDRESS' => $address,
    'MBR_JENIS_KELAMIN' => $gender
];

/** check Avatar */
if(!empty($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
    $uploadFile = FileUpload::upload_myfile($_FILES['avatar'], "avatar");
    if(!is_array($uploadFile) || !array_key_exists("filename", $uploadFile)) {
        ApiResponse([
            'status' => false,
            'message' => $uploadFile ?? "Upload avatar gagal",
            'response' => []
        ]);
    }

    $updateData['MBR_AVATAR'] = $uploadFile['filename'];
}

$update = Database::update("tb_member", $updateData, ['MBR_ID' => $user['MBR_ID']]);
if(!$update) {
    ApiResponse([
        'status' => false,
        'message' => "Gagal memperbarui data",
        'response' => []
    ]);
}

ApiResponse([
    'status' => true,
    'message' => "Berhasil",
    'response' => []
]);
