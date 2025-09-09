<?php

use App\Models\Account;
use App\Models\Helper;

$data = Helper::getSafeInput($_POST);
$required = [
    'account' => "Akun",
    'type' => "Tipe Konversi",
    'amount' => "Jumlah",
];

foreach($required as $req => $text) {
    if(empty($data[ $req ])) {
        ApiResponse([
            'status'    => false,
            'message'   => "Kolom {$text} tidak boleh kosong",
            'response'  => []
        ], 400);
    }
}

/** Validasi Amount */
$amountSource = Helper::stringTonumber($data['amount']);
if(is_numeric($amountSource) === FALSE || $amountSource <= 0) {
    ApiResponse([
        'status'    => false,
        'message'   => "Invalid Amount",
        'response'  => []
    ], 400);
}

/** Check Type */
if(!in_array(strtolower($data['type']), ['deposit', 'withdrawal'])) {
    ApiResponse([
        'status'    => false,
        'message'   => "Tipe Konversi tidak valid",
        'response'  => []
    ], 400);
}

/** Check Account */
$account = Account::realAccountDetail($data['account']);
if(empty($account)) {
    ApiResponse([
        'status'    => false,
        'message'   => "Invalid Account",
        'response'  => []
    ], 400);
}

switch(strtolower($data['type'])) {
    case "deposit":
        $convert = Account::accountConvertation([
            'account_id' => $account['ID_ACC'],
            'amount' => $amountSource,
            'from' => $account['RTYPE_CURR'],
            'to' => "USD"
        ]);

        if(!is_array($convert)) {
            ApiResponse([
                'status'    => false,
                'message'   => $convert ?? "Invalid Convertation",
                'response'  => []
            ], 400);
        }

        $amountFinal = round(($amountSource / $convert['rate']),5);
        ApiResponse([
            'status'    => true,
            'message'   => "Berhasil",
            'response'  => [
                'amount_source' => $amountSource,
                'amount_received' => $amountFinal,
                'rate'  => floatval($convert['rate'])
            ]
        ]);
        break;

    case "withdrawal":
        $convert = Account::accountConvertation([
            'account_id' => $account['ID_ACC'],
            'amount' => $amountSource,
            'from' => "USD",
            'to' => $account['RTYPE_CURR']
        ]);

        if(!is_array($convert)) {
            ApiResponse([
                'status'    => false,
                'message'   => $convert ?? "Invalid Convertation",
                'response'  => []
            ], 400);
        }

        $amountFinal = round(($amountSource * $convert['rate']),5);
        ApiResponse([
            'status'    => true,
            'message'   => "Berhasil",
            'response'  => [
                'amount_source' => $amountSource,
                'amount_received' => $amountFinal,
                'rate'  => floatval($convert['rate'])
            ]
        ]);
        break;
}

ApiResponse([
    'status'    => false,
    'message'   => "Gagal",
    'response'  => []
], 400);