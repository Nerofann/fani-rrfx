<?php

require_once __DIR__ . "/appRegol.php";

$appRegol = new AppRegol($db);
$method = form_input($_GET['c'] ?? "error");

/** Sementara static */
$csrf_token = generateCSRFToken();
$_SESSION['csrf_token'] = $csrf_token;
$_POST['csrf_token'] = $csrf_token;

if(empty($method) || !method_exists($appRegol, $method)) {
    exit(json_encode([
        'success'   => false,
        'message'   => "Invalid Method",
        'response'  => []
    ]));
}

$userData['userid'] = $userId;
call_user_func_array([$appRegol, $method], [$_POST, $userData]);