<?php

use App\Factory\MetatraderFactory;
use App\Models\Account;
use App\Models\Helper;
use App\Models\User;
use Config\Core\Database;

$data = Helper::getSafeInput($_POST);
foreach(['login', 'password'] as $req) {
    if(empty($data[ $req ])) {
        JsonResponse([
            'success' => false,
            'message' => "{$req} is required",
            'data' => []
        ]);
    }
}

$account = Account::realAccountDetail_byLogin($data['login']);
if(!$account || $account['ACC_MBR'] != $user['MBR_ID']) {
    JsonResponse([
        'success' => false,
        'message' => "Invalid Account",
        'data' => []
    ]);
}

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
mysqli_begin_transaction($db);

/** Update tb_racc */
$update = Database::update("tb_racc", ['ACC_PASS' => $data['password']], ['ID_ACC' => $account['ID_ACC']]);
if(!$update) {
    $db->rollback();
    JsonResponse([
        'success' => false,
        'message' => "Change password failed",
        'data' => []
    ]);
}

$apiManager = MetatraderFactory::apiManager();
$changePasswordData = [
    'login' => $account['ACC_LOGIN'],
    'password' => $data['password'],
    'password_type' => CHANGE_MASTER_PASSWORD
];

$changePassword = $apiManager->changePassword($changePasswordData);
if(!is_object($changePassword) || !$changePassword->success) {
    $db->rollback();
    JsonResponse([
        'success' => false,
        'message' => "Change password failed (2)",
        'data' => []
    ]);
}

$db->commit();
JsonResponse([
    'success' => true,
    'message' => "Success",
    'data' => []
]);