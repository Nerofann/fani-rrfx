<?php

use App\Models\Account;
use App\Models\Helper;

$idAcc = Helper::form_input($_GET['acc'] ?? "");
$account = Account::realAccountDetail($idAcc);
if(!$account) {
    exit('Invalid Request');
}

$accountType = filter_var(strtolower($account['RTYPE_TYPE_AS'] ?? ""), FILTER_SANITIZE_URL);
if(file_exists(__DIR__ . "/{$accountType}/{$filename}.php")) {
    require_once __DIR__ . "/{$accountType}/{$filename}.php";
}