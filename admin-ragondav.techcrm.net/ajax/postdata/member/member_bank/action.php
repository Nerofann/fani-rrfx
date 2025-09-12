<?php

use App\Models\Helper;
use App\Models\MemberBank;
use Config\Core\Database;

if(!$adminPermissionCore->hasPermission($authorizedPermission, $url)) {
    JsonResponse([
        'success' => false,
        'message' => "Authorization Failed",
        'data' => []
    ]);
}

$data = Helper::getSafeInput($_POST);
foreach(['type', 'id'] as $req) {
    if(empty($data[ $req ])) {
        JsonResponse([
            'success' => false,
            'message' => "{$req} is required",
            'data' => []
        ]);
    }
}

/** check id */
$bank = MemberBank::findByIdHash($data['id']);
if(!$bank) {
    JsonResponse([
        'success' => false,
        'message' => "Invalid ID",
        'data' => []
    ]);
}

/** check type */
if(!in_array(strtolower($data['type']), ['accept', 'reject'])) {
    JsonResponse([
        'success' => false,
        'message' => "Invalid Type",
        'data' => []
    ]);
}

$status = ($data['type'] == "accept")? MemberBank::$statusAccepted : MemberBank::$statusRejected;
$update = Database::update("tb_member_bank", ['MBANK_STS' => $status], ['ID_MBANK' => $bank['ID_MBANK']]);
if(!$update) {
    JsonResponse([
        'success' => false,
        'message' => "Gagal",
        'data' => []
    ]);
}

JsonResponse([
    'success' => true,
    'message' => "Berhasil " . ucwords($data['type']),
    'data' => []
]);