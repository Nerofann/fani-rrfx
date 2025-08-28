<?php

use App\Models\AccountTrade;

$accountId = form_input($_POST['trade_id']) ?? "";
if(empty($accountId)) {
    ApiResponse([
        'status' => false,
        'message' => "Trade Id is required",
        'response' => []
    ], 400);
}

$accountTrade = AccountTrade::getById($accountId);
if(empty($accountTrade)) {
    ApiResponse([
        'status' => false,
        'message' => "Account Trade not found",
        'response' => []
    ], 400);
}

$sqlDelete = $db->prepare("DELETE FROM tb_racc_trade WHERE ID_ACCTRADE = ?");
$sqlDelete->bind_param("i", $accountTrade['ID_ACCTRADE']);
if($sqlDelete->execute() === FALSE) {
    ApiResponse([
        'status' => false,
        'message' => "Failed to delete account trade",
        'response' => []
    ], 400);
}

ApiResponse([
    'status' => true,
    'message' => "Account trade deleted successfully",
    'response' => []
], 200);