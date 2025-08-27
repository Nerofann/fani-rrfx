<?php

use App\Models\Database;

$data = $helperClass->getSafeInput($_POST);
$required = [
    'bank_id' => "ID bank",
    'currency' => "Bank Currency",
    'bank_name' => "Nama Bank",
    'bank_holder' => "Nama pemilik bank",
    'account' => "No. Rekening",
    'type' => "Tipe"
];

/** Check Id Bank */
$sqlCheckIdBank = $db->query("SELECT * FROM tb_member_bank WHERE MD5(MD5(ID_MBANK)) = '".$data['bank_id']."' LIMIT 1");
$bankDetail = $sqlCheckIdBank->fetch_assoc();
if($sqlCheckIdBank->num_rows != 1) {
    ApiResponse([
        'status' => false,
        'message' => "ID Bank tidak valid",
        'response' => []
    ], 400);
}

foreach($required as $key => $req) {
    if(empty($data[ $key ])) {
        ApiResponse([
            'status' => false,
            'message' => "{$req} wajib diisi",
            'response' => []
        ], 400);
    }
}

/** check tipe */
$bankType = strtoupper($data['type']);
if(!in_array($bankType, ['TABUNGAN', 'GIRO', 'LAINNYA'])) {
    ApiResponse([
        'status' => false,
        'message' => "Tipe bank tidak valid",
        'response' => []
    ], 400);
}

/** check nama bank */
$bankName = $data['bank_name'];
$sqlCheckBankName = $db->query("SELECT * FROM tb_banklist WHERE BANKLST_NAME = '{$bankName}' LIMIT 1");
if($sqlCheckBankName->num_rows != 1) {
    ApiResponse([
        'status' => false,
        'message' => "Nama Bank tidak valid",
        'response' => []
    ], 400);
}

/** Check currency */
if(!in_array(strtoupper($data['currency']), ['IDR', 'USD'])) {
    ApiResponse([
        'status' => false,
        'message' => "Bank Currency tidak valid",
        'response' => []
    ], 400);
}

/** Check nomor rekening */
$userBanks = myBank($userData['MBR_ID']);
if(is_numeric($data['account']) === FALSE || $data['account'] <= 0) {
    ApiResponse([
        'status' => false,
        'message' => "Nomor rekening harus berupa angka",
        'response' => []
    ], 400);
}

foreach($userBanks as $bank) {
    if($bank['MBANK_ACCOUNT'] == $data['account'] && $bank['ID_MBANK'] != $bankDetail['ID_MBANK']) {
        ApiResponse([
            'status' => false,
            'message' => "Nomor rekening sudah didaftarkan",
            'response' => []
        ], 400);
    }
}

/** update */
$bankBranch = $data['bank_branch'] ?? NULL;
$updateData = [
    'MBANK_HOLDER' => $data['bank_holder'],
    'MBANK_NAME' => $bankName,
    'MBANK_ACCOUNT' => $data['account'],
    'MBANK_BRANCH' => $bankBranch,
    'MBANK_CURR' => strtoupper($data['currency']),
    'MBANK_TYPE' => $bankType,
];

$update = Database::updateWithArray("tb_member_bank", $updateData, ['ID_MBANK' => $bankDetail['ID_MBANK']]);
if(!$update) {
    ApiResponse([
        'status' => false,
        'message' => "Gagal memperbarui bank",
        'response' => []
    ], 400);
}

newInsertLog([
    'mbrid' => $userData['MBR_ID'],
    'module' => "bank",
    'ref' => $data['account'],
    'message' => "Memperbarui bank, id: ".$data['bank_id'],
    'device' => "mobile",
    'data'  => json_encode($data)
]);
    
ApiResponse([
    'status' => true,
    'message' => "Berhasil memperbarui bank",
    'response' => []
]);