<?php

use App\Models\Helper;

require_once __DIR__ . "/appRegol.php";

$appRegol = new AppRegol($db);
$method = Helper::form_input($_GET['c'] ?? "error");

/** Sementara static */
$csrf_token = md5(uniqid());
$_SESSION['csrf_token'] = $csrf_token;
$_POST['csrf_token'] = $csrf_token;

if(empty($method) || !method_exists($appRegol, $method)) {
    exit(json_encode([
        'success'   => false,
        'message'   => "Invalid Method",
        'response'  => []
    ]));
}

$user['userid'] = $userId;
call_user_func_array([$appRegol, $method], [$_POST, $user]);