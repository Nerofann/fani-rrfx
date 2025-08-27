<?php

use App\Models\Database;

$data = $helperClass->getSafeInput($_POST);
$required = [
    'currency' => "Bank Currency",
    'bank_name' => "Nama Bank",
    'bank_holder' => "Nama pemilik bank",
    'account' => "No. Rekening",
    'type' => "Tipe"
];

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

/** check jumlah bank */
$userBanks = myBank($userData['MBR_ID']);
if(count($userBanks) >= 2) {
    ApiResponse([
        'status' => false,
        'message' => "Anda sudah memiliki 2 bank aktif",
        'response' => []
    ], 400);
}

/** Check nomor rekening */
if(is_numeric($data['account']) === FALSE || $data['account'] <= 0) {
    ApiResponse([
        'status' => false,
        'message' => "Nomor rekening harus berupa angka",
        'response' => []
    ], 400);
}

foreach($userBanks as $bank) {
    if($bank['MBANK_ACCOUNT'] == $data['account']) {
        ApiResponse([
            'status' => false,
            'message' => "Nomor rekening sudah didaftarkan",
            'response' => []
        ], 400);
    }
}

/** insert */
$bankBranch = $data['bank_branch'] ?? NULL;
$insert = Database::insertWithArray("tb_member_bank", [
    'MBANK_MBR' => $userData['MBR_ID'],
    'MBANK_HOLDER' => $data['bank_holder'],
    'MBANK_NAME' => $bankName,
    'MBANK_ACCOUNT' => $data['account'],
    'MBANK_BRANCH' => $bankBranch,
    'MBANK_CURR' => strtoupper($data['currency']),
    'MBANK_TYPE' => $bankType,
    'MBANK_DATETIME' => date("Y-m-d H:i:s"),
]);

if(!$insert) {
    ApiResponse([
        'status' => false,
        'message' => "Gagal menambahkan bank",
        'response' => []
    ], 400);
}

newInsertLog([
    'mbrid' => $userData['MBR_ID'],
    'module' => "bank",
    'ref' => $data['account'],
    'message' => "Menambahkan bank baru, ".implode(", ", [$bankName, $data['bank_holder'], $data['account']]),
    'device' => "mobile",
    'data'  => json_encode($data)
]);
    
ApiResponse([
    'status' => true,
    'message' => "Berhasil menambah bank",
    'response' => []
]);