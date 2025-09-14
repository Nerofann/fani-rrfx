<?php

use App\Models\Account;
use App\Models\Helper;
use App\Models\Regol;
use Config\Core\Database;

if(!$adminPermissionCore->hasPermission($authorizedPermission, $url)) {
    JsonResponse([
        'success' => false,
        'message' => "Authorization Failed",
        'data' => []
    ]);
}

$data = Helper::getSafeInput($_POST);
foreach(['type', 'note', 'id'] as $req) {
    if(empty($data[ $req ])) {
        JsonResponse([
            'success' => false,
            'message' => "{$req} is required",
            'data' => []
        ]);
    }
}

/** check type */
$data['type'] = strtolower($data['type'] ?? "-");
if(!in_array($data['type'], ['accept', 'reject'])) {
    JsonResponse([
        'success'=> false,
        'message'=> "Invalid Type",
        'data'=> [],
    ]);
}

/** check account */
$account = Account::realAccountDetail($data['id']);
if(!$account) {
    JsonResponse([
        'success' => false,
        'message' => "Invalid Account",
        'data' => []
    ]);
}

/** check status */
if($account['ACC_WPCHECK'] != Regol::$statusWPCheckBankVerification) {
    JsonResponse([
        'success' => false,
        'message' => "Invalid Status",
        'data' => []
    ]);
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
mysqli_begin_transaction($db);

$updateRacc = [];

switch($data['type']) {
    case "accept":
        $updateRacc = ['ACC_WPCHECK' => 1];
        break;

    case "reject":
        $updateRacc = [
            'ACC_F_CMPLT' => 0,
            'ACC_STS' => 2
        ];
}

/** update status */
$update = Database::update("tb_racc", $updateRacc, ['ID_ACC' => $account['ID_ACC']]);
if(!$update) {
    $db->rollback(90);
    JsonResponse([
        'success' => false,
        'message' => "Gagal memperbarui data",
        'data' => []
    ]);
}

/** insert note */
$insert = Database::insert("tb_note", [
    "NOTE_MBR"   => $account["ACC_MBR"],
    "NOTE_RACC"  => $account["ID_ACC"],
    "NOTE_TYPE"  => 'BANK VERIFICATION '.strtoupper($data["type"]),
    "NOTE_NOTE"  => $data["note"],
]);

if(!$insert) {
    $db->rollback();
    JsonResponse([
        'success' => false,
        'message' => "Gagal (2)",
        'data' => []
    ]);
}

$db->commit();
JsonResponse([
    'success' => true,
    'message' => "Berhasil",
    'data' => [
        'redirect' => "/account/progress_real_account/view"
    ]
]);