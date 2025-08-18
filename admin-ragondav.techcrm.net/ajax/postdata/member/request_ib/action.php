<?php

use App\Models\Helper;
use App\Models\Ib;
use App\Models\Logger;
use App\Models\User;
use Config\Core\Database;

if(!$adminPermissionCore->hasPermission($authorizedPermission, $url)) {
    JsonResponse([
        'success' => false,
        'message' => "Permission Denied",
        'data' => []
    ]);
}

$data = Helper::getSafeInput($_POST);
$required = [
    'type' => "Type",
    'id' => "ID",
];

foreach($required as $req => $text) {
    if(empty($data[ $req ])) {
        JsonResponse([
            'success' => false,
            'message' => "{$text} is required",
            'data' => []
        ]);
    }
}

/** check ID */
$ibData = Ib::findById($data['id']);
if(!$ibData) {
    JsonResponse([
        'success' => false,
        'message' => "Invalid ID",
        'data' => []
    ]);
}

/** check Type */
$type = strtolower($data['type']);
if(!in_array($type, ['accept', 'reject'])) {
    JsonResponse([
        'success' => false,
        'message' => "Invalid Type",
        'data' => []
    ]);
}

/** check user */
$userData = User::findByMemberId($ibData['BECOME_MBR']);
if(!$userData) {
    JsonResponse([
        'success' => false,
        'message' => "Invalid User",
        'data' => []
    ]);
}

/** check user type */
if($userData['MBR_TYPE'] != Ib::getTraderType()) {
    JsonResponse([
        'success' => false,
        'message' => "User is not trader",
        'data' => []
    ]);
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
mysqli_begin_transaction($db);

$status = ($type == "accept")? -1 : 1;
$note = $data['note'] ?? "";


/** update becomeib */
$update = Database::update("tb_become_ib", ['BECOME_STS' => $status, 'BECOME_NOTE' => $note], ['ID_BECOME' => $ibData['ID_BECOME']]);
if(!$update) {
    $db->rollback();
    JsonResponse([
        'success' => false,
        'message' => "Failed to update status",
        'data' => []
    ]);
}

/** Update tb_member */
if($type == "accept") {
    $updateMember = Database::update("tb_member", ['MBR_TYPE' => Ib::getIbType()], ['MBR_ID' => $ibData['BECOME_MBR']]);
    if(!$updateMember) {
        $db->rollback();
        JsonResponse([
            'success' => false,
            'message' => "Invalid Type",
            'data' => []
        ]);
    }
}

Logger::admin_log([
    'admid' => $user['ADM_ID'],
    'module' => "request_ib",
    'message' => "{$type} request ib for id: " . $data['id'],
    'data' => $data
]);

$db->commit();
JsonResponse([
    'success' => true,
    'message' => "Successfull",
    'data' => []
]);