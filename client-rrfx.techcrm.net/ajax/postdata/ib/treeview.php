<?php
global $db, $ApiMeta;
loadModel("Rebate");

$rebateClass = new Rebate();
$tree = $rebateClass->getNetworks($user['MBR_ID'], "downline");
$treeMap = [];

if(!$rebateClass->isIB($user['MBR_ID']) && !$rebateClass->haveIb($user['MBR_ID'], "upline")) {
    exit(json_encode([
        'success'   => false,
        'error'     => "Invalid IB",
    ]));
}

if(!is_array($tree)) {
    exit(json_encode([
        'success'   => false,
        'error'     => "Failed to generate treeview",
    ]));
}

/** Ambil semua akun dari member yang ada dalam jaringan */
$mbrList = array_map(fn($ar): int => $ar['MBR_ID'], $tree);
$logins = [];
$sqlGetAccounts = $this->db->query("
    SELECT
        ACC_MBR,
        ACC_LOGIN
    FROM tb_racc tr
    JOIN tb_racctype trc ON (trc.ID_RTYPE = tr.ACC_TYPE)
    WHERE tr.ACC_LOGIN != 0
    AND tr.ACC_DERE = 1
    AND UPPER(trc.RTYPE_TYPE) != 'DEMO'
    AND tr.ACC_MBR IN (".(implode(",", $mbrList)).")
");

/** Get account login as array */
if($sqlGetAccounts->num_rows != 0) {
    // $logins = array_map(fn($ar): string => $ar['ACC_LOGIN'], $sqlGetAccounts->fetch_all(MYSQLI_ASSOC));
    foreach($sqlGetAccounts->fetch_all(MYSQLI_ASSOC) as $ac) {
        $logins[ $ac['ACC_MBR'] ][ $ac['ACC_LOGIN'] ] = 0; 
    }
}

/** Ambil informasi akun dari meta */
// $logins = array_map(fn($ar): string => $ar['ACC_LOGIN'], $);
$accounts = $ApiMeta->accounts();
if($accounts->success) {         
    $listAccounts = json_decode(json_encode($accounts->message ?? []), true);   
    if(is_array($listAccounts)) {
        foreach($logins as $key => $lg) {
            foreach($lg as $key2 => $total) {
                $index = array_search($key2, array_column($listAccounts, "Login"));
                if($index !== FALSE) {
                    $logins[ $key ][ $key2 ] += $listAccounts[ $index ]['Balance'];
                }
            }
        }
    }
}


/** Create Treeview */
foreach ($tree as &$entry) {
    // Tambahkan setiap entry ke dalam map menggunakan MBR_ID sebagai kunci
    $userType = strtoupper($rebateClass->getMyType($entry['MBR_TYPE']));
    $balance = array_sum($logins[ $entry['MBR_ID'] ] ?? []);
    $treeMap[$entry['MBR_ID']] = [
        // 'id'        => $id,
        // 'parent_id' => $idSpn,
        'text'      => $this->buildText($entry['MBR_NAME'] . " ({$userType})", $balance, 0, 0),
        'icon'      => "fas fa-user",
    ];

    // Inisialisasi array children untuk setiap entry
    $treeMap[$entry['MBR_ID']]['nodes'] = [];
}

unset($entry);
foreach ($tree as $entry) {
    // Cek apakah member punya parent selain dirinya sendiri
    if ($entry['MBR_IDSPN'] != $entry['MBR_ID']) {
        // Tambahkan member ke children dari parent-nya
        $treeMap[$entry['MBR_IDSPN']]['nodes'][] = &$treeMap[$entry['MBR_ID']];
    }
}
/** End Create Treeview */

$treedata = $treeMap[ $user['MBR_ID'] ] ?? [];
if(!empty($treedata)) {
    $treedata['expanded'] = true;
}

exit(json_encode([
    'success'   => true,
    'error'     => "",
    'message'   => "Generate Treeview Successfully",
    'data'      => [$treedata]
]));