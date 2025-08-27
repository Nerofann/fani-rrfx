<?php

$sqlGetBankList = $db->query("SELECT * FROM tb_banklist");
$result = [];
foreach($sqlGetBankList->fetch_all(MYSQLI_ASSOC) as $bank) {
    $result[] = $bank['BANKLST_NAME'];
}

ApiResponse([
    'status' => true,
    'message' => "List",
    'response' => $result
]);